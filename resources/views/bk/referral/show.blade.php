@extends('layouts.app')

@section('title', 'Surat Alih Tangan Kasus - ' . $referral->referral_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('bk.referral.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Kembali ke Daftar Rujukan
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.referral.edit', $referral) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                Edit / Tambah Catatan Ahli
            </a>
            <a href="{{ route('bk.referral.print', $referral) }}" target="_blank" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Surat Pengantar Resmi
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-md space-y-6 text-xs text-slate-700">
        <div class="text-center border-b border-slate-200 pb-4">
            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-800">
                SURAT PENGANTAR ALIH TANGAN KASUS (REFERRAL LETTER)
            </span>
            <h1 class="text-lg font-bold text-slate-900 mt-2">Rujukan Konseling & Penanganan Ahli</h1>
            <p class="text-xs text-slate-500">Nomor Registrasi: <span class="font-mono font-bold">{{ $referral->referral_code }}</span> | Tanggal: {{ $referral->referral_date?->translatedFormat('d F Y') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-rose-50/50 border border-rose-100">
            <div>
                <p><strong class="text-slate-800">Nama Siswa:</strong> {{ $referral->student?->name }}</p>
                <p class="mt-1"><strong class="text-slate-800">Kelas / NISN:</strong> {{ $referral->student?->schoolClass?->name }} / {{ $referral->student?->nisn ?? '-' }}</p>
                <p class="mt-1"><strong class="text-slate-800">Guru BK Pengantar:</strong> {{ $referral->counselor?->name ?? 'Guru BK' }}</p>
            </div>
            <div>
                <p><strong class="text-slate-800">Tenaga Ahli Tujuan:</strong> {{ $referral->target_expert_name }}</p>
                <p class="mt-1"><strong class="text-slate-800">Lembaga Tujuan:</strong> {{ $referral->target_institution }}</p>
                <p class="mt-1"><strong class="text-slate-800">Status Rujukan:</strong> <span class="font-bold text-rose-700">{{ str_replace('_', ' ', $referral->status) }}</span></p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">A. Deskripsi Permasalahan / Keluhan Siswa</h3>
                <div class="p-3.5 bg-slate-50 rounded-xl whitespace-pre-line leading-relaxed text-slate-800">{{ $referral->problem_description }}</div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">B. Langkah Bimbingan yang Telah Ditempuh Sekolah</h3>
                <div class="p-3.5 bg-slate-50 rounded-xl whitespace-pre-line leading-relaxed text-slate-800">{{ $referral->previous_interventions }}</div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">C. Alasan Alih Tangan Kasus (Indikasi Rujukan)</h3>
                <div class="p-3.5 bg-slate-50 rounded-xl whitespace-pre-line leading-relaxed text-slate-800">{{ $referral->referral_reason }}</div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] mb-1">D. Harapan Bantuan / Tindak Lanjut dari Tenaga Ahli</h3>
                <div class="p-3.5 bg-rose-50/60 border border-rose-100 rounded-xl whitespace-pre-line leading-relaxed text-slate-800">{{ $referral->expected_support }}</div>
            </div>

            @if($referral->expert_feedback_notes)
                <div>
                    <h3 class="font-bold text-emerald-800 uppercase text-[11px] mb-1">E. Laporan Balik / Feedback dari Tenaga Ahli</h3>
                    <div class="p-3.5 bg-emerald-50 border border-emerald-100 rounded-xl whitespace-pre-line leading-relaxed text-emerald-900">{{ $referral->expert_feedback_notes }}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

