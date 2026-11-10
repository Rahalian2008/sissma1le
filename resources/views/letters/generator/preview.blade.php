@extends('layouts.app')

@section('title', 'Pratinjau Dokumen - ' . $template->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.generator.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Pratinjau Hasil Generator Naskah Dinas</h1>
                <p class="text-xs text-slate-500">Template: {{ $template->title }} &bull; Variabel terisi otomatis.</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Paper Size Selector (A4 vs F4) -->
            <div class="inline-flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 shadow-2xs">
                <button type="button" 
                        id="btnPaperF4" 
                        onclick="setGeneratorPaper('f4')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer bg-blue-600 text-white shadow-xs"
                        title="Ukuran F4 / Folio Indonesia (215 x 330 mm)">
                    <span>F4 / Folio</span>
                    <span class="text-[9px] font-mono opacity-80">(330mm)</span>
                </button>
                <button type="button" 
                        id="btnPaperA4" 
                        onclick="setGeneratorPaper('a4')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5 cursor-pointer"
                        title="Ukuran A4 Standar (210 x 297 mm)">
                    <span>A4</span>
                    <span class="text-[9px] font-mono opacity-80">(297mm)</span>
                </button>
            </div>

            @if($template->code === 'ST-GURU' || str_contains(strtolower($template->category ?? ''), 'tugas') || str_contains(strtolower($template->title ?? ''), 'tugas'))
                <a href="{{ route('letters.sppd.create', [
                    'task_number' => $variables['nomor_surat'] ?? null,
                    'employee_id' => $variables['employee_id'] ?? null,
                    'purpose' => $variables['keperluan'] ?? null,
                    'destination' => $variables['tempat_tujuan'] ?? null,
                    'departure_date' => date('Y-m-d'),
                    'return_date' => date('Y-m-d'),
                ]) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Terbitkan SPPD
                </a>
            @endif

            <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Dokumen
            </button>
        </div>
    </div>

    <!-- Helpful notice -->
    <div class="p-3.5 bg-blue-50/70 border border-blue-200 rounded-2xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-blue-900 no-print">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            <span><strong>Mode Pratinjau Interaktif:</strong> Seluruh teks isi naskah dinas di bawah ini dapat diklik dan diedit langsung sebelum mencetak.</span>
        </div>
        <span id="previewPaperBadge" class="text-[11px] font-mono font-bold text-blue-700 bg-blue-100/70 px-2 py-0.5 rounded-lg border border-blue-200 self-start sm:self-auto">
            Kertas: F4 / Folio (215 × 330 mm)
        </span>
    </div>

    <!-- Printable Official Document Card -->
    <div id="printableDocumentSheet" class="bg-white rounded-3xl p-8 sm:p-14 shadow-sm border border-slate-200 printable-document paper-f4 transition-all duration-150">
        <!-- Kop Surat -->
        <x-print-kop :school="$school" />

        <div class="text-center mb-6">
            <h3 class="text-base font-bold uppercase underline text-black m-0">{{ $template->title }}</h3>
            <p class="text-xs font-mono text-black mt-0.5">Nomor: {{ $variables['nomor_surat'] }}</p>
        </div>

        <!-- Generated Parsed Content (Editable) -->
        <div contenteditable="true" spellcheck="false" class="text-xs text-black leading-relaxed font-serif space-y-4 focus:outline-none focus:ring-1 focus:ring-blue-300 focus:rounded-lg p-1 body-content">
            {!! $generatedHtml !!}
        </div>

        <!-- Signature Block -->
        <div class="signature-block mt-12 flex justify-end">
            <div class="text-center w-64 text-xs text-black font-serif">
                <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold mt-1">Kepala Sekolah,</p>
                <div class="h-20"></div>
                <p class="font-bold underline text-sm">{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</p>
                <p class="text-[11px] font-mono">NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '....................................' }}</p>
            </div>
        </div>
    </div>
</div>

<style id="generatorDynamicPaperCss">
    @page { size: 215mm 330mm; margin: 15mm; }
</style>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: #fff !important;
    }
    .printable-document {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
}

/* Optimization for F4 Paper (+33mm height) */
.paper-f4 {
    min-height: 330mm;
}
.paper-f4 .body-content {
    line-height: 1.6;
}
.paper-f4 .signature-block {
    margin-top: 45px;
}

/* Optimization for A4 Paper (Tighter fit to prevent page 2 spill) */
.paper-a4 {
    min-height: 297mm;
}
.paper-a4 .body-content {
    line-height: 1.4;
}
.paper-a4 .signature-block {
    margin-top: 25px;
}
</style>

<script>
let currentGenPaper = localStorage.getItem('sikatas_print_paper_size') || 'f4';

function setGeneratorPaper(size) {
    currentGenPaper = size.toLowerCase();
    localStorage.setItem('sikatas_print_paper_size', currentGenPaper);
    applyGeneratorPaper();
}

function applyGeneratorPaper() {
    const styleEl = document.getElementById('generatorDynamicPaperCss');
    const docEl = document.getElementById('printableDocumentSheet');
    const btnF4 = document.getElementById('btnPaperF4');
    const btnA4 = document.getElementById('btnPaperA4');
    const badge = document.getElementById('previewPaperBadge');

    if (currentGenPaper === 'f4') {
        styleEl.innerHTML = '@page { size: 215mm 330mm; margin: 15mm; }';
        docEl.classList.remove('paper-a4');
        docEl.classList.add('paper-f4');

        btnF4.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer bg-blue-600 text-white shadow-xs';
        btnA4.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5 cursor-pointer';

        if (badge) badge.innerText = 'Kertas: F4 / Folio (215 × 330 mm) - Dioptimalkan';
    } else {
        styleEl.innerHTML = '@page { size: 210mm 297mm; margin: 15mm; }';
        docEl.classList.remove('paper-f4');
        docEl.classList.add('paper-a4');

        btnA4.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer bg-blue-600 text-white shadow-xs';
        btnF4.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5 cursor-pointer';

        if (badge) badge.innerText = 'Kertas: A4 (210 × 297 mm) - Dioptimalkan';
    }
}

document.addEventListener('DOMContentLoaded', applyGeneratorPaper);
</script>
@endsection
