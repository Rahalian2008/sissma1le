@extends('layouts.app')

@section('title', 'Edit Pelanggaran Tata Tertib')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Edit Pelanggaran Tata Tertib</h2>
                <p class="text-xs text-slate-500">Ubah data pelanggaran siswa</p>
            </div>
            <a href="{{ route('violations.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">← Kembali</a>
        </div>

        <form action="{{ route('violations.update', $violation->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <!-- 1. Siswa Terlapor (Searchable Combobox + Inisial) -->
            <div class="relative" id="viol-student-combobox">
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">
                    Pilih Siswa Terlapor * 
                    <span class="text-[10px] font-normal text-slate-400 ml-1">
                        (Cari dengan ketik nama, NIS, kelas, atau inisial misal: AD, MRP)
                    </span>
                </label>
                <input type="hidden" name="student_id" id="student_id" required value="{{ old('student_id', $violation->student_id) }}">
                
                @php
                    $selectedStudent = $students->firstWhere('id', old('student_id', $violation->student_id));
                    $selInitials = $selectedStudent ? $selectedStudent->initials : '';
                @endphp

                <!-- Selected Badge Preview -->
                <div id="viol-student-selected-preview" class="{{ $selectedStudent ? '' : 'hidden' }} mb-2 p-3 bg-rose-50/90 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl flex items-center justify-between transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <div id="viol-student-preview-avatar" class="w-9 h-9 rounded-xl bg-rose-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                            {{ $selectedStudent ? strtoupper(substr($selInitials ?: $selectedStudent->name, 0, 2)) : '-' }}
                        </div>
                        <div class="min-w-0">
                            <div id="viol-student-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-xs sm:text-sm">
                                {{ $selectedStudent?->name ?? '' }}
                            </div>
                            <div id="viol-student-preview-meta" class="text-[11px] text-rose-700 dark:text-rose-300 truncate">
                                Kelas {{ $selectedStudent?->schoolClass?->name ?? 'Umum' }} • NIS: {{ $selectedStudent?->nis ?? '-' }} • [{{ strtoupper($selInitials) }}]
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="clearViolStudentSelection()" class="px-2.5 py-1 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/50 rounded-lg transition shrink-0 cursor-pointer">
                        Ganti Siswa ✕
                    </button>
                </div>

                <!-- Search Input with Icon -->
                <div class="relative {{ $selectedStudent ? 'hidden' : '' }}" id="viol-student-search-wrapper">
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
                <div class="mt-2 {{ $selectedStudent ? 'hidden' : '' }} flex items-center gap-1 overflow-x-auto pb-1.5 pt-0.5 text-xs no-scrollbar" id="viol-initials-bar">
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
                            $inits = $s->initials;
                            $firstChar = strtoupper(substr(trim($s->name), 0, 1));
                        @endphp
                        <div class="viol-student-item px-3.5 py-2.5 hover:bg-rose-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition"
                             data-id="{{ $s->id }}"
                             data-name="{{ $s->name }}"
                             data-nis="{{ $s->nis }}"
                             data-class="{{ $s->schoolClass?->name ?? 'Umum' }}"
                             data-initials="{{ strtolower($inits) }}"
                             data-avatar="{{ strtoupper(substr($inits ?: $s->name, 0, 2)) }}"
                             data-firstletter="{{ $firstChar }}"
                             onclick="selectViolStudent(this)">
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <span class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($inits ?: $s->name, 0, 2)) }}
                                </span>
                                <div class="truncate">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 truncate text-xs">{{ $s->name }}</div>
                                    <div class="text-[10px] text-slate-400">Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • NIS: {{ $s->nis }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono shrink-0 font-bold">
                                [{{ strtoupper($inits) }}]
                            </span>
                        </div>
                    @endforeach
                    <div id="viol-student-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                        Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut.
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Kategori Pelanggaran *</label>
                <select name="category_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-rose-500">
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ $violation->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Judul / Deskripsi Singkat *</label>
                <input type="text" name="title" required value="{{ old('title', $violation->title) }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-rose-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Poin Pelanggaran *</label>
                <input type="number" name="points" required value="{{ old('points', $violation->points) }}" min="1" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-rose-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal *</label>
                    <input type="date" name="date" required value="{{ old('date', $violation->date) }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-rose-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jam</label>
                    <input type="time" name="time" value="{{ old('time', $violation->time ? \Carbon\Carbon::parse($violation->time)->format('H:i') : '') }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-rose-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $violation->location) }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-rose-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Kronologi Kejadian</label>
                <textarea name="chronology" rows="3" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-rose-500">{{ old('chronology', $violation->chronology) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Catatan Pembinaan</label>
                <textarea name="guidance_notes" rows="3" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-rose-500">{{ old('guidance_notes', $violation->guidance_notes) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Verifikasi *</label>
                <select name="status" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-rose-500">
                    <option value="DIVERIFIKASI" {{ $violation->status == 'DIVERIFIKASI' ? 'selected' : '' }}>DIVERIFIKASI</option>
                    <option value="MENUNGGU_VERIFIKASI" {{ $violation->status == 'MENUNGGU_VERIFIKASI' ? 'selected' : '' }}>MENUNGGU_VERIFIKASI</option>
                    <option value="DITOLAK" {{ $violation->status == 'DITOLAK' ? 'selected' : '' }}>DITOLAK</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('violations.index') }}" class="px-5 py-2.5 text-slate-500 hover:text-slate-700 font-bold transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl shadow-xs transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    let activeViolInitial = 'ALL';

    function openViolStudentDropdown() {
        const dd = document.getElementById('viol-student-dropdown');
        if (dd) dd.classList.remove('hidden');
    }

    function closeViolStudentDropdown() {
        const dd = document.getElementById('viol-student-dropdown');
        if (dd) dd.classList.add('hidden');
    }

    function filterViolByInitial(char) {
        activeViolInitial = char;
        document.querySelectorAll('.viol-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === char) {
                btn.className = 'viol-initial-chip px-2.5 py-1 rounded-lg bg-rose-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
            } else {
                btn.className = 'viol-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700';
            }
        });
        openViolStudentDropdown();
        filterViolStudents(document.getElementById('viol_student_search_input')?.value || '');
    }

    function filterViolStudents(query) {
        const q = query.toLowerCase().trim();
        const clearBtn = document.getElementById('viol-student-search-clear');
        if (clearBtn) clearBtn.classList.toggle('hidden', q === '');

        const items = document.querySelectorAll('.viol-student-item');
        let visibleCount = 0;

        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            const nis = (item.getAttribute('data-nis') || '').toLowerCase();
            const sClass = (item.getAttribute('data-class') || '').toLowerCase();
            const initials = (item.getAttribute('data-initials') || '').toLowerCase();
            const firstLetter = (item.getAttribute('data-firstletter') || '').toUpperCase();

            const matchesInitial = (activeViolInitial === 'ALL' || firstLetter === activeViolInitial);
            const matchesQuery = !q || name.includes(q) || nis.includes(q) || sClass.includes(q) || initials.includes(q);

            if (matchesInitial && matchesQuery) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const empty = document.getElementById('viol-student-empty');
        if (empty) empty.classList.toggle('hidden', visibleCount > 0);
        openViolStudentDropdown();
    }

    function selectViolStudent(el) {
        const id = el.getAttribute('data-id');
        const name = el.getAttribute('data-name');
        const nis = el.getAttribute('data-nis');
        const sClass = el.getAttribute('data-class');
        const avatar = el.getAttribute('data-avatar');
        const initials = el.getAttribute('data-initials');

        const hidden = document.getElementById('student_id');
        if (hidden) hidden.value = id;

        const preview = document.getElementById('viol-student-selected-preview');
        const previewName = document.getElementById('viol-student-preview-name');
        const previewMeta = document.getElementById('viol-student-preview-meta');
        const previewAvatar = document.getElementById('viol-student-preview-avatar');
        const searchWrapper = document.getElementById('viol-student-search-wrapper');
        const initialsBar = document.getElementById('viol-initials-bar');

        if (previewName) previewName.textContent = name;
        if (previewMeta) previewMeta.textContent = `Kelas ${sClass} • NIS: ${nis} • [${initials.toUpperCase()}]`;
        if (previewAvatar) previewAvatar.textContent = avatar;

        if (preview) preview.classList.remove('hidden');
        if (searchWrapper) searchWrapper.classList.add('hidden');
        if (initialsBar) initialsBar.classList.add('hidden');

        closeViolStudentDropdown();
    }

    function clearViolStudentSelection() {
        const hidden = document.getElementById('student_id');
        if (hidden) hidden.value = '';

        const preview = document.getElementById('viol-student-selected-preview');
        const searchWrapper = document.getElementById('viol-student-search-wrapper');
        const initialsBar = document.getElementById('viol-initials-bar');
        const input = document.getElementById('viol_student_search_input');

        if (preview) preview.classList.add('hidden');
        if (searchWrapper) searchWrapper.classList.remove('hidden');
        if (initialsBar) initialsBar.classList.remove('hidden');
        if (input) {
            input.value = '';
            input.focus();
        }

        filterViolByInitial('ALL');
    }

    function resetViolStudentSearch() {
        const input = document.getElementById('viol_student_search_input');
        if (input) {
            input.value = '';
            input.focus();
        }
        filterViolStudents('');
    }

    document.addEventListener('click', function(e) {
        const combobox = document.getElementById('viol-student-combobox');
        if (combobox && !combobox.contains(e.target)) {
            closeViolStudentDropdown();
        }
    });
</script>
@endsection
