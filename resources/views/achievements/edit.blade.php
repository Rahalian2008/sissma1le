@extends('layouts.app')

@section('title', 'Edit Data Prestasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Edit Data Prestasi</h2>
                <p class="text-xs text-slate-500">Ubah data prestasi siswa</p>
            </div>
            <a href="{{ route('achievements.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">← Kembali</a>
        </div>

        <form action="{{ route('achievements.update', $achievement->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <!-- 1. Siswa Berprestasi (Searchable Combobox + Inisial) -->
            <div class="relative" id="achieve-student-combobox">
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">
                    Pilih Siswa Berprestasi * 
                    <span class="text-[10px] font-normal text-slate-400 ml-1">
                        (Cari dengan ketik nama, NIS, kelas, atau inisial misal: AD, MRP)
                    </span>
                </label>
                <input type="hidden" name="student_id" id="student_id" required value="{{ old('student_id', $achievement->student_id) }}">
                
                @php
                    $selectedStudent = $students->firstWhere('id', old('student_id', $achievement->student_id));
                    $selInitials = $selectedStudent ? $selectedStudent->initials : '';
                @endphp

                <!-- Selected Badge Preview -->
                <div id="achieve-student-selected-preview" class="{{ $selectedStudent ? '' : 'hidden' }} mb-2 p-3 bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center justify-between transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <div id="achieve-student-preview-avatar" class="w-9 h-9 rounded-xl bg-emerald-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                            {{ $selectedStudent ? strtoupper(substr($selInitials ?: $selectedStudent->name, 0, 2)) : '-' }}
                        </div>
                        <div class="min-w-0">
                            <div id="achieve-student-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-xs sm:text-sm">
                                {{ $selectedStudent?->name ?? '' }}
                            </div>
                            <div id="achieve-student-preview-meta" class="text-[11px] text-emerald-700 dark:text-emerald-300 truncate">
                                Kelas {{ $selectedStudent?->schoolClass?->name ?? 'Umum' }} • NIS: {{ $selectedStudent?->nis ?? '-' }} • [{{ strtoupper($selInitials) }}]
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="clearAchieveStudentSelection()" class="px-2.5 py-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-950/50 rounded-lg transition shrink-0 cursor-pointer">
                        Ganti Siswa ✕
                    </button>
                </div>

                <!-- Search Input with Icon -->
                <div class="relative {{ $selectedStudent ? 'hidden' : '' }}" id="achieve-student-search-wrapper">
                    <input type="text" id="achieve_student_search_input"
                        placeholder="🔍 Ketik nama, NIS, kelas, atau inisial siswa (misal: MR, AF, Dani, XI-IPA)..."
                        autocomplete="off"
                        onfocus="openAchieveStudentDropdown()"
                        oninput="filterAchieveStudents(this.value)"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button type="button" id="achieve-student-search-clear" onclick="resetAchieveStudentSearch()" class="hidden absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        ✕
                    </button>
                </div>

                <!-- Quick Alphabet Initials Filter Bar (A-Z Chips) -->
                <div class="mt-2 {{ $selectedStudent ? 'hidden' : '' }} flex items-center gap-1 overflow-x-auto pb-1.5 pt-0.5 text-xs no-scrollbar" id="achieve-initials-bar">
                    <button type="button" onclick="filterAchieveByInitial('ALL')"
                        class="achieve-initial-chip px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs" data-letter="ALL">
                        Semua
                    </button>
                    @foreach(range('A', 'Z') as $char)
                        <button type="button" onclick="filterAchieveByInitial('{{ $char }}')"
                            class="achieve-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700" data-letter="{{ $char }}">
                            {{ $char }}
                        </button>
                    @endforeach
                </div>

                <!-- Dropdown Result List -->
                <div id="achieve-student-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-64 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl z-50 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($students as $s)
                        @php
                            $inits = $s->initials;
                            $firstChar = strtoupper(substr(trim($s->name), 0, 1));
                        @endphp
                        <div class="achieve-student-item px-3.5 py-2.5 hover:bg-emerald-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition"
                             data-id="{{ $s->id }}"
                             data-name="{{ $s->name }}"
                             data-nis="{{ $s->nis }}"
                             data-class="{{ $s->schoolClass?->name ?? 'Umum' }}"
                             data-initials="{{ strtolower($inits) }}"
                             data-avatar="{{ strtoupper(substr($inits ?: $s->name, 0, 2)) }}"
                             data-firstletter="{{ $firstChar }}"
                             onclick="selectAchieveStudent(this)">
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center justify-center shrink-0">
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
                    <div id="achieve-student-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                        Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut.
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Kategori Prestasi *</label>
                <select name="category_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ $achievement->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }} (Maks Poin: {{ $c->max_point }})</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih Rincian Prestasi</label>
                <select name="item_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">
                    <option value="">-- Kosongkan Jika Tidak Tersedia --</option>
                    @foreach($categories as $c)
                        <optgroup label="{{ $c->name }}">
                            @foreach($c->items as $item)
                                <option value="{{ $item->id }}" {{ $achievement->item_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->description }} (Poin: {{ $item->point }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Tingkat Prestasi *</label>
                <select name="level" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">
                    @foreach(['SEKOLAH', 'KABUPATEN', 'PROVINSI', 'NASIONAL', 'INTERNASIONAL'] as $lvl)
                        <option value="{{ $lvl }}" {{ $achievement->level == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Judul Prestasi / Lomba *</label>
                <input type="text" name="title" required value="{{ old('title', $achievement->title) }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Poin Prestasi (Opsional) *</label>
                <input type="number" name="points" required value="{{ old('points', $achievement->points) }}" min="1" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Kegiatan *</label>
                    <input type="date" name="date" required value="{{ old('date', $achievement->date) }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Waktu</label>
                    <input type="time" name="time" value="{{ old('time', $achievement->time ? \Carbon\Carbon::parse($achievement->time)->format('H:i') : '') }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Keterangan / Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">{{ old('description', $achievement->description) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Verifikasi *</label>
                <select name="status" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">
                    <option value="DIVERIFIKASI" {{ $achievement->status == 'DIVERIFIKASI' ? 'selected' : '' }}>DIVERIFIKASI</option>
                    <option value="MENUNGGU_VERIFIKASI" {{ $achievement->status == 'MENUNGGU_VERIFIKASI' ? 'selected' : '' }}>MENUNGGU_VERIFIKASI</option>
                    <option value="DITOLAK" {{ $achievement->status == 'DITOLAK' ? 'selected' : '' }}>DITOLAK</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('achievements.index') }}" class="px-5 py-2.5 text-slate-500 hover:text-slate-700 font-bold transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-xs transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    let activeAchieveInitial = 'ALL';

    function openAchieveStudentDropdown() {
        const dd = document.getElementById('achieve-student-dropdown');
        if (dd) dd.classList.remove('hidden');
    }

    function closeAchieveStudentDropdown() {
        const dd = document.getElementById('achieve-student-dropdown');
        if (dd) dd.classList.add('hidden');
    }

    function filterAchieveByInitial(char) {
        activeAchieveInitial = char;
        document.querySelectorAll('.achieve-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === char) {
                btn.className = 'achieve-initial-chip px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
            } else {
                btn.className = 'achieve-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700';
            }
        });
        openAchieveStudentDropdown();
        filterAchieveStudents(document.getElementById('achieve_student_search_input')?.value || '');
    }

    function filterAchieveStudents(query) {
        const q = query.toLowerCase().trim();
        const clearBtn = document.getElementById('achieve-student-search-clear');
        if (clearBtn) clearBtn.classList.toggle('hidden', q === '');

        const items = document.querySelectorAll('.achieve-student-item');
        let visibleCount = 0;

        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            const nis = (item.getAttribute('data-nis') || '').toLowerCase();
            const sClass = (item.getAttribute('data-class') || '').toLowerCase();
            const initials = (item.getAttribute('data-initials') || '').toLowerCase();
            const firstLetter = (item.getAttribute('data-firstletter') || '').toUpperCase();

            const matchesInitial = (activeAchieveInitial === 'ALL' || firstLetter === activeAchieveInitial);
            const matchesQuery = !q || name.includes(q) || nis.includes(q) || sClass.includes(q) || initials.includes(q);

            if (matchesInitial && matchesQuery) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const empty = document.getElementById('achieve-student-empty');
        if (empty) empty.classList.toggle('hidden', visibleCount > 0);
        openAchieveStudentDropdown();
    }

    function selectAchieveStudent(el) {
        const id = el.getAttribute('data-id');
        const name = el.getAttribute('data-name');
        const nis = el.getAttribute('data-nis');
        const sClass = el.getAttribute('data-class');
        const avatar = el.getAttribute('data-avatar');
        const initials = el.getAttribute('data-initials');

        const hidden = document.getElementById('student_id');
        if (hidden) hidden.value = id;

        const preview = document.getElementById('achieve-student-selected-preview');
        const previewName = document.getElementById('achieve-student-preview-name');
        const previewMeta = document.getElementById('achieve-student-preview-meta');
        const previewAvatar = document.getElementById('achieve-student-preview-avatar');
        const searchWrapper = document.getElementById('achieve-student-search-wrapper');
        const initialsBar = document.getElementById('achieve-initials-bar');

        if (previewName) previewName.textContent = name;
        if (previewMeta) previewMeta.textContent = `Kelas ${sClass} • NIS: ${nis} • [${initials.toUpperCase()}]`;
        if (previewAvatar) previewAvatar.textContent = avatar;

        if (preview) preview.classList.remove('hidden');
        if (searchWrapper) searchWrapper.classList.add('hidden');
        if (initialsBar) initialsBar.classList.add('hidden');

        closeAchieveStudentDropdown();
    }

    function clearAchieveStudentSelection() {
        const hidden = document.getElementById('student_id');
        if (hidden) hidden.value = '';

        const preview = document.getElementById('achieve-student-selected-preview');
        const searchWrapper = document.getElementById('achieve-student-search-wrapper');
        const initialsBar = document.getElementById('achieve-initials-bar');
        const input = document.getElementById('achieve_student_search_input');

        if (preview) preview.classList.add('hidden');
        if (searchWrapper) searchWrapper.classList.remove('hidden');
        if (initialsBar) initialsBar.classList.remove('hidden');
        if (input) {
            input.value = '';
            input.focus();
        }

        filterAchieveByInitial('ALL');
    }

    function resetAchieveStudentSearch() {
        const input = document.getElementById('achieve_student_search_input');
        if (input) {
            input.value = '';
            input.focus();
        }
        filterAchieveStudents('');
    }

    document.addEventListener('click', function(e) {
        const combobox = document.getElementById('achieve-student-combobox');
        if (combobox && !combobox.contains(e.target)) {
            closeAchieveStudentDropdown();
        }
    });
</script>
@endsection
