@extends('layouts.app')

@section('title', 'Izin Keluar Lingkungan Sekolah (Sementara)')

@section('content')
<div class="space-y-6">
    @include('attendance.partials.nav')

    {{-- Banner Notifikasi Cetak Surat Resmi --}}
    @if(session('print_url'))
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-teal-600 via-emerald-600 to-teal-700 text-white shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center text-2xl shrink-0">
                    📄
                </div>
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white text-teal-900 mb-1">
                        Surat Izin Resmi Diterbitkan
                    </span>
                    <h3 class="font-extrabold text-sm sm:text-base leading-snug">{{ session('success') }}</h3>
                    <p class="text-xs text-teal-100 mt-0.5">Surat Izin Keluar Lingkungan Sekolah siap dicetak lengkap dengan daftar siswa & Kop Sekolah dari Master Data.</p>
                </div>
            </div>
            <a href="{{ session('print_url') }}" target="_blank"
               class="px-5 py-2.5 bg-white hover:bg-teal-50 text-teal-900 font-extrabold rounded-xl text-xs sm:text-sm shadow-md flex items-center gap-2 whitespace-nowrap active:scale-95 transition">
                <span>🖨️ Cetak Surat Sekarang</span>
                <span class="text-base">&nearr;</span>
            </a>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-md bg-gradient-to-tr from-teal-600 to-emerald-500 shadow-teal-500/20">
                <span class="text-2xl">🚪</span>
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Surat Izin Keluar Lingkungan Sekolah (Sementara)
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Layanan resmi permohonan izin meninggalkan area kampus sementara waktu (tugas dinas, lomba, pembelian alat KBM, atau keperluan medis), mendukung delegasi & rombongan lebih dari 1 siswa.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="openRecapSchoolPassModal()"
                class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 transition flex items-center gap-2 shadow-xs cursor-pointer">
                <span>🖨️ Cetak Rekapitulasi Laporan</span>
            </button>
            <button type="button" onclick="openCreateSchoolPassModal()"
                class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-teal-600 hover:bg-teal-700 active:scale-95 text-white transition flex items-center gap-2 shadow-sm shadow-teal-600/30 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Buat Izin Keluar Sekolah</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs sm:text-sm font-medium flex items-center gap-3">
            <span class="text-lg">✅</span>
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 rounded-xl bg-sky-50 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800 text-sky-800 dark:text-sky-200 text-xs sm:text-sm font-medium flex items-center gap-3">
            <span class="text-lg">ℹ️</span>
            <div class="flex-1">{{ session('info') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs sm:text-sm font-medium flex items-center gap-3">
            <span class="text-lg">⚠️</span>
            <div class="flex-1">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Statistic Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Menunggu (Pending)</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($stats['pending']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-500 flex items-center justify-center font-bold text-lg">⏳</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-teal-600 dark:text-teal-400">Sedang Di Luar</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($stats['approved_outside']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950/50 text-teal-500 flex items-center justify-center font-bold text-lg">🏃</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Sudah Kembali</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($stats['returned']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-500 flex items-center justify-center font-bold text-lg">🏫</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400">Hari Ini</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($stats['today']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-500 flex items-center justify-center font-bold text-lg">📅</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('attendance.school-pass.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase text-slate-500 dark:text-slate-400 mb-1">Cari Data</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa, nomor izin, tujuan..."
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-500 dark:text-slate-400 mb-1">Status Izin</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <option value="ALL">Semua Status</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Menunggu (Pending)</option>
                    <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Disetujui (Sedang Di Luar)</option>
                    <option value="RETURNED" {{ request('status') === 'RETURNED' ? 'selected' : '' }}>Sudah Kembali (Returned)</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-500 dark:text-slate-400 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-xs font-bold bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('attendance.school-pass.index') }}" class="px-3 py-2 text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl transition" title="Reset">
                    ↺
                </a>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Surat Izin & Waktu</th>
                        <th class="py-3 px-4">Daftar Siswa</th>
                        <th class="py-3 px-4">Tujuan & Lokasi</th>
                        <th class="py-3 px-4">Status & Kepulangan</th>
                        <th class="py-3 px-4 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($permits as $index => $permit)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 text-center font-semibold text-slate-400">
                                {{ $permits->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-mono font-bold text-slate-900 dark:text-white">{{ $permit->permit_number }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $permit->formatted_date }}
                                </div>
                                <div class="text-[11px] font-semibold text-teal-600 dark:text-teal-400 mt-0.5">
                                    Keluar: {{ $permit->out_time }} WIB
                                    @if($permit->expected_back_time)
                                        <span class="text-slate-400">&bull; Est. Kembali: {{ $permit->expected_back_time }} WIB</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Pemohon: <span class="font-medium">{{ $permit->requester_type }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @foreach($permit->students as $st)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                            <strong>{{ $st->name }}</strong> ({{ $st->schoolClass?->name ?? 'Kelas' }})
                                        </span>
                                    @endforeach
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1">
                                    Total: <strong class="text-teal-600 dark:text-teal-400">{{ $permit->students->count() }} Siswa</strong>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                                    {{ $permit->purpose }}
                                </div>
                                @if($permit->destination)
                                    <div class="text-[11px] font-semibold text-slate-800 dark:text-slate-200 mt-1">
                                        📍 Ke: {{ $permit->destination }}
                                    </div>
                                @endif
                                <div class="text-slate-600 dark:text-slate-300 mt-0.5 line-clamp-2" title="{{ $permit->reason }}">
                                    {{ $permit->reason }}
                                </div>
                                @if($permit->accompanied_by)
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">
                                        Pendamping: <span class="font-medium">{{ $permit->accompanied_by }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div>
                                    @if(is_array($permit->status_badge))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $permit->status_badge['class'] ?? '' }}">
                                            {{ $permit->status_badge['label'] ?? $permit->status }}
                                        </span>
                                    @elseif(is_string($permit->status_badge))
                                        {!! $permit->status_badge !!}
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-slate-100 text-slate-800">
                                            {{ $permit->status }}
                                        </span>
                                    @endif
                                </div>
                                @if($permit->status === 'RETURNED')
                                    <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1">
                                        Kembali: {{ $permit->actual_back_time }} WIB
                                    </div>
                                @elseif($permit->status === 'APPROVED')
                                    <div class="text-[10px] text-amber-600 dark:text-amber-400 mt-1">
                                        Sedang berada di luar sekolah
                                    </div>
                                @elseif($permit->status === 'REJECTED' && $permit->approval_notes)
                                    <div class="text-[10px] text-rose-500 mt-1 italic">
                                        Alasan: {{ $permit->approval_notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    @if(in_array($permit->status, ['APPROVED', 'RETURNED']))
                                        <a href="{{ route('attendance.school-pass.print', $permit->id) }}" target="_blank"
                                            class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/80 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            <span>Cetak Surat</span>
                                        </a>
                                    @endif

                                    @if($isStaff && $permit->status === 'APPROVED' && ! $permit->actual_back_time)
                                        <button type="button"
                                            onclick="openReturnSchoolPassModal('{{ route('attendance.school-pass.returned', $permit->id) }}', '{{ $permit->permit_number }}', '{{ addslashes($permit->students->pluck('name')->join(', ')) }}')"
                                            class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-teal-600 hover:bg-teal-700 text-white transition shadow-xs cursor-pointer" title="Konfirmasi Kembali">
                                            🔙 Sudah Kembali
                                        </button>
                                    @endif

                                    @if($isStaff && $permit->status === 'PENDING')
                                        <button type="button"
                                            onclick="openApproveSchoolPassModal('{{ route('attendance.school-pass.approve', $permit->id) }}', '{{ $permit->permit_number }}')"
                                            class="px-2 py-1 rounded-lg text-[11px] font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-xs cursor-pointer" title="Setujui">
                                            ✓ Setujui
                                        </button>
                                        <button type="button"
                                            onclick="openRejectSchoolPassModal('{{ route('attendance.school-pass.reject', $permit->id) }}', '{{ $permit->permit_number }}')"
                                            class="px-2 py-1 rounded-lg text-[11px] font-bold bg-rose-600 hover:bg-rose-700 text-white transition shadow-xs cursor-pointer" title="Tolak">
                                            ✕ Tolak
                                        </button>
                                    @endif

                                    @if($isStaff || $permit->created_by === auth()->id())
                                        <a href="{{ route('attendance.school-pass.edit', $permit->id) }}"
                                            class="px-2 py-1 rounded-lg text-[11px] font-bold bg-teal-50 hover:bg-teal-100 text-teal-700 dark:bg-teal-950/60 dark:hover:bg-teal-900/80 dark:text-teal-300 border border-teal-200 dark:border-teal-800 transition inline-flex items-center gap-1" title="Edit Data">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route('attendance.school-pass.destroy', $permit->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus surat izin keluar sekolah No. {{ $permit->permit_number }} ini? Data yang dihapus tidak dapat dikembalikan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 rounded-lg text-[11px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 dark:text-rose-300 border border-rose-200 dark:border-rose-800 transition inline-flex items-center gap-1 cursor-pointer" title="Hapus Data">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">📭</div>
                                <p class="font-medium">Belum ada surat izin keluar lingkungan sekolah.</p>
                                <p class="text-[11px] text-slate-400 mt-1">Klik tombol "+ Buat Izin Keluar Sekolah" di atas untuk membuat ajuan permohonan baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($permits->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $permits->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Buat Izin Keluar Sekolah (Bisa Rombongan > 1 Siswa) (Vanilla JS) -->
    <div id="school-pass-create-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 my-6">
            <div class="px-6 py-4 bg-gradient-to-r from-teal-600 to-emerald-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">🚪</span>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base leading-tight">Buat Surat Izin Keluar Lingkungan Sekolah</h3>
                        <p class="text-[11px] text-teal-100">Bisa memilih lebih dari 1 siswa sekaligus (delegasi/kelompok)</p>
                    </div>
                </div>
                <button type="button" onclick="closeCreateSchoolPassModal()" class="text-white/80 hover:text-white text-2xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('attendance.school-pass.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Pemilihan Siswa (Multi-Select / Checkbox) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Pilih Siswa yang Diberi Izin <span class="text-rose-500">* (Bisa > 1 Siswa)</span>
                        </label>
                        <span class="text-[11px] font-bold text-teal-600 dark:text-teal-400">
                            <span id="sp-index-selected-counter">0</span> Siswa Dipilih
                        </span>
                    </div>

                    <!-- Search Box Filter Siswa -->
                    <div class="relative mb-2">
                        <input type="text" id="sp-index-search-input" oninput="filterSpIndexStudents(this.value)" placeholder="Cari nama, NIS, atau kelas siswa..."
                            class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-teal-500">
                        <span class="absolute left-2.5 top-2 text-slate-400">🔍</span>
                    </div>

                    <!-- Container Chip Siswa Terpilih -->
                    <div id="sp-index-chips-container" class="flex flex-wrap gap-1 mb-2 max-h-24 overflow-y-auto"></div>

                    <!-- Scrollable Student List with Checkboxes -->
                    <div class="max-h-48 overflow-y-auto border border-slate-200 dark:border-slate-700 rounded-xl p-2 divide-y divide-slate-100 dark:divide-slate-800 bg-slate-50/50 dark:bg-slate-800/40 scrollbar-thin">
                        @foreach($allStudents as $st)
                            <label class="sp-index-student-row flex items-center justify-between p-2 hover:bg-slate-100 dark:hover:bg-slate-700/60 rounded-lg cursor-pointer transition text-xs"
                                   data-search="{{ strtolower($st->name . ' ' . $st->nis . ' ' . ($st->schoolClass?->name ?? '')) }}">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="student_ids[]" value="{{ $st->id }}"
                                        data-name="{{ $st->name }}" data-class="{{ $st->schoolClass?->name ?? 'Tanpa Kelas' }}"
                                        onchange="updateSpIndexChips()"
                                        {{ ($isStudent && $student && $student->id === $st->id) ? 'checked' : '' }}
                                        class="sp-index-checkbox rounded text-teal-600 focus:ring-teal-500 w-4 h-4 cursor-pointer">
                                    <div>
                                        <p class="text-xs font-bold text-slate-800 dark:text-white">{{ $st->name }}</p>
                                        <p class="text-[10px] text-slate-400">NIS: {{ $st->nis }} &bull; Kelas: {{ $st->schoolClass?->name ?? 'Tanpa Kelas' }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-semibold text-slate-400">{{ $st->gender === 'L' ? 'L' : 'P' }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Centang satu atau beberapa siswa yang ikut keluar lingkungan sekolah dalam rombongan ini.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Izin <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Keluar <span class="text-rose-500">*</span></label>
                        <input type="time" name="out_time" required value="{{ now('Asia/Jakarta')->format('H:i') }}"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Estimasi Jam Kembali</label>
                        <input type="time" name="expected_back_time"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Keperluan / Tujuan <span class="text-rose-500">*</span></label>
                        <select name="purpose" required
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="Membeli Perlengkapan KBM / Praktikum">Membeli Alat / Bahan KBM / Praktikum</option>
                            <option value="Tugas Dinas / OSIS / MPK / Ekstrakurikuler">Tugas Dinas / OSIS / MPK / Ekstrakurikuler</option>
                            <option value="Mengikuti Lomba / Festival / Kompetisi">Mengikuti Lomba / Pertandingan Resmi</option>
                            <option value="Pemeriksaan Medis / Rujukan Puskesmas">Pemeriksaan Medis / Puskesmas</option>
                            <option value="Keperluan Administrasi / Cetak Dokumen">Cetak / Fotokopi Dokumen Sekolah</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat / Lokasi Tujuan</label>
                        <input type="text" name="destination" placeholder="Contoh: Puskesmas Lengkong / Toko ATK..."
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pendamping (Jika Ada)</label>
                        <input type="text" name="accompanied_by" placeholder="Contoh: Guru Pembina, Satpam, Mandiri"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_returning" value="1" checked
                                class="rounded text-teal-600 focus:ring-teal-500 w-4 h-4">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Siswa Akan Kembali Lagi ke Sekolah Hari Ini
                            </span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Alasan Lengkap <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="3" required placeholder="Jelaskan secara spesifik urgensi dan alasan izin keluar lingkungan sekolah..."
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:outline-none"></textarea>
                </div>

                @if($isStaff)
                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="direct_approve" value="1" checked
                                class="rounded text-teal-600 focus:ring-teal-500 w-4 h-4">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Langsung Setujui & Terbitkan Surat Izin Resmi (Guru Piket / TU Piket / Wali Kelas)
                            </span>
                        </label>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="createModal = false"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-xs font-bold bg-teal-600 hover:bg-teal-700 active:scale-95 text-white rounded-xl transition shadow-sm shadow-teal-600/30">
                        Simpan & Terbitkan Izin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Siswa Sudah Kembali ke Sekolah (Vanilla JS) -->
    <div id="school-pass-return-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl p-6 text-center animate-in fade-in zoom-in-95 duration-200">
            <div class="w-12 h-12 rounded-full bg-teal-100 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center mx-auto text-2xl font-bold mb-3">
                🏫
            </div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Konfirmasi Siswa Sudah Kembali</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Catat kepulangan siswa kembali ke lingkungan sekolah untuk surat izin <strong class="text-slate-800 dark:text-white" id="return-school-pass-number"></strong>.
            </p>
            <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-1 font-semibold" id="return-school-pass-names"></p>

            <form id="return-school-pass-form" action="" method="POST" class="mt-4 space-y-3 text-left">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Tiba Kembali di Sekolah <span class="text-rose-500">*</span></label>
                    <input type="time" name="actual_back_time" required value="{{ now('Asia/Jakarta')->format('H:i') }}"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Contoh: Kembali dengan aman dan tepat waktu"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeReturnSchoolPassModal()"
                        class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white rounded-xl transition shadow-sm cursor-pointer">
                        Simpan Konfirmasi Kembali
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Persetujuan (Approve) (Vanilla JS) -->
    <div id="school-pass-approve-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl p-6 text-center animate-in fade-in zoom-in-95 duration-200">
            <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center mx-auto text-2xl font-bold mb-3">
                ✓
            </div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Setujui Izin Keluar Sekolah</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin menyetujui izin keluar sekolah untuk nomor <strong class="text-slate-800 dark:text-white" id="approve-school-pass-number"></strong>?
            </p>

            <form id="approve-school-pass-form" action="" method="POST" class="mt-4 space-y-3">
                @csrf
                <input type="text" name="notes" placeholder="Catatan persetujuan (opsional)"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white">

                <div class="flex items-center justify-center gap-2 pt-2">
                    <button type="button" onclick="closeApproveSchoolPassModal()"
                        class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition shadow-sm cursor-pointer">
                        Ya, Setujui & Terbitkan Surat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tolak (Reject) (Vanilla JS) -->
    <div id="school-pass-reject-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl p-6 text-center animate-in fade-in zoom-in-95 duration-200">
            <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center mx-auto text-2xl font-bold mb-3">
                ✕
            </div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tolak Izin Keluar Sekolah</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Tolak permohonan izin keluar untuk surat <strong class="text-slate-800 dark:text-white" id="reject-school-pass-number"></strong>.
            </p>

            <form id="reject-school-pass-form" action="" method="POST" class="mt-4 space-y-3 text-left">
                @csrf
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="reason" required rows="3" placeholder="Tuliskan alasan penolakan izin..."
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white"></textarea>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeRejectSchoolPassModal()"
                        class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition shadow-sm cursor-pointer">
                        Konfirmasi Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Filter Cetak Rekapitulasi Laporan -->
<div id="school-pass-recap-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold">🖨️</span>
                <h3 class="font-black text-slate-800 dark:text-white text-sm sm:text-base">Cetak Rekapitulasi Izin Keluar Sekolah</h3>
            </div>
            <button type="button" onclick="closeRecapSchoolPassModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 font-bold cursor-pointer">✕</button>
        </div>

        <form action="{{ route('attendance.school-pass.print_recap') }}" method="GET" target="_blank" class="space-y-3.5 text-xs">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ date('Y-m-01') }}" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Filter Kelas</label>
                <select name="class_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->grade }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Filter Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white">
                    <option value="ALL">-- Semua Status --</option>
                    <option value="APPROVED">Disetujui (APPROVED)</option>
                    <option value="RETURNED" selected>Sudah Kembali (RETURNED)</option>
                    <option value="PENDING">Menunggu Verifikasi (PENDING)</option>
                    <option value="REJECTED">Ditolak (REJECTED)</option>
                </select>
            </div>

            <p class="text-[11px] text-slate-500 dark:text-slate-400 bg-teal-50 dark:bg-teal-950/40 p-2.5 rounded-xl border border-teal-200/60 dark:border-teal-800/60">
                📄 Dokumen resmi ber-kop sekolah dari master data. Ditandatangani oleh <strong>Kepala Sekolah (kiri)</strong> dan <strong>Wakasek Kesiswaan (kanan)</strong>.
            </p>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeRecapSchoolPassModal()" class="px-4 py-2 font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 font-bold bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    🖨️ Cetak Rekap Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRecapSchoolPassModal() {
        const modal = document.getElementById('school-pass-recap-modal');
        if (modal) modal.classList.remove('hidden');
    }
    function closeRecapSchoolPassModal() {
        const modal = document.getElementById('school-pass-recap-modal');
        if (modal) modal.classList.add('hidden');
    }
    function openCreateSchoolPassModal() {
        const modal = document.getElementById('school-pass-create-modal');
        if (modal) {
            modal.classList.remove('hidden');
            updateSpIndexChips();
        }
    }
    function closeCreateSchoolPassModal() {
        const modal = document.getElementById('school-pass-create-modal');
        if (modal) modal.classList.add('hidden');
    }
    function openReturnSchoolPassModal(actionUrl, permitNumber, studentNames) {
        const modal = document.getElementById('school-pass-return-modal');
        if (!modal) return;
        document.getElementById('return-school-pass-form').action = actionUrl;
        document.getElementById('return-school-pass-number').textContent = permitNumber;
        document.getElementById('return-school-pass-names').textContent = studentNames || '';
        modal.classList.remove('hidden');
    }
    function closeReturnSchoolPassModal() {
        const modal = document.getElementById('school-pass-return-modal');
        if (modal) modal.classList.add('hidden');
    }
    function openApproveSchoolPassModal(actionUrl, permitNumber) {
        const modal = document.getElementById('school-pass-approve-modal');
        if (!modal) return;
        document.getElementById('approve-school-pass-form').action = actionUrl;
        document.getElementById('approve-school-pass-number').textContent = permitNumber;
        modal.classList.remove('hidden');
    }
    function closeApproveSchoolPassModal() {
        const modal = document.getElementById('school-pass-approve-modal');
        if (modal) modal.classList.add('hidden');
    }
    function openRejectSchoolPassModal(actionUrl, permitNumber) {
        const modal = document.getElementById('school-pass-reject-modal');
        if (!modal) return;
        document.getElementById('reject-school-pass-form').action = actionUrl;
        document.getElementById('reject-school-pass-number').textContent = permitNumber;
        modal.classList.remove('hidden');
    }
    function closeRejectSchoolPassModal() {
        const modal = document.getElementById('school-pass-reject-modal');
        if (modal) modal.classList.add('hidden');
    }

    function filterSpIndexStudents(query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll('.sp-index-student-row').forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            row.style.display = (!q || searchData.includes(q)) ? 'flex' : 'none';
        });
    }

    function updateSpIndexChips() {
        const checkboxes = document.querySelectorAll('.sp-index-checkbox:checked');
        const counter = document.getElementById('sp-index-selected-counter');
        const container = document.getElementById('sp-index-chips-container');
        if (counter) counter.textContent = checkboxes.length;

        if (!container) return;
        container.innerHTML = '';

        checkboxes.forEach(cb => {
            const id = cb.value;
            const name = cb.getAttribute('data-name');
            const sClass = cb.getAttribute('data-class');

            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-teal-100 dark:bg-teal-950/70 text-teal-800 dark:text-teal-200 border border-teal-300 dark:border-teal-700';
            chip.innerHTML = `
                <span>${name} (${sClass})</span>
                <button type="button" onclick="removeSpIndexStudent('${id}')" class="text-teal-600 hover:text-teal-900 dark:text-teal-300 dark:hover:text-white font-bold ml-1 cursor-pointer">&times;</button>
            `;
            container.appendChild(chip);
        });
    }

    function removeSpIndexStudent(id) {
        const cb = document.querySelector(`.sp-index-checkbox[value="${id}"]`);
        if (cb) {
            cb.checked = false;
            updateSpIndexChips();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateSpIndexChips();
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('create') === '1') {
            openCreateSchoolPassModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateSchoolPassModal();
            closeReturnSchoolPassModal();
            closeApproveSchoolPassModal();
            closeRejectSchoolPassModal();
        }
    });
</script>
@endpush
@endsection

