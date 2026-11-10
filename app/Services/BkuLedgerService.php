<?php

namespace App\Services;

use App\Models\CashTransaction;
use Illuminate\Support\Facades\DB;

class BkuLedgerService
{
    /**
     * Recalculate all running balances sequentially ordered by transaction date and ID.
     */
    public static function recalculateRunningBalances(): void
    {
        DB::transaction(function () {
            $transactions = CashTransaction::orderBy('transaction_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $balance = 0.0;

            foreach ($transactions as $trx) {
                $debit = (float) $trx->debit;
                $credit = (float) $trx->credit;
                $balance = $balance + $debit - $credit;

                if ((float) $trx->running_balance !== $balance) {
                    $trx->updateQuietly(['running_balance' => $balance]);
                }
            }
        });
    }

    /**
     * Get summary metrics for finance dashboard.
     *
     * @return array<string, float>
     */
    public static function getSummaryMetrics(?int $year = null): array
    {
        $year = $year ?? (int) date('Y');

        $query = CashTransaction::whereYear('transaction_date', $year);

        $totalDebit = (float) (clone $query)->sum('debit');
        $totalCredit = (float) (clone $query)->sum('credit');
        $currentBalance = $totalDebit - $totalCredit;

        return [
            'total_income' => $totalDebit,
            'total_expense' => $totalCredit,
            'current_balance' => $currentBalance,
        ];
    }
}
