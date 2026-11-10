@extends('layouts.app')

@section('title', 'Edit Home Visit - ' . $visit->visit_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
        <span>/</span>
        <a href="{{ route('bk.home-visit.show', $visit) }}" class="hover:text-emerald-600 font-medium">{{ $visit->visit_code }}</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-white font-bold">Edit Kunjungan</span>
    </div>

    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">Edit Laporan Home Visit [{{ $visit->visit_code }}]</h1>
            <p class="text-xs text-slate-400 mt-1">Siswa: <strong class="text-slate-700 dark:text-slate-300">{{ $visit->student?->name }}</strong> ({{ $visit->student?->schoolClass?->name }})</p>
        </div>

        <form action="{{ route('bk.home-visit.update', $visit) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Tanggal & Surat Tugas -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Kunjungan *</label>
                    <input type="date" name="visit_date" value="{{ old('visit_date', $visit->visit_date->format('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Surat Tugas</label>
                    <input type="text" name="letter_number" value="{{ old('letter_number', $visit->letter_number) }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Kunjungan *</label>
                    <select name="status" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                        <option value="TERLAKSANA" {{ old('status', $visit->status) == 'TERLAKSANA' ? 'selected' : '' }}>Terlaksana</option>
                        <option value="RENCANA" {{ old('status', $visit->status) == 'RENCANA' ? 'selected' : '' }}>Rencana Jadwal</option>
                        <option value="BATAL" {{ old('status', $visit->status) == 'BATAL' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
            </div>

            <!-- Orang Tua & Alamat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Orang Tua / Wali yang Ditemui</label>
                    <input type="text" name="parent_name" value="{{ old('parent_name', $visit->parent_name) }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat yang Dikunjungi</label>
                    <input type="text" name="address_visited" value="{{ old('address_visited', $visit->address_visited) }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Wali Kelas Pendamping -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Wali Kelas Pendamping</label>
                <select name="homeroom_id" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2.5 px-3">
                    <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                    @foreach($homerooms as $h)
                        <option value="{{ $h->id }}" {{ old('homeroom_id', $visit->homeroom_id) == $h->id ? 'selected' : '' }}>
                            {{ $h->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Latar Belakang -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Latar Belakang / Urgensi Kunjungan *</label>
                <textarea name="case_background" rows="3" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('case_background', $visit->case_background) }}</textarea>
            </div>

            <!-- Kondisi & Komitmen -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kondisi Keluarga & Lingkungan Rumah</label>
                    <textarea name="family_condition" rows="3" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('family_condition', $visit->family_condition) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Komitmen / Tanggapan Orang Tua</label>
                    <textarea name="parent_commitment" rows="3" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('parent_commitment', $visit->parent_commitment) }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kesimpulan & Rekomendasi Hasil Home Visit</label>
                <textarea name="visit_result" rows="2" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 p-3">{{ old('visit_result', $visit->visit_result) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <form action="{{ route('bk.home-visit.destroy', $visit) }}" method="POST" onsubmit="return confirm('Hapus data kunjungan rumah ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-rose-600 font-bold hover:underline">Hapus Data</button>
                </form>
                <div class="flex gap-2">
                    <a href="{{ route('bk.home-visit.show', $visit) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition shadow-md shadow-blue-600/20">
                        Perbarui Data Home Visit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

