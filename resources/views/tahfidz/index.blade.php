@extends('layouts.app')

@section('title', !empty($isHomeroom) && !empty($homeroomClass) ? "Setor Hafalan Siswa Kelas {$homeroomClass->name} - Juz 30" : "Buku Kontrol Digital Setor Hafalan Al-Qur'an (Juz 30)")

@section('content')
<div class="space-y-5">
    @if(!empty($isHomeroom) && !empty($homeroomClass))
        <!-- Header Khusus Akun Wali Kelas -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-emerald-500/30 dark:border-emerald-500/20 shadow-xs relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex items-start sm:items-center gap-3.5 relative z-10">
                <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/25 text-2xl font-black">
                    📖
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                            Wali Kelas Binaan
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30">
                            Kelas {{ $homeroomClass->name }}
                        </span>
                        @if($homeroomGrade)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                Target Kurikulum: Tingkat {{ $homeroomGrade }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                        Setor Hafalan Siswa Kelas {{ $homeroomClass->name }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Pengelolaan setor hafalan siswa-siswi kelas {{ $homeroomClass->name }} untuk {{ count($surahs) }} surat target kurikulum Kelas {{ $homeroomGrade ?? 'Juz 30' }}.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap relative z-10">
                <a href="{{ route('tahfidz.create', ['class_id' => $homeroomClass->id]) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Setoran Siswa</span>
                </a>
                <a href="{{ route('tahfidz.custom_progress', ['class_id' => $homeroomClass->id]) }}" class="px-3.5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-xs shadow-teal-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Laporan Cicilan & Progres</span>
                </a>
                <a href="{{ route('tahfidz.report', ['class_id' => $homeroomClass->id]) }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Kelas</span>
                </a>
                <a href="{{ route('tahfidz.print', ['class_id' => $homeroomClass->id]) }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-xs shadow-indigo-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap</span>
                </a>
            </div>
        </div>

        <!-- Target Kurikulum Surat Kelas {{ $homeroomGrade }} Pills -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-2.5">
            <div class="flex items-center justify-between gap-2 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        📜 Surat-Surat Target Kurikulum Kelas {{ $homeroomGrade }} ({{ count($surahs) }} Surat)
                    </span>
                    <span class="text-[10px] text-slate-400">
                        @if($homeroomGrade === 'X') (No. 90 Al-Balad s.d. 114 An-Nas)
                        @elseif($homeroomGrade === 'XI') (No. 83 Al-Mutaffifin s.d. 89 Al-Fajr)
                        @elseif($homeroomGrade === 'XII') (No. 78 An-Naba' s.d. 82 Al-Infitar)
                        @endif
                    </span>
                </div>
                @if($surahFilter)
                    <a href="{{ route('tahfidz.index') }}" class="text-[10px] font-bold text-rose-500 hover:underline">
                        Hapus Filter Surat ({{ $surahFilter }}) ✕
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-thin">
                @foreach($surahs as $num => $s)
                    @php
                        $isActiveSurah = ($surahFilter === $s['name']);
                    @endphp
                    <a href="{{ route('tahfidz.index', array_merge(request()->query(), ['surah' => $s['name']])) }}"
                       class="px-2.5 py-1 rounded-lg text-[11px] font-bold shrink-0 transition flex items-center gap-1 border
                       {{ $isActiveSurah 
                           ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' 
                           : 'bg-slate-50 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                        <span class="text-[9px] opacity-75 font-mono">#{{ $num }}</span>
                        <span>{{ $s['name'] }}</span>
                        <span class="text-[9px] opacity-70">({{ $s['total_ayah'] }} ay.)</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Summary Stats Wali Kelas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Setoran Kelas</span>
                <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($totalSetoran) }}</div>
                <span class="text-[10px] text-slate-400 font-medium">Log setoran siswa kelas {{ $homeroomClass->name }}</span>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-500/20 shadow-2xs">
                <span class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider block">Setoran Lulus / Mutqin</span>
                <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalLulus) }}</div>
                <span class="text-[10px] text-emerald-600/80 font-medium">Hafalan lancar & tajwid baik</span>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-500/20 shadow-2xs">
                <span class="text-[11px] font-bold text-amber-500 uppercase tracking-wider block">Perlu Mengulang</span>
                <div class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalMengulang) }}</div>
                <span class="text-[10px] text-amber-600/80 font-medium">Perlu bimbingan lanjutan</span>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-indigo-500/20 shadow-2xs">
                <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider block">Siswa di Kelas</span>
                <div class="text-xl sm:text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ count($studentProgressList) }}</div>
                <span class="text-[10px] text-indigo-600/80 font-medium">{{ $distinctStudents }} siswa telah aktif menyetor</span>
            </div>
        </div>



        <!-- Daftar Siswa Kelas Binaan & Tombol Setor Cepat -->
        <div id="tab-content-students" class="space-y-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">
                            Daftar Siswa Kelas {{ $homeroomClass->name }}
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Pilih siswa untuk melakukan input setor hafalan atau melihat buku kontrol lengkap capaian hafalannya.
                        </p>
                    </div>
                    <a href="{{ route('tahfidz.create', ['class_id' => $homeroomClass->id]) }}" class="self-start sm:self-center px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Input Setoran Siswa</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 text-slate-400 font-bold uppercase text-[10px]">
                                <th class="py-3 px-4">No</th>
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4">NISN / NIS</th>
                                <th class="py-3 px-4">Progres Target Kelas {{ $homeroomGrade }}</th>
                                <th class="py-3 px-4">Setoran Terakhir</th>
                                <th class="py-3 px-4 text-center">Aksi Setor & Laporan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($studentProgressList as $idx => $item)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-850/40 transition">
                                    <td class="py-3 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
                                            <span>{{ $item['student']->name }}</span>
                                            @if($item['is_complete'])
                                                <span class="px-1.5 py-0.2 rounded bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 font-extrabold text-[9px] border border-emerald-300 dark:border-emerald-800">
                                                    MUTQIN 🌟
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400">Gender: {{ $item['student']->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300 font-mono text-[11px]">
                                        <div>{{ $item['student']->nisn ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-400">NIS: {{ $item['student']->nis ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-between text-[11px] mb-1">
                                            <span class="font-bold text-slate-700 dark:text-slate-300">
                                                {{ $item['passed_count'] }} / {{ $item['total_target'] }} Surat Tuntas
                                            </span>
                                            <span class="font-mono font-extrabold {{ $item['percentage'] >= 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500' }}">
                                                {{ $item['percentage'] }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                            <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all" style="width: {{ $item['percentage'] }}%"></div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($item['latest_record'])
                                            <div class="font-semibold text-slate-800 dark:text-slate-200">
                                                📖 {{ $item['latest_record']->surah_name }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                                <span>{{ \Carbon\Carbon::parse($item['latest_record']->deposit_date)->format('d M Y') }}</span>
                                                @if($item['latest_record']->overall_grade === 'Lulus')
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">✓ Lulus</span>
                                                @else
                                                    <span class="text-amber-600 dark:text-amber-400 font-bold">↻ Mengulang</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Belum pernah setor</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('tahfidz.create', ['student_id' => $item['student']->id, 'class_id' => $homeroomClass->id]) }}"
                                               class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[11px] shadow-2xs transition flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                <span>Setor Hafalan</span>
                                            </a>
                                            <a href="{{ route('tahfidz.student', $item['student']->id) }}"
                                               class="p-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 transition"
                                               title="Buku Kontrol Siswa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </a>
                                            <a href="{{ route('tahfidz.print_student', $item['student']->id) }}" target="_blank"
                                               class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition"
                                               title="Cetak Kartu Kontrol Siswa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-400 text-xs">
                                        Belum ada siswa terdaftar di kelas {{ $homeroomClass->name }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        </div>

        <div class="mt-8 border-t-2 border-slate-200 dark:border-slate-800 pt-8"></div>
    @endif

    <div id="tab-content-records" class="space-y-4">
        <!-- Header Standar Non-Wali Kelas (Admin / Super Admin / Guru / Koordinator Keagamaan) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <h1 class="text-base sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Buku Kontrol Digital Setor Hafalan Al-Qur'an Juz 30
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Monitoring hafalan Al-Qur'an 37 Surat Juz 30 (An-Naba' s.d. An-Nas) dengan penilaian makhraj, tajwid & kelancaran.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                @if(in_array(Auth::user()->role, ['wali_kelas', 'guru', 'admin', 'super_admin', 'koordinator_keagamaan']))
                    <a href="{{ route('tahfidz.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Input Setoran Baru</span>
                    </a>
                @endif
                @if(!Auth::user()->isStudent())
                <a href="{{ route('tahfidz.custom_progress') }}" class="px-3.5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-xs shadow-teal-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Laporan Cicilan & Progres</span>
                </a>
                <a href="{{ route('tahfidz.report') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Dokumen Laporan</span>
                </a>
                <a href="{{ route('tahfidz.print') }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-xs shadow-indigo-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Summary Stats Standar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Setoran Keseluruhan</span>
                <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($globalTotalSetoran ?? $totalSetoran) }}</div>
                <span class="text-[10px] text-slate-400 font-medium">Log tercatat pada sistem</span>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-500/20 shadow-2xs">
                <span class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider block">Lulus / Mutqin</span>
                <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($globalTotalLulus ?? $totalLulus) }}</div>
                <span class="text-[10px] text-emerald-600/80 font-medium">Hafalan lancar & tajwid benar</span>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-500/20 shadow-2xs">
                <span class="text-[11px] font-bold text-amber-500 uppercase tracking-wider block">Perlu Mengulang</span>
                <div class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($globalTotalMengulang ?? $totalMengulang) }}</div>
                <span class="text-[10px] text-amber-600/80 font-medium">Perlu bimbingan lanjutan</span>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-indigo-500/20 shadow-2xs">
                <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider block">Siswa Aktif Menyetor</span>
                <div class="text-xl sm:text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($globalDistinctStudents ?? $distinctStudents) }}</div>
                <span class="text-[10px] text-indigo-600/80 font-medium">Siswa terdata di buku kontrol</span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs">
            <form method="GET" action="{{ route('tahfidz.index') }}" class="flex flex-col lg:flex-row lg:items-end gap-3">
                <div class="flex-1 min-w-0">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Siswa / Ayat / Catatan</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama siswa, NISN, atau surat..." class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 outline-none">
                </div>

                <div class="w-full lg:w-40 shrink-0">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filter Kelas</label>
                    <select name="class_id" class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 outline-none" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                @if(count($classStudents) > 0)
                <div class="w-full lg:w-48 shrink-0">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filter Siswa</label>
                    <select name="student_id" class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 outline-none">
                        <option value="">Semua Siswa</option>
                        @foreach($classStudents as $st)
                            <option value="{{ $st->id }}" {{ $selectedStudentId == $st->id ? 'selected' : '' }}>
                                {{ $st->name }} ({{ strtoupper($st->initials) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="w-full lg:w-52 shrink-0">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                        @if(!empty($isHomeroom) && !empty($homeroomGrade))
                            Pilih Surat (Kelas {{ $homeroomGrade }})
                        @else
                            Pilih Surat
                        @endif
                    </label>
                    <select name="surah" class="w-full text-xs px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 outline-none">
                        <option value="">
                            @if(!empty($isHomeroom) && !empty($homeroomGrade))
                                Semua Surat ({{ count($surahs) }})
                            @else
                                Semua Surat (1-37)
                            @endif
                        </option>
                        @foreach($surahs as $s)
                            <option value="{{ $s['name'] }}" {{ $surahFilter == $s['name'] ? 'selected' : '' }}>
                                {{ $s['number'] }}. {{ $s['name'] }} ({{ $s['total_ayah'] }} ay.)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('tahfidz.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl transition whitespace-nowrap">
                        Reset Filter
                    </a>
                    <button type="submit" class="py-2 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Table of Deposits -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    @if(!empty($isHomeroom) && !empty($homeroomClass))
                        Log Setoran Siswa Kelas {{ $homeroomClass->name }} ({{ $records->total() }} Catatan)
                    @else
                        Seluruh Catatan Setoran Hafalan ({{ $records->total() }} Catatan)
                    @endif
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 text-slate-400 font-bold uppercase text-[10px]">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Siswa</th>
                            <th class="py-3 px-4">Kelas</th>
                            <th class="py-3 px-4">Surat & Rentang Ayat</th>
                            <th class="py-3 px-4">Rubrik Penilaian</th>
                            <th class="py-3 px-4">Hasil</th>
                            <th class="py-3 px-4">Penguji & Tanggal</th>
                            @if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
                            <th class="py-3 px-4 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($records as $idx => $r)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-850/40 transition">
                                <td class="py-3 px-4 text-slate-400 font-mono">{{ $records->firstItem() + $idx }}</td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('tahfidz.student', $r->student_id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition">
                                        {{ $r->student?->name }}
                                    </a>
                                    <div class="text-[10px] text-slate-400">NISN: {{ $r->student?->nisn }}</div>
                                </td>
                                <td class="py-3 px-4 text-slate-600 dark:text-slate-300">{{ $r->student?->schoolClass?->name ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-1.5">
                                        <span>📖 {{ $r->surah_name }}</span>
                                        @if($r->surah_number)
                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 font-mono">No. {{ $r->surah_number }}</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                        <span>{{ $r->ayat_range }}</span>
                                        @if($r->ayat_count)
                                            <span class="px-1.5 py-0.2 rounded bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 font-bold text-[10px]">
                                                {{ $r->ayat_count }} Ayat
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-col gap-0.5 text-[10px]">
                                        <span>Makhraj: <strong>{{ $r->makhraj }}</strong></span>
                                        <span>Tajwid: <strong>{{ $r->tajwid }}</strong></span>
                                        <span>Kelancaran: <strong>{{ $r->kelancaran }}</strong></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($r->overall_grade === 'Lulus')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                            ✓ LULUS
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                            ↻ MENGULANG
                                        </span>
                                    @endif
                                    @if($r->evaluation_notes)
                                        <div class="text-[10px] text-slate-400 italic mt-1 line-clamp-1" title="{{ $r->evaluation_notes }}">
                                            "{{ $r->evaluation_notes }}"
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-[11px]">
                                    <div class="font-semibold text-slate-700 dark:text-slate-300">{{ $r->examiner_name }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($r->deposit_date)->locale('id')->translatedFormat('d M Y') }}, {{ substr($r->deposit_time, 0, 5) }} WIB
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('tahfidz.student', $r->student_id) }}" class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 transition" title="Buku Kontrol Siswa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                        @if(Auth::user()->hasAnyRole(['admin', 'super_admin']))
                                            <a href="{{ route('tahfidz.edit', $r->id) }}" class="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 hover:bg-amber-100 transition" title="Edit Setoran">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form action="{{ route('tahfidz.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus catatan setoran ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition cursor-pointer" title="Hapus Catatan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                    Belum ada data setoran hafalan Al-Qur'an tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($records->hasPages())
                <div class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
