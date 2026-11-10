<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $years = AcademicYear::latest()->get();

        return view('master.academic_year.index', compact('years'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'string', 'max:20'],
            'semester' => ['required', 'string', 'in:Ganjil,Genap'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $isActive = $request->boolean('is_active');

        if ($isActive) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $validated['is_active'] = $isActive;
        $academicYear = AcademicYear::create($validated);

        AuditTrailService::log('create', 'tahun_pelajaran', 'Menambah tahun pelajaran: '.$academicYear->year.' - '.$academicYear->semester);

        return back()->with('success', 'Tahun pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'string', 'max:20'],
            'semester' => ['required', 'string', 'in:Ganjil,Genap'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $academicYear->update($validated);

        AuditTrailService::log('update', 'tahun_pelajaran', 'Memperbarui tahun pelajaran: '.$academicYear->year.' - '.$academicYear->semester);

        return back()->with('success', 'Tahun pelajaran berhasil diperbarui.');
    }

    public function setActive(AcademicYear $academicYear): RedirectResponse
    {
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        AuditTrailService::log('update', 'tahun_pelajaran', 'Mengaktifkan tahun pelajaran: '.$academicYear->year.' - '.$academicYear->semester);

        return back()->with('success', 'Tahun pelajaran '.$academicYear->year.' '.$academicYear->semester.' aktif.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $info = $academicYear->year.' '.$academicYear->semester;
        $academicYear->delete();

        AuditTrailService::log('delete', 'tahun_pelajaran', 'Menghapus tahun pelajaran: '.$info);

        return back()->with('success', 'Tahun pelajaran berhasil dihapus.');
    }
}
