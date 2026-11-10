<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Good;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\PpdbRegistration;
use App\Models\ReferenceItem;
use App\Models\Room;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentScholarship;
use App\Models\StudentViolation;
use App\Services\AuditTrailService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataImportController extends Controller
{
    /**
     * Download sample CSV template for the specified module
     */
    public function downloadTemplate(string $type): StreamedResponse
    {
        $templates = [
            'students' => [
                'filename' => 'template-import-siswa.csv',
                'headers' => ['nis', 'nisn', 'nik', 'name', 'gender', 'birth_place', 'birth_date', 'religion', 'current_class_level', 'current_rombel', 'status', 'address'],
                'sample' => ['SMA-24001', '0081234567', '3171012345678901', 'Ahmad Fadillah', 'L', 'Jakarta', '2008-05-14', 'Islam', 'X', 'X MIPA 1', 'Aktif', 'Jl. Merdeka No. 10 Jakarta'],
            ],
            'achievements' => [
                'filename' => 'template-import-prestasi.csv',
                'headers' => ['nis', 'name', 'category', 'level', 'rank', 'date', 'organizer', 'notes'],
                'sample' => ['SMA-24001', 'Juara 1 Olimpiade Sains Matematika', 'Akademik', 'Provinsi', 'Juara 1', date('Y-m-d'), 'Dinas Pendidikan', 'Medali Emas & Sertifikat'],
            ],
            'violations' => [
                'filename' => 'template-import-pelanggaran.csv',
                'headers' => ['nis', 'violation_name', 'points', 'violation_date', 'chronology', 'action_taken', 'resolution_status', 'officer_name'],
                'sample' => ['SMA-24001', 'Terlambat Masuk Sekolah Lebih dari 15 Menit', '5', date('Y-m-d'), 'Datang pukul 07.25 WIB karena ban bocor', 'Peringatan lisan dan pembinaan', 'Selesai', 'Guru Piket'],
            ],
            'scholarships' => [
                'filename' => 'template-import-beasiswa.csv',
                'headers' => ['nis', 'scholarship_name', 'source', 'amount', 'period', 'status'],
                'sample' => ['SMA-24001', 'Program Indonesia Pintar (PIP) Tahap 1', 'Kemendikbudristek', '1000000', '2025/2026', 'Disalurkan'],
            ],
            'ppdb' => [
                'filename' => 'template-import-ppdb.csv',
                'headers' => ['full_name', 'nisn', 'nik', 'gender', 'birth_place', 'birth_date', 'school_origin', 'parent_name', 'parent_phone', 'average_score', 'selection_track'],
                'sample' => ['Budi Santoso', '0089876543', '3171019876543210', 'L', 'Bandung', '2009-03-21', 'SMP Negeri 1 Bandung', 'Santoso Wibowo', '081234567890', '88.5', 'Prestasi'],
            ],
            'employees' => [
                'filename' => 'template-import-pegawai.csv',
                'headers' => ['nip', 'nuptk', 'nik', 'name', 'front_title', 'back_title', 'gender', 'employment_type', 'employment_status', 'position', 'rank_group', 'teaching_subject'],
                'sample' => ['198503152010011005', '1234567890123456', '3171011503850001', 'Bambang Sutrisno', 'Drs.', 'M.Pd.', 'L', 'Guru', 'PNS', 'Guru Matematika', 'IV/a', 'Matematika'],
            ],
            'assets' => [
                'filename' => 'template-import-sarpras.csv',
                'headers' => ['code', 'name', 'category', 'brand_spec', 'procurement_year', 'condition', 'quantity', 'unit', 'room_code', 'budget_source', 'acquisition_price'],
                'sample' => ['AST-2024-001', 'Proyektor Epson EB-X500', 'Elektronik', 'Epson 3600 Lumens', date('Y'), 'Baik', '1', 'Unit', 'R-LAB-KOMP', 'BOS Reguler', '6500000'],
            ],
            'goods' => [
                'filename' => 'template-import-barang.csv',
                'headers' => ['code', 'name', 'category', 'unit', 'current_stock', 'minimum_stock', 'storage_location'],
                'sample' => ['ATK-001', 'Kertas HVS A4 75gr PaperOne', 'ATK', 'Rim', '50', '10', 'Gudang Utama - Rak A1'],
            ],
            'incoming_letters' => [
                'filename' => 'template-import-surat-masuk.csv',
                'headers' => ['letter_number', 'sender', 'letter_date', 'received_date', 'subject', 'character', 'attachment_count', 'classification_code'],
                'sample' => ['421/105/Disdik/2025', 'Dinas Pendidikan Provinsi', date('Y-m-d'), date('Y-m-d'), 'Undangan Rapat Koordinasi Kepala SMA Se-Provinsi', 'Penting', '1 Berkas', '005'],
            ],
            'outgoing_letters' => [
                'filename' => 'template-import-surat-keluar.csv',
                'headers' => ['letter_number', 'recipient', 'letter_date', 'subject', 'character', 'signatory_name', 'classification_code'],
                'sample' => ['421.3/088/SMA1LE/2025', 'Orang Tua / Wali Murid Kelas X', date('Y-m-d'), 'Pemberitahuan Kegiatan Penilaian Tengah Semester', 'Biasa', 'Drs. H. Mulyadi, M.Pd.', '421'],
            ],
            'archives' => [
                'filename' => 'template-import-arsip.csv',
                'headers' => ['title', 'category', 'year', 'origin_unit', 'document_number', 'location_rack', 'retention_period_years', 'archive_status'],
                'sample' => ['Laporan Pertanggungjawaban BOS TA 2024', 'Keuangan', '2024', 'Bendahara BOS', 'LPJ/BOS/2024/01', 'Lemari Arsip C-2', '5', 'Aktif'],
            ],
            'references' => [
                'filename' => 'template-import-referensi.csv',
                'headers' => ['category', 'code', 'name', 'description', 'sort_order'],
                'sample' => ['rombel', 'X-MIPA-3', 'X MIPA 3', 'Rombongan Belajar Kelas 10 MIPA 3', '3'],
            ],
        ];

        $conf = $templates[$type] ?? $templates['students'];

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$conf['filename']}\"",
        ];

        return response()->stream(function () use ($conf) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($output, $conf['headers']);
            fputcsv($output, $conf['sample']);
            fclose($output);
        }, 200, $headers);
    }

    /**
     * Process universal CSV data import
     */
    public function import(Request $request, string $type): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'max:5120'], // max 5MB
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        if (! $handle) {
            return back()->with('error', 'Gagal membuka berkas CSV yang diunggah.');
        }

        // Detect BOM and read header
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return back()->with('error', 'Berkas CSV kosong atau tidak memiliki baris tajuk (header).');
        }

        // Clean headers: trim whitespace & lowercase
        $headers = array_map(fn ($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h))), $header);

        $rowCount = 0;
        $successCount = 0;

        while (($data = fgetcsv($handle)) !== false) {
            if (empty(array_filter($data, fn ($val) => trim($val) !== ''))) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $colName) {
                $row[$colName] = isset($data[$index]) ? trim($data[$index]) : null;
            }

            $rowCount++;

            try {
                $imported = $this->processRow($type, $row);
                if ($imported) {
                    $successCount++;
                }
            } catch (\Throwable $e) {
                // Ignore faulty row and continue parsing
            }
        }

        fclose($handle);

        AuditTrailService::log('import', $type, "Mengimpor {$successCount} data dari berkas CSV ({$file->getClientOriginalName()})");

        return back()->with('success', "Proses import berhasil! {$successCount} dari {$rowCount} baris data berhasil dimasukkan ke sistem.");
    }

    /**
     * Helper to process single row per module
     */
    protected function processRow(string $type, array $row): bool
    {
        switch ($type) {
            case 'students':
                if (empty($row['name'])) {
                    return false;
                }
                $nis = $row['nis'] ?? ('SMA-'.date('y').str_pad((string) (Student::count() + 1), 4, '0', STR_PAD_LEFT));
                Student::updateOrCreate(
                    ['nis' => $nis],
                    [
                        'nisn' => $row['nisn'] ?? null,
                        'nik' => $row['nik'] ?? null,
                        'name' => $row['name'],
                        'gender' => in_array(strtoupper($row['gender'] ?? ''), ['L', 'P']) ? strtoupper($row['gender']) : 'L',
                        'birth_place' => $row['birth_place'] ?? null,
                        'birth_date' => ! empty($row['birth_date']) ? Carbon::parse($row['birth_date'])->toDateString() : null,
                        'religion' => $row['religion'] ?? 'Islam',
                        'current_class_level' => $row['current_class_level'] ?? 'X',
                        'current_rombel' => $row['current_rombel'] ?? 'X MIPA 1',
                        'status' => $row['status'] ?? 'Aktif',
                        'address' => $row['address'] ?? null,
                        'qr_code_token' => Str::uuid()->toString(),
                    ]
                );

                return true;

            case 'achievements':
                if (empty($row['name'])) {
                    return false;
                }
                $student = ! empty($row['nis']) ? Student::where('nis', $row['nis'])->orWhere('nisn', $row['nis'])->first() : null;
                if (! $student) {
                    $student = Student::first();
                }
                if (! $student) {
                    return false;
                }

                StudentAchievement::create([
                    'student_id' => $student->id,
                    'name' => $row['name'],
                    'category' => $row['category'] ?? 'Akademik',
                    'level' => $row['level'] ?? 'Sekolah',
                    'rank' => $row['rank'] ?? 'Juara 1',
                    'date' => ! empty($row['date']) ? Carbon::parse($row['date'])->toDateString() : date('Y-m-d'),
                    'organizer' => $row['organizer'] ?? null,
                    'notes' => $row['notes'] ?? null,
                ]);

                return true;

            case 'violations':
                if (empty($row['violation_name'])) {
                    return false;
                }
                $student = ! empty($row['nis']) ? Student::where('nis', $row['nis'])->orWhere('nisn', $row['nis'])->first() : null;
                if (! $student) {
                    $student = Student::first();
                }
                if (! $student) {
                    return false;
                }

                StudentViolation::create([
                    'student_id' => $student->id,
                    'violation_name' => $row['violation_name'],
                    'points' => (int) ($row['points'] ?? 5),
                    'violation_date' => ! empty($row['violation_date']) ? Carbon::parse($row['violation_date'])->toDateString() : date('Y-m-d'),
                    'chronology' => $row['chronology'] ?? null,
                    'action_taken' => $row['action_taken'] ?? null,
                    'resolution_status' => $row['resolution_status'] ?? 'Selesai',
                    'officer_name' => $row['officer_name'] ?? 'Guru Piket',
                ]);

                return true;

            case 'scholarships':
                if (empty($row['scholarship_name'])) {
                    return false;
                }
                $student = ! empty($row['nis']) ? Student::where('nis', $row['nis'])->orWhere('nisn', $row['nis'])->first() : null;
                if (! $student) {
                    $student = Student::first();
                }
                if (! $student) {
                    return false;
                }

                StudentScholarship::create([
                    'student_id' => $student->id,
                    'scholarship_name' => $row['scholarship_name'],
                    'source' => $row['source'] ?? 'Pemerintah',
                    'amount' => (float) ($row['amount'] ?? 1000000),
                    'period' => $row['period'] ?? date('Y').'/'.(date('Y') + 1),
                    'status' => $row['status'] ?? 'Disalurkan',
                ]);

                return true;

            case 'ppdb':
                if (empty($row['full_name'])) {
                    return false;
                }
                PpdbRegistration::create([
                    'registration_number' => 'PPDB-'.date('Y').'-'.strtoupper(Str::random(5)),
                    'full_name' => $row['full_name'],
                    'nisn' => $row['nisn'] ?? null,
                    'nik' => $row['nik'] ?? null,
                    'gender' => in_array(strtoupper($row['gender'] ?? ''), ['L', 'P']) ? strtoupper($row['gender']) : 'L',
                    'birth_place' => $row['birth_place'] ?? null,
                    'birth_date' => ! empty($row['birth_date']) ? Carbon::parse($row['birth_date'])->toDateString() : null,
                    'school_origin' => $row['school_origin'] ?? 'SMP Negeri',
                    'parent_name' => $row['parent_name'] ?? null,
                    'parent_phone' => $row['parent_phone'] ?? null,
                    'average_score' => (float) ($row['average_score'] ?? 80),
                    'selection_track' => $row['selection_track'] ?? 'Reguler',
                    'status' => 'Terdaftar',
                ]);

                return true;

            case 'employees':
                if (empty($row['name'])) {
                    return false;
                }
                Employee::create([
                    'nip' => $row['nip'] ?? null,
                    'nuptk' => $row['nuptk'] ?? null,
                    'nik' => $row['nik'] ?? null,
                    'name' => $row['name'],
                    'front_title' => $row['front_title'] ?? null,
                    'back_title' => $row['back_title'] ?? null,
                    'gender' => in_array(strtoupper($row['gender'] ?? ''), ['L', 'P']) ? strtoupper($row['gender']) : 'L',
                    'employment_type' => $row['employment_type'] ?? 'Guru',
                    'employment_status' => $row['employment_status'] ?? 'PNS',
                    'position' => $row['position'] ?? 'Guru Mata Pelajaran',
                    'rank_group' => $row['rank_group'] ?? 'III/a',
                    'teaching_subject' => $row['teaching_subject'] ?? null,
                    'status' => 'Aktif',
                ]);

                return true;

            case 'assets':
                if (empty($row['name'])) {
                    return false;
                }
                $code = $row['code'] ?? ('AST-'.date('Y').'-'.strtoupper(Str::random(4)));
                $room = ! empty($row['room_code']) ? Room::where('code', $row['room_code'])->first() : Room::first();

                Asset::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $row['name'],
                        'category' => $row['category'] ?? 'Sarana',
                        'brand_spec' => $row['brand_spec'] ?? null,
                        'procurement_year' => (int) ($row['procurement_year'] ?? date('Y')),
                        'condition' => $row['condition'] ?? 'Baik',
                        'quantity' => (int) ($row['quantity'] ?? 1),
                        'unit' => $row['unit'] ?? 'Unit',
                        'room_id' => $room?->id,
                        'budget_source' => $row['budget_source'] ?? 'BOS Reguler',
                        'acquisition_price' => (float) ($row['acquisition_price'] ?? 0),
                        'qr_code_token' => Str::uuid()->toString(),
                    ]
                );

                return true;

            case 'goods':
                if (empty($row['name'])) {
                    return false;
                }
                $code = $row['code'] ?? ('BRG-'.strtoupper(Str::random(5)));

                Good::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $row['name'],
                        'category' => $row['category'] ?? 'ATK',
                        'unit' => $row['unit'] ?? 'Pcs',
                        'current_stock' => (int) ($row['current_stock'] ?? 10),
                        'minimum_stock' => (int) ($row['minimum_stock'] ?? 5),
                        'storage_location' => $row['storage_location'] ?? 'Gudang Utama',
                    ]
                );

                return true;

            case 'incoming_letters':
                if (empty($row['subject'])) {
                    return false;
                }
                $year = date('Y');
                $count = IncomingLetter::whereYear('received_date', $year)->count() + 1;
                $agenda = 'SM/'.$year.'/'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);

                IncomingLetter::create([
                    'agenda_number' => $agenda,
                    'letter_number' => $row['letter_number'] ?? ('420/'.rand(100, 999).'/'.date('Y')),
                    'sender' => $row['sender'] ?? 'Instansi Terkait',
                    'letter_date' => ! empty($row['letter_date']) ? Carbon::parse($row['letter_date'])->toDateString() : date('Y-m-d'),
                    'received_date' => ! empty($row['received_date']) ? Carbon::parse($row['received_date'])->toDateString() : date('Y-m-d'),
                    'subject' => $row['subject'],
                    'character' => $row['character'] ?? 'Biasa',
                    'attachment_count' => $row['attachment_count'] ?? '1 Berkas',
                    'classification_code' => $row['classification_code'] ?? '005',
                    'status' => 'Menunggu Disposisi',
                ]);

                return true;

            case 'outgoing_letters':
                if (empty($row['subject'])) {
                    return false;
                }
                OutgoingLetter::create([
                    'letter_number' => $row['letter_number'] ?? ('421.3/'.rand(100, 999).'/SMA1LE/'.date('Y')),
                    'recipient' => $row['recipient'] ?? 'Pihak Terkait',
                    'letter_date' => ! empty($row['letter_date']) ? Carbon::parse($row['letter_date'])->toDateString() : date('Y-m-d'),
                    'subject' => $row['subject'],
                    'character' => $row['character'] ?? 'Biasa',
                    'signatory_name' => $row['signatory_name'] ?? 'Kepala Sekolah',
                    'classification_code' => $row['classification_code'] ?? '421',
                    'status' => 'Draf',
                ]);

                return true;

            case 'archives':
                if (empty($row['title'])) {
                    return false;
                }
                $year = (int) ($row['year'] ?? date('Y'));
                $retention = (int) ($row['retention_period_years'] ?? 5);

                Archive::create([
                    'archive_number' => 'ARS/'.date('Y').'/'.str_pad((string) (Archive::count() + 1), 4, '0', STR_PAD_LEFT),
                    'title' => $row['title'],
                    'category' => $row['category'] ?? 'Umum',
                    'year' => $year,
                    'origin_unit' => $row['origin_unit'] ?? 'Tata Usaha',
                    'document_number' => $row['document_number'] ?? null,
                    'location_rack' => $row['location_rack'] ?? 'Lemari Arsip Utama',
                    'retention_period_years' => $retention,
                    'retention_expire_date' => now()->addYears($retention)->toDateString(),
                    'archive_status' => $row['archive_status'] ?? 'Aktif',
                    'qr_code_token' => Str::uuid()->toString(),
                ]);

                return true;

            case 'references':
                if (empty($row['name'])) {
                    return false;
                }
                ReferenceItem::create([
                    'category' => $row['category'] ?? 'pangkat_golongan',
                    'code' => $row['code'] ?? null,
                    'name' => $row['name'],
                    'description' => $row['description'] ?? null,
                    'sort_order' => (int) ($row['sort_order'] ?? 1),
                    'is_active' => true,
                ]);

                return true;

            default:
                return false;
        }
    }
}
