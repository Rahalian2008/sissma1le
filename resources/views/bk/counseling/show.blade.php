@extends('layouts.app')

@section('title', 'Detail Sesi Konseling - ' . $session->session_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
            <span>/</span>
            <a href="{{ route('bk.counseling.index') }}" class="hover:text-emerald-600 font-medium">Layanan Konseling</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-white font-bold">{{ $session->session_code }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.counseling.print', $session) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Berita Acara</span>
            </a>
            <a href="{{ route('bk.counseling.edit', $session) }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition">
                Edit
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $session->field_badge['class'] }}">
                        {{ $session->field_badge['label'] }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        {{ str_replace('_', ' ', $session->session_type) }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-500/10 text-teal-600">
                        {{ str_replace('_', ' ', $session->service_category) }}
                    </span>
                </div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">{{ $session->topic }}</h1>
                <p class="text-xs font-mono text-slate-400 mt-0.5">Kode: {{ $session->session_code }} &bull; Tanggal: {{ \Carbon\Carbon::parse($session->date)->translatedFormat('d F Y') }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-black {{ $session->status === 'SELESAI' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-600' }}">
                    STATUS: {{ $session->status }}
                </span>
            </div>
        </div>

        <!-- Student & Counselor Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Identitas Siswa (Konseli)</span>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center shrink-0">
                        {{ $session->student?->initials ?: substr($session->student?->name, 0, 2) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $session->student?->name }}</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kelas: {{ $session->student?->schoolClass?->name }} &bull; NISN: {{ $session->student?->nisn }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700 text-right">
                    <a href="{{ route('bk.student.profile360', $session->student) }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Profil 360° Siswa &rarr;</a>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Konselor / Pembina</span>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center shrink-0">
                        BK
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $session->counselor?->name ?: 'Guru BK' }}</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">NIP: {{ $session->counselor?->nip ?: '-' }} &bull; Tempat: {{ $session->place }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700 text-xs text-slate-400">
                    Waktu: {{ $session->start_time ?: '08:00' }} - {{ $session->end_time ?: '09:00' }} WIB
                </div>
            </div>
        </div>

        <!-- Problem Description -->
        <div>
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Deskripsi Masalah / Latar Belakang Konseling</h3>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line">
                {{ $session->problem_description }}
            </div>
        </div>

        @if($session->counseling_approach)
        <div>
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Pendekatan / Teknik Konseling</h3>
            <div class="p-3 rounded-xl bg-sky-50 dark:bg-sky-950/30 text-sky-800 dark:text-sky-300 text-xs font-medium border border-sky-200 dark:border-sky-900/50">
                {{ $session->counseling_approach }}
            </div>
        </div>
        @endif

        <!-- Agreement Result -->
        @if($session->agreement_result)
        <div>
            <h3 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2">Hasil Konseling & Komitmen Siswa</h3>
            <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 text-xs leading-relaxed text-emerald-900 dark:text-emerald-200 whitespace-pre-line">
                {{ $session->agreement_result }}
            </div>
        </div>
        @endif

        <!-- Follow-up Plan -->
        @if($session->follow_up_plan)
        <div>
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Rencana Tindak Lanjut (RTL)</h3>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line">
                {{ $session->follow_up_plan }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

