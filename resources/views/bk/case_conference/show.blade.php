@extends('layouts.app')

@section('title', 'Notula Konferensi Kasus - ' . $caseConference->case_title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('bk.case-conference.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Kembali ke Daftar Kasus
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.case-conference.edit', $caseConference) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                Edit Data
            </a>
            <a href="{{ route('bk.case-conference.print', $caseConference) }}" target="_blank" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Berita Acara & Notula
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-md space-y-6 text-xs text-slate-700">
        <div class="text-center border-b border-slate-200 pb-4">
            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-purple-100 text-purple-800">
                BERITA ACARA SIDANG KONFERENSI KASUS (CASE CONFERENCE)
            </span>
            <h1 class="text-lg font-bold text-slate-900 mt-2">{{ $caseConference->case_title }}</h1>
            <p class="text-xs text-slate-500">Nomor Registrasi Sidang: <span class="font-mono font-bold">{{ $caseConference->conference_code }}</span></p>
        </div>

        <div class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-purple-50/50 border border-purple-100">
            <div>
                <p><strong class="text-slate-800">Nama Siswa:</strong> {{ $caseConference->student?->name }}</p>
                <p class="mt-1"><strong class="text-slate-800">Kelas / NISN:</strong> {{ $caseConference->student?->schoolClass?->name }} / {{ $caseConference->student?->nisn ?? '-' }}</p>
                <p class="mt-1"><strong class="text-slate-800">Pimpinan Sidang:</strong> {{ $caseConference->leader_name }}</p>
            </div>
            <div>
                <p><strong class="text-slate-800">Hari & Tanggal:</strong> {{ $caseConference->conference_date?->translatedFormat('l, d F Y') }}</p>
                <p class="mt-1"><strong class="text-slate-800">Waktu & Tempat:</strong> {{ $caseConference->conference_time }} ({{ $caseConference->place }})</p>
                <p class="mt-1"><strong class="text-slate-800">Status Sidang:</strong> <span class="font-bold text-purple-700">{{ $caseConference->status }}</span></p>
            </div>
        </div>

        <!-- Peserta Hadir -->
        <div>
            <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-2">Daftar Pihak yang Hadir (Peserta Musyawarah)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach ($caseConference->attendees ?? [] as $att)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-slate-900">{{ $att['name'] ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500">{{ $att['role'] ?? '-' }}</div>
                        </div>
                        <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">Hadir</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Uraian Kasus -->
        <div class="space-y-4">
            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">A. Latar Belakang Masalah</h3>
                <div class="p-3.5 bg-slate-50 rounded-xl whitespace-pre-line leading-relaxed text-slate-800">{{ $caseConference->case_background }}</div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">B. Fakta & Temuan Data</h3>
                <div class="p-3.5 bg-slate-50 rounded-xl whitespace-pre-line leading-relaxed text-slate-800">{{ $caseConference->facts_and_findings }}</div>
            </div>

            @if($caseConference->discussion_dynamics)
                <div>
                    <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">C. Dinamika Musyawarah</h3>
                    <div class="p-3.5 bg-slate-50 rounded-xl whitespace-pre-line leading-relaxed text-slate-800">{{ $caseConference->discussion_dynamics }}</div>
                </div>
            @endif

            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">D. Keputusan Bersama</h3>
                <div class="p-3.5 bg-purple-50/70 border border-purple-100 rounded-xl whitespace-pre-line leading-relaxed font-medium text-slate-900">{{ $caseConference->agreed_decisions }}</div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">E. Komitmen Tindak Lanjut</h3>
                <div class="p-3.5 bg-slate-50 rounded-xl whitespace-pre-line leading-relaxed text-slate-800">{{ $caseConference->follow_up_commitments }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

