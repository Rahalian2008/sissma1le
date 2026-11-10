<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetMaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = AssetMaintenance::with('asset');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $maintenances = $query->latest('request_date')->paginate(15);
        $assets = Asset::orderBy('name')->get();

        return view('sarpras.maintenance.index', compact('maintenances', 'assets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'request_date' => ['required', 'date'],
            'issue_description' => ['required', 'string'],
            'action_plan' => ['nullable', 'string'],
            'estimated_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['status'] = 'Diajukan';

        $maintenance = AssetMaintenance::create($validated);
        $asset = Asset::find($validated['asset_id']);

        AuditTrailService::log('create', 'sarpras', "Mengajukan pemeliharaan untuk aset: {$asset->name}");

        return back()->with('success', 'Pengajuan pemeliharaan aset berhasil dikirim.');
    }

    public function updateStatus(Request $request, AssetMaintenance $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Disetujui,Sedang Dikerjakan,Selesai,Ditolak'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
            'technician_or_vendor' => ['nullable', 'string', 'max:150'],
        ]);

        if ($validated['status'] === 'Selesai') {
            $validated['completed_date'] = now()->toDateString();
            $maintenance->asset->update(['condition' => 'Baik']);
        }

        $maintenance->update($validated);

        AuditTrailService::log('update', 'sarpras', "Memperbarui status pemeliharaan aset [{$maintenance->asset->name}] -> {$maintenance->status}");

        return back()->with('success', 'Status pemeliharaan aset berhasil diperbarui.');
    }
}
