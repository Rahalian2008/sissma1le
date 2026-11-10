<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KlaperController extends Controller
{
    public function index(Request $request): View
    {
        $alphabet = $request->query('letter', 'all');

        $query = Student::query();

        if ($alphabet !== 'all' && strlen($alphabet) === 1) {
            $query->where('name', 'like', $alphabet.'%');
        }

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

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Klaper is sorted alphabetically by student name (A-Z)
        $students = $query->orderBy('name', 'asc')->paginate(25)->withQueryString();

        return view('klaper.index', compact('students', 'alphabet'));
    }

    public function print(Request $request): View
    {
        $school = SchoolIdentity::current();
        $classLevel = $request->query('class_level');

        $query = Student::query();
        if ($classLevel) {
            $query->where('current_class_level', $classLevel);
        }

        $students = $query->orderBy('name', 'asc')->get();

        AuditTrailService::log('print', 'klaper', 'Mencetak Buku Klaper Siswa');

        return view('klaper.print', compact('students', 'school', 'classLevel'));
    }

    public function exportCsv(): StreamedResponse
    {
        $students = Student::orderBy('name', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="buku-klaper-siswa-sma1le-'.date('Y-m-d').'.csv"',
        ];

        return response()->stream(function () use ($students) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'No. Urut', 'NIS', 'NISN', 'Nama Peserta Didik', 'L/P',
                'Tempat Lahir', 'Tanggal Lahir', 'Asal Sekolah', 'Tanggal Masuk',
                'Kelas', 'Status Siswa', 'Tgl Keluar/Lulus', 'Keterangan',
            ]);

            foreach ($students as $idx => $s) {
                fputcsv($output, [
                    $idx + 1,
                    $s->nis,
                    $s->nisn ?? '-',
                    $s->name,
                    $s->gender,
                    $s->birth_place ?? '-',
                    $s->birth_date ? $s->birth_date->format('d/m/Y') : '-',
                    $s->school_origin ?? '-',
                    $s->admission_date ? $s->admission_date->format('d/m/Y') : '-',
                    $s->current_class_level.' '.($s->current_rombel ?? ''),
                    $s->status,
                    $s->graduation_year ?? '-',
                    $s->notes ?? '-',
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }
}
