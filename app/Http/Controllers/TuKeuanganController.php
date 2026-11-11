<?php

namespace App\Http\Controllers;

use App\Models\BudgetItem;
use App\Models\CashTransaction;
use App\Models\Tax;
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

        $hasBudgetItems = Schema::hasTable('budget_items');
        $hasCash = Schema::hasTable('cash_transactions');
        $hasTaxes = class_exists(Tax::class) && Schema::hasTable('taxes');

        // 1. Anggaran (RKAS)
        $totalBudgetAllocated = $hasBudgetItems ? (float) BudgetItem::sum('allocated_amount') : 0.0;
        $totalBudgetRealized = $hasBudgetItems ? (float) BudgetItem::sum('realized_amount') : 0.0;
        $absorptionPct = $totalBudgetAllocated > 0
            ? round(($totalBudgetRealized / $totalBudgetAllocated) * 100, 1)
            : 0.0;

        // 2. BKU & Kas
        $totalDebit = $hasCash ? (float) CashTransaction::sum('debit') : 0.0;
        $totalCredit = $hasCash ? (float) CashTransaction::sum('credit') : 0.0;
        $totalBalance = $totalDebit - $totalCredit;

        // 3. Kas Tunai vs Bank
        $cashOnHand = $hasCash ? ((float) CashTransaction::where('payment_method', 'Kas Tunai')->sum('debit') - (float) CashTransaction::where('payment_method', 'Kas Tunai')->sum('credit')) : 0.0;
        $bankBalance = $hasCash ? ((float) CashTransaction::where('payment_method', 'Bank')->sum('debit') - (float) CashTransaction::where('payment_method', 'Bank')->sum('credit')) : 0.0;

        // 4. Pajak Terutang / Belum Disetor
        $unpaidTaxesCount = $hasTaxes ? Tax::where('status', '!=', 'Disetor')->count() : 0;
        $unpaidTaxesAmount = $hasTaxes ? (float) Tax::where('status', '!=', 'Disetor')->sum('amount') : 0.0;

        // 5. Transaksi Kas Terbaru
        $recentTransactions = ($hasCash && Schema::hasTable('budget_items'))
            ? CashTransaction::with('budgetItem')->latest('transaction_date')->take(8)->get()
            : ($hasCash ? CashTransaction::latest('transaction_date')->take(8)->get() : collect());

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
