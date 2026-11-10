@extends('layouts.app')

@section('title', 'Laporan Kustom Progres Cicilan Hafalan ' . ($target_grade_label ?? ('Kelas ' . $student_grade)) . ' - ' . $student->name)

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar & Identitas Siswa -->
    <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-indigo-600 text-white font-black flex items-center justify-center text-2xl shadow-lg shadow-emerald-600/20 shrink-0">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
            <div class="space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ $student->name }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                        {{ $student->schoolClass?->name ?? 'Tanpa Kelas' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        Tingkat {{ $student->getNormalizedGrade() }}
                    </span>
                    @if($overall_percentage >= 100)
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-500 text-white shadow-xs">
                            ✓ TUNTAS TARGET KELAS {{ $student_grade }}
                        </span>
                    @endif
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-x-4 gap-y-1 flex-wrap">
                    <span>NISN: <strong class="text-slate-700 dark:text-slate-300 font-mono">{{ $student->nisn }}</strong></span>
                    <span>Wali Kelas: <strong class="text-slate-700 dark:text-slate-300">{{ $student->schoolClass?->homeroomTeacher?->name ?? 'Belum ditentukan' }}</strong></span>
                    <span>Target Kurikulum: <strong class="text-emerald-600 dark:text-emerald-400">Target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }} ({{ $total_surahs }} Surat / {{ $total_target_ayat }} Ayat)</strong></span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            @if(Auth::user()->canManageTahfidz())
                <a href="{{ route('tahfidz.create', ['student_id' => $student->id, 'class_id' => $student->class_id]) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Setor Cicilan</span>
                </a>
            @endif

            <a href="{{ route('tahfidz.print_custom_progress', ['student' => $student->id, 'grade' => $student_grade]) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-md shadow-indigo-600/30 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan Resmi A4</span>
            </a>

            @if(empty($isOwnReport))
                <a href="{{ route('tahfidz.custom_progress', ['class_id' => $student->class_id]) }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                    Daftar Siswa
                </a>
            @endif
        </div>
    </div>

    <!-- Ringkasan Statistik Progres Cicilan Hafalan Sesuai Tingkat Kelas (KPI Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Card 1: Total Ketuntasan Ayat -->
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-bold mb-2">
                <span>KETUNTASAN AYAT (KELAS {{ $student_grade }})</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $total_completed_ayat }}</span>
                <span class="text-xs font-bold text-slate-400">/ {{ $total_target_ayat }} Ayat</span>
            </div>
            <div class="mt-3">
                <div class="flex justify-between text-[11px] font-bold text-slate-500 mb-1">
                    <span>Persentase</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-black">{{ $overall_percentage }}%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $overall_percentage) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 2: Sisa Tanggungan Ayat -->
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-bold mb-2">
                <span>SISA AYAT BELUM SELESAI</span>
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 font-mono">{{ $total_pending_ayat }}</span>
                <span class="text-xs font-bold text-slate-400">Ayat Tersisa</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-3 leading-relaxed">
                @if($total_pending_ayat === 0)
                    Alhamdulillah, seluruh {{ $total_target_ayat }} ayat target Kelas {{ $student_grade }} telah diselesaikan!
                @else
                    Tersisa {{ $total_pending_ayat }} ayat lagi pada target kurikulum Kelas {{ $student_grade }}.
                @endif
            </p>
        </div>

        <!-- Card 3: Status Surat Selesai -->
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-bold mb-2">
                <span>SURAT TUNTAS (100%)</span>
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400 font-mono">{{ $total_completed_surahs }}</span>
                <span class="text-xs font-bold text-slate-400">/ {{ $total_surahs }} Surat (Kelas {{ $student_grade }})</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-3 leading-relaxed">
                Surat yang seluruh ayatnya sudah berstatus <strong>Lulus</strong> tanpa sisa.
            </p>
        </div>

        <!-- Card 4: Surat Sedang Dicicil -->
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-bold mb-2">
                <span>SURAT SEDANG DICICIL</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 font-mono">{{ $total_in_installment_surahs }}</span>
                <span class="text-xs font-bold text-slate-400">Surat Aktif</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-3 leading-relaxed">
                {{ $total_unstarted_surahs }} surat target Kelas {{ $student_grade }} lainnya belum disetor.
            </p>
        </div>
    </div>

    <!-- Bagian 1: Tabel Riwayat Setoran Cicilan (Log Kronologis) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                    1. Log Riwayat Setoran Cicilan Siswa (Angsuran Ayat)
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Catatan setiap kali siswa menyetorkan hafalan secara cicilan beserta tanggal, rentang ayat, jumlah ayat, nilai, dan penguji.
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-teal-50 dark:bg-teal-950 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                Total {{ $records->count() }} Kali Setoran Tercatat
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200/80 dark:border-slate-800">
                        <th class="py-3.5 px-4">No</th>
                        <th class="py-3.5 px-4">Tanggal & Waktu</th>
                        <th class="py-3.5 px-4">Surat yang Disetor</th>
                        <th class="py-3.5 px-4">Rentang Ayat Cicilan</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Ayat</th>
                        <th class="py-3.5 px-4">Kualitas Bacaan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4">Penguji / Catatan</th>
                        @if(Auth::user()->canManageTahfidz())
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($records as $idx => $r)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-400">
                                {{ $idx + 1 }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-extrabold text-slate-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($r->deposit_date)->isoFormat('dddd, D MMMM Y') }}
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    Pukul {{ substr($r->deposit_time, 0, 5) }} WIB
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-black text-slate-900 dark:text-white">
                                    No. {{ $r->surah_number }} — {{ $r->surah_name }}
                                </div>
                                <span class="text-[10.5px] text-slate-400">
                                    Juz {{ $r->juz ?? 30 }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 inline-block font-mono">
                                    {{ $r->ayat_range }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="text-sm font-black font-mono text-emerald-600 dark:text-emerald-400">
                                    {{ $r->ayat_count ?? '-' }}
                                </span>
                                <span class="text-[10px] text-slate-400 block font-semibold">Ayat</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-[11px] space-y-0.5">
                                    <div>Makhraj: <span class="font-bold text-slate-900 dark:text-white">{{ $r->makhraj }}</span></div>
                                    <div>Tajwid: <span class="font-bold text-slate-900 dark:text-white">{{ $r->tajwid }}</span></div>
                                    <div>Kelancaran: <span class="font-bold text-slate-900 dark:text-white">{{ $r->kelancaran }}</span></div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($r->overall_grade === 'Lulus')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        ✓ Lulus
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                        ✕ Mengulang
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $r->examiner_name }}
                                </div>
                                @if($r->evaluation_notes)
                                    <p class="text-[11px] text-slate-500 italic mt-0.5">
                                        "{{ $r->evaluation_notes }}"
                                    </p>
                                @endif
                            </td>
                            @if(Auth::user()->canManageTahfidz())
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('tahfidz.edit', $r->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition" title="Edit Catatan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->canManageTahfidz() ? 9 : 8 }}" class="py-10 text-center text-slate-400">
                                Belum ada catatan setoran cicilan untuk siswa ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bagian 2: Status Progres Cicilan Sesuai Pengelompokan Kelas Siswa -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200/80 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    2. Rincian Progres Cicilan Surat Target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }} ({{ $total_surahs }} Surat)
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Hanya menampilkan daftar surat target kurikulum Kelas {{ $student_grade }} (ayat berapa yang sudah tuntas vs belum selesai).
                </p>
            </div>

            <!-- Tab Switcher Kelas jika ingin melihat tingkatan lain -->
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1">Tingkat:</span>
                @foreach(['X' => 'Kelas X (25 Surat)', 'XI' => 'Kelas XI (7 Surat)', 'XII' => 'Kelas XII (5 Surat)'] as $gKey => $gLabel)
                    <a href="{{ route('tahfidz.student_custom_progress', ['student' => $student->id, 'grade' => $gKey]) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ ($student_grade === $gKey) ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                        {{ $gKey === $student->getNormalizedGrade() ? '🌟 ' : '' }}{{ $gLabel }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200/80 dark:border-slate-800">
                        <th class="py-3.5 px-4">No</th>
                        <th class="py-3.5 px-4">Nama Surat & Terjemahan</th>
                        <th class="py-3.5 px-4 text-center">Total Ayat</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4">Ayat yang SUDAH Selesai</th>
                        <th class="py-3.5 px-4">Ayat yang BELUM Selesai</th>
                        <th class="py-3.5 px-4 text-center">Progres (%)</th>
                        <th class="py-3.5 px-4 text-center">Aksi Setor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @foreach($surah_details as $num => $sd)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition {{ $sd['status'] === 'SELESAI' ? 'bg-emerald-50/20 dark:bg-emerald-950/10' : ($sd['status'] === 'SEDANG_DICICIL' ? 'bg-amber-50/30 dark:bg-amber-950/15' : '') }}">
                            <td class="py-3.5 px-4 font-bold text-slate-400">
                                {{ $num }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-black text-slate-900 dark:text-white">
                                    {{ $sd['name'] }}
                                </div>
                                <span class="text-[10.5px] text-slate-400">
                                    {{ $sd['meaning'] }} • Target Kelas {{ $sd['grade'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ $sd['total_ayah'] }} Ayat
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($sd['status'] === 'SELESAI')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300/40">
                                        ✓ Selesai
                                    </span>
                                @elseif($sd['status'] === 'SEDANG_DICICIL')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300/40">
                                        ⏳ Dicicil
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                        Belum Disetor
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($sd['completed_count'] > 0)
                                    <div class="space-y-0.5">
                                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400 font-mono">
                                            {{ $sd['completed_verses_text'] }}
                                        </span>
                                        <span class="text-[10.5px] text-slate-400 block">
                                            (Tuntas {{ $sd['completed_count'] }} dari {{ $sd['total_ayah'] }} ayat)
                                        </span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($sd['pending_count'] > 0)
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-rose-500 dark:text-rose-400 font-mono">
                                            {{ $sd['pending_verses_text'] }}
                                        </span>
                                        <span class="text-[10.5px] text-slate-400 block">
                                            (Sisa {{ $sd['pending_count'] }} ayat lagi)
                                        </span>
                                    </div>
                                @else
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold text-xs flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Tuntas 100%</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="font-mono font-black text-xs {{ $sd['progress_percent'] >= 100 ? 'text-emerald-600 dark:text-emerald-400' : ($sd['progress_percent'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400') }}">
                                        {{ $sd['progress_percent'] }}%
                                    </span>
                                    <div class="w-16 bg-slate-100 dark:bg-slate-800 rounded-full h-1 mt-1 overflow-hidden">
                                        <div class="bg-emerald-500 h-1 rounded-full" style="width: {{ min(100, $sd['progress_percent']) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if(Auth::user()->canManageTahfidz())
                                    <a href="{{ route('tahfidz.create', ['student_id' => $student->id, 'class_id' => $student->class_id, 'surah_number' => $num]) }}" class="px-2.5 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 font-bold text-[11px] transition inline-flex items-center gap-1" title="Input setoran cicilan untuk surat ini">
                                        <span>+ Setor</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bagian 3: PENJELASAN & REKAPITULASI AKHIR (SESUAI PENGELOMPOKAN KELAS) -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950 rounded-3xl border-2 border-emerald-500/40 text-white p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
            <div class="space-y-1">
                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    📋 Laporan Rekapitulasi Akhir
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                    Penjelasan Akhir Progres & Status Hafalan {{ $target_grade_label ?? ('Kelas ' . $student_grade) }} Siswa
                </h2>
                <p class="text-xs text-slate-300">
                    Rangkuman status penyelesaian hafalan atas nama <strong>{{ $student->name }}</strong> (NISN: {{ $student->nisn }}).
                </p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-black font-mono text-emerald-400">{{ $overall_percentage }}%</div>
                <span class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Total Capaian Kelas {{ $student_grade }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kolom Kiri: Surat yang SUDAH SELESAI -->
            <div class="bg-slate-850/80 rounded-2xl p-5 border border-emerald-500/30 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-emerald-400 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 flex items-center justify-center text-[8px] text-white font-black">✓</span>
                        A. Surat yang SUDAH SELESAI (100% Tuntas)
                    </h3>
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        {{ $total_completed_surahs }} dari {{ $total_surahs }} Surat Target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }}
                    </span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Daftar surat target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }} yang telah disetorkan seluruh ayatnya dan dinyatakan <strong>LULUS</strong> secara tuntas:
                </p>

                @if(count($completed_surahs_list) > 0)
                    <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                        @foreach($completed_surahs_list as $cs)
                            <div class="p-2.5 rounded-xl bg-slate-800/80 border border-emerald-500/20 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-extrabold text-white">No. {{ $cs['number'] }} {{ $cs['name'] }}</span>
                                    <span class="text-[10.5px] text-slate-400 block font-mono">
                                        Semua Ayat Tuntas: {{ $cs['completed_verses_text'] }} ({{ $cs['total_ayah'] }} Ayat)
                                    </span>
                                </div>
                                <span class="px-2 py-0.5 rounded bg-emerald-500 text-slate-950 font-black text-[10px]">
                                    100% Lulus
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-slate-800/50 border border-slate-700/60 text-center text-xs text-slate-400">
                        Belum ada surat yang selesai 100% pada tingkatan ini.
                    </div>
                @endif
            </div>

            <!-- Kolom Kanan: Surat yang BELUM SELESAI -->
            <div class="bg-slate-850/80 rounded-2xl p-5 border border-rose-500/30 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-rose-400 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500 flex items-center justify-center text-[8px] text-white font-black">!</span>
                        B. Surat yang BELUM SELESAI (Perlu Dilanjutkan)
                    </h3>
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-black bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        {{ $total_pending_surahs }} dari {{ $total_surahs }} Surat Target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }}
                    </span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Rincian surat target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }} yang sedang dicicil sebagian maupun belum pernah disetorkan:
                </p>

                <!-- Sub-bagian 1: Surat yang Sedang Dicicil -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-amber-300 uppercase tracking-wider flex items-center gap-1.5">
                        <span>⏳</span>
                        <span>1. Surat Sedang Dicicil (Sebagian Ayat Selesai, Sebagian Belum):</span>
                    </h4>

                    @php
                        $inInstallmentList = collect($pending_surahs_list)->where('status', 'SEDANG_DICICIL');
                    @endphp

                    @if($inInstallmentList->isNotEmpty())
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            @foreach($inInstallmentList as $ps)
                                <div class="p-3 rounded-xl bg-slate-800/80 border border-amber-500/30 text-xs space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-extrabold text-white">No. {{ $ps['number'] }} {{ $ps['name'] }} (Total {{ $ps['total_ayah'] }} Ayat)</span>
                                        <span class="text-[10px] font-black text-amber-400 font-mono">{{ $ps['progress_percent'] }}%</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                                        <div class="p-2 rounded-lg bg-emerald-950/40 border border-emerald-500/30">
                                            <span class="text-emerald-400 font-bold block">✓ SUDAH Selesai:</span>
                                            <span class="font-mono text-white">{{ $ps['completed_verses_text'] }}</span>
                                            <span class="text-slate-400 block text-[10px]">Total: {{ $ps['completed_count'] }} Ayat</span>
                                        </div>
                                        <div class="p-2 rounded-lg bg-rose-950/40 border border-rose-500/30">
                                            <span class="text-rose-400 font-bold block">✕ BELUM Selesai:</span>
                                            <span class="font-mono text-white">{{ $ps['pending_verses_text'] }}</span>
                                            <span class="text-slate-400 block text-[10px]">Sisa: {{ $ps['pending_count'] }} Ayat Lagi</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-700/50 text-xs text-slate-400 italic">
                            Tidak ada surat yang sedang menggantung di tengah cicilan.
                        </div>
                    @endif
                </div>

                <!-- Sub-bagian 2: Surat yang Belum Pernah Disetor -->
                <div class="space-y-1.5 pt-2 border-t border-slate-800">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                        <span>⚪</span>
                        <span>2. Surat yang Belum Disetor Sama Sekali ({{ $total_unstarted_surahs }} Surat):</span>
                    </h4>
                    @php
                        $unstartedList = collect($pending_surahs_list)->where('status', 'BELUM_DISETOR');
                    @endphp
                    @if($unstartedList->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 max-h-36 overflow-y-auto pr-1">
                            @foreach($unstartedList as $us)
                                <span class="px-2 py-1 rounded-lg bg-slate-800 border border-slate-700 text-[10.5px] text-slate-300 font-medium">
                                    {{ $us['number'] }}. {{ $us['name'] }} ({{ $us['total_ayah'] }} Ayat)
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-xs text-emerald-400 font-bold">Semua surat target kelas ini sudah pernah disetorkan!</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Rekapitulasi Total Ayat Sesuai Target Kelas -->
        <div class="p-4 sm:p-5 rounded-2xl bg-emerald-950/60 border border-emerald-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-300">
                    📊 REKAPITULASI TOTAL AYAT TARGET {{ $target_grade_label ?? ('KELAS ' . $student_grade) }}
                </span>
                <p class="text-xs text-slate-200">
                    Dari total target kurikulum <strong>{{ $target_grade_label ?? ('Kelas ' . $student_grade) }}</strong> sebanyak <strong>{{ $total_target_ayat }} ayat</strong>, ananda <strong>{{ $student->name }}</strong> telah menyelesaikan <strong>{{ $total_completed_ayat }} ayat</strong>, dan masih memiliki sisa tanggungan <strong>{{ $total_pending_ayat }} ayat</strong> yang belum diselesaikan.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <div class="text-center px-3 py-1.5 rounded-xl bg-slate-900/80 border border-emerald-500/30">
                    <span class="text-xs font-bold text-slate-400 block">Selesai</span>
                    <span class="text-lg font-black text-emerald-400 font-mono">{{ $total_completed_ayat }} Ayat</span>
                </div>
                <div class="text-center px-3 py-1.5 rounded-xl bg-slate-900/80 border border-rose-500/30">
                    <span class="text-xs font-bold text-slate-400 block">Belum Selesai</span>
                    <span class="text-lg font-black text-rose-400 font-mono">{{ $total_pending_ayat }} Ayat</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
