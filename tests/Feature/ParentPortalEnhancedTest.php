<?php

use App\Models\Student;

test('parent portal public page renders successfully with all tabs without authentication', function () {
    $response = $this->get(route('parent.portal'));
    $response->assertStatus(200);
    $response->assertSee('Portal Orang Tua');
    $response->assertSee('Tanpa Login');

    $student = Student::where('is_active', true)->whereNotNull('nisn')->first();
    if ($student) {
        $responseWithNisn = $this->get(route('parent.portal', ['nisn' => $student->nisn]));
        $responseWithNisn->assertStatus(200);
        $responseWithNisn->assertSee($student->name);
        $responseWithNisn->assertSee('Riwayat Presensi');
        $responseWithNisn->assertSee('7 Kebiasaan Anak Hebat');
        $responseWithNisn->assertSee('Ibadah');
        $responseWithNisn->assertSee('Tahfidz');
        $responseWithNisn->assertSee('Rapor Kurnas');
        $responseWithNisn->assertSee('Bimbingan');
        $responseWithNisn->assertSee('Psikotes');
        $responseWithNisn->assertSee('Pustaka');
        $responseWithNisn->assertSee('Literasi');
        $responseWithNisn->assertSee('Jadwal');
        $responseWithNisn->assertSee('Agenda KBM');
    }
});
