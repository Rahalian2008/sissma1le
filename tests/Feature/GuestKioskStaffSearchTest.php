<?php

use App\Models\Guest;
use App\Models\SchoolIdentity;
use App\Models\Teacher;
use App\Models\User;

test('guest kiosk page displays active teachers and staff in combobox from master data', function () {
    SchoolIdentity::create([
        'npsn' => '20202265',
        'name' => 'SMA Negeri 1 Lengkong',
        'headmaster_name' => 'Pian Sopian, S.Pd., MM.Pd.',
    ]);

    $teacher = Teacher::create([
        'name' => 'Undang Mulyana, S.Pd.I., M.Pd.',
        'employment_type' => 'Guru',
        'position' => 'Wakasek Sarpras',
        'nip' => '196806122000101001',
        'is_active' => true,
    ]);

    $staff = Teacher::create([
        'name' => 'Rachmat Sugiarto, A.Md.',
        'employment_type' => 'Tenaga Kependidikan',
        'position' => 'Tenaga Administrasi Sekolah',
        'nip' => '198112282025211059',
        'is_active' => true,
    ]);

    $response = $this->get(route('guests.kiosk'));
    $response->assertStatus(200);

    // Assert staff data is present in datalist and script
    $response->assertSee('Undang Mulyana, S.Pd.I., M.Pd.');
    $response->assertSee('Rachmat Sugiarto, A.Md.');
    $response->assertSee('destination_person_name');
    $response->assertSee('staff_datalist');
    $response->assertSee('staff-dropdown-panel');
    $response->assertSee('btn-toggle-staff-dropdown');
    $response->assertSee('Jika tidak ada di daftar, ketik nama bebas secara manual');
});

test('guest kiosk allows submitting a selected teacher or custom manual name', function () {
    SchoolIdentity::create([
        'npsn' => '20202265',
        'name' => 'SMA Negeri 1 Lengkong',
    ]);

    // Test with manual custom name (not in master data)
    $customName = 'Bapak Hendra (Konsultan IT PT Telkom)';
    $postData = [
        'name' => 'Ahmad Fauzi',
        'gender' => 'Laki-laki',
        'identity_type' => 'KTP',
        'identity_number' => '3210123456780001',
        'phone' => '081234567890',
        'email' => 'tamu@example.com',
        'institution' => 'PT Telkom Indonesia',
        'institution_type' => 'Perusahaan / Dunia Usaha / Vendor',
        'destination_department' => 'Layanan Administrasi TU',
        'destination_person_name' => $customName,
        'purpose' => 'Kedinasan / Rapat / Koordinasi Resmi',
        'purpose_detail' => 'Koordinasi jaringan internet sekolah',
        'number_of_guests' => 1,
        'vehicle_type' => 'Mobil Pribadi / Dinas',
        'vehicle_license_plate' => 'B 1234 ABC',
        'selfie_image' => 'data:image/jpeg;base64,'.base64_encode('fake-image-bytes'),
    ];

    $response = $this->post(route('guests.kiosk.store'), $postData);
    $response->assertRedirect();

    $guest = Guest::latest()->first();
    expect($guest)->not->toBeNull()
        ->and($guest->destination_person_name)->toBe($customName);
});

test('admin buku tamu page receives staff members for edit combobox', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    $response = $this->actingAs($admin)->get(route('guests.index'));
    $response->assertStatus(200);
    $response->assertSee('staff_datalist_admin');
});
