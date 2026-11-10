<!-- ======================================================== -->
<!-- GLOBAL LMS RICH EDITOR ENGINE & WORD-LIKE MODALS SYSTEM  -->
<!-- ======================================================== -->

<!-- 1. MODAL SISIPKAN GAMBAR (DENGAN PILIHAN UKURAN & POSISI) -->
<div id="modal-rich-image-picker" class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <span>🖼️ Sisipkan Gambar ke Soal / Tugas</span>
            </h3>
            <button type="button" onclick="window.RichEditor.closeModal('modal-rich-image-picker')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-bold">✕</button>
        </div>

        <!-- Tab Pilihan: Upload File atau Link URL -->
        <div class="flex rounded-xl bg-slate-100 dark:bg-slate-800 p-1 text-xs font-bold">
            <button type="button" onclick="window.RichEditor.switchImageTab('upload')" id="tab-btn-img-upload" class="flex-1 py-1.5 rounded-lg bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-2xs text-center transition">
                Unggah Berkas Gambar
            </button>
            <button type="button" onclick="window.RichEditor.switchImageTab('url')" id="tab-btn-img-url" class="flex-1 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 text-center transition">
                Tautan / Link URL
            </button>
        </div>

        <!-- Form Tab Upload -->
        <div id="image-tab-upload" class="space-y-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih File Gambar dari Komputer / HP *</label>
                <input type="file" id="rich-editor-file-input" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml,image/gif" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white">
                <p class="text-[11px] text-slate-400 mt-1">Mendukung format PNG, JPG, WebP, SVG, GIF (Maks. 5 MB)</p>
            </div>
            <div id="rich-img-upload-loading" class="hidden text-center py-2 text-indigo-600 dark:text-indigo-400 font-bold flex items-center justify-center gap-2">
                <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span>Mengunggah berkas gambar ke server...</span>
            </div>
        </div>

        <!-- Form Tab URL -->
        <div id="image-tab-url" class="space-y-3 text-xs hidden">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat / Tautan URL Gambar *</label>
                <input type="url" id="rich-editor-url-input" placeholder="https://domain.com/gambar-soal.png" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>
        </div>

        <!-- Pengaturan Tampilan Gambar (Ukuran & Posisi) -->
        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Ukuran Gambar:</label>
                <select id="rich-editor-img-size" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-slate-800 dark:text-slate-200 font-semibold">
                    <option value="medium" selected>Sedang (Maks. 420px - Standar)</option>
                    <option value="small">Kecil (Maks. 220px - Ikon / Diagram Mini)</option>
                    <option value="large">Besar (Maks. 650px - Peta / Grafik Detail)</option>
                    <option value="full">Lebar Penuh (100% Kontainer)</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Posisi / Perataan:</label>
                <select id="rich-editor-img-align" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-slate-800 dark:text-slate-200 font-semibold">
                    <option value="center" selected>Rata Tengah (Tengah Soal)</option>
                    <option value="left">Rata Kiri</option>
                    <option value="right">Rata Kanan</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Judul Gambar (Opsional):</label>
            <input type="text" id="rich-editor-img-caption" placeholder="Contoh: Gambar 1. Skema Rangkaian Listrik Arus Bolak-Balik" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-800">
            <span class="text-[11px] text-slate-400">Gambar akan langsung tertata rapi.</span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.RichEditor.closeModal('modal-rich-image-picker')" class="px-3.5 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="button" onclick="window.RichEditor.submitImage()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                    <span>✓ Sisipkan Gambar</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 2. MODAL PALET SIMBOL SAINS & MATEMATIKA LENGKAP -->
<div id="modal-rich-symbol-palette" class="fixed inset-0 z-[70] flex items-center justify-center p-3 sm:p-4 bg-slate-950/75 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 sm:p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 id="symbol-palette-title" class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📐 Palet Simbol Matematika & Sains Lengkap</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Klik simbol atau rumus untuk langsung menyisipkannya ke posisi kursor soal / tugas</p>
            </div>
            <button type="button" onclick="window.RichEditor.closeModal('modal-rich-symbol-palette')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-bold">✕</button>
        </div>

        <!-- Tab Utama Sains (Matematika, Kimia, Fisika) -->
        <div class="flex rounded-xl bg-slate-100 dark:bg-slate-800 p-1 text-xs font-bold gap-1">
            <button type="button" onclick="window.RichEditor.renderSymbolCategory('math')" id="tab-sym-math" class="flex-1 py-1.5 rounded-lg text-center transition flex items-center justify-center gap-1">
                <span>📐 Matematika Lengkap</span>
            </button>
            <button type="button" onclick="window.RichEditor.renderSymbolCategory('chemistry')" id="tab-sym-chem" class="flex-1 py-1.5 rounded-lg text-center transition flex items-center justify-center gap-1">
                <span>🧪 Kimia</span>
            </button>
            <button type="button" onclick="window.RichEditor.renderSymbolCategory('physics')" id="tab-sym-phys" class="flex-1 py-1.5 rounded-lg text-center transition flex items-center justify-center gap-1">
                <span>⚡ Fisika</span>
            </button>
        </div>

        <!-- Kolom Pencarian Cepat Simbol / Rumus -->
        <div class="relative">
            <input type="text" id="symbol-search-input" oninput="window.RichEditor.filterSymbols(this.value)" placeholder="🔍 Cari simbol atau rumus (misal: integral, akar, limit, sin, deret, sigma, matriks, pecahan, log, delta)..." class="w-full text-xs bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl pl-3 pr-8 py-2 text-slate-900 dark:text-white outline-none focus:border-indigo-500">
            <button type="button" onclick="document.getElementById('symbol-search-input').value = ''; window.RichEditor.filterSymbols('');" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-xs">✕</button>
        </div>

        <!-- Sub-Pills Kategori Khusus Matematika -->
        <div id="math-subcategories-bar" class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[11px] no-scrollbar">
            <!-- Dynamically rendered or toggled via JS -->
        </div>

        <!-- Toast Feedback Saat Simbol Diklik -->
        <div id="symbol-inserted-toast" class="hidden p-2 rounded-xl bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20 text-xs font-bold flex items-center justify-between">
            <span id="symbol-inserted-text">✓ Simbol disisipkan ke naskah</span>
            <span class="text-[10px] text-emerald-600 font-normal">Dapat klik simbol lain berikutnya</span>
        </div>

        <!-- Wadah Grid Simbol -->
        <div id="symbol-grid-container" class="space-y-4 text-xs min-h-[220px]">
            <!-- Dynamically Rendered via JS -->
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-800">
            <span class="text-[11px] text-slate-400">Modal dapat tetap terbuka untuk menyusun rumus majemuk.</span>
            <button type="button" onclick="window.RichEditor.closeModal('modal-rich-symbol-palette')" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                Selesai
            </button>
        </div>
    </div>
</div>

<!-- 3. MODAL VIRTUAL KEYBOARD HURUF ARAB & HARAKAT -->
<div id="modal-rich-arabic-keyboard" class="fixed inset-0 z-[70] flex items-center justify-center p-3 sm:p-4 bg-slate-950/75 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 sm:p-6 max-w-xl w-full max-h-[88vh] overflow-y-auto shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>🇸🇦 Virtual Keyboard Huruf Hijaiyah & Harakat</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Ketik kata dan kalimat Arab lengkap dengan harakat dan tanda baca</p>
            </div>
            <button type="button" onclick="window.RichEditor.closeModal('modal-rich-arabic-keyboard')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-bold">✕</button>
        </div>

        <!-- Preview Sementara Teks Arab yang Sedang Diketik -->
        <div class="p-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl space-y-2">
            <div class="flex items-center justify-between text-[11px] text-slate-400">
                <span>Pratinjau Teks Arab:</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.RichEditor.backspaceArabic()" class="text-amber-600 hover:underline font-bold">⌫ Hapus 1 Karakter</button>
                    <span class="text-slate-300">&bull;</span>
                    <button type="button" onclick="document.getElementById('arabic-live-preview').value = ''" class="text-rose-500 hover:underline font-bold">Hapus Bersih</button>
                </div>
            </div>
            <textarea id="arabic-live-preview" rows="2" dir="rtl" class="w-full text-lg sm:text-xl font-bold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-right font-serif text-slate-900 dark:text-white outline-none focus:border-emerald-500"></textarea>
            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="window.RichEditor.insertArabicPreviewToTarget()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                    <span>Sisipkan Teks Ini ke Soal/Tugas &rarr;</span>
                </button>
            </div>
        </div>

        <!-- 1. Baris Tanda Harakat & Tanda Baca -->
        <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Tanda Harakat / Syakal:</div>
            <div class="grid grid-cols-8 gap-1.5" dir="rtl">
                @php
                    $harakats = [
                        ['char' => "\u{064E}", 'name' => 'Fathah'],
                        ['char' => "\u{0650}", 'name' => 'Kasrah'],
                        ['char' => "\u{064F}", 'name' => 'Dhammah'],
                        ['char' => "\u{064B}", 'name' => 'Fathatain'],
                        ['char' => "\u{064D}", 'name' => 'Kasratain'],
                        ['char' => "\u{064C}", 'name' => 'Dhammatain'],
                        ['char' => "\u{0652}", 'name' => 'Sukun'],
                        ['char' => "\u{0651}", 'name' => 'Tasydid'],
                    ];
                @endphp
                @foreach($harakats as $h)
                    <button type="button" onclick="window.RichEditor.typeArabicChar('{{ $h['char'] }}')" class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 text-emerald-800 dark:text-emerald-300 font-bold text-base border border-emerald-200 dark:border-emerald-800 transition text-center" title="{{ $h['name'] }}">
                        ـ{{ $h['char'] }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- 2. Tombol Huruf Hijaiyah Lengkap -->
        <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Huruf Hijaiyah:</div>
            <div class="grid grid-cols-7 sm:grid-cols-9 gap-1.5" dir="rtl">
                @php
                    $hijaiyah = ['ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'هـ', 'و', 'ي', 'ء', 'ئ', 'ؤ', 'ة', 'ى', 'لا'];
                @endphp
                @foreach($hijaiyah as $char)
                    <button type="button" onclick="window.RichEditor.typeArabicChar('{{ $char }}')" class="p-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 font-bold text-base border border-slate-200 dark:border-slate-700 shadow-2xs transition text-center font-serif">
                        {{ $char }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- 3. Frasa Islami Populer Cepat -->
        <div>
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Kutipan & Frasa Populer:</div>
            <div class="flex flex-wrap gap-1.5" dir="rtl">
                <button type="button" onclick="window.RichEditor.typeArabicChar('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ ')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-800 dark:text-slate-200 font-serif text-sm border border-slate-200 dark:border-slate-700">
                    بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                </button>
                <button type="button" onclick="window.RichEditor.typeArabicChar('السَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللَّهِ وَبَرَكَاتُهُ ')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-800 dark:text-slate-200 font-serif text-sm border border-slate-200 dark:border-slate-700">
                    السَّلَامُ عَلَيْكُمْ
                </button>
                <button type="button" onclick="window.RichEditor.typeArabicChar('جَزَاكُمُ اللَّهُ خَيْرًا ')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-800 dark:text-slate-200 font-serif text-sm border border-slate-200 dark:border-slate-700">
                    جَزَاكُمُ اللَّهُ خَيْرًا
                </button>
                <button type="button" onclick="window.RichEditor.typeArabicChar('صَلَّى اللَّهُ عَلَيْهِ وَسَلَّمَ ')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-800 dark:text-slate-200 font-serif text-sm border border-slate-200 dark:border-slate-700">
                    صَلَّى اللَّهُ عَلَيْهِ وَسَلَّمَ
                </button>
                <button type="button" onclick="window.RichEditor.typeArabicChar('الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ ')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-800 dark:text-slate-200 font-serif text-sm border border-slate-200 dark:border-slate-700">
                    الْحَمْدُ لِلَّهِ
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-800">
            <button type="button" onclick="window.RichEditor.typeArabicChar(' ')" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs font-bold">
                Spasi
            </button>
            <button type="button" onclick="window.RichEditor.closeModal('modal-rich-arabic-keyboard')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                Tutup Keyboard
            </button>
        </div>
    </div>
</div>

<!-- 4. MODAL PENGAMBIL AYAT AL-QUR'AN (QURAN AYAH PICKER - 114 SURAT LENGKAP) -->
<div id="modal-rich-quran-picker" class="fixed inset-0 z-[70] flex items-center justify-center p-3 sm:p-4 bg-slate-950/75 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 sm:p-6 max-w-xl w-full max-h-[90vh] overflow-y-auto shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📖 Ambil & Sisipkan Ayat Al-Qur'an</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Pilih surat (1-114) dan nomor ayat untuk memuat teks Arab berharakat dan terjemahan resmi Kemenag</p>
            </div>
            <button type="button" onclick="window.RichEditor.closeModal('modal-rich-quran-picker')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-bold">✕</button>
        </div>

        <!-- Filter Pilihan Surat & Ayat -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
            <div class="sm:col-span-8">
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Surat Al-Qur'an *</label>
                <select id="quran-picker-surah" onchange="window.RichEditor.loadQuranAyahPreview()" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
                    @php
                        $surahsList = \App\Services\LmsQuranAyahService::getAvailableSurahs();
                    @endphp
                    @foreach($surahsList as $num => $s)
                        <option value="{{ $num }}" {{ $num === 112 ? 'selected' : '' }}>
                            {{ $num }}. {{ $s['name'] }} ({{ $s['total_ayah'] }} Ayat &mdash; {{ $s['meaning'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-4">
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Ayat *</label>
                <input type="text" id="quran-picker-ayah" value="1" oninput="window.RichEditor.loadQuranAyahPreview()" placeholder="Contoh: 1 atau 1-4" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
            </div>
        </div>

        <!-- Pratinjau Teks Ayat -->
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 space-y-3">
            <div class="flex items-center justify-between text-xs font-bold text-emerald-800 dark:text-emerald-300">
                <span id="quran-preview-ref">QS. Al-Ikhlas [112]: 1</span>
                <span class="text-[10px] uppercase bg-emerald-500/20 px-2 py-0.5 rounded text-emerald-700 dark:text-emerald-300">Teks Arab Terverifikasi</span>
            </div>

            <div id="quran-preview-arabic" dir="rtl" class="font-serif text-xl sm:text-2xl font-bold text-right text-emerald-950 dark:text-emerald-100 leading-loose min-h-[48px]">
                قُلْ هُوَ اللَّهُ أَحَدٌ
            </div>

            <div class="pt-2 border-t border-emerald-500/20 text-xs text-slate-700 dark:text-slate-300">
                <span class="font-bold block text-slate-500 dark:text-slate-400 mb-0.5">Artinya:</span>
                <p id="quran-preview-trans" class="italic leading-relaxed">
                    Katakanlah (Muhammad), "Dialah Allah, Yang Maha Esa."
                </p>
            </div>
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-800">
            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 dark:text-slate-400">
                <input type="checkbox" id="quran-include-translation" checked class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                <span>Sertakan terjemahan bahasa Indonesia</span>
            </label>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.RichEditor.closeModal('modal-rich-quran-picker')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="button" onclick="window.RichEditor.insertQuranAyahToTarget()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                    <span>Sisipkan ke Soal / Tugas</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT GLOBAL RICH EDITOR CONTROLLER & ENHANCED WORD-LIKE WYSIWYG SYSTEM -->
<script>
window.RichEditor = {
    activeTargetId: 'question_text',
    activeCategory: 'math',
    activeMathSubCat: 'all',
    currentQuranData: null,
    targetModes: {},

    setTarget: function(targetId) {
        if (!targetId) return;
        this.activeTargetId = targetId;
    },

    // Inisialisasi Canvas Visual Seperti Word
    initVisualCanvas: function(targetId) {
        if (!targetId) return;
        const textarea = document.getElementById(targetId);
        if (!textarea || textarea.__richVisualReady) return;
        textarea.__richVisualReady = true;

        const targetSlug = targetId.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        let visualCanvas = document.getElementById('rich-visual-' + targetSlug);

        if (!visualCanvas) {
            visualCanvas = document.createElement('div');
            visualCanvas.id = 'rich-visual-' + targetSlug;
            visualCanvas.contentEditable = "true";
            visualCanvas.className = 'rich-visual-canvas w-full p-4 min-h-[160px] max-h-[560px] overflow-y-auto bg-white dark:bg-slate-900 border-x border-b border-slate-300 dark:border-slate-700 rounded-b-xl text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 leading-relaxed font-sans shadow-inner';
            visualCanvas.setAttribute('spellcheck', 'false');
            visualCanvas.setAttribute('data-target-id', targetId);

            // Sisipkan visual canvas tepat setelah textarea
            textarea.parentNode.insertBefore(visualCanvas, textarea.nextSibling);
        }

        // Tampilkan konten awal (konversi markdown/HTML ke visual live)
        const initialRaw = textarea.value || '';
        visualCanvas.innerHTML = this.markdownToVisualHtml(initialRaw) || '<p><br></p>';
        textarea.classList.add('hidden'); // Sembunyikan textarea mentah secara default
        this.targetModes[targetId] = 'visual';

        // Event sync dua arah real-time
        const syncToTextarea = () => {
            textarea.value = visualCanvas.innerHTML;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            this.updateLivePreviewIfActive(targetId);
        };

        visualCanvas.addEventListener('input', syncToTextarea);
        visualCanvas.addEventListener('blur', syncToTextarea);
        visualCanvas.addEventListener('focus', () => {
            this.setTarget(targetId);
        });

        // Fitur Word: Tempel Gambar dari Clipboard (Ctrl + V)
        visualCanvas.addEventListener('paste', (e) => {
            const clipboardData = e.clipboardData || window.clipboardData;
            if (!clipboardData) return;
            const items = clipboardData.items;
            if (!items) return;

            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    e.preventDefault();
                    const file = items[i].getAsFile();
                    if (file) {
                        this.uploadAndInsertImageFile(targetId, file);
                    }
                    return;
                }
            }
        });

        // Pastikan sinkron sebelum submit form
        if (textarea.form) {
            textarea.form.addEventListener('submit', () => {
                textarea.value = visualCanvas.innerHTML;
            });
        }
    },

    // Unggah dan Sisipkan Berkas Gambar secara Visual
    uploadAndInsertImageFile: function(targetId, file) {
        if (!file) return;
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        const toast = document.getElementById('symbol-inserted-toast');
        const toastText = document.getElementById('symbol-inserted-text');
        if (toast && toastText) {
            toastText.textContent = '⏳ Mengunggah gambar...';
            toast.classList.remove('hidden');
        }

        fetch('{{ route("lms.editor.upload_image") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (toast) toast.classList.add('hidden');
            if (data.success && data.url) {
                const imgHtml = '<div class="my-3 text-center rich-img-block" contenteditable="false">' +
                    '<img src="' + data.url + '" alt="Gambar Sisipan" class="mx-auto rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm max-h-[420px] max-w-full" style="object-fit: contain;">' +
                    '<p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 italic font-medium">Gambar Terlampir</p>' +
                    '</div><p><br></p>';
                this.insertAtCursor(targetId, imgHtml);
            } else {
                alert('Gagal mengunggah gambar: ' + (data.message || 'Format tidak didukung'));
            }
        })
        .catch(err => {
            if (toast) toast.classList.add('hidden');
            console.error(err);
            alert('Terjadi kesalahan saat mengunggah gambar.');
        });
    },

    insertAtCursor: function(targetId, htmlOrText) {
        const target = targetId || this.activeTargetId;
        const targetSlug = target.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        const visualCanvas = document.getElementById('rich-visual-' + targetSlug);
        const textarea = document.getElementById(target);
        const currentMode = this.targetModes[target] || 'visual';

        // 1. Jika Target adalah Elemen Input Teks Biasa (misal Opsi Pilihan A, B, C, D, E)
        if (textarea && textarea.tagName === 'INPUT') {
            const start = textarea.selectionStart || 0;
            const end = textarea.selectionEnd || 0;
            const val = textarea.value;
            // Bersihkan tag block untuk input baris tunggal jika ada
            const cleanText = htmlOrText.replace(/<[^>]*>?/gm, '').trim() || htmlOrText;
            textarea.value = val.substring(0, start) + cleanText + val.substring(end);
            textarea.focus();
            textarea.selectionStart = textarea.selectionEnd = start + cleanText.length;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            return;
        }

        // 2. Jika Mode Visual Aktif (Seperti Word & Gambar Langsung Tampil)
        if (visualCanvas && currentMode === 'visual' && !visualCanvas.classList.contains('hidden')) {
            visualCanvas.focus();
            const sel = window.getSelection();
            if (sel && sel.rangeCount > 0) {
                const range = sel.getRangeAt(0);
                range.deleteContents();

                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = htmlOrText;
                const frag = document.createDocumentFragment();
                let node, lastNode;
                while ((node = tempDiv.firstChild)) {
                    lastNode = frag.appendChild(node);
                }
                range.insertNode(frag);

                if (lastNode) {
                    range.setStartAfter(lastNode);
                    range.collapse(true);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            } else {
                visualCanvas.innerHTML += htmlOrText;
            }

            if (textarea) {
                textarea.value = visualCanvas.innerHTML;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
            }
            this.updateLivePreviewIfActive(target);
            return;
        }

        // 3. Fallback: Mode Kode HTML atau Textarea mentah
        if (textarea) {
            const start = textarea.selectionStart || 0;
            const end = textarea.selectionEnd || 0;
            const val = textarea.value;
            textarea.value = val.substring(0, start) + htmlOrText + val.substring(end);
            textarea.focus();
            textarea.selectionStart = textarea.selectionEnd = start + htmlOrText.length;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));

            if (visualCanvas) {
                visualCanvas.innerHTML = this.markdownToVisualHtml(textarea.value);
            }
            this.updateLivePreviewIfActive(target);
        }
    },

    wrapText: function(targetId, prefix, suffix) {
        const target = targetId || this.activeTargetId;
        const targetSlug = target.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        const visualCanvas = document.getElementById('rich-visual-' + targetSlug);
        const textarea = document.getElementById(target);
        const currentMode = this.targetModes[target] || 'visual';

        // Jika dalam Canvas Visual Word-like, gunakan Rich Text Commands
        if (visualCanvas && currentMode === 'visual' && !visualCanvas.classList.contains('hidden')) {
            visualCanvas.focus();
            if (prefix === '**') {
                document.execCommand('bold', false, null);
            } else if (prefix === '*') {
                document.execCommand('italic', false, null);
            } else if (prefix === '<u>') {
                document.execCommand('underline', false, null);
            } else if (prefix === '<sup>') {
                document.execCommand('superscript', false, null);
            } else if (prefix === '<sub>') {
                document.execCommand('subscript', false, null);
            }
            if (textarea) {
                textarea.value = visualCanvas.innerHTML;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
            }
            this.updateLivePreviewIfActive(target);
            return;
        }

        // Mode HTML / Input Textarea
        if (!textarea) return;
        const start = textarea.selectionStart || 0;
        const end = textarea.selectionEnd || 0;
        const val = textarea.value;
        const selected = val.substring(start, end);
        const wrapped = prefix + (selected || 'teks') + suffix;

        textarea.value = val.substring(0, start) + wrapped + val.substring(end);
        textarea.focus();
        textarea.selectionStart = start + prefix.length;
        textarea.selectionEnd = start + prefix.length + (selected ? selected.length : 4);
        textarea.dispatchEvent(new Event('input', { bubbles: true }));

        if (visualCanvas) {
            visualCanvas.innerHTML = this.markdownToVisualHtml(textarea.value);
        }
        this.updateLivePreviewIfActive(target);
    },

    insertList: function(targetId, type) {
        const target = targetId || this.activeTargetId;
        const targetSlug = target.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        const visualCanvas = document.getElementById('rich-visual-' + targetSlug);
        const textarea = document.getElementById(target);
        const currentMode = this.targetModes[target] || 'visual';

        if (visualCanvas && currentMode === 'visual' && !visualCanvas.classList.contains('hidden')) {
            visualCanvas.focus();
            document.execCommand(type === 'ordered' ? 'insertOrderedList' : 'insertUnorderedList', false, null);
            if (textarea) {
                textarea.value = visualCanvas.innerHTML;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
            }
            return;
        }

        this.insertAtCursor(target, type === 'ordered' ? '\n1. ' : '\n- ');
    },

    insertBlockquote: function(targetId) {
        const quote = '<blockquote class="p-3.5 my-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border-l-4 border-indigo-500 italic text-slate-700 dark:text-slate-200 text-xs leading-relaxed">\n  Tuliskan teks stimulus atau cerita soal di sini...\n</blockquote><p><br></p>';
        this.insertAtCursor(targetId, quote);
    },

    insertTable: function(targetId) {
        const table = '<table class="w-full my-3 border border-slate-300 dark:border-slate-700 text-xs text-left rounded-xl overflow-hidden">\n' +
            '  <thead class="bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold">\n' +
            '    <tr>\n      <th class="border border-slate-300 dark:border-slate-700 p-2">Kolom 1</th>\n      <th class="border border-slate-300 dark:border-slate-700 p-2">Kolom 2</th>\n      <th class="border border-slate-300 dark:border-slate-700 p-2">Keterangan</th>\n    </tr>\n' +
            '  </thead>\n' +
            '  <tbody class="divide-y divide-slate-200 dark:divide-slate-700">\n' +
            '    <tr>\n      <td class="border border-slate-300 dark:border-slate-700 p-2">Data A</td>\n      <td class="border border-slate-300 dark:border-slate-700 p-2">Nilai 1</td>\n      <td class="border border-slate-300 dark:border-slate-700 p-2">-</td>\n    </tr>\n' +
            '    <tr>\n      <td class="border border-slate-300 dark:border-slate-700 p-2">Data B</td>\n      <td class="border border-slate-300 dark:border-slate-700 p-2">Nilai 2</td>\n      <td class="border border-slate-300 dark:border-slate-700 p-2">-</td>\n    </tr>\n' +
            '  </tbody>\n' +
            '</table><p><br></p>';
        this.insertAtCursor(targetId, table);
    },

    openModal: function(modalId, targetId) {
        if (targetId) this.setTarget(targetId);
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('hidden');
    },

    closeModal: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('hidden');
    },

    // --- TOGGLE 3 MODE: VISUAL (WORD) VS KODE HTML VS PRATINJAU SISWA ---
    toggleMode: function(targetId, mode) {
        const textarea = document.getElementById(targetId);
        if (!textarea) return;

        const targetSlug = targetId.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        const btnVisual = document.getElementById('btn-mode-visual-' + targetSlug);
        const btnHtml = document.getElementById('btn-mode-html-' + targetSlug);
        const btnPrev = document.getElementById('btn-mode-prev-' + targetSlug);

        // Pastikan visual canvas terpasang
        this.initVisualCanvas(targetId);
        const visualCanvas = document.getElementById('rich-visual-' + targetSlug);

        let preview = document.getElementById('rich-preview-' + targetSlug);
        if (!preview) {
            preview = document.createElement('div');
            preview.id = 'rich-preview-' + targetSlug;
            preview.className = 'rich-editor-preview p-4 min-h-[160px] max-h-[560px] overflow-y-auto bg-slate-50 dark:bg-slate-900 border-x border-b border-slate-300 dark:border-slate-700 rounded-b-xl text-xs text-slate-800 dark:text-slate-100 overflow-x-auto leading-relaxed shadow-inner hidden';
            textarea.parentNode.insertBefore(preview, textarea.nextSibling);
        }

        this.targetModes[targetId] = mode;

        const resetTabs = () => {
            [btnVisual, btnHtml, btnPrev].forEach(btn => {
                if (btn) {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
                    btn.classList.add('text-slate-600', 'dark:text-slate-300');
                }
            });
        };

        if (mode === 'visual') {
            resetTabs();
            if (btnVisual) {
                btnVisual.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
                btnVisual.classList.remove('text-slate-600', 'dark:text-slate-300');
            }
            if (visualCanvas) {
                visualCanvas.innerHTML = this.markdownToVisualHtml(textarea.value);
                visualCanvas.classList.remove('hidden');
                visualCanvas.focus();
            }
            textarea.classList.add('hidden');
            preview.classList.add('hidden');
        } else if (mode === 'html') {
            resetTabs();
            if (btnHtml) {
                btnHtml.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
                btnHtml.classList.remove('text-slate-600', 'dark:text-slate-300');
            }
            if (visualCanvas) {
                textarea.value = visualCanvas.innerHTML;
                visualCanvas.classList.add('hidden');
            }
            textarea.classList.remove('hidden');
            textarea.focus();
            preview.classList.add('hidden');
        } else if (mode === 'preview') {
            resetTabs();
            if (btnPrev) {
                btnPrev.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
                btnPrev.classList.remove('text-slate-600', 'dark:text-slate-300');
            }
            const content = visualCanvas ? visualCanvas.innerHTML : textarea.value;
            preview.innerHTML = this.renderPreviewContent(content);
            if (visualCanvas) visualCanvas.classList.add('hidden');
            textarea.classList.add('hidden');
            preview.classList.remove('hidden');
        }
    },

    updateLivePreviewIfActive: function(targetId) {
        const targetSlug = targetId.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        const preview = document.getElementById('rich-preview-' + targetSlug);
        const visualCanvas = document.getElementById('rich-visual-' + targetSlug);
        const textarea = document.getElementById(targetId);
        if (preview && !preview.classList.contains('hidden')) {
            const content = (visualCanvas && !visualCanvas.classList.contains('hidden')) ? visualCanvas.innerHTML : (textarea ? textarea.value : '');
            preview.innerHTML = this.renderPreviewContent(content);
        }
    },

    markdownToVisualHtml: function(raw) {
        if (!raw) return '';
        let parsed = raw;

        // Gambar markdown ![caption](url)
        parsed = parsed.replace(/!\[(.*?)\]\((.*?)\)/g, '<div class="my-3 text-center rich-img-block" contenteditable="false"><img src="$2" alt="$1" class="mx-auto rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm max-h-[420px] max-w-full" style="object-fit: contain;"><p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 italic font-medium">$1</p></div><p><br></p>');

        // Bold & Italic Markdown
        parsed = parsed.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        parsed = parsed.replace(/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/g, '<em>$1</em>');

        return parsed;
    },

    renderPreviewContent: function(raw) {
        if (!raw || !raw.trim() || raw.trim() === '<p><br></p>') {
            return '<div class="p-8 text-center text-slate-400 dark:text-slate-500 italic flex flex-col items-center justify-center gap-2">' +
                '<span class="text-2xl">📝</span>' +
                '<span>Belum ada naskah atau pertanyaan. Klik tombol <strong>📄 Visual (Seperti Word)</strong> di atas untuk mulai mengetik instruksi, rumus, atau menyisipkan gambar.</span>' +
                '</div>';
        }

        let parsed = this.markdownToVisualHtml(raw);

        // Header Markdown
        parsed = parsed.replace(/^### (.*$)/gim, '<h3 class="text-sm font-black text-slate-900 dark:text-white mt-2 mb-1">$1</h3>');
        parsed = parsed.replace(/^## (.*$)/gim, '<h2 class="text-base font-black text-slate-900 dark:text-white mt-2 mb-1">$1</h2>');

        // Newline to br if raw text
        const hasBlockTag = /<(?:div|table|blockquote|p|ul|ol|h[1-6]|img)\b/i.test(parsed);
        if (!hasBlockTag) {
            parsed = parsed.replace(/\n/g, '<br>');
        }

        return '<div class="rich-rendered-output space-y-2">' +
            '<div class="flex items-center gap-1.5 mb-2 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 pb-1.5 border-b border-slate-200 dark:border-slate-800">' +
            '<span>👁️ Pratinjau Tampilan Siswa (Visual Siap Ujian/Tugas):</span>' +
            '</div>' +
            parsed +
            '</div>';
    },

    // --- 1. Image Handlers (Visual Image Insertion) ---
    openImageModal: function(targetId) {
        this.openModal('modal-rich-image-picker', targetId);
    },

    switchImageTab: function(tab) {
        const tabUpload = document.getElementById('image-tab-upload');
        const tabUrl = document.getElementById('image-tab-url');
        const btnUpload = document.getElementById('tab-btn-img-upload');
        const btnUrl = document.getElementById('tab-btn-img-url');

        if (tab === 'upload') {
            tabUpload.classList.remove('hidden');
            tabUrl.classList.add('hidden');
            btnUpload.classList.add('bg-white', 'dark:bg-slate-900', 'text-indigo-600', 'dark:text-indigo-400', 'shadow-2xs');
            btnUpload.classList.remove('text-slate-600', 'dark:text-slate-400');
            btnUrl.classList.remove('bg-white', 'dark:bg-slate-900', 'text-indigo-600', 'dark:text-indigo-400', 'shadow-2xs');
            btnUrl.classList.add('text-slate-600', 'dark:text-slate-400');
        } else {
            tabUpload.classList.add('hidden');
            tabUrl.classList.remove('hidden');
            btnUrl.classList.add('bg-white', 'dark:bg-slate-900', 'text-indigo-600', 'dark:text-indigo-400', 'shadow-2xs');
            btnUrl.classList.remove('text-slate-600', 'dark:text-slate-400');
            btnUpload.classList.remove('bg-white', 'dark:bg-slate-900', 'text-indigo-600', 'dark:text-indigo-400', 'shadow-2xs');
            btnUpload.classList.add('text-slate-600', 'dark:text-slate-400');
        }
    },

    submitImage: function() {
        const isUploadTab = !document.getElementById('image-tab-upload').classList.contains('hidden');
        const caption = document.getElementById('rich-editor-img-caption').value.trim();
        const size = document.getElementById('rich-editor-img-size').value || 'medium';
        const align = document.getElementById('rich-editor-img-align').value || 'center';

        let maxHeightStyle = 'max-height: 420px;';
        let widthClass = 'max-w-full';
        if (size === 'small') {
            maxHeightStyle = 'max-height: 220px; max-width: 280px;';
        } else if (size === 'large') {
            maxHeightStyle = 'max-height: 650px;';
        } else if (size === 'full') {
            maxHeightStyle = 'width: 100%;';
        }

        let alignClass = 'text-center my-3';
        let imgMargin = 'mx-auto';
        if (align === 'left') {
            alignClass = 'text-left my-3';
            imgMargin = 'mr-auto';
        } else if (align === 'right') {
            alignClass = 'text-right my-3';
            imgMargin = 'ml-auto';
        }

        if (isUploadTab) {
            const fileInput = document.getElementById('rich-editor-file-input');
            if (!fileInput.files || !fileInput.files[0]) {
                alert('Silakan pilih berkas gambar terlebih dahulu.');
                return;
            }

            const formData = new FormData();
            formData.append('image', fileInput.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            const loadingEl = document.getElementById('rich-img-upload-loading');
            loadingEl.classList.remove('hidden');

            fetch('{{ route("lms.editor.upload_image") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                loadingEl.classList.add('hidden');
                if (data.success && data.url) {
                    const targetEl = document.getElementById(this.activeTargetId);
                    const isInput = targetEl && targetEl.tagName === 'INPUT';
                    const tag = isInput
                        ? '<img src="' + data.url + '" alt="' + (caption || 'Opsi') + '" style="max-height: 80px; vertical-align: middle; border-radius: 8px; display: inline-block;">'
                        : '<div class="' + alignClass + ' rich-img-block" contenteditable="false">' +
                          '<img src="' + data.url + '" alt="' + (caption || 'Gambar Soal') + '" class="' + imgMargin + ' rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm ' + widthClass + '" style="' + maxHeightStyle + '; object-fit: contain;">' +
                          (caption ? '<p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 italic font-medium">' + caption + '</p>' : '') +
                          '</div><p><br></p>';

                    this.insertAtCursor(this.activeTargetId, tag);
                    this.closeModal('modal-rich-image-picker');
                    fileInput.value = '';
                    document.getElementById('rich-editor-img-caption').value = '';
                } else {
                    alert('Gagal mengunggah gambar: ' + (data.message || 'Format tidak sesuai'));
                }
            })
            .catch(err => {
                loadingEl.classList.add('hidden');
                alert('Terjadi kesalahan saat mengunggah gambar.');
                console.error(err);
            });
        } else {
            const url = document.getElementById('rich-editor-url-input').value.trim();
            if (!url) {
                alert('Silakan masukkan tautan URL gambar.');
                return;
            }
            const targetEl = document.getElementById(this.activeTargetId);
            const isInput = targetEl && targetEl.tagName === 'INPUT';
            const tag = isInput
                ? '<img src="' + url + '" alt="' + (caption || 'Opsi') + '" style="max-height: 80px; vertical-align: middle; border-radius: 8px; display: inline-block;">'
                : '<div class="' + alignClass + ' rich-img-block" contenteditable="false">' +
                  '<img src="' + url + '" alt="' + (caption || 'Gambar Soal') + '" class="' + imgMargin + ' rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm ' + widthClass + '" style="' + maxHeightStyle + '; object-fit: contain;">' +
                  (caption ? '<p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 italic font-medium">' + caption + '</p>' : '') +
                  '</div><p><br></p>';

            this.insertAtCursor(this.activeTargetId, tag);
            this.closeModal('modal-rich-image-picker');
            document.getElementById('rich-editor-url-input').value = '';
            document.getElementById('rich-editor-img-caption').value = '';
        }
    },

    // --- 2. SCIENCE SYMBOL PALETTE ENGINE (LENGKAP MATEMATIKA SMA, KIMIA & FISIKA) ---
    symbolsData: {
        math: [
            {
                subcat: 'arithmetic',
                group: 'Aritmatika, Operasi & Perbandingan',
                items: ['+', '−', '±', '∓', '×', '÷', '·', '∕', '=', '≠', '≈', '≅', '≡', '≢', '~', '≉', '<', '>', '≤', '≥', '≪', '≫', '∝', '∞', '%', '‰', '!', '|x|', '⌊x⌋', '⌈x⌉', '≔', '≝', '∗', '∙']
            },
            {
                subcat: 'powers',
                group: 'Pangkat & Eksponen (Superscript Lengkap)',
                items: [
                    '⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹', '⁺', '⁻', '⁼', '⁽', '⁾', 'ⁿ', 'ⁱ', 'ᵃ', 'ᵇ', 'ᶜ', 'ᵈ', 'ᵉ', 'ᵏ', 'ᵐ', 'ᵒ', 'ᵖ', 'ʳ', 'ˢ', 'ᵗ', 'ᵘ', 'ᵛ', 'ˣ', 'ʸ', 'ᶻ',
                    'x²', 'x³', 'x⁴', 'x⁵', 'x⁶', 'x⁷', 'x⁸', 'x⁹', 'x⁰', 'xⁿ', 'x⁻¹', 'x⁻²', 'xᵃ', 'xᵇ', 'xʸ', 'x⁺', 'x⁻', '(x + y)²', '(x - y)²', '(a + b)³', 'eˣ', 'e⁻ˣ', '10²', '10³', '10⁶', '10⁻³', '10⁻⁶', '10⁻⁹', '2ⁿ', '3ⁿ'
                ]
            },
            {
                subcat: 'indices',
                group: 'Indeks & Urutan (Subscript Lengkap)',
                items: [
                    '₀', '₁', '₂', '₃', '₄', '₅', '₆', '₇', '₈', '₉', '₊', '₋', '₌', '₍', '₎', 'ₐ', 'ₑ', 'ₕ', 'ᵢ', 'ⱼ', 'ₖ', 'ₗ', 'ₘ', 'ₙ', 'ₒ', 'ₚ', 'ᵣ', 'ₛ', 'ₜ', 'ᵤ', 'ᵥ', 'ₓ',
                    'x₀', 'x₁', 'x₂', 'x₃', 'x₄', 'x₅', 'x₆', 'x₇', 'x₈', 'x₉', 'xₙ', 'xᵢ', 'xⱼ', 'y₀', 'y₁', 'y₂', 'z₁', 'z₂', 'a₁', 'a₂', 'aₙ', 'b₁', 'b₂', 'U₁', 'U₂', 'Uₙ', 'Sₙ', 'S_∞', 'f₁', 'f₂', 'k₁', 'k₂'
                ]
            },
            {
                subcat: 'roots',
                group: 'Bentuk Akar & Radikal SMA',
                items: ['√', '√x', '∛', '∛x', '∜', '∜x', 'ⁿ√x', '√2', '√3', '√5', '√6', '√7', '√10', '√(x² + y²)', '√(b² - 4ac)', '√(x₂ - x₁)² ', '√(a² + b²)', '√(a² - b²)', '1/√x', '1/√2', '1/√3', '∛(x + 1)']
            },
            {
                subcat: 'fractions',
                group: 'Pecahan Biasa, Format & Aljabar',
                items: ['½', '⅓', '⅔', '¼', '¾', '⅕', '⅖', '⅗', '⅘', '⅙', '⅚', '⅛', '⅜', '⅝', '⅞', '¹/₂', '¹/₃', '²/₃', '¹/₄', '³/₄', '¹/₅', '²/₅', '³/₅', '⁴/₅', '¹/₆', '⁵/₆', '¹/₈', '³/₈', '⁵/₈', '⁷/₈', '(a)/(b)', '(x + 1)/(x - 1)', '(dy)/(dx)', '(Δy)/(Δx)', '(1)/(2)', '(1)/(3)', '(3)/(4)', '(a + b)/(c)', '(f(x + h) - f(x))/(h)']
            },
            {
                subcat: 'trig',
                group: 'Geometri, Sudut & Trigonometri SMA',
                items: [
                    '∠', '∡', '∢', '⊥', '∥', '∦', '°', "'", '"', '△', '□', '▭', '▱', '◇', '◯', '⌒', '≅', '∼', 'π', 'rad',
                    'sin(θ)', 'cos(θ)', 'tan(θ)', 'csc(θ)', 'sec(θ)', 'cot(θ)', 'sin²(x)', 'cos²(x)', 'tan²(x)', 'sec²(x)', 'sin⁻¹(x)', 'cos⁻¹(x)', 'tan⁻¹(x)',
                    '30°', '45°', '60°', '90°', '180°', '270°', '360°', 'π/6', 'π/4', 'π/3', 'π/2', '2π',
                    'sin(2α)', 'cos(2α)', 'tan(2α)', 'sin(α ± β)', 'cos(α ± β)', '2 sin(α) cos(β)'
                ]
            },
            {
                subcat: 'algebra',
                group: 'Aljabar, Vektor, Matriks & Polinomial',
                items: [
                    'u⃗', 'v⃗', 'w⃗', 'a⃗', 'b⃗', 'c⃗', 'F⃗', 'î', 'ĵ', 'k̂', '|v⃗|', 'u⃗ · v⃗', 'u⃗ × v⃗',
                    '[a  b ; c  d]', '[a b c ; d e f ; g h i]', '[x ; y]', '[x ; y ; z]', 'det(A)', '|A|', 'A⁻¹', 'Aᵀ', 'I', '[1  0 ; 0  1]',
                    'f(x)', 'g(x)', 'h(x)', '(f ∘ g)(x)', '(g ∘ f)(x)', 'f⁻¹(x)', 'P(x)', 'x₁ + x₂ = -b/a', 'x₁ · x₂ = c/a', 'x₁ + x₂ + x₃ = -b/a'
                ]
            },
            {
                subcat: 'calculus',
                group: 'Kalkulus: Limit, Turunan & Integral SMA',
                items: [
                    'lim (x → 0)', 'lim (x → ∞)', 'lim (x → a)', 'lim (x → a⁺)', 'lim (x → a⁻)', 'lim (x → 0) (sin x)/x = 1', 'lim (x → 0) (tan x)/x = 1',
                    'dx', 'dy', 'dt', 'dy/dx', 'd²y/dx²', 'df/dx', "f'(x)", "f''(x)", "y'", "y''", '∂', '∂f/∂x', '∂f/∂y', '∇',
                    '∫ f(x) dx', '∫ₐᵇ f(x) dx', '∫₀¹', '∫₀^π', '∫ xⁿ dx', '∫ eˣ dx', '∫ sin(x) dx', '∫ cos(x) dx', '∫ 1/x dx',
                    '∬', '∭', '∮', '+ C', '∑', '∑ᵢ₌₁ⁿ', '∏', '∏ᵢ₌₁ⁿ', 'Δx', 'Δy', '∫ u dv = u·v - ∫ v du'
                ]
            },
            {
                subcat: 'logarithm',
                group: 'Logaritma & Eksponen SMA',
                items: ['log(x)', 'log₁₀(x)', 'ln(x)', '²log(x)', '³log(x)', '⁵log(x)', 'ᵃlog(b)', 'ᵃlog(b · c)', 'ᵃlog(b / c)', 'ᵃlog(bⁿ)', 'ᵃlog(a) = 1', 'ᵃlog(1) = 0', 'ᵃlog(b) = (ᶜlog b) / (ᶜlog a)', 'ᵃlog(b) · ᵇlog(c) = ᵃlog(c)', 'a^(ᵃlog b) = b', 'e', 'ln(e) = 1']
            },
            {
                subcat: 'statistics',
                group: 'Statistika, Peluang & Kombinatorika SMA',
                items: [
                    'x̄', 'x̃', 'Mo', 's', 's²', 'σ', 'σ²', 'Q₁', 'Q₂', 'Q₃', 'IQR', 'Dₖ', 'Pₖ',
                    'P(A)', "P(A')", 'P(A ∩ B)', 'P(A ∪ B)', 'P(A|B)', 'n!', 'P(n,r)', 'C(n,r)', 'nCr', 'nPr', 'n(S)', 'n(A)',
                    'z = (x - μ) / σ', 'μ', 'Σxᵢ', 'Σfᵢ·xᵢ', 'Σfᵢ', 'R = x_max - x_min', 'k = 1 + 3.3 log(n)', 'p = R / k', 'x̄ = (Σ fᵢ · xᵢ) / (Σ fᵢ)'
                ]
            },
            {
                subcat: 'logic',
                group: 'Himpunan, Notasi Angka & Logika Matematika',
                items: [
                    '∈', '∉', '⊂', '⊆', '⊄', '∪', '∩', '∅', 'U', "A'", 'Aᶜ', 'A - B', 'n(A)', 'ℝ', 'ℤ', 'ℕ', 'ℚ', 'ℂ',
                    '∧', '∨', '¬', '~', '⇒', '→', '⇔', '↔', '∴', '∵', '∀', '∃', '∄', 'p ∧ q', 'p ∨ q', 'p ⇒ q', 'p ⇔ q', '~p', 'p ⊻ q'
                ]
            },
            {
                subcat: 'greek',
                group: 'Huruf Yunani Lengkap (Sudut & Simbol Sains)',
                items: [
                    'α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ', 'ι', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ', 'ω',
                    'Γ', 'Δ', 'Θ', 'Λ', 'Ξ', 'Π', 'Σ', 'Φ', 'Ψ', 'Ω'
                ]
            },
            {
                subcat: 'intervals',
                group: 'Interval Bilangan, Domain & Garis Bilangan SMA',
                items: [
                    '[a, b]', '(a, b)', '[a, b)', '(a, b]', '(-∞, ∞)', '(-∞, a]', '[a, ∞)', 'x ∈ ℝ',
                    'a < x < b', 'a ≤ x ≤ b', 'x ≤ a atau x ≥ b', '{x | a ≤ x ≤ b, x ∈ ℝ}', '{x | x > 0, x ∈ ℝ}',
                    'Df = {x | x ≠ a, x ∈ ℝ}', 'Rf = {y | y ≥ 0, y ∈ ℝ}', 'ℝ⁺', 'ℝ⁻', 'ℤ⁺', 'ℤ⁻', 'ℕ₀'
                ]
            },
            {
                subcat: 'quadratics',
                group: 'Persamaan & Fungsi Kuadrat Parabola SMA',
                items: [
                    'ax² + bx + c = 0', 'D = b² - 4ac', 'x₁ = (-b + √D)/(2a)', 'x₂ = (-b - √D)/(2a)',
                    'x₁ + x₂ = -b/a', 'x₁ · x₂ = c/a', '|x₁ - x₂| = √D / a', 'x_p = -b / (2a)', 'y_p = -D / (4a)',
                    '(x_p, y_p)', 'y = a(x - x_p)² + y_p', 'y = a(x - x₁)(x - x₂)', 'D > 0 (2 akar real beda)',
                    'D = 0 (akar kembar)', 'D < 0 (tidak punya akar real)'
                ]
            },
            {
                subcat: 'complex',
                group: 'Bilangan Kompleks & Vektor Dimensi Tiga (3D)',
                items: [
                    'i = √(-1)', 'i² = -1', 'i³ = -i', 'i⁴ = 1', 'z = a + bi', 'z̄ = a - bi', '|z| = √(a² + b²)',
                    'arg(z) = θ', 'z = r(cos θ + i sin θ)', 'z = r·e^(iθ)', 'e^(iπ) + 1 = 0',
                    '|a⃗ + b⃗| = √(a² + b² + 2ab cos θ)', '|a⃗ - b⃗| = √(a² + b² - 2ab cos θ)',
                    'cos θ = (u⃗ · v⃗) / (|u⃗| |v⃗|)', 'proj_v u⃗ = ((u⃗ · v⃗)/|v⃗|²) v⃗', 's√2 (diagonal sisi)', 's√3 (diagonal ruang)'
                ]
            },
            {
                subcat: 'formulas',
                group: '⭐ Template Rumus Siap Pakai Matematika SMA',
                items: [
                    'x = (-b ± √(b² - 4ac)) / (2a)',
                    'D = b² - 4ac',
                    'a² + b² = c²',
                    'a / sin(A) = b / sin(B) = c / sin(C)',
                    'a² = b² + c² - 2bc · cos(A)',
                    'sin²(x) + cos²(x) = 1',
                    'tan(x) = sin(x) / cos(x)',
                    'sin(2x) = 2 sin(x) cos(x)',
                    'cos(2x) = cos²(x) - sin²(x) = 2cos²(x) - 1 = 1 - 2sin²(x)',
                    'tan(2x) = (2 tan x) / (1 - tan²x)',
                    'sin(α + β) = sin α cos β + cos α sin β',
                    'cos(α + β) = cos α cos β - sin α sin β',
                    'sin A + sin B = 2 sin ½(A+B) cos ½(A-B)',
                    'sin A - sin B = 2 cos ½(A+B) sin ½(A-B)',
                    'cos A + cos B = 2 cos ½(A+B) cos ½(A-B)',
                    'cos A - cos B = -2 sin ½(A+B) sin ½(A-B)',
                    'Luas △ = ½ · a · b · sin(C)',
                    'Uₙ = a + (n - 1)b',
                    'Sₙ = n/2 · (2a + (n - 1)b)',
                    'Uₙ = a · rⁿ⁻¹',
                    'Sₙ = a(1 - rⁿ) / (1 - r)',
                    'S_∞ = a / (1 - r)',
                    'y - y₁ = m(x - x₁)',
                    'm = (y₂ - y₁) / (x₂ - x₁)',
                    'd = √((x₂ - x₁)² + (y₂ - y₁)²)',
                    '(x - a)² + (y - b)² = r²',
                    'x² + y² + Ax + By + C = 0',
                    'C(n,r) = n! / (r! · (n - r)!)',
                    'P(n,r) = n! / (n - r)!',
                    "P(A') = 1 - P(A)",
                    "(u·v)' = u'·v + u·v'",
                    "(u/v)' = (u'·v - u·v') / v²",
                    "f'(x) = n · a · xⁿ⁻¹",
                    'lim_{x→c} f(x)/g(x) = lim_{x→c} f\'(x)/g\'(x) (L\'Hôpital)',
                    '∫ a·xⁿ dx = (a / (n + 1))·xⁿ⁺¹ + C',
                    'x̄ = (Σ fᵢ · xᵢ) / (Σ fᵢ)'
                ]
            }
        ],
        chemistry: [
            {
                subcat: 'reactions',
                group: 'Arah Panah & Tanda Reaksi Kimia',
                items: ['→', '⇌', '↔', '↑ (gas)', '↓ (endapan)', '⇄', 'Δ (pemanasan)', '+', '[Pt] (katalis)']
            },
            {
                subcat: 'compounds',
                group: 'Rumus Kimia Senyawa Populer SMA',
                items: ['H₂O', 'CO₂', 'O₂', 'N₂', 'H₂SO₄', 'HCl', 'NaOH', 'NaCl', 'CaCO₃', 'C₆H₁₂O₆', 'CH₄', 'NH₃', 'HNO₃', 'CH₃COOH', 'C₂H₅OH', 'Fe₂O₃', 'KMnO₄', 'H₂O₂', 'SO₂', 'NO₂']
            },
            {
                subcat: 'ions',
                group: 'Ion, Kation & Anion Muatan Listrik',
                items: ['H⁺', 'OH⁻', 'Na⁺', 'K⁺', 'Cl⁻', 'Ca²⁺', 'Mg²⁺', 'Ba²⁺', 'SO₄²⁻', 'NO₃⁻', 'CO₃²⁻', 'PO₄³⁻', 'Fe²⁺', 'Fe³⁺', 'Al³⁺', 'Cu²⁺', 'Zn²⁺', 'NH₄⁺', 'MnO₄⁻', 'Cr₂O₇²⁻']
            },
            {
                subcat: 'states',
                group: 'Wujud Zat Kimia (Fasa)',
                items: ['(s) padat', '(l) cair', '(g) gas', '(aq) larutan berair']
            },
            {
                subcat: 'thermochem',
                group: 'Termokimia & Stoikiometri',
                items: ['ΔH° = -... kJ/mol', 'ΔH°f', 'ΔH°c', 'ΔH°d', 'mol = gram / Mr', 'M = n / V', 'V = 22.4 L (STP)', 'P·V = n·R·T', 'N = mol · 6.02 × 10²³']
            },
            {
                subcat: 'acids',
                group: 'Kesetimbangan Asam - Basa',
                items: ['pH = -log[H⁺]', 'pOH = -log[OH⁻]', 'pH + pOH = 14', '[H⁺] = √(Ka · M)', '[OH⁻] = √(Kb · M)', 'Ka = ...', 'Kb = ...', 'Kw = 10⁻¹⁴']
            },
            {
                subcat: 'indices',
                group: 'Indeks Angka & Muatan Kimia',
                items: ['₀', '₁', '₂', '₃', '₄', '₅', '₆', '₇', '₈', '₉', '⁺', '⁻', '²⁺', '²⁻', '³⁺', '³⁻']
            }
        ],
        physics: [
            {
                subcat: 'units',
                group: 'Besaran & Satuan Fisika Standar (SI)',
                items: ['Ω (Ohm)', 'μ (Mikro)', 'λ (Panjang Gelombang)', 'ρ (Massa Jenis)', 'ν (Frekuensi)', 'τ (Torsi)', 'ω (Kecepatan Sudut)', '°C', 'K', 'Hz', 'Pa', 'J', 'W', 'N', 'C', 'V', 'T (Tesla)', 'Wb (Weber)', 'H (Henry)', 'F (Farad)']
            },
            {
                subcat: 'vectors',
                group: 'Simbol Vektor & Diferensial Fisika',
                items: ['v⃗', 'a⃗', 'F⃗', 'p⃗', 'E⃗', 'B⃗', 'î', 'ĵ', 'k̂', 'Δt', 'Δx', 'Δv', 'ΔE', 'Δp', '|v⃗|']
            },
            {
                subcat: 'mechanics',
                group: 'Rumus Mekanika & Kinematika SMA',
                items: ['ΣF = m·a', 'w = m·g', 'v = v₀ + a·t', 's = v₀·t + ½·a·t²', 'v² = v₀² + 2·a·s', 'W = F·s·cos(θ)', 'Ek = ½·m·v²', 'Ep = m·g·h', 'Em = Ek + Ep', 'p = m·v', 'I = F·Δt = Δp', 'f_ges = μ·N']
            },
            {
                subcat: 'fluids',
                group: 'Fluida & Termodinamika',
                items: ['P = F / A', 'P_h = ρ·g·h', 'F_a = ρ·g·V_bf', 'Q = m·c·ΔT', 'Q = m·L', 'PV = n·R·T', 'W = P·ΔV']
            },
            {
                subcat: 'electricity',
                group: 'Listrik, Magnet & Gelombang',
                items: ['V = I·R', 'P = V·I = I²·R', 'F = k·|q₁·q₂| / r²', 'E = F / q', 'F = B·I·L·sin(θ)', 'T = 1 / f', 'v = λ·f', 'I = P / (4π·r²)', 'TI = 10·log(I / I₀)']
            },
            {
                subcat: 'constants',
                group: 'Konstanta Alam & Fisika Modern',
                items: ['g = 9.8 m/s²', 'c = 3 × 10⁸ m/s', 'h = 6.63 × 10⁻³⁴ J·s', 'e = 1.6 × 10⁻¹⁹ C', 'G = 6.67 × 10⁻¹¹ N·m²/kg²', 'k = 9 × 10⁹ N·m²/C²', 'E = m·c²', 'E = h·f']
            }
        ]
    },

    openSymbolPalette: function(targetId, category) {
        this.openModal('modal-rich-symbol-palette', targetId);
        this.activeCategory = category || 'math';
        this.activeMathSubCat = 'all';
        document.getElementById('symbol-search-input').value = '';
        this.renderSymbolCategory(this.activeCategory);
    },

    renderSymbolCategory: function(cat) {
        this.activeCategory = cat;

        ['math', 'chem', 'phys'].forEach(k => {
            const btn = document.getElementById('tab-sym-' + k);
            if (btn) {
                btn.classList.remove('bg-white', 'dark:bg-slate-900', 'text-indigo-600', 'dark:text-indigo-400', 'shadow-2xs');
                btn.classList.add('text-slate-600', 'dark:text-slate-400');
            }
        });
        const activeBtn = document.getElementById('tab-sym-' + (cat === 'chemistry' ? 'chem' : (cat === 'physics' ? 'phys' : 'math')));
        if (activeBtn) {
            activeBtn.classList.add('bg-white', 'dark:bg-slate-900', 'text-indigo-600', 'dark:text-indigo-400', 'shadow-2xs');
            activeBtn.classList.remove('text-slate-600', 'dark:text-slate-400');
        }

        // Render Sub-kategori pills untuk Matematika
        const subCatBar = document.getElementById('math-subcategories-bar');
        if (subCatBar) {
            if (cat === 'math') {
                subCatBar.classList.remove('hidden');
                const mathPills = [
                    { id: 'all', label: 'Semua' },
                    { id: 'arithmetic', label: 'Aritmatika' },
                    { id: 'powers', label: 'Pangkat' },
                    { id: 'indices', label: 'Indeks' },
                    { id: 'roots', label: 'Akar' },
                    { id: 'fractions', label: 'Pecahan' },
                    { id: 'intervals', label: 'Interval & Himpunan' },
                    { id: 'quadratics', label: 'Pers. Kuadrat' },
                    { id: 'complex', label: 'Kompleks & Vektor' },
                    { id: 'trig', label: 'Trigonometri' },
                    { id: 'algebra', label: 'Aljabar & Matriks' },
                    { id: 'calculus', label: 'Kalkulus' },
                    { id: 'logarithm', label: 'Logaritma' },
                    { id: 'statistics', label: 'Statistika & Peluang' },
                    { id: 'logic', label: 'Logika & Notasi' },
                    { id: 'greek', label: 'Yunani' },
                    { id: 'formulas', label: '⭐ Rumus SMA' }
                ];

                let pillHtml = '';
                mathPills.forEach(p => {
                    const isSelected = this.activeMathSubCat === p.id;
                    pillHtml += '<button type="button" onclick="window.RichEditor.setMathSubCategory(\'' + p.id + '\')" class="px-2.5 py-1 rounded-full shrink-0 font-bold transition ' +
                        (isSelected ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200') + '">' +
                        p.label +
                        '</button>';
                });
                subCatBar.innerHTML = pillHtml;
            } else {
                subCatBar.classList.add('hidden');
            }
        }

        this.filterSymbols(document.getElementById('symbol-search-input')?.value || '');
    },

    setMathSubCategory: function(subCat) {
        this.activeMathSubCat = subCat;
        this.renderSymbolCategory('math');
    },

    filterSymbols: function(query) {
        const q = (query || '').toLowerCase().trim();
        const container = document.getElementById('symbol-grid-container');
        if (!container) return;

        const allGroups = this.symbolsData[this.activeCategory] || this.symbolsData.math;

        let filteredGroups = allGroups;

        // Filter subkategori jika aktif
        if (this.activeCategory === 'math' && this.activeMathSubCat !== 'all' && !q) {
            filteredGroups = filteredGroups.filter(g => g.subcat === this.activeMathSubCat);
        }

        // Filter kata kunci pencarian
        if (q) {
            filteredGroups = filteredGroups.map(g => {
                const groupMatches = g.group.toLowerCase().includes(q);
                const matchingItems = g.items.filter(item => item.toLowerCase().includes(q) || groupMatches);
                return {
                    ...g,
                    items: matchingItems
                };
            }).filter(g => g.items.length > 0);
        }

        if (filteredGroups.length === 0) {
            container.innerHTML = '<div class="p-8 text-center text-slate-400">' +
                '<span class="text-xl block mb-1">🔍</span>' +
                'Tidak ada simbol matematika atau rumus yang cocok dengan kata kunci "<strong>' + query + '</strong>".' +
                '</div>';
            return;
        }

        let html = '';
        filteredGroups.forEach(g => {
            html += '<div class="space-y-1.5 p-3 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60">';
            html += '<div class="font-extrabold uppercase tracking-wider text-[11px] text-indigo-600 dark:text-indigo-400 flex items-center justify-between">';
            html += '<span>' + g.group + '</span>';
            html += '<span class="text-[10px] text-slate-400 lowercase font-normal">' + g.items.length + ' item</span>';
            html += '</div>';
            html += '<div class="flex flex-wrap gap-1.5 pt-1">';
            g.items.forEach(item => {
                const isLong = item.length > 10;
                const btnClass = isLong
                    ? 'px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 text-slate-800 dark:text-slate-100 hover:text-indigo-600 font-mono font-bold text-xs border border-slate-200 dark:border-slate-700 shadow-2xs transition flex items-center gap-1.5'
                    : 'px-2.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 text-slate-800 dark:text-slate-100 hover:text-indigo-600 font-bold text-xs border border-slate-200 dark:border-slate-700 shadow-2xs transition min-w-[34px] text-center';

                html += '<button type="button" onclick="window.RichEditor.insertAtCursor(window.RichEditor.activeTargetId, \'' + item.replace(/\\/g, '\\\\').replace(/'/g, "\\'") + '\')" class="' + btnClass + '" title="Klik untuk menyisipkan">';
                html += item;
                html += '</button>';
            });
            html += '</div></div>';
        });

        container.innerHTML = html;
    },

    // --- 3. Arabic Keyboard Handlers ---
    openArabicKeyboard: function(targetId) {
        this.openModal('modal-rich-arabic-keyboard', targetId);
    },

    typeArabicChar: function(char) {
        const preview = document.getElementById('arabic-live-preview');
        if (preview) {
            const start = preview.selectionStart || 0;
            const end = preview.selectionEnd || 0;
            preview.value = preview.value.substring(0, start) + char + preview.value.substring(end);
            preview.focus();
            preview.selectionStart = preview.selectionEnd = start + char.length;
        }
        this.insertAtCursor(this.activeTargetId, char);
    },

    backspaceArabic: function() {
        const preview = document.getElementById('arabic-live-preview');
        if (!preview || !preview.value) return;
        preview.value = preview.value.slice(0, -1);
    },

    insertArabicPreviewToTarget: function() {
        const preview = document.getElementById('arabic-live-preview');
        if (preview && preview.value) {
            this.insertAtCursor(this.activeTargetId, preview.value);
            this.closeModal('modal-rich-arabic-keyboard');
        }
    },

    // --- 4. Quran Ayah Picker Handlers (114 Surat Lengkap) ---
    openQuranPicker: function(targetId) {
        this.openModal('modal-rich-quran-picker', targetId);
        this.loadQuranAyahPreview();
    },

    loadQuranAyahPreview: function() {
        const surah = document.getElementById('quran-picker-surah').value;
        const ayah = document.getElementById('quran-picker-ayah').value.trim() || '1';

        fetch('{{ route("lms.quran.ayah") }}?surah=' + encodeURIComponent(surah) + '&ayah=' + encodeURIComponent(ayah), {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) {
                this.currentQuranData = res.data;
                document.getElementById('quran-preview-ref').innerText = res.data.reference;
                document.getElementById('quran-preview-arabic').innerText = res.data.arabic;
                document.getElementById('quran-preview-trans').innerText = res.data.translation;
            }
        })
        .catch(err => console.error(err));
    },

    insertQuranAyahToTarget: function() {
        if (!this.currentQuranData) return;

        const withTrans = document.getElementById('quran-include-translation').checked;
        const d = this.currentQuranData;
        const targetEl = document.getElementById(this.activeTargetId);
        const isInput = targetEl && targetEl.tagName === 'INPUT';

        let formattedText = '';
        if (isInput) {
            formattedText = d.arabic + (withTrans ? ' (' + d.translation + ')' : '');
        } else {
            formattedText = '\n\n' +
                '<div class="quran-quote-box p-4 my-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-slate-800 dark:text-slate-100" dir="rtl">\n' +
                '  <div class="font-arabic text-xl sm:text-2xl leading-loose font-bold text-emerald-950 dark:text-emerald-100 mb-2">' + d.arabic + '</div>\n' +
                (withTrans ? '  <div class="text-xs text-slate-600 dark:text-slate-300 font-sans italic" dir="ltr"><strong>Artinya:</strong> "' + d.translation + '" (' + d.reference + ')</div>\n' : '  <div class="text-[11px] text-emerald-700 dark:text-emerald-400 font-sans" dir="ltr">(' + d.reference + ')</div>\n') +
                '</div>\n\n';
        }

        this.insertAtCursor(this.activeTargetId, formattedText);
        this.closeModal('modal-rich-quran-picker');
    }
};

// Auto-scan and initialize all rich editors on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.rich-editor-toolbar').forEach(tb => {
        const btn = tb.querySelector('[id^="btn-mode-visual-"]');
        if (btn) {
            const targetSlug = btn.id.replace('btn-mode-visual-', '');
            document.querySelectorAll('textarea').forEach(ta => {
                const slug = ta.id.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                if (slug === targetSlug) {
                    window.RichEditor.initVisualCanvas(ta.id);
                }
            });
        }
    });
});
</script>
