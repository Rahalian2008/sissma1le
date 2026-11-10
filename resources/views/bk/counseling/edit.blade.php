@extends('layouts.app')

@section('title', 'Edit Sesi Konseling - ' . $session->session_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
        <span>/</span>
        <a href="{{ route('bk.counseling.show', $session) }}" class="hover:text-emerald-600 font-medium">{{ $session->session_code }}</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-white font-bold">Edit Sesi</span>
    </div>

    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">Edit Sesi Bimbingan & Konseling [{{ $session->session_code }}]</h1>
            <p class="text-xs text-slate-400 mt-1">Konseli: <strong class="text-slate-700 dark:text-slate-300">{{ $session->student?->name }}</strong> ({{ $session->student?->schoolClass?->name }})</p>
        </div>

        <form action="{{ route('bk.counseling.update', $session) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Tanggal & Tempat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Konseling *</label>
                    <input type="date" name="date" value="{{ old('date', $session->date->format('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat</label>
                    <input type="text" name="place" value="{{ old('place', $session->place) }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Bidang Layanan & Jenis -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bidang Layanan *</label>
                    <select name="field_of_service" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
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

            <!-- Topik -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Pokok Bahasan *</label>
                <input type="text" name="topic" value="{{ old('topic', $session->topic) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
            </div>

            <!-- Masalah -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Masalah *</label>
                <textarea name="problem_description" rows="3" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('problem_description', $session->problem_description) }}</textarea>
            </div>

            <!-- Pendekatan & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Teknik Konseling</label>
                    <input type="text" name="counseling_approach" value="{{ old('counseling_approach', $session->counseling_approach) }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status</label>
                    <select name="status" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="SELESAI" {{ old('status', $session->status) == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                        <option value="SEDANG_BERLANGSUNG" {{ old('status', $session->status) == 'SEDANG_BERLANGSUNG' ? 'selected' : '' }}>Sedang Berlangsung</option>
                        <option value="TERJADWAL" {{ old('status', $session->status) == 'TERJADWAL' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="DIRUJUK" {{ old('status', $session->status) == 'DIRUJUK' ? 'selected' : '' }}>Dirujuk</option>
                    </select>
                </div>
            </div>

            <!-- Kesepakatan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Hasil Konseling & Komitmen</label>
                <textarea name="agreement_result" rows="2" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('agreement_result', $session->agreement_result) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rencana Tindak Lanjut (RTL)</label>
                <textarea name="follow_up_plan" rows="2" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('follow_up_plan', $session->follow_up_plan) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <form action="{{ route('bk.counseling.destroy', $session) }}" method="POST" onsubmit="return confirm('Hapus sesi konseling ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-rose-600 font-bold hover:underline">Hapus Sesi</button>
                </form>
                <div class="flex gap-2">
                    <a href="{{ route('bk.counseling.show', $session) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20">
                        Perbarui Sesi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

