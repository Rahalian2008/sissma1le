@extends('layouts.app')

@section('title', 'Detail Sesi Konseling - ' . $session->session_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
            <span>/</span>
            <a href="{{ route('bk.counseling.index') }}" class="hover:text-emerald-600 font-medium">Layanan Konseling</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-white font-bold">{{ $session->session_code }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.counseling.print', $session) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 transition shadow-xs">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Berita Acara</span>
            </a>
            <a href="{{ route('bk.counseling.edit', $session) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Sesi</span>
            </a>
            <a href="{{ route('bk.counseling.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-6">
        
        <!-- Header Sesi -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $session->field_badge['class'] }}">
                        {{ $session->field_badge['label'] }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        {{ str_replace('_', ' ', $session->session_type) }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-500/10 text-teal-600 dark:text-teal-400">
                        {{ str_replace('_', ' ', $session->service_category) }}
                    </span>
                    @if($session->is_confidential)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Asas Kerahasiaan BK</span>
                    </span>
                    @endif
                </div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">{{ $session->topic }}</h1>
                <p class="text-xs font-mono text-slate-400 mt-0.5">Kode: <strong class="text-slate-600 dark:text-slate-300">{{ $session->session_code }}</strong> &bull; Tanggal: {{ \Carbon\Carbon::parse($session->date)->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-black {{ $session->status === 'SELESAI' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : ($session->status === 'SEDANG_BERLANGSUNG' ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400') }}">
                    STATUS: {{ str_replace('_', ' ', $session->status) }}
                </span>
            </div>
        </div>

        <!-- Student & Counselor Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Identitas Siswa (Konseli)</span>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center shrink-0">
                        {{ $session->student?->initials ?: substr($session->student?->name ?? 'S', 0, 2) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $session->student?->name }}</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Kelas: {{ $session->student?->schoolClass?->name ?: '-' }} &bull; NISN: {{ $session->student?->nisn ?: '-' }}
                            @if($session->student?->nis) &bull; NIS: {{ $session->student?->nis }} @endif
                        </p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700 text-right">
                    <a href="{{ route('bk.student.profile360', $session->student) }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Profil 360° Siswa &rarr;</a>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Konselor / Guru BK &amp; Tempat</span>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center shrink-0">
                        BK
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $session->counselor?->name ?: 'Guru BK' }}</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">NIP: {{ $session->counselor?->nip ?: '-' }} &bull; Tempat: {{ $session->place ?: 'Ruang Bimbingan Konseling' }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                    <span>Waktu: {{ $session->start_time ?: '08:00' }} - {{ $session->end_time ?: '09:00' }} WIB</span>
                    <span class="font-semibold text-slate-400">{{ $session->session_type }}</span>
                </div>
            </div>
        </div>

        @if($session->field_of_service === 'KARIER' && $session->career_path)
        <!-- Peminatan Bimbingan Karier (Kuliah / Bekerja) -->
        <div class="p-5 rounded-2xl bg-gradient-to-br from-sky-50 to-indigo-50/40 dark:from-sky-950/40 dark:to-slate-900 border border-sky-200 dark:border-sky-900/60 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-sky-200/60 dark:border-sky-800/60">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                        🎓
                    </span>
                    <h3 class="text-xs font-bold text-sky-950 dark:text-sky-200 uppercase tracking-wider">Hasil Peminatan Bimbingan Karier &amp; Arah Masa Depan</h3>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black {{ $session->career_path === 'KULIAH' ? 'bg-sky-600 text-white' : 'bg-emerald-600 text-white' }}">
                    {{ $session->career_path === 'KULIAH' ? '🏛️ RENCANA STUDI LANJUT (KULIAH)' : '💼 RENCANA BEKERJA / WIRAUSAHA' }}
                </span>
            </div>

            @if($session->career_path === 'KULIAH')
            <div class="space-y-3 pt-1">
                <div class="p-3.5 rounded-xl bg-white/90 dark:bg-slate-800/90 border border-sky-200 dark:border-sky-900/50 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-semibold mb-0.5">Jalur Masuk Kuliah Target</span>
                        <span class="text-sm font-black text-sky-700 dark:text-sky-300">{{ $session->study_admission_track ?: 'Belum ditentukan' }}</span>
                    </div>
                    <span class="text-xs font-bold text-sky-600 bg-sky-50 dark:bg-sky-950 px-2.5 py-1 rounded-lg">
                        {{ count($session->campus_choices ?: []) }} Target Kampus Terpilih
                    </span>
                </div>

                @if(!empty($session->campus_choices) && is_array($session->campus_choices))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($session->campus_choices as $idx => $choice)
                    <div class="p-3.5 rounded-2xl bg-white/90 dark:bg-slate-800/90 border {{ $idx === 0 ? 'border-sky-300 dark:border-sky-700 ring-2 ring-sky-400/20 shadow-xs' : 'border-slate-200 dark:border-slate-700' }} space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $idx === 0 ? 'bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 border border-sky-300 dark:border-sky-800' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                🎯 Pilihan {{ $choice['order'] ?? ($idx + 1) }} {{ $idx === 0 ? '(Prioritas Utama)' : '(Alternatif Cadangan)' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block">Nama Perguruan Tinggi Target</span>
                            <span class="text-xs font-black text-slate-900 dark:text-white block mt-0.5">{{ $choice['campus'] ?: '-' }}</span>
                        </div>
                        <div class="pt-1.5 border-t border-slate-100 dark:border-slate-700/60">
                            <span class="text-[10px] text-slate-400 block">Program Studi / Jurusan</span>
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 block mt-0.5">{{ $choice['major'] ?: '-' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-2xl bg-white/90 dark:bg-slate-800/90 border border-sky-200 dark:border-sky-900/40">
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-semibold mb-0.5">Perguruan Tinggi Target</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $session->campus_name ?: '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/90 dark:bg-slate-800/90 border border-sky-200 dark:border-sky-900/40">
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-semibold mb-0.5">Program Studi / Jurusan</span>
                        <span class="text-sm font-bold text-indigo-700 dark:text-indigo-300">{{ $session->study_program ?: '-' }}</span>
                    </div>
                </div>
                @endif
            </div>
            @elseif($session->career_path === 'BEKERJA')
            <div class="p-4 rounded-xl bg-white/90 dark:bg-slate-800/90 border border-sky-200 dark:border-sky-900/40 space-y-1">
                <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-semibold">Bidang Pekerjaan / Perusahaan / Rencana Kerja yang Diminati</span>
                <span class="text-sm font-bold text-slate-900 dark:text-white block">{{ $session->work_field ?: 'Pencarian lowongan kerja / wirausaha mandiri' }}</span>
            </div>
            @endif
        </div>
        @endif

        <!-- Problem Description -->
        <div>
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Latar Belakang &amp; Deskripsi Masalah Konseling</h3>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line">
                {{ $session->problem_description }}
            </div>
        </div>

        <!-- Pendekatan / Teknik Konseling (POP-BK / ABKIN) -->
        @if($session->counseling_approach)
        <div>
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Pendekatan &amp; Teknik Konseling (Standar POP-BK)</h3>
            <div class="p-3.5 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-200 text-xs font-semibold border border-emerald-200 dark:border-emerald-900/50 flex items-center gap-2.5">
                <span class="text-base">🧠</span>
                <div>
                    <span class="font-bold">{{ $session->counseling_approach }}</span>
                    <p class="text-[11px] text-emerald-700 dark:text-emerald-400 font-normal mt-0.5">Teknik konseling resmi terstandarisasi Asosiasi Bimbingan dan Konseling Indonesia (ABKIN)</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Agreement Result -->
        @if($session->agreement_result)
        <div>
            <h3 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2">Hasil Konseling &amp; Komitmen Tindakan Siswa</h3>
            <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 text-xs leading-relaxed text-emerald-900 dark:text-emerald-200 whitespace-pre-line">
                {{ $session->agreement_result }}
            </div>
        </div>
        @endif

        <!-- Follow-up Plan & Tanggal RTL -->
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 space-y-3">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-base">📅</span>
                    <h3 class="text-xs font-bold text-amber-950 dark:text-amber-200 uppercase tracking-wider">Rencana Tindak Lanjut (RTL) &amp; Pertemuan Lanjutan</h3>
                </div>
                @if($session->follow_up_date)
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700 flex items-center gap-1">
                            <span>Jadwal RTL:</span>
                            <strong class="underline">{{ \Carbon\Carbon::parse($session->follow_up_date)->translatedFormat('l, d F Y') }}</strong>
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $session->follow_up_status === 'SELESAI' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white animate-pulse' }}">
                            {{ $session->follow_up_status === 'SELESAI' ? 'SELESAI DITINDAKLANJUTI' : 'MENUNGGU PERTEMUAN (PENDING)' }}
                        </span>
                    </div>
                @endif
            </div>

            @if($session->follow_up_plan)
                <div class="p-3.5 rounded-xl bg-white/80 dark:bg-slate-800/80 border border-amber-100 dark:border-amber-900/30 text-xs leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line">
                    {{ $session->follow_up_plan }}
                </div>
            @endif

            @if($session->follow_up_date)
                <div class="text-[11px] text-amber-700 dark:text-amber-400 flex items-center gap-1.5 pt-1 border-t border-amber-200/50 dark:border-amber-900/30">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span>Notifikasi pengingat sesi ini terdaftar otomatis untuk Koordinator BK dan Guru BK pembina.</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
