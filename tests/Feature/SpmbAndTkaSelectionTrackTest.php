<?php

use App\Models\PpdbRegistration;
use App\Models\SchoolClass;
use App\Models\SpmbTrack;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);
});

test('spmb dashboard and tka portal render successfully with proper quota metrics', function () {
    $res = $this->actingAs($this->admin)->get(route('spmb.index'));
    $res->assertStatus(200);
    $res->assertSee('Sistem Penerimaan Murid Baru (SPMB)');
    $res->assertSee('Daya Tampung SMA');
    $res->assertSee('Jalur Nilai TKA');

    $tkaRes = $this->actingAs($this->admin)->get(route('spmb.tka.index'));
    $tkaRes->assertStatus(200);
    $tkaRes->assertSee('Jalur SPMB Nilai TKA (Tes Kemampuan Akademik)');
    $tkaRes->assertSee('Skor Akhir Terbobot');
});

test('candidate registration for tka track automatically generates tka exam number', function () {
    $response = $this->actingAs($this->admin)->post(route('spmb.store'), [
        'full_name' => 'Fakhri Pratama Putra',
        'nisn' => '0098765432',
        'nik' => '3202998877660001',
        'gender' => 'L',
        'birth_place' => 'Sukabumi',
        'birth_date' => '2010-06-15',
        'school_origin' => 'SMP Negeri 1 Lengkong',
        'parent_name' => 'Bambang Pratama',
        'parent_phone' => '081234567899',
        'average_score' => 88.50,
        'selection_track' => 'Tes Kemampuan Akademik (TKA)',
    ]);

    $response->assertSessionHas('success');

    $registration = PpdbRegistration::where('nisn', '0098765432')->first();
    expect($registration)->not->toBeNull();
    expect($registration->selection_track)->toBe('Tes Kemampuan Akademik (TKA)');
    expect($registration->tka_exam_number)->not->toBeNull();
    expect($registration->tka_exam_number)->toContain('TKA-');
    expect($registration->tka_status)->toBe('Belum Ujian');
});

test('inputting tka scores computes average and weighted score 60 to 40 accurately', function () {
    $reg = PpdbRegistration::create([
        'registration_number' => 'SPMB-2026-TKA-9999',
        'tka_exam_number' => 'TKA-2026-9999',
        'full_name' => 'Aulia Salsabila',
        'nisn' => '0091122334',
        'gender' => 'P',
        'average_score' => 85.00, // Rapor SMP
        'selection_track' => 'Tes Kemampuan Akademik (TKA)',
        'status' => 'Terverifikasi',
    ]);

    // Input skor: Literasi 90, Numerasi 80, Sains 100 -> Rata-rata TKA = 90
    // Bobot TKA (60%) = 54.00, Bobot Rapor (40% x 85) = 34.00 -> Skor Akhir = 88.00
    $response = $this->actingAs($this->admin)->patch(route('spmb.tka.update_score', $reg), [
        'tka_score_literacy' => 90.00,
        'tka_score_numeracy' => 80.00,
        'tka_score_science' => 100.00,
        'tka_exam_session' => 'Sesi 1 (Lab CBT 1)',
        'tka_status' => 'Selesai Ujian',
    ]);

    $response->assertSessionHas('success');

    $reg->refresh();
    expect((float) $reg->tka_score_total)->toEqual(90.00);
    expect((float) $reg->tka_weighted_score)->toEqual(88.00);
    expect($reg->tka_status)->toBe('Selesai Ujian');
});

test('calculate rankings and auto admit tka candidates according to quota', function () {
    // Buat 3 peserta dengan skor berbeda
    PpdbRegistration::create([
        'registration_number' => 'SPMB-2026-TKA-0001',
        'full_name' => 'Peserta A (Juara 1)',
        'nisn' => '0091111111',
        'gender' => 'L',
        'average_score' => 90.00,
        'selection_track' => 'Tes Kemampuan Akademik (TKA)',
        'tka_score_total' => 95.00,
        'tka_weighted_score' => 93.00,
        'status' => 'Terverifikasi',
    ]);

    PpdbRegistration::create([
        'registration_number' => 'SPMB-2026-TKA-0002',
        'full_name' => 'Peserta B (Juara 2)',
        'nisn' => '0092222222',
        'gender' => 'P',
        'average_score' => 85.00,
        'selection_track' => 'Tes Kemampuan Akademik (TKA)',
        'tka_score_total' => 88.00,
        'tka_weighted_score' => 86.80,
        'status' => 'Terverifikasi',
    ]);

    PpdbRegistration::create([
        'registration_number' => 'SPMB-2026-TKA-0003',
        'full_name' => 'Peserta C (Cadangan)',
        'nisn' => '0093333333',
        'gender' => 'L',
        'average_score' => 80.00,
        'selection_track' => 'Tes Kemampuan Akademik (TKA)',
        'tka_score_total' => 75.00,
        'tka_weighted_score' => 77.00,
        'status' => 'Terverifikasi',
    ]);

    // Hitung ranking
    $this->actingAs($this->admin)->post(route('spmb.tka.calculate_rankings'))->assertSessionHas('success');

    $pesertaA = PpdbRegistration::where('nisn', '0091111111')->first();
    $pesertaB = PpdbRegistration::where('nisn', '0092222222')->first();
    $pesertaC = PpdbRegistration::where('nisn', '0093333333')->first();

    expect($pesertaA->tka_rank)->toBe(1);
    expect($pesertaB->tka_rank)->toBe(2);
    expect($pesertaC->tka_rank)->toBe(3);

    // Auto admit kuota = 2
    $this->actingAs($this->admin)->post(route('spmb.tka.auto_admit'), ['quota' => 2])->assertSessionHas('success');

    $pesertaA->refresh();
    $pesertaB->refresh();
    $pesertaC->refresh();

    expect($pesertaA->status)->toBe('Lolos Seleksi');
    expect($pesertaB->status)->toBe('Lolos Seleksi');
    expect($pesertaC->status)->toBe('Cadangan');
});

test('spmb official printable documents render without error', function () {
    $reg = PpdbRegistration::create([
        'registration_number' => 'SPMB-2026-TKA-8888',
        'tka_exam_number' => 'TKA-2026-8888',
        'full_name' => 'Reza Fahlevi',
        'nisn' => '0094444444',
        'gender' => 'L',
        'average_score' => 88.00,
        'selection_track' => 'Tes Kemampuan Akademik (TKA)',
        'tka_score_total' => 90.00,
        'tka_weighted_score' => 89.20,
        'tka_rank' => 1,
        'status' => 'Lolos Seleksi',
        'assigned_class' => 'X Merdeka 1',
    ]);

    // Kartu Peserta Ujian TKA / Formulir Pendaftaran
    $cardRes = $this->actingAs($this->admin)->get(route('spmb.print-card', $reg));
    $cardRes->assertStatus(200);
    $cardRes->assertSee('KARTU TANDA PESERTA SPMB');
    $cardRes->assertSee('TKA-2026-8888');

    // SK Kelulusan SPMB
    $letterRes = $this->actingAs($this->admin)->get(route('spmb.print-letter', $reg));
    $letterRes->assertStatus(200);
    $letterRes->assertSee('SURAT KEPUTUSAN KELULUSAN SELEKSI SPMB');
    $letterRes->assertSee('LOLOS SELEKSI (DITERIMA)');

    // Rekapitulasi Hasil SPMB & Perangkingan
    $recapRes = $this->actingAs($this->admin)->get(route('spmb.print-recap', ['track' => 'TKA']));
    $recapRes->assertStatus(200);
    $recapRes->assertSee('REKAPITULASI PENETAPAN HASIL SELEKSI');
});

test('promoting spmb candidate seamlessly integrates into sissma1le active students', function () {
    $class = SchoolClass::firstOrCreate(
        ['name' => 'X Merdeka 1'],
        ['grade' => 'X', 'academic_year' => '2026/2027']
    );

    $reg = PpdbRegistration::create([
        'registration_number' => 'SPMB-2026-ZON-7777',
        'full_name' => 'Zahra Anindya',
        'nisn' => '0095555555',
        'nik' => '3202555555550001',
        'gender' => 'P',
        'school_origin' => 'SMPN 1 Lengkong',
        'average_score' => 87.00,
        'selection_track' => 'Zonasi',
        'status' => 'Lolos Seleksi',
        'assigned_class' => 'X Merdeka 1',
    ]);

    $response = $this->actingAs($this->admin)->post(route('spmb.promote', $reg));
    $response->assertSessionHas('success');

    $student = Student::where('nisn', '0095555555')->first();
    expect($student)->not->toBeNull();
    expect($student->name)->toBe('Zahra Anindya');
    expect($student->current_rombel)->toBe('X Merdeka 1');
    expect($student->current_class_level)->toBe('X');
    expect($student->status)->toBe('Aktif');
});

test('admin can manage and customize spmb selection tracks dynamically', function () {
    // 1. Render halaman kustomisasi jalur
    $res = $this->actingAs($this->admin)->get(route('spmb.tracks.index'));
    $res->assertStatus(200);
    $res->assertSee('Kustomisasi');
    $res->assertSee('Kelola Jalur Seleksi');

    // 2. Tambah jalur seleksi baru (Custom Track)
    $createRes = $this->actingAs($this->admin)->post(route('spmb.tracks.store'), [
        'name' => 'Jalur Tahfidz Al-Quran',
        'code' => 'tahfidz',
        'quota_percentage' => 5.00,
        'quota_seats' => 18,
        'description' => 'Jalur khusus penghafal Al-Quran minimal 3 Juz',
        'requirements' => '1. Sertifikat Syahadah Tahfidz min 3 Juz\n2. Tes sambung ayat',
        'is_tka' => false,
        'is_active' => true,
        'sort_order' => 6,
    ]);
    $createRes->assertSessionHas('success');

    $track = SpmbTrack::where('code', 'tahfidz')->first();
    expect($track)->not->toBeNull();
    expect($track->name)->toBe('Jalur Tahfidz Al-Quran');
    expect((float) $track->quota_percentage)->toBe(5.00);

    // 3. Update jalur seleksi (Edit Quota & Kustomisasi)
    $updateRes = $this->actingAs($this->admin)->put(route('spmb.tracks.update', $track), [
        'name' => 'Jalur Tahfidz Quran Unggulan',
        'code' => 'tahfidz',
        'quota_percentage' => 7.00,
        'quota_seats' => 25,
        'description' => 'Jalur khusus penghafal Al-Quran minimal 5 Juz',
        'requirements' => '1. Syahadah Tahfidz min 5 Juz',
        'is_tka' => false,
        'is_active' => true,
        'sort_order' => 6,
    ]);
    $updateRes->assertSessionHas('success');

    $track->refresh();
    expect($track->name)->toBe('Jalur Tahfidz Quran Unggulan');
    expect((float) $track->quota_percentage)->toBe(7.00);

    // 4. Hapus jalur seleksi jika belum ada pendaftar
    $deleteRes = $this->actingAs($this->admin)->delete(route('spmb.tracks.destroy', $track));
    $deleteRes->assertSessionHas('success');
    expect(SpmbTrack::where('code', 'tahfidz')->first())->toBeNull();
});

test('prospective students can register publicly on website and receive registration card', function () {
    // 1. Website homepage shows prominent SPMB registration link and CTA
    $homeRes = $this->get('/');
    $homeRes->assertStatus(200);
    $homeRes->assertSee(route('spmb.public.register'));
    $homeRes->assertSee('Daftar SPMB');

    // 2. Open public registration page (guest)
    $registerPageRes = $this->get(route('spmb.public.register'));
    $registerPageRes->assertStatus(200);
    $registerPageRes->assertSee('Penerimaan Siswa Baru');
    $registerPageRes->assertSee('Identitas Calon Peserta Didik Baru');

    // Get active TKA track
    $tkaTrack = SpmbTrack::where('code', 'tka')->first()
        ?? SpmbTrack::where('is_tka', true)->first();
    expect($tkaTrack)->not->toBeNull();

    // 3. Submit public registration
    $submitRes = $this->post(route('spmb.public.store'), [
        'full_name' => 'Dwi Nurhaliza Putri',
        'nisn' => '0097788990',
        'nik' => '3202112233440002',
        'gender' => 'P',
        'birth_place' => 'Sukabumi',
        'birth_date' => '2010-08-20',
        'school_origin' => 'SMPN 2 Lengkong',
        'address' => 'Jl. Raya Lengkong No. 45 Sukabumi',
        'distance_km' => 2.1,
        'parent_name' => 'Supriyanto',
        'parent_phone' => '085712345678',
        'parent_job' => 'Wiraswasta',
        'average_score' => 89.25,
        'spmb_track_id' => $tkaTrack->id,
    ]);

    $reg = PpdbRegistration::where('nisn', '0097788990')->first();
    expect($reg)->not->toBeNull();
    expect($reg->full_name)->toBe('Dwi Nurhaliza Putri');
    expect($reg->tka_exam_number)->not->toBeNull();
    expect($reg->status)->toBe('Terdaftar');

    // 4. Redirects to public confirmation page
    $submitRes->assertRedirect(route('spmb.public.success', $reg->registration_number));

    // 5. Public confirmation page renders successfully
    $successPageRes = $this->get(route('spmb.public.success', $reg->registration_number));
    $successPageRes->assertStatus(200);
    $successPageRes->assertSee($reg->registration_number);
    $successPageRes->assertSee('Dwi Nurhaliza Putri');
    $successPageRes->assertSee($reg->tka_exam_number);

    // 6. Public printable card renders successfully
    $printCardRes = $this->get(route('spmb.public.print', $reg->registration_number));
    $printCardRes->assertStatus(200);
    $printCardRes->assertSee('KARTU TANDA PESERTA SPMB');
    $printCardRes->assertSee($reg->registration_number);
});
