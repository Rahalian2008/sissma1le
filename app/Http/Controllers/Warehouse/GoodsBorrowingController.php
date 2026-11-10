<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\GoodsBorrowing;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoodsBorrowingController extends Controller
{
    public function index(Request $request): View
    {
        $query = GoodsBorrowing::query();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $borrowings = $query->latest('borrow_date')->paginate(15);

        return view('warehouse.borrowings.index', compact('borrowings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'borrower_name' => ['required', 'string', 'max:150'],
            'borrower_type' => ['required', 'string'],
            'good_or_asset_name' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'items' => ['nullable', 'array'],
            'borrow_date' => ['required', 'date'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:borrow_date'],
            'notes' => ['nullable', 'string'],
        ]);

        $year = date('Y');
        $count = GoodsBorrowing::whereYear('borrow_date', $year)->count() + 1;
        $validated['borrowing_number'] = "PINJAM/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'Dipinjam';

        // Check if multiple items provided
        if (! empty($request->input('items'))) {
            $itemStrings = [];
            $totalQty = 0;
            foreach ($request->input('items') as $it) {
                if (! empty($it['name'])) {
                    $q = (int) ($it['quantity'] ?? 1);
                    $itemStrings[] = "{$it['name']} ({$q} unit)";
                    $totalQty += $q;
                }
            }
            if (! empty($itemStrings)) {
                $validated['good_or_asset_name'] = implode(', ', $itemStrings);
                $validated['quantity'] = $totalQty > 0 ? $totalQty : 1;
            }
        }

        if (empty($validated['good_or_asset_name'])) {
            return back()->with('error', 'Silakan masukkan minimal satu barang yang dipinjam.');
        }

        $borrowing = GoodsBorrowing::create($validated);

        AuditTrailService::log('create', 'gudang', "Mencatat peminjaman [Multi-Item]: {$borrowing->good_or_asset_name} oleh {$borrowing->borrower_name}");

        return back()->with('success', "Peminjaman barang berhasil dicatat dengan nomor {$borrowing->borrowing_number}");
    }

    public function returnItem(Request $request, GoodsBorrowing $borrowing): RedirectResponse
    {
        $validated = $request->validate([
            'actual_return_date' => ['required', 'date'],
            'condition_on_return' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['status'] = 'Dikembalikan';
        $borrowing->update($validated);

        AuditTrailService::log('update', 'gudang', "Pengembalian barang: {$borrowing->good_or_asset_name} dari {$borrowing->borrower_name} (Kondisi: {$validated['condition_on_return']})");

        return back()->with('success', 'Pengembalian barang berhasil dicatat.');
    }

    /**
     * Cetak Surat Bukti Peminjaman Sarana Prasarana Sekolah
     */
    public function print(GoodsBorrowing $borrowing): View
    {
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'gudang', "Mencetak bukti peminjaman sarana: {$borrowing->borrowing_number}");

        return view('warehouse.borrowings.print', compact('borrowing', 'school'));
    }

    public function update(Request $request, GoodsBorrowing $borrowing): RedirectResponse
    {
        $validated = $request->validate([
            'borrower_name' => ['required', 'string', 'max:150'],
            'borrower_type' => ['required', 'string'],
            'good_or_asset_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'borrow_date' => ['required', 'date'],
            'expected_return_date' => ['required', 'date'],
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $borrowing->update($validated);

        AuditTrailService::log('update', 'gudang', "Memperbarui peminjaman sarana: {$borrowing->borrowing_number}");

        return back()->with('success', "Data peminjaman {$borrowing->borrowing_number} berhasil diperbarui.");
    }

    public function destroy(GoodsBorrowing $borrowing): RedirectResponse
    {
        $num = $borrowing->borrowing_number;
        $borrowing->delete();

        AuditTrailService::log('delete', 'gudang', "Menghapus data peminjaman sarana: {$num}");

        return back()->with('success', "Data peminjaman {$num} berhasil dihapus.");
    }
}
