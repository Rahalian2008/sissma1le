@extends('layouts.app')

@section('title', 'Susun RPL BK 1 Lembar Kurikulum Merdeka')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Susun RPL BK 1 Lembar Kurikulum Merdeka</h1>
            <p class="text-xs text-slate-500">Rencana Pelaksanaan Layanan Bimbingan Konseling format ringkas dan terstandar POP-BK.</p>
        </div>
        <a href="{{ route('bk.rpl.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Batal / Kembali
        </a>
    </div>

    <form action="{{ route('bk.rpl.store') }}" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-5 text-xs">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Topik / Tema Layanan <span class="text-rose-500">*</span></label>
                <input type="text" name="topic" placeholder="Contoh: Mengembangkan Sikap Asertif dalam Menghadapi Tekanan Teman Sebaya" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Komponen Layanan <span class="text-rose-500">*</span></label>
                    <select name="service_component" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="LAYANAN_DASAR">Layanan Dasar</option>
                        <option value="LAYANAN_RESPONSIF">Layanan Responsif</option>
                        <option value="PERENCANAAN_INDIVIDUAL">Peminatan & Perencanaan Individual</option>
                        <option value="DUKUNGAN_SISTEM">Dukungan Sistem</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Layanan <span class="text-rose-500">*</span></label>
                    <select name="service_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="KLASIKAL">Bimbingan Klasikal</option>
                        <option value="KELOMPOK">Bimbingan Kelompok</option>
                        <option value="INDIVIDUAL">Konseling Individual</option>
                        <option value="KOLABORASI">Kolaborasi / Mediasi</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bidang Layanan <span class="text-rose-500">*</span></label>
                    <select name="field_of_service" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="PRIBADI">Bidang Pribadi</option>
                        <option value="SOSIAL">Bidang Sosial</option>
                        <option value="BELAJAR">Bidang Belajar</option>
                        <option value="KARIER">Bidang Karier</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Sasaran / Tingkat Kelas <span class="text-rose-500">*</span></label>
                    <input type="text" name="grade_target" value="Fase E / Kelas X" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Semester <span class="text-rose-500">*</span></label>
                    <input type="text" name="semester" value="Ganjil" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alokasi Waktu <span class="text-rose-500">*</span></label>
                    <input type="text" name="time_allocation" value="1 Pertemuan x 45 Menit" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Dimensi Profil Pelajar Pancasila</label>
                <input type="text" name="profile_pelajar_pancasila" value="Mandiri, Bernalar Kritis, dan Berakhlak Mulia" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">A. Capaian & Tujuan Layanan (Learning Objectives) <span class="text-rose-500">*</span></label>
                <textarea name="learning_objectives" rows="3" required placeholder="1. Peserta didik dapat...&#10;2. Peserta didik mampu...&#10;3. Peserta didik berkomitmen..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ringkasan Materi Layanan</label>
                    <textarea name="materials_summary" rows="2" placeholder="Uraian poin materi..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Metode & Media</label>
                    <textarea name="method_and_media" rows="2" placeholder="Contoh: Brainstorming, Diskusi Kelompok, LKPD, LCD Proyektor..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
                </div>
            </div>

            <div class="space-y-3 pt-2 border-t border-slate-100">
                <h3 class="font-bold text-slate-900 uppercase text-[11px]">B. Langkah-langkah Kegiatan Layanan</h3>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">1. Tahap Pendahuluan (Awal)</label>
                    <textarea name="step_introduction" rows="2" placeholder="Salam, apersepsi, ice breaking, penjelasan tujuan..." class="w-full px-3 py-2 border border-slate-300 rounded-xl">Guru BK membuka dengan salam hangat, membina hubungan baik, apersepsi keterkaitan materi, dan menyampaikan tujuan layanan.</textarea>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">2. Tahap Inti (Kegiatan Utama)</label>
                    <textarea name="step_core" rows="3" placeholder="Aktivitas eksplorasi, penugasan, diskusi kelompok, presentasi..." class="w-full px-3 py-2 border border-slate-300 rounded-xl">Peserta didik mengamati materi yang disajikan, berdiskusi memecahkan studi kasus dalam kelompok, dan mempresentasikan solusi.</textarea>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">3. Tahap Penutup (Refleksi & Komitmen)</label>
                    <textarea name="step_closing" rows="2" placeholder="Kesimpulan, refleksi, tindak lanjut..." class="w-full px-3 py-2 border border-slate-300 rounded-xl">Guru BK bersama peserta didik menyimpulkan kegiatan, melakukan refleksi pemahaman, dan merencanakan tindak lanjut konseling.</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">C. Evaluasi Proses</label>
                    <textarea name="evaluation_process" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl">Observasi sikap, antusiasme, dan keaktifan peserta didik selama kegiatan berlangsung.</textarea>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">D. Evaluasi Hasil</label>
                    <textarea name="evaluation_result" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl">Pengisian angket penilaian hasil kepuasan dan lembar komitmen tindakan (Action Plan).</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
            <a href="{{ route('bk.rpl.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                Simpan RPL 1 Lembar
            </button>
        </div>
    </form>
</div>
@endsection

