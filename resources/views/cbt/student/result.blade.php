@extends('layouts.app')

@section('title', 'Hasil Pengerjaan Ujian CBT - ' . $exam->title)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
            <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            Hasil Pengerjaan Ujian CBT
        </h1>
        <a href="{{ route('cbt.student.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Portal
        </a>
    </div>

    <div class="py-4 max-w-2xl mx-auto space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs sm:text-sm font-semibold flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
        @endif

        <!-- KARTU HASIL UJIAN -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-lg p-6 sm:p-8 text-center space-y-6">
            <div class="w-20 h-20 rounded-3xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>

            <div>
                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                    Sesi Ujian Selesai
                </span>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $exam->title }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ $exam->subject?->name ?? 'Mata Pelajaran' }} • Guru: {{ $exam->teacher?->name ?? '-' }}
                </p>
            </div>

            <!-- DETAIL PESERTA -->
            <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 text-xs text-left grid grid-cols-2 gap-3">
                <div>
                    <span class="text-slate-400 block text-[10px]">Nama Peserta:</span>
                    <strong class="font-bold text-slate-800 dark:text-white text-sm">{{ $student->name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">NIS / Rombel:</span>
                    <strong class="font-bold text-slate-800 dark:text-white text-sm">{{ $student->nis }} ({{ $student->schoolClass?->name ?? '-' }})</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Waktu Selesai:</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $session->finished_at ? $session->finished_at->translatedFormat('d F Y H:i:s') : '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Total Soal Terisi:</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $session->answers->whereNotNull('answer')->count() }} Butir Soal</span>
                </div>
            </div>

            <!-- TAMPILAN NILAI -->
            @if($exam->show_result_to_student)
                <div class="p-6 rounded-3xl bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-slate-900 dark:to-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 space-y-3">
                    <div class="text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">Nilai Akhir Anda</div>
                    <div class="text-5xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">
                        {{ number_format($session->final_grade, 1) }}
                    </div>

                    <div class="flex items-center justify-center gap-3 pt-2">
                        @if($session->isPassed())
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                ✓ Tuntas (KKM: {{ $exam->kkm }})
                            </span>
                        @else
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                Belum Tuntas (KKM: {{ $exam->kkm }})
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs pt-3 border-t border-indigo-200/60 dark:border-indigo-900/60">
                        <div class="text-emerald-600 font-bold">
                            {{ $session->total_correct }} Jawaban Benar
                        </div>
                        <div class="text-rose-500 font-bold">
                            {{ $session->total_incorrect }} Jawaban Salah
                        </div>
                    </div>
                </div>
            @else
                <div class="p-5 rounded-2xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/60 text-xs text-blue-800 dark:text-blue-300 leading-relaxed">
                    <svg class="w-6 h-6 mx-auto mb-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Jawaban Anda telah berhasil tersimpan dan terkirim ke server pengawas. Pengumuman nilai akhir akan disampaikan oleh guru mata pelajaran setelah seluruh proses asesmen selesai.
                </div>
            @endif

            <div class="pt-2">
                <a href="{{ route('cbt.student.index') }}" class="w-full py-3 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-indigo-600/20 transition inline-block">
                    Kembali ke Beranda Ujian
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
