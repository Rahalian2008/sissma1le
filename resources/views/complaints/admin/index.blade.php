@extends('layouts.app')

@section('title', 'Kanal Pengaduan & Aspirasi Publik')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Khusus Kepala Sekolah & Wakasek
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Jalur Pimpinan Terpadu</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                Kanal Pengaduan & Aspirasi Publik
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Pusat pemantauan, telaah, dan disposisi tindak lanjut aduan masyarakat, orang tua, dan siswa secara aman & rahasia.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ url('/pengaduan') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 border border-indigo-200 dark:border-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Buka Form Publik</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Executive Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Aduan</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $stats['total'] }}</div>
            <div class="text-[10px] text-slate-400">Seluruh Periode</div>
        </div>

        <!-- Menunggu -->
        <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/80 shadow-sm space-y-1">
            <div class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Menunggu</div>
            <div class="text-2xl font-black text-amber-900 dark:text-amber-200">{{ $stats['menunggu'] }}</div>
            <div class="text-[10px] text-amber-600 dark:text-amber-400 font-medium">Belum Ditelaah</div>
        </div>

        <!-- Ditelaah -->
        <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/80 shadow-sm space-y-1">
            <div class="text-[11px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider">Ditelaah</div>
            <div class="text-2xl font-black text-blue-900 dark:text-blue-200">{{ $stats['ditelaah'] }}</div>
            <div class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">Kajian Pimpinan</div>
        </div>

        <!-- Diproses -->
        <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/80 shadow-sm space-y-1">
            <div class="text-[11px] font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider">Diproses</div>
            <div class="text-2xl font-black text-indigo-900 dark:text-indigo-200">{{ $stats['diproses'] }}</div>
            <div class="text-[10px] text-indigo-600 dark:text-indigo-400 font-medium">Tindak Lanjut Lapangan</div>
        </div>

        <!-- Selesai -->
        <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/80 shadow-sm space-y-1">
            <div class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Selesai</div>
            <div class="text-2xl font-black text-emerald-900 dark:text-emerald-200">{{ $stats['selesai'] }}</div>
            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">Tuntas Diselesaikan</div>
        </div>

        <!-- Bullying Alert -->
        <div class="p-4 rounded-2xl bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/80 shadow-sm space-y-1">
            <div class="text-[11px] font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider">Bullying / Kekerasan</div>
            <div class="text-2xl font-black text-rose-900 dark:text-rose-200">{{ $stats['prioritas_bullying'] }}</div>
            <div class="text-[10px] text-rose-600 dark:text-rose-400 font-medium">Prioritas Perlindungan</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
        <form action="{{ route('complaints.admin.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-center justify-between">
            
            <!-- Status Pills -->
            <div class="flex flex-wrap items-center gap-1.5 w-full md:w-auto">
                <a href="{{ route('complaints.admin.index', array_merge(request()->except('status', 'page'))) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ !$statusFilter ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    Semua
                </a>
                <a href="{{ route('complaints.admin.index', array_merge(request()->except('status', 'page'), ['status' => 'MENUNGGU'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'MENUNGGU' ? 'bg-amber-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    Menunggu ({{ $stats['menunggu'] }})
                </a>
                <a href="{{ route('complaints.admin.index', array_merge(request()->except('status', 'page'), ['status' => 'DITELAAH'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'DITELAAH' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    Ditelaah ({{ $stats['ditelaah'] }})
                </a>
                <a href="{{ route('complaints.admin.index', array_merge(request()->except('status', 'page'), ['status' => 'DIPROSES'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'DIPROSES' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    Diproses ({{ $stats['diproses'] }})
                </a>
                <a href="{{ route('complaints.admin.index', array_merge(request()->except('status', 'page'), ['status' => 'SELESAI'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'SELESAI' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    Selesai ({{ $stats['selesai'] }})
                </a>
            </div>

            <!-- Search & Category Dropdown -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <select name="category" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200 font-semibold focus:ring-indigo-500">
                    <option value="">-- Semua Kategori --</option>
                    <option value="bullying_perundungan" {{ $categoryFilter === 'bullying_perundungan' ? 'selected' : '' }}>Bullying / Kekerasan</option>
                    <option value="layanan_akademik" {{ $categoryFilter === 'layanan_akademik' ? 'selected' : '' }}>Layanan Akademik & Guru</option>
                    <option value="kedisiplinan_tatib" {{ $categoryFilter === 'kedisiplinan_tatib' ? 'selected' : '' }}>Kedisiplinan & Tatib</option>
                    <option value="sarana_prasarana" {{ $categoryFilter === 'sarana_prasarana' ? 'selected' : '' }}>Sarana & Prasarana</option>
                    <option value="administrasi_tu" {{ $categoryFilter === 'administrasi_tu' ? 'selected' : '' }}>Pelayanan Administrasi TU</option>
                    <option value="integritas_pungli" {{ $categoryFilter === 'integritas_pungli' ? 'selected' : '' }}>Integritas & Pungli</option>
                    <option value="aspirasi_kemajuan" {{ $categoryFilter === 'aspirasi_kemajuan' ? 'selected' : '' }}>Aspirasi Kemajuan</option>
                </select>

                <div class="relative flex-1 md:w-64">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari tiket, judul, pelapor..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 pl-8 text-slate-800 dark:text-slate-200 focus:ring-indigo-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 dark:bg-slate-700 text-white text-xs font-bold hover:bg-slate-700">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Complaints Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">No. Tiket & Tanggal</th>
                        <th class="py-3 px-4">Pelapor</th>
                        <th class="py-3 px-4">Kategori & Pokok Aduan</th>
                        <th class="py-3 px-4">Status & Penindaklanjut</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($complaints as $c)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $c->ticket_number }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $c->created_at->format('d/m/Y H:i') }} WIB
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span>{{ $c->display_name }}</span>
                                    @if($c->is_anonymous)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">Anonim</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $c->reporter_type_label }} &bull; {{ $c->reporter_phone }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4 max-w-xs sm:max-w-md">
                                <div class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold {{ $c->category === 'bullying_perundungan' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }} mb-1">
                                    {{ $c->category_label }}
                                </div>
                                <div class="font-bold text-slate-900 dark:text-white truncate">
                                    {{ $c->title }}
                                </div>
                                <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                    {{ Str::limit($c->description, 75) }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                @php $badge = $c->status_badge; @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    {{ $badge['label'] }}
                                </span>
                                @if($c->handler)
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        D.h.i: {{ $c->handler->name }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('complaints.admin.show', $c) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 font-bold text-xs transition border border-indigo-200 dark:border-indigo-800">
                                    <span>Buka & Telaah</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                Tidak ada data pengaduan pada filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($complaints->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
