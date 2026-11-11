<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TuKepegawaianController extends Controller
{
    private function authorizeKepegawaian(): void
    {
        if (! Auth::user()?->canAccessTuSubmodule('kepegawaian')) {
            abort(403, 'Akses tidak diizinkan. Menu ini dikhususkan untuk staf Administrasi Kepegawaian.');
        }
    }

    public function index(Request $request): View
    {
        $this->authorizeKepegawaian();

        $hasEmployees = Schema::hasTable('employees');
        $hasLeaves = Schema::hasTable('employee_leaves');
        $masterAvailableCount = Teacher::where('is_active', true)->count();

        // 1. Metrik Kategori PTK dari Master Data
        $totalEmployees = $hasEmployees ? Employee::count() : 0;
        $totalTeachers = $hasEmployees ? Employee::where('employment_type', 'Guru')->count() : 0;
        $totalStaff = $hasEmployees ? Employee::where('employment_type', '!=', 'Guru')->count() : 0;

        // 2. Status Kepegawaian dari Master Data
        $pnsCount = $hasEmployees ? Employee::where('employment_status', 'PNS')->count() : 0;
        $pppkCount = $hasEmployees ? Employee::where('employment_status', 'like', '%PPPK%')->count() : 0;
        $honorerCount = $hasEmployees ? Employee::whereIn('employment_status', ['GTT', 'PTT', 'Honorer', 'NON ASN'])->count() : 0;

        // 3. Kontrol Kenaikan Pangkat (KP) & KGB & PPPK
        $threeMonths = now()->addMonths(3);
        $kpDueEmployees = $hasEmployees ? Employee::where('employment_status', 'PNS')
            ->whereNotNull('next_kp_tmt')
            ->where('next_kp_tmt', '<=', $threeMonths)
            ->get() : collect();

        $kgbDueEmployees = $hasEmployees ? Employee::whereIn('employment_status', ['PNS', 'PPPK'])
            ->whereNotNull('next_kgb_tmt')
            ->where('next_kgb_tmt', '<=', $threeMonths)
            ->get() : collect();

        $pppkDueEmployees = $hasEmployees ? Employee::where('employment_status', 'PPPK')
            ->whereNotNull('pppk_contract_end')
            ->where('pppk_contract_end', '<=', now()->addMonths(6))
            ->get() : collect();

        // 4. Cuti Pegawai Aktif
        $today = now()->toDateString();
        $activeLeaves = ($hasLeaves && $hasEmployees) ? EmployeeLeave::with('employee')
            ->where('status', 'Disetujui')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get() : collect();

        // 5. Daftar Pegawai dengan Filter
        $employees = new LengthAwarePaginator([], 0, 15);
        if ($hasEmployees) {
            $query = Employee::with('user')->orderBy('name');

            if ($request->filled('type')) {
                $type = $request->type;
                if ($type === 'Tendik' || $type === 'Tenaga Kependidikan') {
                    $query->where('employment_type', '!=', 'Guru');
                } elseif ($type === 'Guru') {
                    $query->where('employment_type', 'Guru');
                }
            }

            if ($request->filled('status')) {
                $status = $request->status;
                if ($status === 'PPPK') {
                    $query->where('employment_status', 'like', '%PPPK%');
                } elseif ($status === 'Honorer') {
                    $query->whereIn('employment_status', ['GTT', 'PTT', 'Honorer', 'NON ASN']);
                } else {
                    $query->where('employment_status', $status);
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('nuptk', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%");
                });
            }

            $employees = $query->paginate(15)->withQueryString();
        }

        return view('tu.kepegawaian.index', compact(
            'totalEmployees',
            'totalTeachers',
            'totalStaff',
            'pnsCount',
            'pppkCount',
            'honorerCount',
            'kpDueEmployees',
            'kgbDueEmployees',
            'pppkDueEmployees',
            'activeLeaves',
            'employees',
            'masterAvailableCount'
        ));
    }

    /**
     * Tarik data Guru & PTK dari Master Data ke Administrasi Kepegawaian
     */
    public function pullFromMaster(Request $request): RedirectResponse
    {
        $this->authorizeKepegawaian();

        try {
            Artisan::call('app:sync-excel-guru-ptk');
            $count = Employee::count();
        } catch (\Throwable $e) {
            $teachers = Teacher::where('is_active', true)->get();
            $count = 0;

            foreach ($teachers as $t) {
                $isGuru = ! in_array(trim($t->title ?? ''), [
                    'Kepala Tata Usaha',
                    'Staf Tata Usaha',
                    'Tenaga Administrasi (TU)',
                    'Koordinator IT & Sistem Informasi',
                    'Pengelola SIAKAD & IT Sekolah',
                    'Kepala Perpustakaan Sekolah',
                    'Pengelola Sarpras',
                    'Bendahara BOSP',
                ]);

                $nip = trim($t->nip ?? '');
                $status = 'PNS';
                if (empty($nip)) {
                    $status = 'Honorer';
                } elseif (str_contains($nip, '202221') || str_contains($nip, '202321') || str_contains($nip, '202421') || str_contains($nip, '202521') || strlen($nip) == 21) {
                    $status = 'PPPK';
                }

                Employee::updateOrCreate(
                    ['user_id' => $t->user_id],
                    [
                        'nip' => $t->nip,
                        'nuptk' => $t->nuptk,
                        'nik' => $t->nik,
                        'name' => $t->name,
                        'title_prefix' => $t->title_prefix,
                        'title_suffix' => $t->title_suffix,
                        'gender' => $t->gender ?? 'L',
                        'birth_place' => $t->birth_place,
                        'birth_date' => $t->birth_date,
                        'employment_type' => $t->employment_type ?: ($isGuru ? 'Guru' : 'Tenaga Kependidikan'),
                        'employment_status' => $t->employment_status ?: $status,
                        'position' => $t->position ?: ($t->title ?: ($isGuru ? 'Guru Mata Pelajaran' : 'Staf Tata Usaha')),
                        'rank_group' => $t->rank_group ?: ($status === 'PNS' ? 'IV/a' : ($status === 'PPPK' ? 'IX' : 'Honorer')),
                        'rank_tmt' => $t->rank_tmt,
                        'position_tmt' => $t->position_tmt,
                        'years_of_service_years' => (int) ($t->years_of_service_years ?? 0),
                        'years_of_service_months' => (int) ($t->years_of_service_months ?? 0),
                        'education_level' => $t->education_level ?: 'S1',
                        'education_institution' => $t->education_institution,
                        'education_major' => $t->education_major,
                        'education_grad_year' => $t->education_grad_year,
                        'teaching_subject' => $t->teaching_subject,
                        'additional_tasks' => $t->additional_tasks,
                        'phone' => $t->phone,
                        'address' => $t->address,
                        'is_active' => true,
                        'leave_balance' => 12,
                    ]
                );
                $count++;
            }
        }

        return redirect()->route('tu.kepegawaian.index')
            ->with('success', "Berhasil menarik {$count} data Guru & PTK dari Master Data ke Administrasi Kepegawaian!");
    }

    /**
     * Kosongkan data di Administrasi Kepegawaian
     */
    public function clearData(Request $request): RedirectResponse
    {
        $this->authorizeKepegawaian();

        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            EmployeeLeave::truncate();
            Employee::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            EmployeeLeave::query()->delete();
            Employee::query()->delete();
        }

        return redirect()->route('tu.kepegawaian.index')
            ->with('success', 'Data Administrasi Kepegawaian berhasil dikosongkan. Data master guru & PTK tetap aman dan utuh.');
    }

    public static function ensureDefaultEmployees(): void
    {
        if (! Schema::hasTable('employees') || Employee::count() > 0) {
            return;
        }

        $teachers = Teacher::where('is_active', true)->get();
        if ($teachers->isEmpty()) {
            return;
        }

        foreach ($teachers as $t) {
            $isGuru = ! in_array(trim($t->title ?? ''), [
                'Kepala Tata Usaha',
                'Staf Tata Usaha',
                'Tenaga Administrasi (TU)',
                'Koordinator IT & Sistem Informasi',
                'Pengelola SIAKAD & IT Sekolah',
                'Kepala Perpustakaan Sekolah',
                'Pengelola Sarpras',
                'Bendahara BOSP',
            ]);

            $nip = trim($t->nip ?? '');
            $status = 'PNS';
            if (empty($nip)) {
                $status = 'Honorer';
            } elseif (str_contains($nip, '202221') || str_contains($nip, '202321') || str_contains($nip, '202421') || str_contains($nip, '202521') || strlen($nip) == 21) {
                $status = 'PPPK';
            }

            Employee::firstOrCreate(
                ['user_id' => $t->user_id],
                [
                    'nip' => $t->nip,
                    'nuptk' => $t->nuptk,
                    'nik' => $t->nik,
                    'name' => $t->name,
                    'title_prefix' => $t->title_prefix,
                    'title_suffix' => $t->title_suffix,
                    'gender' => $t->gender ?? 'L',
                    'birth_place' => $t->birth_place,
                    'birth_date' => $t->birth_date,
                    'employment_type' => $t->employment_type ?: ($isGuru ? 'Guru' : 'Tenaga Kependidikan'),
                    'employment_status' => $t->employment_status ?: $status,
                    'position' => $t->position ?: ($t->title ?: ($isGuru ? 'Guru Mata Pelajaran' : 'Staf Tata Usaha')),
                    'rank_group' => $t->rank_group ?: ($status === 'PNS' ? 'IV/a' : ($status === 'PPPK' ? 'IX' : 'Honorer')),
                    'rank_tmt' => $t->rank_tmt,
                    'position_tmt' => $t->position_tmt,
                    'years_of_service_years' => (int) ($t->years_of_service_years ?? 0),
                    'years_of_service_months' => (int) ($t->years_of_service_months ?? 0),
                    'education_level' => $t->education_level ?: 'S1',
                    'education_institution' => $t->education_institution,
                    'education_major' => $t->education_major,
                    'education_grad_year' => $t->education_grad_year,
                    'teaching_subject' => $t->teaching_subject,
                    'additional_tasks' => $t->additional_tasks,
                    'phone' => $t->phone,
                    'address' => $t->address,
                    'is_active' => true,
                    'leave_balance' => 12,
                ]
            );
        }
    }
}
