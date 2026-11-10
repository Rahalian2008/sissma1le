@extends('layouts.app')

@section('title', 'RPL BK 1 Lembar Kurikulum Merdeka')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-xs font-semibold text-blue-700 mb-2">
                <span>Format Resmi 1 Lembar Kurikulum Merdeka</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Rencana Pelaksanaan Layanan (RPL) BK</h1>
            <p class="text-xs text-slate-500 mt-0.5">Perangkat layanan bimbingan konseling praktis dan ringkas 1 halaman mencakup Layanan Dasar, Responsif, Peminatan Individual, dan Dukungan Sistem.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                &larr; Dashboard BK
            </a>
            <a href="{{ route('bk.rpl.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Susun RPL 1 Lembar
            </a>
        </div>
    </div>

    <!-- Stats Component Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Layanan Dasar</span>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $stats['dasar'] }} <span class="text-xs font-normal text-slate-400">RPL</span></div>
            <div class="text-[11px] text-slate-400 mt-0.5">Bimbingan Klasikal / Kelompok</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Layanan Responsif</span>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['responsif'] }} <span class="text-xs font-normal text-slate-400">RPL</span></div>
            <div class="text-[11px] text-slate-400 mt-0.5">Konseling Individual / Mediasi</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Peminatan Individual</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['peminatan'] }} <span class="text-xs font-normal text-slate-400">RPL</span></div>
            <div class="text-[11px] text-slate-400 mt-0.5">Studi Lanjut PTN & Karier</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Dukungan Sistem</span>
            <div class="text-2xl font-black text-purple-600 mt-1">{{ $stats['dukungan'] }} <span class="text-xs font-normal text-slate-400">RPL</span></div>
            <div class="text-[11px] text-slate-400 mt-0.5">Kolaborasi & Manajemen</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('bk.rpl.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari topik atau materi RPL..." class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <select name="component" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Semua Komponen Layanan --</option>
                    <option value="LAYANAN_DASAR" {{ request('component') == 'LAYANAN_DASAR' ? 'selected' : '' }}>Layanan Dasar</option>
                    <option value="LAYANAN_RESPONSIF" {{ request('component') == 'LAYANAN_RESPONSIF' ? 'selected' : '' }}>Layanan Responsif</option>
                    <option value="PERENCANAAN_INDIVIDUAL" {{ request('component') == 'PERENCANAAN_INDIVIDUAL' ? 'selected' : '' }}>Perencanaan Individual</option>
                    <option value="DUKUNGAN_SISTEM" {{ request('component') == 'DUKUNGAN_SISTEM' ? 'selected' : '' }}>Dukungan Sistem</option>
                </select>
            </div>
            <div>
                <select name="field" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Semua Bidang Layanan --</option>
                    <option value="PRIBADI" {{ request('field') == 'PRIBADI' ? 'selected' : '' }}>Bidang Pribadi</option>
                    <option value="SOSIAL" {{ request('field') == 'SOSIAL' ? 'selected' : '' }}>Bidang Sosial</option>
                    <option value="BELAJAR" {{ request('field') == 'BELAJAR' ? 'selected' : '' }}>Bidang Belajar</option>
                    <option value="KARIER" {{ request('field') == 'KARIER' ? 'selected' : '' }}>Bidang Karier</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition">Filter</button>
                <a href="{{ route('bk.rpl.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- RPL Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($rpls as $rpl)
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm hover:border-blue-300 transition-all flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-mono font-bold text-slate-400">{{ $rpl->rpl_code }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $rpl->field_of_service === 'PRIBADI' ? 'bg-rose-100 text-rose-800' : ($rpl->field_of_service === 'SOSIAL' ? 'bg-amber-100 text-amber-800' : ($rpl->field_of_service === 'BELAJAR' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800')) }}">
                            {{ $rpl->field_of_service }}
                        </span>
                    </div>

                    <h3 class="font-bold text-slate-900 text-sm line-clamp-2">{{ $rpl->topic }}</h3>

                    <div class="text-xs text-slate-500 space-y-1">
                        <div><strong class="text-slate-700">Komponen:</strong> {{ str_replace('_', ' ', $rpl->service_component) }} ({{ $rpl->service_type }})</div>
                        <div><strong class="text-slate-700">Sasaran:</strong> {{ $rpl->grade_target }} - Semester {{ $rpl->semester }}</div>
                        <div><strong class="text-slate-700">Alokasi Waktu:</strong> {{ $rpl->time_allocation }}</div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('bk.rpl.print', $rpl) }}" target="_blank" class="text-slate-600 hover:text-slate-900 font-bold inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Cetak 1 Lembar
                    </a>
                    <a href="{{ route('bk.rpl.show', $rpl) }}" class="text-blue-600 hover:text-blue-700 font-bold">
                        Detail RPL &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-3xl p-12 text-center text-slate-400 text-xs border border-slate-200">
                Belum ada perangkat RPL BK 1 Lembar yang dibuat.
            </div>
        @endforelse
    </div>

    @if($rpls->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $rpls->links() }}
        </div>
    @endif
</div>
@endsection

