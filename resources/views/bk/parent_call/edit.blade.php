@extends('layouts.app')

@section('title', 'Edit Panggilan Orang Tua - ' . $parentCall->call_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
        <span>/</span>
        <a href="{{ route('bk.parent-call.show', $parentCall) }}" class="hover:text-emerald-600 font-medium">{{ $parentCall->call_code }}</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-white font-bold">Edit Panggilan</span>
    </div>

    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">Edit & Tindak Lanjut Panggilan Orang Tua</h1>
            <p class="text-xs text-slate-400 mt-1">Siswa: <strong class="text-slate-700 dark:text-slate-300">{{ $parentCall->student?->name }}</strong> ({{ $parentCall->student?->schoolClass?->name }})</p>
        </div>

        <form action="{{ route('bk.parent-call.update', $parentCall) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- No Surat & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Surat *</label>
                    <input type="text" name="letter_number" value="{{ old('letter_number', $parentCall->letter_number) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Kehadiran *</label>
                    <input type="date" name="call_date" value="{{ old('call_date', $parentCall->call_date->format('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu</label>
                    <input type="text" name="call_time" value="{{ old('call_time', $parentCall->call_time) }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Tempat & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat</label>
                    <input type="text" name="place" value="{{ old('place', $parentCall->place) }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Kehadiran *</label>
                    <select name="status" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="SURAT_DIKIRIM" {{ old('status', $parentCall->status) == 'SURAT_DIKIRIM' ? 'selected' : '' }}>Surat Dikirim</option>
                        <option value="HADIR" {{ old('status', $parentCall->status) == 'HADIR' ? 'selected' : '' }}>Orang Tua Hadir</option>
                        <option value="TIDAK_HADIR" {{ old('status', $parentCall->status) == 'TIDAK_HADIR' ? 'selected' : '' }}>Tidak Hadir</option>
                        <option value="SELESAI" {{ old('status', $parentCall->status) == 'SELESAI' ? 'selected' : '' }}>Selesai Musyawarah</option>
                    </select>
                </div>
            </div>

            <!-- Alasan Pemanggilan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Pemanggilan *</label>
                <textarea name="reason" rows="3" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('reason', $parentCall->reason) }}</textarea>
            </div>

            <!-- Berita Acara & Perjanjian -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Berita Acara Hasil Pertemuan</label>
                <textarea name="meeting_notes" rows="3" placeholder="Catat intisari pembicaraan dan tanggapan orang tua/wali murid saat hadir..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('meeting_notes', $parentCall->meeting_notes) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Surat Perjanjian & Kesepakatan Siswa / Orang Tua</label>
                <textarea name="parent_agreement" rows="3" placeholder="Pernyataan kesanggupan dan sanksi jika melanggar kembali..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('parent_agreement', $parentCall->parent_agreement) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <form action="{{ route('bk.parent-call.destroy', $parentCall) }}" method="POST" onsubmit="return confirm('Hapus surat panggilan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-rose-600 font-bold hover:underline">Hapus Surat</button>
                </form>
                <div class="flex gap-2">
                    <a href="{{ route('bk.parent-call.show', $parentCall) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 text-white font-bold text-xs hover:bg-amber-700 transition shadow-md shadow-amber-600/20">
                        Perbarui Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

