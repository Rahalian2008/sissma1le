<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Good;
use App\Models\StockOpname;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    public function index(): View
    {
        $opnames = StockOpname::with('good')->latest('opname_date')->paginate(15);
        $goods = Good::orderBy('name')->get();

        return view('warehouse.opnames.index', compact('opnames', 'goods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opname_date' => ['required', 'date'],
            'inspector_name' => ['required', 'string', 'max:150'],
            'good_id' => ['required', 'exists:goods,id'],
            'physical_stock' => ['required', 'integer', 'min:0'],
            'explanation' => ['nullable', 'string'],
        ]);

        $good = Good::find($validated['good_id']);
        $systemStock = $good->current_stock;
        $diff = $validated['physical_stock'] - $systemStock;

        $opname = StockOpname::create([
            'opname_date' => $validated['opname_date'],
            'inspector_name' => $validated['inspector_name'],
            'good_id' => $good->id,
            'system_stock' => $systemStock,
            'physical_stock' => $validated['physical_stock'],
            'difference' => $diff,
            'explanation' => $validated['explanation'],
            'official_report_number' => 'BA-OPNAME/'.date('Ym').'/'.rand(100, 999),
        ]);

        // Adjust stock to match physical reality
        $good->update(['current_stock' => $validated['physical_stock']]);

        AuditTrailService::log('create', 'gudang', "Stok Opname [{$good->name}]: Sistem={$systemStock}, Fisik={$validated['physical_stock']}, Selisih={$diff}");

        return back()->with('success', "Stok Opname dicatat. Stok fisik {$good->name} telah diselaraskan.");
    }
}
