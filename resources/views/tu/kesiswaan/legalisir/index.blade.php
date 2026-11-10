@extends('layouts.app')

@section('title', 'Administrasi Legalisir Dokumen Siswa - Tata Usaha')

@section('content')
<div class="space-y-6">
    <!-- Header Hero -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-600 via-orange-600 to-indigo-700 p-6 sm:p-8 text-white shadow-xl shadow-orange-500/10">
        <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-amber-100 mb-3 border border-white/20">
                    <a href="{{ route('tu.kesiswaan.index') }}" class="hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Adm. Kesiswaan</span>
                    </a>
                    <span>&bull;</span>
                    <span>Layanan Legalisir Dokumen</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Administrasi Legalisir Dokumen Siswa</h1>
                <p class="text-amber-100 text-sm mt-1 max-w-xl">
                    Pencatatan permohonan legalisir Ijazah, Rapor, Piagam Prestasi, dan Transkrip Siswa/Alumni terintegrasi dengan penerbitan resi tanda terima resmi dan buku agenda ekspedisi.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-slate-950 font-black text-xs shadow-lg shadow-black/10 hover:bg-amber-50 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Permohonan Baru</span>
                </button>
                <a href="{{ route('tu.kesiswaan.legalisir.print_book', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/30 hover:bg-white/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Buku Agenda</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-900 dark:text-emerald-200 text-sm flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:underline">Tutup</button>
        </div>
    @endif

    <!-- Statistik Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Permohonan</span>
            <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($stats['total']) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Semua Berkas</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">Menunggu / Diajukan</span>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['diajukan']) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Perlu Verifikasi Fisik</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-sky-600 dark:text-sky-400">Sedang Diverifikasi</span>
            <div class="text-2xl font-black text-sky-600 dark:text-sky-400 mt-1">{{ number_format($stats['diverifikasi']) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Proses TTD & Stempel</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Selesai Legalisir</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['selesai']) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Siap Diambil / Tuntas</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs col-span-2 sm:col-span-1">
            <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">Ditolak / Batal</span>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($stats['ditolak']) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Berkas Tidak Sesuai</div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
        <form action="{{ route('tu.kesiswaan.legalisir.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                <select name="status" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    <option value="">-- Semua Status --</option>
                    <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan (Antrean)</option>
                    <option value="diverifikasi" {{ request('status') === 'diverifikasi' ? 'selected' : '' }}>Diverifikasi (Proses)</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Dokumen</label>
                <select name="document_type" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    <option value="">-- Semua Dokumen --</option>
                    <option value="Ijazah SMA" {{ request('document_type') === 'Ijazah SMA' ? 'selected' : '' }}>Ijazah SMA</option>
                    <option value="Rapor Kurnas Lengkap" {{ request('document_type') === 'Rapor Kurnas Lengkap' ? 'selected' : '' }}>Rapor Kurnas Lengkap</option>
                    <option value="Rapor Per Semester" {{ request('document_type') === 'Rapor Per Semester' ? 'selected' : '' }}>Rapor Per Semester</option>
                    <option value="Piagam Prestasi / Sertifikat" {{ request('document_type') === 'Piagam Prestasi / Sertifikat' ? 'selected' : '' }}>Piagam Prestasi / Sertifikat</option>
                    <option value="Surat Keterangan Pengganti Ijazah" {{ request('document_type') === 'Surat Keterangan Pengganti Ijazah' ? 'selected' : '' }}>SK Pengganti Ijazah</option>
                    <option value="Transkrip Nilai Kelulusan" {{ request('document_type') === 'Transkrip Nilai Kelulusan' ? 'selected' : '' }}>Transkrip Nilai Kelulusan</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Pemohon / No. Resi / NISN</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama pemohon, nomor resi, NISN..." class="flex-1 text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition">
                        Cari
                    </button>
                    @if(request()->hasAny(['status', 'document_type', 'search']))
                        <a href="{{ route('tu.kesiswaan.legalisir.index') }}" class="px-3 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-200 transition flex items-center">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Data Legalisir -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 dark:text-white">Buku Kendali & Permohonan Legalisir</h3>
                <p class="text-xs text-slate-400 mt-0.5">Menampilkan seluruh riwayat permohonan legalisir dokumen peserta didik & alumni</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 font-bold uppercase tracking-wider">
                        <th class="p-4">No. Resi / Agenda</th>
                        <th class="p-4">Pemohon & Siswa</th>
                        <th class="p-4">Dokumen & Jumlah</th>
                        <th class="p-4">Keperluan & Kontak</th>
                        <th class="p-4">Status & Verifikator</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-200">
                    @forelse($legalizations as $item)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <td class="p-4">
                                <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $item->receipt_number }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $item->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $item->applicant_name }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                    NISN: <span class="font-mono font-medium">{{ $item->nisn ?: '-' }}</span>
                                    @if($item->graduation_year)
                                        &bull; Lulus: <span class="font-semibold">{{ $item->graduation_year }}</span>
                                    @endif
                                    @if($item->student?->schoolClass)
                                        &bull; Kelas: <span class="font-semibold">{{ $item->student->schoolClass->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 font-bold text-[11px] border border-sky-200/60 dark:border-sky-800/50">
                                    {{ $item->document_type }}
                                </span>
                                <div class="text-[11px] text-slate-600 dark:text-slate-400 mt-1 font-semibold">
                                    {{ $item->copies_count }} Lembar
                                </div>
                            </td>
                            <td class="p-4 max-w-xs">
                                <div class="truncate font-medium text-slate-800 dark:text-slate-200" title="{{ $item->purpose }}">
                                    {{ $item->purpose ?: '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    WA/Telp: <span class="font-mono">{{ $item->phone_number ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="p-4">
                                @if($item->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Selesai
                                    </span>
                                @elseif($item->status === 'diverifikasi')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-sky-500/15 text-sky-700 dark:text-sky-300 border border-sky-500/30">
                                        <svg class="w-3 h-3 text-sky-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Diverifikasi
                                    </span>
                                @elseif($item->status === 'ditolak')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30">
                                        <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Diajukan
                                    </span>
                                @endif

                                @if($item->verifier)
                                    <div class="text-[10px] text-slate-400 mt-1 truncate" title="Diverifikasi oleh {{ $item->verifier->name }}">
                                        Oleh: {{ $item->verifier->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Cetak Tanda Terima / Resi -->
                                    <a href="{{ route('tu.kesiswaan.legalisir.print_receipt', $item) }}" target="_blank" class="p-2 rounded-xl text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 transition" title="Cetak Resi Tanda Terima">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>

                                    <!-- Tombol Ubah Status Cepat -->
                                    <button type="button" onclick="openStatusModal({{ $item->id }}, '{{ $item->receipt_number }}', '{{ $item->status }}', '{{ addslashes($item->notes ?? '') }}')" class="p-2 rounded-xl text-sky-600 hover:bg-sky-50 dark:hover:bg-slate-800 transition" title="Ubah Status Verifikasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    </button>

                                    <!-- Hapus Permohonan -->
                                    <form action="{{ route('tu.kesiswaan.legalisir.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus data legalisir {{ $item->receipt_number }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition" title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400">
                                Belum ada berkas permohonan legalisir. Klik tombol "Permohonan Baru" untuk mencatat agenda legalisir dokumen siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($legalizations->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $legalizations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Permohonan Legalisir Baru -->
<div id="create-modal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-slate-800 my-8">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-5">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Pencatatan Permohonan Legalisir Baru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Entri data pemohon legalisir dokumen siswa atau alumni</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form action="{{ route('tu.kesiswaan.legalisir.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Pilihan Siswa Aktif Autocomplete -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih dari Master Siswa Aktif (Opsional)</label>
                <select id="student-picker" onchange="autoFillStudent(this)" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    <option value="">-- Ketik manual jika Alumni, atau pilih Siswa Aktif --</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}" data-name="{{ $st->name }}" data-nisn="{{ $st->nisn }}" data-nis="{{ $st->nis }}" data-class="{{ $st->schoolClass?->name }}">
                            {{ $st->name }} ({{ $st->schoolClass?->name ?? 'Siswa' }}) - NISN: {{ $st->nisn }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="student_id" id="form-student-id">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Pemohon <span class="text-rose-500">*</span></label>
                    <input type="text" name="applicant_name" id="form-name" required placeholder="Nama siswa / alumni" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">NISN Pemohon</label>
                    <input type="text" name="nisn" id="form-nisn" placeholder="10 Digit NISN" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Lulus / Angkatan</label>
                    <input type="text" name="graduation_year" id="form-grad" placeholder="Contoh: 2024 / Siswa Aktif" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WA / Telp Pemohon</label>
                    <input type="text" name="phone_number" id="form-phone" placeholder="Contoh: 08123456789" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Dokumen <span class="text-rose-500">*</span></label>
                    <select name="document_type" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="Ijazah SMA">Ijazah SMA</option>
                        <option value="Rapor Kurnas Lengkap">Rapor Kurnas Lengkap</option>
                        <option value="Rapor Per Semester">Rapor Per Semester</option>
                        <option value="Piagam Prestasi / Sertifikat">Piagam Prestasi / Sertifikat</option>
                        <option value="Surat Keterangan Pengganti Ijazah">Surat Keterangan Pengganti Ijazah</option>
                        <option value="Transkrip Nilai Kelulusan">Transkrip Nilai Kelulusan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jumlah (Lembar) <span class="text-rose-500">*</span></label>
                    <input type="number" name="copies_count" min="1" max="50" value="5" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Keperluan Legalisir</label>
                <input type="text" name="purpose" placeholder="Contoh: Seleksi SNBP / SNBT PTN, Pendaftaran TNI/Polri, Melamar Kerja" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan berkas, nomor ijazah asli, dsb." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition shadow-md">Simpan Permohonan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Status Legalisir -->
<div id="status-modal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Perbarui Status Legalisir</h3>
                <p id="modal-receipt-label" class="text-xs font-mono text-indigo-600 dark:text-indigo-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeStatusModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form id="status-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Dokumen <span class="text-rose-500">*</span></label>
                <select name="status" id="modal-status-select" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    <option value="diajukan">Diajukan (Menunggu Berkas Asli)</option>
                    <option value="diverifikasi">Diverifikasi (Sedang TTD & Stempel)</option>
                    <option value="selesai">Selesai (Siap Diambil)</option>
                    <option value="ditolak">Ditolak / Batal</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Verifikasi</label>
                <textarea name="notes" id="modal-notes" rows="3" placeholder="Catatan untuk pemohon / status berkas..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 transition">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('create-modal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('create-modal').classList.add('hidden');
    }

    function autoFillStudent(select) {
        const option = select.options[select.selectedIndex];
        if (!option || !option.value) {
            document.getElementById('form-student-id').value = '';
            return;
        }
        document.getElementById('form-student-id').value = option.value;
        document.getElementById('form-name').value = option.dataset.name || '';
        document.getElementById('form-nisn').value = option.dataset.nisn || '';
        document.getElementById('form-grad').value = option.dataset.class ? 'Kelas ' + option.dataset.class : 'Siswa Aktif';
    }

    function openStatusModal(id, receipt, status, notes) {
        document.getElementById('status-form').action = `/tu/kesiswaan/legalisir/${id}/status`;
        document.getElementById('modal-receipt-label').textContent = receipt;
        document.getElementById('modal-status-select').value = status;
        document.getElementById('modal-notes').value = notes || '';
        document.getElementById('status-modal').classList.remove('hidden');
    }
    function closeStatusModal() {
        document.getElementById('status-modal').classList.add('hidden');
    }
</script>
@endpush

