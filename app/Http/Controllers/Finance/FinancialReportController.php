<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Budget;
use App\Models\CashTransaction;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use App\Services\NumberHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialReportController extends Controller
{
    public function index(Request $request): View
    {
        $year = $request->query('year', (string) date('Y'));

        $budgets = Budget::with('items')->where('fiscal_year', $year)->get();
        $totalAllocated = $budgets->sum('total_allocation');
        $totalRealized = $budgets->flatMap->items->sum('realized_amount');
        $remainingBudget = $totalAllocated - $totalRealized;

        $incomeTotal = CashTransaction::whereYear('transaction_date', $year)->sum('debit');
        $expenseTotal = CashTransaction::whereYear('transaction_date', $year)->sum('credit');

        return view('finance.reports.index', compact('budgets', 'year', 'totalAllocated', 'totalRealized', 'remainingBudget', 'incomeTotal', 'expenseTotal'));
    }

    public function print(Request $request): View
    {
        $year = $request->query('year', (string) date('Y'));
        $budgets = Budget::with('items')->where('fiscal_year', $year)->get();
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'keuangan', "Mencetak Laporan Realisasi Anggaran TA {$year}");

        return view('finance.reports.print', compact('budgets', 'year', 'school'));
    }

    /**
     * Dashboard Rekonsiliasi & Pemeriksaan Kas Bulanan
     */
    public function reconciliation(Request $request): View
    {
        $month = $request->query('month', (string) date('m'));
        $year = $request->query('year', (string) date('Y'));
        $source = $request->query('source', 'all');

        $query = CashTransaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($source !== 'all') {
            $query->where('funding_source', $source);
        }

        $transactions = $query->orderBy('transaction_date', 'asc')->get();

        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');
        $bkuBalance = $totalDebit - $totalCredit;

        // Saldo Tunai & Saldo Bank
        $cashDebit = $transactions->where('payment_method', 'Tunai')->sum('debit');
        $cashCredit = $transactions->where('payment_method', 'Tunai')->sum('credit');
        $cashBalance = $cashDebit - $cashCredit;

        $bankDebit = $transactions->where('payment_method', '!=', 'Tunai')->sum('debit');
        $bankCredit = $transactions->where('payment_method', '!=', 'Tunai')->sum('credit');
        $bankBalance = $bankDebit - $bankCredit;

        $calculatedTotal = $cashBalance + $bankBalance;
        $difference = $bkuBalance - $calculatedTotal;

        return view('finance.reports.reconciliation', compact(
            'transactions',
            'month',
            'year',
            'source',
            'totalDebit',
            'totalCredit',
            'bkuBalance',
            'cashBalance',
            'bankBalance',
            'calculatedTotal',
            'difference'
        ));
    }

    /**
     * Cetak Berita Acara Pemeriksaan Kas & Penutupan BKU Bulanan
     */
    public function printReconciliation(Request $request): View
    {
        $month = $request->query('month', (string) date('m'));
        $year = $request->query('year', (string) date('Y'));
        $source = $request->query('source', 'all');

        $query = CashTransaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($source !== 'all') {
            $query->where('funding_source', $source);
        }

        $transactions = $query->orderBy('transaction_date', 'asc')->get();

        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');
        $bkuBalance = $totalDebit - $totalCredit;

        $cashDebit = $transactions->where('payment_method', 'Tunai')->sum('debit');
        $cashCredit = $transactions->where('payment_method', 'Tunai')->sum('credit');
        $cashBalance = $cashDebit - $cashCredit;

        $bankDebit = $transactions->where('payment_method', '!=', 'Tunai')->sum('debit');
        $bankCredit = $transactions->where('payment_method', '!=', 'Tunai')->sum('credit');
        $bankBalance = $bankDebit - $bankCredit;

        $terbilangBku = NumberHelper::terbilang($bkuBalance).' Rupiah';
        $terbilangCash = NumberHelper::terbilang($cashBalance).' Rupiah';
        $terbilangBank = NumberHelper::terbilang($bankBalance).' Rupiah';

        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'keuangan', "Mencetak Berita Acara Pemeriksaan Kas & Penutupan BKU Bulan {$month}/{$year}");

        return view('finance.reports.reconciliation_print', compact(
            'month',
            'year',
            'source',
            'totalDebit',
            'totalCredit',
            'bkuBalance',
            'cashBalance',
            'bankBalance',
            'terbilangBku',
            'terbilangCash',
            'terbilangBank',
            'school'
        ));
    }

    /**
     * Arsipkan Berita Acara Rekonsiliasi Kas ke Modul Kearsipan Digital
     */
    public function archiveReconciliation(Request $request): RedirectResponse
    {
        $month = $request->input('month', (string) date('m'));
        $year = $request->input('year', (string) date('Y'));
        $monthName = \DateTime::createFromFormat('!m', (int) $month)->format('F');

        $existing = Archive::where('source_type', 'cash_reconciliation')
            ->where('notes', 'LIKE', "%Bulan {$month}/{$year}%")
            ->first();

        if ($existing) {
            return back()->with('info', "Berita Acara Rekonsiliasi Kas Bulan {$monthName} {$year} sudah diarsipkan dengan No. {$existing->archive_number}");
        }

        $count = Archive::where('year', $year)->count() + 1;
        $archiveNumber = "ARS/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        $archive = Archive::create([
            'archive_number' => $archiveNumber,
            'title' => "Berita Acara Pemeriksaan Kas & Penutupan BKU - {$monthName} {$year}",
            'category' => 'Keuangan',
            'classification_code' => '900',
            'origin_unit' => 'Bendahara & Kepala Sekolah',
            'year' => $year,
            'archive_status' => 'Aktif',
            'retention_period_years' => 10,
            'retention_expire_date' => now()->addYears(10)->toDateString(),
            'location_rack' => 'Lemari Arsip BKU & SPJ Keuangan',
            'source_type' => 'cash_reconciliation',
            'notes' => "Diarsipkan otomatis dari Register Penutupan Kas Bulan {$month}/{$year}",
        ]);

        AuditTrailService::log('create', 'kearsipan', "Mengarsipkan Berita Acara Rekonsiliasi Kas {$monthName} {$year} ke No. Arsip {$archive->archive_number}");

        return back()->with('success', "Berita Acara Rekonsiliasi Kas berhasil disimpan ke Kearsipan Digital dengan No. {$archive->archive_number}");
    }
}
