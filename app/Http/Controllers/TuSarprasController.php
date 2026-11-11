<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\AssetMaintenance;
use App\Models\Good;
use App\Models\GoodsBorrowing;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TuSarprasController extends Controller
{
    public function index(Request $request): View
    {
        if (! Auth::user()?->canAccessTuSubmodule('sarpras')) {
            abort(403, 'Akses tidak diizinkan. Menu ini dikhususkan untuk staf Sarpras & BMD.');
        }

        $hasAssets = Schema::hasTable('assets');
        $hasRooms = Schema::hasTable('rooms');
        $hasGoods = Schema::hasTable('goods');
        $hasBorrowings = Schema::hasTable('goods_borrowings');
        $hasDisposals = Schema::hasTable('asset_disposals');
        $hasMaintenances = Schema::hasTable('asset_maintenances');

        // 1. Metrik Aset BMD
        $totalAssets = $hasAssets ? Asset::count() : 0;
        $totalValuation = $hasAssets ? (float) Asset::sum('price') : 0.0;
        $assetsGood = $hasAssets ? Asset::where('condition', 'Baik')->count() : 0;
        $assetsMinorDamage = $hasAssets ? Asset::where('condition', 'Rusak Ringan')->count() : 0;
        $assetsHeavyDamage = $hasAssets ? Asset::where('condition', 'Rusak Berat')->count() : 0;

        // 2. Ruangan & KIR
        $totalRooms = $hasRooms ? Room::count() : 0;
        $roomsWithKir = ($hasRooms && $hasAssets) ? Room::whereHas('assets')->count() : 0;

        // 3. Logistik Gudang ATK
        $totalGoods = $hasGoods ? Good::count() : 0;
        $activeBorrowings = $hasBorrowings ? GoodsBorrowing::where('status', 'Dipinjam')->count() : 0;
        $pendingDisposals = $hasDisposals ? AssetDisposal::where('status', 'Diusulkan')->count() : 0;
        $activeMaintenances = $hasMaintenances ? AssetMaintenance::where('status', '!=', 'Selesai')->count() : 0;

        // 4. KIB Distribution
        $kibCounts = [
            'A' => $hasAssets ? Asset::where('kib_type', 'A')->count() : 0,
            'B' => $hasAssets ? Asset::where('kib_type', 'B')->count() : 0,
            'C' => $hasAssets ? Asset::where('kib_type', 'C')->count() : 0,
            'D' => $hasAssets ? Asset::where('kib_type', 'D')->count() : 0,
            'E' => $hasAssets ? Asset::where('kib_type', 'E')->count() : 0,
            'F' => $hasAssets ? Asset::where('kib_type', 'F')->count() : 0,
        ];

        // 5. Daftar Aset Terbaru
        $recentAssets = ($hasAssets && $hasRooms) ? Asset::with('room')->latest()->take(8)->get() : collect();

        return view('tu.sarpras.index', compact(
            'totalAssets',
            'totalValuation',
            'assetsGood',
            'assetsMinorDamage',
            'assetsHeavyDamage',
            'totalRooms',
            'roomsWithKir',
            'totalGoods',
            'activeBorrowings',
            'pendingDisposals',
            'activeMaintenances',
            'kibCounts',
            'recentAssets'
        ));
    }
}
