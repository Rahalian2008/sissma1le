<?php

use App\Models\User;
use App\Services\TwoFactorAuthService;
use Illuminate\Support\Facades\Hash;

test('TwoFactorAuthService generates and verifies RFC 6238 TOTP codes correctly', function () {
    $secret = TwoFactorAuthService::generateSecretKey(16);
    expect($secret)->toHaveLength(16);

    $code = TwoFactorAuthService::calculateCode($secret);
    expect($code)->toHaveLength(6);
    expect(TwoFactorAuthService::verifyCode($secret, $code))->toBeTrue();
    expect(TwoFactorAuthService::verifyCode($secret, '000000'))->toBeFalse();
});

test('authenticated user can request 2FA setup details', function () {
    $user = User::factory()->create([
        'role' => 'super_admin',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($user)->getJson(route('two_factor.setup'));

    $response->assertOk()
        ->assertJsonStructure([
            'enabled',
            'secret',
            'formatted_key',
            'account',
            'qr_uri',
        ]);
});

test('user can enable 2FA with valid code', function () {
    $user = User::factory()->create([
        'role' => 'super_admin',
        'password' => Hash::make('password123'),
    ]);

    $secret = TwoFactorAuthService::generateSecretKey(16);
    session(['2fa_pending_secret' => $secret]);

    $validCode = TwoFactorAuthService::calculateCode($secret);

    $response = $this->actingAs($user)->post(route('two_factor.enable'), [
        'code' => $validCode,
    ]);

    $response->assertRedirect(route('profile'));
    $user->refresh();

    expect($user->hasTwoFactorEnabled())->toBeTrue();
    expect($user->two_factor_secret)->toBe($secret);
    expect($user->getRecoveryCodes())->toHaveCount(8);
});

test('user with 2FA enabled is redirected to 2FA challenge upon password login', function () {
    $secret = TwoFactorAuthService::generateSecretKey(16);
    $user = User::factory()->create([
        'username' => 'super_test',
        'email' => 'super_test@example.com',
        'role' => 'super_admin',
        'password' => Hash::make('secretpass'),
        'two_factor_secret' => $secret,
        'two_factor_recovery_codes' => ['code-1111', 'code-2222'],
        'two_factor_confirmed_at' => now(),
    ]);

    $response = $this->post(route('login.post'), [
        'email' => 'super_test',
        'password' => 'secretpass',
    ]);

    $response->assertRedirect(route('login.two_factor'));
    expect(session('auth_2fa_user_id'))->toBe($user->id);
    expect(auth()->check())->toBeFalse();
});

test('user completes 2FA challenge with valid TOTP code', function () {
    $secret = TwoFactorAuthService::generateSecretKey(16);
    $user = User::factory()->create([
        'role' => 'super_admin',
        'two_factor_secret' => $secret,
        'two_factor_recovery_codes' => ['code-1111', 'code-2222'],
        'two_factor_confirmed_at' => now(),
    ]);

    session(['auth_2fa_user_id' => $user->id]);

    $validCode = TwoFactorAuthService::calculateCode($secret);

    $response = $this->post(route('login.two_factor.post'), [
        'code' => $validCode,
    ]);

    $response->assertRedirect(route('dashboard'));
    expect(auth()->check())->toBeTrue();
    expect(auth()->id())->toBe($user->id);
});

test('user completes 2FA challenge with recovery code', function () {
    $secret = TwoFactorAuthService::generateSecretKey(16);
    $user = User::factory()->create([
        'role' => 'super_admin',
        'two_factor_secret' => $secret,
        'two_factor_recovery_codes' => ['code-1111', 'code-2222'],
        'two_factor_confirmed_at' => now(),
    ]);

    session(['auth_2fa_user_id' => $user->id]);

    $response = $this->post(route('login.two_factor.post'), [
        'recovery_code' => 'CODE-1111',
    ]);

    $response->assertRedirect(route('dashboard'));
    expect(auth()->check())->toBeTrue();
    expect(auth()->id())->toBe($user->id);

    $user->refresh();
    expect($user->getRecoveryCodes())->toBe(['code-2222']);
});

test('user can disable 2FA with current password', function () {
    $secret = TwoFactorAuthService::generateSecretKey(16);
    $user = User::factory()->create([
        'role' => 'super_admin',
        'password' => Hash::make('mysecurepassword'),
        'two_factor_secret' => $secret,
        'two_factor_recovery_codes' => ['code-1111'],
        'two_factor_confirmed_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('two_factor.disable'), [
        'password' => 'mysecurepassword',
    ]);

    $response->assertRedirect(route('profile'));
    $user->refresh();

    expect($user->hasTwoFactorEnabled())->toBeFalse();
    expect($user->two_factor_secret)->toBeNull();
});
