@php
    $target = $targetId ?? 'active_option';
@endphp
<div class="inline-flex items-center gap-1 flex-wrap">
    <button type="button" onclick="window.RichEditor.openSymbolPalette('{{ $target }}', 'math')" class="px-1.5 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 font-bold text-[10px] border border-indigo-200 dark:border-indigo-800 transition" title="Sisipkan Simbol/Rumus Matematika">
        📐 Math
    </button>
    <button type="button" onclick="window.RichEditor.openSymbolPalette('{{ $target }}', 'chemistry')" class="px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100 text-amber-700 dark:text-amber-300 font-bold text-[10px] border border-amber-200 dark:border-amber-800 transition" title="Sisipkan Simbol/Reaksi Kimia">
        🧪 Kimia
    </button>
    <button type="button" onclick="window.RichEditor.openSymbolPalette('{{ $target }}', 'physics')" class="px-1.5 py-0.5 rounded-md bg-cyan-50 dark:bg-cyan-950/60 hover:bg-cyan-100 text-cyan-700 dark:text-cyan-300 font-bold text-[10px] border border-cyan-200 dark:border-cyan-800 transition" title="Sisipkan Simbol/Satuan Fisika">
        ⚡ Fisika
    </button>
    <button type="button" onclick="window.RichEditor.openArabicKeyboard('{{ $target }}')" class="px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-800 dark:text-emerald-200 font-bold text-[10px] border border-emerald-200 dark:border-emerald-800 transition" title="Ketik Huruf Hijaiyah / Arab">
        🇸🇦 Arab
    </button>
    <button type="button" onclick="window.RichEditor.openQuranPicker('{{ $target }}')" class="px-1.5 py-0.5 rounded-md bg-teal-50 dark:bg-teal-950/60 hover:bg-teal-100 text-teal-800 dark:text-teal-200 font-bold text-[10px] border border-teal-200 dark:border-teal-800 transition" title="Ambil Ayat Al-Qur'an Lengkap">
        📖 Ayat
    </button>
    <button type="button" onclick="window.RichEditor.openImageModal('{{ $target }}')" class="px-1.5 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/60 hover:bg-purple-100 text-purple-700 dark:text-purple-300 font-bold text-[10px] border border-purple-200 dark:border-purple-800 transition" title="Sisipkan Gambar ke Opsi">
        🖼️ Gambar
    </button>
    <button type="button" onclick="window.RichEditor.insertAtCursor('{{ $target }}', '²')" class="px-1 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold text-[10px] border border-slate-200 dark:border-slate-600 transition" title="Pangkat / Kuadrat (x²)">
        X²
    </button>
    <button type="button" onclick="window.RichEditor.insertAtCursor('{{ $target }}', '₂')" class="px-1 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold text-[10px] border border-slate-200 dark:border-slate-600 transition" title="Indeks (x₂)">
        X₂
    </button>
</div>

