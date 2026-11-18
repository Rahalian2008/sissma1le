<?php

use App\Models\BkConsultation;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('student can access curhat page and start a new consultation thread', function () {
    Storage::fake('public');

    $rand = Str::lower(Str::random(6));

    $studentUser = User::create([
        'name' => 'Siswa Curhat Test',
        'email' => "siswa_curhat_{$rand}@sman1lengkong.sch.id",
        'username' => "nisn_{$rand}",
        'nip' => null,
        'role' => 'siswa',
        'roles' => ['siswa'],
        'password' => bcrypt('password'),
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'X-Merdeka-1',
        'grade' => 'X',
        'major' => 'Umum',
    ]);

    $student = Student::create([
        'user_id' => $studentUser->id,
        'nis' => '77'.rand(1000, 9999),
        'nisn' => "nisn_{$rand}",
        'name' => 'Siswa Curhat Test',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $response = $this->actingAs($studentUser)->get(route('bk.curhat.index'));
    $response->assertStatus(200);
    $response->assertSee('Ruang Curhat &amp; Konseling Online BK', false);

    // Kirim formulir mulai curhat baru dengan lampiran gambar
    $image = UploadedFile::fake()->image('masalah_belajar.jpg', 600, 400);

    $storeResponse = $this->actingAs($studentUser)->post(route('bk.curhat.store'), [
        'category' => 'BELAJAR',
        'topic' => 'Kesulitan Belajar Matematika',
        'message' => 'Halo Bu Guru BK, saya merasa kesulitan memahami materi kalkulus.',
        'is_anonymous' => 0,
        'attachment' => $image,
    ]);

    $consultation = BkConsultation::where('student_id', $student->id)->first();
    expect($consultation)->not->toBeNull();
    expect($consultation->topic)->toBe('Kesulitan Belajar Matematika');
    expect($consultation->category)->toBe('BELAJAR');
    expect($consultation->unread_counselor_count)->toBe(1);

    $storeResponse->assertRedirect(route('bk.curhat.index', ['c' => $consultation->id]));

    // Cek pesan pertama dan file tersimpan
    $firstMessage = $consultation->messages()->first();
    expect($firstMessage)->not->toBeNull();
    expect($firstMessage->message)->toContain('Halo Bu Guru BK');
    expect($firstMessage->is_image)->toBeTrue();
    Storage::disk('public')->assertExists($firstMessage->attachment_path);
});

test('counselor can view consultations and reply with attachments', function () {
    Storage::fake('public');

    $rand = Str::lower(Str::random(6));

    // Buat siswa & konsultasi
    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'XI-Merdeka-2',
        'grade' => 'XI',
        'major' => 'Umum',
    ]);

    $student = Student::create([
        'nis' => '66'.rand(1000, 9999),
        'nisn' => '00'.rand(10000000, 99999999),
        'name' => 'Alfan Konseling',
        'class_id' => $class->id,
        'gender' => 'L',
        'is_active' => true,
    ]);

    $consultation = BkConsultation::create([
        'consultation_code' => 'CRH-TEST-'.rand(100, 999),
        'student_id' => $student->id,
        'topic' => 'Konsultasi Studi Lanjut PTN',
        'category' => 'KARIER',
        'status' => 'OPEN',
        'unread_counselor_count' => 1,
        'unread_student_count' => 0,
        'last_message_at' => now(),
        'last_message_preview' => 'Saya mau tanya info SNBP',
    ]);

    // Buat konselor
    $counselorUser = User::create([
        'name' => 'Guru BK S.Pd.',
        'email' => "counselor_{$rand}@sman1lengkong.sch.id",
        'username' => "bk_{$rand}",
        'nip' => '198001012005011005',
        'role' => 'guru_bk',
        'roles' => ['guru_bk', 'bk'],
        'password' => bcrypt('password'),
    ]);

    // Konselor akses halaman curhat
    $response = $this->actingAs($counselorUser)->get(route('bk.curhat.index', ['c' => $consultation->id]));
    $response->assertStatus(200);
    $response->assertSee('Alfan Konseling');
    $response->assertSee('Konsultasi Studi Lanjut PTN');

    // Konselor membalas via AJAX
    $doc = UploadedFile::fake()->create('panduan_snbp.pdf', 500, 'application/pdf');

    $replyResponse = $this->actingAs($counselorUser)->postJson(route('bk.curhat.message', $consultation), [
        'message' => 'Halo Alfan, ini panduan SNBP terbaru: https://snbp.bppp.kemdikbud.go.id',
        'attachment' => $doc,
    ]);

    $replyResponse->assertStatus(200);
    $replyResponse->assertJson(['success' => true]);

    $consultation->refresh();
    expect($consultation->unread_student_count)->toBe(1);
    expect($consultation->status)->toBe('ACTIVE');

    $latestMessage = $consultation->messages()->latest('id')->first();
    expect($latestMessage)->not->toBeNull();
    expect($latestMessage->sender_role)->toBe('COUNSELOR');
    expect($latestMessage->is_document)->toBeTrue();
    Storage::disk('public')->assertExists($latestMessage->attachment_path);

    // Test live polling fetch-messages
    $fetchResponse = $this->actingAs($counselorUser)->getJson(route('bk.curhat.fetch-messages', $consultation));
    $fetchResponse->assertStatus(200);
    $fetchResponse->assertJsonStructure(['success', 'messages', 'consultation']);

    // Test resolve consultation
    $resolveResponse = $this->actingAs($counselorUser)->post(route('bk.curhat.resolve', $consultation), [
        'conclusion_notes' => 'Siswa sudah paham tahapan SNBP.',
    ]);
    $resolveResponse->assertRedirect(route('bk.curhat.index', ['c' => $consultation->id]));

    $consultation->refresh();
    expect($consultation->status)->toBe('RESOLVED');
});

test('student can choose a specific counselor and send voice note audio recording', function () {
    Storage::fake('public');

    $rand = Str::lower(Str::random(6));

    // Buat Koordinator BK User
    $counselorUser = User::create([
        'name' => 'Ida Marttin Maolidah SE, SP.,M.Pd.',
        'email' => "koordinator_bk_{$rand}@sman1lengkong.sch.id",
        'username' => "koor_{$rand}",
        'nip' => '197803102008012006',
        'role' => 'koordinator_bk',
        'roles' => ['koordinator_bk', 'guru_bk', 'bk'],
        'password' => bcrypt('password'),
    ]);

    // Buat Siswa User
    $studentUser = User::create([
        'name' => 'Siswa Audio Test',
        'email' => "siswa_audio_{$rand}@sman1lengkong.sch.id",
        'username' => "nisn_audio_{$rand}",
        'nip' => null,
        'role' => 'siswa',
        'roles' => ['siswa'],
        'password' => bcrypt('password'),
    ]);

    $class = SchoolClass::first() ?? SchoolClass::create([
        'name' => 'XII-Merdeka-1',
        'grade' => 'XII',
        'major' => 'Umum',
    ]);

    $student = Student::create([
        'user_id' => $studentUser->id,
        'nis' => '88'.rand(1000, 9999),
        'nisn' => "nisn_audio_{$rand}",
        'name' => 'Siswa Audio Test',
        'class_id' => $class->id,
        'gender' => 'P',
        'is_active' => true,
    ]);

    // Siswa membuat konsultasi memilih Koordinator BK secara spesifik
    $storeResponse = $this->actingAs($studentUser)->post(route('bk.curhat.store'), [
        'category' => 'PRIBADI',
        'topic' => 'Curhat Masalah Pribadi dengan Koordinator BK',
        'message' => 'Halo Bu Ida, saya ingin bercerita secara langsung.',
        'assigned_counselor_id' => $counselorUser->id,
        'is_anonymous' => 0,
    ]);

    $consultation = BkConsultation::where('student_id', $student->id)->latest('id')->first();
    expect($consultation)->not->toBeNull();
    expect($consultation->assigned_counselor_id)->toBe($counselorUser->id);
    expect($consultation->assignedCounselor->name)->toBe('Ida Marttin Maolidah SE, SP.,M.Pd.');

    // Siswa mengirim voice note (audio file .webm) via AJAX
    $voiceNote = UploadedFile::fake()->create('voicenote_123456789.webm', 250, 'audio/webm');

    $voiceResponse = $this->actingAs($studentUser)->postJson(route('bk.curhat.message', $consultation), [
        'message' => '🎙️ Pesan Suara',
        'attachment' => $voiceNote,
    ]);

    $voiceResponse->assertStatus(200);
    $voiceResponse->assertJson(['success' => true]);
    $voiceData = $voiceResponse->json('data');
    expect($voiceData['is_audio'])->toBeTrue();

    // Verifikasi pesan tersimpan sebagai audio
    $audioMessage = $consultation->messages()->latest('id')->first();
    expect($audioMessage)->not->toBeNull();
    expect($audioMessage->is_audio)->toBeTrue();
    expect($audioMessage->is_document)->toBeFalse();
    expect($audioMessage->attachment_type)->toBe('AUDIO');
    Storage::disk('public')->assertExists($audioMessage->attachment_path);

    // Halaman index juga menampilkan konselor terpilih
    $pageResponse = $this->actingAs($studentUser)->get(route('bk.curhat.index', ['c' => $consultation->id]));
    $pageResponse->assertStatus(200);
    $pageResponse->assertSee('Ida Marttin Maolidah SE, SP.,M.Pd.');
});
