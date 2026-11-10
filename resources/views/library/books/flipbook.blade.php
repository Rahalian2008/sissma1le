<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-Reader Dokumen: {{ $book->title }} - SMAN 1 Lengkong</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; 
            background-color: #070b14;
            color: #f1f5f9;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* 3D Scene Studio */
        .reader-3d-scene {
            perspective: 2000px;
            perspective-origin: 50% 50%;
            transform-style: preserve-3d;
        }

        /* 3D Floating Single Sheet Card */
        .sheet-3d {
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1), opacity 0.3s ease;
            box-shadow: 
                0 25px 60px -15px rgba(0, 0, 0, 0.85),
                0 10px 25px -10px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(255, 255, 255, 0.15);
            border-radius: 4px;
            background: #ffffff;
        }

        /* Subtle paper edge depth simulation (Ketebalan Kertas 3D) */
        .sheet-3d::after {
            content: '';
            position: absolute;
            bottom: -3px;
            right: -2px;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 96%, rgba(203, 213, 225, 0.6) 98%, rgba(148, 163, 184, 0.8) 100%);
            border-radius: 4px;
            pointer-events: none;
            z-index: -1;
        }

        /* 3D Flip Out Animations */
        .flip-turn-next {
            transform: rotateY(-26deg) scale(0.96) translateZ(40px) translateX(-20px) !important;
            opacity: 0.4 !important;
        }
        .flip-turn-prev {
            transform: rotateY(26deg) scale(0.96) translateZ(40px) translateX(20px) !important;
            opacity: 0.4 !important;
        }

        /* Custom Scrollbar for Reading Area */
        .reader-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .reader-scroll::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        .reader-scroll::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.5);
            border-radius: 6px;
        }
        .reader-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.8);
        }

        /* Anti-Copy and Clean Presentation */
        .no-select {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body id="reader-root" class="h-full w-full overflow-hidden flex flex-col bg-[#070b14] text-slate-100 no-select m-0 p-0" oncontextmenu="return false;">

    <!-- =========================================================
         1. TOP BAR (MODERN, CLEAN, TANPA FITUR DOWNLOAD)
         ========================================================= -->
    <header class="h-16 px-4 sm:px-6 bg-slate-900/95 border-b border-slate-800 flex items-center justify-between gap-4 shrink-0 z-30 backdrop-blur-xl">
        
        <!-- Sisi Kiri: Tombol Kembali & Judul Buku -->
        <div class="flex items-center gap-3.5 min-w-0">
            <a href="{{ route('library.books.show', $book) }}" 
               class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-1.5 shrink-0 border border-slate-700 cursor-pointer shadow-sm">
                <span>←</span>
                <span class="hidden sm:inline">Kembali</span>
            </a>

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-xs sm:text-sm font-extrabold text-white truncate max-w-[200px] sm:max-w-md lg:max-w-xl">
                        {{ $book->title }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase tracking-wider hidden sm:inline-block">
                        3D E-Reader &bull; Flip HTML 3D
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 truncate">
                    Penulis: {{ $book->author }} &bull; {{ $book->category->name ?? 'Koleksi Perpustakaan' }}
                </p>
            </div>
        </div>

        <!-- Sisi Kanan: Kontrol Tampilan, Zoom, Tema & Fullscreen (TANPA TOMBOL DOWNLOAD) -->
        <div class="flex items-center gap-2 shrink-0">
            
            <!-- Mode Ukuran Tampilan (Lebar Jelas vs Pas Layar) -->
            <div class="flex items-center bg-slate-800/90 border border-slate-700 rounded-xl p-0.5 shadow-sm">
                <button type="button" onclick="setViewMode('fit-width')" id="btn-mode-fit-width" title="Mode Lebar Jelas: Huruf Besar & Nyaman Dibaca" 
                        class="px-2.5 h-8 rounded-lg bg-indigo-600 text-white font-bold text-xs transition cursor-pointer flex items-center gap-1.5">
                    <span>↔</span>
                    <span class="hidden md:inline">Lebar Jelas</span>
                </button>
                <button type="button" onclick="setViewMode('fit-page')" id="btn-mode-fit-page" title="Mode Pas Layar: Tampil Utuh 1 Halaman" 
                        class="px-2.5 h-8 rounded-lg hover:bg-slate-700 text-slate-300 font-bold text-xs transition cursor-pointer flex items-center gap-1.5">
                    <span>↕</span>
                    <span class="hidden md:inline">Pas Layar</span>
                </button>
            </div>

            <!-- Zoom Controls -->
            <div class="flex items-center bg-slate-800/90 border border-slate-700 rounded-xl p-0.5 shadow-sm">
                <button type="button" onclick="changeZoom(-0.15)" title="Perkecil (-)" 
                        class="w-8 h-8 rounded-lg hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-sm font-bold transition cursor-pointer">
                    −
                </button>
                <button type="button" onclick="setViewMode('fit-width')" title="Reset ke Lebar Jelas" 
                        class="px-2.5 h-8 rounded-lg hover:bg-slate-700 text-[11px] font-mono font-bold text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                    <span id="zoom-label">Lebar</span>
                </button>
                <button type="button" onclick="changeZoom(0.15)" title="Perbesar (+)" 
                        class="w-8 h-8 rounded-lg hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-sm font-bold transition cursor-pointer">
                    +
                </button>
            </div>

            <!-- Theme Mood Mode (Dark, Sepia, Light) -->
            <div class="flex items-center bg-slate-800/90 border border-slate-700 rounded-xl p-0.5 shadow-sm">
                <button type="button" onclick="setReaderTheme('dark')" title="Mode Gelap" 
                        id="btn-theme-dark" class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs transition cursor-pointer">
                    🌙
                </button>
                <button type="button" onclick="setReaderTheme('sepia')" title="Mode Sepia (Hangat)" 
                        id="btn-theme-sepia" class="w-8 h-8 rounded-lg hover:bg-slate-700 text-amber-200 flex items-center justify-center text-xs transition cursor-pointer">
                    📖
                </button>
                <button type="button" onclick="setReaderTheme('light')" title="Mode Terang" 
                        id="btn-theme-light" class="w-8 h-8 rounded-lg hover:bg-slate-700 text-slate-300 flex items-center justify-center text-xs transition cursor-pointer">
                    ☀️
                </button>
            </div>

            <!-- Fullscreen Toggle -->
            <button type="button" onclick="toggleReaderFullscreen()" title="Layar Penuh Layar Monitor (F11)" 
                    class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-sm transition cursor-pointer shadow-sm">
                <span id="reader-fullscreen-icon">⛶</span>
            </button>
        </div>
    </header>

    <!-- =========================================================
         2. MAIN 3D READING STUDIO (TAMPIL PER LEMBAR + GESER SENTUH)
         ========================================================= -->
    <main id="page-stage" 
          class="reader-3d-scene flex-1 overflow-y-auto overflow-x-hidden flex flex-col items-center p-3 sm:p-6 lg:p-8 relative cursor-grab active:cursor-grabbing reader-scroll">
        
        <!-- Ambient 3D Glow Backlight -->
        <div class="absolute inset-0 bg-radial from-indigo-500/10 via-transparent to-transparent pointer-events-none"></div>

        <!-- Loading Spinner -->
        <div id="reader-loading" class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-slate-950/80 backdrop-blur-md space-y-3">
            <div class="w-12 h-12 rounded-full border-4 border-indigo-500 border-t-transparent animate-spin"></div>
            <div class="text-sm font-extrabold text-white">Menyiapkan Lembar Dokumen 3D...</div>
        </div>

        <!-- Gesture Hint Pill (Otomatis memudar) -->
        <div id="gesture-hint" class="absolute top-4 left-1/2 -translate-x-1/2 z-20 px-4 py-1.5 rounded-full bg-slate-900/90 border border-slate-700 text-slate-300 text-xs font-semibold shadow-xl flex items-center gap-2 pointer-events-none transition-opacity duration-500">
            <span>👈</span>
            <span>Geser layar atau klik panah untuk membuka lembar selanjutnya</span>
            <span>👉</span>
        </div>

        <!-- Tombol Navigasi 3D Mengambang Kiri -->
        <button type="button" onclick="goToPrevPage()" id="btn-floating-prev" aria-label="Halaman Sebelumnya"
                class="fixed left-3 sm:left-6 top-1/2 -translate-y-1/2 z-40 w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-slate-900/85 hover:bg-indigo-600 border border-slate-700 hover:border-indigo-500 text-white flex items-center justify-center text-2xl font-black shadow-2xl backdrop-blur-md transition transform active:scale-90 cursor-pointer disabled:opacity-20 disabled:pointer-events-none">
            ‹
        </button>

        <!-- Tombol Navigasi 3D Mengambang Kanan -->
        <button type="button" onclick="goToNextPage()" id="btn-floating-next" aria-label="Halaman Selanjutnya"
                class="fixed right-3 sm:right-6 top-1/2 -translate-y-1/2 z-40 w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-slate-900/85 hover:bg-indigo-600 border border-slate-700 hover:border-indigo-500 text-white flex items-center justify-center text-2xl font-black shadow-2xl backdrop-blur-md transition transform active:scale-90 cursor-pointer disabled:opacity-20 disabled:pointer-events-none">
            ›
        </button>

        <!-- Container Wadah Lembar 3D Tunggal (Per Lembar) -->
        <div id="sheet-wrapper" class="relative z-10 flex items-center justify-center my-auto transition-transform duration-300">
            
            @if($digitalFileUrl)
                <!-- MODE A: RENDER PDF DENGAN RETINA HD SUPER-SAMPLING KEDALAM LEMBAR 3D -->
                <div id="pdf-sheet" class="sheet-3d overflow-hidden flex items-center justify-center transition-all duration-200">
                    <canvas id="pdf-canvas" class="block pointer-events-none"></canvas>
                </div>

                <!-- Pesan jika berkas rusak atau jaringan terputus (tanpa memunculkan popup download) -->
                <div id="pdf-error-container" class="hidden text-center p-8 bg-slate-900/95 border border-rose-500/30 rounded-3xl max-w-md mx-auto shadow-2xl">
                    <div class="text-4xl mb-3">⚠️</div>
                    <h3 class="text-base font-bold text-white mb-2">Dokumen Digital Gagal Dimuat</h3>
                    <p id="pdf-error-message" class="text-xs text-slate-400 mb-4">Pastikan berkas PDF tersimpan dengan benar dan dapat diakses.</p>
                    <button type="button" onclick="location.reload()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition cursor-pointer">
                        Muat Ulang Halaman
                    </button>
                </div>
            @else
                <!-- MODE B: RENDER HALAMAN DIGITAL HTML PER LEMBAR BERGAYA 3D -->
                <div id="html-card-container" class="max-w-4xl w-full">
                    @foreach($pages as $idx => $p)
                        <div id="html-page-{{ $idx + 1 }}" 
                             class="html-page-sheet sheet-3d {{ $idx === 0 ? 'block' : 'hidden' }} text-slate-900 p-8 sm:p-14 min-h-[560px] sm:min-h-[700px] flex flex-col justify-between">
                            
                            <div>
                                <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
                                    <div class="text-[11px] font-extrabold text-indigo-600 uppercase tracking-wider">
                                        {{ $p['title'] ?? ('Halaman ' . ($idx + 1)) }}
                                    </div>
                                    <div class="text-xs font-mono font-bold text-slate-400">
                                        Lembar {{ $idx + 1 }} dari {{ count($pages) }}
                                    </div>
                                </div>

                                @if(!empty($p['subtitle']))
                                    <h3 class="text-lg font-bold text-slate-800 mb-4">
                                        {{ $p['subtitle'] }}
                                    </h3>
                                @endif

                                <div class="prose max-w-none text-base leading-relaxed text-slate-800">
                                    {!! $p['body_html'] ?? '' !!}
                                </div>
                            </div>

                            <div class="pt-6 mt-8 border-t border-slate-200 flex items-center justify-between text-xs text-slate-400 font-medium">
                                <span>{{ $book->title }}</span>
                                <span class="font-mono">Lembar #{{ $idx + 1 }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

    </main>

    <!-- =========================================================
         3. BOTTOM FLOATING BAR (NAVIGASI & INDIKATOR LEMBARAN)
         ========================================================= -->
    <footer class="h-16 px-4 sm:px-6 bg-slate-900/95 border-t border-slate-800 flex items-center justify-between gap-4 shrink-0 z-30 backdrop-blur-xl">
        
        <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400">
            <span class="text-indigo-400">✦</span>
            <span>Geser layar atau tekan tombol panah untuk membalik lembar</span>
        </div>

        <!-- Tombol Balik Lembar & Lompat Halaman -->
        <div class="flex items-center justify-center gap-3 mx-auto sm:mx-0">
            <button type="button" onclick="goToPrevPage()" id="btn-bottom-prev"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white font-bold text-xs transition flex items-center gap-1.5 border border-slate-700 cursor-pointer disabled:opacity-30 disabled:pointer-events-none shadow-sm">
                <span>◀</span>
                <span class="hidden sm:inline">Sebelumnya</span>
            </button>

            <!-- Input Jump to Sheet -->
            <div class="flex items-center gap-1.5 bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-1.5 text-xs shadow-inner">
                <span class="text-slate-400 font-semibold">Lembar</span>
                <input type="number" id="input-page-num" min="1" max="1" value="1" onchange="jumpToPage(this.value)" 
                       class="w-12 text-center bg-slate-800 border border-slate-700 rounded-lg text-white font-mono font-bold py-0.5 outline-none focus:border-indigo-500">
                <span class="text-slate-400 font-semibold">dari <strong id="total-pages-label" class="text-white font-mono font-bold">1</strong></span>
            </div>

            <button type="button" onclick="goToNextPage()" id="btn-bottom-next"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white font-bold text-xs transition flex items-center gap-1.5 border border-slate-700 cursor-pointer disabled:opacity-30 disabled:pointer-events-none shadow-sm">
                <span class="hidden sm:inline">Selanjutnya</span>
                <span>▶</span>
            </button>
        </div>

        <div class="hidden md:flex items-center gap-2 text-xs text-slate-400">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Dokumen Terbuka Sempurna</span>
        </div>
    </footer>

    <!-- LOAD LOCAL PDF.JS SCRIPTS (BEBAS CORS & BEBAS ERROR WORKER) -->
    @if($digitalFileUrl)
    <script src="{{ asset('js/pdf/pdf.min.js') }}"></script>
    <script>
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('js/pdf/pdf.worker.min.js') }}";
        }
    </script>
    @endif

    <script>
        const isPdfMode = @json(!empty($digitalFileUrl));
        const pdfStreamUrl = @json($digitalFileUrl);
        const totalHtmlPages = @json(is_array($pages) ? count($pages) : 1);

        let pdfDoc = null;
        let currentPageNum = 1;
        let totalPagesCount = isPdfMode ? 1 : totalHtmlPages;
        let isPageRendering = false;
        let pendingPageNum = null;
        let viewMode = 'fit-width'; // 'fit-width' (Lebar Jelas) | 'fit-page' (Pas Layar) | 'custom'
        let currentZoomScale = 1.35;

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const hint = document.getElementById('gesture-hint');
                if (hint) {
                    hint.classList.add('opacity-0');
                    setTimeout(() => hint.remove(), 500);
                }
            }, 5000);

            if (isPdfMode && pdfStreamUrl) {
                initPdfViewer(pdfStreamUrl);
            } else {
                initHtmlViewer();
            }

            setupGestures();
            setupKeyboard();
        });

        // 1. INISIALISASI PDF.JS DENGAN BUFFER MEMORI (KEBAL INTERSEP IDM / DOWNLOAD MANAGER)
        async function initPdfViewer(url) {
            const loadingEl = document.getElementById('reader-loading');
            if (loadingEl) loadingEl.classList.remove('hidden');

            let pdfData = null;

            try {
                // Tahap 1: Ambil stream biner langsung via fetch() dengan header khusus agar tidak dicegat IDM
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/octet-stream'
                    }
                });

                if (!response.ok) {
                    throw new Error('HTTP status ' + response.status);
                }

                const buffer = await response.arrayBuffer();
                pdfData = new Uint8Array(buffer);
            } catch (fetchErr) {
                console.warn('Fetch stream binary dicegat atau gagal, mencoba jalur fallback JSON Base64:', fetchErr);
                try {
                    // Tahap 2: Fallback ke Base64 JSON jika ada ekstensi download manager browser yang memblokir binary stream
                    const fallbackUrl = url + (url.includes('?') ? '&' : '?') + 'format=base64';
                    const jsonRes = await fetch(fallbackUrl, {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' }
                    });
                    const jsonData = await jsonRes.json();
                    if (jsonData && jsonData.data) {
                        const binaryString = atob(jsonData.data);
                        const len = binaryString.length;
                        const bytes = new Uint8Array(len);
                        for (let i = 0; i < len; i++) {
                            bytes[i] = binaryString.charCodeAt(i);
                        }
                        pdfData = bytes;
                    } else {
                        throw new Error('Data dokumen kosong');
                    }
                } catch (jsonErr) {
                    console.error('Semua metode pemuatan PDF gagal:', jsonErr);
                    if (loadingEl) loadingEl.classList.add('hidden');
                    showPdfError('Tidak dapat memuat berkas dokumen PDF.');
                    return;
                }
            }

            try {
                // Muat dokumen PDF langsung dari memory buffer
                const loadingTask = pdfjsLib.getDocument({
                    data: pdfData,
                    cMapUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/cmaps/',
                    cMapPacked: true,
                });

                pdfDoc = await loadingTask.promise;
                totalPagesCount = pdfDoc.numPages;
                document.getElementById('total-pages-label').textContent = totalPagesCount;
                document.getElementById('input-page-num').max = totalPagesCount;

                if (loadingEl) loadingEl.classList.add('hidden');
                renderPage(currentPageNum);
            } catch (pdfErr) {
                console.error('PDF.js engine error:', pdfErr);
                if (loadingEl) loadingEl.classList.add('hidden');
                showPdfError('Format dokumen tidak dapat dibaca oleh PDF engine.');
            }
        }

        function showPdfError(msg) {
            const errorContainer = document.getElementById('pdf-error-container');
            const errorMsg = document.getElementById('pdf-error-message');
            const pdfSheet = document.getElementById('pdf-sheet');
            if (pdfSheet) pdfSheet.classList.add('hidden');
            if (errorContainer) {
                if (errorMsg) errorMsg.textContent = msg;
                errorContainer.classList.remove('hidden');
            }
        }

        function initHtmlViewer() {
            const loadingEl = document.getElementById('reader-loading');
            if (loadingEl) loadingEl.classList.add('hidden');
            document.getElementById('total-pages-label').textContent = totalPagesCount;
            document.getElementById('input-page-num').max = totalPagesCount;
            updateNavButtons();
        }

        // 2. RENDER LEMBAR 3D TUNGGAL
        function renderPage(num, direction = null) {
            if (num < 1) num = 1;
            if (num > totalPagesCount) num = totalPagesCount;
            currentPageNum = num;

            const sheetWrapper = document.getElementById('sheet-wrapper');
            const stage = document.getElementById('page-stage');

            // Kembalikan posisi scroll ke atas dokumen setiap membalik lembaran
            if (stage) stage.scrollTop = 0;

            // Berikan efek 3D flip saat membalik lembaran
            if (direction === 'next') {
                sheetWrapper.classList.add('flip-turn-next');
            } else if (direction === 'prev') {
                sheetWrapper.classList.add('flip-turn-prev');
            }

            setTimeout(() => {
                sheetWrapper.classList.remove('flip-turn-next', 'flip-turn-prev');
            }, 150);

            if (isPdfMode && pdfDoc) {
                renderPdfCanvas(num);
            } else {
                renderHtmlCard(num);
            }

            updateNavButtons();
        }

        function renderPdfCanvas(num) {
            if (!pdfDoc) return;
            if (isPageRendering) {
                pendingPageNum = num;
                return;
            }
            isPageRendering = true;

            pdfDoc.getPage(num).then(page => {
                const canvas = document.getElementById('pdf-canvas');
                if (!canvas) {
                    isPageRendering = false;
                    return;
                }
                const ctx = canvas.getContext('2d');

                const stage = document.getElementById('page-stage');
                const stageW = stage ? stage.clientWidth : window.innerWidth;
                const stageH = stage ? stage.clientHeight : window.innerHeight;

                const unscaledViewport = page.getViewport({ scale: 1 });
                
                let scaleToUse = 1.0;
                if (viewMode === 'fit-width') {
                    // Mode Lebar Jelas: Huruf besar, tajam & nyaman dibaca seperti membaca buku asli
                    if (stageW < 768) {
                        const availW = Math.max(280, stageW - 20);
                        scaleToUse = availW / unscaledViewport.width;
                    } else {
                        // Di desktop/laptop, lebar dokumen ideal adalah 860px - 1020px
                        const targetWidth = Math.min(Math.max(860, stageW * 0.70), stageW - 140);
                        scaleToUse = targetWidth / unscaledViewport.width;
                    }
                    if (scaleToUse < 0.8) scaleToUse = 0.8;
                    if (scaleToUse > 3.0) scaleToUse = 3.0;
                } else if (viewMode === 'fit-page') {
                    // Mode Pas Layar: Menampilkan 1 halaman utuh dari atas sampai bawah pas di layar
                    const availW = Math.max(280, stageW - (stageW < 640 ? 24 : 100));
                    const availH = Math.max(380, stageH - (stageW < 640 ? 30 : 60));
                    const widthScale = availW / unscaledViewport.width;
                    const heightScale = availH / unscaledViewport.height;
                    scaleToUse = Math.min(widthScale, heightScale);
                    if (scaleToUse < 0.4) scaleToUse = 0.4;
                    if (scaleToUse > 2.2) scaleToUse = 2.2;
                } else {
                    scaleToUse = currentZoomScale;
                }

                // Super-Sampling Retina HD: Render bitmap kanvas minimal 2x resolusi asli agar teks tajam tanpa buram
                const baseDpr = window.devicePixelRatio || 1;
                const renderDpr = Math.max(2, baseDpr);

                const viewport = page.getViewport({ scale: scaleToUse * renderDpr });

                // Resolusi fisik kanvas (HD Retina)
                canvas.width = Math.round(viewport.width);
                canvas.height = Math.round(viewport.height);

                // Ukuran tampilan CSS kanvas
                const cssW = Math.round(viewport.width / renderDpr);
                const cssH = Math.round(viewport.height / renderDpr);
                canvas.style.width = `${cssW}px`;
                canvas.style.height = `${cssH}px`;

                // Samakan ukuran kartu kertas 3D agar presisi membungkus dokumen
                const pdfSheet = document.getElementById('pdf-sheet');
                if (pdfSheet) {
                    pdfSheet.style.width = `${cssW}px`;
                    pdfSheet.style.height = `${cssH}px`;
                }

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);
                renderTask.promise.then(() => {
                    isPageRendering = false;
                    if (pendingPageNum !== null) {
                        renderPdfCanvas(pendingPageNum);
                        pendingPageNum = null;
                    }
                }).catch(renderErr => {
                    console.warn('Canvas render interrupted:', renderErr);
                    isPageRendering = false;
                });
            }).catch(pageErr => {
                console.error('Failed to get page ' + num, pageErr);
                isPageRendering = false;
            });
        }

        function renderHtmlCard(num) {
            document.querySelectorAll('.html-page-sheet').forEach((sheet, idx) => {
                if (idx + 1 === num) {
                    sheet.classList.remove('hidden');
                    sheet.classList.add('block');
                } else {
                    sheet.classList.remove('block');
                    sheet.classList.add('hidden');
                }
            });
        }

        function updateNavButtons() {
            document.getElementById('input-page-num').value = currentPageNum;

            const isFirst = currentPageNum <= 1;
            const isLast = currentPageNum >= totalPagesCount;

            document.getElementById('btn-floating-prev').disabled = isFirst;
            document.getElementById('btn-bottom-prev').disabled = isFirst;

            document.getElementById('btn-floating-next').disabled = isLast;
            document.getElementById('btn-bottom-next').disabled = isLast;
        }

        // 3. NAVIGASI LEMBAR (SELANJUTNYA / SEBELUMNYA)
        function goToNextPage() {
            if (currentPageNum < totalPagesCount) {
                renderPage(currentPageNum + 1, 'next');
            }
        }

        function goToPrevPage() {
            if (currentPageNum > 1) {
                renderPage(currentPageNum - 1, 'prev');
            }
        }

        function jumpToPage(num) {
            let target = parseInt(num, 10);
            if (isNaN(target)) target = 1;
            if (target < 1) target = 1;
            if (target > totalPagesCount) target = totalPagesCount;
            renderPage(target);
        }

        // 4. GESTUR SENTUH & GESER (TOUCH SWIPE & MOUSE DRAG)
        function setupGestures() {
            const stage = document.getElementById('page-stage');

            let touchStartX = 0;
            let touchStartY = 0;

            stage.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
                touchStartY = e.changedTouches[0].screenY;
            }, { passive: true });

            stage.addEventListener('touchend', e => {
                const endX = e.changedTouches[0].screenX;
                const endY = e.changedTouches[0].screenY;
                const diffX = endX - touchStartX;
                const diffY = endY - touchStartY;

                // Pastikan gerakan geser horizontal lebih dominan
                if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 40) {
                    if (diffX < 0) {
                        goToNextPage(); // Geser ke kiri -> Halaman Selanjutnya
                    } else {
                        goToPrevPage(); // Geser ke kanan -> Halaman Sebelumnya
                    }
                }
            }, { passive: true });

            // Mouse Drag Support
            let isMouseDown = false;
            let mouseStartX = 0;

            stage.addEventListener('mousedown', e => {
                if (e.target.closest('button, input, select, a')) return;
                isMouseDown = true;
                mouseStartX = e.clientX;
            });

            window.addEventListener('mouseup', e => {
                if (!isMouseDown) return;
                isMouseDown = false;
                const diffX = e.clientX - mouseStartX;
                if (diffX < -55) {
                    goToNextPage();
                } else if (diffX > 55) {
                    goToPrevPage();
                }
            });
        }

        // 5. KEYBOARD SHORTCUTS
        function setupKeyboard() {
            window.addEventListener('keydown', e => {
                if (e.target.tagName === 'INPUT') return;

                if (e.key === 'ArrowRight' || e.key === 'PageDown' || e.key === ' ') {
                    e.preventDefault();
                    goToNextPage();
                } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                    e.preventDefault();
                    goToPrevPage();
                } else if (e.key === '+' || e.key === '=') {
                    changeZoom(0.15);
                } else if (e.key === '-') {
                    changeZoom(-0.15);
                } else if (e.key.toLowerCase() === 'f') {
                    toggleReaderFullscreen();
                } else if (e.key === 'Escape' && !document.fullscreenElement) {
                    window.location.href = "{{ route('library.books.show', $book) }}";
                }
            });
        }

        // 6. KONTROL TAMPILAN UKURAN & ZOOM
        function setViewMode(mode) {
            viewMode = mode;
            if (mode === 'fit-width') {
                document.getElementById('zoom-label').textContent = 'Lebar';
            } else if (mode === 'fit-page') {
                document.getElementById('zoom-label').textContent = 'Pas';
            }
            updateViewModeButtons();
            if (isPdfMode) renderPdfCanvas(currentPageNum);
        }

        function updateViewModeButtons() {
            const btnFitWidth = document.getElementById('btn-mode-fit-width');
            const btnFitPage = document.getElementById('btn-mode-fit-page');
            if (btnFitWidth && btnFitPage) {
                const activeCls = 'bg-indigo-600 text-white font-bold shadow-xs';
                const inactiveCls = 'hover:bg-slate-700 text-slate-300';
                btnFitWidth.className = `px-2.5 h-8 rounded-lg text-xs font-bold transition cursor-pointer flex items-center gap-1.5 ${viewMode === 'fit-width' ? activeCls : inactiveCls}`;
                btnFitPage.className = `px-2.5 h-8 rounded-lg text-xs font-bold transition cursor-pointer flex items-center gap-1.5 ${viewMode === 'fit-page' ? activeCls : inactiveCls}`;
            }
        }

        function changeZoom(delta) {
            viewMode = 'custom';
            currentZoomScale = Math.max(0.6, Math.min(3.5, (currentZoomScale || 1.35) + delta));
            document.getElementById('zoom-label').textContent = Math.round(currentZoomScale * 100) + '%';
            updateViewModeButtons();
            if (isPdfMode) renderPdfCanvas(currentPageNum);
        }

        // Tangani perubahan ukuran jendela (Resize Responsif)
        let resizeTimer = null;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (isPdfMode && pdfDoc) {
                    renderPdfCanvas(currentPageNum);
                }
            }, 150);
        });

        // 7. THEME MOOD MODES
        function setReaderTheme(mode) {
            const root = document.getElementById('reader-root');
            const btnDark = document.getElementById('btn-theme-dark');
            const btnSepia = document.getElementById('btn-theme-sepia');
            const btnLight = document.getElementById('btn-theme-light');

            const activeClass = 'bg-indigo-600 text-white font-bold';
            const inactiveClass = 'hover:bg-slate-700 text-slate-300';

            btnDark.className = `w-8 h-8 rounded-lg flex items-center justify-center text-xs transition cursor-pointer ${mode === 'dark' ? activeClass : inactiveClass}`;
            btnSepia.className = `w-8 h-8 rounded-lg flex items-center justify-center text-xs transition cursor-pointer ${mode === 'sepia' ? activeClass : inactiveClass}`;
            btnLight.className = `w-8 h-8 rounded-lg flex items-center justify-center text-xs transition cursor-pointer ${mode === 'light' ? activeClass : inactiveClass}`;

            if (mode === 'sepia') {
                root.style.backgroundColor = '#2c221b';
            } else if (mode === 'light') {
                root.style.backgroundColor = '#e2e8f0';
            } else {
                root.style.backgroundColor = '#070b14';
            }
        }

        // 8. FULLSCREEN TOGGLE
        function toggleReaderFullscreen() {
            const root = document.getElementById('reader-root');
            const icon = document.getElementById('reader-fullscreen-icon');

            if (!document.fullscreenElement) {
                if (root.requestFullscreen) {
                    root.requestFullscreen().catch(() => {});
                } else if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen().catch(() => {});
                }
                if (icon) icon.textContent = '✕';
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                }
                if (icon) icon.textContent = '⛶';
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const icon = document.getElementById('reader-fullscreen-icon');
            if (icon) {
                icon.textContent = document.fullscreenElement ? '✕' : '⛶';
            }
            if (isPdfMode && pdfDoc) {
                setTimeout(() => renderPdfCanvas(currentPageNum), 100);
            }
        });
    </script>
</body>
</html>
