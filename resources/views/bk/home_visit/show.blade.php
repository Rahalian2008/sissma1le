@extends('layouts.app')

@section('title', 'Detail Home Visit - ' . $visit->visit_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
            <span>/</span>
            <a href="{{ route('bk.home-visit.index') }}" class="hover:text-emerald-600 font-medium">Home Visit</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-white font-bold">{{ $visit->visit_code }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.home-visit.print', $visit) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan Home Visit</span>
            </a>
            <a href="{{ route('bk.home-visit.edit', $visit) }}" class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition">
                Edit
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/10 text-blue-600 border border-blue-500/20">
                        HOME VISIT BK
                    </span>
                    <span class="text-xs font-mono text-slate-400">Kode: {{ $visit->visit_code }}</span>
                </div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">Kunjungan Rumah: {{ $visit->student?->name }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tanggal: {{ \Carbon\Carbon::parse($visit->visit_date)->translatedFormat('d F Y') }} &bull; Surat Tugas: {{ $visit->letter_number ?: '-' }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-black {{ $visit->status === 'TERLAKSANA' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-600' }}">
                    STATUS: {{ $visit->status }}
                </span>
            </div>
        </div>

        <!-- Student & Parent Meta -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Identitas Peserta Didik</span>
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $visit->student?->name }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelas: {{ $visit->student?->schoolClass?->name }} &bull; NISN: {{ $visit->student?->nisn }}</p>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 font-medium">Alamat: {{ $visit->address_visited ?: ($visit->student?->address ?: 'Alamat terdaftar di buku induk') }}</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Petugas & Pihak Ditemui</span>
                <p class="text-xs text-slate-700 dark:text-slate-300 font-bold">Orang Tua / Wali: {{ $visit->parent_name ?: 'Orang Tua Peserta Didik' }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Guru BK: <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $visit->counselor?->name ?: 'Konselor Sekolah' }}</span></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Wali Kelas: <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $visit->homeroom?->name ?: ($visit->student?->schoolClass?->homeroomTeacher?->name ?: '-') }}</span></p>
            </div>
        </div>

        <!-- Case Background -->
        <div>
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Latar Belakang Kasus & Alasan Kunjungan</h3>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line">
                {{ $visit->case_background }}
            </div>
        </div>

        @if($visit->family_condition)
        <div>
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Kondisi Keluarga & Lingkungan Rumah</h3>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line">
                {{ $visit->family_condition }}
            </div>
        </div>
        @endif

        @if($visit->parent_commitment)
        <div>
            <h3 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2">Tanggapan & Komitmen Orang Tua</h3>
            <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 text-xs leading-relaxed text-emerald-900 dark:text-emerald-200 whitespace-pre-line">
                {{ $visit->parent_commitment }}
            </div>
        </div>
        @endif

        @if($visit->visit_result)
        <div>
            <h3 class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider mb-2">Hasil Kunjungan & Kesepakatan Solusi</h3>
            <div class="p-4 rounded-2xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40 text-xs leading-relaxed text-blue-900 dark:text-blue-200 whitespace-pre-line">
                {{ $visit->visit_result }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

