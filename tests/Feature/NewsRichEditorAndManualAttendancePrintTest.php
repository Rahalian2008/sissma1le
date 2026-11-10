<?php

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SchoolHoliday;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator', 'level' => 100]);
    Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator', 'level' => 90]);
    Role::firstOrCreate(['name' => 'tu'], ['display_name' => 'Tata Usaha', 'level' => 50]);
    Role::firstOrCreate(['name' => 'wakasek_kesiswaan'], ['display_name' => 'Wakasek Kesiswaan', 'level' => 80]);
});

test('kelola konten web has rich editor toolbar and inline image upload endpoint', function () {
    Storage::fake('public');

    $admin = User::factory()->create([
        'role' => 'admin',
        'roles' => ['admin'],
    ]);

    // 1. Visit Kelola Konten Web news tab
    $response = $this->actingAs($admin)->get(route('master.website_content', ['tab' => 'news']));
    $response->assertStatus(200);
    $response->assertSee('news_editor_visual', false);
    $response->assertSee('Sisipkan Gambar', false);
    $response->assertSee('Sisipkan Tabel', false);
    $response->assertSee('modal-insert-news-image', false);
    $response->assertSee('modal-insert-news-table', false);

    // 2. Test inline image upload endpoint
    $fakeImage = UploadedFile::fake()->image('kegiatan_sekolah.jpg', 800, 600);
    $uploadResponse = $this->actingAs($admin)->postJson(route('master.website_content.news.upload_inline_image'), [
        'image' => $fakeImage,
    ]);

    $uploadResponse->assertStatus(200);
    $uploadResponse->assertJsonStructure([
        'success',
        'url',
        'filename',
    ]);
    expect($uploadResponse->json('success'))->toBeTrue();

    // 3. Store news with rich HTML containing table and image
    $richContent = '<h2>Gelar Karya SMAN 1 Lengkong</h2><p>Berikut adalah tabel ringkasan kegiatan:</p><div class="overflow-x-auto"><table class="w-full"><tr><th>Hari</th><th>Kegiatan</th></tr><tr><td>Senin</td><td>Pentas Seni</td></tr></table></div><p><img src="'.$uploadResponse->json('url').'" alt="Dokumentasi"></p>';

    $storeResponse = $this->actingAs($admin)->post(route('master.website_content.news.store'), [
        'news_title' => 'Gelar Karya Budaya Nusantara 2026',
        'news_category' => 'Kegiatan',
        'news_date' => '2026-11-08',
        'news_author' => 'Humas SMAN 1 Lengkong',
        'news_excerpt' => 'Liputan pameran karya siswa SMAN 1 Lengkong 2026.',
        'news_content' => $richContent,
    ]);

    $storeResponse->assertRedirect(route('master.website_content', ['tab' => 'news']));
    $storeResponse->assertSessionHas('success');
});

test('adm kesiswaan has cetak absen manual form and SIKATAS print view works for monthly, weekly, and custom modes', function () {
    $tuUser = User::factory()->create([
        'role' => 'tu',
        'roles' => ['tu'],
    ]);

    // Create Homeroom Teacher & Class
    $teacherUser = User::factory()->create(['name' => 'Budi Santoso, S.Pd.', 'nip' => '198501012010011005']);
    $teacher = Teacher::create([
        'user_id' => $teacherUser->id,
        'name' => 'Budi Santoso, S.Pd.',
        'nip' => '198501012010011005',
        'title' => 'Guru Matematika',
        'is_active' => true,
    ]);

    $schoolClass = SchoolClass::create([
        'name' => 'X Merdeka 1',
        'grade' => '10',
        'academic_year' => '2026/2027',
        'homeroom_teacher_id' => $teacher->id,
    ]);

    // Create Students
    $student1 = Student::create([
        'class_id' => $schoolClass->id,
        'name' => 'Ahmad Dahlan',
        'nisn' => '0091234567',
        'nis' => '26271001',
        'gender' => 'L',
        'is_active' => true,
    ]);

    $student2 = Student::create([
        'class_id' => $schoolClass->id,
        'name' => 'Siti Aisyah',
        'nisn' => '0091234568',
        'nis' => '26271002',
        'gender' => 'P',
        'is_active' => true,
    ]);

    // Create holiday in November 2026
    SchoolHoliday::create([
        'name' => 'Hari Pahlawan Nasional',
        'start_date' => '2026-11-10',
        'end_date' => '2026-11-10',
        'type' => 'nasional',
        'is_active' => true,
    ]);

    // 1. Visit Adm Kesiswaan Index -> should see Cetak Absen Manual button & card
    $indexResponse = $this->actingAs($tuUser)->get(route('tu.kesiswaan.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee(route('tu.kesiswaan.absen_manual.index'), false);
    $indexResponse->assertSee('Cetak Absen Manual', false);

    // 2. Visit Cetak Absen Manual Form
    $formResponse = $this->actingAs($tuUser)->get(route('tu.kesiswaan.absen_manual.index'));
    $formResponse->assertStatus(200);
    $formResponse->assertSee('Cetak Lembar Presensi Manual Siswa', false);
    $formResponse->assertSee('X Merdeka 1', false);
    $formResponse->assertSee('Model SIKATAS', false);

    // 3. Print Mode Monthly (November 2026) - F4 Paper
    $monthlyPrint = $this->actingAs($tuUser)->get(route('tu.kesiswaan.absen_manual.print', [
        'class_id' => $schoolClass->id,
        'period_type' => 'monthly',
        'month' => 11,
        'year' => 2026,
        'paper' => 'f4',
    ]));

    $monthlyPrint->assertStatus(200);
    $monthlyPrint->assertSee('DAFTAR HADIR MANUAL SISWA', false);
    $monthlyPrint->assertSee('X Merdeka 1', false);
    $monthlyPrint->assertSee('Budi Santoso, S.Pd.', false);
    $monthlyPrint->assertSee('Ahmad Dahlan', false);
    $monthlyPrint->assertSee('Siti Aisyah', false);
    $monthlyPrint->assertSee('Bulan November 2026', false);
    $monthlyPrint->assertSee('SIKATAS PRESENSI', false);

    // 4. Print Mode Weekly - A4 Paper
    $weeklyPrint = $this->actingAs($tuUser)->get(route('tu.kesiswaan.absen_manual.print', [
        'class_id' => $schoolClass->id,
        'period_type' => 'weekly',
        'start_date' => '2026-11-09',
        'paper' => 'a4',
    ]));

    $weeklyPrint->assertStatus(200);
    $weeklyPrint->assertSee('Pekan:', false);
    $weeklyPrint->assertSee('X Merdeka 1', false);

    // 5. Print Mode Custom Range
    $customPrint = $this->actingAs($tuUser)->get(route('tu.kesiswaan.absen_manual.print', [
        'class_id' => $schoolClass->id,
        'period_type' => 'custom',
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-15',
        'paper' => 'f4',
    ]));

    $customPrint->assertStatus(200);
    $customPrint->assertSee('01 Nov 2026 s.d. 15 Nov 2026', false);
});
