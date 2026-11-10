@extends('layouts.app')

@section('title', 'Detail Calon Siswa SPMB - ' . $spmb->full_name)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-2">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold border {{ $spmb->getTrackBadgeClass() }}">
                    Jalur: {{ $spmb->selection_track }}
                </span>
                @if($spmb->spmbWave)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-900 dark:bg-indigo-950 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-800">
                        🗓️ {{ $spmb->spmbWave->name }}
                    </span>
                @endif
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>{{ $spmb->full_name }}</span>
            </h1>
            <p class="text-xs text-slate-500 font-mono mt-0.5">
                No. Registrasi: <span class="font-bold text-blue-700 dark:text-blue-400">{{ $spmb->registration_number }}</span>
                @if($spmb->tka_exam_number)
                    &bull; No. Ujian TKA: <span class="font-bold text-amber-700 dark:text-amber-400">{{ $spmb->tka_exam_number }}</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('spmb.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-xs transition">
                &larr; Kembali ke Daftar
            </a>

            <a href="{{ route('spmb.print-card', $spmb) }}" target="_blank" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Kartu Peserta</span>
            </a>

            @if(in_array($spmb->status, ['Lolos Seleksi', 'Diterima', 'Daftar Ulang']))
                <a href="{{ route('spmb.print-letter', $spmb) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Cetak SK Kelulusan</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Status Banner & Integrasi Promosi Siswa -->
    <div class="p-5 rounded-3xl border-2 {{ $spmb->getStatusBadgeClass() }} flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Status Seleksi SPMB</div>
            <div class="text-xl font-black mt-0.5">{{ $spmb->status }}</div>
            <p class="text-xs mt-1 text-slate-700 dark:text-slate-300">
                @if($spmb->assigned_class)
                    Ditempatkan di Rombel: <strong>{{ $spmb->assigned_class }}</strong>.
                @endif
                @if($spmb->verified_at)
                    Diverifikasi pada: {{ $spmb->verified_at->format('d/m/Y H:i') }} oleh {{ $spmb->verifier?->name ?? 'Panitia' }}.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($spmb->status !== 'Daftar Ulang')
                <form action="{{ route('spmb.promote', $spmb) }}" method="POST" onsubmit="return confirm('Tetapkan calon siswa ini langsung sebagai Siswa Aktif SISSMA1LE?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-black text-xs shadow-md transition cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>Konversi ke Siswa Aktif SISSMA1LE</span>
                    </button>
                </form>
            @else
                <span class="px-3 py-1.5 rounded-xl bg-teal-200 text-teal-900 font-bold text-xs">
                    ✓ Sudah Menjadi Siswa Aktif SISSMA1LE
                </span>
            @endif
        </div>
    </div>

    <!-- Informasi Khusus Jalur Nilai TKA -->
    @if(str_contains($spmb->selection_track, 'TKA') || $spmb->tka_score_total || $spmb->tka_certificate_file)
    <div class="bg-amber-50/70 dark:bg-amber-950/30 border-2 border-amber-300 dark:border-amber-700 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-black shadow-md">
                    🎯
                </div>
                <div>
                    <h3 class="font-black text-amber-900 dark:text-amber-200 text-base">Hasil & Sertifikat Tes Kemampuan Akademik (TKA)</h3>
                    <p class="text-xs text-amber-700 dark:text-amber-400">Nomor Peserta: <strong class="font-mono">{{ $spmb->tka_exam_number ?? '-' }}</strong> &bull; Sesi: {{ $spmb->tka_exam_session ?? 'Lab CBT' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($spmb->tka_rank)
                    <span class="px-3.5 py-1.5 rounded-2xl bg-amber-200 text-amber-950 font-black text-xs border border-amber-300">
                        Peringkat Seleksi: #{{ $spmb->tka_rank }}
                    </span>
                @endif
                @if($spmb->tka_certificate_file)
                    <a href="{{ Storage::url($spmb->tka_certificate_file) }}" target="_blank"
                       class="px-3.5 py-1.5 rounded-2xl bg-white dark:bg-slate-900 text-amber-900 dark:text-amber-300 font-black text-xs border border-amber-300 hover:bg-amber-100 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Lihat Sertifikat TKA</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center">
            <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-amber-200 dark:border-slate-800 shadow-2xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase">Literasi Bahasa</span>
                <div class="text-xl font-black text-blue-600 mt-1">{{ $spmb->tka_score_literacy ?? '-' }}</div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-amber-200 dark:border-slate-800 shadow-2xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase">Numerasi Dasar</span>
                <div class="text-xl font-black text-indigo-600 mt-1">{{ $spmb->tka_score_numeracy ?? '-' }}</div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-amber-200 dark:border-slate-800 shadow-2xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase">Sains / Logika</span>
                <div class="text-xl font-black text-emerald-600 mt-1">{{ $spmb->tka_score_science ?? '-' }}</div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-3.5 rounded-2xl border border-amber-200 dark:border-slate-800 shadow-2xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase">Rata-rata TKA</span>
                <div class="text-xl font-black text-amber-600 mt-1">{{ $spmb->tka_score_total ?? '-' }}</div>
            </div>

            <div class="bg-amber-100 dark:bg-amber-900/40 p-3.5 rounded-2xl border-2 border-amber-300 dark:border-amber-700 shadow-2xs col-span-2 sm:col-span-1">
                <span class="text-[10px] font-black text-amber-900 dark:text-amber-200 uppercase">Skor Terbobot (60:40)</span>
                <div class="text-2xl font-black text-amber-950 dark:text-amber-100 mt-0.5">{{ $spmb->tka_weighted_score ?? '-' }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Berkas Dokumen Identitas Kependudukan (KTP, KK, Akte, PIP/KIP, Sertifikat TKA) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="text-lg">📁</span>
                <h3 class="font-black text-slate-900 dark:text-white text-base">Berkas Dokumen Persyaratan Pendaftar</h3>
            </div>
            <span class="text-xs text-slate-400">Verifikasi Dokumen Asli</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- KTP Orang Tua -->
            <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-lg">🪪</span>
                        @if($spmb->ktp_parent_file)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Kosong</span>
                        @endif
                    </div>
                    <div class="font-bold text-xs text-slate-900 dark:text-white">KTP Orang Tua / Wali</div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Identitas Ayah/Ibu/Wali</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-200 dark:border-slate-700">
                    @if($spmb->ktp_parent_file)
                        <a href="{{ Storage::url($spmb->ktp_parent_file) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                            <span>Buka Dokumen</span> &rarr;
                        </a>
                    @else
                        <span class="text-xs text-slate-400 italic">Belum diunggah</span>
                    @endif
                </div>
            </div>

            <!-- Kartu Keluarga -->
            <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-lg">👨‍👩‍👦</span>
                        @if($spmb->kk_file)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Kosong</span>
                        @endif
                    </div>
                    <div class="font-bold text-xs text-slate-900 dark:text-white">Kartu Keluarga (KK)</div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Data domisili & keluarga</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-200 dark:border-slate-700">
                    @if($spmb->kk_file)
                        <a href="{{ Storage::url($spmb->kk_file) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                            <span>Buka Dokumen</span> &rarr;
                        </a>
                    @else
                        <span class="text-xs text-slate-400 italic">Belum diunggah</span>
                    @endif
                </div>
            </div>

            <!-- Akte Kelahiran -->
            <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-lg">📜</span>
                        @if($spmb->birth_cert_file)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Kosong</span>
                        @endif
                    </div>
                    <div class="font-bold text-xs text-slate-900 dark:text-white">Akte Kelahiran</div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bukti tanggal & tempat lahir</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-200 dark:border-slate-700">
                    @if($spmb->birth_cert_file)
                        <a href="{{ Storage::url($spmb->birth_cert_file) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                            <span>Buka Dokumen</span> &rarr;
                        </a>
                    @else
                        <span class="text-xs text-slate-400 italic">Belum diunggah</span>
                    @endif
                </div>
            </div>

            <!-- PIP / KIP / PKH -->
            <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-lg">💳</span>
                        @if($spmb->pip_kip_file)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-600">Opsional</span>
                        @endif
                    </div>
                    <div class="font-bold text-xs text-slate-900 dark:text-white">Kartu PIP / KIP / PKH</div>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $spmb->kip_pip_number ? 'No: '.$spmb->kip_pip_number : 'Jalur Afirmasi' }}</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-200 dark:border-slate-700">
                    @if($spmb->pip_kip_file)
                        <a href="{{ Storage::url($spmb->pip_kip_file) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                            <span>Buka Dokumen</span> &rarr;
                        </a>
                    @else
                        <span class="text-xs text-slate-400 italic">Tidak ada berkas</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Rincian Nilai & Berkas Rapor SMP/MTs (Semester 1 s.d. 6) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="text-lg">📊</span>
                <h3 class="font-black text-slate-900 dark:text-white text-base">Rincian Nilai & Berkas Scan Rapor SMP/MTs</h3>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-500">Rata-rata Keseluruhan: </span>
                <span class="font-mono font-black text-base text-blue-600 dark:text-blue-400">{{ number_format((float) $spmb->average_score, 2) }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-extrabold text-[10px]">
                    <tr>
                        <th class="px-4 py-3 rounded-l-xl">Semester</th>
                        <th class="px-4 py-3">Tingkat / Keterangan</th>
                        <th class="px-4 py-3 text-center">Nilai Rata-rata</th>
                        <th class="px-4 py-3 text-center">Status Berkas</th>
                        <th class="px-4 py-3 text-right rounded-r-xl">Aksi Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <!-- Sem 1 -->
                    <tr>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">Semester 1</td>
                        <td class="px-4 py-3 text-slate-500">Kelas VII Gasal</td>
                        <td class="px-4 py-3 text-center font-mono font-black text-indigo-600 text-sm">
                            {{ $spmb->report_score_sem1 ? number_format((float) $spmb->report_score_sem1, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($spmb->report_file_sem1)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Terunggah</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Belum Ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($spmb->report_file_sem1)
                                <a href="{{ Storage::url($spmb->report_file_sem1) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:underline">
                                    <span>Lihat Berkas</span> &rarr;
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Sem 2 -->
                    <tr>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">Semester 2</td>
                        <td class="px-4 py-3 text-slate-500">Kelas VII Genap</td>
                        <td class="px-4 py-3 text-center font-mono font-black text-indigo-600 text-sm">
                            {{ $spmb->report_score_sem2 ? number_format((float) $spmb->report_score_sem2, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($spmb->report_file_sem2)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Terunggah</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Belum Ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($spmb->report_file_sem2)
                                <a href="{{ Storage::url($spmb->report_file_sem2) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:underline">
                                    <span>Lihat Berkas</span> &rarr;
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Sem 3 -->
                    <tr>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">Semester 3</td>
                        <td class="px-4 py-3 text-slate-500">Kelas VIII Gasal</td>
                        <td class="px-4 py-3 text-center font-mono font-black text-indigo-600 text-sm">
                            {{ $spmb->report_score_sem3 ? number_format((float) $spmb->report_score_sem3, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($spmb->report_file_sem3)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Terunggah</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Belum Ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($spmb->report_file_sem3)
                                <a href="{{ Storage::url($spmb->report_file_sem3) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:underline">
                                    <span>Lihat Berkas</span> &rarr;
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Sem 4 -->
                    <tr>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">Semester 4</td>
                        <td class="px-4 py-3 text-slate-500">Kelas VIII Genap</td>
                        <td class="px-4 py-3 text-center font-mono font-black text-indigo-600 text-sm">
                            {{ $spmb->report_score_sem4 ? number_format((float) $spmb->report_score_sem4, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($spmb->report_file_sem4)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Terunggah</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Belum Ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($spmb->report_file_sem4)
                                <a href="{{ Storage::url($spmb->report_file_sem4) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:underline">
                                    <span>Lihat Berkas</span> &rarr;
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Sem 5 -->
                    <tr>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">Semester 5</td>
                        <td class="px-4 py-3 text-slate-500">Kelas IX Gasal</td>
                        <td class="px-4 py-3 text-center font-mono font-black text-indigo-600 text-sm">
                            {{ $spmb->report_score_sem5 ? number_format((float) $spmb->report_score_sem5, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($spmb->report_file_sem5)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Terunggah</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Belum Ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($spmb->report_file_sem5)
                                <a href="{{ Storage::url($spmb->report_file_sem5) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:underline">
                                    <span>Lihat Berkas</span> &rarr;
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Sem 6 (Opsional) -->
                    <tr class="bg-slate-50/50 dark:bg-slate-800/30">
                        <td class="px-4 py-3 font-bold text-slate-700 dark:text-slate-300">Semester 6 (Opsional)</td>
                        <td class="px-4 py-3 text-slate-400">Kelas IX Genap</td>
                        <td class="px-4 py-3 text-center font-mono font-bold text-slate-700 dark:text-slate-300 text-sm">
                            {{ $spmb->report_score_sem6 ? number_format((float) $spmb->report_score_sem6, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($spmb->report_file_sem6)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Terunggah</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-600">Opsional</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($spmb->report_file_sem6)
                                <a href="{{ Storage::url($spmb->report_file_sem6) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:underline">
                                    <span>Lihat Berkas</span> &rarr;
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rincian Biodata Calon Siswa & Orang Tua -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs space-y-6">
        <h3 class="font-black text-slate-900 dark:text-white text-base pb-3 border-b border-slate-100 dark:border-slate-800">
            Rincian Biodata & Domisili Calon Siswa
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
            <div class="space-y-3">
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Nama Lengkap:</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $spmb->full_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">NISN:</span>
                    <span class="font-mono font-bold text-blue-600">{{ $spmb->nisn }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">NIK Siswa:</span>
                    <span class="font-mono font-semibold">{{ $spmb->nik ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Jenis Kelamin:</span>
                    <span class="font-semibold">{{ $spmb->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Tempat, Tanggal Lahir:</span>
                    <span class="font-semibold">{{ $spmb->birth_place ?? '-' }}, {{ $spmb->birth_date ? $spmb->birth_date->format('d F Y') : '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Asal SMP / MTs:</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $spmb->school_origin ?? '-' }}</span>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Nama Orang Tua / Wali:</span>
                    <span class="font-semibold">{{ $spmb->parent_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Pekerjaan Orang Tua:</span>
                    <span class="font-semibold">{{ $spmb->parent_job ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">No. WhatsApp / HP:</span>
                    <span class="font-mono font-bold">{{ $spmb->parent_phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Jarak Tempat Tinggal:</span>
                    <span class="font-semibold">{{ $spmb->distance_km ? $spmb->distance_km . ' KM' : '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">No. KIP / PIP / PKH:</span>
                    <span class="font-mono font-semibold">{{ $spmb->kip_pip_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Alamat Lengkap:</span>
                    <span class="font-medium text-right max-w-xs">{{ $spmb->address ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
