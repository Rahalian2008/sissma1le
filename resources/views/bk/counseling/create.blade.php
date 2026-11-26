@extends('layouts.app')

@section('title', 'Catat Sesi Bimbingan & Konseling - BK')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
        <span>/</span>
        <a href="{{ route('bk.counseling.index') }}" class="hover:text-emerald-600 font-medium">Layanan Konseling</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-white font-bold">Catat Sesi Baru</span>
    </div>

    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">Form Layanan Bimbingan & Konseling</h1>
            <p class="text-xs text-slate-400 mt-1">Dokumentasi operasional konseling berstandar Panduan Operasional Penyelenggaraan BK (POP-BK)</p>
        </div>

        <form action="{{ route('bk.counseling.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Siswa & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Siswa Konseli *</label>
                    <select name="student_id" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ (old('student_id') ?? $selectedStudent?->id) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->schoolClass?->name ?: 'Kelas -' }}) - NISN: {{ $s->nisn }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Konseling *</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Bidang Layanan & Komponen Program -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bidang Layanan BK *</label>
                    <select name="field_of_service" id="field_of_service" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="PRIBADI" {{ old('field_of_service') == 'PRIBADI' ? 'selected' : '' }}>Bimbingan Pribadi</option>
                        <option value="SOSIAL" {{ old('field_of_service') == 'SOSIAL' ? 'selected' : '' }}>Bimbingan Sosial</option>
                        <option value="BELAJAR" {{ old('field_of_service') == 'BELAJAR' ? 'selected' : '' }}>Bimbingan Belajar</option>
                        <option value="KARIER" {{ old('field_of_service') == 'KARIER' ? 'selected' : '' }}>Bimbingan Karier</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Layanan *</label>
                    <select name="session_type" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="INDIVIDUAL" {{ old('session_type') == 'INDIVIDUAL' ? 'selected' : '' }}>Konseling Individual</option>
                        <option value="KELOMPOK" {{ old('session_type') == 'KELOMPOK' ? 'selected' : '' }}>Konseling Kelompok</option>
                        <option value="KLASIKAL" {{ old('session_type') == 'KLASIKAL' ? 'selected' : '' }}>Bimbingan Klasikal</option>
                        <option value="MEDIASI" {{ old('session_type') == 'MEDIASI' ? 'selected' : '' }}>Mediasi</option>
                        <option value="KONFERENSI_KASUS" {{ old('session_type') == 'KONFERENSI_KASUS' ? 'selected' : '' }}>Konferensi Kasus</option>
                        <option value="REFERRAL" {{ old('session_type') == 'REFERRAL' ? 'selected' : '' }}>Alih Tangan Kasus</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Komponen Program *</label>
                    <select name="service_category" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="LAYANAN_RESPONSIF" {{ old('service_category') == 'LAYANAN_RESPONSIF' ? 'selected' : '' }}>Layanan Responsif</option>
                        <option value="LAYANAN_DASAR" {{ old('service_category') == 'LAYANAN_DASAR' ? 'selected' : '' }}>Layanan Dasar</option>
                        <option value="PERENCANAAN_INDIVIDUAL" {{ old('service_category') == 'PERENCANAAN_INDIVIDUAL' ? 'selected' : '' }}>Perencanaan Individual</option>
                        <option value="DUKUNGAN_SISTEM" {{ old('service_category') == 'DUKUNGAN_SISTEM' ? 'selected' : '' }}>Dukungan Sistem</option>
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
                            <h3 class="text-xs font-bold text-sky-950 dark:text-sky-300">Peminatan Bimbingan Karier & Arah Masa Depan Siswa</h3>
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
                            <input type="radio" name="career_path" value="KULIAH" id="career_path_kuliah" {{ old('career_path') === 'KULIAH' ? 'checked' : '' }} class="text-sky-600 focus:ring-sky-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                <span>🏛️</span> Kuliah (Perguruan Tinggi)
                            </span>
                        </label>
                        <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer hover:border-sky-500 transition has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/60 dark:has-[:checked]:bg-sky-900/30 has-[:checked]:ring-1 has-[:checked]:ring-sky-500">
                            <input type="radio" name="career_path" value="BEKERJA" id="career_path_bekerja" {{ old('career_path') === 'BEKERJA' ? 'checked' : '' }} class="text-sky-600 focus:ring-sky-500">
                            <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                <span>💼</span> Bekerja / Wirausaha
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Sub-Pilihan 1: Jika Memilih Kuliah -->
                <div id="college_detail_group" class="hidden space-y-3 pt-3 border-t border-sky-100 dark:border-sky-900/30">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Pilihan Jalur Kuliah *
                            </label>
                            <select name="study_admission_track" id="study_admission_track" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                                <option value="">-- Pilih Jalur Kuliah --</option>
                                <option value="SNBP" {{ old('study_admission_track') == 'SNBP' ? 'selected' : '' }}>SNBP (Prestasi / Rapor)</option>
                                <option value="SNBT" {{ old('study_admission_track') == 'SNBT' ? 'selected' : '' }}>SNBT (Tes / UTBK)</option>
                                <option value="SPMB" {{ old('study_admission_track') == 'SPMB' ? 'selected' : '' }}>SPMB (Politeknik / Vokasi)</option>
                                <option value="MANDIRI" {{ old('study_admission_track') == 'MANDIRI' ? 'selected' : '' }}>Mandiri (PTN / PTS)</option>
                                <option value="KEDINASAN" {{ old('study_admission_track') == 'KEDINASAN' ? 'selected' : '' }}>Sekolah Kedinasan</option>
                                <option value="BEASISWA" {{ old('study_admission_track') == 'BEASISWA' ? 'selected' : '' }}>Beasiswa Luar / Dalam Negeri</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Nama Perguruan Tinggi Target *
                            </label>
                            <input type="text"
                                   name="campus_name"
                                   id="campus_name"
                                   value="{{ old('campus_name') }}"
                                   placeholder="Contoh: Universitas Indonesia, ITB, UPI, Unpad..."
                                   class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Jurusan / Program Studi *
                            </label>
                            <input type="text"
                                   name="study_program"
                                   id="study_program"
                                   value="{{ old('study_program') }}"
                                   placeholder="Contoh: Teknik Informatika, Manajemen, Kedokteran..."
                                   class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        </div>
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
                               value="{{ old('work_field') }}"
                               placeholder="Contoh: Teknisi Jaringan IT, Industri Otomotif, Perbankan, BUMN, atau Wirausaha Kuliner..."
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    </div>
                </div>
            </div>

            <!-- Tempat & Waktu -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Sesi</label>
                    <input type="text" name="place" value="{{ old('place', 'Ruang Bimbingan Konseling') }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Mulai</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Selesai</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Topik & Deskripsi Masalah -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Pokok Bahasan *</label>
                <input type="text" name="topic" value="{{ old('topic') }}" placeholder="Contoh: Konsultasi pemilihan jalur masuk PTN dan peminatan prodi saintek" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Latar Belakang & Deskripsi Masalah *</label>
                <textarea name="problem_description" rows="3" required placeholder="Uraikan keluhan, gejala, atau latar belakang konseling secara komprehensif..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('problem_description') }}</textarea>
            </div>

            <!-- Pendekatan & Kesepakatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pendekatan / Teknik Konseling</label>
                    <input type="text" name="counseling_approach" value="{{ old('counseling_approach') }}" placeholder="Contoh: Cognitive Behavioral Therapy (CBT) / Solution-Focused" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Sesi</label>
                    <select name="status" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="SELESAI" {{ old('status') == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                        <option value="SEDANG_BERLANGSUNG" {{ old('status') == 'SEDANG_BERLANGSUNG' ? 'selected' : '' }}>Sedang Berlangsung / Pemantauan</option>
                        <option value="TERJADWAL" {{ old('status') == 'TERJADWAL' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="DIRUJUK" {{ old('status') == 'DIRUJUK' ? 'selected' : '' }}>Dirujuk / Alih Tangan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Hasil Konseling & Komitmen Siswa</label>
                <textarea name="agreement_result" rows="2" placeholder="Komitmen tindakan nyata yang disepakati oleh siswa..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('agreement_result') }}</textarea>
            </div>

            <!-- Rencana Tindak Lanjut (RTL) & Tanggal Rencana Pertemuan -->
            <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📅</span>
                        <div>
                            <h3 class="text-xs font-bold text-amber-900 dark:text-amber-300">Rencana Tindak Lanjut (RTL) & Jadwal Pertemuan Lanjutan</h3>
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
                               value="{{ old('follow_up_date') }}"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tanggal ini otomatis terkirim sebagai notifikasi pengingat ke akun Koordinator BK & Guru BK.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Status Tindak Lanjut
                        </label>
                        <select name="follow_up_status" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                            <option value="PENDING" {{ old('follow_up_status') === 'PENDING' ? 'selected' : '' }}>Menunggu Jadwal Pertemuan (Pending)</option>
                            <option value="SELESAI" {{ old('follow_up_status') === 'SELESAI' ? 'selected' : '' }}>Selesai Ditindaklanjuti</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Uraian Rencana Tindak Lanjut (RTL)</label>
                    <textarea name="follow_up_plan" rows="2" placeholder="Langkah pemantauan bersama wali kelas, orang tua, konsultasi lanjutan dengan Guru BK, atau latihan soal/persiapan tes..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('follow_up_plan') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_confidential" value="1" id="is_confidential" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="is_confidential" class="text-xs text-slate-600 dark:text-slate-400">
                    Jaga Asas Kerahasiaan BK (Hanya dapat diakses oleh Guru BK dan Kepala Sekolah)
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <a href="{{ route('bk.counseling.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20">
                    Simpan Sesi Konseling
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

    // Inisialisasi kondisi awal form
    toggleCareerPanel();
});
</script>
@endsection
