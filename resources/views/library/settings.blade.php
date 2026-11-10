@extends('layouts.app')

@section('title', 'Pengaturan Sistem Perpustakaan')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
        <div>
            <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Administrator Control</div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>⚙️ Pengaturan Perpustakaan & Penugasan Pengelola</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Konfigurasi aturan peminjaman, denda keterlambatan harian, dan penetapan role Pengelola Perpustakaan (Pustakawan).
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('library.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                &larr; Dashboard Pustaka
            </a>
        </div>
    </div>

    <!-- Form Pengaturan -->
    <form action="{{ route('library.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Card 1: Parameter Aturan Sirkulasi & Denda -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-xs space-y-5">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📋 Kebijakan Sirkulasi & Denda (SNP)</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Parameter standar sirkulasi buku sesuai Standar Nasional Perpustakaan.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Denda Keterlambatan per Hari (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="fine_per_day" value="{{ old('fine_per_day', $finePerDay) }}" required min="0" max="50000" step="500" class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-bold text-slate-900 dark:text-white">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Denda dihitung otomatis saat pemustaka mengembalikan buku melewati batas tanggal jatuh tempo.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Durasi Standar Pinjam Buku (Hari) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="loan_days" value="{{ old('loan_days', $loanDays) }}" required min="1" max="30" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-bold text-slate-900 dark:text-white">
                    <p class="text-[11px] text-slate-400 mt-1">Lama peminjaman standar (biasanya 7 hari kerja untuk sekolah tingkat SMA).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Maksimal Pinjaman Aktif Siswa (Buku) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="max_student_loans" value="{{ old('max_student_loans', $maxStudentLoans) }}" required min="1" max="10" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-bold text-slate-900 dark:text-white">
                    <p class="text-[11px] text-slate-400 mt-1">Batas kuota eksemplar buku fisik yang boleh dipinjam bersamaan oleh satu siswa.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Maksimal Pinjaman Aktif Guru (Buku) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="max_teacher_loans" value="{{ old('max_teacher_loans', $maxTeacherLoans) }}" required min="1" max="20" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-bold text-slate-900 dark:text-white">
                    <p class="text-[11px] text-slate-400 mt-1">Batas kuota buku referensi yang boleh dipinjam oleh guru / tenaga pendidik.</p>
                </div>
            </div>
        </div>

        <!-- Card 2: Penugasan Pengelola Perpustakaan (Pustakawan) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-xs space-y-5">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>👥 Penugasan Pengelola Perpustakaan (Pustakawan)</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Tetapkan akun staf atau guru yang bertugas mengelola katalog, menyetujui peminjaman, dan melayani sirkulasi.</p>
            </div>

            <!-- Pilih Guru/Staf untuk Ditugaskan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Tugaskan Akun Menjadi Pengelola Perpustakaan Baru
                </label>
                <div class="flex gap-2">
                    <select name="assign_user_id" class="flex-1 px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                        <option value="">-- Pilih Guru / Tenaga Kependidikan --</option>
                        @foreach($staffUsers as $su)
                            @if(! $su->hasRole('pustakawan'))
                                <option value="{{ $su->id }}">
                                    {{ $su->name }} ({{ $su->username }} - {{ strtoupper($su->role) }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Akun yang dipilih akan otomatis mendapatkan hak akses Pengelola Perpustakaan (Pustakawan).</p>
            </div>

            <!-- Daftar Pustakawan Aktif Saat Ini -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5">Daftar Pengelola Perpustakaan Aktif:</h3>
                <div class="space-y-2">
                    @forelse($librarians as $lib)
                        <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($lib->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-xs text-slate-900 dark:text-white">{{ $lib->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $lib->email ?? $lib->username }} • Role: {{ strtoupper($lib->role) }}</div>
                                </div>
                            </div>

                            <button type="submit" name="remove_user_id" value="{{ $lib->id }}" onclick="return confirm('Cabut peran pustakawan dari {{ addslashes($lib->name) }}?')" class="px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 hover:bg-rose-100 text-xs font-semibold transition cursor-pointer">
                                Cabut Akses
                            </button>
                        </div>
                    @empty
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-slate-400 text-xs text-center">
                            Belum ada akun guru/staf khusus yang ditugaskan sebagai pustakawan. Admin bertindak sebagai pengelola penuh.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition transform active:scale-95 cursor-pointer">
                Simpan Perubahan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection

