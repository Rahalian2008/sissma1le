<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Services\AuditTrailService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BukuIndukController extends Controller
{
    public function index(Request $request): View
    {
        $query = Student::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($classLevel = $request->query('class_level')) {
            $query->where('current_class_level', $classLevel);
        }

        $students = $query->orderBy('nis')->paginate(15)->withQueryString();

        return view('buku_induk.index', compact('students'));
    }

    public function show(Student $student): View
    {
        $student->load(['family', 'mutations', 'achievements', 'violations', 'scholarships', 'alumnus']);
        $school = SchoolIdentity::current();

        $verificationUrl = route('verify.public', ['type' => 'buku-induk', 'token' => $student->qr_code_token ?? $student->nis]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 140);

        AuditTrailService::log('view', 'buku_induk', "Melihat lembar Buku Induk Digital: {$student->name} (NIS: {$student->nis})");

        return view('buku_induk.show', compact('student', 'school', 'qrCodeSvg', 'verificationUrl'));
    }

    public function print(Student $student): View
    {
        $student->load(['family', 'mutations', 'achievements', 'violations', 'scholarships', 'alumnus']);
        $school = SchoolIdentity::current();

        $verificationUrl = route('verify.public', ['type' => 'buku-induk', 'token' => $student->qr_code_token ?? $student->nis]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 120);

        AuditTrailService::log('print', 'buku_induk', "Mencetak Lembar Buku Induk Siswa: {$student->name}");

        return view('buku_induk.print', compact('student', 'school', 'qrCodeSvg'));
    }
}
