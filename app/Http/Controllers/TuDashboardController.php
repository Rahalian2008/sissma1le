<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Archive;
use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\BudgetItem;
use App\Models\CashTransaction;
use App\Models\Disposition;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Models\Good;
use App\Models\GoodsBorrowing;
use App\Models\GoodsReceipt;
use App\Models\Guest;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Room;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\StudentDiploma;
use App\Models\StudentMutation;
use App\Models\StudentScholarship;
use App\Models\Violation;
use App\Services\SipdBmdService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TuDashboardController extends Controller
{
    public function index(): View
    {
        TuKepegawaianController::ensureDefaultEmployees();
        $user = Auth::user();
        $school = SchoolIdentity::first();

        // 1. Kesiswaan Metrics
        $totalStudents = Student::count();
        $activeStudents = Student::where('is_active', true)->count() ?: $totalStudents;
        $maleStudents = Student::where('gender', 'L')->count();
        $femaleStudents = Student::where('gender', 'P')->count();
        $classXCount = Student::whereHas('schoolClass', fn ($q) => $q->where('grade', '10')->orWhere('name', 'like', 'X %'))->count();
        $classXICount = Student::whereHas('schoolClass', fn ($q) => $q->where('grade', '11')->orWhere('name', 'like', 'XI %'))->count();
        $classXIICount = Student::whereHas('schoolClass', fn ($q) => $q->where('grade', '12')->orWhere('name', 'like', 'XII %'))->count();
        $pipBeneficiaries = StudentScholarship::count();
        $totalAchievements = class_exists(Achievement::class) ? Achievement::count() : 0;
        $totalViolations = class_exists(Violation::class) ? Violation::count() : 0;

        // 2. Kepegawaian Metrics
        $totalEmployees = Employee::count();
        $totalTeachers = Employee::where('employment_type', 'Guru')->count();
        $totalStaff = Employee::where('employment_type', '!=', 'Guru')->count();
        $pnsEmployees = Employee::where('employment_status', 'PNS')->count();
        $pppkEmployees = Employee::where('employment_status', 'PPPK')->count();
        $honorerEmployees = Employee::whereIn('employment_status', ['GTT', 'PTT', 'Honorer'])->count();

        // 2b. Buku Kontrol Kepegawaian Metrics
        $threeMonthsAhead = now()->addMonths(3);
        $kpDueCount = Employee::where('employment_status', 'PNS')
            ->whereNotNull('next_kp_tmt')
            ->where('next_kp_tmt', '<=', $threeMonthsAhead)
            ->count();

        $kgbDueCount = Employee::whereIn('employment_status', ['PNS', 'PPPK'])
            ->whereNotNull('next_kgb_tmt')
            ->where('next_kgb_tmt', '<=', $threeMonthsAhead)
            ->count();

        $pppkDueCount = Employee::where('employment_status', 'PPPK')
            ->whereNotNull('pppk_contract_end')
            ->where('pppk_contract_end', '<=', now()->addMonths(6))
            ->count();

        $todayDate = now()->toDateString();
        $activeLeavesCount = EmployeeLeave::where('status', 'Disetujui')
            ->where('start_date', '<=', $todayDate)
            ->where('end_date', '>=', $todayDate)
            ->count();

        // 3. Keuangan Metrics
        $totalBudgetAllocation = (float) BudgetItem::sum('allocated_amount');
        $totalBudgetRealized = (float) BudgetItem::sum('realized_amount');
        $totalIncome = (float) CashTransaction::sum('debit');
        $totalExpense = (float) CashTransaction::sum('credit');
        $cashBalance = $totalIncome - $totalExpense;
        $budgetAbsorptionPct = $totalBudgetAllocation > 0
            ? round(($totalBudgetRealized / $totalBudgetAllocation) * 100, 1)
            : 0.0;

        // 4. Persuratan Metrics
        $incomingLettersCount = IncomingLetter::count();
        $outgoingLettersCount = OutgoingLetter::count();
        $pendingDispositionsCount = Disposition::where('status', '!=', 'Selesai')->count();

        // 5. Sarpras Metrics (KIB & KIR)
        $totalAssets = Asset::count();
        $assetsGood = Asset::where('condition', 'Baik')->count();
        $assetsMinorDamage = Asset::where('condition', 'Rusak Ringan')->count();
        $assetsHeavyDamage = Asset::where('condition', 'Rusak Berat')->count();
        $totalRoomsWithKir = Room::whereHas('assets')->count();
        $kibCounts = [
            'A' => Asset::where('kib_type', 'A')->count(),
            'B' => Asset::where('kib_type', 'B')->count(),
            'C' => Asset::where('kib_type', 'C')->count(),
            'D' => Asset::where('kib_type', 'D')->count(),
            'E' => Asset::where('kib_type', 'E')->count(),
            'F' => Asset::where('kib_type', 'F')->count(),
        ];

        // 5b. Sarpras Valuasi & Afkir & SIPD e-BMD
        $totalAssetValuation = (float) Asset::sum('price');
        $pendingDisposalsCount = AssetDisposal::where('status', 'Diusulkan')->count();
        $totalSipdCodes = class_exists(SipdBmdService::class) ? count(SipdBmdService::getStandardCodes()) : 0;

        // 6b. Logistik Gudang Valuasi & Peminjaman
        $activeBorrowingsCount = GoodsBorrowing::where('status', 'Dipinjam')->count();
        $pendingReceiptsCount = GoodsReceipt::whereDoesntHave('cashTransactions', fn ($q) => $q->where('type', 'Pengeluaran'))->count();
        $recentReceipts = GoodsReceipt::latest('receipt_date')->take(5)->get();

        // 3b. Keuangan Rincian Kas Tunai & Bank
        $cashOnHand = (float) CashTransaction::where('payment_method', 'Kas Tunai')->sum('debit') - (float) CashTransaction::where('payment_method', 'Kas Tunai')->sum('credit');
        $bankBalance = (float) CashTransaction::where('payment_method', 'Bank')->sum('debit') - (float) CashTransaction::where('payment_method', 'Bank')->sum('credit');
        $recentCashTransactions = CashTransaction::latest('transaction_date')->take(5)->get();

        // 1b. Operasional Kesiswaan Lainnya
        $mutationsCount = StudentMutation::count();
        $diplomasCount = StudentDiploma::count();
        $scholarshipsCount = StudentScholarship::count();

        // 6. Gudang Metrics
        $totalGoods = Good::count();
        $lowStockGoods = Good::all()->filter(fn ($g) => method_exists($g, 'isLowStock') && $g->isLowStock())->count();
        $lowStockList = Good::all()->filter(fn ($g) => method_exists($g, 'isLowStock') && $g->isLowStock())->take(5);

        // 7. Arsip Metrics
        $activeArchives = Archive::where('archive_status', 'Aktif')->count();
        $totalArchives = Archive::count();

        // 8. Buku Tamu Digital Metrics
        $totalGuestsToday = Guest::whereDate('created_at', today())->count();
        $activeGuestsCount = Guest::whereNull('check_out_at')->count();
        $totalGuestsAll = Guest::count();
        $recentGuests = Guest::latest()->take(5)->get();

        // Recent Letters & Pending Dispositions
        $recentIncomingLetters = IncomingLetter::latest('received_date')->take(5)->get();
        $pendingDispositions = Disposition::with('incomingLetter')
            ->where('status', '!=', 'Selesai')
            ->latest('disposition_date')
            ->take(5)
            ->get();

        return view('tu.index', compact(
            'user',
            'school',
            'totalStudents',
            'activeStudents',
            'maleStudents',
            'femaleStudents',
            'classXCount',
            'classXICount',
            'classXIICount',
            'pipBeneficiaries',
            'totalAchievements',
            'totalViolations',
            'mutationsCount',
            'diplomasCount',
            'scholarshipsCount',
            'totalEmployees',
            'totalTeachers',
            'totalStaff',
            'pnsEmployees',
            'pppkEmployees',
            'honorerEmployees',
            'kpDueCount',
            'kgbDueCount',
            'pppkDueCount',
            'activeLeavesCount',
            'totalBudgetAllocation',
            'totalBudgetRealized',
            'totalIncome',
            'totalExpense',
            'cashBalance',
            'cashOnHand',
            'bankBalance',
            'budgetAbsorptionPct',
            'recentCashTransactions',
            'incomingLettersCount',
            'outgoingLettersCount',
            'pendingDispositionsCount',
            'totalAssets',
            'totalAssetValuation',
            'assetsGood',
            'assetsMinorDamage',
            'assetsHeavyDamage',
            'totalRoomsWithKir',
            'pendingDisposalsCount',
            'kibCounts',
            'totalSipdCodes',
            'totalGoods',
            'lowStockGoods',
            'lowStockList',
            'activeBorrowingsCount',
            'pendingReceiptsCount',
            'recentReceipts',
            'activeArchives',
            'totalArchives',
            'totalGuestsToday',
            'activeGuestsCount',
            'totalGuestsAll',
            'recentGuests',
            'recentIncomingLetters',
            'pendingDispositions'
        ));
    }
}
