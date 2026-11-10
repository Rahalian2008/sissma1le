<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $fiscalYear = $request->query('year', (string) date('Y'));

        $budgets = Budget::with('items')
            ->where('fiscal_year', $fiscalYear)
            ->get();

        $availableYears = Budget::select('fiscal_year')->distinct()->pluck('fiscal_year')->toArray();
        if (! in_array((int) date('Y'), $availableYears)) {
            $availableYears[] = (int) date('Y');
        }
        rsort($availableYears);

        return view('finance.budgets.index', compact('budgets', 'fiscalYear', 'availableYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fiscal_year' => ['required', 'integer', 'min:2020'],
            'funding_source' => ['required', 'string'],
            'total_allocation' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_approved'] = true;
        $budget = Budget::create($validated);

        AuditTrailService::log('create', 'keuangan', "Menetapkan pagu anggaran: {$budget->funding_source} ({$budget->fiscal_year}) sebesar Rp ".number_format($budget->total_allocation, 0, ',', '.'));

        return back()->with('success', 'Rencana anggaran berhasil ditetapkan.');
    }

    public function storeItem(Request $request, Budget $budget): RedirectResponse
    {
        $validated = $request->validate([
            'account_code' => ['nullable', 'string', 'max:50'],
            'program_name' => ['required', 'string', 'max:255'],
            'activity_name' => ['required', 'string', 'max:255'],
            'allocated_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['budget_id'] = $budget->id;
        $validated['realized_amount'] = 0;

        $item = BudgetItem::create($validated);

        AuditTrailService::log('create', 'keuangan', "Menambah mata kegiatan anggaran [{$item->activity_name}] sebesar Rp ".number_format($item->allocated_amount, 0, ',', '.'));

        return back()->with('success', 'Komponen kegiatan anggaran berhasil ditambahkan.');
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        $validated = $request->validate([
            'fiscal_year' => ['required', 'integer', 'min:2020'],
            'funding_source' => ['required', 'string'],
            'total_allocation' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $budget->update($validated);

        AuditTrailService::log('update', 'keuangan', "Memperbarui pagu anggaran: {$budget->funding_source} ({$budget->fiscal_year})");

        return back()->with('success', 'Pagu anggaran berhasil diperbarui.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $funding = $budget->funding_source;
        $year = $budget->fiscal_year;

        $budget->items()->delete();
        $budget->delete();

        AuditTrailService::log('delete', 'keuangan', "Menghapus pagu anggaran {$funding} ({$year}) beserta seluruh rincian kegiatannya");

        return back()->with('success', 'Pagu anggaran dan komponen kegiatannya berhasil dihapus.');
    }

    public function updateItem(Request $request, BudgetItem $budgetItem): RedirectResponse
    {
        $validated = $request->validate([
            'account_code' => ['nullable', 'string', 'max:50'],
            'program_name' => ['required', 'string', 'max:255'],
            'activity_name' => ['required', 'string', 'max:255'],
            'allocated_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $budgetItem->update($validated);

        AuditTrailService::log('update', 'keuangan', "Memperbarui mata kegiatan anggaran: {$budgetItem->activity_name}");

        return back()->with('success', 'Rincian kegiatan anggaran berhasil diperbarui.');
    }

    public function destroyItem(BudgetItem $budgetItem): RedirectResponse
    {
        $name = $budgetItem->activity_name;
        $budgetItem->delete();

        AuditTrailService::log('delete', 'keuangan', "Menghapus rincian kegiatan anggaran: {$name}");

        return back()->with('success', 'Rincian kegiatan anggaran berhasil dihapus.');
    }
}
