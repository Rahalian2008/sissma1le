@extends('layouts.app')

@section('title', 'Kontrol Tahfidz - Profil Belum Terhubung')

@section('content')
<div class="max-w-2xl mx-auto py-12 px-4 sm:px-6">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-8 text-center space-y-5">
        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 mx-auto flex items-center justify-center text-3xl">
            📖
        </div>

        <div class="space-y-2">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Data Profil Siswa Belum Terhubung
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                Akun Anda saat ini belum terhubung dengan data profil siswa aktif pada sistem Buku Kontrol Tahfidz Al-Qur'an.
            </p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-left text-xs text-slate-600 dark:text-slate-300 space-y-1.5">
            <div class="font-bold text-slate-800 dark:text-slate-200">Langkah Penyelesaian:</div>
            <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-500 dark:text-slate-400">
                <li>Untuk <strong>Siswa & Sekretaris Kelas</strong>: Pastikan NISN atau akun Anda telah ditautkan pada Master Data Siswa.</li>
                <li>Untuk <strong>Orang Tua / Wali</strong>: Hubungi Wali Kelas untuk menautkan akun wali dengan profil ananda.</li>
            </ul>
        </div>

        <div class="pt-2">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-xs transition">
                <span>← Kembali ke Dashboard</span>
            </a>
        </div>
    </div>
</div>
@endsection
