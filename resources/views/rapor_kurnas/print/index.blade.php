@extends('layouts.app')

@section('title', 'Pusat Cetak e-Rapor SMA Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Top Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>🖨️</span>
                <span>Pusat Cetak e-Rapor SMA Kurikulum Nasional 2025</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pilih kelas dan siswa untuk mencetak dokumen Rapor Resmi, Sampul, Data Peserta Didik, Rapor P5, serta Leger Nilai Kelas.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('rapor_kurnas.print_hub') }}" class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-500 shrink-0">Pilih Kelas:</label>
                <select name="class_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-semibold">
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" {{ $selectedClassId == $cls->id ? 'selected' : '' }}>
                            {{ $cls->name }} ({{ $cls->students()->count() }} Siswa)
                        </option>
                    @endforeach
                </select>
            </form>

            @if($selectedClass)
                <a href="{{ route('rapor_kurnas.print_leger', $selectedClass->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md transition inline-flex items-center gap-1.5 shrink-0">
                    <span>📑</span>
                    <span>Cetak Leger Kelas</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Official Format Notice -->
    <div class="bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 rounded-2xl p-4 text-xs text-indigo-900 dark:text-indigo-300 flex items-center gap-3">
        <span class="text-2xl">🏛️</span>
        <div>
            <strong class="font-bold">Standar Format Cetak Dinas Pendidikan Kemendikdasmen:</strong>
            <p class="mt-0.5 text-indigo-800 dark:text-indigo-400">
                Dokumen cetak rapor disusun sesuai format resmi e-Rapor SMA Versi 2025. Tanda tangan Kepala Sekolah berada di sebelah <strong>KIRI</strong> dan tanda tangan Wali Kelas berada di sebelah <strong>KANAN</strong> pada setiap lembar laporan dinas.
            </p>
        </div>
    </div>

    @if($selectedClass)
        <!-- Tabel Cetak Per Siswa -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">
                    Daftar Siswa Kelas {{ $selectedClass->name }} ({{ count($students) }} Siswa)
                </h2>
                <span class="text-xs text-slate-400">Klik tombol cetak untuk membuka dokumen siap cetak / simpan PDF</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5 w-28">NIS / NISN</th>
                            <th class="p-3.5">Nama Lengkap Siswa</th>
                            <th class="p-3.5 w-24 text-center">L/P</th>
                            <th class="p-3.5 text-center">Menu Cetak Dokumen Rapor e-Rapor 2025</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($students as $index => $student)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3.5 text-center text-slate-400 font-mono">
                                    {{ $index + 1 }}
                                </td>
                                <td class="p-3.5 font-mono text-slate-600 dark:text-slate-400">
                                    <div>{{ $student->nis ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $student->nisn ?? '-' }}</div>
                                </td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $student->name }}</div>
                                </td>
                                <td class="p-3.5 text-center font-bold text-slate-600 dark:text-slate-400">
                                    {{ $student->gender === 'L' || $student->gender === 'Laki-laki' ? 'L' : 'P' }}
                                </td>
                                <td class="p-3.5 text-center">
                                    <div class="inline-flex flex-wrap items-center justify-center gap-1.5">
                                        <!-- 1. Cover -->
                                        <a href="{{ route('rapor_kurnas.print_cover', $student->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-[11px] transition inline-flex items-center gap-1">
                                            <span>📘</span>
                                            <span>Sampul</span>
                                        </a>

                                        <!-- 2. Identitas -->
                                        <a href="{{ route('rapor_kurnas.print_identity', $student->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-[11px] transition inline-flex items-center gap-1">
                                            <span>👤</span>
                                            <span>Identitas</span>
                                        </a>

                                        <!-- 3. Rapor Akademik -->
                                        <a href="{{ route('rapor_kurnas.print_academic', $student->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 font-extrabold text-[11px] border border-indigo-200 dark:border-indigo-800 transition inline-flex items-center gap-1">
                                            <span>📊</span>
                                            <span>Nilai Akademik</span>
                                        </a>

                                        <!-- 4. Rapor Kokurikuler (P5) -->
                                        <a href="{{ route('rapor_kurnas.print_cocurricular', $student->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 font-bold text-[11px] border border-emerald-200 dark:border-emerald-800 transition inline-flex items-center gap-1">
                                            <span>💡</span>
                                            <span>Rapor P5</span>
                                        </a>

                                        <!-- 5. Rapor Lengkap 1 Paket -->
                                        <a href="{{ route('rapor_kurnas.print_full', $student->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[11px] shadow-sm transition inline-flex items-center gap-1">
                                            <span>⭐</span>
                                            <span>Paket Lengkap</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400">
                                    Tidak ada data siswa dalam kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

