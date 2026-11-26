<?php

use App\Models\SchoolSetting;
use App\Models\User;

test('admin can toggle default extracurricular schedule on and off', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    // Test enabling default schedule
    $response = $this->actingAs($admin)->post('/master/settings', [
        'enable_default_extra_schedule' => '1',
        'extra_time_start' => '14:00',
        'extra_time_end' => '17:30',
        'extra_days' => ['Senin', 'Rabu', 'Jumat'],
    ]);

    $response->assertSessionHas('success');
    expect(SchoolSetting::get('enable_default_extra_schedule', '1'))->toBe('1');
    expect(SchoolSetting::get('extra_days'))->toBe('Senin,Rabu,Jumat');

    // Test disabling default schedule
    $disableResponse = $this->actingAs($admin)->post('/master/settings', [
        'enable_default_extra_schedule' => '0',
        'extra_time_start' => '14:00',
        'extra_time_end' => '17:30',
        'extra_days' => ['Senin', 'Rabu'],
    ]);

    $disableResponse->assertSessionHas('success');
    expect(SchoolSetting::get('enable_default_extra_schedule'))->toBe('0');
});

test('admin can manage satpam staff category in website content and public website renders satpam', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    // Store a new satpam member
    $storeResponse = $this->actingAs($admin)->post('/master/website-content/staff', [
        'category' => 'satpam',
        'name' => 'Bambang Sudirman',
        'nip' => '20202265099',
        'gender' => 'L',
        'employment_status' => 'Satpam / Keamanan',
        'satpam_duty' => 'Petugas Keamanan Gerbang Timur',
        'order' => '1',
    ]);

    $storeResponse->assertRedirect('/master/website-content?tab=staff');
    $storeResponse->assertSessionHas('success');

    $staffList = json_decode(SchoolSetting::get('website_staff_json'), true);
    $satpam = collect($staffList)->firstWhere('nip', '20202265099');

    expect($satpam)->not->toBeNull();
    expect($satpam['category'])->toBe('satpam');
    expect($satpam['satpam_duty'])->toBe('Petugas Keamanan Gerbang Timur');
    expect($satpam['duty'])->toBe('Petugas Keamanan Gerbang Timur');

    // Public website renders Satpam card and Tracer Alumni link
    $webResponse = $this->get('/');
    $webResponse->assertStatus(200);
    $webResponse->assertSee('Bambang Sudirman');
    $webResponse->assertSee('Petugas Keamanan Gerbang Timur');
    $webResponse->assertSee('Tracer Study Alumni');
    $webResponse->assertSee('tracer-alumni');
});
