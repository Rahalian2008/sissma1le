<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ReferenceItem;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferenceItemController extends Controller
{
    public function index(Request $request): View
    {
        $categories = [
            'agama' => 'Agama',
            'status_siswa' => 'Status Siswa',
            'status_kepegawaian' => 'Status Kepegawaian',
            'pangkat_golongan' => 'Pangkat / Golongan',
            'jabatan' => 'Jabatan Dinas',
            'kelas' => 'Tingkat Kelas',
            'rombel' => 'Rombongan Belajar (Rombel)',
            'jenis_surat' => 'Jenis Surat Dinas',
            'klasifikasi_surat' => 'Klasifikasi Surat / Arsip',
            'jenis_barang' => 'Jenis / Kategori Barang',
            'satuan' => 'Satuan Ukuran',
            'kondisi_barang' => 'Kondisi Fisik Barang/Aset',
            'sumber_dana' => 'Sumber Dana Anggaran',
            'jenis_arsip' => 'Kategori Kearsipan',
        ];

        $currentCategory = $request->query('category', 'pangkat_golongan');
        if (! array_key_exists($currentCategory, $categories)) {
            $currentCategory = 'pangkat_golongan';
        }

        $moduleUsage = [
            'jenis_surat' => [
                'rule' => 'Pergub Jabar No. 30/2018 & Permendagri No. 1/2023 (Tata Naskah Dinas Baku)',
                'target' => 'Persuratan (Surat Masuk/Keluar, SPPD, Template & Dokumen Otomatis)',
            ],
            'klasifikasi_surat' => [
                'rule' => 'Permendagri No. 83 Tahun 2022 (Kode Klasifikasi Arsip SMA Wilayah V)',
                'target' => 'Penomoran Surat Keluar Otomatis, Lembar Disposisi & Kearsipan Digital',
            ],
            'jenis_barang' => [
                'rule' => 'Permendagri No. 47/2021 & No. 108/2016 (SIM-BMD / SIPD e-BMD Jabar)',
                'target' => 'Katalog Gudang Persediaan (BHP), Penerimaan Barang Masuk (BAST), KIB A-F Sarpras, KIR Ruangan & Label Barcode/QR',
            ],
            'satuan' => [
                'rule' => 'Standar Satuan Ukuran Logistik & Perlengkapan Sekolah',
                'target' => 'Barang Gudang Persediaan, Transaksi Penerimaan/Pengeluaran & Sarpras Aset',
            ],
            'kondisi_barang' => [
                'rule' => 'Standar Pengelolaan BMD Permendagri 47/2021 (B, RR, RS, RB, HLG)',
                'target' => 'Kartu Inventaris Barang (KIB A-F), Kartu Inventaris Ruangan (KIR) & Verifikasi Fisik Aset',
            ],
            'sumber_dana' => [
                'rule' => 'Permendikbudristek No. 63/2023 (Juknis BOSP) & Pergub Jabar (BOPD)',
                'target' => 'Buku Kas Umum (BKU), Transaksi Penerimaan Gudang & Pengadaan Sarpras Aset',
            ],
            'status_kepegawaian' => [
                'rule' => 'UU No. 20/2023 (Aparatur Sipil Negara) & Juknis Kepegawaian Disdik Jabar',
                'target' => 'Data Tenaga Pendidik & Kependidikan (PTK), Riwayat DUK & Nominatif Pegawai',
            ],
            'pangkat_golongan' => [
                'rule' => 'PP No. 5/2024 & Perpres No. 11/2024 (Golongan Ruang PNS & PPPK)',
                'target' => 'Data Pegawai PTK, Riwayat Kenaikan Pangkat, KGB & Surat Keputusan',
            ],
            'jabatan' => [
                'rule' => 'Permendikbudristek No. 40/2021 & Struktur Organisasi Satuan Pendidikan Jabar',
                'target' => 'Penugasan Fungsional/Struktural Pegawai, Otomatisasi Wali Kelas & Cetak Dokumen Kedinasan',
            ],
            'status_siswa' => [
                'rule' => 'Juknis Dapodik Kemendikbudristek & Mutasi Peserta Didik',
                'target' => 'Buku Induk Siswa Digital, Klaper, Demografi Siswa & Agenda Pembelajaran Kelas',
            ],
            'kelas' => [
                'rule' => 'Kurikulum Merdeka Kemendikbudristek (Fase E & Fase F Jenjang SMA)',
                'target' => 'Rombongan Belajar, Penempatan Siswa, Presensi & Agenda Pembelajaran Kelas',
            ],
            'rombel' => [
                'rule' => 'Rombongan Belajar SMA Negeri 1 Lemahsugih Wilayah V',
                'target' => 'Distribusi Siswa, Penugasan Wali Kelas Otomatis, Presensi & Cetak Agenda Pembelajaran',
            ],
            'agama' => [
                'rule' => 'Standar Data Pokok Pendidikan (Dapodik) Kemendikbudristek',
                'target' => 'Buku Induk Siswa, Biodata Pegawai PTK & Laporan Demografi Keagamaan',
            ],
            'jenis_arsip' => [
                'rule' => 'UU No. 43/2009 tentang Kearsipan & Pedoman Arsip Digital ANRI',
                'target' => 'Kearsipan Digital Dokumen Sekolah, Retensi Berkas, Lokasi Rak & Verifikasi QR Code',
            ],
        ];

        $items = ReferenceItem::where('category', $currentCategory)
            ->orderBy('sort_order')
            ->get();

        return view('master.references.index', compact('categories', 'currentCategory', 'items', 'moduleUsage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string'],
            'code' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $validated['is_active'] = true;
        $item = ReferenceItem::create($validated);

        AuditTrailService::log('create', 'master_referensi', "Menambah referensi [{$item->category}]: {$item->name}");

        return back()->with('success', 'Data referensi berhasil ditambahkan.');
    }

    public function update(Request $request, ReferenceItem $referenceItem): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $referenceItem->update($validated);

        AuditTrailService::log('update', 'master_referensi', "Memperbarui referensi [{$referenceItem->category}]: {$referenceItem->name}");

        return back()->with('success', 'Data referensi berhasil diperbarui.');
    }

    public function destroy(ReferenceItem $referenceItem): RedirectResponse
    {
        $category = $referenceItem->category;
        $name = $referenceItem->name;
        $referenceItem->delete();

        AuditTrailService::log('delete', 'master_referensi', "Menghapus referensi [{$category}]: {$name}");

        return back()->with('success', 'Data referensi berhasil dihapus.');
    }
}
