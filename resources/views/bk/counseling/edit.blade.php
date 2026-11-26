@extends('layouts.app')

@section('title', 'Edit Sesi Konseling - ' . $session->session_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
        <span>/</span>
        <a href="{{ route('bk.counseling.index') }}" class="hover:text-emerald-600 font-medium">Layanan Konseling</a>
        <span>/</span>
        <a href="{{ route('bk.counseling.show', $session) }}" class="hover:text-emerald-600 font-medium">{{ $session->session_code }}</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-white font-bold">Edit Sesi</span>
    </div>

    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">Edit Sesi Bimbingan &amp; Konseling</h1>
                <p class="text-xs text-slate-400 mt-1">Kode Sesi: <strong class="font-mono text-emerald-600 dark:text-emerald-400">{{ $session->session_code }}</strong> &bull; Berstandar Panduan Operasional Penyelenggaraan BK (POP-BK)</p>
            </div>
            <a href="{{ route('bk.counseling.show', $session) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
                &larr; Kembali ke Detail
            </a>
        </div>

        <form action="{{ route('bk.counseling.update', $session) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Siswa & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Siswa Konseli *</label>
                    <select name="student_id" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ (old('student_id', $session->student_id) == $s->id) ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->schoolClass?->name ?: 'Kelas -' }}) - NISN: {{ $s->nisn }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Konseling *</label>
                    <input type="date" name="date" value="{{ old('date', $session->date ? $session->date->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Bidang Layanan & Komponen Program -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bidang Layanan BK *</label>
                    <select name="field_of_service" id="field_of_service" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="PRIBADI" {{ old('field_of_service', $session->field_of_service) == 'PRIBADI' ? 'selected' : '' }}>Bimbingan Pribadi</option>
                        <option value="SOSIAL" {{ old('field_of_service', $session->field_of_service) == 'SOSIAL' ? 'selected' : '' }}>Bimbingan Sosial</option>
                        <option value="BELAJAR" {{ old('field_of_service', $session->field_of_service) == 'BELAJAR' ? 'selected' : '' }}>Bimbingan Belajar</option>
                        <option value="KARIER" {{ old('field_of_service', $session->field_of_service) == 'KARIER' ? 'selected' : '' }}>Bimbingan Karier</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Layanan *</label>
                    <select name="session_type" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="INDIVIDUAL" {{ old('session_type', $session->session_type) == 'INDIVIDUAL' ? 'selected' : '' }}>Konseling Individual</option>
                        <option value="KELOMPOK" {{ old('session_type', $session->session_type) == 'KELOMPOK' ? 'selected' : '' }}>Konseling Kelompok</option>
                        <option value="KLASIKAL" {{ old('session_type', $session->session_type) == 'KLASIKAL' ? 'selected' : '' }}>Bimbingan Klasikal</option>
                        <option value="MEDIASI" {{ old('session_type', $session->session_type) == 'MEDIASI' ? 'selected' : '' }}>Mediasi</option>
                        <option value="KONFERENSI_KASUS" {{ old('session_type', $session->session_type) == 'KONFERENSI_KASUS' ? 'selected' : '' }}>Konferensi Kasus</option>
                        <option value="REFERRAL" {{ old('session_type', $session->session_type) == 'REFERRAL' ? 'selected' : '' }}>Alih Tangan Kasus</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Komponen Program *</label>
                    <select name="service_category" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="LAYANAN_RESPONSIF" {{ old('service_category', $session->service_category) == 'LAYANAN_RESPONSIF' ? 'selected' : '' }}>Layanan Responsif</option>
                        <option value="LAYANAN_DASAR" {{ old('service_category', $session->service_category) == 'LAYANAN_DASAR' ? 'selected' : '' }}>Layanan Dasar</option>
                        <option value="PERENCANAAN_INDIVIDUAL" {{ old('service_category', $session->service_category) == 'PERENCANAAN_INDIVIDUAL' ? 'selected' : '' }}>Perencanaan Individual</option>
                        <option value="DUKUNGAN_SISTEM" {{ old('service_category', $session->service_category) == 'DUKUNGAN_SISTEM' ? 'selected' : '' }}>Dukungan Sistem</option>
                    </select>
                </div>
            </div>

            <!-- Panel Khusus Bimbingan Karier (Tampil Otomatis saat Bimbingan Karier Dipilih) -->
            <div id="career_guidance_panel" class="hidden p-4 sm:p-5 rounded-2xl bg-sky-50/80 dark:bg-sky-950/20 border border-sky-200 dark:border-sky-900/50 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-sky-200/60 dark:border-sky-900/40">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                            🎓
                        </span>
                        <div>
                            <h3 class="text-xs font-bold text-sky-950 dark:text-sky-300">Peminatan Bimbingan Karier &amp; Arah Masa Depan Siswa</h3>
                            <p class="text-[11px] text-sky-700 dark:text-sky-400">Pilihan rencana studi lanjut (Kuliah) atau bekerja pasca kelulusan</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-sky-100 dark:bg-sky-900/60 text-sky-700 dark:text-sky-300 border border-sky-300 dark:border-sky-800">
                        Bimbingan Karier
                    </span>
                </div>

                <!-- Pilihan 1: Kuliah atau Bekerja -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Rencana Arah Pasca Kelulusan *
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md">
                        <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer hover:border-sky-500 transition has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/60 dark:has-[:checked]:bg-sky-900/30 has-[:checked]:ring-1 has-[:checked]:ring-sky-500">
                            <input type="radio" name="career_path" value="KULIAH" id="career_path_kuliah" {{ old('career_path', $session->career_path) === 'KULIAH' ? 'checked' : '' }} class="text-sky-600 focus:ring-sky-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                <span>🏛️</span> Kuliah (Perguruan Tinggi)
                            </span>
                        </label>
                        <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer hover:border-sky-500 transition has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/60 dark:has-[:checked]:bg-sky-900/30 has-[:checked]:ring-1 has-[:checked]:ring-sky-500">
                            <input type="radio" name="career_path" value="BEKERJA" id="career_path_bekerja" {{ old('career_path', $session->career_path) === 'BEKERJA' ? 'checked' : '' }} class="text-sky-600 focus:ring-sky-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                <span>💼</span> Bekerja / Wirausaha
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Sub-Pilihan 1: Jika Memilih Kuliah (Multi Kampus & Prodi Target) -->
                <div id="college_detail_group" class="hidden space-y-4 pt-3 border-t border-sky-100 dark:border-sky-900/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Pilihan Jalur Kuliah -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Pilihan Jalur Masuk Kuliah *
                            </label>
                            <select name="study_admission_track" id="study_admission_track" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3 focus:ring-1 focus:ring-sky-500">
                                <option value="">-- Pilih Jalur Masuk --</option>
                                <option value="SNBP" {{ old('study_admission_track', $session->study_admission_track) == 'SNBP' ? 'selected' : '' }}>SNBP (Prestasi / Rapor)</option>
                                <option value="SNBT" {{ old('study_admission_track', $session->study_admission_track) == 'SNBT' ? 'selected' : '' }}>SNBT (Tes UTBK SNPMB)</option>
                                <option value="SPMB" {{ old('study_admission_track', $session->study_admission_track) == 'SPMB' ? 'selected' : '' }}>SPMB (Politeknik / Vokasi)</option>
                                <option value="MANDIRI" {{ old('study_admission_track', $session->study_admission_track) == 'MANDIRI' ? 'selected' : '' }}>Seleksi Mandiri (PTN / PTS)</option>
                                <option value="KEDINASAN" {{ old('study_admission_track', $session->study_admission_track) == 'KEDINASAN' ? 'selected' : '' }}>Sekolah Kedinasan</option>
                                <option value="BEASISWA" {{ old('study_admission_track', $session->study_admission_track) == 'BEASISWA' ? 'selected' : '' }}>Beasiswa Penuh (KIP-K / Prestasi)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pilihan Target Perguruan Tinggi (Bisa Lebih Dari Satu) -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Target Perguruan Tinggi &amp; Program Studi (Bisa Lebih Dari Satu) *
                                </label>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Tentukan prioritas kampus dan jurusan yang dituju siswa (Pilihan 1, Pilihan 2, dst.)
                                </p>
                            </div>
                            <button type="button" id="btn-add-choice" class="px-3 py-1.5 text-xs font-bold rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800 hover:bg-sky-100 dark:hover:bg-sky-900/40 flex items-center gap-1.5 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Pilihan Kampus</span>
                            </button>
                        </div>

                        <!-- Fallback Hidden Inputs untuk Kompatibilitas Tunggal -->
                        <input type="hidden" name="campus_name" id="hidden_campus_name" value="{{ old('campus_name', $session->campus_name) }}">
                        <input type="hidden" name="study_program" id="hidden_study_program" value="{{ old('study_program', $session->study_program) }}">

                        <!-- Container Kartu Pilihan Kampus Dinamis -->
                        <div id="campus_choices_container" class="space-y-3">
                            <!-- Pilihan 1 (Prioritas Utama) Server-Side Rendered -->
                            <div class="choice-item p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 space-y-3 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                        <span>🎯 Pilihan 1 (Prioritas Utama)</span>
                                    </span>
                                </div>

                                <input type="hidden" name="campus_choices[0][order]" value="1">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                                    <div class="campus-combobox-wrapper">
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">
                                                Nama Perguruan Tinggi Target *
                                            </label>
                                            <span class="text-[10px] text-sky-600 dark:text-sky-400 font-semibold">PTN / PTS Se-Jawa</span>
                                        </div>
                                        <div class="relative">
                                            <input type="text"
                                                   name="campus_choices[0][campus]"
                                                   list="campus_datalist"
                                                   value="{{ old('campus_choices.0.campus', $session->campus_choices[0]['campus'] ?? $session->campus_name) }}"
                                                   placeholder="Cari universitas atau ketik manual..."
                                                   class="choice-campus-input w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3 focus:ring-1 focus:ring-sky-500 transition-colors">
                                        </div>
                                    </div>

                                    <div class="major-combobox-wrapper">
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">
                                                Jurusan / Program Studi *
                                            </label>
                                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">Daftar Prodi</span>
                                        </div>
                                        <div class="relative">
                                            <input type="text"
                                                   name="campus_choices[0][major]"
                                                   list="major_datalist"
                                                   value="{{ old('campus_choices.0.major', $session->campus_choices[0]['major'] ?? $session->study_program) }}"
                                                   placeholder="Cari jurusan atau ketik manual..."
                                                   class="choice-major-input w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3 focus:ring-1 focus:ring-sky-500 transition-colors">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Datalist Autocomplete Native PTN/PTS se-Jawa & Prodi -->
                        <datalist id="campus_datalist">
                            @if(isset($universities))
                                @foreach($universities as $u)
                                    <option value="{{ $u['name'] }}">{{ $u['short_name'] }} - {{ $u['province'] }} ({{ $u['type'] }})</option>
                                @endforeach
                            @endif
                        </datalist>

                        <datalist id="major_datalist">
                            @if(isset($standardMajors))
                                @foreach($standardMajors as $group => $majors)
                                    @foreach($majors as $m)
                                        <option value="{{ $m }}">{{ $group }}</option>
                                    @endforeach
                                @endforeach
                            @endif
                        </datalist>
                    </div>
                </div>

                <!-- Sub-Pilihan 2: Jika Memilih Bekerja -->
                <div id="work_detail_group" class="hidden space-y-3 pt-3 border-t border-sky-100 dark:border-sky-900/30">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Bidang Pekerjaan / Perusahaan / Rencana Kerja yang Diminati
                        </label>
                        <input type="text"
                               name="work_field"
                               id="work_field"
                               value="{{ old('work_field', $session->work_field) }}"
                               placeholder="Contoh: Teknisi Jaringan IT, Industri Otomotif, Perbankan, BUMN, atau Wirausaha Kuliner..."
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    </div>
                </div>
            </div>

            <!-- Tempat & Waktu -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Sesi</label>
                    <input type="text" name="place" value="{{ old('place', $session->place ?? 'Ruang Bimbingan Konseling') }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Mulai</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $session->start_time) }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Selesai</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $session->end_time) }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Topik & Deskripsi Masalah -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Pokok Bahasan *</label>
                <input type="text" name="topic" value="{{ old('topic', $session->topic) }}" placeholder="Contoh: Konsultasi pemilihan jalur masuk PTN dan peminatan prodi saintek" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Latar Belakang &amp; Deskripsi Masalah *</label>
                <textarea name="problem_description" rows="3" required placeholder="Uraikan keluhan, gejala, atau latar belakang konseling secara komprehensif..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('problem_description', $session->problem_description) }}</textarea>
            </div>

            <!-- Pendekatan & Kesepakatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Pendekatan / Teknik Konseling (Standar POP-BK)
                    </label>
                    <select id="select_counseling_approach" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3 focus:ring-1 focus:ring-emerald-500">
                        <option value="">-- Pilih Pendekatan / Teknik Konseling --</option>
                        <option value="Cognitive Behavioral Therapy (CBT)">Cognitive Behavioral Therapy (CBT) - Restrukturisasi Kognitif</option>
                        <option value="Solution-Focused Brief Therapy (SFBT)">Solution-Focused Brief Therapy (SFBT) - Berfokus Solusi</option>
                        <option value="Konseling Realitas / WDEP">Konseling Realitas / WDEP (Wants, Direction, Evaluation, Plan)</option>
                        <option value="Rational Emotive Behavior Therapy (REBT)">Rational Emotive Behavior Therapy (REBT) - Terapi Rasional Emotif</option>
                        <option value="Behavioral Therapy">Behavioral Therapy - Penguatan Positif &amp; Kontrak Perilaku</option>
                        <option value="Person-Centered Therapy (Carl Rogers)">Person-Centered Therapy (Carl Rogers) - Humanistik</option>
                        <option value="Trait and Factor">Trait and Factor - Keselarasan Potensi, Minat &amp; Karier</option>
                        <option value="Konseling Gestalt">Konseling Gestalt - Eksplorasi Kesadaran Diri &amp; Kursi Kosong</option>
                        <option value="Relaksasi &amp; Regulasi Emosi (Mindfulness)">Relaksasi &amp; Regulasi Emosi (Mindfulness) - Regulasi Kecemasan</option>
                        <option value="Konseling Singkat Berorientasi Solusi (KSBS)">Konseling Singkat Berorientasi Solusi (KSBS)</option>
                        <option value="Peer Counseling (Bimbingan Teman Sebaya)">Peer Counseling (Bimbingan Teman Sebaya)</option>
                        <option value="CUSTOM">Lainnya (Ketik Manual / Kustom)</option>
                    </select>

                    <input type="text"
                           name="counseling_approach"
                           id="counseling_approach"
                           value="{{ old('counseling_approach', $session->counseling_approach) }}"
                           placeholder="Ketik teknik / pendekatan konseling kustom..."
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3 mt-2 {{ old('counseling_approach', $session->counseling_approach) ? '' : 'hidden' }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Sesi</label>
                    <select name="status" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="SELESAI" {{ old('status', $session->status) == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                        <option value="SEDANG_BERLANGSUNG" {{ old('status', $session->status) == 'SEDANG_BERLANGSUNG' ? 'selected' : '' }}>Sedang Berlangsung / Pemantauan</option>
                        <option value="TERJADWAL" {{ old('status', $session->status) == 'TERJADWAL' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="DIRUJUK" {{ old('status', $session->status) == 'DIRUJUK' ? 'selected' : '' }}>Dirujuk / Alih Tangan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Hasil Konseling &amp; Komitmen Siswa</label>
                <textarea name="agreement_result" rows="2" placeholder="Komitmen tindakan nyata yang disepakati oleh siswa..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('agreement_result', $session->agreement_result) }}</textarea>
            </div>

            <!-- Rencana Tindak Lanjut (RTL) & Tanggal Rencana Pertemuan -->
            <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📅</span>
                        <div>
                            <h3 class="text-xs font-bold text-amber-900 dark:text-amber-300">Rencana Tindak Lanjut (RTL) &amp; Jadwal Pertemuan Lanjutan</h3>
                            <p class="text-[11px] text-amber-700 dark:text-amber-400">Tentukan rencana pemantauan serta tanggal pertemuan sesi tindak lanjut berikutnya</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                        Notifikasi Otomatis
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Tanggal Rencana Pertemuan Berikutnya (Follow-Up RTL)
                        </label>
                        <input type="date"
                               name="follow_up_date"
                               id="follow_up_date"
                               value="{{ old('follow_up_date', $session->follow_up_date ? $session->follow_up_date->format('Y-m-d') : '') }}"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tanggal ini otomatis terkirim sebagai notifikasi pengingat ke akun Koordinator BK &amp; Guru BK.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Status Tindak Lanjut
                        </label>
                        <select name="follow_up_status" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                            <option value="PENDING" {{ old('follow_up_status', $session->follow_up_status) === 'PENDING' ? 'selected' : '' }}>Menunggu Jadwal Pertemuan (Pending)</option>
                            <option value="SELESAI" {{ old('follow_up_status', $session->follow_up_status) === 'SELESAI' ? 'selected' : '' }}>Selesai Ditindaklanjuti</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Uraian Rencana Tindak Lanjut (RTL)</label>
                    <textarea name="follow_up_plan" rows="2" placeholder="Langkah pemantauan bersama wali kelas, orang tua, konsultasi lanjutan dengan Guru BK, atau latihan soal/persiapan tes..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('follow_up_plan', $session->follow_up_plan) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_confidential" value="1" id="is_confidential" {{ old('is_confidential', $session->is_confidential) ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="is_confidential" class="text-xs text-slate-600 dark:text-slate-400">
                    Jaga Asas Kerahasiaan BK (Hanya dapat diakses oleh Guru BK dan Kepala Sekolah)
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <a href="{{ route('bk.counseling.show', $session) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20">
                    Perbarui Sesi Konseling
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldSelect = document.getElementById('field_of_service');
    const careerPanel = document.getElementById('career_guidance_panel');
    const radioKuliah = document.getElementById('career_path_kuliah');
    const radioBekerja = document.getElementById('career_path_bekerja');
    const collegeGroup = document.getElementById('college_detail_group');
    const workGroup = document.getElementById('work_detail_group');

    function toggleCareerPanel() {
        if (!fieldSelect || !careerPanel) return;
        if (fieldSelect.value === 'KARIER') {
            careerPanel.classList.remove('hidden');
            togglePathDetails();
        } else {
            careerPanel.classList.add('hidden');
        }
    }

    function togglePathDetails() {
        if (!radioKuliah || !radioBekerja) return;
        if (radioKuliah.checked) {
            collegeGroup.classList.remove('hidden');
            workGroup.classList.add('hidden');
        } else if (radioBekerja.checked) {
            workGroup.classList.remove('hidden');
            collegeGroup.classList.add('hidden');
        } else {
            collegeGroup.classList.add('hidden');
            workGroup.classList.add('hidden');
        }
    }

    if (fieldSelect) {
        fieldSelect.addEventListener('change', toggleCareerPanel);
    }
    if (radioKuliah) {
        radioKuliah.addEventListener('change', togglePathDetails);
    }
    if (radioBekerja) {
        radioBekerja.addEventListener('change', togglePathDetails);
    }

    // Run initial state
    toggleCareerPanel();

    // ========================================================
    // LOGIKA PILIHAN MULTI KAMPUS & PROGRAM STUDI TARGET
    // ========================================================
    const choicesContainer = document.getElementById('campus_choices_container');
    const btnAddChoice = document.getElementById('btn-add-choice');
    const hiddenCampusName = document.getElementById('hidden_campus_name');
    const hiddenStudyProgram = document.getElementById('hidden_study_program');

    let rawOldChoices = @json(old('campus_choices', $session->campus_choices ?: []));
    let choices = [];

    if (Array.isArray(rawOldChoices) && rawOldChoices.length > 0) {
        choices = rawOldChoices;
    } else if (hiddenCampusName && hiddenCampusName.value.trim() !== '') {
        choices = [
            { order: 1, campus: hiddenCampusName.value.trim(), major: hiddenStudyProgram ? hiddenStudyProgram.value.trim() : '' },
            { order: 2, campus: '', major: '' }
        ];
    } else {
        choices = [
            { order: 1, campus: '', major: '' },
            { order: 2, campus: '', major: '' }
        ];
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderChoices() {
        if (!choicesContainer) return;
        choicesContainer.innerHTML = '';

        choices.forEach((choice, index) => {
            const isFirst = index === 0;
            const badgeClass = isFirst 
                ? 'bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 border border-sky-300 dark:border-sky-800' 
                : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700';

            const card = document.createElement('div');
            card.className = 'choice-item p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-xs space-y-2 relative transition-all';
            card.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold ${badgeClass}">
                        <span>🎯 Pilihan ${index + 1} ${isFirst ? '(Prioritas Utama)' : '(Alternatif Cadangan)'}</span>
                    </span>
                    ${!isFirst ? `
                        <button type="button" class="btn-remove-choice text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 text-xs font-semibold flex items-center gap-1 transition" data-index="${index}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus Pilihan</span>
                        </button>
                    ` : ''}
                </div>

                <input type="hidden" name="campus_choices[${index}][order]" value="${index + 1}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                    <div class="campus-combobox-wrapper">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">
                                Nama Perguruan Tinggi Target ${isFirst ? '*' : ''}
                            </label>
                            <span class="text-[10px] text-sky-600 dark:text-sky-400 font-semibold">PTN / PTS Se-Jawa</span>
                        </div>
                        <div class="relative">
                            <input type="text"
                                   name="campus_choices[${index}][campus]"
                                   list="campus_datalist"
                                   value="${escapeHtml(choice.campus || '')}"
                                   placeholder="Cari universitas atau ketik manual..."
                                   class="choice-campus-input w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3 focus:ring-1 focus:ring-sky-500 transition-colors">
                        </div>
                    </div>

                    <div class="major-combobox-wrapper">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">
                                Jurusan / Program Studi ${isFirst ? '*' : ''}
                            </label>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">Daftar Prodi</span>
                        </div>
                        <div class="relative">
                            <input type="text"
                                   name="campus_choices[${index}][major]"
                                   list="major_datalist"
                                   value="${escapeHtml(choice.major || '')}"
                                   placeholder="Cari jurusan atau ketik manual..."
                                   class="choice-major-input w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3 focus:ring-1 focus:ring-sky-500 transition-colors">
                        </div>
                    </div>
                </div>
            `;

            const campusInput = card.querySelector('.choice-campus-input');
            const majorInput = card.querySelector('.choice-major-input');

            campusInput.addEventListener('input', function() {
                choice.campus = this.value;
                if (index === 0 && hiddenCampusName) {
                    hiddenCampusName.value = this.value;
                }
            });

            majorInput.addEventListener('input', function() {
                choice.major = this.value;
                if (index === 0 && hiddenStudyProgram) {
                    hiddenStudyProgram.value = this.value;
                }
            });

            const btnRemove = card.querySelector('.btn-remove-choice');
            if (btnRemove) {
                btnRemove.addEventListener('click', function() {
                    const removeIdx = parseInt(this.getAttribute('data-index'), 10);
                    choices.splice(removeIdx, 1);
                    renderChoices();
                });
            }

            choicesContainer.appendChild(card);
        });

        if (choices.length > 0) {
            if (hiddenCampusName) hiddenCampusName.value = choices[0].campus || '';
            if (hiddenStudyProgram) hiddenStudyProgram.value = choices[0].major || '';
        }
    }

    if (btnAddChoice) {
        btnAddChoice.addEventListener('click', function() {
            choices.push({
                order: choices.length + 1,
                campus: '',
                major: ''
            });
            renderChoices();
            const items = choicesContainer.querySelectorAll('.choice-item');
            if (items.length > 0) {
                items[items.length - 1].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }

    renderChoices();

    // ========================================================
    // STANDARISASI PENDEKATAN / TEKNIK KONSELING (POP-BK / ABKIN)
    // ========================================================
    const approachSelect = document.getElementById('select_counseling_approach');
    const approachInput = document.getElementById('counseling_approach');

    if (approachSelect && approachInput) {
        const initialVal = approachInput.value.trim();
        if (initialVal) {
            let matched = false;
            for (let opt of approachSelect.options) {
                if (opt.value && opt.value !== 'CUSTOM' && opt.value.toLowerCase() === initialVal.toLowerCase()) {
                    approachSelect.value = opt.value;
                    matched = true;
                    break;
                }
            }
            if (!matched) {
                approachSelect.value = 'CUSTOM';
                approachInput.classList.remove('hidden');
            }
        }

        approachSelect.addEventListener('change', function() {
            if (this.value === 'CUSTOM') {
                approachInput.classList.remove('hidden');
                approachInput.focus();
            } else if (this.value === '') {
                approachInput.value = '';
                approachInput.classList.add('hidden');
            } else {
                approachInput.value = this.value;
                approachInput.classList.add('hidden');
            }
        });
    }
});
</script>
@endsection
