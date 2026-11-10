@extends('layouts.app')

@section('title', 'Buat Surat Pemanggilan Orang Tua - BK')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
        <span>/</span>
        <a href="{{ route('bk.parent-call.index') }}" class="hover:text-emerald-600 font-medium">Panggilan Orang Tua</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-white font-bold">Buat Surat Panggilan</span>
    </div>

    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">Penerbitan Surat Pemanggilan Orang Tua</h1>
            <p class="text-xs text-slate-400 mt-1">Surat resmi permohonan kehadiran orang tua/wali murid ke sekolah untuk musyawarah pembinaan</p>
        </div>

        <form action="{{ route('bk.parent-call.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Siswa & No Surat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Siswa yang Bersangkutan *</label>
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
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Surat Dinas Resmi *</label>
                    <input type="text" name="letter_number" value="{{ old('letter_number', $suggestedNumber) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Tanggal & Jam Hadir & Tempat -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Kehadiran Diharapkan *</label>
                    <input type="date" name="call_date" value="{{ old('call_date', date('Y-m-d', strtotime('+1 day'))) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Kehadiran *</label>
                    <input type="text" name="call_time" value="{{ old('call_time', '08.30 WIB s.d Selesai') }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Pertemuan *</label>
                    <input type="text" name="place" value="{{ old('place', 'Ruang Bimbingan Konseling ' . ($school?->name ?? 'SMA Negeri 1 Lengkong')) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Alasan Pemanggilan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Pemanggilan (Perihal Masalah) *</label>
                <textarea name="reason" rows="3" required placeholder="Tuliskan pokok persoalan yang perlu dimusyawarahkan (misal: evaluasi kehadiran belajar / kedisiplinan siswa / tindak lanjut pembinaan khusus)..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('reason') }}</textarea>
            </div>

            <!-- Status Awal -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Pengiriman Surat *</label>
                <select name="status" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    <option value="SURAT_DIKIRIM" {{ old('status') == 'SURAT_DIKIRIM' ? 'selected' : '' }}>Surat Dikirim ke Orang Tua</option>
                    <option value="HADIR" {{ old('status') == 'HADIR' ? 'selected' : '' }}>Orang Tua Hadir</option>
                    <option value="TIDAK_HADIR" {{ old('status') == 'TIDAK_HADIR' ? 'selected' : '' }}>Tidak Hadir (Perlu Panggilan Ulang)</option>
                    <option value="SELESAI" {{ old('status') == 'SELESAI' ? 'selected' : '' }}>Musyawarah Selesai</option>
                </select>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <a href="{{ route('bk.parent-call.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 text-white font-bold text-xs hover:bg-amber-700 transition shadow-md shadow-amber-600/20">
                    Terbitkan Surat Panggilan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

