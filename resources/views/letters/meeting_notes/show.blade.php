@extends('layouts.app')

@section('title', 'Notula Rapat - ' . $meetingNote->meeting_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.meeting-notes.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Notula & Risalah Rapat Dinas</h1>
                <p class="text-xs text-slate-500 font-mono">{{ $meetingNote->meeting_number }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('letters.meeting-notes.print', $meetingNote) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Notula
            </a>
            <form action="{{ route('letters.meeting-notes.destroy', $meetingNote) }}" method="POST" class="inline" onsubmit="return confirm('Hapus notula rapat ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-xs rounded-xl border border-rose-200 shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-sm border border-slate-200 space-y-6 text-xs">
        <div class="border-b border-slate-200 pb-4 flex justify-between items-start">
            <div>
                <span class="text-slate-400 block text-[11px]">Agenda Rapat:</span>
                <h2 class="text-base font-bold text-slate-900 mt-0.5">{{ $meetingNote->meeting_title }}</h2>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 mt-2">
                    {{ $meetingNote->meeting_type ?? 'Rapat Kedinasan' }}
                </span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block text-[11px]">Tanggal & Waktu:</span>
                <span class="font-bold text-slate-900 text-sm block">{{ $meetingNote->meeting_date?->translatedFormat('d F Y') }}</span>
                <span class="text-slate-500 font-mono">{{ $meetingNote->start_time }} - {{ $meetingNote->end_time ?: 'Selesai' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50 rounded-xl">
                <span class="text-slate-400 block text-[11px]">Tempat Pelaksanaan:</span>
                <strong class="text-slate-800 text-sm mt-0.5 block">{{ $meetingNote->location }}</strong>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl">
                <span class="text-slate-400 block text-[11px]">Pimpinan Rapat:</span>
                <strong class="text-slate-800 text-sm mt-0.5 block">{{ $meetingNote->chairperson_name }}</strong>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl">
                <span class="text-slate-400 block text-[11px]">Notulis Rapat:</span>
                <strong class="text-slate-800 text-sm mt-0.5 block">{{ $meetingNote->secretary_name }}</strong>
            </div>
        </div>

        @if($meetingNote->agenda_topics)
            <div>
                <h3 class="font-bold text-slate-900 mb-2 text-sm">Susunan Acara / Agenda:</h3>
                <div class="p-4 bg-slate-50 rounded-xl whitespace-pre-line text-slate-700 leading-relaxed">{{ $meetingNote->agenda_topics }}</div>
            </div>
        @endif

        @if($meetingNote->discussion_summary)
            <div>
                <h3 class="font-bold text-slate-900 mb-2 text-sm">Ringkasan Jalannya Pembahasan:</h3>
                <div class="p-4 bg-slate-50 rounded-xl whitespace-pre-line text-slate-700 leading-relaxed">{{ $meetingNote->discussion_summary }}</div>
            </div>
        @endif

        @if($meetingNote->decisions_and_follow_up)
            <div>
                <h3 class="font-bold text-slate-900 mb-2 text-sm text-emerald-800">Kesimpulan, Keputusan & Tindak Lanjut:</h3>
                <div class="p-4 bg-emerald-50/60 border border-emerald-200 rounded-xl whitespace-pre-line text-emerald-900 font-medium leading-relaxed">{{ $meetingNote->decisions_and_follow_up }}</div>
            </div>
        @endif

        @if($meetingNote->attendance_file_path)
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between">
                <div>
                    <strong class="text-blue-900 block font-bold">Berkas Fisik Daftar Hadir / Notula Terlampir</strong>
                    <span class="text-blue-600 text-[11px]">Dokumen bukti kehadiran telah diunggah</span>
                </div>
                <a href="{{ Storage::url($meetingNote->attendance_file_path) }}" target="_blank" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-xs shadow-xs transition">
                    Lihat Berkas
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
