<?php

use App\Models\Guest;
use Illuminate\Support\Facades\Storage;

test('kiosk tamu page loads successfully with auto camera elements and without file upload', function () {
    $response = $this->get(route('guests.kiosk'));

    $response->assertOk();
    $response->assertSee('Ambil Foto Wajah (Selfie) Langsung');
    $response->assertSee('id="camera-container"', false);
    $response->assertSee('id="btn-capture-photo"', false);
    $response->assertSee('id="selfie-image-data"', false);
    $response->assertSee('startCamera();', false);

    // Pastikan input file upload media sudah dihapus sepenuhnya dari tampilan
    $response->assertDontSee('name="selfie_file"', false);
    $response->assertDontSee('Atau unggah file foto:');
});

test('submitting guest kiosk without live selfie fails validation', function () {
    $response = $this->post(route('guests.kiosk.store'), [
        'name' => 'Budi Santoso',
        'gender' => 'Laki-laki',
        'identity_type' => 'KTP',
        'identity_number' => '3201234567890001',
        'phone' => '081234567890',
        'institution' => 'Dinas Pendidikan',
        'institution_type' => 'Kementerian / Instansi Pemerintah',
        'destination_department' => 'Kepala Sekolah',
        'purpose' => 'Kedinasan / Rapat / Koordinasi Resmi',
        'purpose_detail' => 'Koordinasi program sekolah penggerak',
        'number_of_guests' => 1,
    ]);

    $response->assertSessionHasErrors(['selfie_image']);
});

test('submitting guest kiosk with live selfie succeeds and creates guest record', function () {
    Storage::fake('public');

    // 1x1 transparent GIF as base64 data URI
    $base64Image = 'data:image/jpeg;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    $response = $this->post(route('guests.kiosk.store'), [
        'name' => 'Dr. H. Ahmad Dahlan',
        'gender' => 'Laki-laki',
        'identity_type' => 'KTP',
        'identity_number' => '3201999888770002',
        'phone' => '081398765432',
        'email' => 'ahmad.dahlan@example.com',
        'institution' => 'Pengawas Pembina Cabdin',
        'institution_type' => 'Pengawas Pembina Sekolah',
        'destination_department' => 'Kepala Sekolah',
        'destination_person_name' => 'Drs. H. Suwandi',
        'purpose' => 'Kedinasan / Rapat / Koordinasi Resmi',
        'purpose_detail' => 'Monitoring & Evaluasi Manajemen Sekolah',
        'number_of_guests' => 2,
        'vehicle_type' => 'Mobil',
        'vehicle_license_plate' => 'B 1234 SIK',
        'selfie_image' => $base64Image,
    ]);

    $guest = Guest::where('identity_number', '3201999888770002')->first();
    expect($guest)->not->toBeNull()
        ->and($guest->name)->toBe('Dr. H. Ahmad Dahlan')
        ->and($guest->selfie_path)->not->toBeNull();

    $response->assertRedirect(route('guests.card', $guest->ticket_number));
});
