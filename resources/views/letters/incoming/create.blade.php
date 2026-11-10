@extends('layouts.app')

@section('title', 'Catat Surat Masuk Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.incoming.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Catat Surat Masuk Baru</h1>
                <p class="text-xs text-slate-500">Pencatatan nomor agenda dan lembar penerimaan surat resmi.</p>
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
        <form action="{{ route('letters.incoming.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Surat Masuk <span class="text-rose-500">*</span></label>
                    <input type="text" name="letter_number" value="{{ old('letter_number') }}" placeholder="Contoh: 421.3/089/Disdik/2026" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Asal Instansi / Pengirim <span class="text-rose-500">*</span></label>
                    <input type="text" name="sender" value="{{ old('sender') }}" placeholder="Contoh: Dinas Pendidikan Provinsi DKI Jakarta" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Surat <span class="text-rose-500">*</span></label>
                    <input type="date" name="letter_date" value="{{ old('letter_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Diterima di Sekolah <span class="text-rose-500">*</span></label>
                    <input type="date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Perihal / Isi Ringkas Surat <span class="text-rose-500">*</span></label>
                <textarea name="subject" rows="2" placeholder="Uraian ringkas maksud dan isi surat masuk..." required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('subject') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kode Klasifikasi Surat</label>
                    <select name="classification_code" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono text-xs">
                        <option value="">-- Pilih Kode Klasifikasi --</option>
                        @foreach($classifications as $classification)
                            <option value="{{ $classification->code }}" {{ old('classification_code') == $classification->code ? 'selected' : '' }}>
                                {{ $classification->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sifat Surat <span class="text-rose-500">*</span></label>
                    <select name="character" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="Biasa">Biasa</option>
                        <option value="Penting">Penting</option>
                        <option value="Segera">Segera</option>
                        <option value="Sangat Segera">Sangat Segera</option>
                        <option value="Rahasia">Rahasia</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jumlah Lampiran</label>
                    <input type="text" name="attachment_count" value="{{ old('attachment_count', '1 Berkas') }}" placeholder="1 Berkas / 2 Lembar" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Upload Bukti / Berkas File Surat -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                <label class="block font-semibold text-slate-800 mb-1">
                    Unggah Berkas Bukti / File Surat (PDF / Gambar)
                    <span class="text-slate-400 font-normal">(Opsional, Maks. 10MB)</span>
                </label>
                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                <p class="text-[11px] text-slate-400">Format yang didukung: PDF, JPG, PNG, DOC/DOCX. Berkas ini dapat dilihat dan diunduh di lembar detail surat masuk.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('letters.incoming.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                    Simpan Surat Masuk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
