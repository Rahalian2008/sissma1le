@extends('layouts.app')

@section('title', 'Buat Instrumen Sosiometri - BK')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
        <span>/</span>
        <a href="{{ route('bk.sociometry.index') }}" class="hover:text-emerald-600 font-medium">Sosiometri</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-white font-bold">Buat Baru</span>
    </div>

    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">Instrumen Sosiometri Kelas Baru</h1>
            <p class="text-xs text-slate-400 mt-1">Pemetaan relasi penerimaan dan interaksi sosial peserta didik dalam satu rombel</p>
        </div>

        <form action="{{ route('bk.sociometry.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Rombongan Belajar (Kelas) *</label>
                <select name="class_id" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Sosiometri *</label>
                    <input type="text" name="title" value="{{ old('title', 'Sosiometri Dinamika Hubungan Kelas') }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal *</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tema / Kriteria Pemilihan *</label>
                <input type="text" name="theme" value="{{ old('theme', 'Teman Belajar Kelompok & Kolaborasi Tugas') }}" required placeholder="Contoh: Teman Belajar Kelompok, Teman Curhat, Teman Kegiatan Sosial" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan / Petunjuk Guru BK</label>
                <textarea name="description" rows="3" placeholder="Petunjuk khusus pengisian pilihan sosiometri untuk siswa..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('description') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <a href="{{ route('bk.sociometry.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-purple-600 text-white font-bold text-xs hover:bg-purple-700 transition shadow-md shadow-purple-600/20">
                    Aktifkan Sosiometri
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

