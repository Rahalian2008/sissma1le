<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubsidiaryBookController extends Controller
{
    /**
     * Tampilan Buku Pembantu Kas (Tunai) dan Buku Pembantu Bank
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'bank'); // 'bank' or 'cash'
        $month = $request->query('month', (string) date('m'));
        $year = $request->query('year', (string) date('Y'));
        $source = $request->query('source', 'all');

        $query = CashTransaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($source !== 'all') {
            $query->where('funding_source', $source);
        }

        if ($tab === 'cash') {
            // Kas Tunai
            $query->where('payment_method', 'Tunai');
        } else {
            // Kas Bank (Transfer Bank / Non Tunai)
            $query->where('payment_method', '!=', 'Tunai');
        }

        $transactions = $query->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');
        $netBalance = $totalDebit - $totalCredit;

        // Hitung juga statistik ringkas kedua buku
        $cashTotalDebit = CashTransaction::whereYear('transaction_date', $year)->whereMonth('transaction_date', $month)->where('payment_method', 'Tunai')->sum('debit');
        $cashTotalCredit = CashTransaction::whereYear('transaction_date', $year)->whereMonth('transaction_date', $month)->where('payment_method', 'Tunai')->sum('credit');
        $cashBalance = $cashTotalDebit - $cashTotalCredit;

        $bankTotalDebit = CashTransaction::whereYear('transaction_date', $year)->whereMonth('transaction_date', $month)->where('payment_method', '!=', 'Tunai')->sum('debit');
        $bankTotalCredit = CashTransaction::whereYear('transaction_date', $year)->whereMonth('transaction_date', $month)->where('payment_method', '!=', 'Tunai')->sum('credit');
        $bankBalance = $bankTotalDebit - $bankTotalCredit;

        return view('finance.subsidiary.index', compact(
            'transactions',
            'tab',
            'month',
            'year',
            'source',
            'totalDebit',
            'totalCredit',
            'netBalance',
            'cashBalance',
            'bankBalance'
        ));
    }

    /**
     * Cetak Buku Pembantu Kas Tunai atau Bank
     */
    public function print(Request $request): View
    {
        $type = $request->query('type', 'bank'); // 'cash' or 'bank'
        $month = $request->query('month', (string) date('m'));
        $year = $request->query('year', (string) date('Y'));
        $source = $request->query('source', 'all');

        $query = CashTransaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($source !== 'all') {
            $query->where('funding_source', $source);
        }

        if ($type === 'cash') {
            $query->where('payment_method', 'Tunai');
            $bookTitle = 'BUKU PEMBANTU KAS (TUNAI)';
        } else {
            $query->where('payment_method', '!=', 'Tunai');
            $bookTitle = 'BUKU PEMBANTU BANK (NON-TUNAI)';
        }

        $transactions = $query->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');
        $closingBalance = $totalDebit - $totalCredit;

        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'keuangan', "Mencetak {$bookTitle} Bulan {$month}/{$year}");

        return view('finance.subsidiary.print', compact(
            'transactions',
            'type',
            'bookTitle',
            'month',
            'year',
            'source',
            'totalDebit',
            'totalCredit',
            'closingBalance',
            'school'
        ));
    }
}
