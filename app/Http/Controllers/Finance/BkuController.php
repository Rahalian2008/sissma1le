<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use App\Services\BkuLedgerService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BkuController extends Controller
{
    public function index(Request $request): View
    {
        $month = $request->query('month', (string) date('m'));
        $year = $request->query('year', (string) date('Y'));
        $source = $request->query('source', 'all');

        $query = CashTransaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($source !== 'all') {
            $query->where('funding_source', $source);
        }

        $transactions = $query->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');
        $netBalance = $totalDebit - $totalCredit;

        $metrics = BkuLedgerService::getSummaryMetrics((int) $year);

        return view('finance.bku.index', compact('transactions', 'month', 'year', 'source', 'totalDebit', 'totalCredit', 'netBalance', 'metrics'));
    }

    public function print(Request $request): View
    {
        $month = $request->query('month', (string) date('m'));
        $year = $request->query('year', (string) date('Y'));
        $source = $request->query('source', 'all');

        $query = CashTransaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($source !== 'all') {
            $query->where('funding_source', $source);
        }

        $transactions = $query->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');
        $closingBalance = $totalDebit - $totalCredit;

        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'bku', "Mencetak Buku Kas Umum (BKU) Bulan {$month}/{$year}");

        return view('finance.bku.print', compact('transactions', 'month', 'year', 'source', 'totalDebit', 'totalCredit', 'closingBalance', 'school'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $month = $request->query('month', (string) date('m'));
        $year = $request->query('year', (string) date('Y'));

        $transactions = CashTransaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('transaction_date', 'asc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"bku-sma1le-{$year}-{$month}.csv\"",
        ];

        return response()->stream(function () use ($transactions) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, ['No. Bukti', 'Tanggal', 'Kode Rekening', 'Uraian Transaksi', 'Penerimaan (Debit)', 'Pengeluaran (Kredit)', 'Saldo Kas', 'Penerima/Penyetor']);

            foreach ($transactions as $t) {
                fputcsv($output, [
                    $t->evidence_number,
                    $t->transaction_date->format('d/m/Y'),
                    $t->account_code ?? '-',
                    $t->description,
                    $t->debit,
                    $t->credit,
                    $t->running_balance,
                    $t->recipient_or_payer,
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }
}
