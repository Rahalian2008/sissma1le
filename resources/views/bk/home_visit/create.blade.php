@extends('layouts.app')

@section('title', 'Rencana Kunjungan Rumah (Home Visit) - BK')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
        <span>/</span>
        <a href="{{ route('bk.home-visit.index') }}" class="hover:text-emerald-600 font-medium">Home Visit</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-white font-bold">Rencana Kunjungan Baru</span>
    </div>

    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">Formulir Kunjungan Rumah (Home Visit)</h1>
            <p class="text-xs text-slate-400 mt-1">Dokumentasi resmi surat tugas dan berita acara kunjungan ke tempat tinggal peserta didik</p>
        </div>

        <form action="{{ route('bk.home-visit.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Siswa & Wali Kelas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Siswa yang Dikunjungi *</label>
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
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Wali Kelas Pendamping</label>
                    <select name="homeroom_id" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                        @foreach($homerooms as $h)
                            <option value="{{ $h->id }}" {{ old('homeroom_id') == $h->id ? 'selected' : '' }}>
                                {{ $h->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tanggal & Nomor Surat Tugas -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Kunjungan *</label>
                    <input type="date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Surat Tugas</label>
                    <input type="text" name="letter_number" value="{{ old('letter_number') }}" placeholder="Contoh: 421.3/089-ST/SMA1LE/XI/2026" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Kunjungan *</label>
                    <select name="status" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="TERLAKSANA" {{ old('status') == 'TERLAKSANA' ? 'selected' : '' }}>Terlaksana</option>
                        <option value="RENCANA" {{ old('status') == 'RENCANA' ? 'selected' : '' }}>Rencana Jadwal</option>
                        <option value="BATAL" {{ old('status') == 'BATAL' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
            </div>

            <!-- Orang Tua & Alamat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Orang Tua / Wali yang Ditemui</label>
                    <input type="text" name="parent_name" value="{{ old('parent_name') }}" placeholder="Nama ayah/ibu/wali" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Rumah yang Dikunjungi</label>
                    <input type="text" name="address_visited" value="{{ old('address_visited') }}" placeholder="Kampung/Desa, RT/RW, Kecamatan" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Latar Belakang Kasus -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Latar Belakang / Urgensi Kunjungan *</label>
                <textarea name="case_background" rows="3" required placeholder="Jelaskan alasan mengapa perlu dilakukan kunjungan rumah (misal: absensi alpa berturut-turut, sakit menahun, kesulitan ekonomi)..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('case_background') }}</textarea>
            </div>

            <!-- Kondisi Keluarga & Kesepakatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kondisi Keluarga & Lingkungan Rumah</label>
                    <textarea name="family_condition" rows="3" placeholder="Deskripsi kondisi sosial-ekonomi, keharmonisan keluarga, fasilitas belajar di rumah..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('family_condition') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Komitmen / Tanggapan Orang Tua</label>
                    <textarea name="parent_commitment" rows="3" placeholder="Kesanggupan orang tua dalam mendampingi dan mengawasi putra/putrinya..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('parent_commitment') }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kesimpulan & Rekomendasi Hasil Home Visit</label>
                <textarea name="visit_result" rows="2" placeholder="Rencana tindak lanjut dan solusi pemecahan masalah yang disepakati..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('visit_result') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <a href="{{ route('bk.home-visit.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition shadow-md shadow-blue-600/20">
                    Simpan Data Home Visit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

