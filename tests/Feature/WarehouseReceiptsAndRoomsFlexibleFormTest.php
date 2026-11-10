<?php

use App\Models\Room;
use App\Models\User;

test('sarpras rooms index loads successfully and provides teacher options', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $response = $this->actingAs($admin)->get(route('sarpras.rooms.index'));
    $response->assertStatus(200);
    $response->assertViewHas('teachers');
    $response->assertViewHas('staff');
    $response->assertViewHas('allEmployees');

    // Default rooms should now exist
    expect(Room::count())->toBeGreaterThan(0);
});

test('sarpras rooms can be created via json ajax request for inline room creation', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $testCode = 'R-TEST-'.rand(1000, 9999);
    $response = $this->actingAs($admin)->postJson(route('sarpras.rooms.store'), [
        'code' => $testCode,
        'name' => 'Ruang Laboratorium Bahasa Testing',
        'capacity' => 32,
        'condition' => 'Baik',
        'person_in_charge' => 'Bambang Sudarsono, M.Pd.',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);
    $response->assertJsonPath('room.code', $testCode);

    expect(Room::where('code', $testCode)->exists())->toBeTrue();
});

test('warehouse receipts page loads successfully with rooms and funding sources', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $response = $this->actingAs($admin)->get(route('warehouse.receipts.index'));
    $response->assertStatus(200);
    $response->assertViewHas('rooms');
    $response->assertViewHas('fundingSources');
    $response->assertViewHas('units');
});

test('warehouse receipt for barang inventaris can be saved without room_id and without sim_bmd_code', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $itemName = 'Laptop Lenovo ThinkBook Core i5 '.rand(100, 999);

    $response = $this->actingAs($admin)->post(route('warehouse.receipts.store'), [
        'item_type' => 'inventaris',
        'good_name' => $itemName,
        'kib_type' => 'KIB B',
        'sim_bmd_code' => '', // Empty: system should auto-generate Permendagri 108 code
        'room_id' => '',      // Empty: system should auto-allocate to Transit Room
        'initial_condition' => 'Baik',
        'brand_model' => 'Lenovo ThinkBook 14 Gen 4',
        'serial_number' => 'SN-THINK-'.rand(10000, 99999),
        'specification' => 'Core i5-1235U, RAM 16GB, SSD 512GB, 14 inch IPS FHD',
        'quantity' => 1,
        'unit' => 'Unit',
        'unit_price' => 11500000,
        'receipt_date' => now()->format('Y-m-d'),
        'supplier_name' => 'CV. Mitra Prima Teknologi',
        'funding_source' => 'BOS Reguler',
        'notes' => 'Pengadaan Laptop Pembelajaran Digital',
    ]);

    $response->assertRedirect(route('warehouse.receipts.index'));
    $response->assertSessionHas('success');
});

test('warehouse receipt for barang habis pakai (ATK) via quick preset can be saved smoothly', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    $response = $this->actingAs($admin)->post(route('warehouse.receipts.store'), [
        'item_type' => 'habis_pakai',
        'good_id' => '',
        'new_bhp_code' => 'BHP-HVS-'.rand(100, 999),
        'new_bhp_name' => 'Kertas HVS A4 80gr Sinar Dunia',
        'new_bhp_category' => 'Alat Tulis Kantor (ATK)',
        'new_bhp_unit' => 'Rim',
        'quantity' => 10,
        'unit_price' => 55000,
        'receipt_date' => now()->format('Y-m-d'),
        'supplier_name' => 'Toko Buku & ATK Berkah',
        'funding_source' => 'BOS Reguler',
        'notes' => 'Persediaan ATK Ujian Sekolah',
    ]);

    $response->assertRedirect(route('warehouse.receipts.index'));
    $response->assertSessionHas('success');
});
