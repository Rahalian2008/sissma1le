@extends('layouts.app')

@section('title', 'Detail Panggilan Orang Tua - ' . $parentCall->call_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
            <span>/</span>
            <a href="{{ route('bk.parent-call.index') }}" class="hover:text-emerald-600 font-medium">Panggilan Orang Tua</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-white font-bold">{{ $parentCall->call_code }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.parent-call.print-letter', $parentCall) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 text-white font-bold text-xs hover:bg-amber-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Surat Panggilan</span>
            </a>
            <a href="{{ route('bk.parent-call.print-berita-acara', $parentCall) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Cetak Berita Acara</span>
            </a>
            <a href="{{ route('bk.parent-call.edit', $parentCall) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 transition">
                Edit
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 border border-amber-500/20">
                        SURAT PEMANGGILAN RESMI
                    </span>
                    <span class="text-xs font-mono text-slate-400">Kode: {{ $parentCall->call_code }}</span>
                </div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">Panggilan Orang Tua: {{ $parentCall->student?->name }}</h1>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5">No. Surat: {{ $parentCall->letter_number }} &bull; Tanggal Hadir: {{ \Carbon\Carbon::parse($parentCall->call_date)->translatedFormat('d F Y') }} ({{ $parentCall->call_time }})</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-black {{ $parentCall->status === 'SELESAI' || $parentCall->status === 'HADIR' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-blue-500/10 text-blue-600' }}">
                    STATUS: {{ $parentCall->status }}
                </span>
            </div>
        </div>

        <!-- Student & Meeting Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Data Peserta Didik</span>
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $parentCall->student?->name }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelas: {{ $parentCall->student?->schoolClass?->name }} &bull; NISN: {{ $parentCall->student?->nisn }}</p>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 font-medium">Orang Tua / Wali: {{ $parentCall->student?->father_name ?: ($parentCall->student?->mother_name ?: 'Orang Tua') }}</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Lokasi & Konselor Pemanggil</span>
                <p class="text-xs text-slate-700 dark:text-slate-300 font-bold">Tempat: {{ $parentCall->place }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Konselor BK: <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $parentCall->counselor?->name ?: 'Guru BK' }}</span></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Waktu: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $parentCall->call_time }}</span></p>
            </div>
        </div>

        <!-- Alasan Pemanggilan -->
        <div>
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Alasan Pemanggilan (Isi Surat)</h3>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line">
                {{ $parentCall->reason }}
            </div>
        </div>

        <!-- Berita Acara Pertemuan & Perjanjian -->
        @if($parentCall->meeting_notes)
        <div>
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Berita Acara Pertemuan / Musyawarah</h3>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line">
                {{ $parentCall->meeting_notes }}
            </div>
        </div>
        @endif

        @if($parentCall->parent_agreement)
        <div>
            <h3 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2">Surat Perjanjian & Kesepakatan Orang Tua</h3>
            <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 text-xs leading-relaxed text-emerald-900 dark:text-emerald-200 whitespace-pre-line">
                {{ $parentCall->parent_agreement }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

