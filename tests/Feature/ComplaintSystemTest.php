<?php

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('public can view the complaint form without logging in', function () {
    $response = $this->get(route('complaints.create'));
    $response->assertOk()
        ->assertSee('Kanal Pengaduan', false)
        ->assertSee('Kirim Sebagai Anonim');
});

test('public can submit a regular complaint and receive a ticket number', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('foto_bukti.jpg', 500, 'image/jpeg');

    $response = $this->post(route('complaints.store'), [
        'is_anonymous' => 0,
        'reporter_name' => 'Ahmad Fauzi',
        'reporter_phone' => '081234567890',
        'reporter_email' => 'ahmad@example.com',
        'reporter_type' => 'orang_tua',
        'category' => 'sarana_prasarana',
        'title' => 'Usulan Perbaikan Keran Air Toilet Lantai 2',
        'description' => 'Keran air di toilet siswa lantai 2 bocor dan air terus mengalir, mohon diperbaiki.',
        'incident_date' => now()->toDateString(),
        'incident_location' => 'Toilet Siswa Lt. 2',
        'attachment' => $file,
    ]);

    $complaint = Complaint::where('reporter_phone', '081234567890')->first();
    expect($complaint)->not->toBeNull()
        ->and($complaint->ticket_number)->toStartWith('ADU-')
        ->and($complaint->status)->toBe('MENUNGGU')
        ->and($complaint->is_anonymous)->toBeFalse()
        ->and($complaint->attachment_path)->not->toBeNull();

    $response->assertRedirect(route('complaints.success', $complaint->ticket_number));
});

test('public can submit an anonymous complaint', function () {
    $response = $this->post(route('complaints.store'), [
        'is_anonymous' => 1,
        'reporter_phone' => '089876543210',
        'reporter_type' => 'siswa',
        'category' => 'bullying_perundungan',
        'title' => 'Laporan Dugaan Intimidasi Siswa Baru',
        'description' => 'Ada intimidasi verbal yang terjadi di lorong belakang kelas X saat jam istirahat kedua.',
    ]);

    $complaint = Complaint::where('reporter_phone', '089876543210')->first();
    expect($complaint)->not->toBeNull()
        ->and($complaint->is_anonymous)->toBeTrue()
        ->and($complaint->category)->toBe('bullying_perundungan')
        ->and($complaint->status)->toBe('MENUNGGU');

    $response->assertRedirect(route('complaints.success', $complaint->ticket_number));
});

test('public can track complaint status using ticket number', function () {
    $complaint = Complaint::create([
        'ticket_number' => 'ADU-20261125-TEST',
        'is_anonymous' => false,
        'reporter_name' => 'Budi Santoso',
        'reporter_phone' => '081122334455',
        'reporter_type' => 'masyarakat',
        'category' => 'aspirasi_kemajuan',
        'title' => 'Usulan Pembangunan Taman Baca Digital',
        'description' => 'Usulan pembuatan taman baca outdoor dengan akses WiFi dan barcode e-library.',
        'status' => 'DIPROSES',
        'disposition_notes' => 'Terima kasih atas aspirasi Anda. Usulan telah dibahas dalam rapat kerja sekolah.',
    ]);

    $response = $this->get(route('complaints.track', ['ticket' => 'ADU-20261125-TEST']));
    $response->assertOk()
        ->assertSee('ADU-20261125-TEST')
        ->assertSee('Usulan Pembangunan Taman Baca Digital')
        ->assertSee('Terima kasih atas aspirasi Anda');
});

test('unauthenticated users cannot access admin backoffice', function () {
    $response = $this->get(route('complaints.admin.index'));
    $response->assertRedirect(route('login'));
});

test('non-leadership users like regular guru cannot access complaints backoffice', function () {
    $guru = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru'],
    ]);

    $response = $this->actingAs($guru)->get(route('complaints.admin.index'));
    $response->assertForbidden();
});

test('kepala sekolah can access complaints backoffice and update disposition', function () {
    $kepsek = User::factory()->create([
        'role' => 'kepala_sekolah',
        'roles' => ['kepala_sekolah'],
    ]);

    $complaint = Complaint::create([
        'ticket_number' => 'ADU-20261125-EXEC',
        'is_anonymous' => true,
        'reporter_name' => 'Anonim',
        'reporter_phone' => '081299887766',
        'reporter_type' => 'siswa',
        'category' => 'sarana_prasarana',
        'title' => 'Lampu Kelas Mati',
        'description' => 'Dua lampu di kelas XI-1 mati dan membuat ruangan redup.',
        'status' => 'MENUNGGU',
    ]);

    // 1. List view
    $response = $this->actingAs($kepsek)->get(route('complaints.admin.index'));
    $response->assertOk()
        ->assertSee('ADU-20261125-EXEC')
        ->assertSee('Lampu Kelas Mati');

    // 2. Detail view
    $response = $this->actingAs($kepsek)->get(route('complaints.admin.show', $complaint));
    $response->assertOk()
        ->assertSee('ADU-20261125-EXEC')
        ->assertSee('Lampu Kelas Mati');

    // 3. Update disposition
    $updateResponse = $this->actingAs($kepsek)->put(route('complaints.admin.disposition', $complaint), [
        'status' => 'SELESAI',
        'disposition_notes' => 'Lampu telah diganti oleh petugas sarpras sekolah.',
        'internal_notes' => 'Koordinasi selesai dengan staf sarpras.',
    ]);

    $updateResponse->assertRedirect();

    $complaint->refresh();
    expect($complaint->status)->toBe('SELESAI')
        ->and($complaint->disposition_notes)->toBe('Lampu telah diganti oleh petugas sarpras sekolah.')
        ->and($complaint->handled_by)->toBe($kepsek->id);
});

test('wakasek kesiswaan can also access and manage complaints', function () {
    $wakasek = User::factory()->create([
        'role' => 'guru',
        'roles' => ['guru', 'wakasek_kesiswaan'],
    ]);

    $response = $this->actingAs($wakasek)->get(route('complaints.admin.index'));
    $response->assertOk()
        ->assertSee('Kanal Pengaduan & Aspirasi Publik');
});
