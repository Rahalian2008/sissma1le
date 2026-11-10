@extends('layouts.app')

@section('title', 'Edit Surat Masuk - ' . $incomingLetter->letter_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.incoming.show', $incomingLetter) }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Edit Data Surat Masuk</h1>
                <p class="text-xs text-slate-500 font-mono">No. Agenda: {{ $incomingLetter->agenda_number }}</p>
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
        <form action="{{ route('letters.incoming.update', $incomingLetter) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Surat Masuk <span class="text-rose-500">*</span></label>
                    <input type="text" name="letter_number" value="{{ old('letter_number', $incomingLetter->letter_number) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Asal Instansi / Pengirim <span class="text-rose-500">*</span></label>
                    <input type="text" name="sender" value="{{ old('sender', $incomingLetter->sender) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Surat <span class="text-rose-500">*</span></label>
                    <input type="date" name="letter_date" value="{{ old('letter_date', $incomingLetter->letter_date?->format('Y-m-d')) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Diterima di Sekolah <span class="text-rose-500">*</span></label>
                    <input type="date" name="received_date" value="{{ old('received_date', $incomingLetter->received_date?->format('Y-m-d')) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Perihal / Isi Ringkas Surat <span class="text-rose-500">*</span></label>
                <textarea name="subject" rows="2" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('subject', $incomingLetter->subject) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kode Klasifikasi Surat</label>
                    <select name="classification_code" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono text-xs">
                        <option value="">-- Pilih Kode Klasifikasi --</option>
                        @foreach($classifications as $classification)
                            <option value="{{ $classification->code }}" {{ old('classification_code', $incomingLetter->classification_code) == $classification->code ? 'selected' : '' }}>
                                {{ $classification->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sifat Surat <span class="text-rose-500">*</span></label>
                    <select name="character" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="Biasa" {{ old('character', $incomingLetter->character) === 'Biasa' ? 'selected' : '' }}>Biasa</option>
                        <option value="Penting" {{ old('character', $incomingLetter->character) === 'Penting' ? 'selected' : '' }}>Penting</option>
                        <option value="Segera" {{ old('character', $incomingLetter->character) === 'Segera' ? 'selected' : '' }}>Segera</option>
                        <option value="Sangat Segera" {{ old('character', $incomingLetter->character) === 'Sangat Segera' ? 'selected' : '' }}>Sangat Segera</option>
                        <option value="Rahasia" {{ old('character', $incomingLetter->character) === 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jumlah Lampiran</label>
                    <input type="text" name="attachment_count" value="{{ old('attachment_count', $incomingLetter->attachment_count) }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Status Penanganan Surat <span class="text-rose-500">*</span></label>
                <select name="status" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="Menunggu Disposisi" {{ old('status', $incomingLetter->status) === 'Menunggu Disposisi' ? 'selected' : '' }}>Menunggu Disposisi</option>
                    <option value="Didisposisikan" {{ old('status', $incomingLetter->status) === 'Didisposisikan' ? 'selected' : '' }}>Didisposisikan</option>
                    <option value="Ditindaklanjuti" {{ old('status', $incomingLetter->status) === 'Ditindaklanjuti' ? 'selected' : '' }}>Ditindaklanjuti</option>
                    <option value="Selesai" {{ old('status', $incomingLetter->status) === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <!-- Upload Bukti / Berkas File Surat -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                <label class="block font-semibold text-slate-800 mb-1">
                    Unggah Bukti / File Berkas Surat (PDF / Gambar)
                    <span class="text-slate-400 font-normal">(Opsional, Maks. 10MB)</span>
                </label>
                @if($incomingLetter->file_path)
                    <div class="flex items-center gap-2 p-2 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 text-xs mb-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="truncate">Berkas saat ini tersimpan: <strong>{{ basename($incomingLetter->file_path) }}</strong></span>
                        <a href="{{ $incomingLetter->file_url }}" target="_blank" class="ml-auto px-2 py-0.5 bg-blue-600 text-white rounded font-bold text-[11px] hover:bg-blue-700">Lihat File</a>
                    </div>
                @endif
                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                <p class="text-[11px] text-slate-400">Pilih berkas baru jika ingin mengganti berkas yang sudah ada.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('letters.incoming.show', $incomingLetter) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
