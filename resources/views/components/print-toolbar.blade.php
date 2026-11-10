@props([
    'title' => 'Pratinjau Cetak Dokumen Resmi',
    'orientation' => 'portrait', // 'portrait' or 'landscape'
    'defaultPaper' => 'f4', // 'f4' or 'a4'
    'backUrl' => null,
    'margin' => null,
])

<div class="print-toolbar-container no-print" id="printToolbarContainer">
    <div class="print-toolbar-inner">
        <!-- Title & Document Info -->
        <div class="ptb-left-section">
            <div class="ptb-icon-main">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
            </div>
            <div class="ptb-info-block">
                <div class="ptb-title-row">
                    <span class="ptb-title-text">{{ $title }}</span>
                    <span class="ptb-badge-orientation">
                        {{ $orientation === 'landscape' ? 'Lanskap' : 'Potret' }}
                    </span>
                </div>
                <div class="ptb-meta-row">
                    <span id="activePaperBadge" class="ptb-badge-paper">F4 (215 × 330 mm)</span>
                    <span class="ptb-meta-dot">•</span>
                    <span id="activePaperDesc" class="ptb-meta-desc">Standar Folio Sekolah</span>
                    <span class="ptb-meta-dot">•</span>
                    <span id="activeScaleBadge" class="ptb-badge-scale">Skala: 100%</span>
                </div>
            </div>
        </div>

        <!-- Controls: Paper Selector, Page Scaling (Fit to Page), Print & Actions -->
        <div class="ptb-right-section">
            <!-- 1. Paper Size Selector -->
            <div class="ptb-control-group">
                <button type="button" 
                        id="btnPaperF4" 
                        onclick="setPaperSize('f4')" 
                        class="ptb-btn-paper active" 
                        title="Pilih Kertas F4 / Folio (215 x 330 mm)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>F4</span>
                    <span class="ptb-btn-tag">(330mm)</span>
                </button>
                <button type="button" 
                        id="btnPaperA4" 
                        onclick="setPaperSize('a4')" 
                        class="ptb-btn-paper" 
                        title="Pilih Kertas A4 Standar (210 x 297 mm)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>A4</span>
                    <span class="ptb-btn-tag">(297mm)</span>
                </button>
            </div>

            <!-- 2. Page Scaling / Fit to Printable Area -->
            <div class="ptb-control-group" title="Atur Skala Cetak agar dokumen pas pada lembar kertas (Fit to Page / Printable Area)">
                <button type="button" 
                        id="btnFitToPage" 
                        onclick="fitToPage()" 
                        class="ptb-btn-scale"
                        title="Sesuaikan otomatis agar pas 1 lembar (Fit to Page / Printable Area)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                    <span>Fit to Page</span>
                </button>
                <button type="button" 
                        onclick="zoomOut()" 
                        class="ptb-btn-step" 
                        title="Perkecil Skala (-5%)">
                    −
                </button>
                <button type="button" 
                        id="btnScaleReset" 
                        onclick="resetZoom()" 
                        class="ptb-btn-val" 
                        title="Klik untuk reset ke 100%">
                    <span id="ptbScaleLabel">100%</span>
                </button>
                <button type="button" 
                        onclick="zoomIn()" 
                        class="ptb-btn-step" 
                        title="Perbesar Skala (+5%)">
                    +
                </button>
            </div>

            <!-- 3. Print Button -->
            <button type="button" 
                    onclick="window.print()" 
                    class="ptb-btn-print"
                    title="Cetak Dokumen Sekarang (Ctrl+P)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Dokumen</span>
                <span class="ptb-shortcut">Ctrl+P</span>
            </button>

            <!-- 4. Close / Back Button -->
            @if($backUrl)
                <a href="{{ $backUrl }}" class="ptb-btn-secondary" title="Kembali ke halaman sebelumnya">
                    Kembali
                </a>
            @else
                <button type="button" onclick="window.close()" class="ptb-btn-secondary" title="Tutup pratinjau cetak">
                    Tutup
                </button>
            @endif
        </div>
    </div>
</div>

<style id="sikatas-dynamic-paper-style">
    /* Injected dynamically by JS */
</style>

<style>
/* ==========================================================================
   Base print toolbar styles - 100% self-contained Pure Vanilla CSS
   ========================================================================== */
:root {
    --print-scale: 1;
}

.print-toolbar-container {
    position: sticky;
    top: 0;
    left: 0;
    right: 0;
    z-index: 999999;
    background: #0f172a;
    border-bottom: 1px solid #1e293b;
    padding: 7px 16px;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: #f8fafc;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
    box-sizing: border-box;
    width: 100%;
}

.print-toolbar-container * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.print-toolbar-inner {
    max-width: 1280px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

/* Left Section */
.ptb-left-section {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ptb-icon-main {
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    min-height: 32px !important;
    max-width: 32px !important;
    max-height: 32px !important;
    border-radius: 8px;
    background: #2563eb;
    color: #ffffff;
    display: flex !important;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.4);
}

.ptb-icon-main svg {
    width: 17px !important;
    height: 17px !important;
    max-width: 17px !important;
    max-height: 17px !important;
    display: block !important;
    stroke: #ffffff;
    flex-shrink: 0;
}

.ptb-info-block {
    display: flex;
    flex-direction: column;
    gap: 1.5px;
}

.ptb-title-row {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.ptb-title-text {
    font-size: 12.5px;
    font-weight: 700;
    color: #f8fafc;
    letter-spacing: -0.2px;
    line-height: 1.2;
}

.ptb-badge-orientation {
    font-size: 9px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    text-transform: uppercase;
    padding: 1px 5px;
    border-radius: 4px;
    background: #1e293b;
    color: #94a3b8;
    border: 1px solid #334155;
    font-weight: 600;
    line-height: 1.1;
}

.ptb-meta-row {
    font-size: 10.5px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
    line-height: 1.2;
}

.ptb-badge-paper {
    color: #fbbf24;
    font-weight: 600;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

.ptb-badge-scale {
    color: #38bdf8;
    font-weight: 600;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

.ptb-meta-dot {
    color: #475569;
}

.ptb-meta-desc {
    color: #94a3b8;
    font-size: 10.5px;
}

/* Right Section */
.ptb-right-section {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* Segmented Control Groups */
.ptb-control-group {
    display: inline-flex;
    align-items: center;
    background: #1e293b;
    padding: 2.5px;
    border-radius: 8px;
    border: 1px solid #334155;
    gap: 2px;
}

/* Paper Switcher Buttons */
.ptb-btn-paper {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4.5px 9px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    font-family: inherit;
    border: none;
    cursor: pointer;
    background: transparent;
    color: #94a3b8;
    transition: all 0.15s ease-in-out;
    text-decoration: none;
    line-height: 1;
}

.ptb-btn-paper svg {
    width: 13px !important;
    height: 13px !important;
    max-width: 13px !important;
    max-height: 13px !important;
    display: inline-block !important;
    stroke: currentColor;
    flex-shrink: 0;
}

.ptb-btn-paper:hover {
    color: #f8fafc;
}

.ptb-btn-paper.active {
    background: #2563eb;
    color: #ffffff;
    font-weight: 700;
    box-shadow: 0 2px 5px rgba(37, 99, 235, 0.4);
}

.ptb-btn-paper.active svg {
    stroke: #ffffff;
}

.ptb-btn-tag {
    font-size: 8.5px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    opacity: 0.8;
}

/* Page Scaling / Fit to Page Buttons */
.ptb-btn-scale {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4.5px 9px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    font-family: inherit;
    border: none;
    cursor: pointer;
    background: transparent;
    color: #38bdf8;
    transition: all 0.15s ease-in-out;
    line-height: 1;
}

.ptb-btn-scale svg {
    width: 13px !important;
    height: 13px !important;
    max-width: 13px !important;
    max-height: 13px !important;
    display: inline-block !important;
    stroke: currentColor;
    flex-shrink: 0;
}

.ptb-btn-scale:hover {
    background: #0284c7;
    color: #ffffff;
}

.ptb-btn-step {
    width: 22px;
    height: 22px;
    border-radius: 5px;
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 13px;
    font-weight: bold;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
    line-height: 1;
}

.ptb-btn-step:hover {
    background: #334155;
    color: #ffffff;
}

.ptb-btn-val {
    background: transparent;
    border: none;
    color: #f8fafc;
    font-size: 10.5px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-weight: bold;
    padding: 0 4px;
    cursor: pointer;
}

.ptb-btn-val:hover {
    color: #38bdf8;
}

/* Action Buttons */
.ptb-btn-print {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 13px;
    background: #059669;
    color: #ffffff;
    font-weight: 700;
    font-size: 11.5px;
    font-family: inherit;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(5, 150, 105, 0.35);
    transition: all 0.15s ease-in-out;
    line-height: 1;
}

.ptb-btn-print:hover {
    background: #10b981;
}

.ptb-btn-print svg {
    width: 14px !important;
    height: 14px !important;
    max-width: 14px !important;
    max-height: 14px !important;
    display: inline-block !important;
    stroke: #ffffff;
    flex-shrink: 0;
}

.ptb-shortcut {
    font-size: 9px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    background: rgba(0, 0, 0, 0.25);
    padding: 1.5px 4px;
    border-radius: 3px;
    opacity: 0.9;
}

.ptb-btn-secondary {
    display: inline-flex;
    align-items: center;
    padding: 6px 11px;
    background: #1e293b;
    color: #cbd5e1;
    font-weight: 600;
    font-size: 11.5px;
    font-family: inherit;
    border-radius: 8px;
    border: 1px solid #334155;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease-in-out;
    line-height: 1;
}

.ptb-btn-secondary:hover {
    background: #334155;
    color: #ffffff;
}

@media (max-width: 768px) {
    .ptb-meta-desc {
        display: none;
    }
    .ptb-shortcut {
        display: none;
    }
}

/* Hide toolbar on actual paper/PDF print */
@media print {
    .print-toolbar-container,
    .no-print {
        display: none !important;
    }
}

/* ==========================================================================
   Normalisasi Ukuran Kop Surat Resmi di Semua Dokumen Cetak
   ========================================================================== */
.header-kop-banner {
    text-align: center !important;
    margin-bottom: 12px !important;
    border-bottom: 2.5px double #000 !important;
    padding-bottom: 4px !important;
    width: 100% !important;
}
.header-kop-banner img {
    max-width: 100% !important;
    width: 100% !important;
    height: auto !important;
    max-height: 140px !important;
    min-height: 70px !important;
    object-fit: contain !important;
    display: block !important;
    margin: 0 auto !important;
}
.header-kop table {
    width: 100% !important;
    border: none !important;
    border-collapse: collapse !important;
    margin: 0 !important;
    padding: 0 !important;
}
.header-kop table td {
    vertical-align: middle !important;
    border: none !important;
}
.header-kop img {
    width: 75px !important;
    height: 75px !important;
    max-width: 75px !important;
    max-height: 75px !important;
    object-fit: contain !important;
}
.header-kop h2 {
    font-size: 14pt !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    margin: 2px 0 !important;
    line-height: 1.2 !important;
}
.header-kop p {
    line-height: 1.25 !important;
}

/* ==========================================================================
   Standar Huruf Kapital Resmi untuk Nama Siswa, Guru, Staff & Kepala Sekolah
   ========================================================================== */
.student-name, 
.employee-name, 
.teacher-name, 
.headmaster-name, 
.staff-name, 
.officer-name,
.signer-name,
.signature-table strong, 
.signature-table strong u, 
.sig-block strong, 
.sig-block strong u, 
.sig-name,
td.col-student-name, 
td.col-employee-name, 
table.attendance-table td:nth-child(4),
table.attendance-table td:nth-child(4) strong,
table.data-table td.col-name,
table.data-table td:nth-child(3) strong,
table.data-table td:nth-child(4) strong {
    text-transform: uppercase !important;
}

/* ==========================================================================
   Page Scaling & Fit to Printable Area Rules
   ========================================================================== */
.printable-sheet, 
.page-container, 
.print-container, 
.printable-document {
    zoom: var(--print-scale, 1);
    box-sizing: border-box;
    background: #ffffff;
    margin: 15px auto;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: width 0.2s, min-height 0.2s;
}

/* Portrait & Landscape Paper Preview Dimensions on Screen */
@media screen {
    .paper-f4.portrait-sheet {
        width: 215mm;
        min-height: 330mm;
    }
    .paper-a4.portrait-sheet {
        width: 210mm;
        min-height: 297mm;
    }

    .paper-f4.landscape-sheet {
        width: 330mm;
        min-height: 215mm;
    }
    .paper-a4.landscape-sheet {
        width: 297mm;
        min-height: 210mm;
    }
}

@media print {
    html, body {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .printable-sheet, 
    .page-container, 
    .print-container, 
    .printable-document {
        zoom: var(--print-scale, 1) !important;
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 auto !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
    }
    table {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
}

/* F4 Content Spacing Rules */
body.paper-f4, .paper-f4 {
    --doc-line-height: 1.55;
    --doc-para-spacing: 16px;
    --doc-sig-space: 45px;
    --doc-table-padding: 5px 6px;
}
body.paper-f4 .body-content {
    line-height: 1.55;
    margin-bottom: 20px;
}
body.paper-f4 .signature-table, 
body.paper-f4 .signature-box, 
body.paper-f4 .signature-block {
    margin-top: 40px;
}

/* A4 Content Spacing Rules */
body.paper-a4, .paper-a4 {
    --doc-line-height: 1.35;
    --doc-para-spacing: 12px;
    --doc-sig-space: 25px;
    --doc-table-padding: 3px 5px;
}
body.paper-a4 .body-content {
    line-height: 1.35;
    margin-bottom: 14px;
}
body.paper-a4 .signature-table, 
body.paper-a4 .signature-box, 
body.paper-a4 .signature-block {
    margin-top: 25px;
}
</style>

<script>
(function() {
    const orientation = "{{ $orientation }}"; // 'portrait' or 'landscape'
    const defaultPaper = "{{ $defaultPaper }}";
    const customMargin = @json($margin);

    // 1. Paper Size Management
    const urlParams = new URLSearchParams(window.location.search);
    const urlPaper = urlParams.get('paper');
    const storedPaper = localStorage.getItem('sikatas_print_paper_size');
    let currentPaper = (urlPaper || storedPaper || defaultPaper).toLowerCase();

    if (currentPaper !== 'a4' && currentPaper !== 'f4') {
        currentPaper = 'f4';
    }

    // 2. Scaling (Fit to Printable Area) Management
    const storedScale = parseFloat(localStorage.getItem('sikatas_print_scale') || '1.0');
    let currentScale = (!isNaN(storedScale) && storedScale >= 0.5 && storedScale <= 1.4) ? storedScale : 1.0;

    window.setPaperSize = function(size) {
        currentPaper = size.toLowerCase();
        localStorage.setItem('sikatas_print_paper_size', currentPaper);
        applyPaperStyles();
    };

    window.setPrintScale = function(scale) {
        currentScale = Math.round(scale * 100) / 100;
        if (currentScale < 0.5) currentScale = 0.5;
        if (currentScale > 1.4) currentScale = 1.4;
        localStorage.setItem('sikatas_print_scale', currentScale);
        applyScaleStyles();
    };

    window.zoomIn = function() {
        setPrintScale(currentScale + 0.05);
    };

    window.zoomOut = function() {
        setPrintScale(currentScale - 0.05);
    };

    window.resetZoom = function() {
        setPrintScale(1.0);
    };

    window.fitToPage = function() {
        // Find main document container
        const sheet = document.querySelector('.printable-sheet') || 
                      document.querySelector('.page-container') || 
                      document.querySelector('.printable-document') || 
                      document.querySelector('table.attendance-table') || 
                      document.querySelector('table') || 
                      document.body;

        if (sheet) {
            const contentWidth = sheet.scrollWidth;
            const targetWidth = orientation === 'landscape' ? (currentPaper === 'f4' ? 1200 : 1080) : (currentPaper === 'f4' ? 800 : 750);
            
            let optimalScale = 1.0;
            if (contentWidth > targetWidth && targetWidth > 0) {
                optimalScale = targetWidth / contentWidth;
            } else {
                optimalScale = 0.95; // comfortable standard 95% fit
            }

            optimalScale = Math.max(0.65, Math.min(1.0, Math.floor(optimalScale * 20) / 20));
            setPrintScale(optimalScale);
        } else {
            setPrintScale(0.95);
        }
    };

    function applyPaperStyles() {
        const styleTag = document.getElementById('sikatas-dynamic-paper-style');
        const btnF4 = document.getElementById('btnPaperF4');
        const btnA4 = document.getElementById('btnPaperA4');
        const badge = document.getElementById('activePaperBadge');
        const desc = document.getElementById('activePaperDesc');

        let pageCss = '';
        const f4Margin = customMargin || (orientation === 'landscape' ? '10mm 8mm 10mm 8mm' : '15mm');
        const a4Margin = customMargin || (orientation === 'landscape' ? '10mm 8mm 10mm 8mm' : '15mm');

        if (currentPaper === 'f4') {
            if (orientation === 'landscape') {
                pageCss = `@page { size: 330mm 215mm; margin: ${f4Margin}; }`;
            } else {
                pageCss = `@page { size: 215mm 330mm; margin: ${f4Margin}; }`;
            }

            if (btnF4) btnF4.classList.add('active');
            if (btnA4) btnA4.classList.remove('active');

            if (badge) badge.innerText = 'F4 (215 × 330 mm)';
            if (desc) desc.innerText = 'Folio Standar Sekolah (+33mm)';

            document.body.classList.remove('paper-a4');
            document.body.classList.add('paper-f4');

            document.querySelectorAll('.printable-sheet').forEach(el => {
                el.classList.remove('paper-a4');
                el.classList.add('paper-f4');
            });
        } else {
            if (orientation === 'landscape') {
                pageCss = `@page { size: 297mm 210mm; margin: ${a4Margin}; }`;
            } else {
                pageCss = `@page { size: 210mm 297mm; margin: ${a4Margin}; }`;
            }

            if (btnA4) btnA4.classList.add('active');
            if (btnF4) btnF4.classList.remove('active');

            if (badge) badge.innerText = 'A4 (210 × 297 mm)';
            if (desc) desc.innerText = 'Format A4 Standar Internasional';

            document.body.classList.remove('paper-f4');
            document.body.classList.add('paper-a4');

            document.querySelectorAll('.printable-sheet').forEach(el => {
                el.classList.remove('paper-f4');
                el.classList.add('paper-a4');
            });
        }

        if (styleTag) {
            styleTag.innerHTML = pageCss;
        }
    }

    function applyScaleStyles() {
        const pct = Math.round(currentScale * 100);
        const label = document.getElementById('ptbScaleLabel');
        const scaleBadge = document.getElementById('activeScaleBadge');

        if (label) label.innerText = pct + '%';
        if (scaleBadge) scaleBadge.innerText = 'Skala: ' + pct + '%';

        document.documentElement.style.setProperty('--print-scale', currentScale);

        const targets = document.querySelectorAll('.printable-sheet, .page-container, .print-container, .printable-document');
        if (targets.length > 0) {
            targets.forEach(el => {
                el.style.zoom = currentScale;
            });
        } else {
            const bodyChildren = document.querySelectorAll('body > *:not(.print-toolbar-container):not(script):not(style)');
            bodyChildren.forEach(el => {
                el.style.zoom = currentScale;
            });
        }
    }

    // Run on page load
    function init() {
        applyPaperStyles();
        applyScaleStyles();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
