@extends('layouts.app')

@section('title', 'Detail Surat Keluar - ' . $outgoingLetter->letter_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.outgoing.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Detail Surat Keluar Resmi</h1>
                <p class="text-xs text-slate-500 font-mono">{{ $outgoingLetter->letter_number }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($isArchived)
                <span class="inline-flex items-center px-3 py-2 bg-emerald-50 text-emerald-800 font-semibold text-xs rounded-xl border border-emerald-200">
                    <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Terarsip
                </span>
            @else
                <form action="{{ route('letters.outgoing.archive', $outgoingLetter) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        Simpan ke Arsip
                    </button>
                </form>
            @endif

            @if($isExpedited)
                <span class="inline-flex items-center px-3 py-2 bg-indigo-50 text-indigo-800 font-semibold text-xs rounded-xl border border-indigo-200">
                    <svg class="w-4 h-4 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Di Buku Ekspedisi
                </span>
            @else
                <form action="{{ route('letters.outgoing.send-expedition', $outgoingLetter) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Kirim via Ekspedisi
                    </button>
                </form>
            @endif

            <a href="{{ route('letters.outgoing.edit', $outgoingLetter) }}" class="inline-flex items-center px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs rounded-xl border border-amber-200 shadow-xs transition">
                <svg class="w-4 h-4 mr-1 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </a>

            <form action="{{ route('letters.outgoing.destroy', $outgoingLetter) }}" method="POST" class="inline" onsubmit="return confirm('Hapus surat keluar ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-xs rounded-xl border border-rose-200 shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus
                </button>
            </form>

            <a href="{{ route('letters.outgoing.print', $outgoingLetter) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Surat Resmi
            </a>
        </div>
    </div>

    <!-- Letter View Card -->
    <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-200 space-y-6 text-xs">
        <div class="border-b border-slate-200 pb-4 flex justify-between items-start">
            <div>
                <span class="text-slate-400 block text-[11px]">Nomor Surat:</span>
                <span class="font-mono font-bold text-base text-slate-900">{{ $outgoingLetter->letter_number }}</span>
                <div class="text-slate-500 font-mono mt-0.5">No. Agenda: {{ $outgoingLetter->agenda_number }}</div>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block text-[11px]">Tanggal Surat:</span>
                <span class="font-bold text-slate-900 text-sm">{{ $outgoingLetter->letter_date?->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Kepada Yth.:</span>
                <span class="font-bold text-slate-900 text-sm block">{{ $outgoingLetter->recipient }}</span>
                <span class="text-slate-500">Di tempat</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Perihal Surat:</span>
                <span class="font-bold text-slate-900 text-sm block">{{ $outgoingLetter->subject }}</span>
                <span class="text-slate-500 font-mono">Klasifikasi: {{ $outgoingLetter->classification_code ?? '421.3' }}</span>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex justify-end">
            <div class="text-center w-64">
                <p class="text-slate-500">{{ $school->city ?? 'Kota' }}, {{ $outgoingLetter->letter_date?->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-900 mt-1">Kepala Sekolah,</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">{{ $outgoingLetter->signer_name }}</p>
                @if($outgoingLetter->signer_nip)
                    <p class="text-[11px] text-slate-500 font-mono">NIP. {{ $outgoingLetter->signer_nip }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
