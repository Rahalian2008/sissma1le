@extends('layouts.app')

@section('title', 'Detail Arsip Digital - ' . $archive->archive_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('archives.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Kartu Kendali Arsip Digital</h1>
                <p class="text-xs text-slate-500 font-mono">{{ $archive->archive_number }}</p>
            </div>
        </div>
    </div>

    <!-- Archive Card -->
    <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-sm border border-slate-200 space-y-6 text-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-100 pb-6">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                    {{ $archive->category }}
                </span>
                <h2 class="text-lg font-bold text-slate-900 mt-2">{{ $archive->title }}</h2>
                <p class="text-slate-500 font-mono mt-1">Nomor Registrasi: {{ $archive->archive_number }}</p>
            </div>
            <div class="text-center shrink-0">
                <div class="p-2 border border-slate-200 rounded-2xl inline-block bg-slate-50">
                    {!! $qrCodeSvg !!}
                </div>
                <div class="text-[9px] text-slate-400 font-mono mt-1">SCAN UNTUK VERIFIKASI</div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Tahun Dokumen:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $archive->year }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Unit Pengolah Asal:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $archive->origin_unit ?? 'Tata Usaha' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Lokasi Fisik Berkas:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $archive->location_rack ?? '-' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl space-y-1">
                <span class="text-slate-400 block text-[11px]">Masa Retensi & Status:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $archive->retention_period_years }} Tahun ({{ $archive->archive_status }})</span>
                <div class="text-[10px] text-slate-500">Berakhir: {{ $archive->retention_expire_date?->format('d M Y') ?? '-' }}</div>
            </div>
        </div>

        @if($archive->notes)
            <div class="p-4 bg-slate-50 rounded-xl">
                <span class="text-slate-400 block text-[11px] mb-1">Catatan Dokumen:</span>
                <p class="text-slate-700 leading-relaxed">{{ $archive->notes }}</p>
            </div>
        @endif

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-slate-500 text-[11px]">Tautan verifikasi publik resmi:</span>
            <a href="{{ $verificationUrl }}" target="_blank" class="text-blue-600 hover:underline font-mono text-[11px]">
                {{ $verificationUrl }}
            </a>
        </div>
    </div>
</div>
@endsection
