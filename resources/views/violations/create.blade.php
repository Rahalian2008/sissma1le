@extends('layouts.app')

@section('title', 'Catat Pelanggaran Tata Tertib')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Catat Pelanggaran Tata Tertib</h2>
                <p class="text-xs text-slate-500">Pilih butir pelanggaran berdasarkan kategori P1 s/d P6 Tata Tertib SMAN 1 Lengkong</p>
            </div>
            <a href="{{ route('violations.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">← Kembali</a>
        </div>

        <form action="{{ route('violations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- 1. Siswa Terlapor (Searchable Combobox + Inisial) -->
            <div class="relative" id="viol-student-combobox">
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">
                    Pilih Siswa Terlapor * 
                    <span class="text-[10px] font-normal text-slate-400 ml-1">
                        (Cari dengan ketik nama, NIS, kelas, atau inisial misal: AD, MRP)
                    </span>
                </label>
                <input type="hidden" name="student_id" id="student_id" required value="{{ old('student_id') }}">
                
                <!-- Selected Badge Preview -->
                <div id="viol-student-selected-preview" class="hidden mb-2 p-3 bg-rose-50/90 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl flex items-center justify-between transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <div id="viol-student-preview-avatar" class="w-9 h-9 rounded-xl bg-rose-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                            -
                        </div>
                        <div class="min-w-0">
                            <div id="viol-student-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-xs sm:text-sm"></div>
                            <div id="viol-student-preview-meta" class="text-[11px] text-rose-700 dark:text-rose-300 truncate"></div>
                        </div>
                    </div>
                    <button type="button" onclick="clearViolStudentSelection()" class="px-2.5 py-1 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/50 rounded-lg transition shrink-0 cursor-pointer">
                        Ganti Siswa ✕
                    </button>
                </div>

                <!-- Search Input with Icon -->
                <div class="relative" id="viol-student-search-wrapper">
                    <input type="text" id="viol_student_search_input"
                        placeholder="🔍 Ketik nama, NIS, kelas, atau inisial siswa (misal: MR, AF, Dani, XI-IPA)..."
                        autocomplete="off"
                        onfocus="openViolStudentDropdown()"
                        oninput="filterViolStudents(this.value)"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 focus:bg-white dark:focus:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button type="button" id="viol-student-search-clear" onclick="resetViolStudentSearch()" class="hidden absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        ✕
                    </button>
                </div>

                <!-- Quick Alphabet Initials Filter Bar (A-Z Chips) -->
                <div class="mt-2 flex items-center gap-1 overflow-x-auto pb-1.5 pt-0.5 text-xs no-scrollbar" id="viol-initials-bar">
                    <button type="button" onclick="filterViolByInitial('ALL')"
                        class="viol-initial-chip px-2.5 py-1 rounded-lg bg-rose-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs" data-letter="ALL">
                        Semua
                    </button>
                    @foreach(range('A', 'Z') as $char)
                        <button type="button" onclick="filterViolByInitial('{{ $char }}')"
                            class="viol-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700" data-letter="{{ $char }}">
                            {{ $char }}
                        </button>
                    @endforeach
                </div>

                <!-- Dropdown Result List -->
                <div id="viol-student-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-64 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl z-50 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($students as $s)
                        @php
                            $words = explode(' ', trim($s->name));
                            $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                            $firstChar = strtoupper(substr(trim($s->name), 0, 1));
                        @endphp
                        <div class="viol-student-item px-3.5 py-2.5 hover:bg-rose-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition"
                             data-id="{{ $s->id }}"
                             data-name="{{ $s->name }}"
                             data-nis="{{ $s->nis }}"
                             data-class="{{ $s->schoolClass?->name ?? 'Umum' }}"
                             data-initials="{{ strtolower($initials) }}"
                             data-firstletter="{{ $firstChar }}"
                             onclick="selectViolStudent('{{ $s->id }}', '{{ addslashes($s->name) }}', 'Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • NIS: {{ $s->nis }}', '{{ strtoupper(substr($initials, 0, 2)) }}')">
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <span class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 font-bold text-xs flex items-center justify-center shrink-0">
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
                    <div id="viol-student-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                        Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut.
                    </div>
                </div>
            </div>

            <!-- 2. Kategori Pelanggaran (P1 - P6) (Searchable Combobox) -->
            <div class="relative" id="viol-category-combobox">
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">
                    Kategori Pelanggaran (P1 - P6) *
                    <span class="text-[10px] font-normal text-slate-400 ml-1">
                        (Ketik kode misal P1 atau nama kategori)
                    </span>
                </label>
                <input type="hidden" name="category_id" id="viol-category-id-hidden" required value="{{ old('category_id') }}">

                <!-- Selected Category Preview -->
                <div id="viol-category-selected-preview" class="hidden mb-2 p-3 bg-rose-50/90 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl flex items-center justify-between transition">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span id="viol-category-preview-code" class="px-2.5 py-1 bg-rose-600 text-white font-black text-xs rounded-lg shrink-0">
                            P1
                        </span>
                        <div id="viol-category-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-xs sm:text-sm"></div>
                    </div>
                    <button type="button" onclick="clearViolCategorySelection()" class="px-2.5 py-1 text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-lg transition shrink-0 cursor-pointer">
                        Ganti ✕
                    </button>
                </div>

                <!-- Search Input for Category -->
                <div class="relative" id="viol-category-search-wrapper">
                    <input type="text" id="viol_category_search_input"
                        placeholder="🔍 Ketik kode (misal: P1, P4) atau inisial/nama kategori pelanggaran..."
                        autocomplete="off"
                        onfocus="openViolCategoryDropdown()"
                        oninput="filterViolCategories(this.value)"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 focus:bg-white dark:focus:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Dropdown Result List for Categories -->
                <div id="viol-category-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl z-40 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($categories as $cat)
                        @php
                            $catInitials = collect(explode(' ', $cat->name))->map(fn($w) => substr($w, 0, 1))->join('');
                        @endphp
                        <div class="viol-category-item px-3.5 py-2.5 hover:bg-rose-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition"
                             data-id="{{ $cat->id }}"
                             data-code="{{ $cat->code }}"
                             data-name="{{ $cat->name }}"
                             data-initials="{{ strtolower($catInitials) }}"
                             onclick="selectViolCategory('{{ $cat->id }}', '{{ $cat->code }}', '{{ addslashes($cat->name) }}')">
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <span class="px-2 py-0.5 rounded-md bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-300 font-extrabold text-xs shrink-0">
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
                    <div id="viol-category-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                        Tidak ada kategori yang cocok.
                    </div>
                </div>
            </div>

            <!-- 3. Butir Master Pelanggaran (Searchable Combobox) -->
            <div class="relative" id="viol-item-combobox">
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">
                    Butir Master Pelanggaran (Poin Otomatis)
                    <span class="text-[10px] font-normal text-slate-400 ml-1">
                        (Ketik kode misal P1.1, nama pelanggaran, kata kunci, atau inisial)
                    </span>
                </label>
                <input type="hidden" name="item_id" id="viol-item-id-hidden" value="{{ old('item_id') }}">

                <!-- Selected Item Preview -->
                <div id="viol-item-selected-preview" class="hidden mb-2 p-3 bg-rose-50/90 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl flex items-center justify-between transition">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span id="viol-item-preview-code" class="px-2.5 py-1 bg-rose-600 text-white font-black text-xs rounded-lg shrink-0">
                            -
                        </span>
                        <div class="min-w-0">
                            <div id="viol-item-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-xs"></div>
                            <div id="viol-item-preview-meta" class="text-[11px] text-rose-700 dark:text-rose-300"></div>
                        </div>
                    </div>
                    <button type="button" onclick="clearViolItemSelection()" class="px-2.5 py-1 text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-lg transition shrink-0 cursor-pointer">
                        Ganti ✕
                    </button>
                </div>

                <!-- Search Input for Item -->
                <div class="relative" id="viol-item-search-wrapper">
                    <input type="text" id="viol_item_search_input"
                        placeholder="🔍 Ketik kode butir (misal: P1.1, P2.3), kata kunci, atau inisial butir..."
                        autocomplete="off"
                        onfocus="openViolItemDropdown()"
                        oninput="filterViolMasterItems(this.value)"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 focus:bg-white dark:focus:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Dropdown Result List for Items -->
                <div id="viol-item-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl z-30 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($categories as $cat)
                        @foreach($cat->items as $item)
                            @php
                                $itemInitials = collect(explode(' ', $item->name))->map(fn($w) => substr($w, 0, 1))->join('');
                            @endphp
                            <div class="viol-master-item-row px-3.5 py-2.5 hover:bg-rose-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition"
                                 data-id="{{ $item->id }}"
                                 data-category="{{ $cat->id }}"
                                 data-cat-code="{{ $cat->code }}"
                                 data-cat-name="{{ $cat->name }}"
                                 data-code="{{ $item->code }}"
                                 data-name="{{ $item->name }}"
                                 data-points="{{ $item->default_points }}"
                                 data-rec="{{ $item->guidance_recommendation ?? '' }}"
                                 data-initials="{{ strtolower($itemInitials) }}"
                                 onclick="selectViolMasterItem('{{ $item->id }}', '{{ $item->code }}', '{{ addslashes($item->name) }}', '{{ $item->default_points }}', '{{ addslashes($item->guidance_recommendation ?? '') }}', '{{ $cat->id }}', '{{ $cat->code }}', '{{ addslashes($cat->name) }}')">
                                <div class="flex items-center gap-2.5 truncate mr-2">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono text-[10px] font-bold shrink-0">
                                        [{{ $item->code }}]
                                    </span>
                                    <div class="truncate">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 text-xs truncate">{{ $item->name }}</div>
                                        <div class="text-[10px] text-slate-400 truncate">{{ $cat->code }} - {{ $cat->name }}</div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-300 font-black text-[10px] shrink-0">
                                    {{ $item->default_points }} Poin
                                </span>
                            </div>
                        @endforeach
                    @endforeach
                    <div id="viol-item-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                        Tidak ada butir pelanggaran yang cocok.
                    </div>
                </div>
            </div>

            <!-- Bentuk Pelanggaran -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Uraian Bentuk Pelanggaran *</label>
                <input type="text" name="title" id="viol-title-input" required placeholder="Contoh: Terlambat masuk sekolah lebih dari 15 menit"
                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 text-xs text-slate-800 dark:text-slate-100">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Poin Pelanggaran *</label>
                    <input type="number" name="points" id="viol-points-input" value="10" min="1" max="200" required
                        class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 font-bold text-rose-600 dark:text-rose-400 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Tanggal Kejadian *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 text-xs text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Jam Kejadian</label>
                    <input type="time" name="time" value="{{ date('H:i') }}"
                        class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 text-xs text-slate-800 dark:text-slate-100">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Tempat / Lokasi Kejadian</label>
                <input type="text" name="location" placeholder="Contoh: Pintu Gerbang Utama / Ruang Kelas XI-IPA-1"
                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 text-xs text-slate-800 dark:text-slate-100">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Kronologi Kejadian</label>
                <textarea name="chronology" rows="3" placeholder="Jelaskan ringkas kronologi kejadian pelanggaran..."
                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 text-xs text-slate-800 dark:text-slate-100"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Catatan Pembinaan / Rekomendasi</label>
                <textarea name="guidance_notes" id="viol-rec-input" rows="2" placeholder="Arahan pembinaan atau tindakan awal yang diberikan..."
                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-rose-500 text-xs text-slate-800 dark:text-slate-100"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">Upload Bukti Foto / Dokumen Pendukung (PDF/JPG/PNG)</label>
                <input type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-rose-600 file:text-white hover:file:bg-rose-500 text-xs text-slate-600 dark:text-slate-300">
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <a href="{{ route('violations.index') }}" class="px-5 py-2.5 font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Catat Pelanggaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- 1. VIOLATION STUDENT SEARCH & INITIALS ---
    let activeViolInitial = 'ALL';

    function openViolStudentDropdown() {
        document.getElementById('viol-student-dropdown').classList.remove('hidden');
    }

    function closeViolStudentDropdown() {
        document.getElementById('viol-student-dropdown').classList.add('hidden');
    }

    function filterViolByInitial(letter) {
        activeViolInitial = letter;
        document.querySelectorAll('.viol-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === letter) {
                btn.className = 'viol-initial-chip px-2.5 py-1 rounded-lg bg-rose-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
            } else {
                btn.className = 'viol-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700';
            }
        });
        openViolStudentDropdown();
        applyViolFilters();
    }

    function filterViolStudents(query) {
        openViolStudentDropdown();
        applyViolFilters();
    }

    function applyViolFilters() {
        const input = document.getElementById('viol_student_search_input');
        const q = (input?.value || '').trim().toLowerCase();
        const items = document.querySelectorAll('.viol-student-item');
        let visibleCount = 0;

        const clearBtn = document.getElementById('viol-student-search-clear');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', q.length === 0);
        }

        items.forEach(el => {
            const name = (el.getAttribute('data-name') || '').toLowerCase();
            const nis = (el.getAttribute('data-nis') || '').toLowerCase();
            const cls = (el.getAttribute('data-class') || '').toLowerCase();
            const initials = (el.getAttribute('data-initials') || '').toLowerCase();
            const firstLetter = (el.getAttribute('data-firstletter') || '').toUpperCase();

            const matchInitial = (activeViolInitial === 'ALL') || (firstLetter === activeViolInitial);
            const matchKeyword = !q || name.includes(q) || nis.includes(q) || cls.includes(q) || initials.includes(q) || initials.startsWith(q);

            if (matchInitial && matchKeyword) {
                el.classList.remove('hidden');
                visibleCount++;
            } else {
                el.classList.add('hidden');
            }
        });

        document.getElementById('viol-student-empty').classList.toggle('hidden', visibleCount > 0);
    }

    function resetViolStudentSearch() {
        const input = document.getElementById('viol_student_search_input');
        if (input) {
            input.value = '';
            applyViolFilters();
            input.focus();
        }
    }

    function selectViolStudent(id, name, meta, initials) {
        document.getElementById('student_id').value = id;
        document.getElementById('viol-student-preview-name').textContent = name;
        document.getElementById('viol-student-preview-meta').textContent = meta;
        document.getElementById('viol-student-preview-avatar').textContent = initials;

        document.getElementById('viol-student-selected-preview').classList.remove('hidden');
        document.getElementById('viol-student-search-wrapper').classList.add('hidden');
        document.getElementById('viol-initials-bar').classList.add('hidden');
        closeViolStudentDropdown();
    }

    function clearViolStudentSelection() {
        document.getElementById('student_id').value = '';
        document.getElementById('viol-student-selected-preview').classList.add('hidden');
        document.getElementById('viol-student-search-wrapper').classList.remove('hidden');
        document.getElementById('viol-initials-bar').classList.remove('hidden');
        resetViolStudentSearch();
    }

    // --- 2. VIOLATION CATEGORY SEARCH & INITIALS ---
    let currentSelectedViolCategory = null;

    function openViolCategoryDropdown() {
        document.getElementById('viol-category-dropdown').classList.remove('hidden');
    }

    function closeViolCategoryDropdown() {
        document.getElementById('viol-category-dropdown').classList.add('hidden');
    }

    function filterViolCategories(query) {
        openViolCategoryDropdown();
        const q = (query || '').trim().toLowerCase();
        const items = document.querySelectorAll('.viol-category-item');
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

        document.getElementById('viol-category-empty').classList.toggle('hidden', visibleCount > 0);
    }

    function selectViolCategory(id, code, name) {
        currentSelectedViolCategory = id;
        document.getElementById('viol-category-id-hidden').value = id;
        document.getElementById('viol-category-preview-code').textContent = code;
        document.getElementById('viol-category-preview-name').textContent = name;

        document.getElementById('viol-category-selected-preview').classList.remove('hidden');
        document.getElementById('viol-category-search-wrapper').classList.add('hidden');
        closeViolCategoryDropdown();

        // Also filter items to this category
        filterViolMasterItems('');
    }

    function clearViolCategorySelection() {
        currentSelectedViolCategory = null;
        document.getElementById('viol-category-id-hidden').value = '';
        document.getElementById('viol-category-selected-preview').classList.add('hidden');
        document.getElementById('viol-category-search-wrapper').classList.remove('hidden');
        document.getElementById('viol_category_search_input').value = '';
        filterViolCategories('');
        filterViolMasterItems('');
    }

    // --- 3. VIOLATION MASTER ITEM SEARCH & INITIALS ---
    function openViolItemDropdown() {
        document.getElementById('viol-item-dropdown').classList.remove('hidden');
    }

    function closeViolItemDropdown() {
        document.getElementById('viol-item-dropdown').classList.add('hidden');
    }

    function filterViolMasterItems(query) {
        openViolItemDropdown();
        const q = (query || '').trim().toLowerCase();
        const rows = document.querySelectorAll('.viol-master-item-row');
        let visibleCount = 0;

        rows.forEach(el => {
            const catId = el.getAttribute('data-category');
            const code = (el.getAttribute('data-code') || '').toLowerCase();
            const name = (el.getAttribute('data-name') || '').toLowerCase();
            const catName = (el.getAttribute('data-cat-name') || '').toLowerCase();
            const initials = (el.getAttribute('data-initials') || '').toLowerCase();
            const points = (el.getAttribute('data-points') || '').toLowerCase();

            // If a category is selected, show items for that category unless user is explicitly searching
            const categoryMatch = !currentSelectedViolCategory || catId === currentSelectedViolCategory || q.length > 0;
            const queryMatch = !q || code.includes(q) || name.includes(q) || catName.includes(q) || initials.includes(q) || initials.startsWith(q) || points.includes(q);

            if (categoryMatch && queryMatch) {
                el.classList.remove('hidden');
                visibleCount++;
            } else {
                el.classList.add('hidden');
            }
        });

        document.getElementById('viol-item-empty').classList.toggle('hidden', visibleCount > 0);
    }

    function selectViolMasterItem(id, code, name, points, rec, catId, catCode, catName) {
        document.getElementById('viol-item-id-hidden').value = id;
        document.getElementById('viol-item-preview-code').textContent = code;
        document.getElementById('viol-item-preview-name').textContent = name;
        document.getElementById('viol-item-preview-meta').textContent = catCode + ' - ' + catName + ' (' + points + ' Poin)';

        document.getElementById('viol-item-selected-preview').classList.remove('hidden');
        document.getElementById('viol-item-search-wrapper').classList.add('hidden');
        closeViolItemDropdown();

        // Auto-fill form values
        if (points) document.getElementById('viol-points-input').value = points;
        if (name && !document.getElementById('viol-title-input').value) document.getElementById('viol-title-input').value = name;
        if (rec) document.getElementById('viol-rec-input').value = rec;

        // Auto-select category if not selected yet
        if (!currentSelectedViolCategory && catId) {
            selectViolCategory(catId, catCode, catName);
        }
    }

    function clearViolItemSelection() {
        document.getElementById('viol-item-id-hidden').value = '';
        document.getElementById('viol-item-selected-preview').classList.add('hidden');
        document.getElementById('viol-item-search-wrapper').classList.remove('hidden');
        document.getElementById('viol_item_search_input').value = '';
        filterViolMasterItems('');
    }

    // --- GLOBAL CLICK LISTENER TO CLOSE DROPDOWNS ---
    document.addEventListener('click', function(e) {
        const studentBox = document.getElementById('viol-student-combobox');
        const catBox = document.getElementById('viol-category-combobox');
        const itemBox = document.getElementById('viol-item-combobox');

        if (studentBox && !studentBox.contains(e.target)) {
            closeViolStudentDropdown();
        }
        if (catBox && !catBox.contains(e.target)) {
            closeViolCategoryDropdown();
        }
        if (itemBox && !itemBox.contains(e.target)) {
            closeViolItemDropdown();
        }
    });
</script>
@endpush
