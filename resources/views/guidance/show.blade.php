@extends('layouts.app')

@section('title', 'Detail Pembinaan Siswa')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                    {{ $guidance->followUpLabel() }}
                </span>
                <h2 class="text-xl font-extrabold text-slate-900 mt-2">Catatan Konseling: {{ $guidance->student->name }}</h2>
                <p class="text-xs text-slate-400">Tanggal: {{ $guidance->date->format('d F Y') }} • Konselor: {{ $guidance->counselor->name ?? '-' }}</p>
            </div>
            <a href="{{ route('guidance.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">← Kembali</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-6">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                <div class="text-slate-500 font-semibold">Profil Siswa:</div>
                <div class="font-bold text-sm text-slate-900">{{ $guidance->student->name }}</div>
                <div class="text-slate-400">NIS: {{ $guidance->student->nis }} • Kelas {{ $guidance->student->schoolClass?->name }}</div>
                <div class="text-rose-600 font-bold mt-1">Total Poin Pelanggaran: {{ $guidance->student->totalViolationPoints() }} Poin</div>
            </div>

            <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-200/80 space-y-1.5">
                <div class="text-purple-800 font-semibold">Status Penanganan:</div>
                <div class="font-bold text-sm text-purple-900">{{ str_replace('_', ' ', $guidance->status) }}</div>
                <p class="text-slate-500 text-[11px] mt-1">Tingkat: {{ $guidance->followUpLabel() }}</p>
            </div>
        </div>

        <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
            <span class="font-bold text-slate-800 block mb-1">Rekomendasi & Kesepakatan Tindak Lanjut:</span>
            <p class="text-slate-700 leading-relaxed">{{ $guidance->recommendation }}</p>
        </div>

        @if($guidance->notes)
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                <span class="font-bold text-slate-800 block mb-1">Catatan Observasi Konselor:</span>
                <p class="text-slate-600 leading-relaxed">{{ $guidance->notes }}</p>
            </div>
        @endif

        @if($guidance->agreement_letter_path)
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                <span class="font-bold text-slate-800 block mb-2">Surat Perjanjian / Komitmen Siswa:</span>
                <a href="{{ Storage::url($guidance->agreement_letter_path) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white font-bold rounded-xl shadow-xs hover:bg-purple-500 transition">
                    Lihat Berkas Surat Perjanjian ↗
                </a>
            </div>
        @endif

        <!-- Update Status Form -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
            <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Perbarui Status Penanganan</h4>
            <form action="{{ route('guidance.update', $guidance->id) }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 text-xs">
                @csrf
                @method('PUT')
                <select name="status" class="px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none font-bold text-slate-700">
                    <option value="DALAM_PROSES" {{ $guidance->status === 'DALAM_PROSES' ? 'selected' : '' }}>DALAM PROSES</option>
                    <option value="PEMANTAUAN_LANJUTAN" {{ $guidance->status === 'PEMANTAUAN_LANJUTAN' ? 'selected' : '' }}>PEMANTAUAN LANJUTAN</option>
                    <option value="SELESAI" {{ $guidance->status === 'SELESAI' ? 'selected' : '' }}>SELESAI</option>
                </select>
                <input type="text" name="notes" placeholder="Catatan pembaruan..." value="{{ $guidance->notes }}" class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none">
                <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-purple-600 text-white font-bold rounded-xl shadow-xs transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
