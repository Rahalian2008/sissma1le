<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeHistory;
use App\Models\EmployeeLeave;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeBookController extends Controller
{
    /**
     * 1. Buku Induk Pegawai
     */
    public function bukuInduk(Request $request): View
    {
        $query = Employee::with('histories');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('ni_pppk', 'like', "%{$search}%")
                    ->orWhere('nuptk', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('employment_status', $status);
        }

        if ($type = $request->query('type')) {
            $query->where('employment_type', $type);
        }

        $employees = $query->orderBy('name')->paginate(15)->withQueryString();
        $totalPns = Employee::where('employment_status', 'PNS')->count();
        $totalPppk = Employee::where('employment_status', 'PPPK')->count();
        $totalHonorer = Employee::whereIn('employment_status', ['GTT', 'PTT', 'Honorer'])->count();

        return view('employees.books.buku_induk', compact('employees', 'totalPns', 'totalPppk', 'totalHonorer'));
    }

    public function showBukuInduk(Employee $employee): View
    {
        $employee->load(['histories', 'leaves' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('employees.books.buku_induk_show', compact('employee'));
    }

    public function printBukuInduk(Request $request): View
    {
        $school = SchoolIdentity::current();
        $status = $request->query('status');

        $query = Employee::with('histories')->orderBy('name');
        if ($status) {
            $query->where('employment_status', $status);
        }
        $employees = $query->get();

        return view('employees.books.buku_induk_print', compact('school', 'employees', 'status'));
    }

    /**
     * 2. Buku Kontrol Kenaikan Pangkat (KP)
     */
    public function kontrolPangkat(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = Employee::where('employment_status', 'PNS')->with('histories');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('rank_group', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('next_kp_tmt', 'asc')->paginate(15)->withQueryString();

        // Calculate statistics
        $pnsEmployees = Employee::where('employment_status', 'PNS')->get();
        $dueSoonCount = $pnsEmployees->filter(fn ($e) => $e->isKpDue(6))->count();
        $eligibleCount = $pnsEmployees->filter(fn ($e) => $e->next_kp_tmt && $e->next_kp_tmt->isPast())->count();

        return view('employees.books.kontrol_pangkat', compact('employees', 'school', 'dueSoonCount', 'eligibleCount'));
    }

    public function updateStatusKp(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'kp_status' => ['required', 'string', 'max:50'],
            'next_kp_tmt' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $employee->update([
            'kp_status' => $validated['kp_status'],
            'next_kp_tmt' => $validated['next_kp_tmt'] ?? $employee->next_kp_tmt,
        ]);

        AuditTrailService::log('update', 'kepegawaian', "Memperbarui status usulan Kenaikan Pangkat {$employee->formatted_name}: {$validated['kp_status']}");

        return back()->with('success', 'Status usulan Kenaikan Pangkat berhasil diperbarui.');
    }

    public function printKontrolPangkat(): View
    {
        $school = SchoolIdentity::current();
        $employees = Employee::where('employment_status', 'PNS')
            ->orderBy('next_kp_tmt', 'asc')
            ->get();

        return view('employees.books.kontrol_pangkat_print', compact('school', 'employees'));
    }

    /**
     * 3. Buku Kontrol Kenaikan Gaji Berkala (KGB)
     */
    public function kontrolKgb(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = Employee::whereIn('employment_status', ['PNS', 'PPPK']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('ni_pppk', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('employment_status', $status);
        }

        $employees = $query->orderBy('next_kgb_tmt', 'asc')->paginate(15)->withQueryString();

        $allKgb = Employee::whereIn('employment_status', ['PNS', 'PPPK'])->get();
        $dueSoonKgb = $allKgb->filter(fn ($e) => $e->isKgbDue(3))->count();

        return view('employees.books.kontrol_kgb', compact('employees', 'school', 'dueSoonKgb'));
    }

    public function updateKgb(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'last_kgb_tmt' => ['required', 'date'],
            'last_kgb_salary' => ['required', 'numeric', 'min:0'],
            'sk_number' => ['nullable', 'string', 'max:100'],
        ]);

        $nextKgb = Carbon::parse($validated['last_kgb_tmt'])->addYears(2)->toDateString();

        $employee->update([
            'last_kgb_tmt' => $validated['last_kgb_tmt'],
            'next_kgb_tmt' => $nextKgb,
            'last_kgb_salary' => $validated['last_kgb_salary'],
        ]);

        EmployeeHistory::create([
            'employee_id' => $employee->id,
            'history_type' => 'KGB',
            'title' => 'Kenaikan Gaji Berkala TMT '.Carbon::parse($validated['last_kgb_tmt'])->translatedFormat('d F Y'),
            'document_number' => $validated['sk_number'] ?? '822.2/KGB/'.date('Y'),
            'start_date' => $validated['last_kgb_tmt'],
            'notes' => 'Gaji Pokok Baru: Rp '.number_format($validated['last_kgb_salary'], 0, ',', '.'),
        ]);

        AuditTrailService::log('update', 'kepegawaian', "Memperbarui KGB {$employee->formatted_name} TMT {$validated['last_kgb_tmt']}");

        return back()->with('success', 'Data KGB berhasil diperbarui dan dijadwalkan kembali untuk 2 tahun mendatang.');
    }

    public function printSuratKgb(Employee $employee): View
    {
        $school = SchoolIdentity::current();

        return view('employees.books.surat_kgb_print', compact('school', 'employee'));
    }

    public function printKontrolKgb(): View
    {
        $school = SchoolIdentity::current();
        $employees = Employee::whereIn('employment_status', ['PNS', 'PPPK'])
            ->orderBy('next_kgb_tmt', 'asc')
            ->get();

        return view('employees.books.kontrol_kgb_print', compact('school', 'employees'));
    }

    /**
     * 4. Buku Kontrol Cuti Pegawai
     */
    public function kontrolCuti(Request $request): View
    {
        $school = SchoolIdentity::current();
        $year = (int) $request->query('year', date('Y'));

        $query = Employee::active();
        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%");
        }
        $employees = $query->orderBy('name')->paginate(15)->withQueryString();

        $recentLeaves = EmployeeLeave::with('employee')
            ->whereYear('start_date', $year)
            ->latest('start_date')
            ->take(10)
            ->get();

        $allEmployeesList = Employee::active()->orderBy('name')->get();

        return view('employees.books.kontrol_cuti', compact('employees', 'recentLeaves', 'allEmployeesList', 'school', 'year'));
    }

    public function storeCuti(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'days_count' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string'],
            'address_during_leave' => ['nullable', 'string'],
            'replacement_employee_id' => ['nullable', 'exists:employees,id'],
            'letter_number' => ['nullable', 'string', 'max:100'],
        ]);

        $year = date('Y', strtotime($validated['start_date']));
        $count = EmployeeLeave::whereYear('start_date', $year)->count() + 1;
        $letterNumber = $validated['letter_number'] ?: "800.1.3/CUTI/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        $leave = EmployeeLeave::create([
            'employee_id' => $validated['employee_id'],
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_count' => $validated['days_count'],
            'reason' => $validated['reason'],
            'address_during_leave' => $validated['address_during_leave'],
            'replacement_employee_id' => $validated['replacement_employee_id'],
            'letter_number' => $letterNumber,
            'status' => 'Disetujui',
            'approved_by' => Auth::id(),
        ]);

        $employee = Employee::find($validated['employee_id']);
        AuditTrailService::log('create', 'kepegawaian', "Menerbitkan surat cuti [{$validated['leave_type']}] untuk {$employee->formatted_name} selama {$validated['days_count']} hari");

        return back()->with('success', "Permohonan cuti untuk {$employee->formatted_name} berhasil dicatat dengan Nomor {$letterNumber}.");
    }

    public function printSuratCuti(EmployeeLeave $leave): View
    {
        $school = SchoolIdentity::current();
        $leave->load(['employee', 'replacementEmployee', 'approver']);

        return view('employees.books.surat_cuti_print', compact('school', 'leave'));
    }

    public function printKontrolCuti(Request $request): View
    {
        $school = SchoolIdentity::current();
        $year = (int) $request->query('year', date('Y'));
        $employees = Employee::active()->with(['leaves' => function ($q) use ($year) {
            $q->whereYear('start_date', $year)->where('status', 'Disetujui');
        }])->orderBy('name')->get();

        return view('employees.books.kontrol_cuti_print', compact('school', 'employees', 'year'));
    }

    /**
     * 5. Buku Kontrol PPPK (MHPK & Evaluasi Kontrak)
     */
    public function kontrolPppk(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = Employee::where('employment_status', 'PPPK');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('ni_pppk', 'like', "%{$search}%")
                    ->orWhere('pppk_contract_number', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('pppk_contract_end', 'asc')->paginate(15)->withQueryString();

        $allPppk = Employee::where('employment_status', 'PPPK')->get();
        $expiringCount = $allPppk->filter(fn ($e) => $e->isPppkContractExpiring(6))->count();

        return view('employees.books.kontrol_pppk', compact('employees', 'school', 'expiringCount'));
    }

    public function updateKontrakPppk(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'ni_pppk' => ['nullable', 'string', 'max:50'],
            'pppk_contract_number' => ['required', 'string', 'max:100'],
            'pppk_contract_start' => ['required', 'date'],
            'pppk_contract_end' => ['required', 'date', 'after:pppk_contract_start'],
        ]);

        $employee->update($validated);

        AuditTrailService::log('update', 'kepegawaian', "Memperbarui Perjanjian Kerja PPPK {$employee->formatted_name} No. {$validated['pppk_contract_number']}");

        return back()->with('success', 'Data Masa Hubungan Perjanjian Kerja (MHPK) PPPK berhasil diperbarui.');
    }

    public function printKontrolPppk(): View
    {
        $school = SchoolIdentity::current();
        $employees = Employee::where('employment_status', 'PPPK')
            ->orderBy('pppk_contract_end', 'asc')
            ->get();

        return view('employees.books.kontrol_pppk_print', compact('school', 'employees'));
    }

    /**
     * Terapkan SK Kenaikan Pangkat Baru & Sinkronisasi KGB
     */
    public function applySkPangkat(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'new_rank_group' => ['required', 'string', 'max:50'],
            'sk_number' => ['required', 'string', 'max:100'],
            'rank_tmt' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldRank = $employee->rank_group;
        $nextKp = Carbon::parse($validated['rank_tmt'])->addYears(4)->toDateString();
        $nextKgb = Carbon::parse($validated['rank_tmt'])->addYears(2)->toDateString();

        $employee->update([
            'rank_group' => $validated['new_rank_group'],
            'rank_tmt' => $validated['rank_tmt'],
            'last_kp_tmt' => $validated['rank_tmt'],
            'next_kp_tmt' => $nextKp,
            'next_kgb_tmt' => $nextKgb,
            'kp_status' => 'SK Diterbitkan',
        ]);

        EmployeeHistory::create([
            'employee_id' => $employee->id,
            'history_type' => 'Pangkat',
            'title' => "Kenaikan Pangkat ke Golongan {$validated['new_rank_group']}",
            'document_number' => $validated['sk_number'],
            'start_date' => $validated['rank_tmt'],
            'notes' => $validated['notes'] ?: "Diterbitkan melalui SK No. {$validated['sk_number']} (Riwayat Pangkat: {$oldRank} -> {$validated['new_rank_group']})",
        ]);

        AuditTrailService::log('update', 'kepegawaian', "Menerapkan SK Kenaikan Pangkat {$employee->formatted_name}: {$oldRank} -> {$validated['new_rank_group']}");

        return back()->with('success', "SK Kenaikan Pangkat baru untuk {$employee->formatted_name} berhasil diterapkan. Golongan ruang telah diperbarui ke {$validated['new_rank_group']} dan jadwal KGB/KP berikutnya telah dihitung ulang.");
    }

    /**
     * 6. Buku Kontrol Pensiun Pegawai (BUP - Batas Usia Pensiun)
     */
    public function kontrolPensiun(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = Employee::whereIn('employment_status', ['PNS', 'PPPK']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('rank_group', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            if ($status === 'Pensiun') {
                $query->where('pension_status', 'Pensiun');
            } elseif ($status === 'Aktif') {
                $query->where('pension_status', '!=', 'Pensiun');
            } else {
                $query->where('pension_status', $status);
            }
        }

        if ($type = $request->query('type')) {
            $query->where('employment_type', $type);
        }

        // Order by birth_date asc (older employees first)
        $employees = $query->orderBy('birth_date', 'asc')->paginate(15)->withQueryString();

        $allPns = Employee::whereIn('employment_status', ['PNS', 'PPPK'])->get();
        $dueSoonCount = $allPns->filter(fn ($e) => $e->isPensionDue(12))->count();
        $mppCount = $allPns->filter(fn ($e) => $e->isPensionMpp(24))->count();
        $retiredCount = Employee::where('pension_status', 'Pensiun')->orWhere(function ($q) {
            $q->where('is_active', false)->whereNotNull('birth_date');
        })->count();

        return view('employees.books.kontrol_pensiun', compact('employees', 'school', 'dueSoonCount', 'mppCount', 'retiredCount'));
    }

    public function updateStatusPensiun(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'pension_status' => ['required', 'string', 'max:50'],
            'pension_tmt' => ['nullable', 'date'],
            'pension_sk_number' => ['nullable', 'string', 'max:100'],
            'pension_notes' => ['nullable', 'string'],
        ]);

        if (empty($validated['pension_tmt']) && $employee->calculated_pension_tmt) {
            $validated['pension_tmt'] = $employee->calculated_pension_tmt->toDateString();
        }

        // If officially retired, set is_active = false
        if ($validated['pension_status'] === 'Pensiun') {
            $employee->is_active = false;

            EmployeeHistory::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'history_type' => 'Pensiun',
                ],
                [
                    'title' => 'Pemberhentian Dengan Hormat / Pensiun BUP',
                    'document_number' => $validated['pension_sk_number'] ?: 'SK-BUP/BKN/'.date('Y'),
                    'start_date' => $validated['pension_tmt'] ?: now()->toDateString(),
                    'notes' => $validated['pension_notes'] ?: 'Mencapai Batas Usia Pensiun (BUP) Pegawai ASN.',
                ]
            );
        }

        $employee->update($validated);

        AuditTrailService::log('update', 'kepegawaian', "Memperbarui status pensiun {$employee->formatted_name}: {$validated['pension_status']}");

        return back()->with('success', "Status kontrol pensiun {$employee->formatted_name} berhasil diperbarui.");
    }

    public function printKontrolPensiun(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = Employee::whereIn('employment_status', ['PNS', 'PPPK']);

        if ($type = $request->query('type')) {
            $query->where('employment_type', $type);
        }

        $employees = $query->orderBy('birth_date', 'asc')->get();

        return view('employees.books.kontrol_pensiun_print', compact('school', 'employees'));
    }

    public function printSuratPensiun(Employee $employee): View
    {
        $school = SchoolIdentity::current();

        return view('employees.books.surat_pensiun_print', compact('school', 'employee'));
    }

    /**
     * 7. Buku Kontrol Diklat, Workshop & Sertifikasi (PKB Guru & Tendik)
     */
    public function kontrolDiklat(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = EmployeeHistory::where('history_type', 'Diklat/Pelatihan')->with('employee');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('institution_or_issuer', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%");
                    });
            });
        }

        if ($employeeId = $request->query('employee_id')) {
            $query->where('employee_id', $employeeId);
        }

        if ($year = $request->query('year')) {
            $query->whereYear('start_date', $year);
        }

        $trainings = $query->latest('start_date')->paginate(15)->withQueryString();
        $employees = Employee::active()->orderBy('name')->get();

        // Stats
        $totalTrainings = EmployeeHistory::where('history_type', 'Diklat/Pelatihan')->count();
        $totalHours = (int) EmployeeHistory::where('history_type', 'Diklat/Pelatihan')->sum('training_hours');
        $thisYearCount = EmployeeHistory::where('history_type', 'Diklat/Pelatihan')->whereYear('start_date', date('Y'))->count();
        $uniqueEmployeesCount = EmployeeHistory::where('history_type', 'Diklat/Pelatihan')->distinct('employee_id')->count('employee_id');

        return view('employees.books.kontrol_diklat', compact('trainings', 'employees', 'school', 'totalTrainings', 'totalHours', 'thisYearCount', 'uniqueEmployeesCount'));
    }

    public function storeDiklat(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'title' => ['required', 'string', 'max:255'],
            'institution_or_issuer' => ['required', 'string', 'max:150'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'training_hours' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('diklat_documents', 'public');
        }

        EmployeeHistory::create([
            'employee_id' => $validated['employee_id'],
            'history_type' => 'Diklat/Pelatihan',
            'title' => $validated['title'],
            'institution_or_issuer' => $validated['institution_or_issuer'],
            'document_number' => $validated['document_number'],
            'training_hours' => $validated['training_hours'] ?? 32,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? $validated['start_date'],
            'document_path' => $documentPath,
            'notes' => $validated['notes'],
        ]);

        $employee = Employee::find($validated['employee_id']);
        AuditTrailService::log('create', 'kepegawaian', "Mencatat Diklat/Pelatihan [{$validated['title']}] untuk {$employee->formatted_name}");

        return back()->with('success', "Data sertifikat/diklat untuk {$employee->formatted_name} berhasil disimpan.");
    }

    public function destroyDiklat(EmployeeHistory $history): RedirectResponse
    {
        if ($history->history_type !== 'Diklat/Pelatihan') {
            abort(403);
        }

        $empName = $history->employee?->name ?? 'Pegawai';
        $title = $history->title;
        $history->delete();

        AuditTrailService::log('delete', 'kepegawaian', "Menghapus catatan diklat [{$title}] untuk {$empName}");

        return back()->with('success', 'Catatan pelatihan/diklat berhasil dihapus.');
    }

    public function printKontrolDiklat(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = EmployeeHistory::where('history_type', 'Diklat/Pelatihan')->with('employee');

        if ($year = $request->query('year')) {
            $query->whereYear('start_date', $year);
        }

        if ($employeeId = $request->query('employee_id')) {
            $query->where('employee_id', $employeeId);
        }

        $trainings = $query->orderBy('start_date', 'desc')->get();
        $selectedYear = $request->query('year', date('Y'));

        return view('employees.books.kontrol_diklat_print', compact('school', 'trainings', 'selectedYear'));
    }

    /**
     * 8. Cetak Format Resmi Daftar Hadir / Presensi Bulanan Guru & Tendik
     */
    public function printDaftarHadir(Request $request): View
    {
        $school = SchoolIdentity::current();
        $month = (int) $request->query('month', date('n'));
        $year = (int) $request->query('year', date('Y'));
        $type = $request->query('type', 'Semua');

        $query = Employee::active();
        if ($type === 'Guru') {
            $query->where('employment_type', 'Guru');
        } elseif ($type === 'Tendik' || $type === 'Tenaga Kependidikan') {
            $query->where('employment_type', 'Tenaga Kependidikan');
        }

        $employees = $query->orderBy('name')->get();

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F');

        AuditTrailService::log('print', 'kepegawaian', "Mencetak Format Daftar Hadir Pegawai Bulan {$monthName} {$year}");

        return view('employees.books.daftar_hadir_print', compact('school', 'employees', 'month', 'year', 'monthName', 'daysInMonth', 'type'));
    }

    public function printEmployeeDetail(Employee $employee): View
    {
        $school = SchoolIdentity::current();
        $employee->load(['histories', 'leaves']);

        return view('employees.books.buku_induk_show', compact('employee', 'school'));
    }

    public function controlKp(Request $request): View
    {
        return $this->kontrolPangkat($request);
    }

    public function printControlKp(): View
    {
        return $this->printKontrolPangkat();
    }

    public function controlKgb(Request $request): View
    {
        return $this->kontrolKgb($request);
    }

    public function printControlKgb(): View
    {
        return $this->printKontrolKgb();
    }

    public function controlPppk(Request $request): View
    {
        return $this->kontrolPppk($request);
    }

    public function printControlPppk(): View
    {
        return $this->printKontrolPppk();
    }

    public function controlPension(Request $request): View
    {
        return $this->kontrolPensiun($request);
    }

    public function printControlPension(Request $request): View
    {
        return $this->printKontrolPensiun($request);
    }

    public function controlLeaves(Request $request): View
    {
        return $this->kontrolCuti($request);
    }

    public function printControlLeaves(Request $request): View
    {
        return $this->printKontrolCuti($request);
    }
}
