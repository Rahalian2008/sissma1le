<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use App\Models\SchoolIdentity;
use App\Models\TaxRecord;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxController extends Controller
{
    public function index(): View
    {
        $taxes = TaxRecord::with('transaction')->latest()->paginate(15);
        $transactions = CashTransaction::where('type', 'Pengeluaran')->latest()->take(20)->get();

        $totalTaxes = TaxRecord::sum('tax_amount');
        $depositedTaxes = TaxRecord::where('status', 'Disetor')->sum('tax_amount');
        $pendingTaxes = TaxRecord::where('status', 'Dipotong')->sum('tax_amount');

        return view('finance.taxes.index', compact('taxes', 'transactions', 'totalTaxes', 'depositedTaxes', 'pendingTaxes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cash_transaction_id' => ['nullable', 'exists:cash_transactions,id'],
            'tax_type' => ['required', 'string'],
            'tax_base' => ['required', 'numeric', 'min:0'],
            'rate_percentage' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['required', 'numeric', 'min:0'],
            'ntpn' => ['nullable', 'string', 'max:50'],
            'billing_code' => ['nullable', 'string', 'max:50'],
            'deposit_date' => ['nullable', 'date'],
            'status' => ['required', 'string'],
        ]);

        $tax = TaxRecord::create($validated);

        AuditTrailService::log('create', 'keuangan', "Mencatat kewajiban pajak [{$tax->tax_type}]: Rp ".number_format($tax->tax_amount, 0, ',', '.'));

        return back()->with('success', 'Catatan pajak berhasil disimpan.');
    }

    public function updateStatus(Request $request, TaxRecord $tax): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'ntpn' => ['nullable', 'string', 'max:50'],
            'deposit_date' => ['nullable', 'date'],
        ]);

        $tax->update($validated);

        AuditTrailService::log('update', 'keuangan', "Memperbarui status pajak [{$tax->tax_type}] -> {$tax->status} (NTPN: {$tax->ntpn})");

        return back()->with('success', 'Status setoran pajak berhasil diperbarui.');
    }

    public function update(Request $request, TaxRecord $tax): RedirectResponse
    {
        $validated = $request->validate([
            'cash_transaction_id' => ['nullable', 'exists:cash_transactions,id'],
            'tax_type' => ['required', 'string'],
            'tax_base' => ['required', 'numeric', 'min:0'],
            'rate_percentage' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['required', 'numeric', 'min:0'],
            'ntpn' => ['nullable', 'string', 'max:50'],
            'billing_code' => ['nullable', 'string', 'max:50'],
            'deposit_date' => ['nullable', 'date'],
            'status' => ['required', 'string'],
        ]);

        $tax->update($validated);

        AuditTrailService::log('update', 'keuangan', "Memperbarui catatan pajak [{$tax->tax_type}]: Rp ".number_format($tax->tax_amount, 0, ',', '.'));

        return back()->with('success', 'Catatan pajak berhasil diperbarui.');
    }

    public function destroy(TaxRecord $tax): RedirectResponse
    {
        $taxType = $tax->tax_type;
        $amount = $tax->tax_amount;
        $tax->delete();

        AuditTrailService::log('delete', 'keuangan', "Menghapus catatan pajak [{$taxType}]: Rp ".number_format($amount, 0, ',', '.'));

        return back()->with('success', 'Catatan pajak berhasil dihapus.');
    }

    /**
     * Cetak Buku Pembantu Pajak
     */
    public function print(Request $request): View
    {
        $year = $request->query('year', (string) date('Y'));
        $taxes = TaxRecord::with('transaction')
            ->whereHas('transaction', fn ($q) => $q->whereYear('transaction_date', $year))
            ->orWhereNull('cash_transaction_id')
            ->latest('id')
            ->get();

        $totalTaxes = $taxes->sum('tax_amount');
        $depositedTaxes = $taxes->where('status', 'Disetor')->sum('tax_amount');
        $pendingTaxes = $taxes->where('status', '!=', 'Disetor')->sum('tax_amount');

        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'keuangan', "Mencetak Buku Pembantu Pajak Tahun {$year}");

        return view('finance.taxes.print', compact('taxes', 'year', 'totalTaxes', 'depositedTaxes', 'pendingTaxes', 'school'));
    }
}
