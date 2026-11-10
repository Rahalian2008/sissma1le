@extends('layouts.app')

@section('title', 'Rapor Saya - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    @if(!$setting->is_published_to_students)
        <!-- Belum Dipublikasikan -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-8 sm:p-12 text-center max-w-2xl mx-auto shadow-sm space-y-4">
            <div class="w-16 h-16 rounded-3xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-3xl mx-auto">
                ⏳
            </div>
            <h2 class="text-xl font-black text-slate-800 dark:text-white">Rapor Sedang Dalam Proses Finalisasi</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Laporan Hasil Belajar (Rapor) untuk Tahun Pelajaran {{ $setting->academicYear->name ?? '2025/2026' }} Semester {{ $setting->semester == '1' ? '1 (Ganjil)' : '2 (Genap)' }} saat ini belum dipublikasikan secara resmi ke portal siswa/wali murid.
            </p>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300">
                Jadwal resmi pembagian rapor: <strong>{{ Carbon\Carbon::parse($setting->report_date)->translatedFormat('d F Y') }}</strong> di {{ $setting->report_place }}.
            </div>
        </div>
    @elseif(!$student)
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-12 text-center max-w-xl mx-auto shadow-sm text-slate-400">
            Data profil peserta didik tidak terhubung dengan akun ini. Silakan hubungi admin sekolah.
        </div>
    @else
        <!-- Header Siswa & Download Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-2xl font-black shadow-md">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-black text-slate-800 dark:text-white">
                        {{ $student->name }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Kelas: <strong>{{ $student->schoolClass->name ?? '-' }}</strong> | NIS: {{ $student->nis ?? '-' }} | NISN: {{ $student->nisn ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Tombol Cetak Dokumen Rapor Mandiri -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('rapor_kurnas.print_academic', $student) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold text-xs flex items-center gap-1.5 transition">
                    <span>🖨️ Rapor Akademik</span>
                </a>
                <a href="{{ route('rapor_kurnas.print_cocurricular', $student) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 font-bold text-xs flex items-center gap-1.5 transition">
                    <span>💡 Rapor P5</span>
                </a>
                <a href="{{ route('rapor_kurnas.print_full', $student) }}" target="_blank" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs flex items-center gap-1.5 transition">
                    <span>📑 Rapor Lengkap (PDF)</span>
                </a>
            </div>
        </div>

        <!-- Ringkasan Kehadiran & Catatan Wali Kelas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Card Presensi -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Rekapitulasi Kehadiran Semester Ini</h3>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Sakit</span>
                        <span class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1 block">{{ $homeroom->sick ?? 0 }}</span>
                        <span class="text-[9px] text-slate-400">Hari</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Izin</span>
                        <span class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1 block">{{ $homeroom->permission ?? 0 }}</span>
                        <span class="text-[9px] text-slate-400">Hari</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Alpa</span>
                        <span class="text-xl font-black text-rose-600 dark:text-rose-400 mt-1 block">{{ $homeroom->absent ?? 0 }}</span>
                        <span class="text-[9px] text-slate-400">Hari</span>
                    </div>
                </div>
            </div>

            <!-- Card Catatan Wali Kelas -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan Wali Kelas</h3>
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300 italic leading-relaxed">
                    "{{ $homeroom->note ?? 'Pertahankan prestasi belajar dan terus aktif dalam kegiatan pengembangan karakter di sekolah.' }}"
                </div>
                <div class="mt-2 text-[11px] text-slate-400 text-right">
                    Wali Kelas: <strong>{{ $student->schoolClass->homeroomTeacher->name ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- Tabel Nilai & Capaian Kompetensi -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Nilai Hasil Belajar & Capaian Kompetensi</h2>
                <p class="text-xs text-slate-400">Penilaian Kurikulum Nasional / Merdeka Berdasarkan Ketercapaian TP.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5 w-64">Mata Pelajaran</th>
                            <th class="p-3.5 w-24 text-center">Nilai Akhir</th>
                            <th class="p-3.5">Capaian Kompetensi Pembelajaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($scores as $idx => $sc)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                <td class="p-3.5 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="p-3.5 font-bold text-slate-800 dark:text-white">
                                    {{ $sc->courseClass->subject->name ?? '-' }}
                                    <span class="block text-[10px] font-normal text-slate-400">Pengampu: {{ $sc->courseClass->teacher->name ?? '-' }}</span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="text-base font-black px-3 py-1 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 inline-block">
                                        {{ number_format($sc->score, 0) }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-700 dark:text-slate-300 leading-relaxed text-[11px]">
                                    {{ $sc->full_description ?? 'Mencapai kompetensi pembelajaran yang dipersyaratkan dengan predikat memuaskan.' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">
                                    Belum ada data nilai tersimpan untuk semester ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Nilai Ekstrakurikuler & P5 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Ekstrakurikuler -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3">Kegiatan Ekstrakurikuler</h3>
                <div class="space-y-2">
                    @forelse($extracurriculars as $ex)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-xs">{{ $ex->extracurricular->name ?? '-' }}</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $ex->description ?? 'Aktif mengikuti kegiatan pengembangan diri.' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg font-black text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300">
                                Predikat {{ $ex->predicate }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada kegiatan ekstrakurikuler tercatat.</p>
                    @endforelse
                </div>
            </div>

            <!-- Kokurikuler P5 -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3">Kokurikuler Profil Pelajar Pancasila (P5)</h3>
                <div class="space-y-2">
                    @forelse($cocurricularProjects as $cp)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700">
                            <span class="text-[10px] font-bold text-indigo-600 uppercase">{{ $cp->project->theme ?? 'Tema P5' }}</span>
                            <h4 class="font-bold text-slate-800 dark:text-white text-xs mt-0.5">{{ $cp->project->name ?? 'Projek P5' }}</h4>
                            <div class="mt-2 text-[11px] text-slate-600 dark:text-slate-300">
                                Capaian: <strong class="text-emerald-600">{{ $cp->predicate_text ?? 'Berkembang Sesuai Harapan' }}</strong>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada projek kokurikuler tercatat pada semester ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

