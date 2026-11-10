@extends('layouts.app')

@section('title', 'Edit Surat Keluar - ' . $outgoingLetter->letter_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.outgoing.show', $outgoingLetter) }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Edit Data Surat Keluar</h1>
                <p class="text-xs text-slate-500">Nomor Surat: <strong class="text-blue-600 font-mono">{{ $outgoingLetter->letter_number }}</strong> (No. Agenda: {{ $outgoingLetter->agenda_number }})</p>
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
        <form action="{{ route('letters.outgoing.update', $outgoingLetter) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tujuan Surat (Penerima) <span class="text-rose-500">*</span></label>
                    <input type="text" name="recipient" value="{{ old('recipient', $outgoingLetter->recipient) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Surat <span class="text-rose-500">*</span></label>
                    <input type="date" name="letter_date" value="{{ old('letter_date', $outgoingLetter->letter_date ? $outgoingLetter->letter_date->format('Y-m-d') : '') }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Perihal Surat Keluar <span class="text-rose-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject', $outgoingLetter->subject) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kode Klasifikasi Tata Naskah</label>
                    <select name="classification_code" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono text-xs">
                        @foreach($classifications as $classification)
                            <option value="{{ $classification->code }}" {{ old('classification_code', $outgoingLetter->classification_code) == $classification->code ? 'selected' : '' }}>
                                {{ $classification->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Surat <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="draft" {{ old('status', $outgoingLetter->status) === 'draft' ? 'selected' : '' }}>Konsep / Draft</option>
                        <option value="sent" {{ old('status', $outgoingLetter->status) === 'sent' ? 'selected' : '' }}>Terkirim</option>
                        <option value="archived" {{ old('status', $outgoingLetter->status) === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Penandatangan <span class="text-rose-500">*</span></label>
                    <input type="text" name="signer_name" value="{{ old('signer_name', $outgoingLetter->signer_name) }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NIP Penandatangan</label>
                    <input type="text" name="signer_nip" value="{{ old('signer_nip', $outgoingLetter->signer_nip) }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('letters.outgoing.show', $outgoingLetter) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
