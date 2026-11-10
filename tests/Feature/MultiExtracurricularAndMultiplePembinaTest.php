<?php

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->admin = User::firstOrCreate(
        ['email' => 'admin.multi.test@sma1le.sch.id'],
        [
            'name' => 'Admin Multi Ekstra',
            'username' => 'admin_multi_test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
            'is_active' => true,
        ]
    );

    $this->academicYear = AcademicYear::firstOrCreate(
        ['name' => '2026/2027'],
        [
            'semester' => 'Ganjil',
            'is_active' => true,
        ]
    );

    $this->schoolClass = SchoolClass::firstOrCreate(
        ['name' => 'XI-F1-TEST'],
        [
            'grade' => 'XI',
            'major' => 'Fase F',
            'is_active' => true,
        ]
    );
});

test('siswa bisa menjadi anggota ekstrakurikuler lebih dari 1 melalui pembuatan atau pembaruan siswa', function () {
    $extra1 = Extracurricular::firstOrCreate(
        ['code' => 'PRAM-MULTI'],
        [
            'name' => 'Pramuka Multi Test',
            'category' => 'Kepanduan & Bela Negara',
            'is_active' => true,
        ]
    );

    $extra2 = Extracurricular::firstOrCreate(
        ['code' => 'PMR-MULTI'],
        [
            'name' => 'PMR Wira Multi Test',
            'category' => 'Kemanusiaan',
            'is_active' => true,
        ]
    );

    $extra3 = Extracurricular::firstOrCreate(
        ['code' => 'FUT-MULTI'],
        [
            'name' => 'Futsal Club Multi Test',
            'category' => 'Olahraga & Bela Diri',
            'is_active' => true,
        ]
    );

    // 1. Simpan siswa baru dengan memilih multiple extracurricular_ids
    $response = $this->actingAs($this->admin)->post(route('master.students.store'), [
        'nis' => '99001',
        'nisn' => '0099887701',
        'name' => 'Budi Multi Ekskul',
        'gender' => 'L',
        'class_id' => $this->schoolClass->id,
        'academic_year_id' => $this->academicYear->id,
        'extracurricular_ids' => [$extra1->id, $extra2->id],
    ]);

    $response->assertRedirect();
    $student = Student::where('nis', '99001')->firstOrFail();

    // Siswa terdaftar pada 2 ekskul sekaligus
    expect($student->extracurriculars)->toHaveCount(2);
    expect($student->extracurriculars->pluck('id')->all())->toContain($extra1->id, $extra2->id);

    // 2. Update siswa untuk menambah ekskul ke-3
    $this->actingAs($this->admin)->put(route('master.students.update', $student->id), [
        'nis' => '99001',
        'nisn' => '0099887701',
        'name' => 'Budi Multi Ekskul Update',
        'gender' => 'L',
        'class_id' => $this->schoolClass->id,
        'academic_year_id' => $this->academicYear->id,
        'is_active' => 1,
        'extracurricular_ids' => [$extra1->id, $extra2->id, $extra3->id],
    ]);

    $student->refresh();
    expect($student->extracurriculars)->toHaveCount(3);
    expect($student->extracurriculars->pluck('id')->all())->toContain($extra1->id, $extra2->id, $extra3->id);

    // 3. Verifikasi halaman data siswa merender badge untuk seluruh ekstrakurikuler yang diikuti
    $viewResponse = $this->actingAs($this->admin)->get(route('master.students'));
    $viewResponse->assertOk();
    $viewResponse->assertSee('Pramuka Multi Test');
    $viewResponse->assertSee('PMR Wira Multi Test');
    $viewResponse->assertSee('Futsal Club Multi Test');
});

test('bisa memilih lebih dari 1 guru pembina pada data ekstrakurikuler dan semua guru mendapat role pembina_ekstra', function () {
    $teacher1User = User::firstOrCreate(
        ['email' => 'guru.test1@sma1le.sch.id'],
        [
            'name' => 'Drs. Suparman, M.Pd.',
            'username' => 'GURU_TEST_01',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'roles' => ['guru'],
            'is_active' => true,
        ]
    );
    $teacher1 = Teacher::firstOrCreate(
        ['nip' => '197001012000031001'],
        [
            'user_id' => $teacher1User->id,
            'name' => 'Drs. Suparman, M.Pd.',
            'gender' => 'L',
            'title' => 'Guru Biologi',
            'is_active' => true,
        ]
    );

    $teacher2User = User::firstOrCreate(
        ['email' => 'guru.test2@sma1le.sch.id'],
        [
            'name' => 'Siti Nurhaliza, S.Pd.',
            'username' => 'GURU_TEST_02',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'roles' => ['guru'],
            'is_active' => true,
        ]
    );
    $teacher2 = Teacher::firstOrCreate(
        ['nip' => '198002022005012002'],
        [
            'user_id' => $teacher2User->id,
            'name' => 'Siti Nurhaliza, S.Pd.',
            'gender' => 'P',
            'title' => 'Guru Fisika',
            'is_active' => true,
        ]
    );

    // 1. Tambah Ekstrakurikuler dengan 2 Pembina
    Extracurricular::where('code', 'ROBOTIK01')->delete();
    $response = $this->actingAs($this->admin)->post(route('master.extracurriculars.store'), [
        'code' => 'ROBOTIK01',
        'name' => 'Klub Robotik & Otomasi',
        'category' => 'Teknologi & Multimedia',
        'teacher_ids' => [$teacher1->id, $teacher2->id],
        'location' => 'Lab Komputer 1',
        'is_active' => 1,
    ]);

    $response->assertRedirect();
    $extra = Extracurricular::where('code', 'ROBOTIK01')->firstOrFail();

    // Verifikasi pivot table
    expect($extra->teachers)->toHaveCount(2);
    expect($extra->teachers->pluck('id')->all())->toContain($teacher1->id, $teacher2->id);

    // Verifikasi teacher_names attribute
    expect($extra->teacher_names)->toContain('Drs. Suparman, M.Pd.');
    expect($extra->teacher_names)->toContain('Siti Nurhaliza, S.Pd.');

    // Verifikasi kedua user guru otomatis diberikan role pembina_ekstra
    $teacher1User->refresh();
    $teacher2User->refresh();
    expect($teacher1User->hasRole('pembina_ekstra'))->toBeTrue();
    expect($teacher2User->hasRole('pembina_ekstra'))->toBeTrue();

    // 2. Update Ekstrakurikuler dengan pembina baru
    $teacher3User = User::firstOrCreate(
        ['email' => 'guru.test3@sma1le.sch.id'],
        [
            'name' => 'Agus Priyono, S.Kom.',
            'username' => 'GURU_TEST_03',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'roles' => ['guru'],
            'is_active' => true,
        ]
    );
    $teacher3 = Teacher::firstOrCreate(
        ['nip' => '198503032010011003'],
        [
            'user_id' => $teacher3User->id,
            'name' => 'Agus Priyono, S.Kom.',
            'gender' => 'L',
            'title' => 'Guru Informatika',
            'is_active' => true,
        ]
    );

    $this->actingAs($this->admin)->put(route('master.extracurriculars.update', $extra->id), [
        'code' => 'ROBOTIK01',
        'name' => 'Klub Robotik & Otomasi Unggulan',
        'category' => 'Teknologi & Multimedia',
        'teacher_ids' => [$teacher1->id, $teacher3->id],
        'location' => 'Lab STEM',
        'is_active' => 1,
    ]);

    $extra->refresh();
    expect($extra->teachers)->toHaveCount(2);
    expect($extra->teachers->pluck('id')->all())->toContain($teacher1->id, $teacher3->id);

    $teacher3User->refresh();
    expect($teacher3User->hasRole('pembina_ekstra'))->toBeTrue();

    // Verifikasi view master ekstrakurikuler merender semua pembina
    $viewResponse = $this->actingAs($this->admin)->get(route('master.extracurriculars'));
    $viewResponse->assertOk();
    $viewResponse->assertSee('Drs. Suparman, M.Pd.');
    $viewResponse->assertSee('Agus Priyono, S.Kom.');
    $viewResponse->assertSee('(2 Guru Pembina)');
});

test('ekstrakurikuler tidak memerlukan hari latihan dan jam mulai/selesai serta validasi time lock mengacu ke pengaturan master', function () {
    // 1. Simpan ekstra tanpa schedule_day, start_time, end_time
    Extracurricular::where('code', 'PASKIBRA01')->delete();
    $response = $this->actingAs($this->admin)->post(route('master.extracurriculars.store'), [
        'code' => 'PASKIBRA01',
        'name' => 'Paskibra Satria',
        'category' => 'Kepanduan & Bela Negara',
        'location' => 'Lapangan Utama',
        'is_active' => 1,
    ]);

    $response->assertRedirect();
    $extra = Extracurricular::where('code', 'PASKIBRA01')->firstOrFail();
    expect($extra->schedule_day)->toBeNull();
    expect($extra->start_time)->toBeNull();
    expect($extra->end_time)->toBeNull();

    // 2. Set global SchoolSetting
    SchoolSetting::set('extra_attendance_days', 'senin,rabu,jumat');
    SchoolSetting::set('extra_attendance_start', '15:00');
    SchoolSetting::set('extra_attendance_end', '17:30');

    // Hari yang tidak terdaftar (misal Minggu)
    $sunday = Carbon::parse('2026-10-18 16:00:00', 'Asia/Jakarta'); // Sunday
    $sundayCheck = $extra->validateTimeLock($sunday);
    expect($sundayCheck['allowed'])->toBeFalse();
    expect($sundayCheck['status'])->toBe('WRONG_DAY');

    // Hari yang terdaftar (Rabu) jam 15:30 (masuk jam operasional 15:00 - 17:30)
    $wednesdayValid = Carbon::parse('2026-10-14 15:30:00', 'Asia/Jakarta'); // Wednesday
    $wednesdayCheck = $extra->validateTimeLock($wednesdayValid);
    expect($wednesdayCheck['allowed'])->toBeTrue();
    expect($wednesdayCheck['status'])->toBe('OPEN');

    // Hari yang terdaftar tapi di luar jam operasional (Rabu jam 10:00 pagi)
    $wednesdayOutside = Carbon::parse('2026-10-14 10:00:00', 'Asia/Jakarta');
    $outsideCheck = $extra->validateTimeLock($wednesdayOutside);
    expect($outsideCheck['allowed'])->toBeFalse();
    expect($outsideCheck['status'])->toBe('WRONG_TIME');

    // Di luar jam operasional presensi ditolak
    $outsideCheck2 = $extra->validateTimeLock($wednesdayOutside);
    expect($outsideCheck2['allowed'])->toBeFalse();
    expect($outsideCheck2['status'])->toBe('WRONG_TIME');
});

test('pengaturan jam operasional presensi ekstrakurikuler di master settings mendukung jadwal khusus per jenis kegiatan dan jadwal default umum', function () {
    // 1. Siapkan 2 kegiatan ekstrakurikuler
    $extraPramuka = Extracurricular::create([
        'code' => 'PRAMUKA_TEST',
        'name' => 'Pramuka Ambalan Test',
        'category' => 'Kepemimpinan',
        'description' => 'Latihan Pramuka',
        'location' => 'Sanggar Pramuka',
        'is_active' => 1,
    ]);

    $extraBasket = Extracurricular::create([
        'code' => 'BASKET_TEST',
        'name' => 'Bola Basket Test',
        'category' => 'Olahraga',
        'description' => 'Latihan Basket',
        'location' => 'Lapangan Basket',
        'is_active' => 1,
    ]);

    // 2. Akses halaman Pengaturan Sistem & Geofence
    $settingsView = $this->actingAs($this->admin)->get(route('master.settings'));
    $settingsView->assertOk();
    $settingsView->assertSee('Presensi Ekstrakurikuler');
    $settingsView->assertSee('Pramuka Ambalan Test');
    $settingsView->assertSee('Bola Basket Test');
    $settingsView->assertSee('Jadwal Standar / Umum Ekstrakurikuler');
    $settingsView->assertSee('Jadwal Khusus Per Jenis Ekstrakurikuler');
    $settingsView->assertSee('Catatan Penyesuaian Jadwal & Ekstrakurikuler Baru:', false);

    // 3. Simpan pengaturan:
    // - Default umum: Senin s.d. Kamis, 14:00 - 17:00
    // - Pramuka: Jadwal khusus Jum'at saja, 13:30 - 16:30
    // - Basket: Ikuti jadwal umum default (use_custom false)
    $postData = [
        'extra_attendance_days' => ['senin', 'selasa', 'rabu', 'kamis'],
        'extra_attendance_start' => '14:00',
        'extra_attendance_end' => '17:00',
        'extra_schedules' => [
            $extraPramuka->id => [
                'use_custom' => '1',
                'days' => ['jumat'],
                'start_time' => '13:30',
                'end_time' => '16:30',
            ],
            $extraBasket->id => [
                'use_custom' => '0',
            ],
        ],
    ];

    $response = $this->actingAs($this->admin)->post(route('master.settings.update'), $postData);
    $response->assertRedirect();

    $extraPramuka->refresh();
    $extraBasket->refresh();

    // Verifikasi Pramuka memiliki jadwal khusus tersimpan
    expect($extraPramuka->hasCustomSchedule())->toBeTrue();
    expect($extraPramuka->schedule_day)->toBe('Jumat');
    expect($extraPramuka->start_time)->toBe('13:30');
    expect($extraPramuka->end_time)->toBe('16:30');
    expect($extraPramuka->effective_days)->toBe(['jumat']);

    // Verifikasi Basket tidak memiliki jadwal khusus (null) dan mengikuti default
    expect($extraBasket->hasCustomSchedule())->toBeFalse();
    expect($extraBasket->schedule_day)->toBeNull();
    expect($extraBasket->effective_days)->toBe(['senin', 'selasa', 'rabu', 'kamis']);
    expect($extraBasket->effective_start_time)->toBe('14:00');
    expect($extraBasket->effective_end_time)->toBe('17:00');

    // 4. Uji Time-Lock Pramuka (Jadwal Khusus: Jumat 13:30 - 16:30)
    // Pada hari Jumat jam 14:00: Pramuka OPEN, tapi Basket WRONG_DAY (karena default Basket Senin-Kamis)
    $fridayTime = Carbon::parse('2026-10-16 14:00:00', 'Asia/Jakarta'); // Friday
    $pramukaCheckFriday = $extraPramuka->validateTimeLock($fridayTime);
    expect($pramukaCheckFriday['allowed'])->toBeTrue();
    expect($pramukaCheckFriday['status'])->toBe('OPEN');

    $basketCheckFriday = $extraBasket->validateTimeLock($fridayTime);
    expect($basketCheckFriday['allowed'])->toBeFalse();
    expect($basketCheckFriday['status'])->toBe('WRONG_DAY');

    // Pada hari Rabu jam 14:30: Basket OPEN (mengikuti default umum), tapi Pramuka WRONG_DAY (karena khusus Jumat)
    $wednesdayTime = Carbon::parse('2026-10-14 14:30:00', 'Asia/Jakarta'); // Wednesday
    $basketCheckWed = $extraBasket->validateTimeLock($wednesdayTime);
    expect($basketCheckWed['allowed'])->toBeTrue();
    expect($basketCheckWed['status'])->toBe('OPEN');

    $pramukaCheckWed = $extraPramuka->validateTimeLock($wednesdayTime);
    expect($pramukaCheckWed['allowed'])->toBeFalse();
    expect($pramukaCheckWed['status'])->toBe('WRONG_DAY');

    // 5. Kembalikan Pramuka ke Jadwal Umum Default (uncheck use_custom)
    $resetData = [
        'extra_schedules' => [
            $extraPramuka->id => [
                'use_custom' => '0',
            ],
        ],
    ];
    $this->actingAs($this->admin)->post(route('master.settings.update'), $resetData);
    $extraPramuka->refresh();

    expect($extraPramuka->hasCustomSchedule())->toBeFalse();
    expect($extraPramuka->schedule_day)->toBeNull();
    expect($extraPramuka->effective_days)->toBe(['senin', 'selasa', 'rabu', 'kamis']);
});
