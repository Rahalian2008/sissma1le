<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OrganizationStructure;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DsoController extends Controller
{
    public function index(): View
    {
        // Fetch top-level nodes (parents) with eager loaded recursive children
        $rootNodes = OrganizationStructure::whereNull('parent_id')
            ->with(['children.children.children', 'employee'])
            ->orderBy('sort_order')
            ->get();

        $allNodes = OrganizationStructure::with('employee')->orderBy('sort_order')->get();
        $employees = Employee::active()->orderBy('name')->get();

        return view('dso.index', compact('rootNodes', 'allNodes', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'position_name' => ['required', 'string', 'max:150'],
            'parent_id' => ['nullable', 'exists:organization_structures,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'officer_name' => ['nullable', 'string', 'max:150'],
            'officer_nip' => ['nullable', 'string', 'max:30'],
            'sk_number' => ['nullable', 'string', 'max:100'],
            'duties_and_functions' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if (! empty($validated['employee_id'])) {
            $employee = Employee::find($validated['employee_id']);
            if ($employee) {
                $validated['officer_name'] = $employee->formatted_name;
                $validated['officer_nip'] = $employee->nip;
            }
        }

        $node = OrganizationStructure::create($validated);

        AuditTrailService::log('create', 'dso', "Menambahkan node struktur organisasi: {$node->position_name}");

        return back()->with('success', 'Jabatan dalam struktur organisasi berhasil ditambahkan.');
    }

    public function update(Request $request, OrganizationStructure $dso): RedirectResponse
    {
        $validated = $request->validate([
            'position_name' => ['required', 'string', 'max:150'],
            'parent_id' => ['nullable', 'exists:organization_structures,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'officer_name' => ['nullable', 'string', 'max:150'],
            'officer_nip' => ['nullable', 'string', 'max:30'],
            'sk_number' => ['nullable', 'string', 'max:100'],
            'duties_and_functions' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if (! empty($validated['employee_id'])) {
            $employee = Employee::find($validated['employee_id']);
            if ($employee) {
                $validated['officer_name'] = $employee->formatted_name;
                $validated['officer_nip'] = $employee->nip;
            }
        }

        $dso->update($validated);

        AuditTrailService::log('update', 'dso', "Memperbarui jabatan struktur organisasi: {$dso->position_name}");

        return back()->with('success', 'Struktur organisasi berhasil diperbarui.');
    }

    public function destroy(OrganizationStructure $dso): RedirectResponse
    {
        $name = $dso->position_name;
        $dso->delete();

        AuditTrailService::log('delete', 'dso', "Menghapus jabatan struktur organisasi: {$name}");

        return back()->with('success', 'Jabatan berhasil dihapus dari bagan struktur.');
    }

    public function print(): View
    {
        $school = SchoolIdentity::current();
        $rootNodes = OrganizationStructure::whereNull('parent_id')
            ->with(['children.children.children', 'employee'])
            ->orderBy('sort_order')
            ->get();

        AuditTrailService::log('print', 'dso', 'Mencetak Bagan Struktur Organisasi (DSO)');

        return view('dso.print', compact('rootNodes', 'school'));
    }
}
