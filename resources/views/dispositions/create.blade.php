@extends('layouts.app')

@section('title', 'Buat Lembar Disposisi Surat')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.incoming.show', $letter) }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Buat Lembar Disposisi</h1>
                <p class="text-xs text-slate-500">Instruksi resmi tindak lanjut surat masuk.</p>
            </div>
        </div>
    </div>

    <!-- Letter Reference Banner -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-xs space-y-1">
        <div class="flex items-center justify-between">
            <span class="font-bold text-blue-900 font-mono">No. Agenda: {{ $letter->agenda_number }}</span>
            <span class="text-slate-600">{{ $letter->received_date?->format('d M Y') }}</span>
        </div>
        <div class="font-bold text-slate-900 text-sm">{{ $letter->subject }}</div>
        <div class="text-slate-600">Pengirim: <strong class="text-slate-800">{{ $letter->sender }}</strong> &bull; No: {{ $letter->letter_number }}</div>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <form action="{{ route('letters.incoming.dispositions.store', $letter) }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Diteruskan Kepada <span class="text-rose-500">*</span></label>
                    <select name="target_role_or_user" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Posisi / Jabatan Penerima --</option>
                        @foreach ($targetRoles as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Disposisi <span class="text-rose-500">*</span></label>
                    <input type="date" name="disposition_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Instruksi / Catatan Pimpinan <span class="text-rose-500">*</span></label>
                <textarea name="instructions" rows="4" placeholder="Tuliskan arahan dan instruksi Kepala Sekolah..." required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500"></textarea>
                <div class="flex flex-wrap gap-2 mt-2">
                    <button type="button" onclick="document.querySelector('textarea[name=instructions]').value = 'Mohon dipelajari, dikoordinasikan, dan siapkan tanggapan resmi.'" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-[11px] text-slate-700 cursor-pointer">+ Pelajari & Koordinasi</button>
                    <button type="button" onclick="document.querySelector('textarea[name=instructions]').value = 'Harap hadiri mewakili sekolah dan buat laporan hasil kegiatan.'" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-[11px] text-slate-700 cursor-pointer">+ Hadiri & Laporkan</button>
                    <button type="button" onclick="document.querySelector('textarea[name=instructions]').value = 'Tindaklanjuti sesuai prosedur yang berlaku.'" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-[11px] text-slate-700 cursor-pointer">+ Tindaklanjuti</button>
                    <button type="button" onclick="document.querySelector('textarea[name=instructions]').value = 'Simpan sebagai arsip referensi kepegawaian/kesiswaan.'" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-[11px] text-slate-700 cursor-pointer">+ Arsipkan</button>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tenggat Waktu Penyelesaian (Batas Waktu)</label>
                <input type="date" name="due_date" value="{{ now()->addDays(3)->format('Y-m-d') }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('letters.incoming.show', $letter) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition cursor-pointer">
                    Simpan & Teruskan Disposisi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
