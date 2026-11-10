<?php

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

test('audit: all user role dashboards render successfully without error', function (string $role) {
    $user = User::where('role', $role)->first();
    expect($user)->not->toBeNull();

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertStatus(200);
})->with([
    'super_admin',
    'admin',
    'kepala_sekolah',
    'guru',
    'wali_kelas',
    'siswa',
    'orang_tua',
]);

test('audit: homeroom dashboard displays full attendance statistics cards', function () {
    $wali = User::where('role', 'wali_kelas')->first();
    expect($wali)->not->toBeNull();

    $response = $this->actingAs($wali)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Jumlah Siswa');
    $response->assertSee('Hadir');
    $response->assertSee('Terlambat');
    $response->assertSee('Izin');
    $response->assertSee('Sakit');
    $response->assertSee('Alpa');
});

test('audit: presensi terpadu menus render successfully for authorized roles', function (string $routeName, string $role) {
    $user = User::where('role', $role)->first();
    expect($user)->not->toBeNull();

    $response = $this->actingAs($user)->get(route($routeName));
    $response->assertStatus(200);
})->with([
    ['attendance.index', 'admin'],
    ['attendance.index', 'wali_kelas'],
    ['attendance.index', 'guru'],
    ['attendance.index', 'siswa'],
    ['attendance.selfie', 'siswa'],
    ['attendance.qr.scanner', 'siswa'],
    ['attendance.qr.scanner', 'guru'],
    ['attendance.cards.index', 'admin'],
    ['attendance.cards.index', 'wali_kelas'],
    ['attendance.leave', 'siswa'],
    ['attendance.leaves.index', 'admin'],
    ['attendance.leaves.index', 'wali_kelas'],
    ['attendance.qr.session', 'guru'],
    ['attendance.manual', 'wali_kelas'],
    ['attendance.manual', 'admin'],
    ['attendance.rfid.simulator', 'admin'],
    ['attendance.history', 'siswa'],
    ['attendance.history', 'guru'],
    ['attendance.history', 'wali_kelas'],
    ['attendance.history', 'admin'],
    ['attendance.report', 'wali_kelas'],
    ['attendance.report', 'admin'],
    ['attendance.report', 'kepala_sekolah'],
]);

test('audit: tata tertib siswa menus render successfully for authorized roles', function (string $routeName, string $role) {
    $user = User::where('role', $role)->first();
    expect($user)->not->toBeNull();

    $response = $this->actingAs($user)->get(route($routeName));
    $response->assertStatus(200);
})->with([
    ['achievements.index', 'admin'],
    ['achievements.index', 'guru'],
    ['achievements.index', 'wali_kelas'],
    ['achievements.index', 'siswa'],
    ['achievements.create', 'admin'],
    ['achievements.create', 'guru'],
    ['achievements.create', 'wali_kelas'],
    ['violations.index', 'admin'],
    ['violations.index', 'guru'],
    ['violations.index', 'wali_kelas'],
    ['violations.index', 'siswa'],
    ['violations.create', 'admin'],
    ['violations.create', 'guru'],
    ['violations.create', 'wali_kelas'],
    ['guidance.index', 'admin'],
    ['guidance.index', 'wali_kelas'],
    ['guidance.index', 'guru'],
    ['guidance.index', 'kepala_sekolah'],
    ['discipline.ranking', 'admin'],
    ['discipline.ranking', 'wali_kelas'],
    ['discipline.ranking', 'guru'],
    ['discipline.ranking', 'kepala_sekolah'],
    ['discipline.report', 'admin'],
    ['discipline.report', 'wali_kelas'],
    ['discipline.report', 'kepala_sekolah'],
]);

test('audit: karakter 7 kebiasaan menus render successfully for authorized roles', function (string $routeName, string $role) {
    $user = User::where('role', $role)->first();
    expect($user)->not->toBeNull();

    $response = $this->actingAs($user)->get(route($routeName));
    $response->assertStatus(200);
})->with([
    ['habits.journal', 'siswa'],
    ['habits.summary', 'siswa'],
    ['habits.summary', 'wali_kelas'],
    ['habits.summary', 'admin'],
    ['habits.charts', 'wali_kelas'],
    ['habits.charts', 'admin'],
    ['habits.charts', 'kepala_sekolah'],
    ['habits.report', 'wali_kelas'],
    ['habits.report', 'admin'],
    ['habits.report', 'kepala_sekolah'],
]);

test('audit: raport digital and ai analysis menus render successfully', function (string $role) {
    $user = User::where('role', $role)->first();
    expect($user)->not->toBeNull();

    $response = $this->actingAs($user)->get(route('raport.index'));
    $response->assertStatus(200);
})->with([
    'super_admin',
    'admin',
    'kepala_sekolah',
    'guru',
    'wali_kelas',
    'siswa',
]);

test('audit: reports and printable views render successfully without errors', function () {
    $admin = User::where('role', 'admin')->first();
    $class = SchoolClass::first();
    $student = Student::first();

    expect($admin)->not->toBeNull();
    expect($class)->not->toBeNull();
    expect($student)->not->toBeNull();

    $this->actingAs($admin)->get(route('reports.index'))->assertStatus(200);
    $this->actingAs($admin)->get(route('ai.analysis'))->assertStatus(200);

    // Test print routes
    $startDate = Carbon::now()->startOfMonth()->toDateString();
    $endDate = Carbon::now()->toDateString();

    $this->actingAs($admin)->get(route('reports.print.attendance', [
        'class_id' => $class->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]))->assertStatus(200);

    $this->actingAs($admin)->get(route('reports.print.discipline', [
        'class_id' => $class->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]))->assertStatus(200);

    $this->actingAs($admin)->get(route('reports.print.habits', [
        'class_id' => $class->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]))->assertStatus(200);

    $this->actingAs($admin)->get(route('reports.print.class_recap', [
        'class_id' => $class->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]))->assertStatus(200);

    $this->actingAs($admin)->get(route('reports.student.dossier', $student->id))->assertStatus(200);
    $this->actingAs($admin)->get(route('reports.student.print', $student->id))->assertStatus(200);
    $this->actingAs($admin)->get(route('raport.print', $student->id))->assertStatus(200);
});

test('audit: all master data management pages render successfully for admin', function (string $routeName) {
    $admin = User::where('role', 'admin')->first();
    expect($admin)->not->toBeNull();

    $response = $this->actingAs($admin)->get(route($routeName));
    $response->assertStatus(200);
})->with([
    'master.school_profile',
    'master.branding',
    'master.classes',
    'master.students',
    'master.teachers',
    'master.academic_years',
    'master.holidays',
    'master.settings',
    'master.categories',
]);

test('audit: public parent portal and common user pages render successfully', function () {
    // Public portal (guest)
    $this->get(route('parent.portal'))->assertStatus(200);

    // Profile & Notifications for authenticated users
    $wali = User::where('role', 'wali_kelas')->first();
    $this->actingAs($wali)->get(route('profile'))->assertStatus(200);
    $this->actingAs($wali)->get(route('notifications.index'))->assertStatus(200);
});

test('audit: library menus render successfully without error', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $libRoutes = [
        'library.index',
        'library.catalog',
        'library.loans',
        'library.members',
        'library.visits',
        'library.reports',
    ];

    foreach ($libRoutes as $routeName) {
        if (Route::has($routeName)) {
            $this->actingAs($admin)->get(route($routeName))->assertStatus(200);
        }
    }
});

test('audit: all bimbingan konseling and psikotes menus render successfully without error', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $bkRoutes = [
        'bk.dashboard',
        'bk.akpd.index',
        'bk.rpl.index',
        'bk.counseling.index',
        'bk.case-conference.index',
        'bk.referral.index',
        'bk.home-visit.index',
        'bk.parent-call.index',
        'bk.sociometry.index',
        'bk.psychological.index',
        'bk.psychological.class-report',
    ];

    foreach ($bkRoutes as $routeName) {
        if (Route::has($routeName)) {
            $this->actingAs($admin)->get(route($routeName))->assertStatus(200);
        }
    }
});

test('audit: all tu submenus and primary pages render successfully without error', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $tuRoutes = [
        'tu.dashboard',
        'tu.kesiswaan.index',
        'tu.kepegawaian.index',
        'tu.persuratan.index',
        'tu.sarpras.index',
        'tu.keuangan.index',
        'buku-induk.index',
        'klaper.index',
        'mutations.index',
        'students.cohort.index',
        'students.rombel.index',
        'students.scholarships.index',
        'students.diplomas.index',
        'alumni.index',
        'ppdb.index',
        'spmb.index',
        'spmb.tka.index',
        'spmb.tracks.index',
        'students.learning-agendas.index',
        'students.demographics',
        'letters.incoming.index',
        'letters.outgoing.index',
        'letters.generator.index',
    ];

    foreach ($tuRoutes as $routeName) {
        if (Route::has($routeName)) {
            $this->actingAs($admin)->get(route($routeName))->assertStatus(200);
        }
    }
});
