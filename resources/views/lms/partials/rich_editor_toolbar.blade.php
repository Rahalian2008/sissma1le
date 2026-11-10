@php
    $editorTarget = $targetId ?? 'question_text';
    $targetSlug = Str::slug($editorTarget);
    $toolbarId = 'rich-toolbar-' . $targetSlug . '-' . Str::random(4);
@endphp

<div id="{{ $toolbarId }}" class="rich-editor-toolbar bg-slate-100 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-t-2xl text-xs select-none divide-y divide-slate-200/70 dark:divide-slate-700/70">
    <!-- BARIS 1: MODE TABS (VISUAL WORD-LIKE VS KODE HTML VS PRATINJAU SISWA) & BADGE FITUR LENGKAP -->
    <div class="flex flex-wrap items-center justify-between gap-2 p-2">
        <!-- Tab Mode Switcher (Visual Seperti Word adalah Default) -->
        <div class="flex items-center gap-1 bg-white dark:bg-slate-900 p-1 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
            <button type="button" onclick="window.RichEditor.toggleMode('{{ $editorTarget }}', 'visual')" id="btn-mode-visual-{{ $targetSlug }}" class="btn-mode-tab px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 text-white shadow-xs transition flex items-center gap-1.5 cursor-pointer" title="Mode Menulis Visual interaktif seperti Microsoft Word (Gambar, Rumus, dan Teks Arab tampil langsung)">
                <span>📄 Visual (Seperti Word)</span>
            </button>
            <button type="button" onclick="window.RichEditor.toggleMode('{{ $editorTarget }}', 'html')" id="btn-mode-html-{{ $targetSlug }}" class="btn-mode-tab px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-1.5 cursor-pointer" title="Mode Teks & Kode HTML untuk pengeditan tag manual">
                <span>&lt;/&gt; Kode HTML</span>
            </button>
            <button type="button" onclick="window.RichEditor.toggleMode('{{ $editorTarget }}', 'preview')" id="btn-mode-prev-{{ $targetSlug }}" class="btn-mode-tab px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-1.5 cursor-pointer" title="Pratinjau tampilan soal persis seperti di layar siswa">
                <span>👁️ Pratinjau Siswa</span>
            </button>
        </div>

        <!-- Indikator Fitur Word-Like -->
        <div class="hidden sm:flex items-center gap-2 text-[11px] font-bold text-slate-500 dark:text-slate-400">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                <span>✨ Editor Interaktif Seperti Word: Gambar Langsung Tampil • Math • Kimia • Fisika • Arab • Qur'an</span>
            </span>
        </div>
    </div>

    <!-- BARIS 2: PALET ALAT BANTU PENULISAN & SAINS -->
    <div class="flex flex-wrap items-center justify-between gap-1.5 p-2 bg-slate-50/70 dark:bg-slate-800/50">
        <!-- Grup Alat Kiri -->
        <div class="flex flex-wrap items-center gap-1">
            <!-- 1. Format Teks Dasar (Word-Like) -->
            <div class="flex items-center gap-0.5 border-r border-slate-300 dark:border-slate-600 pr-1 mr-0.5">
                <button type="button" onclick="window.RichEditor.wrapText('{{ $editorTarget }}', '**', '**')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 font-black text-slate-800 dark:text-slate-100 transition" title="Tebal (Bold)">
                    <span class="font-bold">B</span>
                </button>
                <button type="button" onclick="window.RichEditor.wrapText('{{ $editorTarget }}', '*', '*')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 italic font-serif text-slate-800 dark:text-slate-100 transition" title="Miring (Italic)">
                    <span class="italic font-bold">I</span>
                </button>
                <button type="button" onclick="window.RichEditor.wrapText('{{ $editorTarget }}', '<u>', '</u>')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 underline text-slate-800 dark:text-slate-100 transition" title="Garis Bawah (Underline)">
                    <span class="underline font-bold">U</span>
                </button>
                <button type="button" onclick="window.RichEditor.insertList('{{ $editorTarget }}', 'unordered')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition" title="Daftar Butir (Bullet List)">
                    •—
                </button>
                <button type="button" onclick="window.RichEditor.insertList('{{ $editorTarget }}', 'ordered')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold transition" title="Daftar Nomor (Numbered List)">
                    1.
                </button>
                <button type="button" onclick="window.RichEditor.insertBlockquote('{{ $editorTarget }}')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-serif font-black transition" title="Kutipan / Kotak Stimulus Soal Cerita">
                    “ ”
                </button>
                <button type="button" onclick="window.RichEditor.insertTable('{{ $editorTarget }}')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold flex items-center gap-0.5 transition" title="Sisipkan Tabel Data Soal (2 Kolom x 3 Baris)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </button>
            </div>

            <!-- 2. Tombol Sisipkan Gambar (Langsung Visual) -->
            <div class="flex items-center gap-0.5 border-r border-slate-300 dark:border-slate-600 pr-1 mr-0.5">
                <button type="button" onclick="window.RichEditor.openImageModal('{{ $editorTarget }}')" class="px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold flex items-center gap-1 shadow-2xs transition" title="Sisipkan Gambar (Unggah Berkas PNG/JPG atau Tautan URL &mdash; Gambar Langsung Tampil Visual Seperti Word)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>🖼️ Gambar</span>
                </button>
            </div>

            <!-- 3. Tombol Palet Sains Lengkap (Matematika Lengkap, Kimia, Fisika) -->
            <div class="flex items-center gap-1 border-r border-slate-300 dark:border-slate-600 pr-1 mr-0.5">
                <button type="button" onclick="window.RichEditor.openSymbolPalette('{{ $editorTarget }}', 'math')" class="px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1 shadow-2xs transition" title="Palet Simbol & Rumus Matematika Lengkap SMA (Aritmatika, Pangkat, Indeks, Akar, Pecahan, Trigonometri, Aljabar, Matriks, Kalkulus, Logaritma, Statistika, Logika, Yunani, Rumus Siap Pakai)">
                    <span>📐 Math Lengkap</span>
                </button>
                <button type="button" onclick="window.RichEditor.openSymbolPalette('{{ $editorTarget }}', 'chemistry')" class="px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-amber-500 text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1 shadow-2xs transition" title="Simbol & Notasi Kimia (Reaksi, Senyawa Populer, Ion Muatan, Fasa Zat, Termokimia, Asam Basa)">
                    <span>🧪 Kimia</span>
                </button>
                <button type="button" onclick="window.RichEditor.openSymbolPalette('{{ $editorTarget }}', 'physics')" class="px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-cyan-500 text-cyan-600 dark:text-cyan-400 font-bold flex items-center gap-1 shadow-2xs transition" title="Simbol & Satuan Fisika (Besaran SI Ohm/Mikro/Lambda, Vektor, Rumus Kinematika, Mekanika, Listrik)">
                    <span>⚡ Fisika</span>
                </button>
            </div>

            <!-- 4. Tombol Huruf Arab & Al-Qur'an Lengkap -->
            <div class="flex items-center gap-1 border-r border-slate-300 dark:border-slate-600 pr-1 mr-0.5">
                <button type="button" onclick="window.RichEditor.openArabicKeyboard('{{ $editorTarget }}')" class="px-2.5 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 font-bold flex items-center gap-1 shadow-2xs transition" title="Virtual Keyboard Huruf Arab Hijaiyah & Tanda Harakat">
                    <span>🇸🇦 Huruf Arab</span>
                </button>
                <button type="button" onclick="window.RichEditor.openQuranPicker('{{ $editorTarget }}')" class="px-2.5 py-1 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-extrabold flex items-center gap-1 shadow-xs transition" title="Ambil Ayat Al-Qur'an (114 Surat Lengkap Teks Arab Berharakat & Terjemahan Kemenag RI)">
                    <span>📖 Ambil Ayat Al-Qur'an</span>
                </button>
            </div>

            <!-- 5. Subscript / Superscript Cepat -->
            <div class="flex items-center gap-0.5">
                <button type="button" onclick="window.RichEditor.wrapText('{{ $editorTarget }}', '<sup>', '</sup>')" class="px-1.5 py-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[11px] transition" title="Superscript (Pangkat / Kuadrat: x²)">
                    X²
                </button>
                <button type="button" onclick="window.RichEditor.wrapText('{{ $editorTarget }}', '<sub>', '</sub>')" class="px-1.5 py-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[11px] transition" title="Subscript (Indeks Kimia / Variabel: x₂)">
                    X₂
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Inisialisasi otomatis editor visual Word-like saat toolbar dimuat
    if (window.RichEditor && typeof window.RichEditor.initVisualCanvas === 'function') {
        window.RichEditor.initVisualCanvas('{{ $editorTarget }}');
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            if (window.RichEditor && typeof window.RichEditor.initVisualCanvas === 'function') {
                window.RichEditor.initVisualCanvas('{{ $editorTarget }}');
            }
        });
    }
</script>
