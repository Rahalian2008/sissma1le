<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeHistory;
use App\Models\ReferenceItem;
use App\Models\Student;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Employee::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('homeroom_rombel', 'like', "%{$search}%");
            });
        }

        if ($type = $request->query('type')) {
            $query->where('employment_type', $type);
        }

        if ($status = $request->query('status')) {
            $query->where('employment_status', $status);
        }

        $employees = $query->orderBy('name')->paginate(15)->withQueryString();

        $types = ['Guru', 'Tenaga Kependidikan'];
        $statuses = ReferenceItem::category('status_kepegawaian')->pluck('name')->toArray();
        if (empty($statuses)) {
            $statuses = ['PNS', 'PPPK', 'PPPK Paruh Waktu', 'GTT', 'PTT', 'Honorer'];
        }

        return view('employees.index', compact('employees', 'types', 'statuses'));
    }

    public function create(): View
    {
        $rankGroups = ReferenceItem::category('pangkat_golongan')->get();
        $positions = ReferenceItem::category('jabatan')->get();
        $employmentStatuses = ReferenceItem::category('status_kepegawaian')->get();
        $rombels = Student::distinct()->whereNotNull('current_rombel')->where('current_rombel', '!=', '')->orderBy('current_rombel')->pluck('current_rombel')->toArray();
        if (empty($rombels)) {
            $rombels = [
                'X Merdeka 1', 'X Merdeka 2', 'X Merdeka 3', 'X Merdeka 4', 'X Merdeka 5', 'X Merdeka 6', 'X Merdeka 7',
                'XI Merdeka 1', 'XI Merdeka 2', 'XI Merdeka 3', 'XI Merdeka 4', 'XI Merdeka 5', 'XI Merdeka 6', 'XI Merdeka 7', 'XI Merdeka 8',
                'XII Merdeka 1', 'XII Merdeka 2', 'XII Merdeka 3', 'XII Merdeka 4', 'XII Merdeka 5', 'XII Merdeka 6', 'XII Merdeka 7',
            ];
        }
        $standardPositions = [
            'Kepala Sekolah',
            'Wakil Kepala Sekolah Bid. Kesiswaan',
            'Wakil Kepala Sekolah Bid. Sarpras',
            'Kepala Sekolah Bid. Kurikulum',
            'Wakil Kepala Sekolah Bid. Kurikulum',
            'Wakil Kepala Sekolah Bid. Humas',
            'Koord. Bp/BK',
            'Koordinator Keagamaan',
            'Kepala Lab. IPA',
            'Kepala Lab. Komputer',
            'Pembina OSIS',
            'Wali Kelas',
            'Pembina Ekstrakurikuler',
        ];

        return view('employees.create', compact('rankGroups', 'positions', 'rombels', 'standardPositions', 'employmentStatuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:30', 'unique:employees'],
            'nuptk' => ['nullable', 'string', 'max:30'],
            'nik' => ['nullable', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:255'],
            'title_prefix' => ['nullable', 'string', 'max:30'],
            'title_suffix' => ['nullable', 'string', 'max:50'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'employment_type' => ['required', 'string'],
            'employment_status' => ['required', 'string'],
            'rank_group' => ['nullable', 'string', 'max:50'],
            'rank_tmt' => ['nullable', 'date'],
            'position' => ['nullable', 'string', 'max:255'],
            'positions' => ['nullable', 'array'],
            'positions.*' => ['string', 'max:100'],
            'homeroom_rombel' => ['nullable', 'string', 'max:50'],
            'custom_position' => ['nullable', 'string', 'max:100'],
            'position_tmt' => ['nullable', 'date'],
            'years_of_service_years' => ['nullable', 'integer', 'min:0'],
            'years_of_service_months' => ['nullable', 'integer', 'min:0', 'max:11'],
            'education_level' => ['nullable', 'string', 'max:30'],
            'education_major' => ['nullable', 'string', 'max:100'],
            'education_institution' => ['nullable', 'string', 'max:150'],
            'education_grad_year' => ['nullable', 'string', 'max:10'],
            'is_certified' => ['nullable', 'boolean'],
            'teaching_subject' => ['nullable', 'string', 'max:100'],
            'additional_tasks' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
        ]);

        $selectedPositions = (array) $request->input('positions', []);
        $homeroomRombel = $request->input('homeroom_rombel');
        $customPos = trim((string) $request->input('custom_position', ''));

        $finalPositions = [];
        $hasWali = false;
        foreach ($selectedPositions as $pos) {
            if ($pos === 'Wali Kelas') {
                $hasWali = true;
                if (! empty($homeroomRombel)) {
                    $finalPositions[] = "Wali Kelas {$homeroomRombel}";
                } else {
                    $finalPositions[] = 'Wali Kelas';
                }
            } else {
                $finalPositions[] = $pos;
            }
        }

        if (! empty($customPos) && ! in_array($customPos, $finalPositions)) {
            $finalPositions[] = $customPos;
        }

        if (! empty($finalPositions)) {
            $validated['position'] = implode(', ', $finalPositions);
            $validated['homeroom_rombel'] = $hasWali ? $homeroomRombel : null;
        } elseif ($request->has('position')) {
            $validated['position'] = $request->input('position');
            if (preg_match('/Wali\s+Kelas\s+([X|V|I]+(?:\s+Merdeka)?\s+\d+|[A-Za-z0-9\s]+)/i', (string) $validated['position'], $matches)) {
                $validated['homeroom_rombel'] = trim($matches[1]);
            }
        }

        unset($validated['positions'], $validated['custom_position']);

        $validated['is_certified'] = $request->boolean('is_certified');
        $validated['is_active'] = true;

        $employee = Employee::create($validated);

        AuditTrailService::log('create', 'kepegawaian', "Menambahkan pegawai PTK: {$employee->formatted_name}");

        return redirect()->route('employees.index')->with('success', 'Data pegawai PTK berhasil ditambahkan.');
    }

    public function show(Employee $employee): View
    {
        $employee->load('histories');

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $rankGroups = ReferenceItem::category('pangkat_golongan')->get();
        $positions = ReferenceItem::category('jabatan')->get();
        $employmentStatuses = ReferenceItem::category('status_kepegawaian')->get();
        $rombels = Student::distinct()->whereNotNull('current_rombel')->where('current_rombel', '!=', '')->orderBy('current_rombel')->pluck('current_rombel')->toArray();
        if (empty($rombels)) {
            $rombels = [
                'X Merdeka 1', 'X Merdeka 2', 'X Merdeka 3', 'X Merdeka 4', 'X Merdeka 5', 'X Merdeka 6', 'X Merdeka 7',
                'XI Merdeka 1', 'XI Merdeka 2', 'XI Merdeka 3', 'XI Merdeka 4', 'XI Merdeka 5', 'XI Merdeka 6', 'XI Merdeka 7', 'XI Merdeka 8',
                'XII Merdeka 1', 'XII Merdeka 2', 'XII Merdeka 3', 'XII Merdeka 4', 'XII Merdeka 5', 'XII Merdeka 6', 'XII Merdeka 7',
            ];
        }
        $standardPositions = [
            'Kepala Sekolah',
            'Wakil Kepala Sekolah Bid. Kesiswaan',
            'Wakil Kepala Sekolah Bid. Sarpras',
            'Kepala Sekolah Bid. Kurikulum',
            'Wakil Kepala Sekolah Bid. Kurikulum',
            'Wakil Kepala Sekolah Bid. Humas',
            'Koord. Bp/BK',
            'Koordinator Keagamaan',
            'Kepala Lab. IPA',
            'Kepala Lab. Komputer',
            'Pembina OSIS',
            'Wali Kelas',
            'Pembina Ekstrakurikuler',
        ];

        return view('employees.edit', compact('employee', 'rankGroups', 'positions', 'rombels', 'standardPositions', 'employmentStatuses'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:30', 'unique:employees,nip,'.$employee->id],
            'nuptk' => ['nullable', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:255'],
            'title_prefix' => ['nullable', 'string', 'max:30'],
            'title_suffix' => ['nullable', 'string', 'max:50'],
            'gender' => ['required', 'in:L,P'],
            'employment_type' => ['required', 'string'],
            'employment_status' => ['required', 'string'],
            'rank_group' => ['nullable', 'string', 'max:50'],
            'rank_tmt' => ['nullable', 'date'],
            'position' => ['nullable', 'string', 'max:255'],
            'positions' => ['nullable', 'array'],
            'positions.*' => ['string', 'max:100'],
            'homeroom_rombel' => ['nullable', 'string', 'max:50'],
            'custom_position' => ['nullable', 'string', 'max:100'],
            'position_tmt' => ['nullable', 'date'],
            'years_of_service_years' => ['nullable', 'integer', 'min:0'],
            'years_of_service_months' => ['nullable', 'integer', 'min:0'],
            'education_level' => ['nullable', 'string', 'max:30'],
            'teaching_subject' => ['nullable', 'string', 'max:100'],
            'additional_tasks' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:100'],
        ]);

        $selectedPositions = (array) $request->input('positions', []);
        $homeroomRombel = $request->input('homeroom_rombel');
        $customPos = trim((string) $request->input('custom_position', ''));

        $finalPositions = [];
        $hasWali = false;
        foreach ($selectedPositions as $pos) {
            if ($pos === 'Wali Kelas') {
                $hasWali = true;
                if (! empty($homeroomRombel)) {
                    $finalPositions[] = "Wali Kelas {$homeroomRombel}";
                } else {
                    $finalPositions[] = 'Wali Kelas';
                }
            } else {
                $finalPositions[] = $pos;
            }
        }

        if (! empty($customPos) && ! in_array($customPos, $finalPositions)) {
            $finalPositions[] = $customPos;
        }

        if (! empty($finalPositions)) {
            $validated['position'] = implode(', ', $finalPositions);
            $validated['homeroom_rombel'] = $hasWali ? $homeroomRombel : null;
        } elseif ($request->has('position')) {
            $validated['position'] = $request->input('position');
            if (preg_match('/Wali\s+Kelas\s+([X|V|I]+(?:\s+Merdeka)?\s+\d+|[A-Za-z0-9\s]+)/i', (string) $validated['position'], $matches)) {
                $validated['homeroom_rombel'] = trim($matches[1]);
            }
        }

        unset($validated['positions'], $validated['custom_position']);

        $validated['is_certified'] = $request->boolean('is_certified');

        $old = $employee->toArray();
        $employee->update($validated);

        AuditTrailService::log('update', 'kepegawaian', "Memperbarui data PTK: {$employee->formatted_name}", $old, $validated);

        return redirect()->route('employees.show', $employee)->with('success', 'Data pegawai PTK berhasil diperbarui.');
    }

    public function storeHistory(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'history_type' => ['required', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'institution_or_issuer' => ['nullable', 'string', 'max:150'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['employee_id'] = $employee->id;
        EmployeeHistory::create($validated);

        AuditTrailService::log('create', 'kepegawaian', "Menambah riwayat [{$validated['history_type']}] untuk {$employee->name}");

        return back()->with('success', 'Riwayat kepegawaian berhasil ditambahkan.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $name = $employee->formatted_name;
        $employee->delete();

        AuditTrailService::log('delete', 'kepegawaian', "Menghapus data PTK: {$name}");

        return redirect()->route('employees.index')->with('success', 'Data pegawai PTK berhasil dihapus.');
    }
}
