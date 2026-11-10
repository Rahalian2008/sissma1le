<!-- Modal Panduan Nomor Induk & Call Number Perpustakaan (Standar Perpusnas & DDC) -->
<div id="modalPanduanKatalogisasi" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-3xl w-full overflow-hidden border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-200 flex flex-col max-h-[92vh]">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-5 sm:p-6 text-white flex items-center justify-between shrink-0 border-b border-indigo-900/50">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-indigo-400 text-2xl shadow-inner">
                    📘
                </div>
                <div>
                    <h3 class="font-extrabold text-base sm:text-lg text-white">Pedoman Nomor Induk & Nomor Panggil (Call Number)</h3>
                    <p class="text-xs text-indigo-200/80">Sesuai Standar Nasional Perpustakaan (Perpusnas RI & Dewey Decimal Classification / DDC)</p>
                </div>
            </div>
            <button type="button" onclick="closePanduanKatalogisasiModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Tab Buttons -->
        <div class="flex items-center border-b border-slate-200 dark:border-slate-800 px-6 pt-3 bg-slate-50/80 dark:bg-slate-800/40 shrink-0 gap-2">
            <button type="button" onclick="switchGuideTab('call-number')" id="tab-btn-call-number" class="px-4 py-2 text-xs font-bold border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 cursor-pointer transition">
                🏷️ Nomor Panggil (Call Number)
            </button>
            <button type="button" onclick="switchGuideTab('nomor-induk')" id="tab-btn-nomor-induk" class="px-4 py-2 text-xs font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 cursor-pointer transition">
                📟 Nomor Induk & Barcode
            </button>
            <button type="button" onclick="switchGuideTab('simulator')" id="tab-btn-simulator" class="px-4 py-2 text-xs font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 cursor-pointer transition">
                ⚡ Simulator Generator
            </button>
        </div>

        <!-- Modal Body Content (Scrollable) -->
        <div class="p-6 overflow-y-auto space-y-5 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
            
            <!-- TAB 1: NOMOR PANGGIL (CALL NUMBER) -->
            <div id="guide-tab-call-number" class="guide-tab-pane space-y-4">
                <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/60 dark:border-indigo-800/60">
                    <h4 class="font-bold text-sm text-indigo-900 dark:text-indigo-300 mb-1">Apa itu Nomor Panggil (Call Number)?</h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300">
                        Nomor Panggil adalah identitas kode alamat penyimpanan buku di rak perpustakaan. Sesuai aturan baku Perpusnas RI dan Standar Dewey Decimal Classification (DDC), nomor panggil dicetak pada stiker label berukuran <strong>3.5 cm × 4.5 cm</strong> dan ditempel pada <strong>punggung buku (spine)</strong> dengan jarak <strong>2 - 3 cm</strong> dari tepi bawah punggung buku.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                    <!-- Penjelasan 4 Baris -->
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-800 pb-1">
                            Struktur Baku 3 s.d. 4 Baris Label Punggung:
                        </h5>

                        <div class="space-y-2.5">
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <span class="font-bold text-indigo-600 dark:text-indigo-400 block text-[11px]">Baris 1: Kode Klasifikasi DDC (3 Digit Angka)</span>
                                <span class="text-slate-600 dark:text-slate-300 text-[11px]">
                                    Menentukan disiplin ilmu buku (contoh: <strong>530</strong> untuk Fisika, <strong>813</strong> untuk Fiksi Indonesia, <strong>297</strong> untuk Agama Islam).
                                </span>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <span class="font-bold text-indigo-600 dark:text-indigo-400 block text-[11px]">Baris 2: Tiga (3) Huruf Kapital Pengarang (Cutter Code)</span>
                                <span class="text-slate-600 dark:text-slate-300 text-[11px]">
                                    Diambil dari 3 huruf pertama nama tajuk utama pengarang:
                                    <ul class="list-disc list-inside mt-1 space-y-0.5 text-[10.5px]">
                                        <li>Nama tunggal: <em>Sutrisno</em> &rarr; <strong>SUT</strong></li>
                                        <li>Nama majemuk / nama keluarga: <em>Pramoedya Ananta Toer</em> &rarr; <strong>TOE</strong></li>
                                        <li>Pengarang barat: <em>Albert Einstein</em> &rarr; <strong>EIN</strong></li>
                                        <li>Anonim / tanpa pengarang: diambil dari 3 huruf pertama judul buku.</li>
                                    </ul>
                                </span>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <span class="font-bold text-indigo-600 dark:text-indigo-400 block text-[11px]">Baris 3: Satu (1) Huruf Kecil Judul Buku</span>
                                <span class="text-slate-600 dark:text-slate-300 text-[11px]">
                                    Diambil dari 1 huruf kecil pertama judul buku, dengan mengabaikan kata sandang (<em>A, An, The, Si, Sang</em>). Contoh: "Fisika Dasar" &rarr; <strong>f</strong>, "Sang Pemimpi" &rarr; <strong>p</strong>.
                                </span>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <span class="font-bold text-indigo-600 dark:text-indigo-400 block text-[11px]">Baris 4 (Opsional): Nomor Eksemplar / Jilid</span>
                                <span class="text-slate-600 dark:text-slate-300 text-[11px]">
                                    Untuk membedakan buku kembar atau berseri: <strong>c.1</strong> (copy 1), <strong>c.2</strong>, atau <strong>jil.1</strong> / <strong>v.2</strong>.
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Visualisasi Stiker Punggung -->
                    <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-2xl flex flex-col items-center justify-center text-center space-y-3">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Visualisasi Label Punggung Buku:</span>
                        
                        <!-- Box Stiker Standar (3.5cm x 4.5cm) -->
                        <div class="w-32 bg-white text-slate-900 border-2 border-slate-900 rounded-md p-3 shadow-md flex flex-col items-center justify-between font-sans">
                            <div class="text-[8px] font-black uppercase tracking-wider border-b-2 border-slate-900 pb-1 w-full text-center">
                                SMAN 1 LENGKONG
                            </div>
                            <div class="py-2 space-y-0.5">
                                <div class="text-base font-black tracking-wider text-slate-950 leading-none">530</div>
                                <div class="text-sm font-extrabold tracking-wider text-slate-950 leading-none">SUT</div>
                                <div class="text-sm font-bold text-slate-900 leading-none">f</div>
                            </div>
                            <div class="text-[9px] font-mono font-bold border-t border-dashed border-slate-600 pt-0.5 w-full text-center">
                                c.1
                            </div>
                        </div>

                        <div class="text-[10.5px] text-slate-500 max-w-xs leading-relaxed">
                            Contoh buku: <em>"Fisika Dasar 1"</em> karya <em>Bambang Sutrisno</em>, nomor eksemplar 1. Nomor Panggil lengkap: <code class="font-bold text-indigo-600 bg-white dark:bg-slate-900 px-1 py-0.5 rounded font-mono">530 SUT f c.1</code>
                        </div>

                        <div class="w-full pt-3 border-t border-slate-200 dark:border-slate-700 text-left text-[11px]">
                            <strong class="text-slate-800 dark:text-slate-200">10 Kelas Utama DDC Ringkas:</strong>
                            <div class="grid grid-cols-2 gap-x-2 gap-y-1 mt-1 text-[10px] text-slate-600 dark:text-slate-400 font-mono">
                                <div>000 - Komputer & Umum</div>
                                <div>500 - Sains & Matematika</div>
                                <div>100 - Filsafat & Psikologi</div>
                                <div>600 - Terapan & Teknologi</div>
                                <div>200 - Agama (297 = Islam)</div>
                                <div>700 - Kesenian & Olahraga</div>
                                <div>300 - Ilmu Sosial & Hukum</div>
                                <div>800 - Kesusastraan & Fiksi</div>
                                <div>400 - Bahasa & Linguistik</div>
                                <div>900 - Sejarah & Geografi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: NOMOR INDUK & BARCODE -->
            <div id="guide-tab-nomor-induk" class="guide-tab-pane hidden space-y-4">
                <div class="p-4 rounded-2xl bg-cyan-50/70 dark:bg-cyan-950/40 border border-cyan-200/60 dark:border-cyan-800/60">
                    <h4 class="font-bold text-sm text-cyan-900 dark:text-cyan-300 mb-1">Apa itu Nomor Induk Buku & Barcode?</h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300">
                        Nomor Induk (<em>Accession Number</em> / Nomor Inventaris) adalah nomor urut unik yang diberikan untuk setiap eksemplar fisik buku yang masuk ke koleksi perpustakaan. Nomor ini tidak boleh berubah sepanjang umur buku, dan diubah menjadi bentuk <strong>Barcode Code 128</strong> agar dapat dibaca otomatis oleh mesin scanner perpustakaan saat transaksi peminjaman & pengembalian buku.
                    </p>
                </div>

                <div class="space-y-3">
                    <h5 class="font-extrabold text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">
                        Format Standar Nomor Induk Perpustakaan Sekolah:
                    </h5>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-[11px]">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-1">
                            <span class="font-bold text-cyan-600 dark:text-cyan-400 text-xs">Pola 1: Standar Perpustakaan Nasional (Perpusnas)</span>
                            <div class="font-mono font-bold text-slate-900 dark:text-white text-xs bg-white dark:bg-slate-900 px-2 py-1 rounded border border-slate-200 dark:border-slate-700">
                                [KODE_SEKOLAH]-[TAHUN]-[NO_URUT]-[EKSEMPLAR]
                            </div>
                            <p class="text-slate-500 text-[10.5px]">Contoh: <code class="font-bold">SMA1LE-2026-00045-01</code> (Koleksi SMAN 1 Lengkong tahun 2026 urutan ke-45 eksemplar 1).</p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-1">
                            <span class="font-bold text-indigo-600 dark:text-indigo-400 text-xs">Pola 2: Standar Otomasi SLiMS / Barcode Cepat</span>
                            <div class="font-mono font-bold text-slate-900 dark:text-white text-xs bg-white dark:bg-slate-900 px-2 py-1 rounded border border-slate-200 dark:border-slate-700">
                                B[NOMOR_URUT_5_DIGIT] atau LIB-[TAHUN]-[ID]
                            </div>
                            <p class="text-slate-500 text-[10.5px]">Contoh: <code class="font-bold">B00125</code> atau <code class="font-bold">LIB-2026-00125</code>.</p>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/60 text-[11px] text-amber-900 dark:text-amber-200 space-y-1">
                        <strong>📌 Lokasi & Ketentuan Penempelan Barcode:</strong>
                        <ul class="list-disc list-inside space-y-0.5 text-[10.5px]">
                            <li>Ditempel pada bagian halaman judul utama (title page) atau di bagian dalam sampul belakang buku.</li>
                            <li>Pastikan permukaan barcode rata dan tidak terlipat agar scanner laser USB maupun kamera smartphone dapat membaca barcode secara instan.</li>
                            <li>Buku yang memiliki lebih dari satu eksemplar (misal ada 5 buku dengan judul sama) masing-masing memiliki nomor eksemplar barcode tersendiri (-01 s/d -05).</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TAB 3: SIMULATOR & GENERATOR INTERAKTIF -->
            <div id="guide-tab-simulator" class="guide-tab-pane hidden space-y-4">
                <div class="p-3.5 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-xs">
                    <strong>Kalkulator Generator Otomatis:</strong> Ketik judul buku, nama pengarang, dan nomor DDC di bawah ini untuk melihat simulasi hasil nomor panggil & stiker secara langsung.
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Buku</label>
                        <input type="text" id="sim-title" value="Fisika Dasar untuk SMA/MA" oninput="calculateSimulator()" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Penulis / Pengarang</label>
                        <input type="text" id="sim-author" value="Halliday & Resnick" oninput="calculateSimulator()" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kode DDC</label>
                        <input type="text" id="sim-ddc" value="530" oninput="calculateSimulator()" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white font-mono">
                    </div>
                </div>

                <!-- Hasil Kalkulasi Simulator -->
                <div class="bg-slate-100 dark:bg-slate-800/80 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-around gap-4 text-center">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor Panggil Dihasilkan (Call Number)</div>
                        <div id="sim-result-text" class="text-sm font-mono font-black text-indigo-600 dark:text-indigo-400 bg-white dark:bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 inline-block">
                            530 HAL f c.1
                        </div>
                    </div>

                    <!-- Visualisasi Mini Stiker Punggung -->
                    <div class="w-28 bg-white text-slate-900 border-2 border-slate-900 rounded p-2 shadow-md font-sans">
                        <div class="text-[7px] font-black uppercase tracking-wider border-b border-slate-900 pb-0.5">
                            SMAN 1 LENGKONG
                        </div>
                        <div class="py-1">
                            <div id="sim-vis-ddc" class="text-sm font-black text-slate-950 leading-none">530</div>
                            <div id="sim-vis-author" class="text-xs font-black text-slate-950 leading-none mt-0.5">HAL</div>
                            <div id="sim-vis-title" class="text-xs font-bold text-slate-900 leading-none mt-0.5">f</div>
                        </div>
                        <div id="sim-vis-copy" class="text-[8px] font-mono font-bold border-t border-dashed border-slate-600 pt-0.5">
                            c.1
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 flex items-center justify-between shrink-0">
            <span class="text-[11px] text-slate-500">
                Pustakawan SMAN 1 Lengkong • Standar Akreditasi Perpustakaan Sekolah
            </span>
            <button type="button" onclick="closePanduanKatalogisasiModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-xs shadow-md transition cursor-pointer">
                Tutup Panduan
            </button>
        </div>

    </div>
</div>

<!-- Modal Dialog Cetak Stiker & Barcode Buku Massal -->
<div id="modalBatchPrintLabels" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-200">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 p-5 text-white flex items-center justify-between border-b border-emerald-900/40">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 text-xl font-bold">
                    🏷️
                </div>
                <div>
                    <h3 class="font-bold text-base text-white">Cetak Stiker & Barcode Buku</h3>
                    <p class="text-xs text-slate-300">Format stiker tempel buku perpustakaan (A4 / F4)</p>
                </div>
            </div>
            <button type="button" onclick="closeBatchPrintLabelsModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Form Filter Cetak -->
        <form action="{{ route('library.books.print_labels') }}" method="GET" target="_blank" class="p-6 space-y-4 text-xs">
            <!-- Filter Kategori DDC -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan Kategori DDC</label>
                <select name="category_id" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-emerald-500 text-slate-900 dark:text-white text-xs font-semibold">
                    <option value="">Semua Kategori (Seluruh Katalog Buku)</option>
                    @if(isset($categories))
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">DDC {{ $cat->ddc_code }} - {{ $cat->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Pilihan Jenis Stiker -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Format Tampilan Stiker</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300 font-bold text-center cursor-pointer">
                        <input type="radio" name="label_type" value="both" checked class="hidden">
                        <span>📌 Lengkap</span>
                        <span class="text-[9.5px] font-normal text-slate-500">Punggung + Barcode</span>
                    </label>
                    <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-center cursor-pointer">
                        <input type="radio" name="label_type" value="spine" class="hidden">
                        <span>🏷️ Punggung</span>
                        <span class="text-[9.5px] font-normal text-slate-500">Call Number Saja</span>
                    </label>
                    <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-center cursor-pointer">
                        <input type="radio" name="label_type" value="barcode" class="hidden">
                        <span>📟 Barcode</span>
                        <span class="text-[9.5px] font-normal text-slate-500">No. Induk Saja</span>
                    </label>
                </div>
            </div>

            <!-- Pilihan Kelipatan Eksemplar -->
            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 space-y-2">
                <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-800 dark:text-slate-200">
                    <input type="checkbox" name="include_copies" value="1" class="w-4 h-4 rounded text-emerald-600 focus:ring-0">
                    <span>Cetak label untuk setiap nomor eksemplar buku (c.1, c.2, dst.)</span>
                </label>
                <p class="text-[10.5px] text-slate-500 leading-normal">
                    Jika dicentang, buku yang memiliki 5 eksemplar akan mencetak 5 stiker sekaligus dengan kode copy berurutan. Jika tidak dicentang, hanya dicetak 1 label per judul buku.
                </p>
            </div>

            <!-- Ukuran Kertas -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Ukuran Kertas</label>
                    <select name="paper" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-emerald-500 text-slate-900 dark:text-white text-xs">
                        <option value="a4" selected>A4 (210 × 297 mm)</option>
                        <option value="f4">F4 / Folio (215 × 330 mm)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pencarian Spesifik</label>
                    <input type="text" name="search" placeholder="Opsional kata kunci..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-emerald-500 text-slate-900 dark:text-white text-xs">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeBatchPrintLabelsModal()" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl font-bold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-md shadow-emerald-600/30 flex items-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Buka Lembar Cetak Stiker
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPanduanKatalogisasiModal() {
        document.getElementById('modalPanduanKatalogisasi')?.classList.remove('hidden');
    }

    function closePanduanKatalogisasiModal() {
        document.getElementById('modalPanduanKatalogisasi')?.classList.add('hidden');
    }

    function openBatchPrintLabelsModal() {
        document.getElementById('modalBatchPrintLabels')?.classList.remove('hidden');
    }

    function closeBatchPrintLabelsModal() {
        document.getElementById('modalBatchPrintLabels')?.classList.add('hidden');
    }

    function switchGuideTab(tabKey) {
        document.querySelectorAll('.guide-tab-pane').forEach(el => el.classList.add('hidden'));
        document.getElementById('guide-tab-' + tabKey)?.classList.remove('hidden');

        ['call-number', 'nomor-induk', 'simulator'].forEach(k => {
            const btn = document.getElementById('tab-btn-' + k);
            if (btn) {
                if (k === tabKey) {
                    btn.classList.add('border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                    btn.classList.remove('border-transparent', 'text-slate-500');
                } else {
                    btn.classList.remove('border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                    btn.classList.add('border-transparent', 'text-slate-500');
                }
            }
        });
    }

    function calculateSimulator() {
        const title = document.getElementById('sim-title')?.value || 'Judul Buku';
        const author = document.getElementById('sim-author')?.value || 'Penulis';
        const ddc = document.getElementById('sim-ddc')?.value || '000';

        // Extract author cutter code (3 letters)
        const cleanAuthor = author.replace(/[^a-zA-Z\s]/g, '').trim();
        const words = cleanAuthor.split(/\s+/).filter(Boolean);
        let authorCode = 'AAA';
        if (words.length > 0) {
            const lastWord = words[words.length - 1];
            authorCode = (lastWord.length >= 3 ? lastWord : words[0]).substring(0, 3).toUpperCase();
        }

        // Extract 1 title letter (skipping stop words The, A, An, Si, Sang)
        let cleanTitle = title.replace(/^(the|a|an|si|sang)\s+/i, '').replace(/[^a-zA-Z]/g, '').trim();
        const titleLetter = (cleanTitle.charAt(0) || 'a').toLowerCase();

        // Update display
        const result = `${ddc} ${authorCode} ${titleLetter} c.1`;
        const resEl = document.getElementById('sim-result-text');
        if (resEl) resEl.textContent = result;

        const visDdc = document.getElementById('sim-vis-ddc');
        const visAuthor = document.getElementById('sim-vis-author');
        const visTitle = document.getElementById('sim-vis-title');
        if (visDdc) visDdc.textContent = ddc;
        if (visAuthor) visAuthor.textContent = authorCode;
        if (visTitle) visTitle.textContent = titleLetter;
    }
</script>

