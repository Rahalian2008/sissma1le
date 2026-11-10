<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\Good;
use App\Models\SchoolIdentity;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BukuIndukBhpController extends Controller
{
    /**
     * Tampilkan Buku Induk Barang Habis Pakai / Non-Inventaris (Akun 1.1.7)
     */
    public function index(Request $request): View
    {
        $school = SchoolIdentity::first() ?? new SchoolIdentity;
        $search = $request->query('search');
        $selectedCategory = $request->query('category');
        $selectedLocation = $request->query('storage_location');
        $selectedStatus = $request->query('status', 'all');
        $sortBy = $request->query('sort_by', 'default');

        $query = Good::with(['receiptItems.receipt', 'issuanceItems.issuance'])
            ->where(function ($q) {
                $q->where('item_type', 'habis_pakai')
                    ->orWhereNull('item_type')
                    ->orWhere('code', 'like', '1.1.7%');
            });

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('sim_bmd_code', 'like', "%{$search}%")
                    ->orWhere('storage_location', 'like', "%{$search}%");
            });
        }

        if (! empty($selectedCategory) && $selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }

        if (! empty($selectedLocation) && $selectedLocation !== 'all') {
            $query->where('storage_location', $selectedLocation);
        }

        if ($selectedStatus === 'aman') {
            $query->where('current_stock', '>', 0)->whereColumn('current_stock', '>', 'minimum_stock');
        } elseif ($selectedStatus === 'menipis') {
            $query->where('current_stock', '>', 0)->whereColumn('current_stock', '<=', 'minimum_stock');
        } elseif ($selectedStatus === 'habis') {
            $query->where('current_stock', '<=', 0);
        }

        $allGoods = $query->get();

        // Hitung akumulasi masuk, keluar, harga estimasi, dan nilai persediaan per item
        $decoratedGoods = $allGoods->map(function (Good $good) {
            $totalReceived = (int) $good->receiptItems->sum('quantity');
            $totalIssued = (int) $good->issuanceItems->sum('quantity');
            $latestPrice = (float) ($good->receiptItems->sortByDesc('created_at')->first()?->unit_price ?? 0);

            // Jika harga belum pernah tercatat di receipt, gunakan estimasi wajar per kategori
            if ($latestPrice <= 0) {
                $latestPrice = static::estimateDefaultUnitPrice($good);
            }

            $currentStock = (int) $good->current_stock;
            $inventoryValue = $currentStock * $latestPrice;

            $status = 'Aman';
            if ($currentStock <= 0) {
                $status = 'Habis';
            } elseif ($currentStock <= $good->minimum_stock) {
                $status = 'Menipis';
            }

            $good->total_received = $totalReceived;
            $good->total_issued = $totalIssued;
            $good->initial_stock = max(0, $currentStock + $totalIssued - $totalReceived);
            $good->latest_unit_price = $latestPrice;
            $good->inventory_value = $inventoryValue;
            $good->stock_status = $status;

            return $good;
        });

        // Pengurutan
        if ($sortBy === 'code') {
            $decoratedGoods = $decoratedGoods->sortBy('code')->values();
        } elseif ($sortBy === 'name') {
            $decoratedGoods = $decoratedGoods->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();
        } elseif ($sortBy === 'stock_asc') {
            $decoratedGoods = $decoratedGoods->sortBy('current_stock')->values();
        } elseif ($sortBy === 'stock_desc') {
            $decoratedGoods = $decoratedGoods->sortByDesc('current_stock')->values();
        } elseif ($sortBy === 'value_desc') {
            $decoratedGoods = $decoratedGoods->sortByDesc('inventory_value')->values();
        } elseif ($sortBy === 'status') {
            $statusOrder = ['Habis' => 1, 'Menipis' => 2, 'Aman' => 3];
            $decoratedGoods = $decoratedGoods->sortBy(fn ($g) => $statusOrder[$g->stock_status] ?? 4)->values();
        } else {
            $decoratedGoods = $decoratedGoods->sortBy(fn ($g) => $g->category.'_'.$g->name)->values();
        }

        // Paginasi manual untuk koleksi yang didekorasi
        $page = (int) $request->query('page', 1);
        $perPage = 20;
        $total = $decoratedGoods->count();
        $paginatedGoods = new LengthAwarePaginator(
            $decoratedGoods->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Statistik Keseluruhan
        $allBaseGoods = Good::with('receiptItems')->get();
        $totalItemsCount = $allBaseGoods->count();
        $totalStockQty = (int) $allBaseGoods->sum('current_stock');

        $totalInventoryValue = (float) $allBaseGoods->sum(function (Good $g) {
            $price = (float) ($g->receiptItems->sortByDesc('created_at')->first()?->unit_price ?? 0);
            if ($price <= 0) {
                $price = static::estimateDefaultUnitPrice($g);
            }

            return (int) $g->current_stock * $price;
        });

        $stockStats = [
            'aman' => $allBaseGoods->filter(fn ($g) => $g->current_stock > $g->minimum_stock && $g->current_stock > 0)->count(),
            'menipis' => $allBaseGoods->filter(fn ($g) => $g->current_stock <= $g->minimum_stock && $g->current_stock > 0)->count(),
            'habis' => $allBaseGoods->filter(fn ($g) => $g->current_stock <= 0)->count(),
        ];

        // Daftar kategori & lokasi unik untuk dropdown filter
        $categories = Good::select('category')->distinct()->pluck('category')->filter()->values();
        if ($categories->isEmpty()) {
            $categories = collect(['ATK', 'Tinta & Komputer', 'Barang Cetakan', 'Bahan Kebersihan', 'Bahan Praktik Lab', 'Obat & Medis UKS']);
        }
        $locations = Good::select('storage_location')->distinct()->pluck('storage_location')->filter()->values();

        AuditTrailService::log('view', 'sarpras', 'Melihat Buku Induk Barang Habis Pakai / Non-Inventaris (BIBHP)');

        return view('sarpras.bibhp.index', compact(
            'school',
            'paginatedGoods',
            'totalItemsCount',
            'totalStockQty',
            'totalInventoryValue',
            'stockStats',
            'categories',
            'locations',
            'search',
            'selectedCategory',
            'selectedLocation',
            'selectedStatus',
            'sortBy'
        ));
    }

    /**
     * Cetak Resmi Dokumen Buku Induk Barang Habis Pakai / Persediaan (BIBHP)
     */
    public function print(Request $request): View
    {
        $school = SchoolIdentity::first() ?? new SchoolIdentity;
        $search = $request->query('search');
        $selectedCategory = $request->query('category');
        $selectedLocation = $request->query('storage_location');
        $selectedStatus = $request->query('status', 'all');
        $sortBy = $request->query('sort_by', 'default');

        $query = Good::with(['receiptItems.receipt', 'issuanceItems.issuance'])
            ->where(function ($q) {
                $q->where('item_type', 'habis_pakai')
                    ->orWhereNull('item_type')
                    ->orWhere('code', 'like', '1.1.7%');
            });

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('sim_bmd_code', 'like', "%{$search}%")
                    ->orWhere('storage_location', 'like', "%{$search}%");
            });
        }

        if (! empty($selectedCategory) && $selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }

        if (! empty($selectedLocation) && $selectedLocation !== 'all') {
            $query->where('storage_location', $selectedLocation);
        }

        if ($selectedStatus === 'aman') {
            $query->where('current_stock', '>', 0)->whereColumn('current_stock', '>', 'minimum_stock');
        } elseif ($selectedStatus === 'menipis') {
            $query->where('current_stock', '>', 0)->whereColumn('current_stock', '<=', 'minimum_stock');
        } elseif ($selectedStatus === 'habis') {
            $query->where('current_stock', '<=', 0);
        }

        $allGoods = $query->get();

        $goods = $allGoods->map(function (Good $good) {
            $totalReceived = (int) $good->receiptItems->sum('quantity');
            $totalIssued = (int) $good->issuanceItems->sum('quantity');
            $latestPrice = (float) ($good->receiptItems->sortByDesc('created_at')->first()?->unit_price ?? 0);

            if ($latestPrice <= 0) {
                $latestPrice = static::estimateDefaultUnitPrice($good);
            }

            $currentStock = (int) $good->current_stock;
            $inventoryValue = $currentStock * $latestPrice;

            $status = 'Aman';
            if ($currentStock <= 0) {
                $status = 'Habis';
            } elseif ($currentStock <= $good->minimum_stock) {
                $status = 'Menipis';
            }

            $good->total_received = $totalReceived;
            $good->total_issued = $totalIssued;
            $good->initial_stock = max(0, $currentStock + $totalIssued - $totalReceived);
            $good->latest_unit_price = $latestPrice;
            $good->inventory_value = $inventoryValue;
            $good->stock_status = $status;

            return $good;
        });

        if ($sortBy === 'code') {
            $goods = $goods->sortBy('code')->values();
        } elseif ($sortBy === 'name') {
            $goods = $goods->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();
        } elseif ($sortBy === 'stock_asc') {
            $goods = $goods->sortBy('current_stock')->values();
        } elseif ($sortBy === 'stock_desc') {
            $goods = $goods->sortByDesc('current_stock')->values();
        } elseif ($sortBy === 'value_desc') {
            $goods = $goods->sortByDesc('inventory_value')->values();
        } elseif ($sortBy === 'status') {
            $statusOrder = ['Habis' => 1, 'Menipis' => 2, 'Aman' => 3];
            $goods = $goods->sortBy(fn ($g) => $statusOrder[$g->stock_status] ?? 4)->values();
        } else {
            $goods = $goods->sortBy(fn ($g) => $g->category.'_'.$g->name)->values();
        }

        $grandTotalQty = (int) $goods->sum('current_stock');
        $grandTotalValue = (float) $goods->sum('inventory_value');
        $totalItemsCount = $goods->count();

        AuditTrailService::log('print', 'sarpras', 'Mencetak Buku Induk Barang Habis Pakai / Non-Inventaris (BIBHP)');

        return view('sarpras.bibhp.print', compact(
            'school',
            'goods',
            'grandTotalQty',
            'grandTotalValue',
            'totalItemsCount',
            'selectedCategory',
            'selectedStatus',
            'sortBy'
        ));
    }

    /**
     * Ekspor Data Buku Induk Barang Habis Pakai ke format CSV / Excel
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $school = SchoolIdentity::first() ?? new SchoolIdentity;
        $goods = Good::with(['receiptItems', 'issuanceItems'])
            ->where(function ($q) {
                $q->where('item_type', 'habis_pakai')
                    ->orWhereNull('item_type')
                    ->orWhere('code', 'like', '1.1.7%');
            })
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $filename = 'buku_induk_barang_habis_pakai_'.date('Ymd_His').'.csv';

        AuditTrailService::log('export', 'sarpras', 'Mengekspor Buku Induk Barang Habis Pakai ke CSV');

        return response()->streamDownload(function () use ($goods, $school) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk Microsoft Excel agar terbaca rapi
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['BUKU INDUK BARANG HABIS PAKAI / NON-INVENTARIS (BARANG PERSEDIAAN)']);
            fputcsv($handle, ['SATUAN PENDIDIKAN: '.strtoupper($school->name ?? 'SMA NEGERI 1 LENGKONG')]);
            fputcsv($handle, ['STANDAR PERATURAN: PERMENDAGRI NO. 108/2016 & BAGAN AKUN STANDAR 1.1.7']);
            fputcsv($handle, ['TANGGAL CETAK: '.date('d/m/Y H:i')]);
            fputcsv($handle, []);

            // Baris Judul Kolom
            fputcsv($handle, [
                'No.',
                'Kode Barang (Permendagri 1.1.7)',
                'Nama Barang Habis Pakai',
                'Kategori',
                'Satuan',
                'Lokasi Gudang',
                'Stok Masuk',
                'Stok Keluar',
                'Sisa Stok Fisik',
                'Stok Minimum',
                'Harga Satuan (Rp)',
                'Total Nilai Persediaan (Rp)',
                'Status Ketersediaan',
            ]);

            $index = 1;
            foreach ($goods as $good) {
                $totalReceived = (int) $good->receiptItems->sum('quantity');
                $totalIssued = (int) $good->issuanceItems->sum('quantity');
                $latestPrice = (float) ($good->receiptItems->sortByDesc('created_at')->first()?->unit_price ?? 0);
                if ($latestPrice <= 0) {
                    $latestPrice = static::estimateDefaultUnitPrice($good);
                }
                $currentStock = (int) $good->current_stock;
                $inventoryValue = $currentStock * $latestPrice;

                $status = 'Aman';
                if ($currentStock <= 0) {
                    $status = 'Habis';
                } elseif ($currentStock <= $good->minimum_stock) {
                    $status = 'Menipis';
                }

                fputcsv($handle, [
                    $index++,
                    $good->code,
                    $good->name,
                    $good->category,
                    $good->unit,
                    $good->storage_location ?? '-',
                    $totalReceived,
                    $totalIssued,
                    $currentStock,
                    $good->minimum_stock,
                    number_format($latestPrice, 0, ',', '.'),
                    number_format($inventoryValue, 0, ',', '.'),
                    $status,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Estimasi harga satuan wajar berdasarkan kategori/nama barang jika belum tercatat di riwayat BAST
     */
    public static function estimateDefaultUnitPrice(Good $good): float
    {
        $name = strtolower($good->name);
        $category = strtolower($good->category);

        if (str_contains($name, 'kertas hvs') || str_contains($name, 'folio')) {
            return 55000;
        }
        if (str_contains($name, 'toner') || str_contains($name, 'cartridge')) {
            return 350000;
        }
        if (str_contains($name, 'tinta') && str_contains($name, 'botol')) {
            return 95000;
        }
        if (str_contains($name, 'spidol') || str_contains($name, 'boardmarker')) {
            return 9500;
        }
        if (str_contains($name, 'pembersih lantai') || str_contains($name, 'karbol')) {
            return 45000;
        }
        if (str_contains($name, 'sabun cuci tangan') || str_contains($name, 'hand soap')) {
            return 32000;
        }
        if (str_contains($name, 'paracetamol') || str_contains($name, 'antasida')) {
            return 12000;
        }
        if (str_contains($name, 'aquades')) {
            return 40000;
        }
        if (str_contains($name, 'kanvas')) {
            return 25000;
        }
        if (str_contains($name, 'lampu led')) {
            return 38000;
        }
        if (str_contains($category, 'kebersihan')) {
            return 25000;
        }
        if (str_contains($category, 'cetakan')) {
            return 15000;
        }
        if (str_contains($category, 'obat') || str_contains($category, 'p3k')) {
            return 18000;
        }

        return 15000;
    }
}
