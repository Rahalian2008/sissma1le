<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SchoolIdentity;
use App\Models\SchoolSetting;
use App\Models\User;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    /**
     * Get or initiate 2FA setup details (secret, QR URI, formatted key).
     */
    public function setup(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // If user already has active 2FA, do not re-setup without disabling first
        if ($user->hasTwoFactorEnabled()) {
            return response()->json([
                'enabled' => true,
                'message' => 'Otentikasi 2 Langkah sudah aktif pada akun Anda.',
            ]);
        }

        $pendingSecret = $request->session()->get('2fa_pending_secret');
        if (! $pendingSecret) {
            $pendingSecret = TwoFactorAuthService::generateSecretKey(16);
            $request->session()->put('2fa_pending_secret', $pendingSecret);
        }

        $appName = config('app.name', 'SIS SMA 1 Lengkong');
        $account = $user->email ?: $user->username;
        $otpAuthUri = TwoFactorAuthService::getOtpAuthUri($appName, $account, $pendingSecret);
        $formattedKey = TwoFactorAuthService::formatSecretKey($pendingSecret);

        return response()->json([
            'enabled' => false,
            'secret' => $pendingSecret,
            'formatted_key' => $formattedKey,
            'account' => $account,
            'qr_uri' => $otpAuthUri,
        ]);
    }

    /**
     * Confirm and enable 2FA by verifying the first 6-digit TOTP code.
     */
    public function enable(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $pendingSecret = $request->session()->get('2fa_pending_secret');

        if (! $pendingSecret) {
            return back()->with('error', 'Sesi penyiapan 2FA telah kedaluwarsa. Silakan ulangi langkah aktivasi.');
        }

        if (! TwoFactorAuthService::verifyCode($pendingSecret, $validated['code'])) {
            return back()->with('error', 'Kode verifikasi 6-digit tidak cocok atau sudah kedaluwarsa. Pastikan jam di HP Anda sudah disetel otomatis/sinkron.')
                ->with('2fa_setup_open', true);
        }

        $recoveryCodes = TwoFactorAuthService::generateRecoveryCodes(8);

        $user->forceFill([
            'two_factor_secret' => $pendingSecret,
            'two_factor_recovery_codes' => $recoveryCodes,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('2fa_pending_secret');
        $request->session()->flash('2fa_just_enabled', true);
        $request->session()->flash('2fa_recovery_codes_show', $recoveryCodes);

        AuditLog::log('ENABLE_2FA', 'User', $user->id, null, ['method' => 'google_authenticator']);

        return redirect()->route('profile')
            ->with('success', 'Otentikasi 2 Langkah (Google Authenticator) BERHASIL diaktifkan! Harap simpan kode pemulihan cadangan Anda di tempat yang aman.');
    }

    /**
     * Disable 2FA after password confirmation.
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Kata sandi yang Anda masukkan salah. Pembatalan 2FA ditolak.');
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $request->session()->forget('2fa_pending_secret');

        AuditLog::log('DISABLE_2FA', 'User', $user->id);

        return redirect()->route('profile')
            ->with('success', 'Otentikasi 2 Langkah berhasil dinonaktifkan.');
    }

    /**
     * Regenerate fresh recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (! $user->hasTwoFactorEnabled()) {
            return back()->with('error', 'Otentikasi 2 Langkah belum aktif.');
        }

        if (! Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Kata sandi yang Anda masukkan salah.');
        }

        $newCodes = TwoFactorAuthService::generateRecoveryCodes(8);
        $user->two_factor_recovery_codes = $newCodes;
        $user->save();

        $request->session()->flash('2fa_recovery_codes_show', $newCodes);

        AuditLog::log('REGENERATE_2FA_RECOVERY_CODES', 'User', $user->id);

        return redirect()->route('profile')
            ->with('success', 'Kode pemulihan cadangan baru berhasil dibuat. Kode lama sudah tidak berlaku.');
    }

    /**
     * Show the 2FA challenge login screen.
     */
    public function showChallenge(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $userId = $request->session()->get('auth_2fa_user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (! $user || ! $user->hasTwoFactorEnabled()) {
            $request->session()->forget(['auth_2fa_user_id', 'auth_2fa_remember']);

            return redirect()->route('login');
        }

        $school = SchoolIdentity::current();
        $appName = SchoolSetting::get('app_name', 'SIS-SMA1LE');
        $schoolName = $school->name ?? SchoolSetting::get('school_name', 'SMAN 1 Lengkong');
        $appLogoUrl = $school?->logo_url ?? null;

        return view('auth.two-factor-challenge', [
            'user' => $user,
            'appName' => $appName,
            'schoolName' => $schoolName,
            'appLogoUrl' => $appLogoUrl,
        ]);
    }

    /**
     * Verify the 2FA challenge during login.
     */
    public function verifyChallenge(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('auth_2fa_user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        /** @var User|null $user */
        $user = User::find($userId);
        if (! $user || ! $user->hasTwoFactorEnabled()) {
            $request->session()->forget(['auth_2fa_user_id', 'auth_2fa_remember']);

            return redirect()->route('login');
        }

        $remember = (bool) $request->session()->get('auth_2fa_remember', false);

        // A. Login using Recovery Code
        if ($request->filled('recovery_code')) {
            $recoveryCode = trim((string) $request->input('recovery_code'));
            if ($user->useRecoveryCode($recoveryCode)) {
                $request->session()->forget(['auth_2fa_user_id', 'auth_2fa_remember']);
                Auth::login($user, $remember);
                $request->session()->regenerate();

                AuditLog::log('LOGIN_2FA_RECOVERY', 'User', $user->id, null, ['remaining_codes' => count($user->getRecoveryCodes())]);

                $remaining = count($user->getRecoveryCodes());

                return redirect()->intended(route('dashboard'))
                    ->with('success', "Verifikasi berhasil menggunakan Kode Pemulihan Cadangan. Anda memiliki {$remaining} kode cadangan tersisa.");
            }

            return back()->withErrors([
                'recovery_code' => 'Kode pemulihan cadangan tidak valid atau sudah pernah digunakan sebelumnya.',
            ]);
        }

        // B. Login using 6-Digit TOTP Code
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = trim((string) $request->input('code'));
        if (TwoFactorAuthService::verifyCode((string) $user->two_factor_secret, $code)) {
            $request->session()->forget(['auth_2fa_user_id', 'auth_2fa_remember']);
            Auth::login($user, $remember);
            $request->session()->regenerate();

            AuditLog::log('LOGIN_2FA', 'User', $user->id);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Verifikasi 2 Langkah berhasil! Selamat datang kembali, '.$user->name.'.');
        }

        return back()->withErrors([
            'code' => 'Kode verifikasi 6-digit salah atau telah kedaluwarsa. Silakan periksa kembali aplikasi Google Authenticator di ponsel Anda.',
        ]);
    }

    /**
     * Cancel the 2FA login challenge and return to login.
     */
    public function cancelChallenge(Request $request): RedirectResponse
    {
        $request->session()->forget(['auth_2fa_user_id', 'auth_2fa_remember']);

        return redirect()->route('login');
    }
}
