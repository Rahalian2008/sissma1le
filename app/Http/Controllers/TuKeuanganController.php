<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\CashTransaction;
use App\Models\TaxRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TuKeuanganController extends Controller
{
    public function index(Request $request): View
    {
        if (! Auth::user()?->canAccessTuSubmodule('keuangan')) {
            abort(403, 'Akses tidak diizinkan. Menu ini dikhususkan untuk Bendahara dan Pengelola Keuangan.');
        }

        $hasBudgets = Schema::hasTable('budgets');
        $hasBudgetItems = Schema::hasTable('budget_items');
        $hasCash = Schema::hasTable('cash_transactions');
        $hasTaxes = Schema::hasTable('tax_records');

        // 1. Anggaran (RKAS)
        $totalBudgetAllocated = $hasBudgetItems ? (float) BudgetItem::sum('allocated_amount') : 0.0;
        if ($totalBudgetAllocated == 0 && $hasBudgets) {
            $totalBudgetAllocated = (float) Budget::sum('total_allocation');
        }

        $totalBudgetRealized = $hasBudgetItems ? (float) BudgetItem::sum('realized_amount') : 0.0;
        if ($totalBudgetRealized == 0 && $hasCash) {
            $totalBudgetRealized = (float) CashTransaction::where('type', 'Pengeluaran')->sum('credit');
        }

        $absorptionPct = $totalBudgetAllocated > 0
            ? round(($totalBudgetRealized / $totalBudgetAllocated) * 100, 1)
            : 0.0;

        // 2. BKU & Kas
        $totalDebit = $hasCash ? (float) CashTransaction::sum('debit') : 0.0;
        $totalCredit = $hasCash ? (float) CashTransaction::sum('credit') : 0.0;
        $totalBalance = $totalDebit - $totalCredit;

        // 3. Kas Tunai vs Bank
        $cashDebit = $hasCash ? (float) CashTransaction::where('payment_method', 'Tunai')->sum('debit') : 0.0;
        $cashCredit = $hasCash ? (float) CashTransaction::where('payment_method', 'Tunai')->sum('credit') : 0.0;
        $cashOnHand = $cashDebit - $cashCredit;

        $bankDebit = $hasCash ? (float) CashTransaction::where('payment_method', '!=', 'Tunai')->sum('debit') : 0.0;
        $bankCredit = $hasCash ? (float) CashTransaction::where('payment_method', '!=', 'Tunai')->sum('credit') : 0.0;
        $bankBalance = $bankDebit - $bankCredit;

        // 4. Pajak Terutang / Belum Disetor
        $unpaidTaxesCount = $hasTaxes ? TaxRecord::where('status', '!=', 'Disetor')->count() : 0;
        $unpaidTaxesAmount = $hasTaxes ? (float) TaxRecord::where('status', '!=', 'Disetor')->sum('tax_amount') : 0.0;

        // 5. Transaksi Kas Terbaru
        $recentTransactions = ($hasCash && Schema::hasTable('budget_items'))
            ? CashTransaction::with('budgetItem')->latest('transaction_date')->latest('id')->take(8)->get()
            : ($hasCash ? CashTransaction::latest('transaction_date')->latest('id')->take(8)->get() : collect());

        return view('tu.keuangan.index', compact(
            'totalBudgetAllocated',
            'totalBudgetRealized',
            'absorptionPct',
            'totalDebit',
            'totalCredit',
            'totalBalance',
            'cashOnHand',
            'bankBalance',
            'unpaidTaxesCount',
            'unpaidTaxesAmount',
            'recentTransactions'
        ));
    }
}
