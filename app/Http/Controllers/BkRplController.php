<?php

namespace App\Http\Controllers;

use App\Models\BkRpl;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkRplController extends Controller
{
    public function index(Request $request): View
    {
        $query = BkRpl::with('counselor')->latest();

        if ($request->filled('component')) {
            $query->where('service_component', $request->component);
        }

        if ($request->filled('field')) {
            $query->where('field_of_service', $request->field);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                    ->orWhere('rpl_code', 'like', "%{$search}%")
                    ->orWhere('learning_objectives', 'like', "%{$search}%");
            });
        }

        $rpls = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => BkRpl::count(),
            'dasar' => BkRpl::where('service_component', 'LAYANAN_DASAR')->count(),
            'responsif' => BkRpl::where('service_component', 'LAYANAN_RESPONSIF')->count(),
            'peminatan' => BkRpl::where('service_component', 'PERENCANAAN_INDIVIDUAL')->count(),
            'dukungan' => BkRpl::where('service_component', 'DUKUNGAN_SISTEM')->count(),
        ];

        return view('bk.rpl.index', compact('rpls', 'stats'));
    }

    public function create(): View
    {
        return view('bk.rpl.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'service_component' => ['required', 'in:LAYANAN_DASAR,LAYANAN_RESPONSIF,PERENCANAAN_INDIVIDUAL,DUKUNGAN_SISTEM'],
            'service_type' => ['required', 'in:KLASIKAL,KELOMPOK,INDIVIDUAL,KOLABORASI'],
            'field_of_service' => ['required', 'in:PRIBADI,SOSIAL,BELAJAR,KARIER'],
            'grade_target' => ['required', 'string', 'max:50'],
            'semester' => ['required', 'string', 'max:20'],
            'time_allocation' => ['required', 'string', 'max:50'],
            'profile_pelajar_pancasila' => ['nullable', 'string', 'max:255'],
            'learning_objectives' => ['required', 'string'],
            'materials_summary' => ['nullable', 'string'],
            'method_and_media' => ['nullable', 'string'],
            'step_introduction' => ['nullable', 'string'],
            'step_core' => ['nullable', 'string'],
            'step_closing' => ['nullable', 'string'],
            'evaluation_process' => ['nullable', 'string'],
            'evaluation_result' => ['nullable', 'string'],
        ]);

        $year = date('Y');
        $count = BkRpl::whereYear('created_at', $year)->count() + 1;
        $validated['rpl_code'] = sprintf('RPL-BK-%s-%03d', $year, $count);
        $validated['counselor_id'] = Auth::id();
        $validated['status'] = 'DISETUJUI';

        $rpl = BkRpl::create($validated);

        AuditTrailService::log('create', 'rpl_bk', "Membuat RPL BK Kurikulum Merdeka 1 Lembar: {$rpl->topic} ({$rpl->rpl_code})");

        return redirect()->route('bk.rpl.show', $rpl)->with('success', "RPL BK 1 Lembar '{$rpl->topic}' berhasil disusun.");
    }

    public function show(BkRpl $rpl): View
    {
        $school = SchoolIdentity::current();

        return view('bk.rpl.show', compact('rpl', 'school'));
    }

    public function edit(BkRpl $rpl): View
    {
        return view('bk.rpl.edit', compact('rpl'));
    }

    public function update(Request $request, BkRpl $rpl): RedirectResponse
    {
        $validated = $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'service_component' => ['required', 'in:LAYANAN_DASAR,LAYANAN_RESPONSIF,PERENCANAAN_INDIVIDUAL,DUKUNGAN_SISTEM'],
            'service_type' => ['required', 'in:KLASIKAL,KELOMPOK,INDIVIDUAL,KOLABORASI'],
            'field_of_service' => ['required', 'in:PRIBADI,SOSIAL,BELAJAR,KARIER'],
            'grade_target' => ['required', 'string', 'max:50'],
            'semester' => ['required', 'string', 'max:20'],
            'time_allocation' => ['required', 'string', 'max:50'],
            'profile_pelajar_pancasila' => ['nullable', 'string', 'max:255'],
            'learning_objectives' => ['required', 'string'],
            'materials_summary' => ['nullable', 'string'],
            'method_and_media' => ['nullable', 'string'],
            'step_introduction' => ['nullable', 'string'],
            'step_core' => ['nullable', 'string'],
            'step_closing' => ['nullable', 'string'],
            'evaluation_process' => ['nullable', 'string'],
            'evaluation_result' => ['nullable', 'string'],
        ]);

        $rpl->update($validated);

        AuditTrailService::log('update', 'rpl_bk', "Memperbarui RPL BK ID #{$rpl->id}: {$rpl->topic}");

        return redirect()->route('bk.rpl.show', $rpl)->with('success', 'RPL BK berhasil diperbarui.');
    }

    public function destroy(BkRpl $rpl): RedirectResponse
    {
        $topic = $rpl->topic;
        $rpl->delete();

        AuditTrailService::log('delete', 'rpl_bk', "Menghapus RPL BK {$topic}");

        return redirect()->route('bk.rpl.index')->with('success', 'RPL BK berhasil dihapus.');
    }

    public function print(BkRpl $rpl): View
    {
        $school = SchoolIdentity::current();
        AuditTrailService::log('print', 'rpl_bk', "Mencetak RPL BK 1 Lembar Resmi: {$rpl->topic}");

        return view('bk.rpl.print', compact('rpl', 'school'));
    }
}
