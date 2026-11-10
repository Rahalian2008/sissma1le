@extends('layouts.app')

@section('title', 'Edit RPL BK 1 Lembar - ' . $rpl->topic)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit RPL BK 1 Lembar</h1>
            <p class="text-xs text-slate-500">Nomor Registrasi: <span class="font-mono font-bold">{{ $rpl->rpl_code }}</span></p>
        </div>
        <a href="{{ route('bk.rpl.show', $rpl) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Batal / Kembali
        </a>
    </div>

    <form action="{{ route('bk.rpl.update', $rpl) }}" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-5 text-xs">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Topik / Tema Layanan <span class="text-rose-500">*</span></label>
                <input type="text" name="topic" value="{{ old('topic', $rpl->topic) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Komponen Layanan <span class="text-rose-500">*</span></label>
                    <select name="service_component" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="LAYANAN_DASAR" {{ $rpl->service_component === 'LAYANAN_DASAR' ? 'selected' : '' }}>Layanan Dasar</option>
                        <option value="LAYANAN_RESPONSIF" {{ $rpl->service_component === 'LAYANAN_RESPONSIF' ? 'selected' : '' }}>Layanan Responsif</option>
                        <option value="PERENCANAAN_INDIVIDUAL" {{ $rpl->service_component === 'PERENCANAAN_INDIVIDUAL' ? 'selected' : '' }}>Peminatan & Perencanaan Individual</option>
                        <option value="DUKUNGAN_SISTEM" {{ $rpl->service_component === 'DUKUNGAN_SISTEM' ? 'selected' : '' }}>Dukungan Sistem</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Layanan <span class="text-rose-500">*</span></label>
                    <select name="service_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="KLASIKAL" {{ $rpl->service_type === 'KLASIKAL' ? 'selected' : '' }}>Bimbingan Klasikal</option>
                        <option value="KELOMPOK" {{ $rpl->service_type === 'KELOMPOK' ? 'selected' : '' }}>Bimbingan Kelompok</option>
                        <option value="INDIVIDUAL" {{ $rpl->service_type === 'INDIVIDUAL' ? 'selected' : '' }}>Konseling Individual</option>
                        <option value="KOLABORASI" {{ $rpl->service_type === 'KOLABORASI' ? 'selected' : '' }}>Kolaborasi / Mediasi</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bidang Layanan <span class="text-rose-500">*</span></label>
                    <select name="field_of_service" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="PRIBADI" {{ $rpl->field_of_service === 'PRIBADI' ? 'selected' : '' }}>Bidang Pribadi</option>
                        <option value="SOSIAL" {{ $rpl->field_of_service === 'SOSIAL' ? 'selected' : '' }}>Bidang Sosial</option>
                        <option value="BELAJAR" {{ $rpl->field_of_service === 'BELAJAR' ? 'selected' : '' }}>Bidang Belajar</option>
                        <option value="KARIER" {{ $rpl->field_of_service === 'KARIER' ? 'selected' : '' }}>Bidang Karier</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Sasaran / Tingkat Kelas <span class="text-rose-500">*</span></label>
                    <input type="text" name="grade_target" value="{{ old('grade_target', $rpl->grade_target) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Semester <span class="text-rose-500">*</span></label>
                    <input type="text" name="semester" value="{{ old('semester', $rpl->semester) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alokasi Waktu <span class="text-rose-500">*</span></label>
                    <input type="text" name="time_allocation" value="{{ old('time_allocation', $rpl->time_allocation) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Dimensi Profil Pelajar Pancasila</label>
                <input type="text" name="profile_pelajar_pancasila" value="{{ old('profile_pelajar_pancasila', $rpl->profile_pelajar_pancasila) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">A. Capaian & Tujuan Layanan (Learning Objectives) <span class="text-rose-500">*</span></label>
                <textarea name="learning_objectives" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('learning_objectives', $rpl->learning_objectives) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ringkasan Materi Layanan</label>
                    <textarea name="materials_summary" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('materials_summary', $rpl->materials_summary) }}</textarea>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Metode & Media</label>
                    <textarea name="method_and_media" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('method_and_media', $rpl->method_and_media) }}</textarea>
                </div>
            </div>

            <div class="space-y-3 pt-2 border-t border-slate-100">
                <h3 class="font-bold text-slate-900 uppercase text-[11px]">B. Langkah-langkah Kegiatan Layanan</h3>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">1. Tahap Pendahuluan (Awal)</label>
                    <textarea name="step_introduction" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('step_introduction', $rpl->step_introduction) }}</textarea>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">2. Tahap Inti (Kegiatan Utama)</label>
                    <textarea name="step_core" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('step_core', $rpl->step_core) }}</textarea>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">3. Tahap Penutup (Refleksi & Komitmen)</label>
                    <textarea name="step_closing" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('step_closing', $rpl->step_closing) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">C. Evaluasi Proses</label>
                    <textarea name="evaluation_process" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('evaluation_process', $rpl->evaluation_process) }}</textarea>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">D. Evaluasi Hasil</label>
                    <textarea name="evaluation_result" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('evaluation_result', $rpl->evaluation_result) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <button type="button" onclick="if(confirm('Hapus perangkat RPL ini?')) document.getElementById('delete-form').submit();" class="text-rose-600 hover:text-rose-700 font-bold">
                Hapus RPL
            </button>
            <div class="flex items-center gap-2">
                <a href="{{ route('bk.rpl.show', $rpl) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Perbarui RPL 1 Lembar
                </button>
            </div>
        </div>
    </form>

    <form id="delete-form" action="{{ route('bk.rpl.destroy', $rpl) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

