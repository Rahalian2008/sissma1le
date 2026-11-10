@extends('layouts.app')

@section('title', 'Buat Surat Keluar Resmi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.outgoing.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Buat Surat Keluar Resmi</h1>
                <p class="text-xs text-slate-500">Nomor surat dinas resmi digenerate otomatis berdasarkan klasifikasi dan urutan tahunan.</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800">
            <p class="font-bold mb-1">Terdapat kesalahan formulir:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <form action="{{ route('letters.outgoing.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tujuan Surat (Penerima) <span class="text-rose-500">*</span></label>
                    <input type="text" name="recipient" value="{{ old('recipient') }}" placeholder="Contoh: Kepala Dinas Pendidikan Provinsi DKI Jakarta" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Surat <span class="text-rose-500">*</span></label>
                    <input type="date" name="letter_date" value="{{ old('letter_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Perihal Surat Keluar <span class="text-rose-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Contoh: Undangan Rapat Koordinasi Kurikulum Satuan Pendidikan" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kode Klasifikasi Tata Naskah (Pergub Jabar No. 14/2018 & SIKD Jabar)</label>
                <select name="classification_code" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono text-xs">
                    @forelse($classifications as $classification)
                        <option value="{{ $classification->code }}" {{ (old('classification_code', request('classification_code')) == $classification->code) ? 'selected' : '' }}>
                            {{ $classification->name }}
                        </option>
                    @empty
                        <option value="PK.03.03">PK.03.03 - Pendidikan Menengah (SMA)</option>
                    @endforelse
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Sistem menyusun format penomoran resmi standar Pergub Jabar No. 14/2018 & SIKD: <strong>[Kode Pokok] / [No. Verbal] / [Kode Klasifikasi] - {{ $school->name ?? 'SMAN 1 Lengkong' }} / [Bulan Romawi] / {{ date('Y') }}</strong></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Penandatangan <span class="text-rose-500">*</span></label>
                    <input type="text" name="signer_name" value="{{ old('signer_name', $school->headmaster_name ?? $school->principal_name ?? '') }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NIP Penandatangan</label>
                    <input type="text" name="signer_nip" value="{{ old('signer_nip', $school->headmaster_nip ?? $school->principal_nip ?? '') }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('letters.outgoing.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                    Terbitkan Nomor & Buat Surat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
