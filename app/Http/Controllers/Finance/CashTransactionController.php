<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Asset;
use App\Models\BudgetItem;
use App\Models\CashTransaction;
use App\Models\GoodsReceipt;
use App\Models\SchoolIdentity;
use App\Models\TaxRecord;
use App\Services\AuditTrailService;
use App\Services\BkuLedgerService;
use App\Services\NumberHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = CashTransaction::with(['budgetItem', 'taxes', 'goodsReceipt', 'asset']);

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($source = $request->query('source')) {
            $query->where('funding_source', $source);
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(15)->withQueryString();
        $budgetItems = BudgetItem::orderBy('activity_name')->get();
        $goodsReceipts = GoodsReceipt::with('receiver')->latest('receipt_date')->take(50)->get();
        $assets = Asset::orderBy('name')->take(50)->get();

        return view('finance.transactions.index', compact('transactions', 'budgetItems', 'goodsReceipts', 'assets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'type' => ['required', 'in:Penerimaan,Pengeluaran'],
            'funding_source' => ['required', 'string'],
            'budget_item_id' => ['nullable', 'exists:budget_items,id'],
            'account_code' => ['nullable', 'string', 'max:50'],
            'goods_receipt_id' => ['nullable', 'exists:goods_receipts,id'],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'spk_number' => ['nullable', 'string', 'max:100'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'recipient_or_payer' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string'],
            'has_tax' => ['nullable', 'boolean'],
            'tax_type' => ['nullable', 'string'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $year = date('Y', strtotime($validated['transaction_date']));
            $count = CashTransaction::whereYear('transaction_date', $year)->count() + 1;
            $evidenceNumber = "BKU/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);

            $debit = $validated['type'] === 'Penerimaan' ? $validated['amount'] : 0;
            $credit = $validated['type'] === 'Pengeluaran' ? $validated['amount'] : 0;

            $budgetItem = null;
            if (! empty($validated['budget_item_id'])) {
                $budgetItem = BudgetItem::find($validated['budget_item_id']);
            }

            $trx = CashTransaction::create([
                'evidence_number' => $evidenceNumber,
                'transaction_date' => $validated['transaction_date'],
                'type' => $validated['type'],
                'funding_source' => $validated['funding_source'],
                'budget_item_id' => $budgetItem?->id,
                'goods_receipt_id' => $validated['goods_receipt_id'] ?? null,
                'asset_id' => $validated['asset_id'] ?? null,
                'spk_number' => $validated['spk_number'] ?? null,
                'invoice_number' => $validated['invoice_number'] ?? null,
                'account_code' => $validated['account_code'] ?? $budgetItem?->account_code,
                'description' => $validated['description'],
                'recipient_or_payer' => $validated['recipient_or_payer'],
                'debit' => $debit,
                'credit' => $credit,
                'running_balance' => 0,
                'payment_method' => $validated['payment_method'],
                'is_verified' => true,
                'verified_by_user_id' => Auth::id(),
            ]);

            // If expenditure links to budget item, increment realized amount
            if ($budgetItem && $credit > 0) {
                $budgetItem->increment('realized_amount', $credit);
            }

            // If tax specified
            if ($request->boolean('has_tax') && ! empty($validated['tax_amount']) && $validated['tax_amount'] > 0) {
                TaxRecord::create([
                    'cash_transaction_id' => $trx->id,
                    'tax_type' => $validated['tax_type'] ?? 'PPN',
                    'tax_base' => $validated['amount'],
                    'rate_percentage' => round(($validated['tax_amount'] / $validated['amount']) * 100, 2),
                    'tax_amount' => $validated['tax_amount'],
                    'status' => 'Dipotong',
                ]);
            }

            // Recalculate BKU ledger balances
            BkuLedgerService::recalculateRunningBalances();

            AuditTrailService::log('create', 'keuangan', "Transaksi BKU [{$trx->type}]: Rp ".number_format($validated['amount'], 0, ',', '.')." - {$trx->description}");
        });

        return back()->with('success', 'Transaksi kas berhasil dibukukan.');
    }

    public function update(Request $request, CashTransaction $cashTransaction): RedirectResponse
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'type' => ['required', 'in:Penerimaan,Pengeluaran'],
            'funding_source' => ['required', 'string'],
            'budget_item_id' => ['nullable', 'exists:budget_items,id'],
            'account_code' => ['nullable', 'string', 'max:50'],
            'goods_receipt_id' => ['nullable', 'exists:goods_receipts,id'],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'spk_number' => ['nullable', 'string', 'max:100'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'recipient_or_payer' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($validated, $cashTransaction) {
            // Revert old budget item realization
            if ($cashTransaction->budgetItem && $cashTransaction->credit > 0) {
                $cashTransaction->budgetItem->decrement('realized_amount', $cashTransaction->credit);
            }

            $debit = $validated['type'] === 'Penerimaan' ? $validated['amount'] : 0;
            $credit = $validated['type'] === 'Pengeluaran' ? $validated['amount'] : 0;

            $budgetItem = null;
            if (! empty($validated['budget_item_id'])) {
                $budgetItem = BudgetItem::find($validated['budget_item_id']);
            }

            $cashTransaction->update([
                'transaction_date' => $validated['transaction_date'],
                'type' => $validated['type'],
                'funding_source' => $validated['funding_source'],
                'budget_item_id' => $budgetItem?->id,
                'goods_receipt_id' => $validated['goods_receipt_id'] ?? null,
                'asset_id' => $validated['asset_id'] ?? null,
                'spk_number' => $validated['spk_number'] ?? null,
                'invoice_number' => $validated['invoice_number'] ?? null,
                'account_code' => $validated['account_code'] ?? $budgetItem?->account_code,
                'description' => $validated['description'],
                'recipient_or_payer' => $validated['recipient_or_payer'],
                'debit' => $debit,
                'credit' => $credit,
                'payment_method' => $validated['payment_method'],
            ]);

            // Add new budget item realization
            if ($budgetItem && $credit > 0) {
                $budgetItem->increment('realized_amount', $credit);
            }

            // Recalculate BKU ledger balances
            BkuLedgerService::recalculateRunningBalances();

            AuditTrailService::log('update', 'keuangan', "Memperbarui transaksi BKU [{$cashTransaction->evidence_number}]: Rp ".number_format($validated['amount'], 0, ',', '.')." - {$cashTransaction->description}");
        });

        return back()->with('success', 'Transaksi kas berhasil diperbarui.');
    }

    /**
     * Cetak Kuitansi Resmi / Bukti Pengeluaran Kas (SPJ)
     */
    public function printReceipt(CashTransaction $cashTransaction): View
    {
        $cashTransaction->load(['budgetItem', 'taxes', 'goodsReceipt.items.good', 'asset', 'verifier']);
        $school = SchoolIdentity::current();

        $amount = $cashTransaction->type === 'Penerimaan' ? (float) $cashTransaction->debit : (float) $cashTransaction->credit;
        $terbilang = NumberHelper::terbilang($amount).' Rupiah';

        $totalTax = (float) $cashTransaction->taxes->sum('tax_amount');
        $netAmount = $amount - $totalTax;
        $netTerbilang = NumberHelper::terbilang($netAmount).' Rupiah';

        AuditTrailService::log('print', 'keuangan', "Mencetak Kuitansi SPJ Kas: {$cashTransaction->evidence_number}");

        return view('finance.transactions.receipt_print', compact(
            'cashTransaction',
            'school',
            'amount',
            'terbilang',
            'totalTax',
            'netAmount',
            'netTerbilang'
        ));
    }

    /**
     * Arsipkan Kuitansi SPJ ke Modul Kearsipan Dokumen Digital
     */
    public function archiveReceipt(CashTransaction $cashTransaction): RedirectResponse
    {
        $existing = Archive::where('source_type', 'cash_transaction')
            ->where('source_id', $cashTransaction->id)
            ->first();

        if ($existing) {
            return back()->with('info', "Kuitansi ini sudah diarsipkan ke Kearsipan Digital dengan No. {$existing->archive_number}");
        }

        $year = date('Y', strtotime($cashTransaction->transaction_date));
        $count = Archive::where('year', $year)->count() + 1;
        $archiveNumber = "ARS/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        $archive = Archive::create([
            'archive_number' => $archiveNumber,
            'title' => "Kuitansi & SPJ Kas: {$cashTransaction->evidence_number} ({$cashTransaction->recipient_or_payer})",
            'category' => 'Keuangan',
            'classification_code' => '900', // Kode Keuangan Satuan Pendidikan
            'origin_unit' => 'Bendahara Pengeluaran',
            'year' => $year,
            'archive_status' => 'Aktif',
            'retention_period_years' => 10,
            'retention_expire_date' => now()->addYears(10)->toDateString(),
            'location_rack' => 'Lemari Kuitansi & SPJ Keuangan',
            'source_type' => 'cash_transaction',
            'source_id' => $cashTransaction->id,
            'notes' => "Diarsipkan otomatis dari BKU No. {$cashTransaction->evidence_number} - Sumber: {$cashTransaction->funding_source} - Rp ".number_format($cashTransaction->credit > 0 ? $cashTransaction->credit : $cashTransaction->debit, 0, ',', '.'),
        ]);

        AuditTrailService::log('create', 'kearsipan', "Mengarsipkan Kuitansi SPJ {$cashTransaction->evidence_number} ke No. Arsip {$archive->archive_number}");

        return back()->with('success', "Kuitansi SPJ berhasil disimpan ke Kearsipan Digital dengan No. {$archive->archive_number}");
    }

    public function destroy(CashTransaction $cashTransaction): RedirectResponse
    {
        DB::transaction(function () use ($cashTransaction) {
            if ($cashTransaction->budgetItem && $cashTransaction->credit > 0) {
                $cashTransaction->budgetItem->decrement('realized_amount', $cashTransaction->credit);
            }

            $desc = $cashTransaction->description;
            $cashTransaction->delete();

            BkuLedgerService::recalculateRunningBalances();

            AuditTrailService::log('delete', 'keuangan', "Menghapus transaksi BKU: {$desc}");
        });

        return back()->with('success', 'Transaksi berhasil dihapus dan saldo kas diperbarui.');
    }
}
