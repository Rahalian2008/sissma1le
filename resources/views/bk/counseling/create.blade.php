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
                    <select name="field_of_service" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
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
                <input type="text" name="topic" value="{{ old('topic') }}" placeholder="Contoh: Kesulitan mengatur waktu belajar dan penyesuaian gaya belajar" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
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

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rencana Tindak Lanjut (RTL)</label>
                <textarea name="follow_up_plan" rows="2" placeholder="Langkah pemantauan bersama wali kelas, orang tua, atau pertemuan berikutnya..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('follow_up_plan') }}</textarea>
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
@endsection

