<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OutgoingLetter;
use App\Models\SchoolIdentity;
use App\Models\Sppd;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SppdController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sppd::with('employee')->latest();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('sppd_number', 'like', "%{$search}%")
                    ->orWhere('assignment_purpose', 'like', "%{$search}%")
                    ->orWhere('destination_location', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $sppds = $query->paginate(15)->withQueryString();

        return view('letters.sppd.index', compact('sppds'));
    }

    public function create(Request $request): View
    {
        $school = SchoolIdentity::current();
        $employees = Employee::active()->orderBy('name')->get();
        $outgoingLetters = OutgoingLetter::latest('letter_date')->take(20)->get();

        // Pre-fill parameters if generated from Surat Tugas
        $prefilledEmployeeId = $request->query('employee_id');
        $prefilledTaskNumber = $request->query('task_number');
        $prefilledPurpose = $request->query('purpose');
        $prefilledLetterId = $request->query('letter_id');

        return view('letters.sppd.create', compact(
            'school',
            'employees',
            'outgoingLetters',
            'prefilledEmployeeId',
            'prefilledTaskNumber',
            'prefilledPurpose',
            'prefilledLetterId'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'outgoing_letter_id' => ['nullable', 'exists:outgoing_letters,id'],
            'letter_task_number' => ['nullable', 'string', 'max:100'],
            'assignment_purpose' => ['required', 'string'],
            'transportation_type' => ['required', 'string', 'max:100'],
            'departure_location' => ['required', 'string', 'max:150'],
            'destination_location' => ['required', 'string', 'max:150'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'budget_source' => ['required', 'string', 'max:100'],
            'budget_account' => ['nullable', 'string', 'max:100'],
            'followers' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $school = SchoolIdentity::current();
        $year = date('Y');
        $nextNum = Sppd::whereYear('created_at', $year)->count() + 1;
        $sppdNumber = '094/'.str_pad((string) $nextNum, 3, '0', STR_PAD_LEFT).'/SPPD/SMA1LE/'.$year;

        $validated['sppd_number'] = $sppdNumber;
        $validated['ppk_name'] = $school->headmaster_name ?? 'Kepala Sekolah';
        $validated['ppk_nip'] = $school->headmaster_nip ?? '-';
        $validated['status'] = 'Diterbitkan';

        $sppd = Sppd::create($validated);

        AuditTrailService::log('create', 'sppd', "Menerbitkan SPPD No. {$sppd->sppd_number} untuk {$sppd->employee->name}");

        return redirect()->route('letters.sppd.index')
            ->with('success', "SPPD No. {$sppd->sppd_number} berhasil diterbitkan.")
            ->with('created_sppd_id', $sppd->id);
    }

    public function show(Sppd $sppd): View
    {
        return $this->print($sppd);
    }

    public function edit(Sppd $sppd): View
    {
        $school = SchoolIdentity::current();
        $employees = Employee::active()->orderBy('name')->get();
        $outgoingLetters = OutgoingLetter::latest('letter_date')->take(20)->get();

        return view('letters.sppd.edit', compact('sppd', 'school', 'employees', 'outgoingLetters'));
    }

    public function update(Request $request, Sppd $sppd): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'outgoing_letter_id' => ['nullable', 'exists:outgoing_letters,id'],
            'letter_task_number' => ['nullable', 'string', 'max:100'],
            'assignment_purpose' => ['required', 'string'],
            'transportation_type' => ['required', 'string', 'max:100'],
            'departure_location' => ['required', 'string', 'max:150'],
            'destination_location' => ['required', 'string', 'max:150'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'budget_source' => ['required', 'string', 'max:100'],
            'budget_account' => ['nullable', 'string', 'max:100'],
            'followers' => ['nullable', 'string'],
            'special_notes' => ['nullable', 'string'],
        ]);

        $sppd->update($validated);

        AuditTrailService::log('update', 'sppd', "Memperbarui berkas SPPD No. {$sppd->sppd_number} ({$sppd->employee->name})");

        return redirect()->route('letters.sppd.index')->with('success', "SPPD No. {$sppd->sppd_number} berhasil diperbarui.");
    }

    public function print(Sppd $sppd): View
    {
        $school = SchoolIdentity::current();
        $sppd->load('employee', 'outgoingLetter');

        AuditTrailService::log('print', 'sppd', "Mencetak berkas resmi SPPD No. {$sppd->sppd_number}");

        return view('letters.sppd.print', compact('sppd', 'school'));
    }

    public function destroy(Sppd $sppd): RedirectResponse
    {
        $number = $sppd->sppd_number;
        $sppd->delete();

        AuditTrailService::log('delete', 'sppd', "Menghapus lembar SPPD No. {$number}");

        return redirect()->route('letters.sppd.index')->with('success', "SPPD No. {$number} berhasil dihapus.");
    }
}
