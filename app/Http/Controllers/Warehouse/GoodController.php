<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Good;
use App\Models\ReferenceItem;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoodController extends Controller
{
    public function index(Request $request): View
    {
        $query = Good::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($request->boolean('low_stock')) {
            $goods = $query->get()->filter(fn ($g) => $g->isLowStock());
        } else {
            $goods = $query->orderBy('name')->paginate(15)->withQueryString();
        }

        $categories = ReferenceItem::category('jenis_barang')->get();
        $units = ReferenceItem::category('satuan')->get();

        return view('warehouse.goods.index', compact('goods', 'categories', 'units'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:goods'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'unit' => ['required', 'string'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'storage_location' => ['nullable', 'string', 'max:100'],
        ]);

        $good = Good::create($validated);

        AuditTrailService::log('create', 'gudang', "Menambahkan item barang gudang: {$good->name} ({$good->code})");

        return back()->with('success', 'Barang persediaan berhasil ditambahkan ke katalog gudang.');
    }

    public function update(Request $request, Good $good): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:goods,code,'.$good->id],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'unit' => ['required', 'string'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'storage_location' => ['nullable', 'string', 'max:100'],
        ]);

        $good->update($validated);

        AuditTrailService::log('update', 'gudang', "Memperbarui item barang gudang: {$good->name} ({$good->code})");

        return back()->with('success', 'Data barang persediaan berhasil diperbarui.');
    }

    public function card(Good $good): View
    {
        $receipts = $good->receiptItems()->with('receipt')->latest()->get();
        $issuances = $good->issuanceItems()->with('issuance')->latest()->get();

        return view('warehouse.goods.card', compact('good', 'receipts', 'issuances'));
    }

    public function destroy(Good $good): RedirectResponse
    {
        $name = $good->name;
        $good->delete();

        AuditTrailService::log('delete', 'gudang', "Menghapus item barang: {$name}");

        return back()->with('success', 'Barang berhasil dihapus.');
    }
}
