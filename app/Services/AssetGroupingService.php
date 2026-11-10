<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Collection;

class GroupedAsset
{
    public int $id;

    public string $name;

    public ?string $code;

    public ?string $sim_bmd_code;

    public string $kib_type;

    public ?string $brand_model;

    public ?string $serial_number;

    public ?string $specification;

    public ?string $funding_source;

    public int $procurement_year;

    public float $price;

    public float $total_price;

    public string $condition;

    public string $unit;

    public ?int $room_id;

    public mixed $room;

    public ?int $goods_receipt_id;

    public mixed $goodsReceipt;

    public int $quantity;

    public string $register_range;

    public string $register_number;

    public ?string $qr_code_token;

    /** @var array<int> */
    public array $asset_ids = [];

    public Asset $first_asset;

    public Collection $items;

    public function __construct(Collection $items)
    {
        $this->items = $items;
        /** @var Asset $first */
        $first = $items->first();
        $this->first_asset = $first;
        $this->id = $first->id;
        $this->name = $first->name;
        $this->code = $first->code;
        $this->sim_bmd_code = $first->sim_bmd_code;
        $this->kib_type = $first->kib_type ?? 'B';
        $this->brand_model = $first->brand_model;
        $this->specification = $first->specification;
        $this->funding_source = $first->funding_source ?? 'BOSP';
        $this->procurement_year = (int) $first->procurement_year;
        $this->price = (float) $first->price;
        $this->condition = $first->condition ?? 'Baik';
        $this->unit = $first->unit ?? 'Unit';
        $this->room_id = $first->room_id;
        $this->room = $first->room;
        $this->goods_receipt_id = $first->goods_receipt_id;
        $this->goodsReceipt = $first->goodsReceipt;
        $this->qr_code_token = $first->qr_code_token;
        $this->quantity = $items->count();
        $this->total_price = (float) $items->sum('price');
        $this->asset_ids = $items->pluck('id')->all();

        // Rentang nomor register (misal: 0001 s.d. 0020 atau 0001)
        $registers = $items->pluck('register_number')->filter()->all();
        $this->register_range = AssetGroupingService::formatRegisterRange($registers, $first->register_number ?? $first->code);
        $this->register_number = $this->register_range;

        // Ringkasan nomor seri pabrik
        $serials = $items->pluck('serial_number')->filter()->unique()->all();
        if (count($serials) === 1) {
            $this->serial_number = reset($serials);
        } elseif (count($serials) > 1) {
            $this->serial_number = count($serials).' No. Seri ('.reset($serials).' s.d. '.end($serials).')';
        } else {
            $this->serial_number = $first->serial_number;
        }
    }

    public function __get(string $name): mixed
    {
        return $this->first_asset->{$name} ?? null;
    }

    public function __call(string $method, array $args): mixed
    {
        return $this->first_asset->{$method}(...$args);
    }
}

class AssetGroupingService
{
    /**
     * Mengelompokkan koleksi aset fisik menjadi entri teragregasi.
     * Barang dari penerimaan yang sama (atau spesifikasi identik) ditampilkan sebagai 1 baris
     * dengan pembeda di jumlah barang dan rentang registrasinya.
     *
     * @param  iterable<Asset>  $assets
     * @return Collection<int, GroupedAsset>
     */
    public static function group(iterable $assets): Collection
    {
        $collection = $assets instanceof Collection ? $assets : collect($assets);

        if ($collection->isEmpty()) {
            return collect();
        }

        $grouped = $collection->groupBy(function (Asset $asset) {
            if ($asset->goods_receipt_id) {
                return 'rcp_'.$asset->goods_receipt_id.'_'.($asset->sim_bmd_code ?: $asset->code).'_'.($asset->room_id ?: 0).'_'.trim(strtolower($asset->name)).'_'.$asset->condition;
            }

            return 'std_'.($asset->sim_bmd_code ?: 'none').'_'.($asset->room_id ?: 0).'_'.trim(strtolower($asset->name)).'_'.$asset->procurement_year.'_'.$asset->kib_type.'_'.$asset->condition.'_'.(int) $asset->price;
        });

        return $grouped->map(function (Collection $items) {
            return new GroupedAsset($items);
        })->values();
    }

    /**
     * Format rentang nomor register (contoh: "0001 s.d. 0020" atau "0001").
     *
     * @param  array<string>  $registers
     */
    public static function formatRegisterRange(array $registers, ?string $fallback = '-'): string
    {
        $clean = array_values(array_filter(array_map('trim', $registers)));

        if (empty($clean)) {
            return $fallback ?: '-';
        }

        if (count($clean) === 1) {
            return $clean[0];
        }

        natsort($clean);
        $clean = array_values($clean);

        $first = reset($clean);
        $last = end($clean);

        if ($first === $last) {
            return $first;
        }

        return "{$first} s.d. {$last}";
    }
}
