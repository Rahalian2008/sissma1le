@extends('layouts.app')

@section('title', 'Input Prestasi Siswa')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Catat Prestasi Siswa</h2>
                <p class="text-xs text-slate-500">Pilih butir prestasi sesuai kategori R1 s/d R10 Tata Tertib SMAN 1 Lengkong</p>
            </div>
            <a href="{{ route('achievements.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">← Kembali</a>
        </div>

        <form action="{{ route('achievements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- 1. Siswa (Searchable Combobox + Inisial) -->
            <div class="relative" id="student-combobox">
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">
                    Pilih Siswa * 
                    <span class="text-[10px] font-normal text-slate-400 ml-1">
                        (Cari dengan ketik nama, NIS, kelas, atau inisial misal: AD, MRP)
                    </span>
                </label>
                <input type="hidden" name="student_id" id="student_id" required value="{{ old('student_id') }}">
                
                <!-- Selected Badge Preview -->
                <div id="student-selected-preview" class="hidden mb-2 p-3 bg-indigo-50/90 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-2xl flex items-center justify-between transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <div id="student-preview-avatar" class="w-9 h-9 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                            -
                        </div>
                        <div class="min-w-0">
                            <div id="student-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-xs sm:text-sm"></div>
                            <div id="student-preview-meta" class="text-[11px] text-indigo-700 dark:text-indigo-300 truncate"></div>
                        </div>
                    </div>
                    <button type="button" onclick="clearStudentSelection()" class="px-2.5 py-1 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/50 rounded-lg transition shrink-0 cursor-pointer">
                        Ganti Siswa ✕
                    </button>
                </div>

                <!-- Search Input with Icon -->
                <div class="relative" id="student-search-wrapper">
                    <input type="text" id="student_search_input"
                        placeholder="🔍 Ketik nama, NIS, kelas, atau inisial siswa (misal: MR, AF, Dani, XI-IPA)..."
                        autocomplete="off"
                        onfocus="openStudentDropdown()"
                        oninput="filterStudents(this.value)"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button type="button" id="student-search-clear" onclick="resetStudentSearch()" class="hidden absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        ✕
                    </button>
                </div>

                <!-- Quick Alphabet Initials Filter Bar (A-Z Chips) -->
                <div class="mt-2 flex items-center gap-1 overflow-x-auto pb-1.5 pt-0.5 text-xs no-scrollbar" id="student-initials-bar">
                    <button type="button" onclick="filterStudentByInitial('ALL')"
                        class="ach-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs" data-letter="ALL">
                        Semua
                    </button>
                    @foreach(range('A', 'Z') as $char)
                        <button type="button" onclick="filterStudentByInitial('{{ $char }}')"
                            class="ach-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700" data-letter="{{ $char }}">
                            {{ $char }}
                        </button>
                    @endforeach
                </div>

                <!-- Dropdown Result List -->
                <div id="student-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-64 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl z-50 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($students as $s)
                        @php
                            $words = explode(' ', trim($s->name));
                            $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                            $firstChar = strtoupper(substr(trim($s->name), 0, 1));
                        @endphp
                        <div class="student-item px-3.5 py-2.5 hover:bg-indigo-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition"
                             data-id="{{ $s->id }}"
                             data-name="{{ $s->name }}"
                             data-nis="{{ $s->nis }}"
                             data-class="{{ $s->schoolClass?->name ?? 'Umum' }}"
                             data-initials="{{ strtolower($initials) }}"
                             data-firstletter="{{ $firstChar }}"
                             onclick="selectStudent('{{ $s->id }}', '{{ addslashes($s->name) }}', 'Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • NIS: {{ $s->nis }}', '{{ strtoupper(substr($initials, 0, 2)) }}')">
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($initials, 0, 2)) }}
                                </span>
                                <div class="truncate">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 truncate text-xs">{{ $s->name }}</div>
                                    <div class="text-[10px] text-slate-400">Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • NIS: {{ $s->nis }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono shrink-0 font-bold">
                                [{{ strtoupper($initials) }}]
                            </span>
                        </div>
                    @endforeach
                    <div id="student-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                        Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut.
                    </div>
                </div>
            </div>

            <!-- 2. Kategori Prestasi (R1 - R10) (Searchable Combobox) -->
            <div class="relative" id="category-combobox">
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">
                    Kategori Prestasi (R1 - R10) *
                    <span class="text-[10px] font-normal text-slate-400 ml-1">
                        (Ketik kode misal R1 atau nama kategori)
                    </span>
                </label>
                <input type="hidden" name="category_id" id="category-id-hidden" required value="{{ old('category_id') }}">

                <!-- Selected Category Preview -->
                <div id="category-selected-preview" class="hidden mb-2 p-3 bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-2xl flex items-center justify-between transition">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span id="category-preview-code" class="px-2.5 py-1 bg-amber-500 text-slate-950 font-black text-xs rounded-lg shrink-0">
                            R1
                        </span>
                        <div id="category-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-xs sm:text-sm"></div>
                    </div>
                    <button type="button" onclick="clearCategorySelection()" class="px-2.5 py-1 text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-lg transition shrink-0 cursor-pointer">
                        Ganti ✕
                    </button>
                </div>

                <!-- Search Input for Category -->
                <div class="relative" id="category-search-wrapper">
                    <input type="text" id="category_search_input"
                        placeholder="🔍 Ketik kode (misal: R1, R5) atau inisial/nama kategori prestasi..."
                        autocomplete="off"
                        onfocus="openCategoryDropdown()"
                        oninput="filterCategories(this.value)"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Dropdown Result List for Categories -->
                <div id="category-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl z-40 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($categories as $cat)
                        @php
                            $catInitials = collect(explode(' ', $cat->name))->map(fn($w) => substr($w, 0, 1))->join('');
                        @endphp
                        <div class="category-item px-3.5 py-2.5 hover:bg-amber-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition"
                             data-id="{{ $cat->id }}"
                             data-code="{{ $cat->code }}"
                             data-name="{{ $cat->name }}"
                             data-initials="{{ strtolower($catInitials) }}"
                             onclick="selectCategory('{{ $cat->id }}', '{{ $cat->code }}', '{{ addslashes($cat->name) }}')">
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <span class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 font-extrabold text-xs shrink-0">
                                    {{ $cat->code }}
                                </span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 text-xs truncate">
                                    {{ $cat->name }}
                                </span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono shrink-0">
                                [{{ strtoupper($catInitials) }}] • {{ $cat->items->count() }} Butir
                            </span>
                        </div>
                    @endforeach
                    <div id="category-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                        Tidak ada kategori yang cocok.
                    </div>
                </div>
            </div>

            <!-- 3. Butir Prestasi Spesifik (Searchable Combobox) -->
            <div class="relative" id="item-combobox">
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">
                    Butir Master Prestasi (Otomatis Menyesuaikan Poin)
                    <span class="text-[10px] font-normal text-slate-400 ml-1">
                        (Ketik kode misal R1.1, nama lomba, kata kunci, atau inisial)
                    </span>
                </label>
                <input type="hidden" name="item_id" id="item-id-hidden" value="{{ old('item_id') }}">

                <!-- Selected Item Preview -->
                <div id="item-selected-preview" class="hidden mb-2 p-3 bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center justify-between transition">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span id="item-preview-code" class="px-2.5 py-1 bg-emerald-500 text-slate-950 font-black text-xs rounded-lg shrink-0">
                            -
                        </span>
                        <div class="min-w-0">
                            <div id="item-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-xs"></div>
                            <div id="item-preview-meta" class="text-[11px] text-emerald-700 dark:text-emerald-300"></div>
                        </div>
                    </div>
                    <button type="button" onclick="clearItemSelection()" class="px-2.5 py-1 text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-lg transition shrink-0 cursor-pointer">
                        Ganti ✕
                    </button>
                </div>

                <!-- Search Input for Item -->
                <div class="relative" id="item-search-wrapper">
                    <input type="text" id="item_search_input"
                        placeholder="🔍 Ketik kode butir (misal: R1.1, R2.2), kata kunci, atau inisial butir..."
                        autocomplete="off"
                        onfocus="openItemDropdown()"
                        oninput="filterMasterItems(this.value)"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Dropdown Result List for Items -->
                <div id="item-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl z-30 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($categories as $cat)
                        @foreach($cat->items as $item)
                            @php
                                $itemInitials = collect(explode(' ', $item->name))->map(fn($w) => substr($w, 0, 1))->join('');
                            @endphp
                            <div class="master-item-row px-3.5 py-2.5 hover:bg-emerald-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition"
                                 data-id="{{ $item->id }}"
                                 data-category="{{ $cat->id }}"
                                 data-cat-code="{{ $cat->code }}"
                                 data-cat-name="{{ $cat->name }}"
                                 data-code="{{ $item->code }}"
                                 data-name="{{ $item->name }}"
                                 data-points="{{ $item->default_points }}"
                                 data-level="{{ $item->level ?? 'KABUPATEN' }}"
                                 data-initials="{{ strtolower($itemInitials) }}"
                                 onclick="selectMasterItem('{{ $item->id }}', '{{ $item->code }}', '{{ addslashes($item->name) }}', '{{ $item->default_points }}', '{{ $item->level ?? 'KABUPATEN' }}', '{{ $cat->id }}', '{{ $cat->code }}', '{{ addslashes($cat->name) }}')">
                                <div class="flex items-center gap-2.5 truncate mr-2">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono text-[10px] font-bold shrink-0">
                                        [{{ $item->code }}]
                                    </span>
                                    <div class="truncate">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 text-xs truncate">{{ $item->name }}</div>
                                        <div class="text-[10px] text-slate-400 truncate">{{ $cat->code }} - {{ $cat->name }}</div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 font-black text-[10px] shrink-0">
                                    +{{ $item->default_points }} Poin
                                </span>
                            </div>
                        @endforeach
                    @endforeach
                    <div id="item-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                        Tidak ada butir prestasi yang cocok.
                    </div>
                </div>
            </div>

            <!-- Judul Prestasi -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Judul / Uraian Capaian Prestasi *</label>
                <input type="text" name="title" id="title-input" required placeholder="Contoh: Juara 1 Lomba Karya Tulis Ilmiah Tingkat Kabupaten"
                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-xs text-slate-800 dark:text-slate-100">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Tingkat Prestasi *</label>
                    <select name="level" id="level-select" required class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-xs text-slate-800 dark:text-slate-100">
                        <option value="SEKOLAH">SEKOLAH</option>
                        <option value="KECAMATAN">KECAMATAN</option>
                        <option value="KABUPATEN" selected>KABUPATEN</option>
                        <option value="PROVINSI">PROVINSI</option>
                        <option value="NASIONAL">NASIONAL</option>
                        <option value="INTERNASIONAL">INTERNASIONAL</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Nilai Poin *</label>
                    <input type="number" name="points" id="points-input" value="25" min="1" max="500" required
                        class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 font-bold text-amber-600 dark:text-amber-400 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Tanggal Kegiatan *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-xs text-slate-800 dark:text-slate-100">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Keterangan Tambahan / Refleksi</label>
                <textarea name="description" rows="3" placeholder="Informasi pendukung, penyelenggara perlombaan, dll..."
                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-xs text-slate-800 dark:text-slate-100"></textarea>
            </div>

            <!-- Upload Sertifikat Bukti -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Upload Bukti Sertifikat / Foto Piagam (PDF / JPG / PNG)</label>
                <input type="file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 text-xs text-slate-600 dark:text-slate-300">
                <p class="text-[10px] text-slate-400 mt-1">Maksimal ukuran file: 5MB</p>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <a href="{{ route('achievements.index') }}" class="px-5 py-2.5 font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Simpan Prestasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- 1. STUDENT SEARCH & INITIALS ---
    let activeStudentInitial = 'ALL';

    function openStudentDropdown() {
        document.getElementById('student-dropdown').classList.remove('hidden');
    }

    function closeStudentDropdown() {
        document.getElementById('student-dropdown').classList.add('hidden');
    }

    function filterStudentByInitial(letter) {
        activeStudentInitial = letter;
        document.querySelectorAll('.ach-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === letter) {
                btn.className = 'ach-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
            } else {
                btn.className = 'ach-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700';
            }
        });
        openStudentDropdown();
        applyStudentFilters();
    }

    function filterStudents(query) {
        openStudentDropdown();
        applyStudentFilters();
    }

    function applyStudentFilters() {
        const input = document.getElementById('student_search_input');
        const q = (input?.value || '').trim().toLowerCase();
        const items = document.querySelectorAll('.student-item');
        let visibleCount = 0;

        const clearBtn = document.getElementById('student-search-clear');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', q.length === 0);
        }

        items.forEach(el => {
            const name = (el.getAttribute('data-name') || '').toLowerCase();
            const nis = (el.getAttribute('data-nis') || '').toLowerCase();
            const cls = (el.getAttribute('data-class') || '').toLowerCase();
            const initials = (el.getAttribute('data-initials') || '').toLowerCase();
            const firstLetter = (el.getAttribute('data-firstletter') || '').toUpperCase();

            const matchInitial = (activeStudentInitial === 'ALL') || (firstLetter === activeStudentInitial);
            const matchKeyword = !q || name.includes(q) || nis.includes(q) || cls.includes(q) || initials.includes(q) || initials.startsWith(q);

            if (matchInitial && matchKeyword) {
                el.classList.remove('hidden');
                visibleCount++;
            } else {
                el.classList.add('hidden');
            }
        });

        document.getElementById('student-empty').classList.toggle('hidden', visibleCount > 0);
    }

    function resetStudentSearch() {
        const input = document.getElementById('student_search_input');
        if (input) {
            input.value = '';
            applyStudentFilters();
            input.focus();
        }
    }

    function selectStudent(id, name, meta, initials) {
        document.getElementById('student_id').value = id;
        document.getElementById('student-preview-name').textContent = name;
        document.getElementById('student-preview-meta').textContent = meta;
        document.getElementById('student-preview-avatar').textContent = initials;

        document.getElementById('student-selected-preview').classList.remove('hidden');
        document.getElementById('student-search-wrapper').classList.add('hidden');
        document.getElementById('student-initials-bar').classList.add('hidden');
        closeStudentDropdown();
    }

    function clearStudentSelection() {
        document.getElementById('student_id').value = '';
        document.getElementById('student-selected-preview').classList.add('hidden');
        document.getElementById('student-search-wrapper').classList.remove('hidden');
        document.getElementById('student-initials-bar').classList.remove('hidden');
        resetStudentSearch();
    }

    // --- 2. CATEGORY SEARCH & INITIALS ---
    let currentSelectedCategory = null;

    function openCategoryDropdown() {
        document.getElementById('category-dropdown').classList.remove('hidden');
    }

    function closeCategoryDropdown() {
        document.getElementById('category-dropdown').classList.add('hidden');
    }

    function filterCategories(query) {
        openCategoryDropdown();
        const q = (query || '').trim().toLowerCase();
        const items = document.querySelectorAll('.category-item');
        let visibleCount = 0;

        items.forEach(el => {
            const code = (el.getAttribute('data-code') || '').toLowerCase();
            const name = (el.getAttribute('data-name') || '').toLowerCase();
            const initials = (el.getAttribute('data-initials') || '').toLowerCase();

            const isMatch = !q || code.includes(q) || name.includes(q) || initials.includes(q) || initials.startsWith(q);

            if (isMatch) {
                el.classList.remove('hidden');
                visibleCount++;
            } else {
                el.classList.add('hidden');
            }
        });

        document.getElementById('category-empty').classList.toggle('hidden', visibleCount > 0);
    }

    function selectCategory(id, code, name) {
        currentSelectedCategory = id;
        document.getElementById('category-id-hidden').value = id;
        document.getElementById('category-preview-code').textContent = code;
        document.getElementById('category-preview-name').textContent = name;

        document.getElementById('category-selected-preview').classList.remove('hidden');
        document.getElementById('category-search-wrapper').classList.add('hidden');
        closeCategoryDropdown();

        // Also filter items to this category
        filterMasterItems('');
    }

    function clearCategorySelection() {
        currentSelectedCategory = null;
        document.getElementById('category-id-hidden').value = '';
        document.getElementById('category-selected-preview').classList.add('hidden');
        document.getElementById('category-search-wrapper').classList.remove('hidden');
        document.getElementById('category_search_input').value = '';
        filterCategories('');
        filterMasterItems('');
    }

    // --- 3. MASTER ITEM SEARCH & INITIALS ---
    function openItemDropdown() {
        document.getElementById('item-dropdown').classList.remove('hidden');
    }

    function closeItemDropdown() {
        document.getElementById('item-dropdown').classList.add('hidden');
    }

    function filterMasterItems(query) {
        openItemDropdown();
        const q = (query || '').trim().toLowerCase();
        const rows = document.querySelectorAll('.master-item-row');
        let visibleCount = 0;

        rows.forEach(el => {
            const catId = el.getAttribute('data-category');
            const code = (el.getAttribute('data-code') || '').toLowerCase();
            const name = (el.getAttribute('data-name') || '').toLowerCase();
            const catName = (el.getAttribute('data-cat-name') || '').toLowerCase();
            const initials = (el.getAttribute('data-initials') || '').toLowerCase();
            const points = (el.getAttribute('data-points') || '').toLowerCase();

            // If a category is selected, only show items belonging to that category unless user is explicitly searching
            const categoryMatch = !currentSelectedCategory || catId === currentSelectedCategory || q.length > 0;
            const queryMatch = !q || code.includes(q) || name.includes(q) || catName.includes(q) || initials.includes(q) || initials.startsWith(q) || points.includes(q);

            if (categoryMatch && queryMatch) {
                el.classList.remove('hidden');
                visibleCount++;
            } else {
                el.classList.add('hidden');
            }
        });

        document.getElementById('item-empty').classList.toggle('hidden', visibleCount > 0);
    }

    function selectMasterItem(id, code, name, points, level, catId, catCode, catName) {
        document.getElementById('item-id-hidden').value = id;
        document.getElementById('item-preview-code').textContent = code;
        document.getElementById('item-preview-name').textContent = name;
        document.getElementById('item-preview-meta').textContent = catCode + ' - ' + catName + ' (+' + points + ' Poin)';

        document.getElementById('item-selected-preview').classList.remove('hidden');
        document.getElementById('item-search-wrapper').classList.add('hidden');
        closeItemDropdown();

        // Auto-fill form values
        if (points) document.getElementById('points-input').value = points;
        if (name && !document.getElementById('title-input').value) document.getElementById('title-input').value = name;
        if (level) document.getElementById('level-select').value = level;

        // Auto-select category if not selected yet
        if (!currentSelectedCategory && catId) {
            selectCategory(catId, catCode, catName);
        }
    }

    function clearItemSelection() {
        document.getElementById('item-id-hidden').value = '';
        document.getElementById('item-selected-preview').classList.add('hidden');
        document.getElementById('item-search-wrapper').classList.remove('hidden');
        document.getElementById('item_search_input').value = '';
        filterMasterItems('');
    }

    // --- GLOBAL CLICK LISTENER TO CLOSE DROPDOWNS ---
    document.addEventListener('click', function(e) {
        const studentBox = document.getElementById('student-combobox');
        const catBox = document.getElementById('category-combobox');
        const itemBox = document.getElementById('item-combobox');

        if (studentBox && !studentBox.contains(e.target)) {
            closeStudentDropdown();
        }
        if (catBox && !catBox.contains(e.target)) {
            closeCategoryDropdown();
        }
        if (itemBox && !itemBox.contains(e.target)) {
            closeItemDropdown();
        }
    });
</script>
@endpush
