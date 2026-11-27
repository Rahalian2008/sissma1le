<?php

use App\Models\BkTracerAlumnus;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    SchoolIdentity::firstOrCreate(
        ['id' => 1],
        [
            'name' => 'SMAN 1 Lengkong',
            'npsn' => '20202265',
            'headmaster_name' => 'Pian Sopian, S.Pd., M.M.Pd',
            'headmaster_nip' => '198009132008011005',
        ]
    );

    $this->bkTeacher = User::where('role', 'guru_bk')->orWhere('role', 'bk')->orWhere('role', 'koordinator_bk')->first();
    if (! $this->bkTeacher) {
        $this->bkTeacher = User::create([
            'name' => 'Rahmat Hidayat, S.Kom.',
            'username' => 'gurubktracer',
            'email' => 'gurubktracer@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru_bk',
            'is_active' => true,
        ]);
    }

    $class = SchoolClass::first() ?? SchoolClass::create(['name' => 'XII-MIPA-1', 'level' => 'XII']);
    $this->student = Student::first();
    if (! $this->student) {
        $this->student = Student::create([
            'name' => 'Alumni Hebat',
            'nisn' => '0059988771',
            'class_id' => $class->id,
            'gender' => 'L',
            'is_active' => true,
        ]);
    }
});

test('public alumni can view the tracer study questionnaire form', function () {
    $response = $this->get(route('bk.tracer.public_form'));

    $response->assertStatus(200);
    $response->assertSee('Kuesioner Tracer Study Alumni', false);
    $response->assertSee('Identitas Diri Alumni', false);
    $response->assertSee('Kuliah / Studi Lanjut', false);
});

test('alumni can submit tracer study form and redirect to success page', function () {
    $payload = [
        'full_name' => 'Alumni Hebat Pengisi',
        'nisn' => '0059988771',
        'gender' => 'L',
        'graduation_year' => '2025',
        'last_class' => 'XII Merdeka 7',
        'phone' => '081234567890',
        'email' => 'alumni@gmail.com',
        'current_city' => 'Sukabumi',
        'activity_status' => 'KULIAH',
        'college_level' => 'S1',
        'campus_name' => 'Universitas Indonesia (UI)',
        'study_program' => 'Ilmu Komputer',
        'admission_track' => 'SNBP (Prestasi / Rapor)',
        'major_alignment' => 'Sangat Selaras (Sesuai Peminatan/Cita-cita)',
        'bk_service_rating' => 5,
        'bk_career_guidance_impact' => 'Sangat membantu pemetaan pilihan studi lanjut.',
        'school_feedback' => 'Terima kasih atas bimbingan para guru.',
    ];

    $response = $this->post(route('bk.tracer.public_store'), $payload);

    $response->assertRedirect(route('bk.tracer.public_success'));

    $this->assertDatabaseHas('bk_tracer_alumni', [
        'full_name' => 'Alumni Hebat Pengisi',
        'nisn' => '0059988771',
        'graduation_year' => '2025',
        'activity_status' => 'KULIAH',
        'campus_name' => 'Universitas Indonesia (UI)',
        'study_program' => 'Ilmu Komputer',
    ]);
});

test('bk counselor can view tracer alumni index dashboard and print report', function () {
    $tracer = BkTracerAlumnus::create([
        'full_name' => 'Alumni Berprestasi',
        'gender' => 'P',
        'graduation_year' => '2024',
        'phone' => '08987654321',
        'activity_status' => 'BEKERJA',
        'company_name' => 'PT. Teknologi Nasional',
        'job_position' => 'Software Engineer',
    ]);

    // View Index Dashboard
    $response = $this->actingAs($this->bkTeacher)->get(route('bk.tracer.index'));
    $response->assertStatus(200);
    $response->assertSee('Alumni Berprestasi', false);
    $response->assertSee('PT. Teknologi Nasional', false);

    // View Detail
    $showResponse = $this->actingAs($this->bkTeacher)->get(route('bk.tracer.show', $tracer));
    $showResponse->assertStatus(200);
    $showResponse->assertSee('Alumni Berprestasi', false);

    // Print Report
    $printResponse = $this->actingAs($this->bkTeacher)->get(route('bk.tracer.print'));
    $printResponse->assertStatus(200);
    $printResponse->assertSee('LAPORAN HASIL PENELUSURAN TAMATAN (TRACER STUDY ALUMNI)', false);
    $printResponse->assertSee('Alumni Berprestasi', false);
    $printResponse->assertSee('Koordinator Bimbingan Konseling (BK)', false);
    $printResponse->assertDontSee('Rahmat Hidayat, S.Kom.', false);
});
