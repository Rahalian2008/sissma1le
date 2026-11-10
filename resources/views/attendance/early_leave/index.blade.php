@extends('layouts.app')

@section('title', 'Ajuan Pulang Cepat Siswa & Surat Keterangan Resmi')

@section('content')
<div class="space-y-6">
    @include('attendance.partials.nav')

    {{-- Banner Notifikasi Cetak Surat Resmi --}}
    @if(session('print_url'))
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 text-white shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center text-2xl shrink-0">
                    📄
                </div>
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white text-amber-900 mb-1">
                        Surat Keterangan Diterbitkan
                    </span>
                    <h3 class="font-extrabold text-sm sm:text-base leading-snug">{{ session('success') }}</h3>
                    <p class="text-xs text-amber-100 mt-0.5">Surat Keterangan Pulang Cepat siap dicetak dengan Kop Surat resmi dari Master Data.</p>
                </div>
            </div>
            <a href="{{ session('print_url') }}" target="_blank"
               class="px-5 py-2.5 bg-white hover:bg-amber-50 text-amber-900 font-extrabold rounded-xl text-xs sm:text-sm shadow-md flex items-center gap-2 whitespace-nowrap active:scale-95 transition">
                <span>🖨️ Cetak Surat Sekarang</span>
                <span class="text-base">&nearr;</span>
            </a>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-md bg-gradient-to-tr from-amber-500 to-orange-500 shadow-amber-500/20">
                <span class="text-2xl">🏃</span>
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Ajuan Pulang Cepat Siswa
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Layanan resmi permohonan kepulangan siswa mendahului jam usai KBM, otomatis menerbitkan Surat Keterangan Pulang Cepat & terintegrasi dengan presensi harian.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="openRecapEarlyLeaveModal()"
                class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 transition flex items-center gap-2 shadow-xs cursor-pointer">
                <span>🖨️ Cetak Rekapitulasi Laporan</span>
            </button>
            <button type="button" onclick="openCreateEarlyLeaveModal()"
                class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-amber-500 hover:bg-amber-600 active:scale-95 text-white transition flex items-center gap-2 shadow-sm shadow-amber-500/30 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Buat Ajuan Pulang Cepat</span>
            </button>
        </div>
    </div>

    <!-- Alert Success / Info / Error -->
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

    <!-- Stat Cards -->
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
                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Disetujui & Terbit</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($stats['approved']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-500 flex items-center justify-center font-bold text-lg">📄</div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Ditolak</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($stats['rejected']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-500 flex items-center justify-center font-bold text-lg">❌</div>
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
        <form method="GET" action="{{ route('attendance.early-leave.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase text-slate-500 dark:text-slate-400 mb-1">Cari Data</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa, NIS, no. surat..."
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-500 dark:text-slate-400 mb-1">Status Ajuan</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="ALL">Semua Status</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Menunggu (Pending)</option>
                    <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Disetujui (Approved)</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-500 dark:text-slate-400 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-xs font-bold bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('attendance.early-leave.index') }}" class="px-3 py-2 text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl transition" title="Reset">
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
                        <th class="py-3 px-4">Surat & Tanggal</th>
                        <th class="py-3 px-4">Siswa</th>
                        <th class="py-3 px-4">Alasan & Penjemput</th>
                        <th class="py-3 px-4">Status & Verifikator</th>
                        <th class="py-3 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($requests as $index => $req)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 text-center font-semibold text-slate-400">
                                {{ $requests->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-mono font-bold text-slate-900 dark:text-white">{{ $req->letter_number }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $req->formatted_date }} &bull; Pukul <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $req->leave_time }} WIB</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Diajukan oleh: <span class="font-medium">{{ $req->requester_type }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $req->student?->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    NIS: {{ $req->student?->nis ?? '-' }} &bull; Kelas: {{ $req->student?->schoolClass?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    {{ $req->reason_category }}
                                </div>
                                <div class="text-slate-600 dark:text-slate-300 mt-1 line-clamp-2" title="{{ $req->reason }}">
                                    {{ $req->reason }}
                                </div>
                                @if($req->pickup_name)
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                        🚗 Penjemput: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $req->pickup_name }}</span> ({{ $req->pickup_relationship ?? 'Keluarga' }})
                                        @if($req->pickup_phone) &bull; {{ $req->pickup_phone }} @endif
                                    </div>
                                @endif
                                @if($req->attachment_path)
                                    <div class="mt-1">
                                        <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1 font-semibold">
                                            📎 Lihat Bukti Pendukung
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div>
                                    @if(is_array($req->status_badge))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $req->status_badge['class'] ?? '' }}">
                                            {{ $req->status_badge['label'] ?? $req->status }}
                                        </span>
                                    @elseif(is_string($req->status_badge))
                                        {!! $req->status_badge !!}
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-slate-100 text-slate-800">
                                            {{ $req->status }}
                                        </span>
                                    @endif
                                </div>
                                @if($req->status === 'APPROVED' && $req->approver)
                                    <div class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1">
                                        Disetujui: {{ $req->approver->name }}
                                    </div>
                                @elseif($req->status === 'REJECTED' && $req->approval_notes)
                                    <div class="text-[10px] text-rose-500 mt-1 italic">
                                        Alasan: {{ $req->approval_notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    @if($req->status === 'APPROVED')
                                        <a href="{{ route('attendance.early-leave.print', $req->id) }}" target="_blank"
                                            class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/80 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 transition inline-flex items-center gap-1" title="Cetak Surat Izin">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            <span>Surat</span>
                                        </a>
                                    @endif

                                    @if($isStaff && $req->status === 'PENDING')
                                        <button type="button"
                                            onclick="openApproveEarlyLeaveModal('{{ route('attendance.early-leave.approve', $req->id) }}', '{{ addslashes($req->student?->name ?? 'Siswa') }}', '{{ $req->letter_number }}')"
                                            class="px-2 py-1 rounded-lg text-[11px] font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-xs cursor-pointer" title="Setujui">
                                            ✓ Setujui
                                        </button>
                                        <button type="button"
                                            onclick="openRejectEarlyLeaveModal('{{ route('attendance.early-leave.reject', $req->id) }}', '{{ addslashes($req->student?->name ?? 'Siswa') }}', '{{ $req->letter_number }}')"
                                            class="px-2 py-1 rounded-lg text-[11px] font-bold bg-rose-600 hover:bg-rose-700 text-white transition shadow-xs cursor-pointer" title="Tolak">
                                            ✕ Tolak
                                        </button>
                                    @endif

                                    @if($isStaff || $req->created_by === auth()->id())
                                        <a href="{{ route('attendance.early-leave.edit', $req->id) }}"
                                            class="px-2 py-1 rounded-lg text-[11px] font-bold bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:hover:bg-amber-900/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800 transition inline-flex items-center gap-1" title="Edit Data">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route('attendance.early-leave.destroy', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus ajuan pulang cepat No. {{ $req->letter_number }} ini? Data yang dihapus tidak dapat dikembalikan.');">
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
                                <p class="font-medium">Belum ada ajuan pulang cepat yang tercatat.</p>
                                <p class="text-[11px] text-slate-400 mt-1">Klik tombol "+ Buat Ajuan Pulang Cepat" di atas untuk menambahkan permohonan baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Buat Ajuan Pulang Cepat (Vanilla JS) -->
    <div id="early-leave-create-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 my-6">
            <div class="px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">🏃</span>
                    <h3 class="font-bold text-sm sm:text-base">Buat Ajuan Pulang Cepat</h3>
                </div>
                <button type="button" onclick="closeCreateEarlyLeaveModal()" class="text-white/80 hover:text-white text-2xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('attendance.early-leave.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf

                @if($isStaff)
                    <!-- Staff Mode: Pilih Siswa -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Siswa <span class="text-rose-500">*</span></label>
                        <select name="student_id" required
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="">-- Pilih Siswa yang Mengajukan Pulang Cepat --</option>
                            @foreach($allStudents as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->name }} (NIS: {{ $st->nis }}) - {{ $st->schoolClass?->name ?? 'Tanpa Kelas' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Pilih nama siswa yang bersangkutan dari daftar.</p>
                    </div>
                @else
                    <!-- Student Mode: Auto-filled -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                        <p class="text-[11px] font-bold text-slate-400 uppercase">Data Siswa Pemohon</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-white mt-0.5">{{ $student?->name ?? Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500">NIS: {{ $student?->nis ?? '-' }} &bull; Kelas: {{ $student?->schoolClass?->name ?? '-' }}</p>
                        <input type="hidden" name="student_id" value="{{ $student?->id }}">
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Kepulangan <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Pulang Cepat <span class="text-rose-500">*</span></label>
                        <input type="time" name="leave_time" required value="{{ now('Asia/Jakarta')->format('H:i') }}"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-semibold font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori Alasan <span class="text-rose-500">*</span></label>
                    <select name="reason_category" required
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <option value="Sakit di Sekolah (Rujukan UKS)">Sakit di Sekolah (Rujukan UKS)</option>
                        <option value="Urusan Keluarga Mendesak">Urusan Keluarga Mendesak / Takziyah / Berduka</option>
                        <option value="Keperluan Medis / Berobat / Terapi">Keperluan Medis / Berobat ke Rumah Sakit</option>
                        <option value="Perjalanan / Jadwal Keberangkatan Luar Kota">Perjalanan / Tiket Luar Kota</option>
                        <option value="Dispensasi Lomba / Tugas Khusus">Dispensasi Lomba / Tugas Resmi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rincian Keterangan & Alasan <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="3" required placeholder="Tuliskan keterangan detail mengapa siswa harus pulang mendahului jadwal KBM..."
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800 space-y-3">
                    <p class="text-xs font-bold text-amber-900 dark:text-amber-200 flex items-center gap-1.5">
                        <span>🚗</span>
                        <span>Identitas Penjemput (Orang Tua / Wali)</span>
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Nama Penjemput</label>
                            <input type="text" name="pickup_name" placeholder="Nama orang tua/wali"
                                class="w-full px-2.5 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Hubungan</label>
                            <select name="pickup_relationship" class="w-full px-2.5 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white">
                                <option value="Orang Tua (Ayah/Ibu)">Orang Tua (Ayah/Ibu)</option>
                                <option value="Wali Siswa">Wali Siswa</option>
                                <option value="Kakak / Saudara Kandung">Kakak / Saudara</option>
                                <option value="Pulang Mandiri">Pulang Mandiri (Izin Ortu)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">No. HP / WA</label>
                            <input type="text" name="pickup_phone" placeholder="08xxxxxxxxxx"
                                class="w-full px-2.5 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Lampiran / Bukti Surat (Opsional)</label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf"
                        class="w-full px-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                    <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, atau PDF (Maks. 5MB). Contoh: Surat rujukan dokter, surat keterangan orang tua.</p>
                </div>

                @if($isStaff)
                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="direct_approve" value="1" checked
                                class="rounded text-amber-600 focus:ring-amber-500 w-4 h-4">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Langsung Setujui & Terbitkan Surat Keterangan Resmi (Wali Kelas / Guru Piket)
                            </span>
                        </label>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeCreateEarlyLeaveModal()"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-xs font-bold bg-amber-500 hover:bg-amber-600 active:scale-95 text-white rounded-xl transition shadow-sm shadow-amber-500/30 cursor-pointer">
                        Simpan & Ajukan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Persetujuan (Approve) (Vanilla JS) -->
    <div id="early-leave-approve-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl p-6 text-center animate-in fade-in zoom-in-95 duration-200">
            <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center mx-auto text-2xl font-bold mb-3">
                ✓
            </div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Setujui Ajuan Pulang Cepat</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin menyetujui permohonan kepulangan untuk <strong class="text-slate-800 dark:text-white" id="approve-early-leave-student"></strong>?
            </p>
            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-2 font-medium">
                Surat Keterangan Pulang Cepat resmi (<span id="approve-early-leave-letter" class="font-mono font-bold"></span>) akan otomatis diterbitkan dan data presensi harian siswa langsung disinkronkan.
            </p>

            <form id="approve-early-leave-form" action="" method="POST" class="mt-4 space-y-3">
                @csrf
                <input type="text" name="notes" placeholder="Catatan persetujuan (opsional)"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white">

                <div class="flex items-center justify-center gap-2 pt-2">
                    <button type="button" onclick="closeApproveEarlyLeaveModal()"
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
    <div id="early-leave-reject-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl p-6 text-center animate-in fade-in zoom-in-95 duration-200">
            <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center mx-auto text-2xl font-bold mb-3">
                ✕
            </div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tolak Ajuan Pulang Cepat</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Tolak permohonan kepulangan untuk <strong class="text-slate-800 dark:text-white" id="reject-early-leave-student"></strong>.
            </p>

            <form id="reject-early-leave-form" action="" method="POST" class="mt-4 space-y-3 text-left">
                @csrf
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="reason" required rows="3" placeholder="Tuliskan alasan mengapa ajuan pulang cepat ditolak..."
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white"></textarea>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeRejectEarlyLeaveModal()"
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
<div id="early-leave-recap-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold">🖨️</span>
                <h3 class="font-black text-slate-800 dark:text-white text-sm sm:text-base">Cetak Rekapitulasi Pulang Cepat</h3>
            </div>
            <button type="button" onclick="closeRecapEarlyLeaveModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 font-bold cursor-pointer">✕</button>
        </div>

        <form action="{{ route('attendance.early-leave.print_recap') }}" method="GET" target="_blank" class="space-y-3.5 text-xs">
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
                    <option value="APPROVED" selected>Disetujui (APPROVED)</option>
                    <option value="PENDING">Menunggu Verifikasi (PENDING)</option>
                    <option value="REJECTED">Ditolak (REJECTED)</option>
                </select>
            </div>

            <p class="text-[11px] text-slate-500 dark:text-slate-400 bg-amber-50 dark:bg-amber-950/40 p-2.5 rounded-xl border border-amber-200/60 dark:border-amber-800/60">
                📄 Dokumen resmi ber-kop sekolah dari master data. Ditandatangani oleh <strong>Kepala Sekolah (kiri)</strong> dan <strong>Wakasek Kesiswaan (kanan)</strong>.
            </p>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeRecapEarlyLeaveModal()" class="px-4 py-2 font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    🖨️ Cetak Rekap Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRecapEarlyLeaveModal() {
        const modal = document.getElementById('early-leave-recap-modal');
        if (modal) modal.classList.remove('hidden');
    }
    function closeRecapEarlyLeaveModal() {
        const modal = document.getElementById('early-leave-recap-modal');
        if (modal) modal.classList.add('hidden');
    }
    function openCreateEarlyLeaveModal() {
        const modal = document.getElementById('early-leave-create-modal');
        if (modal) modal.classList.remove('hidden');
    }
    function closeCreateEarlyLeaveModal() {
        const modal = document.getElementById('early-leave-create-modal');
        if (modal) modal.classList.add('hidden');
    }
    function openApproveEarlyLeaveModal(actionUrl, studentName, letterNumber) {
        const modal = document.getElementById('early-leave-approve-modal');
        if (!modal) return;
        document.getElementById('approve-early-leave-form').action = actionUrl;
        document.getElementById('approve-early-leave-student').textContent = studentName;
        document.getElementById('approve-early-leave-letter').textContent = letterNumber || '';
        modal.classList.remove('hidden');
    }
    function closeApproveEarlyLeaveModal() {
        const modal = document.getElementById('early-leave-approve-modal');
        if (modal) modal.classList.add('hidden');
    }
    function openRejectEarlyLeaveModal(actionUrl, studentName, letterNumber) {
        const modal = document.getElementById('early-leave-reject-modal');
        if (!modal) return;
        document.getElementById('reject-early-leave-form').action = actionUrl;
        document.getElementById('reject-early-leave-student').textContent = studentName;
        modal.classList.remove('hidden');
    }
    function closeRejectEarlyLeaveModal() {
        const modal = document.getElementById('early-leave-reject-modal');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('create') === '1') {
            openCreateEarlyLeaveModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateEarlyLeaveModal();
            closeApproveEarlyLeaveModal();
            closeRejectEarlyLeaveModal();
        }
    });
</script>
@endpush
@endsection

