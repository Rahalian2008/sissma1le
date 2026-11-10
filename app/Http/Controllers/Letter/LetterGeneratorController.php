<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LetterTemplate;
use App\Models\ReferenceItem;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Services\AuditTrailService;
use App\Services\DocumentGeneratorService;
use App\Services\LetterNumberService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterGeneratorController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ReferenceItem::where('category', 'jenis_surat')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $selectedCategory = $request->query('category', 'all');

        $query = LetterTemplate::query();
        if ($selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }
        $templates = $query->orderBy('title')->get();

        $students = Student::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('name')->get();

        return view('letters.generator.index', compact('categories', 'selectedCategory', 'templates', 'students', 'employees'));
    }

    public function create(Request $request, $template = null): View
    {
        $selectedTemplate = is_numeric($template) ? LetterTemplate::find($template) : LetterTemplate::where('code', $template)->first();
        $categories = ReferenceItem::where('category', 'jenis_surat')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $selectedCategory = $selectedTemplate?->category ?? 'all';
        $templates = LetterTemplate::orderBy('title')->get();
        $students = Student::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('name')->get();

        return view('letters.generator.index', compact('categories', 'selectedCategory', 'templates', 'students', 'employees', 'selectedTemplate'));
    }

    public function generate(Request $request): View
    {
        $request->validate([
            'template_id' => ['required', 'exists:letter_templates,id'],
            'student_id' => ['nullable', 'exists:students,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'purpose' => ['nullable', 'string'],
            'destination' => ['nullable', 'string'],
            'activity_date' => ['nullable', 'string'],
            'letter_number' => ['nullable', 'string'],
        ]);

        $template = LetterTemplate::find($request->input('template_id'));
        $school = SchoolIdentity::current();

        $defaultCode = match ($template?->code) {
            'SK-TIM-KURIKULUM' => 'PK.03.03.01',
            'ST-IHT-WORKSHOP' => 'PK.03.03.01',
            'SRT-DELEGASI-LOMBA' => 'PK.09.09',
            'SRT-USULAN-PIP' => 'PK.03.03.02',
            'SK-PENGURUS-OSIS' => 'KO.03.03.05',
            'MOU-KEMITRAAN' => 'HM.02.05',
            'SRT-IZIN-BELAJAR' => 'KPG.03.01.01',
            'SK-CUTI-PEGAWAI' => 'KPG.11.02',
            'SRT-USULAN-KGB' => 'KPG.14',
            'NOTULA-RAPAT-DINAS' => 'TU.04',
            'ST-GURU' => 'KPG.03.01.01',
            'SPPD-DINAS' => 'KPG.11.01',
            'SK-PEGAWAI' => 'KPG.02.04.07',
            'SK-KS' => 'KPG.07',
            'SRT-UNDANGAN' => 'TU.01.02',
            default => 'PK.03.03',
        };

        $letterNumber = $request->input('letter_number') ?: LetterNumberService::formatNumber(rand(100, 999), $defaultCode, $school?->name);
        $purpose = $request->input('purpose', 'Keperluan Administrasi Resmi');
        $destination = $request->input('destination', 'Dinas Pendidikan Provinsi');
        $activityDate = $request->input('activity_date', now()->translatedFormat('d F Y'));

        $variables = [
            'keperluan' => $purpose,
            'nomor_surat' => $letterNumber,
            'tempat_tujuan' => $destination,
            'tanggal_kegiatan' => $activityDate,
            'student_id' => $request->input('student_id'),
            'employee_id' => $request->input('employee_id'),
        ];

        if ($studentId = $request->input('student_id')) {
            $student = Student::with('family')->find($studentId);
            if ($student) {
                $variables['nama_siswa'] = $student->name;
                $variables['nis'] = $student->nis;
                $variables['nisn'] = $student->nisn ?? '-';
                $variables['nik_siswa'] = $student->nik ?? '-';
                $variables['no_kk'] = $student->family_card_number ?? '-';
                $variables['kelas'] = $student->current_class_level.($student->current_rombel ? ' '.$student->current_rombel : '');
                $variables['ttl_siswa'] = ($student->birth_place ?? '-').', '.($student->birth_date ? $student->birth_date->format('d/m/Y') : '-');
                $variables['alamat_siswa'] = $student->address ?? '-';
                $variables['nama_orang_tua'] = $student->family?->father_name ?? $student->family?->mother_name ?? $student->family?->guardian_name ?? '-';
                $variables['pekerjaan_orang_tua'] = $student->family?->father_occupation ?? $student->family?->mother_occupation ?? '-';
                $variables['sekolah_asal'] = $student->school_origin ?? '-';

                $waliKelas = Employee::getWaliKelasForRombel($student->current_rombel);
                $variables['nama_wali_kelas'] = $waliKelas?->formatted_name ?? '-';
                $variables['nip_wali_kelas'] = $waliKelas?->nip ?? '-';
                $variables['wali_kelas'] = $waliKelas?->formatted_name ?? '-';
            }
        }

        if ($employeeId = $request->input('employee_id')) {
            $employee = Employee::find($employeeId);
            $variables['nama_pegawai'] = $employee->formatted_name;
            $variables['nip_pegawai'] = $employee->nip ?? '-';
            $variables['pangkat_pegawai'] = $employee->rank_group ?? '-';
            $variables['jabatan_pegawai'] = $employee->position ?? '-';
        }

        $generatedHtml = DocumentGeneratorService::parse($template->body_content, $variables);

        AuditTrailService::log('generate', 'generator_dokumen', "Menghasilkan dokumen dari template [{$template->title}]");

        return view('letters.generator.preview', compact('template', 'school', 'generatedHtml', 'variables'));
    }

    public function classifications(Request $request): View
    {
        $school = SchoolIdentity::current();
        $classifications = ReferenceItem::category('klasifikasi_surat')->get();

        return view('letters.classifications.index', compact('school', 'classifications'));
    }
}
