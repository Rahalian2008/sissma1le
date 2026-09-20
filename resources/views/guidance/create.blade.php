@extends('layouts.app')

@section('title', 'Buka Catatan Pembinaan Siswa')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Catatan Pembinaan & Konseling Siswa</h2>
                <p class="text-xs text-slate-500">Formulir penanganan tata tertib, bimbingan wali kelas, SP BK, dan surat komitmen</p>
            </div>
            <a href="{{ route('guidance.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">← Kembali</a>
        </div>

        <form action="{{ route('guidance.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- Siswa (Searchable Combobox + Inisial) -->
            <div class="relative" id="guidance-student-combobox">
                <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1">
                    Pilih Siswa yang Dibina * 
                    <span class="text-[10px] font-normal text-slate-400 ml-1">
                        (Ketik nama, NIS, kelas, atau inisial misal: AD, MRP)
                    </span>
                </label>
                <input type="hidden" name="student_id" id="student_id" required value="{{ old('student_id', $selectedStudent?->id) }}">
                
                <!-- Selected Badge Preview -->
                @php
                    $initStudent = $selectedStudent ? $selectedStudent : null;
                    $initWords = $initStudent ? explode(' ', trim($initStudent->name)) : [];
                    $initInitials = $initStudent ? collect($initWords)->map(fn($w) => substr($w, 0, 1))->join('') : '';
                @endphp
                <div id="guidance-student-selected-preview" class="{{ $initStudent ? '' : 'hidden' }} mb-2 p-3 bg-purple-50/90 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 rounded-2xl flex items-center justify-between transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <div id="guidance-student-preview-avatar" class="w-9 h-9 rounded-xl bg-purple-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                            {{ $initInitials ? strtoupper(substr($initInitials, 0, 2)) : '-' }}
                        </div>
                        <div class="min-w-0">
                            <div id="guidance-student-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-xs sm:text-sm">
                                {{ $initStudent?->name }}
                            </div>
                            <div id="guidance-student-preview-meta" class="text-[11px] text-purple-700 dark:text-purple-300 truncate">
                                @if($initStudent)
                                    Kelas {{ $initStudent->schoolClass?->name ?? 'Umum' }} • Total Pelanggaran: {{ $initStudent->totalViolationPoints() }} Poin
                                @endif
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="clearGuidanceStudentSelection()" class="px-2.5 py-1 text-xs font-bold text-purple-600 dark:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-950/50 rounded-lg transition shrink-0 cursor-pointer">
                        Ganti Siswa ✕
                    </button>
                </div>

                <!-- Search Input with Icon -->
                <div class="relative {{ $initStudent ? 'hidden' : '' }}" id="guidance-student-search-wrapper">
                    <input type="text" id="guidance_student_search_input"
                        placeholder="🔍 Ketik nama, NIS, kelas, atau inisial siswa (misal: MR, AF, Dani, XI-IPA)..."
                        autocomplete="off"
                        onfocus="openGuidanceStudentDropdown()"
                        oninput="filterGuidanceStudents(this.value)"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-purple-500 focus:bg-white dark:focus:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button type="button" id="guidance-student-search-clear" onclick="resetGuidanceStudentSearch()" class="hidden absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        ✕
                    </button>
                </div>

                <!-- Quick Alphabet Initials Filter Bar (A-Z Chips) -->
                <div class="mt-2 flex items-center gap-1 overflow-x-auto pb-1.5 pt-0.5 text-xs no-scrollbar {{ $initStudent ? 'hidden' : '' }}" id="guidance-initials-bar">
                    <button type="button" onclick="filterGuidanceByInitial('ALL')"
                        class="guidance-initial-chip px-2.5 py-1 rounded-lg bg-purple-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs" data-letter="ALL">
                        Semua
                    </button>
                    @foreach(range('A', 'Z') as $char)
                        <button type="button" onclick="filterGuidanceByInitial('{{ $char }}')"
                            class="guidance-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700" data-letter="{{ $char }}">
                            {{ $char }}
                        </button>
                    @endforeach
                </div>

                <!-- Dropdown Result List -->
                <div id="guidance-student-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-64 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl z-50 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($students as $s)
                        @php
                            $words = explode(' ', trim($s->name));
                            $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                            $firstChar = strtoupper(substr(trim($s->name), 0, 1));
                            $violPoints = $s->totalViolationPoints();
                        @endphp
                        <div class="guidance-student-item px-3.5 py-2.5 hover:bg-purple-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition"
                             data-id="{{ $s->id }}"
                             data-name="{{ $s->name }}"
                             data-nis="{{ $s->nis }}"
                             data-class="{{ $s->schoolClass?->name ?? 'Umum' }}"
                             data-initials="{{ strtolower($initials) }}"
                             data-firstletter="{{ $firstChar }}"
                             onclick="selectGuidanceStudent('{{ $s->id }}', '{{ addslashes($s->name) }}', 'Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • Total Pelanggaran: {{ $violPoints }} Poin', '{{ strtoupper(substr($initials, 0, 2)) }}')">
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <span class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($initials, 0, 2)) }}
                                </span>
                                <div class="truncate">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 truncate text-xs">{{ $s->name }}</div>
                                    <div class="text-[10px] text-slate-400">Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • NIS: {{ $s->nis }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-bold">
                                    {{ $violPoints }} Poin
                                </span>
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono font-bold">
                                    [{{ strtoupper($initials) }}]
                                </span>
                            </div>
                        </div>
                    @endforeach
                    <div id="guidance-student-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                        Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut.
                    </div>
                </div>
            </div>

            <!-- Follow-up Type (Ambang Pembinaan) -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Tahapan / Tingkat Pembinaan *</label>
                <select name="follow_up_type" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-purple-500 font-bold text-purple-700">
                    <option value="BIMBINGAN_WALI_KELAS">Bimbingan Wali Kelas (Ambang 75 - 124 Poin)</option>
                    <option value="SP1_BK">Surat Peringatan 1 - BK (Ambang 125 - 175 Poin)</option>
                    <option value="SP2_BK">Surat Peringatan 2 - BK (Ambang 176 - 199 Poin)</option>
                    <option value="SP3_BK_KESISWAAN">Surat Peringatan 3 - BK & Kesiswaan (Ambang 200 Poin)</option>
                    <option value="RAPAT_KHUSUS">Rapat Khusus Dewan Guru & Komite (> 200 Poin)</option>
                    <option value="KONSELING_RUTIN">Konseling Rutin / Konseling Perkembangan</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Pelaksanaan Bimbingan *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kasus Pelanggaran Terkait (Opsional)</label>
                    <select name="violation_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-purple-500">
                        <option value="">-- Tidak Terikat Kasus Spesifik --</option>
                        @foreach($violations as $v)
                            <option value="{{ $v->id }}">{{ $v->title }} ({{ $v->points }} Poin - {{ $v->date->format('d/m/Y') }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Rekomendasi & Kesepakatan Tindak Lanjut *</label>
                <textarea name="recommendation" required rows="3" placeholder="Tuliskan rekomendasi tindakan, hasil diskusi dengan siswa/wali murid..."
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-purple-500"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Catatan Observasi Konselor</label>
                <textarea name="notes" rows="2" placeholder="Catatan respons emosional, sikap keterbukaan, atau progres..."
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-purple-500"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Upload Berkas / Surat Perjanjian Siswa (PDF/JPG/PNG)</label>
                <input type="file" name="agreement_letter_file" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-500">
                <p class="text-[10px] text-slate-400 mt-1">Surat pernyataan komitmen ditandatangani siswa dan orang tua</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                <a href="{{ route('guidance.index') }}" class="px-5 py-2.5 font-bold text-slate-500 hover:text-slate-800">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl shadow-md transition">
                    Simpan Catatan Pembinaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeGuidanceInitial = 'ALL';

    function openGuidanceStudentDropdown() {
        document.getElementById('guidance-student-dropdown').classList.remove('hidden');
    }

    function closeGuidanceStudentDropdown() {
        document.getElementById('guidance-student-dropdown').classList.add('hidden');
    }

    function filterGuidanceByInitial(letter) {
        activeGuidanceInitial = letter;
        document.querySelectorAll('.guidance-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === letter) {
                btn.className = 'guidance-initial-chip px-2.5 py-1 rounded-lg bg-purple-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
            } else {
                btn.className = 'guidance-initial-chip px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition hover:bg-slate-200 dark:hover:bg-slate-700';
            }
        });
        openGuidanceStudentDropdown();
        applyGuidanceFilters();
    }

    function filterGuidanceStudents(query) {
        openGuidanceStudentDropdown();
        applyGuidanceFilters();
    }

    function applyGuidanceFilters() {
        const input = document.getElementById('guidance_student_search_input');
        const q = (input?.value || '').trim().toLowerCase();
        const items = document.querySelectorAll('.guidance-student-item');
        let visibleCount = 0;

        const clearBtn = document.getElementById('guidance-student-search-clear');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', q.length === 0);
        }

        items.forEach(el => {
            const name = (el.getAttribute('data-name') || '').toLowerCase();
            const nis = (el.getAttribute('data-nis') || '').toLowerCase();
            const cls = (el.getAttribute('data-class') || '').toLowerCase();
            const initials = (el.getAttribute('data-initials') || '').toLowerCase();
            const firstLetter = (el.getAttribute('data-firstletter') || '').toUpperCase();

            const matchInitial = (activeGuidanceInitial === 'ALL') || (firstLetter === activeGuidanceInitial);
            const matchKeyword = !q || name.includes(q) || nis.includes(q) || cls.includes(q) || initials.includes(q) || initials.startsWith(q);

            if (matchInitial && matchKeyword) {
                el.classList.remove('hidden');
                visibleCount++;
            } else {
                el.classList.add('hidden');
            }
        });

        document.getElementById('guidance-student-empty').classList.toggle('hidden', visibleCount > 0);
    }

    function resetGuidanceStudentSearch() {
        const input = document.getElementById('guidance_student_search_input');
        if (input) {
            input.value = '';
            applyGuidanceFilters();
            input.focus();
        }
    }

    function selectGuidanceStudent(id, name, meta, initials) {
        document.getElementById('student_id').value = id;
        document.getElementById('guidance-student-preview-name').textContent = name;
        document.getElementById('guidance-student-preview-meta').textContent = meta;
        document.getElementById('guidance-student-preview-avatar').textContent = initials;

        document.getElementById('guidance-student-selected-preview').classList.remove('hidden');
        document.getElementById('guidance-student-search-wrapper').classList.add('hidden');
        document.getElementById('guidance-initials-bar').classList.add('hidden');
        closeGuidanceStudentDropdown();
    }

    function clearGuidanceStudentSelection() {
        document.getElementById('student_id').value = '';
        document.getElementById('guidance-student-selected-preview').classList.add('hidden');
        document.getElementById('guidance-student-search-wrapper').classList.remove('hidden');
        document.getElementById('guidance-initials-bar').classList.remove('hidden');
        resetGuidanceStudentSearch();
    }

    document.addEventListener('click', function(e) {
        const studentBox = document.getElementById('guidance-student-combobox');
        if (studentBox && !studentBox.contains(e.target)) {
            closeGuidanceStudentDropdown();
        }
    });
</script>
@endpush
