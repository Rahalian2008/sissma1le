<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sarpras\RoomController;
use App\Models\Archive;
use App\Models\Asset;
use App\Models\Good;
use App\Models\GoodsIssuance;
use App\Models\GoodsIssuanceItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\ReferenceItem;
use App\Models\Room;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use App\Services\SimBmdService;
use App\Services\SipdBmdService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GoodsTransactionController extends Controller
{
    public function receipts(Request $request): View
    {
        RoomController::ensureDefaultRooms();

        $receipts = GoodsReceipt::with(['items.good', 'items.asset.room', 'items.room', 'receiver'])
            ->latest('receipt_date')
            ->paginate(15);
        $goods = Good::orderBy('name')->get();
        $rooms = Room::orderBy('name')->get();
        $sipdBmdCodes = SipdBmdService::getStandardCodes();
        $simBmdCodes = $sipdBmdCodes;
        $kibCategories = SipdBmdService::getKibCategories();

        $fundingSources = ReferenceItem::category('sumber_dana')->get();
        if ($fundingSources->isEmpty()) {
            $fundingSources = collect([
                (object) ['name' => 'BOS Reguler'],
                (object) ['name' => 'BOPD Jawa Barat'],
                (object) ['name' => 'BOS Kinerja'],
                (object) ['name' => 'Komite Sekolah'],
                (object) ['name' => 'DAK Fisik'],
                (object) ['name' => 'Bantuan Pemerintah / Hibah'],
                (object) ['name' => 'Lainnya / Kas Sekolah'],
            ]);
        }

        $units = ReferenceItem::category('satuan')->get();
        if ($units->isEmpty()) {
            $units = collect([
                (object) ['code' => 'UNIT', 'name' => 'Unit'],
                (object) ['code' => 'BH', 'name' => 'Buah'],
                (object) ['code' => 'PCS', 'name' => 'Pcs'],
                (object) ['code' => 'SET', 'name' => 'Set'],
                (object) ['code' => 'RIM', 'name' => 'Rim'],
                (object) ['code' => 'DUS', 'name' => 'Dus / Kotak'],
                (object) ['code' => 'PAK', 'name' => 'Paket'],
                (object) ['code' => 'M2', 'name' => 'M2'],
            ]);
        }

        return view('warehouse.receipts.index', compact('receipts', 'goods', 'rooms', 'sipdBmdCodes', 'simBmdCodes', 'kibCategories', 'fundingSources', 'units'));
    }

    public function storeReceipt(Request $request): RedirectResponse
    {
        $itemType = $request->input('item_type', 'habis_pakai');

        if ($itemType === 'inventaris') {
            if ($request->filled('kib_type')) {
                $cleanKib = strtoupper(trim(str_replace(['KIB', 'kib', '_', '-', ' '], '', (string) $request->input('kib_type'))));
                if (in_array($cleanKib, ['A', 'B', 'C', 'D', 'E', 'F'])) {
                    $request->merge(['kib_type' => $cleanKib]);
                }
            }

            $validated = $request->validate([
                'item_type' => ['required', 'in:habis_pakai,inventaris'],
                'supplier_name' => ['nullable', 'string', 'max:150'],
                'receipt_date' => ['nullable', 'date'],
                'funding_source' => ['nullable', 'string'],
                'notes' => ['nullable', 'string'],
                'good_name' => ['required', 'string', 'max:255'],
                'sim_bmd_code' => ['nullable', 'string', 'max:50'],
                'kib_type' => ['nullable', 'string', 'in:A,B,C,D,E,F'],
                'room_id' => ['nullable', 'exists:rooms,id'],
                'quantity' => ['required', 'integer', 'min:1'],
                'unit_price' => ['nullable', 'numeric', 'min:0'],
                'unit' => ['nullable', 'string', 'max:30'],
                'brand_model' => ['nullable', 'string', 'max:150'],
                'serial_number' => ['nullable', 'string', 'max:100'],
                'specification' => ['nullable', 'string'],
                'condition' => ['nullable', 'string', 'in:Baik,Rusak Ringan,Rusak Sedang,Rusak Berat'],
            ]);

            $validated['receipt_date'] = $validated['receipt_date'] ?? date('Y-m-d');
            $validated['funding_source'] = $validated['funding_source'] ?? 'BOS Reguler';
            $validated['unit_price'] = $validated['unit_price'] ?? 0;
            $validated['unit'] = $validated['unit'] ?? 'Unit';
            $validated['condition'] = $validated['condition'] ?? 'Baik';
        } else {
            $validated = $request->validate([
                'item_type' => ['required', 'in:habis_pakai,inventaris'],
                'supplier_name' => ['nullable', 'string', 'max:150'],
                'receipt_date' => ['nullable', 'date'],
                'funding_source' => ['nullable', 'string'],
                'notes' => ['nullable', 'string'],
                'good_id' => ['nullable', 'exists:goods,id'],
                'new_bhp_code' => ['nullable', 'string', 'max:50'],
                'new_bhp_name' => ['nullable', 'string', 'max:255'],
                'new_bhp_category' => ['nullable', 'string', 'max:100'],
                'new_bhp_unit' => ['nullable', 'string', 'max:30'],
                'quantity' => ['required', 'integer', 'min:1'],
                'unit_price' => ['nullable', 'numeric', 'min:0'],
            ]);

            $validated['receipt_date'] = $validated['receipt_date'] ?? date('Y-m-d');
            $validated['funding_source'] = $validated['funding_source'] ?? 'BOS Reguler';
            $validated['unit_price'] = $validated['unit_price'] ?? 0;

            if (empty($validated['good_id'])) {
                if (empty($validated['new_bhp_name'])) {
                    return back()->withErrors(['good_id' => 'Silakan pilih barang persediaan atau pilih item dari katalog BHP.'])->withInput();
                }

                $bhpCode = ! empty($validated['new_bhp_code']) ? $validated['new_bhp_code'] : 'BHP-'.strtoupper(Str::random(6));
                $good = Good::firstOrCreate(
                    ['code' => $bhpCode],
                    [
                        'name' => $validated['new_bhp_name'],
                        'category' => ! empty($validated['new_bhp_category']) ? $validated['new_bhp_category'] : 'Alat Tulis Kantor (ATK)',
                        'unit' => ! empty($validated['new_bhp_unit']) ? $validated['new_bhp_unit'] : 'Buah',
                        'current_stock' => 0,
                        'minimum_stock' => 5,
                        'storage_location' => 'Gudang Logistik & ATK',
                    ]
                );
                $validated['good_id'] = $good->id;
            }
        }

        $createdAssetCount = 0;
        $kibLabel = '';
        $roomName = '';

        DB::transaction(function () use ($validated, $itemType, &$receipt, &$createdAssetCount, &$kibLabel, &$roomName) {
            $year = date('Y', strtotime($validated['receipt_date']));
            $count = GoodsReceipt::whereYear('receipt_date', $year)->count() + 1;
            $totalPrice = $validated['quantity'] * $validated['unit_price'];

            $receipt = GoodsReceipt::create([
                'receipt_number' => "BM/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT),
                'receipt_date' => $validated['receipt_date'],
                'supplier_name' => $validated['supplier_name'] ?? null,
                'funding_source' => $validated['funding_source'],
                'received_by_user_id' => Auth::id(),
                'total_amount' => $totalPrice,
                'notes' => $validated['notes'],
            ]);

            if ($itemType === 'inventaris') {
                $kibType = $validated['kib_type'] ?? SimBmdService::determineKibType($validated['sim_bmd_code'] ?? null);
                $kibLabel = "KIB {$kibType}";

                // Otomatisasi generate kodefikasi SIPD e-BMD jika kosong
                if (empty($validated['sim_bmd_code'])) {
                    $prefix = match ($kibType) {
                        'A' => '1.3.1.01.01.01',
                        'C' => '1.3.3.01.01.01',
                        'D' => '1.3.4.01.01.01',
                        'E' => '1.3.5.01.01.01',
                        'F' => '1.3.6.01.01.01',
                        default => '1.3.2.10.01.02',
                    };
                    $hash = abs(crc32($validated['good_name'])) % 900 + 100;
                    $validated['sim_bmd_code'] = sprintf('%s.%03d', $prefix, $hash);
                }

                // Alokasi ruangan: jika belum dipilih, arahkan ke Ruang Transit
                $roomId = $validated['room_id'] ?? null;
                $roomName = 'Gudang Transit (Belum Ditempatkan)';
                if ($roomId) {
                    $room = Room::find($roomId);
                    if ($room) {
                        $roomName = $room->name;
                    }
                } else {
                    $transitRoom = Room::firstOrCreate(
                        ['code' => 'R-TRANSIT'],
                        [
                            'name' => 'Gudang Transit & Penerimaan Sarpras',
                            'person_in_charge' => 'Pengelola Logistik',
                            'capacity' => 10,
                            'condition' => 'Baik',
                        ]
                    );
                    $roomId = $transitRoom->id;
                    $roomName = $transitRoom->name;
                }
                $validated['room_id'] = $roomId;

                // Tautkan atau buat master Good berjenis inventaris
                $cleanCode = preg_replace('/[^A-Za-z0-9]/', '', $validated['sim_bmd_code']);
                $good = Good::firstOrCreate(
                    ['code' => "INV-{$cleanCode}"],
                    [
                        'name' => $validated['good_name'],
                        'category' => 'Aset Inventaris',
                        'item_type' => 'inventaris',
                        'kib_type' => $kibType,
                        'sim_bmd_code' => $validated['sim_bmd_code'],
                        'unit' => $validated['unit'] ?? 'Unit',
                        'current_stock' => 0,
                        'minimum_stock' => 0,
                        'storage_location' => $roomName,
                    ]
                );
                $good->increment('current_stock', $validated['quantity']);

                $firstAssetId = null;

                // Daftarkan unit aset fisik ke administrasi KIB-KIR dengan kode SIM BMD
                for ($i = 0; $i < $validated['quantity']; $i++) {
                    $regNumber = SimBmdService::getNextRegisterNumber($validated['sim_bmd_code'], $year);
                    $fullBmdCode = SimBmdService::generateFullBmdCode($validated['sim_bmd_code'], $year, $regNumber);

                    $serial = $validated['serial_number'];
                    if ($serial && $validated['quantity'] > 1) {
                        $serial = "{$serial}-".($i + 1);
                    }

                    $asset = Asset::create([
                        'name' => $validated['good_name'],
                        'code' => $fullBmdCode,
                        'brand_model' => $validated['brand_model'] ?? null,
                        'serial_number' => $serial,
                        'procurement_year' => (int) $year,
                        'funding_source' => $validated['funding_source'],
                        'price' => $validated['unit_price'],
                        'room_id' => $validated['room_id'],
                        'condition' => $validated['condition'],
                        'qr_code_token' => Str::uuid()->toString(),
                        'notes' => "Penerimaan Barang Masuk No. {$receipt->receipt_number}",
                        'kib_type' => $kibType,
                        'sim_bmd_code' => $validated['sim_bmd_code'],
                        'register_number' => $regNumber,
                        'unit' => $validated['unit'] ?? 'Unit',
                        'specification' => $validated['specification'] ?? null,
                        'goods_receipt_id' => $receipt->id,
                    ]);

                    if ($firstAssetId === null) {
                        $firstAssetId = $asset->id;
                    }
                    $createdAssetCount++;
                }

                GoodsReceiptItem::create([
                    'goods_receipt_id' => $receipt->id,
                    'good_id' => $good->id,
                    'item_type' => 'inventaris',
                    'kib_type' => $kibType,
                    'sim_bmd_code' => $validated['sim_bmd_code'],
                    'room_id' => $validated['room_id'],
                    'brand_model' => $validated['brand_model'] ?? null,
                    'serial_number' => $validated['serial_number'] ?? null,
                    'specification' => $validated['specification'] ?? null,
                    'condition' => $validated['condition'],
                    'quantity' => $validated['quantity'],
                    'unit_price' => $validated['unit_price'],
                    'total_price' => $totalPrice,
                    'asset_id' => $firstAssetId,
                ]);

                AuditTrailService::log(
                    'create',
                    'sarpras',
                    "Barang masuk inventaris: {$validated['good_name']} ({$validated['quantity']} {$good->unit}) -> Otomatis masuk {$kibLabel} dan KIR {$roomName} (Kode SIPD e-BMD: {$validated['sim_bmd_code']})"
                );
            } else {
                // Barang Habis Pakai
                GoodsReceiptItem::create([
                    'goods_receipt_id' => $receipt->id,
                    'good_id' => $validated['good_id'],
                    'item_type' => 'habis_pakai',
                    'quantity' => $validated['quantity'],
                    'unit_price' => $validated['unit_price'],
                    'total_price' => $totalPrice,
                ]);

                $good = Good::find($validated['good_id']);
                $good->increment('current_stock', $validated['quantity']);

                AuditTrailService::log(
                    'create',
                    'gudang',
                    "Penerimaan barang habis pakai: {$good->name} (+{$validated['quantity']} {$good->unit})"
                );
            }
        });

        if ($itemType === 'inventaris') {
            return redirect()->route('warehouse.receipts.index')->with(
                'success',
                "Barang Masuk {$receipt->receipt_number} berhasil dibukukan! {$createdAssetCount} unit Barang Inventaris otomatis terdaftar ke {$kibLabel} dan dialokasikan ke KIR {$roomName} dengan kodefikasi SIPD e-BMD."
            );
        }

        return redirect()->route('warehouse.receipts.index')->with('success', "Barang masuk habis pakai berhasil dibukukan dengan nomor {$receipt->receipt_number} dan stok persediaan bertambah.");
    }

    public function issuances(Request $request): View
    {
        $issuances = GoodsIssuance::with(['items.good', 'approver'])
            ->latest('issuance_date')
            ->paginate(15);
        $goods = Good::where('current_stock', '>', 0)->orderBy('name')->get();

        return view('warehouse.issuances.index', compact('issuances', 'goods'));
    }

    public function storeIssuance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:150'],
            'issuance_date' => ['required', 'date'],
            'purpose' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'good_id' => ['nullable', 'exists:goods,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.good_id' => ['required_with:items', 'exists:goods,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
        ]);

        // Normalize items array
        $itemsToIssue = [];
        if (! empty($validated['items'])) {
            foreach ($validated['items'] as $it) {
                if (! empty($it['good_id']) && ! empty($it['quantity'])) {
                    $itemsToIssue[] = [
                        'good_id' => (int) $it['good_id'],
                        'quantity' => (int) $it['quantity'],
                    ];
                }
            }
        } elseif (! empty($validated['good_id']) && ! empty($validated['quantity'])) {
            $itemsToIssue[] = [
                'good_id' => (int) $validated['good_id'],
                'quantity' => (int) $validated['quantity'],
            ];
        }

        if (empty($itemsToIssue)) {
            return back()->with('error', 'Silakan pilih minimal 1 barang untuk dikeluarkan.');
        }

        // Verify stock for all chosen goods
        foreach ($itemsToIssue as $item) {
            $good = Good::find($item['good_id']);
            if (! $good || $good->current_stock < $item['quantity']) {
                $goodName = $good ? $good->name : "Barang ID {$item['good_id']}";
                $avail = $good ? "{$good->current_stock} {$good->unit}" : '0';

                return back()->with('error', "Stok {$goodName} tidak mencukupi. Tersedia: {$avail}, diminta: {$item['quantity']}");
            }
        }

        $issuance = null;
        DB::transaction(function () use ($validated, $itemsToIssue, &$issuance) {
            $year = date('Y');
            $count = GoodsIssuance::whereYear('issuance_date', $year)->count() + 1;

            $issuance = GoodsIssuance::create([
                'issuance_number' => "BK/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT),
                'issuance_date' => $validated['issuance_date'],
                'recipient_name' => $validated['recipient_name'],
                'purpose' => $validated['purpose'],
                'approved_by_user_id' => Auth::id(),
                'notes' => $validated['notes'],
            ]);

            $issuedNames = [];
            foreach ($itemsToIssue as $item) {
                $good = Good::find($item['good_id']);
                GoodsIssuanceItem::create([
                    'goods_issuance_id' => $issuance->id,
                    'good_id' => $good->id,
                    'quantity' => $item['quantity'],
                ]);

                // Decrement stock
                $good->decrement('current_stock', $item['quantity']);
                $issuedNames[] = "{$good->name} ({$item['quantity']} {$good->unit})";
            }

            $namesString = implode(', ', $issuedNames);
            AuditTrailService::log('create', 'gudang', "Pengeluaran barang [Multi-Item]: {$namesString} untuk {$validated['recipient_name']}");
        });

        return back()->with('success', "Pengeluaran barang berhasil dibukukan dengan nomor {$issuance->issuance_number} untuk ".count($itemsToIssue).' jenis barang.');
    }

    /**
     * Cetak Berita Acara Serah Terima (BAST) Penerimaan Barang Pengadaan
     */
    public function printReceipt(GoodsReceipt $receipt): View
    {
        $receipt->load(['items.good', 'items.asset.room', 'items.room', 'receiver']);
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'gudang', "Mencetak BAST Penerimaan Barang No: {$receipt->receipt_number}");

        return view('warehouse.receipts.print', compact('receipt', 'school'));
    }

    /**
     * Otomatis simpan BAST Penerimaan Barang ke Kearsipan Digital
     */
    public function archiveReceipt(GoodsReceipt $receipt): RedirectResponse
    {
        $existing = Archive::where('source_type', 'goods_receipt')
            ->where('source_id', $receipt->id)
            ->first();

        if ($existing) {
            return back()->with('info', "BAST ini sudah diarsipkan ke Kearsipan Digital dengan No. {$existing->archive_number}");
        }

        $year = date('Y', strtotime($receipt->receipt_date));
        $count = Archive::where('year', $year)->count() + 1;
        $archiveNumber = "ARS/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        $supplierLabel = $receipt->supplier_name ?: 'Penyedia / Rekanan';
        $archive = Archive::create([
            'archive_number' => $archiveNumber,
            'title' => "BAST Penerimaan Barang: {$receipt->receipt_number} ({$supplierLabel})",
            'category' => 'Sarpras',
            'classification_code' => '028',
            'origin_unit' => 'Gudang & Sarpras',
            'year' => $year,
            'archive_status' => 'Aktif',
            'retention_period_years' => 5,
            'retention_expire_date' => now()->addYears(5)->toDateString(),
            'location_rack' => 'Lemari BAST Pengadaan & BMD',
            'source_type' => 'goods_receipt',
            'source_id' => $receipt->id,
            'notes' => "Diarsipkan otomatis dari Penerimaan Barang No. {$receipt->receipt_number} - Sumber: {$receipt->funding_source}",
        ]);

        AuditTrailService::log('create', 'kearsipan', "Mengarsipkan BAST Penerimaan Barang {$receipt->receipt_number} ke No. Arsip {$archive->archive_number}");

        return back()->with('success', "BAST Penerimaan Barang berhasil disimpan ke Kearsipan Dokumen Digital dengan No. {$archive->archive_number}");
    }

    /**
     * Cetak Surat Bukti Pengeluaran Barang (SBB / Bon Permintaan Barang Gudang)
     */
    public function printIssuance(GoodsIssuance $issuance): View
    {
        $issuance->load(['items.good', 'approver']);
        $school = SchoolIdentity::current();

        AuditTrailService::log('print', 'gudang', "Mencetak Surat Bukti Barang Keluar No: {$issuance->issuance_number}");

        return view('warehouse.issuances.print', compact('issuance', 'school'));
    }

    public function updateReceipt(Request $request, GoodsReceipt $receipt): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => ['nullable', 'string', 'max:150'],
            'receipt_date' => ['required', 'date'],
            'funding_source' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $receipt->update($validated);

        AuditTrailService::log('update', 'gudang', "Memperbarui data penerimaan barang No: {$receipt->receipt_number}");

        return back()->with('success', "Data penerimaan barang {$receipt->receipt_number} berhasil diperbarui.");
    }

    public function destroyReceipt(GoodsReceipt $receipt): RedirectResponse
    {
        $num = $receipt->receipt_number;

        DB::transaction(function () use ($receipt) {
            // Hapus semua unit aset fisik yang terdaftar dari penerimaan ini
            Asset::where('goods_receipt_id', $receipt->id)->delete();

            foreach ($receipt->items as $item) {
                if ($item->asset_id) {
                    Asset::where('id', $item->asset_id)->delete();
                } elseif ($item->good_id) {
                    $good = Good::find($item->good_id);
                    if ($good) {
                        $good->decrement('current_stock', min($good->current_stock, $item->quantity));
                    }
                }
                $item->delete();
            }

            Archive::where('source_type', 'goods_receipt')->where('source_id', $receipt->id)->delete();
            $receipt->delete();
        });

        AuditTrailService::log('delete', 'gudang', "Menghapus data penerimaan barang No: {$num}");

        return back()->with('success', "Penerimaan barang {$num} berhasil dihapus.");
    }

    public function updateIssuance(Request $request, GoodsIssuance $issuance): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:150'],
            'issuance_date' => ['required', 'date'],
            'purpose' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $issuance->update($validated);

        AuditTrailService::log('update', 'gudang', "Memperbarui pengeluaran barang No: {$issuance->issuance_number}");

        return back()->with('success', "Data pengeluaran barang {$issuance->issuance_number} berhasil diperbarui.");
    }

    public function destroyIssuance(GoodsIssuance $issuance): RedirectResponse
    {
        $num = $issuance->issuance_number;

        DB::transaction(function () use ($issuance) {
            foreach ($issuance->items as $item) {
                $good = Good::find($item->good_id);
                if ($good) {
                    $good->increment('current_stock', $item->quantity);
                }
                $item->delete();
            }

            $issuance->delete();
        });

        AuditTrailService::log('delete', 'gudang', "Menghapus pengeluaran barang No: {$num} (stok barang dikembalikan)");

        return back()->with('success', "Pengeluaran barang {$num} berhasil dihapus dan stok barang dikembalikan.");
    }
}
