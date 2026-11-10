<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\ReferenceItem;
use App\Models\Room;
use App\Models\SchoolIdentity;
use App\Services\AssetGroupingService;
use App\Services\AuditTrailService;
use App\Services\QrCodeService;
use App\Services\SimBmdService;
use App\Services\SipdBmdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $query = Asset::with('room');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('sim_bmd_code', 'like', "%{$search}%")
                    ->orWhere('brand_model', 'like', "%{$search}%");
            });
        }

        if ($condition = $request->query('condition')) {
            $query->where('condition', $condition);
        }

        if ($roomId = $request->query('room_id')) {
            $query->where('room_id', $roomId);
        }

        if ($kibType = $request->query('kib_type')) {
            $query->where('kib_type', $kibType);
        }

        $rawAssets = $query->latest('id')->get();
        $groupedAssets = AssetGroupingService::group($rawAssets);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $assets = new LengthAwarePaginator(
            $groupedAssets->forPage($page, $perPage)->values(),
            $groupedAssets->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $rooms = Room::orderBy('name')->get();
        $conditions = ReferenceItem::category('kondisi_barang')->pluck('name')->toArray();
        if (empty($conditions)) {
            $conditions = ['Baik', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat', 'Hilang'];
        }
        $kibCategories = SimBmdService::getKibCategories();

        // KIB Counts for tabs
        $kibCounts = [
            'all' => Asset::count(),
            'A' => Asset::where('kib_type', 'A')->count(),
            'B' => Asset::where('kib_type', 'B')->count(),
            'C' => Asset::where('kib_type', 'C')->count(),
            'D' => Asset::where('kib_type', 'D')->count(),
            'E' => Asset::where('kib_type', 'E')->count(),
            'F' => Asset::where('kib_type', 'F')->count(),
        ];

        return view('sarpras.assets.index', compact('assets', 'rooms', 'conditions', 'kibCategories', 'kibCounts'));
    }

    public function create(): View
    {
        $rooms = Room::orderBy('name')->get();
        $kibCategories = SipdBmdService::getKibCategories();
        $sipdBmdCodes = SipdBmdService::getStandardCodes();
        $simBmdCodes = $sipdBmdCodes;
        $conditions = ReferenceItem::category('kondisi_barang')->get();
        $fundingSources = ReferenceItem::category('sumber_dana')->get();
        $units = ReferenceItem::category('satuan')->get();

        return view('sarpras.assets.create', compact('rooms', 'kibCategories', 'sipdBmdCodes', 'simBmdCodes', 'conditions', 'fundingSources', 'units'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'kib_type' => ['nullable', 'string', 'in:A,B,C,D,E,F'],
            'sim_bmd_code' => ['nullable', 'string', 'max:50'],
            'brand_model' => ['nullable', 'string', 'max:150'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'specification' => ['nullable', 'string'],
            'procurement_year' => ['required', 'integer', 'min:2000', 'max:'.(date('Y') + 1)],
            'funding_source' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:30'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'condition' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $year = $validated['procurement_year'];

        // Tentukan KIB Type
        $kibType = $validated['kib_type'] ?? (! empty($validated['sim_bmd_code']) ? SimBmdService::determineKibType($validated['sim_bmd_code']) : 'B');
        $validated['kib_type'] = $kibType;

        // Kodefikasi SIM BMD Jawa Barat jika sim_bmd_code diisi
        if (! empty($validated['sim_bmd_code'])) {
            $regNumber = SimBmdService::getNextRegisterNumber($validated['sim_bmd_code'], $year);
            $validated['register_number'] = $regNumber;
            $validated['code'] = SimBmdService::generateFullBmdCode($validated['sim_bmd_code'], $year, $regNumber);
        } else {
            $count = Asset::where('procurement_year', $year)->count() + 1;
            $validated['code'] = "AST/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            $validated['register_number'] = str_pad((string) $count, 4, '0', STR_PAD_LEFT);
        }

        $validated['qr_code_token'] = Str::uuid()->toString();

        $asset = Asset::create($validated);

        AuditTrailService::log('create', 'sarpras', "Mendaftarkan aset KIB {$kibType}: {$asset->name} (Kode: {$asset->code})");

        return redirect()->route('sarpras.assets.index')->with('success', "Aset berhasil didaftarkan ke KIB {$kibType} dengan kode {$asset->code}");
    }

    public function show(Asset $asset): View
    {
        $asset->load(['room', 'maintenances', 'goodsReceipt']);
        $school = SchoolIdentity::current();

        $verificationUrl = route('verify.public', ['type' => 'aset', 'token' => $asset->qr_code_token ?? $asset->code]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 160);

        // Cari item sejenis yang masuk bersamaan dalam satu penerimaan barang (batch)
        $siblingAssets = collect();
        if ($asset->goods_receipt_id) {
            $siblingAssets = Asset::where('goods_receipt_id', $asset->goods_receipt_id)
                ->where('sim_bmd_code', $asset->sim_bmd_code)
                ->orderBy('register_number')
                ->get();
        }

        return view('sarpras.assets.show', compact('asset', 'school', 'qrCodeSvg', 'verificationUrl', 'siblingAssets'));
    }

    public function label(Asset $asset): View
    {
        $school = SchoolIdentity::current();
        $verificationUrl = route('verify.public', ['type' => 'aset', 'token' => $asset->qr_code_token ?? $asset->code]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 120);

        AuditTrailService::log('print', 'sarpras', "Mencetak label inventaris barcode/QR: {$asset->code}");

        return view('sarpras.assets.label', compact('asset', 'school', 'qrCodeSvg'));
    }

    /**
     * Halaman Kustomisasi Cetak Label Inventaris (Bisa pilih per barang, per ruangan / KIR, atau per KIB)
     */
    public function customLabelIndex(Request $request): View
    {
        $rooms = Room::orderBy('name')->get();
        $kibCategories = SimBmdService::getKibCategories();

        $query = Asset::with('room');

        if ($roomId = $request->query('room_id')) {
            $query->where('room_id', $roomId);
        }

        if ($kibType = $request->query('kib_type')) {
            $query->where('kib_type', $kibType);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('sim_bmd_code', 'like', "%{$search}%")
                    ->orWhere('brand_model', 'like', "%{$search}%");
            });
        }

        $assets = $query->orderBy('room_id')->orderBy('kib_type')->orderBy('code')->get();

        return view('sarpras.assets.labels-custom', compact('assets', 'rooms', 'kibCategories'));
    }

    /**
     * Cetak Label Stiker Inventaris (Multi-item dalam satu dokumen: per barang terpilih atau per ruangan/KIR)
     */
    public function printLabels(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = Asset::with('room');

        if ($request->filled('asset_ids')) {
            $rawIds = $request->query('asset_ids');
            $ids = is_array($rawIds) ? $rawIds : explode(',', (string) $rawIds);
            $cleanIds = array_filter(array_map('intval', $ids));
            if (! empty($cleanIds)) {
                $query->whereIn('id', $cleanIds);
            }
        } elseif ($request->filled('room_id')) {
            $query->where('room_id', $request->query('room_id'));
        } elseif ($request->filled('goods_receipt_id')) {
            $query->where('goods_receipt_id', $request->query('goods_receipt_id'));
        } elseif ($request->filled('kib_type')) {
            $query->where('kib_type', $request->query('kib_type'));
        }

        $assets = $query->orderBy('room_id')->orderBy('kib_type')->orderBy('code')->get();

        $labels = $assets->map(function (Asset $item) {
            $verificationUrl = route('verify.public', ['type' => 'aset', 'token' => $item->qr_code_token ?? $item->code]);
            $qrCodeSvg = QrCodeService::svg($verificationUrl, 110);

            return [
                'asset' => $item,
                'qrCodeSvg' => $qrCodeSvg,
                'verificationUrl' => $verificationUrl,
            ];
        });

        $layout = $request->query('layout', 'grid_2col');
        $room = $request->filled('room_id') ? Room::find($request->query('room_id')) : null;

        AuditTrailService::log('print', 'sarpras', "Mencetak {$assets->count()} label inventaris stiker".($room ? " (Ruangan: {$room->name})" : ''));

        return view('sarpras.assets.labels-print', compact('school', 'labels', 'layout', 'room', 'assets'));
    }

    public function edit(Asset $asset): View
    {
        $rooms = Room::orderBy('name')->get();
        $kibCategories = SipdBmdService::getKibCategories();
        $sipdBmdCodes = SipdBmdService::getStandardCodes();
        $simBmdCodes = $sipdBmdCodes;
        $conditions = ReferenceItem::category('kondisi_barang')->get();
        $fundingSources = ReferenceItem::category('sumber_dana')->get();
        $units = ReferenceItem::category('satuan')->get();

        return view('sarpras.assets.edit', compact('asset', 'rooms', 'kibCategories', 'sipdBmdCodes', 'simBmdCodes', 'conditions', 'fundingSources', 'units'));
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'kib_type' => ['nullable', 'string', 'in:A,B,C,D,E,F'],
            'sim_bmd_code' => ['nullable', 'string', 'max:50'],
            'brand_model' => ['nullable', 'string', 'max:150'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'specification' => ['nullable', 'string'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'condition' => ['required', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'funding_source' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $old = $asset->toArray();
        $asset->update($validated);

        AuditTrailService::log('update', 'sarpras', "Memperbarui aset: {$asset->name} (Kondisi: {$asset->condition})", $old, $validated);

        return redirect()->route('sarpras.assets.show', $asset)->with('success', 'Data aset inventaris berhasil diperbarui.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $name = $asset->name;
        $asset->delete();

        AuditTrailService::log('delete', 'sarpras', "Menghapus aset inventaris: {$name}");

        return redirect()->route('sarpras.assets.index')->with('success', 'Data aset berhasil dihapus.');
    }

    /**
     * Halaman Administrasi KIR (Kartu Inventaris Ruangan)
     */
    public function kir(Request $request): View
    {
        $rooms = Room::withCount('assets')->orderBy('name')->get();
        $selectedRoomId = $request->query('room_id', $rooms->first()?->id);
        $selectedRoom = $rooms->firstWhere('id', $selectedRoomId) ?? $rooms->first();

        $assets = collect();
        if ($selectedRoom) {
            $rawAssets = Asset::where('room_id', $selectedRoom->id)
                ->orderBy('kib_type')
                ->orderBy('name')
                ->get();
            $assets = AssetGroupingService::group($rawAssets);
        }

        return view('sarpras.kir.index', compact('rooms', 'selectedRoom', 'assets'));
    }

    /**
     * Cetak Format Resmi KIR (Kartu Inventaris Ruangan) Standar Disdik Jabar
     */
    public function printKir(Room $room): View
    {
        $school = SchoolIdentity::current();
        $rawAssets = Asset::where('room_id', $room->id)
            ->orderBy('kib_type')
            ->orderBy('name')
            ->get();
        $assets = AssetGroupingService::group($rawAssets);

        AuditTrailService::log('print', 'sarpras', "Mencetak Kartu Inventaris Ruangan (KIR): {$room->name}");

        return view('sarpras.kir.print', compact('school', 'room', 'assets'));
    }

    /**
     * Cetak Format Resmi KIB (Kartu Inventaris Barang A s.d. F)
     */
    public function printKib(Request $request): View
    {
        $school = SchoolIdentity::current();
        $kibType = $request->query('kib_type', 'B');
        $kibCategories = SimBmdService::getKibCategories();
        $kibInfo = $kibCategories[$kibType] ?? $kibCategories['B'];

        $rawAssets = Asset::with('room')
            ->where('kib_type', $kibType)
            ->orderBy('code')
            ->get();
        $assets = AssetGroupingService::group($rawAssets);

        AuditTrailService::log('print', 'sarpras', "Mencetak Dokumen KIB {$kibType} ({$kibInfo['title']})");

        return view('sarpras.assets.kib-print', compact('school', 'kibType', 'kibInfo', 'assets'));
    }

    /**
     * Pencarian online / interaktif katalog kodefikasi SIM BMD Jawa Barat
     */
    public function searchSimBmd(Request $request): JsonResponse
    {
        $q = $request->query('q', '');
        $kib = $request->query('kib', 'ALL');
        $results = SimBmdService::searchCodes($q, $kib, 80);

        return response()->json([
            'success' => true,
            'count' => count($results),
            'results' => $results,
        ]);
    }

    /**
     * Buku Induk Inventaris (BII) - Standar Administrasi TAS Permendiknas No. 24/2008 & BMD
     */
    public function bibi(Request $request): View
    {
        $query = Asset::with(['room', 'goodsReceipt']);

        if ($year = $request->query('year')) {
            $query->where('procurement_year', $year);
        }

        if ($kibType = $request->query('kib_type')) {
            $query->where('kib_type', $kibType);
        }

        if ($funding = $request->query('funding_source')) {
            $query->where('funding_source', $funding);
        }

        if ($selectedCondition = $request->query('condition')) {
            if ($selectedCondition !== 'all') {
                $query->where('condition', $selectedCondition);
            }
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('sim_bmd_code', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->query('sort_by', 'default');
        if ($sortBy === 'condition' || $sortBy === 'kondisi') {
            $query->orderByRaw("CASE condition WHEN 'Baik' THEN 1 WHEN 'Rusak Ringan' THEN 2 WHEN 'Rusak Sedang' THEN 3 WHEN 'Rusak Berat' THEN 4 WHEN 'Hilang' THEN 5 WHEN 'Dihapus/Afkir' THEN 6 ELSE 7 END ASC")
                ->orderBy('kib_type')
                ->orderBy('procurement_year', 'desc')
                ->orderBy('code');
        } else {
            $query->orderBy('kib_type')->orderBy('procurement_year', 'desc')->orderBy('code');
        }

        $rawAssets = $query->get();
        $groupedAssets = AssetGroupingService::group($rawAssets);

        if ($sortBy === 'condition' || $sortBy === 'kondisi') {
            $condOrder = ['Baik' => 1, 'Rusak Ringan' => 2, 'Rusak Sedang' => 3, 'Rusak Berat' => 4, 'Hilang' => 5, 'Dihapus/Afkir' => 6];
            $groupedAssets = $groupedAssets->sortBy(function ($item) use ($condOrder) {
                $rank = $condOrder[$item->condition] ?? 99;

                return sprintf('%02d_%s_%04d_%s', $rank, $item->kib_type, 9999 - (int) $item->procurement_year, $item->code);
            })->values();
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $assets = new LengthAwarePaginator(
            $groupedAssets->forPage($page, $perPage)->values(),
            $groupedAssets->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $years = Asset::distinct()->orderBy('procurement_year', 'desc')->pluck('procurement_year');
        $kibCategories = SimBmdService::getKibCategories();

        // Rekapitulasi Neraca Aset Sekolah
        $totalItems = Asset::count();
        $totalValue = (float) Asset::sum('price');
        $activeItems = Asset::whereNotIn('condition', ['Dihapus/Afkir', 'Hilang'])->count();
        $activeValue = (float) Asset::whereNotIn('condition', ['Dihapus/Afkir', 'Hilang'])->sum('price');

        $kibSummary = [];
        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $kib) {
            $kibSummary[$kib] = [
                'count' => Asset::where('kib_type', $kib)->count(),
                'value' => (float) Asset::where('kib_type', $kib)->sum('price'),
                'title' => $kibCategories[$kib]['title'] ?? "KIB {$kib}",
            ];
        }

        // Rekap Kondisi Aset Lengkap: Baik, Rusak Ringan, Rusak Sedang, Rusak Berat, Hilang
        $conditionList = ['Baik', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat', 'Hilang'];
        $conditionSummary = [];
        foreach ($conditionList as $condName) {
            $cCount = Asset::where('condition', $condName)->count();
            $cValue = (float) Asset::where('condition', $condName)->sum('price');
            $conditionSummary[$condName] = [
                'count' => $cCount,
                'value' => $cValue,
                'percentage' => $totalItems > 0 ? round(($cCount / $totalItems) * 100, 1) : 0,
            ];
        }
        $conditionSummary['Dihapus/Afkir'] = [
            'count' => Asset::where('condition', 'Dihapus/Afkir')->count(),
            'value' => (float) Asset::where('condition', 'Dihapus/Afkir')->sum('price'),
            'percentage' => $totalItems > 0 ? round((Asset::where('condition', 'Dihapus/Afkir')->count() / $totalItems) * 100, 1) : 0,
        ];

        return view('sarpras.bibi.index', compact(
            'assets',
            'years',
            'kibCategories',
            'totalItems',
            'totalValue',
            'activeItems',
            'activeValue',
            'kibSummary',
            'conditionSummary',
            'selectedCondition',
            'sortBy'
        ));
    }

    /**
     * Cetak Resmi Dokumen Buku Induk Inventaris (BII)
     */
    public function printBibi(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = Asset::with(['room']);

        if ($year = $request->query('year')) {
            $query->where('procurement_year', $year);
        }

        if ($kibType = $request->query('kib_type')) {
            $query->where('kib_type', $kibType);
        }

        if ($funding = $request->query('funding_source')) {
            $query->where('funding_source', $funding);
        }

        $selectedCondition = $request->query('condition');
        if ($selectedCondition && $selectedCondition !== 'all') {
            $query->where('condition', $selectedCondition);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('sim_bmd_code', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->query('sort_by', 'default');
        if ($sortBy === 'condition' || $sortBy === 'kondisi') {
            $query->orderByRaw("CASE condition WHEN 'Baik' THEN 1 WHEN 'Rusak Ringan' THEN 2 WHEN 'Rusak Sedang' THEN 3 WHEN 'Rusak Berat' THEN 4 WHEN 'Hilang' THEN 5 WHEN 'Dihapus/Afkir' THEN 6 ELSE 7 END ASC")
                ->orderBy('kib_type')
                ->orderBy('procurement_year', 'asc')
                ->orderBy('code');
        } else {
            $query->orderBy('kib_type')->orderBy('procurement_year', 'asc')->orderBy('code');
        }

        $rawAssets = $query->get();
        $assets = AssetGroupingService::group($rawAssets);

        if ($sortBy === 'condition' || $sortBy === 'kondisi') {
            $condOrder = ['Baik' => 1, 'Rusak Ringan' => 2, 'Rusak Sedang' => 3, 'Rusak Berat' => 4, 'Hilang' => 5, 'Dihapus/Afkir' => 6];
            $assets = $assets->sortBy(function ($item) use ($condOrder) {
                $rank = $condOrder[$item->condition] ?? 99;

                return sprintf('%02d_%s_%04d_%s', $rank, $item->kib_type, $item->procurement_year, $item->code);
            })->values();
        }

        $grandTotal = $assets->sum('total_price');
        $totalQuantity = $assets->sum('quantity');
        $kibCategories = SimBmdService::getKibCategories();
        $selectedYear = $request->query('year');
        $selectedKib = $request->query('kib_type');

        // Rekapitulasi Kondisi Aset Lengkap: Baik, Rusak Ringan, Rusak Sedang, Rusak Berat, Hilang
        $conditionList = ['Baik', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat', 'Hilang'];
        $conditionSummary = [];
        $baseStatsQuery = Asset::query();
        if ($year) {
            $baseStatsQuery->where('procurement_year', $year);
        }
        if ($kibType) {
            $baseStatsQuery->where('kib_type', $kibType);
        }
        $totalItemsCount = (clone $baseStatsQuery)->count();
        $totalItemsValue = (float) (clone $baseStatsQuery)->sum('price');

        foreach ($conditionList as $condName) {
            $cCount = (clone $baseStatsQuery)->where('condition', $condName)->count();
            $cValue = (float) (clone $baseStatsQuery)->where('condition', $condName)->sum('price');
            $conditionSummary[$condName] = [
                'count' => $cCount,
                'value' => $cValue,
                'percentage' => $totalItemsCount > 0 ? round(($cCount / $totalItemsCount) * 100, 1) : 0,
            ];
        }

        AuditTrailService::log('print', 'sarpras', 'Mencetak Buku Induk Inventaris (BII)');

        return view('sarpras.bibi.print', compact(
            'school',
            'assets',
            'grandTotal',
            'totalQuantity',
            'kibCategories',
            'selectedYear',
            'selectedKib',
            'selectedCondition',
            'sortBy',
            'conditionSummary',
            'totalItemsCount',
            'totalItemsValue'
        ));
    }
}
