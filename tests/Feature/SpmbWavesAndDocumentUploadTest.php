<?php

use App\Models\PpdbRegistration;
use App\Models\SchoolSetting;
use App\Models\SpmbTrack;
use App\Models\SpmbWave;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    Storage::fake('public');
});

test('spmb wave index displays waves and allows creation, update, and status toggle', function () {
    // 1. Index page
    $response = $this->actingAs($this->admin)->get(route('spmb.waves.index'));
    $response->assertStatus(200);
    $response->assertSee('Jadwal Gelombang Pendaftaran');
    $response->assertSee('Gelombang 1');

    // 2. Create Wave
    $track = SpmbTrack::first();
    $createRes = $this->actingAs($this->admin)->post(route('spmb.waves.store'), [
        'name' => 'Gelombang 3 (Afirmasi Khusus)',
        'wave_number' => 3,
        'academic_year' => '2026/2027',
        'start_date' => '2026-07-01',
        'end_date' => '2026-07-15',
        'announcement_date' => '2026-07-17',
        're_registration_start' => '2026-07-18',
        're_registration_end' => '2026-07-20',
        'quota_seats' => 50,
        'allowed_tracks' => [$track->id],
        'is_active' => 1,
        'description' => 'Gelombang khusus afirmasi dan penelusuran bakat',
    ]);

    $createRes->assertSessionHas('success');
    $wave = SpmbWave::where('wave_number', 3)->first();
    expect($wave)->not->toBeNull();
    expect($wave->name)->toBe('Gelombang 3 (Afirmasi Khusus)');
    expect($wave->quota_seats)->toBe(50);
    expect($wave->is_active)->toBeTrue();

    // 3. Update Wave
    $updateRes = $this->actingAs($this->admin)->put(route('spmb.waves.update', $wave), [
        'name' => 'Gelombang 3 (Revisi Kuota)',
        'wave_number' => 3,
        'academic_year' => '2026/2027',
        'start_date' => '2026-07-01',
        'end_date' => '2026-07-16',
        'announcement_date' => '2026-07-18',
        'quota_seats' => 60,
        'allowed_tracks' => [$track->id],
        'is_active' => 1,
    ]);

    $updateRes->assertSessionHas('success');
    $wave->refresh();
    expect($wave->name)->toBe('Gelombang 3 (Revisi Kuota)');
    expect($wave->quota_seats)->toBe(60);

    // 4. Toggle Status (Active -> Inactive)
    $toggleRes = $this->actingAs($this->admin)->patch(route('spmb.waves.toggle-status', $wave));
    $toggleRes->assertSessionHas('success');
    $wave->refresh();
    expect($wave->is_active)->toBeFalse();

    // 5. Toggle Status back to Active
    $this->actingAs($this->admin)->patch(route('spmb.waves.toggle-status', $wave));
    $wave->refresh();
    expect($wave->is_active)->toBeTrue();
});

test('admin can update total spmb capacity and track quotas', function () {
    $track = SpmbTrack::first();

    $response = $this->actingAs($this->admin)->post(route('spmb.capacity.update'), [
        'total_capacity' => 432, // 12 rombel x 36
        'track_quotas' => [
            $track->id => 200,
        ],
    ]);

    $response->assertSessionHas('success');
    expect((int) SchoolSetting::get('spmb_total_capacity'))->toBe(432);

    $track->refresh();
    expect($track->quota_seats)->toBe(200);
});

test('public registration page loads active wave and tracks', function () {
    $response = $this->get(route('spmb.public.register'));
    $response->assertStatus(200);
    $response->assertSee('Penerimaan Murid Baru (SPMB)');
    $response->assertSee('Upload Berkas Identitas Kependudukan');
    $response->assertSee('KTP Orang Tua / Wali');
    $response->assertSee('Kartu Keluarga (KK)');
    $response->assertSee('Akte Kelahiran Siswa');
    $response->assertSee('Semester 1 (Kelas VII Gasal)');
    $response->assertSee('Semester 5 (Kelas IX Gasal)');
    $response->assertSee('Semester 6 (Opsional)');
});

test('public registration validates mandatory files and semester 1 to 5 report scores', function () {
    $track = SpmbTrack::first();

    $response = $this->post(route('spmb.public.store'), [
        'is_public_form' => 1,
        'spmb_track_id' => $track->id,
        'full_name' => 'Calon Siswa Belum Lengkap',
        'nisn' => '0099887766',
        'nik' => '3202112233445566',
        'gender' => 'L',
        'birth_place' => 'Sukabumi',
        'birth_date' => '2010-05-12',
        'school_origin' => 'SMPN 1 Lengkong',
        'address' => 'Jl. Lengkong No. 12',
        'parent_name' => 'Orang Tua',
        'parent_phone' => '081234567890',
        // Nilai rapor dan file dikosongkan
    ]);

    $response->assertSessionHasErrors([
        'ktp_parent_file',
        'kk_file',
        'birth_cert_file',
        'report_score_sem1',
        'report_score_sem2',
        'report_score_sem3',
        'report_score_sem4',
        'report_score_sem5',
        'report_file_sem1',
        'report_file_sem2',
        'report_file_sem3',
        'report_file_sem4',
        'report_file_sem5',
    ]);
});

test('public candidate successfully registers with uploaded documents and computed semester average', function () {
    $wave = SpmbWave::where('is_active', true)->first();
    $track = SpmbTrack::where('is_tka', true)->first() ?? SpmbTrack::first();

    $ktpFile = UploadedFile::fake()->create('ktp_ayah.pdf', 500, 'application/pdf');
    $kkFile = UploadedFile::fake()->create('kk_keluarga.jpg', 600, 'image/jpeg');
    $akteFile = UploadedFile::fake()->create('akte_lahir.pdf', 400, 'application/pdf');
    $kipFile = UploadedFile::fake()->create('kartu_pip.png', 300, 'image/png');
    $tkaCert = UploadedFile::fake()->create('sertifikat_tka.pdf', 700, 'application/pdf');

    $sem1 = UploadedFile::fake()->create('rapor_sem1.pdf', 300, 'application/pdf');
    $sem2 = UploadedFile::fake()->create('rapor_sem2.pdf', 300, 'application/pdf');
    $sem3 = UploadedFile::fake()->create('rapor_sem3.pdf', 300, 'application/pdf');
    $sem4 = UploadedFile::fake()->create('rapor_sem4.pdf', 300, 'application/pdf');
    $sem5 = UploadedFile::fake()->create('rapor_sem5.pdf', 300, 'application/pdf');

    $response = $this->post(route('spmb.public.store'), [
        'spmb_wave_id' => $wave?->id,
        'spmb_track_id' => $track->id,
        'full_name' => 'Muhammad Bintang Pratama',
        'nisn' => '0087654321',
        'nik' => '3202112233440005',
        'gender' => 'L',
        'birth_place' => 'Sukabumi',
        'birth_date' => '2010-08-20',
        'school_origin' => 'SMP Negeri 1 Lengkong',
        'address' => 'Kp. Cijangkar RT 02 RW 01 Desa Lengkong',
        'distance_km' => 1.8,
        'parent_name' => 'Ahmad Suhendar',
        'parent_phone' => '085712345678',
        'parent_job' => 'Wiraswasta',
        'kip_pip_number' => 'PIP-2026-991',

        // Entri Nilai Rapor Sem 1-5 (Rata-rata: (80 + 82 + 84 + 86 + 88) / 5 = 84.00)
        'report_score_sem1' => 80.00,
        'report_score_sem2' => 82.00,
        'report_score_sem3' => 84.00,
        'report_score_sem4' => 86.00,
        'report_score_sem5' => 88.00,

        // Upload Berkas Wajib
        'ktp_parent_file' => $ktpFile,
        'kk_file' => $kkFile,
        'birth_cert_file' => $akteFile,
        'pip_kip_file' => $kipFile,
        'tka_certificate_file' => $tkaCert,
        'tka_score_total' => 85.00,

        // Upload Berkas Rapor
        'report_file_sem1' => $sem1,
        'report_file_sem2' => $sem2,
        'report_file_sem3' => $sem3,
        'report_file_sem4' => $sem4,
        'report_file_sem5' => $sem5,
    ]);

    $response->assertSessionHas('success');

    $candidate = PpdbRegistration::where('nisn', '0087654321')->first();
    expect($candidate)->not->toBeNull();
    expect($candidate->full_name)->toBe('Muhammad Bintang Pratama');
    expect((float) $candidate->average_score)->toEqual(84.00);
    expect($candidate->spmb_wave_id)->toEqual($wave?->id);

    // Verify files stored on disk
    expect($candidate->ktp_parent_file)->not->toBeNull();
    expect($candidate->kk_file)->not->toBeNull();
    expect($candidate->birth_cert_file)->not->toBeNull();
    expect($candidate->pip_kip_file)->not->toBeNull();
    expect($candidate->report_file_sem1)->not->toBeNull();
    expect($candidate->report_file_sem5)->not->toBeNull();
    expect($candidate->tka_certificate_file)->not->toBeNull();

    Storage::disk('public')->assertExists($candidate->ktp_parent_file);
    Storage::disk('public')->assertExists($candidate->report_file_sem1);
    Storage::disk('public')->assertExists($candidate->tka_certificate_file);

    // Verify Detail Page
    $detailRes = $this->actingAs($this->admin)->get(route('spmb.show', $candidate));
    $detailRes->assertStatus(200);
    $detailRes->assertSee('Muhammad Bintang Pratama');
    $detailRes->assertSee('Berkas Dokumen Persyaratan Pendaftar');
    $detailRes->assertSee('KTP Orang Tua / Wali');
    $detailRes->assertSee('Kartu Keluarga (KK)');
    $detailRes->assertSee('Akte Kelahiran');
    $detailRes->assertSee('Rincian Nilai');
    $detailRes->assertSee('84.00');
});
