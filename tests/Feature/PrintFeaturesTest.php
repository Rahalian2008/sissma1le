<?php

use App\Models\BkSociometry;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\SchoolClass;
use App\Models\User;

test('bk sociometry print page returns 200 and has kepala sekolah on the left', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $class = SchoolClass::firstOrCreate(['name' => 'X-TEST-PRINT'], ['grade' => 10]);
    $sociometry = BkSociometry::create([
        'class_id' => $class->id,
        'title' => 'Sosiometri Uji Coba Cetak',
        'date' => now()->toDateString(),
        'theme' => 'Kerja Sama Kelompok',
        'counselor_id' => $admin->id,
        'status' => 'AKTIF',
    ]);

    $response = $this->actingAs($admin)->get(route('bk.sociometry.print', $sociometry));
    $response->assertStatus(200);
    $response->assertSee('LAPORAN HASIL ASESMEN SOSIOMETRI');
    $response->assertSee('Mengetahui');
    $response->assertSee('Kepala');
});

test('library print member card returns 200', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $member = LibraryMember::firstOrCreate(
        ['member_code' => 'TEST-MEM-001'],
        [
            'name' => 'Siswa Test Member',
            'member_type' => 'siswa',
            'identity_number' => '1234567890',
            'joined_at' => now(),
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($admin)->get(route('library.members.print_card', $member));
    $response->assertStatus(200);
    $response->assertSee('KARTU ANGGOTA PERPUSTAKAAN');
    $response->assertSee('TEST-MEM-001');
    $response->assertSee('Kepala Sekolah');
});

test('library print loan receipt returns 200', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $member = LibraryMember::firstOrCreate(
        ['member_code' => 'TEST-MEM-002'],
        [
            'name' => 'Siswa Test Member 2',
            'member_type' => 'siswa',
            'identity_number' => '1234567891',
            'joined_at' => now(),
            'is_active' => true,
        ]
    );

    $book = LibraryBook::firstOrCreate(
        ['book_code' => 'TEST-BK-001'],
        [
            'title' => 'Buku Uji Coba Peminjaman',
            'author' => 'Penulis Uji Coba',
            'publisher' => 'Penerbit Uji Coba',
            'total_copies' => 5,
            'available_copies' => 4,
            'is_active' => true,
        ]
    );

    $loan = LibraryLoan::firstOrCreate(
        ['loan_code' => 'TEST-LN-001'],
        [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'user_id' => $admin->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'DIPINJAM',
        ]
    );

    $response = $this->actingAs($admin)->get(route('library.loans.print_receipt', $loan));
    $response->assertStatus(200);
    $response->assertSee('BUKTI TRANSAKSI PEMINJAMAN KOLEKSI PUSTAKA');
    $response->assertSee('TEST-LN-001');
    $response->assertSee('Buku Uji Coba Peminjaman');
});

test('library print clearance certificate returns 200', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_test@sman1lengkong.sch.id'],
        ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true]
    );

    $member = LibraryMember::firstOrCreate(
        ['member_code' => 'TEST-MEM-003'],
        [
            'name' => 'Siswa Bebas Pustaka',
            'member_type' => 'siswa',
            'identity_number' => '1234567892',
            'joined_at' => now(),
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($admin)->get(route('library.members.clearance', $member));
    $response->assertStatus(200);
    $response->assertSee('SURAT KETERANGAN BEBAS PERPUSTAKAAN');
    $response->assertSee('Siswa Bebas Pustaka');
    $response->assertSee('Kepala');
});
