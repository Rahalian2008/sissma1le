@extends('layouts.app')

@section('title', 'Hasil Analisis AI - SIKATAS SMA1LE')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('ai.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Dasbor AI
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs text-slate-500 font-mono">#AI-{{ str_pad($report->id ?? 1, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span class="p-1.5 rounded-xl bg-linear-to-tr from-indigo-600 to-purple-600 text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
                Kajian Analisis Eksekutif SIKATAS AI
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Modul: <span class="font-bold text-indigo-700">{{ ucwords(str_replace('_', ' ', $report->module)) }}</span> • 
                Periode: <span class="font-semibold text-slate-800">{{ $report->period }}</span> • 
                Dihasilkan pada: <span class="text-slate-700">{{ is_string($report->created_at) ? $report->created_at : $report->created_at?->format('d/m/Y H:i') }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if(isset($report->id))
                <form action="{{ route('ai.archive', $report->id) }}" method="POST" onsubmit="return confirm('Simpan dokumen kajian AI ini ke Kearsipan Digital Sekolah?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan ke Arsip Digital
                    </button>
                </form>

                <a href="{{ route('ai.print', $report->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Dokumen Resmi
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Verification Banner -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-4 text-xs text-indigo-900 flex items-start gap-3">
        <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        <div>
            <h2 class="font-bold text-slate-900">Validasi Data Berbasis Ground-Truth</h2>
            <p class="text-slate-600 mt-0.5">Analisis ini dihasilkan dari kalkulasi real-time sistem database operasional SMA1LE (Buku Induk Siswa, DUK Kepegawaian, BKU Keuangan BOSP, Mutasi Sarpras & Gudang) tanpa manipulasi atau halusinasi kecerdasan buatan.</p>
        </div>
    </div>

    <!-- 7 Sections Container -->
    <div class="space-y-5">
        <!-- 1. Ringkasan Eksekutif -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200">
            <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">1</span>
                <h2 class="text-sm font-bold text-slate-900 tracking-tight uppercase">Ringkasan Eksekutif (Executive Summary)</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-normal">
                {{ $report->executive_summary }}
            </p>
        </div>

        <!-- 2. Temuan Kunci -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200">
            <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">2</span>
                <h2 class="text-sm font-bold text-slate-900 tracking-tight uppercase">Temuan Kunci (Key Findings)</h2>
            </div>
            <div class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $report->key_findings }}
            </div>
        </div>

        <!-- 3. Kesimpulan -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200">
            <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center font-bold text-xs">3</span>
                <h2 class="text-sm font-bold text-slate-900 tracking-tight uppercase">Kesimpulan Manajerial (Conclusions)</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                {{ $report->conclusions }}
            </p>
        </div>

        <!-- 4. Permasalahan & Titik Kritis -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200">
            <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs">4</span>
                <h2 class="text-sm font-bold text-slate-900 tracking-tight uppercase">Identifikasi Permasalahan & Titik Kritis (Issues Identified)</h2>
            </div>
            <div class="text-xs sm:text-sm text-rose-900 bg-rose-50/60 p-4 rounded-2xl border border-rose-100 whitespace-pre-line leading-relaxed">
                {{ $report->issues_identified }}
            </div>
        </div>

        <!-- 5. Rekomendasi Solutif -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200">
            <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">5</span>
                <h2 class="text-sm font-bold text-slate-900 tracking-tight uppercase">Rekomendasi Solutif (Strategic Recommendations)</h2>
            </div>
            <div class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $report->recommendations }}
            </div>
        </div>

        <!-- 6. Rencana Tindak Lanjut -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200">
            <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs">6</span>
                <h2 class="text-sm font-bold text-slate-900 tracking-tight uppercase">Rencana Tindak Lanjut (Action Plan)</h2>
            </div>
            <div class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $report->action_plan }}
            </div>
        </div>

        <!-- 7. Data Metrik Pendukung -->
        @php
            $supporting = is_array($report->supporting_data) ? $report->supporting_data : (is_string($report->supporting_data) ? json_decode($report->supporting_data, true) : []);
        @endphp
        @if(!empty($supporting))
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200">
            <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">7</span>
                <h2 class="text-sm font-bold text-slate-900 tracking-tight uppercase">Data Metrik Pendukung (Factual Ground-Truth Metrics)</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($supporting as $metricKey => $metricValue)
                    <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ str_replace('_', ' ', $metricKey) }}</div>
                        <div class="text-base sm:text-lg font-black text-slate-800 mt-1">
                            @if(is_numeric($metricValue))
                                @if(str_contains($metricKey, 'amount') || str_contains($metricKey, 'income') || str_contains($metricKey, 'expense') || str_contains($metricKey, 'balance'))
                                    Rp {{ number_format((float)$metricValue, 0, ',', '.') }}
                                @elseif(str_contains($metricKey, 'pct'))
                                    {{ $metricValue }}%
                                @else
                                    {{ number_format((float)$metricValue, 0, ',', '.') }}
                                @endif
                            @else
                                {{ $metricValue }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
