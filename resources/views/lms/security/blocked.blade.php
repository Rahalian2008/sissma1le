@extends('layouts.app')

@section('title', 'Akses Pembelajaran Diblokir - ' . ($itemTitle ?? 'LMS SMA Negeri 1 Lengkong'))

@section('content')
@php
    $student = $student ?? (isset($activeBlock) && $activeBlock->student ? $activeBlock->student : (auth()->user()?->student ?? null));
    $block = $block ?? ($activeBlock ?? null);
@endphp
<div class="min-h-[80vh] flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-white dark:bg-slate-900 border-2 border-rose-500/80 dark:border-rose-500/70 rounded-3xl shadow-2xl p-6 sm:p-8 space-y-6 text-center relative overflow-hidden">
        <!-- Background Glow Danger Accent -->
        <div class="absolute -top-16 -right-16 w-36 h-36 bg-rose-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-36 h-36 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Lock Icon & Header Badge -->
        <div class="flex flex-col items-center justify-center space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl shadow-inner border border-rose-500/30 animate-pulse">
                🔒
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/30">
                Akses Dikunci Oleh Keamanan Sistem LMS
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Akses {{ $itemType ?? 'Pembelajaran' }} Sedang Diblokir
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-md mx-auto leading-relaxed">
                Anda terdeteksi melakukan tindakan yang dilarang saat sedang mengikuti pembelajaran digital aktif.
            </p>
        </div>

        <!-- Rincian Pelanggaran & Identitas Siswa -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-left text-xs space-y-2.5">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200/80 dark:border-slate-700/80">
                <span class="text-slate-500 dark:text-slate-400">Identitas Siswa:</span>
                <span class="font-extrabold text-slate-900 dark:text-white">{{ $student ? ($student->name . ' (NISN: ' . ($student->nisn ?? '-') . ')') : (auth()->user()?->name ?? 'Siswa') }}</span>
            </div>
            <div class="flex items-center justify-between pb-2 border-b border-slate-200/80 dark:border-slate-700/80">
                <span class="text-slate-500 dark:text-slate-400">Mata Pelajaran:</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $courseClass->subject->name }} (Kelas {{ $courseClass->schoolClass->name }})</span>
            </div>
            <div class="flex items-center justify-between pb-2 border-b border-slate-200/80 dark:border-slate-700/80">
                <span class="text-slate-500 dark:text-slate-400">Item Pembelajaran:</span>
                <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $itemType ?? 'Item' }}: {{ $itemTitle }}</span>
            </div>
            <div class="flex items-center justify-between pb-2 border-b border-slate-200/80 dark:border-slate-700/80">
                <span class="text-slate-500 dark:text-slate-400">Penyebab Blokir:</span>
                <span class="font-bold text-rose-600 dark:text-rose-400">{{ $block->violation_label ?? 'Buka Tab Baru / Minimize Browser' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-500 dark:text-slate-400">Waktu Terdeteksi:</span>
                <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">
                    {{ $block->blocked_at ? $block->blocked_at->translatedFormat('d F Y - H:i:s') : now()->translatedFormat('d F Y - H:i:s') }} WIB
                </span>
            </div>
        </div>

        <!-- Ketentuan & Solusi Pembukaan Blokir -->
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 text-xs text-left space-y-2">
            <div class="font-bold flex items-center gap-1.5 text-amber-700 dark:text-amber-300">
                <span>⚠️</span>
                <span>Prosedur Pembukaan Blokir (Wajib Lapor):</span>
            </div>
            <p class="leading-relaxed">
                Untuk menjaga integritas dan kedisiplinan belajar, akses materi/tugas/ujian yang terkunci <strong>hanya dapat dibuka oleh Guru Mata Pelajaran bersangkutan atau Administrator LMS</strong>.
            </p>
            <div class="pt-1 font-semibold text-[11px] text-slate-700 dark:text-slate-300">
                <span>Guru Pengampu: <strong>{{ $courseClass->teacher->name ?? 'Guru Mata Pelajaran' }}</strong></span>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <button type="button" onclick="window.location.reload()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 animate-spin-hover" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Muat Ulang Halaman (Cek Status Buka Blokir)</span>
            </button>
            <a href="{{ route('lms.course', $courseClass->id) }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                Kembali ke Beranda LMS Kelas
            </a>
        </div>

        <p class="text-[11px] text-slate-400 italic">
            *Fitur blokir keamanan ini berlaku khusus pada sesi aktif {{ strtolower($itemType ?? 'pembelajaran') }} dan tidak mempengaruhi menu aplikasi sekolah lainnya.
        </p>
    </div>
</div>
@endsection
