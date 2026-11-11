@extends('layouts.app')

@section('title', 'Profil Pengguna - SISWA SMA1LE')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div class="w-24 h-24 rounded-2xl bg-white/10 border-2 border-white/20 p-1 flex items-center justify-center shrink-0 shadow-lg">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-xl">
                @else
                    <div class="w-full h-full rounded-xl bg-blue-500/30 flex items-center justify-center text-3xl font-extrabold text-white">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
            </div>

            <div class="text-center sm:text-left flex-1">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                    <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/20 uppercase tracking-wider">
                        {{ str_replace('_', ' ', $user->role) }}
                    </span>
                </div>
                <p class="text-blue-100 text-sm mt-1">{{ $user->email }} ({{ '@' . $user->username }})</p>
                <div class="mt-4 flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs text-blue-200">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $user->phone ?? 'Belum ada nomor telepon' }}
                    </span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Status: {{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Bergabung: {{ $user->created_at->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Perbarui Informasi Profil</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pastikan informasi kontak Anda selalu valid dan dapat dihubungi sekolah.</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Nomor Telepon / WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('phone')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email (Read-only) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Alamat Email (Akun)</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed">
                    <p class="text-[11px] text-slate-400 mt-1">Hubungi Administrator untuk perubahan email login.</p>
                </div>

                <!-- Username (Read-only) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Username</label>
                    <input type="text" value="{{ $user->username }}" disabled
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed">
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-800 pt-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Ubah Kata Sandi (Opsional)</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Kosongkan jika Anda tidak bermaksud mengganti kata sandi saat ini.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        @error('password')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Two-Factor Authentication (Google Authenticator / 2FA) Card -->
    <div id="two-factor-section" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 dark:border-slate-800 gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl {{ $user->hasTwoFactorEnabled() ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-indigo-500/10 text-indigo-500 border border-indigo-500/20' }} flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Otentikasi Dua Langkah (2FA)</h2>
                        @if($user->hasTwoFactorEnabled())
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30">
                                AKTIF & TERLINDUNGI
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                BELUM AKTIF
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Amankan akun Anda menggunakan aplikasi <strong class="text-slate-700 dark:text-slate-200">Google Authenticator</strong> atau aplikasi TOTP lainnya.
                        Setiap kali masuk, Anda akan diminta memasukkan 6-digit kode verifikasi dari ponsel.
                    </p>
                </div>
            </div>
        </div>

        @if($user->role === 'super_admin' && ! $user->hasTwoFactorEnabled())
            <div class="mt-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 text-xs flex items-start gap-3">
                <span class="text-lg">🛡️</span>
                <div>
                    <strong class="font-bold text-amber-800 dark:text-amber-300">Rekomendasi Utama Super Admin:</strong>
                    Akun Super Admin memegang kendali penuh atas sistem dan basis data sekolah. Sangat disarankan untuk segera mengaktifkan fitur ini agar akun kebal dari pencurian kata sandi.
                </div>
            </div>
        @endif

        {{-- Flash Recovery Codes Display --}}
        @if(session('2fa_recovery_codes_show'))
            <div class="mt-6 p-5 rounded-2xl bg-emerald-500/10 border-2 border-emerald-500/40 text-emerald-950 dark:text-emerald-100">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <h3 class="text-sm font-black text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Simpan Kode Pemulihan Cadangan Anda Sekarang!
                    </h3>
                    <button type="button" onclick="copyRecoveryCodes()" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition cursor-pointer">
                        Salin Semua Kode
                    </button>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 mb-4">
                    Jika ponsel Anda hilang atau tidak dapat diakses, kode-kode ini adalah <strong>satu-satunya cara</strong> bagi Anda untuk masuk ke sistem. Simpan di tempat yang aman (pengelola sandi atau cetak):
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 font-mono text-center text-xs font-bold text-slate-800 dark:text-slate-200 bg-white/80 dark:bg-slate-950/60 p-4 rounded-xl border border-emerald-500/20 select-all" id="recovery-codes-list">
                    @foreach(session('2fa_recovery_codes_show') as $code)
                        <div class="py-1.5 px-2 bg-slate-100 dark:bg-slate-800/80 rounded-lg border border-slate-200 dark:border-slate-700">{{ $code }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Condition A: 2FA is ALREADY ENABLED --}}
        @if($user->hasTwoFactorEnabled())
            <div class="mt-6 space-y-4">
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div>
                        <div class="font-bold text-slate-800 dark:text-white">Google Authenticator Terhubung</div>
                        <div class="text-slate-500 dark:text-slate-400 mt-0.5">
                            Diaktifkan sejak: <strong class="text-slate-700 dark:text-slate-200">{{ $user->two_factor_confirmed_at?->format('d M Y H:i') ?? '-' }}</strong>
                            &bull; Sisa kode cadangan: <strong class="text-emerald-600 dark:text-emerald-400">{{ count($user->getRecoveryCodes()) }} kode</strong>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openRegenerateCodesModal()" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-white transition cursor-pointer">
                            Buat Ulang Kode Cadangan
                        </button>
                        <button type="button" onclick="openDisable2faModal()" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 transition cursor-pointer">
                            Nonaktifkan 2FA
                        </button>
                    </div>
                </div>
            </div>

        {{-- Condition B: 2FA is NOT ENABLED --}}
        @else
            <div class="mt-6">
                <div id="btn-start-2fa-container" class="{{ session('2fa_setup_open') ? 'hidden' : '' }}">
                    <button type="button" onclick="startTwoFactorSetup()" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 hover:from-emerald-500 hover:to-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Aktifkan Google Authenticator Sekarang
                    </button>
                </div>

                {{-- Interactive Setup Box --}}
                <div id="setup-2fa-box" class="{{ session('2fa_setup_open') ? '' : 'hidden' }} mt-4 p-6 rounded-3xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📱</span>
                            Langkah Penyiapan Google Authenticator
                        </h3>
                        <button type="button" onclick="cancelTwoFactorSetup()" class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                            Batal
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                        <!-- Step 1 & 2: QR & Key -->
                        <div class="space-y-4">
                            <div>
                                <span class="px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-300 text-[10px] font-black uppercase tracking-wider">Langkah 1</span>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1">Unduh & Buka Aplikasi</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    Unduh aplikasi <strong>Google Authenticator</strong> dari Google Play Store (Android) atau App Store (iPhone).
                                </p>
                            </div>

                            <div>
                                <span class="px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-300 text-[10px] font-black uppercase tracking-wider">Langkah 2</span>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1">Pindai Kode QR atau Masukkan Kunci</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    Buka aplikasi Google Authenticator, tekan tanda <strong>+</strong> lalu pilih <strong>Pindai kode QR</strong>:
                                </p>
                            </div>

                            <!-- Secret Manual Key -->
                            <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                                <div class="text-[10px] font-semibold text-slate-400 uppercase">Kunci Penyiapan Manual (Jika Tidak Bisa Scan):</div>
                                <div class="flex items-center justify-between gap-2 mt-1">
                                    <span id="formatted-secret-key" class="font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400 tracking-wider">Memuat...</span>
                                    <button type="button" onclick="copySecretKey()" class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-[11px] font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-200 transition cursor-pointer">
                                        Salin
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- QR Image Preview -->
                        <div class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-inner">
                            <div class="w-48 h-48 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-700" id="qr-loading-container">
                                <img id="qr-code-img" src="" alt="QR Code Google Authenticator" class="w-full h-full object-contain p-2 hidden">
                                <span id="qr-loading-text" class="text-xs text-slate-400 font-medium">Membuat Kode QR...</span>
                            </div>
                            <span class="text-[10px] text-slate-400 mt-2 text-center">Arahkan kamera Google Authenticator ke gambar QR di atas</span>
                        </div>
                    </div>

                    <!-- Step 3: Enter 6 digit code to confirm -->
                    <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 text-[10px] font-black uppercase tracking-wider">Langkah 3</span>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1">Masukkan 6-Digit Kode Verifikasi dari Aplikasi:</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 mb-3">
                            Ketik 6 angka yang saat ini muncul di Google Authenticator untuk memverifikasi bahwa penyiapan telah berhasil.
                        </p>

                        <form action="{{ route('two_factor.enable') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3">
                            @csrf
                            <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autocomplete="one-time-code"
                                   placeholder="000000"
                                   class="w-full sm:w-48 text-center font-mono font-black text-xl tracking-[0.3em] py-2.5 px-4 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition cursor-pointer">
                                Konfirmasi & Aktifkan 2FA
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal: Disable 2FA -->
    <div id="modal-disable-2fa" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Nonaktifkan Otentikasi 2 Langkah?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Menonaktifkan 2FA akan mengurangi keamanan akun Anda. Masukkan kata sandi akun Anda untuk mengonfirmasi pembatalan:
            </p>

            <form action="{{ route('two_factor.disable') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kata Sandi Akun</label>
                    <input type="password" name="password" required placeholder="Masukkan kata sandi saat ini"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-rose-500 focus:border-transparent transition">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeDisable2faModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition cursor-pointer">
                        Ya, Nonaktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Regenerate Recovery Codes -->
    <div id="modal-regen-codes" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Buat Ulang Kode Pemulihan Cadangan</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Seluruh kode pemulihan cadangan lama akan <strong>dihapus</strong> dan diganti dengan 8 kode baru. Masukkan kata sandi akun Anda:
            </p>

            <form action="{{ route('two_factor.recovery_codes') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kata Sandi Akun</label>
                    <input type="password" name="password" required placeholder="Masukkan kata sandi saat ini"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeRegenerateCodesModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition cursor-pointer">
                        Buat Kode Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentRawSecret = '';

    function startTwoFactorSetup() {
        document.getElementById('btn-start-2fa-container').classList.add('hidden');
        document.getElementById('setup-2fa-box').classList.remove('hidden');

        // Fetch setup details from backend
        fetch('{{ route('two_factor.setup') }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.secret) {
                currentRawSecret = data.secret;
                document.getElementById('formatted-secret-key').innerText = data.formatted_key;

                // Load QR Image
                const qrImg = document.getElementById('qr-code-img');
                const encodedUri = encodeURIComponent(data.qr_uri);
                qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodedUri}&margin=5`;
                qrImg.onload = function() {
                    qrImg.classList.remove('hidden');
                    document.getElementById('qr-loading-text').classList.add('hidden');
                };
            }
        })
        .catch(err => {
            alert('Gagal memuat detail penyiapan 2FA. Silakan coba kembali.');
        });
    }

    function cancelTwoFactorSetup() {
        document.getElementById('setup-2fa-box').classList.add('hidden');
        document.getElementById('btn-start-2fa-container').classList.remove('hidden');
    }

    function copySecretKey() {
        if (!currentRawSecret) return;
        navigator.clipboard.writeText(currentRawSecret).then(() => {
            alert('Kunci penyiapan manual (' + currentRawSecret + ') berhasil disalin ke clipboard!');
        });
    }

    function copyRecoveryCodes() {
        const container = document.getElementById('recovery-codes-list');
        if (!container) return;
        const text = Array.from(container.children).map(c => c.innerText.trim()).join('\n');
        navigator.clipboard.writeText(text).then(() => {
            alert('8 Kode Pemulihan Cadangan berhasil disalin ke clipboard!');
        });
    }

    function openDisable2faModal() {
        document.getElementById('modal-disable-2fa').classList.remove('hidden');
    }

    function closeDisable2faModal() {
        document.getElementById('modal-disable-2fa').classList.add('hidden');
    }

    function openRegenerateCodesModal() {
        document.getElementById('modal-regen-codes').classList.remove('hidden');
    }

    function closeRegenerateCodesModal() {
        document.getElementById('modal-regen-codes').classList.add('hidden');
    }

    // Auto-trigger setup if session requested it (e.g. on validation failure)
    @if(session('2fa_setup_open'))
        document.addEventListener('DOMContentLoaded', function() {
            startTwoFactorSetup();
        });
    @endif
</script>
@endsection
