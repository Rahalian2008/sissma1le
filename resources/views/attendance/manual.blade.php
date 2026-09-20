@extends('layouts.app')

@section('title', 'Absen Manual Siswa')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Absensi Manual Siswa</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                @if(Auth::user()->role === 'wali_kelas')
                    Pencatatan absensi manual khusus untuk siswa kelas binaan <strong>{{ $homeroomClass?->name ?? 'Wali Kelas' }}</strong>.
                @else
                    Pencatatan dan penyesuaian absensi manual siswa untuk seluruh rombel/kelas.
                @endif
            </p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Absen Manual (Left / 2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
            <h2 class="text-base font-black text-slate-900 mb-5 flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    ✍️
                </span>
                <span>Form Input Absensi Manual</span>
            </h2>

            <form action="{{ route('attendance.manual.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Pilihan Kelas & Tanggal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kelas -->
                    <div>
                        <label for="class_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Kelas / Rombel <span class="text-rose-500">*</span>
                        </label>
                        @if(Auth::user()->role === 'wali_kelas')
                            <input type="hidden" name="class_id" value="{{ $homeroomClass?->id }}">
                            <div class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700">
                                {{ $homeroomClass?->name ?? 'Kelas Binaan' }} (Terkunci)
                            </div>
                        @else
                            <select name="class_id" id="class_id" onchange="window.location.href='{{ route('attendance.manual') }}?class_id=' + this.value"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 focus:bg-white focus:border-indigo-500 outline-none transition">
                                <option value="" disabled {{ !$selectedClassId ? 'selected' : '' }}>-- Pilih Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ (string)$selectedClassId === (string)$c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label for="date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Tanggal Presensi <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium focus:bg-white focus:border-indigo-500 outline-none transition">
                    </div>
                </div>

                <!-- Pilihan Siswa & Jam -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Siswa (2 cols) -->
                    <!-- Siswa (2 cols) - Searchable Combobox with Initials -->
                    <div class="sm:col-span-2 relative" id="manual-student-combobox">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Pilih Siswa <span class="text-rose-500">*</span>
                            <span class="text-[11px] font-normal text-slate-400 ml-1">(Bisa cari nama, NIS, atau filter inisial)</span>
                        </label>
                        <input type="hidden" name="student_id" id="student_id" required value="{{ old('student_id') }}">

                        <!-- Selected Student Card Preview -->
                        <div id="manual-student-selected-preview" class="hidden mb-2.5 p-3.5 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-between transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div id="manual-student-preview-avatar" class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                                    -
                                </div>
                                <div class="min-w-0">
                                    <div id="manual-student-preview-name" class="font-extrabold text-slate-900 truncate text-sm"></div>
                                    <div id="manual-student-preview-meta" class="text-xs text-indigo-700 truncate"></div>
                                </div>
                            </div>
                            <button type="button" onclick="clearManualStudentSelection()" class="px-3 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-100 rounded-xl transition shrink-0 cursor-pointer">
                                Ganti Siswa ✕
                            </button>
                        </div>

                        <!-- Search Input Wrapper -->
                        <div class="relative" id="manual-student-search-wrapper">
                            <input type="text" id="manual_student_search_input"
                                placeholder="🔍 Ketik nama, NIS, atau inisial siswa..."
                                autocomplete="off"
                                onfocus="openManualStudentDropdown()"
                                oninput="filterManualStudents(this.value)"
                                class="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 focus:bg-white focus:border-indigo-500 outline-none transition">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <button type="button" id="manual-student-search-clear" onclick="resetManualStudentSearch()" class="hidden absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 text-sm font-bold">
                                ✕
                            </button>
                        </div>

                        <!-- Quick Alphabet Initials Filter Bar (A-Z Chips) -->
                        <div class="mt-2 flex items-center gap-1 overflow-x-auto pb-1.5 pt-0.5 text-xs no-scrollbar" id="manual-initials-bar">
                            <button type="button" onclick="filterByManualInitial('ALL')"
                                class="manual-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition" data-letter="ALL">
                                Semua
                            </button>
                            @foreach(range('A', 'Z') as $char)
                                <button type="button" onclick="filterByManualInitial('{{ $char }}')"
                                    class="manual-initial-chip px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-[11px] shrink-0 transition" data-letter="{{ $char }}">
                                    {{ $char }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Dropdown List of Students -->
                        <div id="manual-student-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-64 overflow-y-auto rounded-2xl bg-white border border-slate-200 shadow-xl z-50 divide-y divide-slate-100">
                            @foreach($students as $s)
                                @php
                                    $words = explode(' ', trim($s->name));
                                    $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                                    $firstChar = strtoupper(substr(trim($s->name), 0, 1));
                                @endphp
                                <div class="manual-student-item px-3.5 py-2.5 hover:bg-indigo-50 cursor-pointer flex items-center justify-between transition"
                                     data-id="{{ $s->id }}"
                                     data-name="{{ $s->name }}"
                                     data-nis="{{ $s->nis }}"
                                     data-class="{{ $s->schoolClass?->name ?? 'Umum' }}"
                                     data-initials="{{ strtolower($initials) }}"
                                     data-firstletter="{{ $firstChar }}"
                                     onclick="selectManualStudent('{{ $s->id }}', '{{ addslashes($s->name) }}', 'Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • NIS: {{ $s->nis }}', '{{ strtoupper(substr($initials, 0, 2)) }}')">
                                    <div class="flex items-center gap-2.5 truncate mr-2">
                                        <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($initials, 0, 2)) }}
                                        </span>
                                        <div class="truncate">
                                            <div class="font-bold text-slate-800 truncate text-xs sm:text-sm">{{ $s->name }}</div>
                                            <div class="text-[11px] text-slate-400">Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • NIS: {{ $s->nis }}</div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono shrink-0 font-bold">
                                        [{{ strtoupper($initials) }}]
                                    </span>
                                </div>
                            @endforeach
                            <div id="manual-student-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                                Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut.
                            </div>
                        </div>
                    </div>

                    <!-- Jam -->
                    <div>
                        <label for="time" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Jam Masuk / Absen
                        </label>
                        <input type="time" name="time" id="time" value="{{ date('H:i') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium focus:bg-white focus:border-indigo-500 outline-none transition">
                    </div>
                </div>

                <!-- Status Presensi (Radio Chips) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Presensi <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                        <!-- Hadir -->
                        <label class="relative flex flex-col items-center justify-center p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-emerald-500 has-checked:bg-emerald-50 has-checked:text-emerald-900 border-slate-200 hover:border-slate-300 text-slate-700 text-center">
                            <input type="radio" name="status" value="HADIR" class="sr-only" checked>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mb-1"></span>
                            <span class="text-xs font-extrabold">HADIR</span>
                        </label>

                        <!-- Terlambat -->
                        <label class="relative flex flex-col items-center justify-center p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-amber-500 has-checked:bg-amber-50 has-checked:text-amber-900 border-slate-200 hover:border-slate-300 text-slate-700 text-center">
                            <input type="radio" name="status" value="TERLAMBAT" class="sr-only">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mb-1"></span>
                            <span class="text-xs font-extrabold">TERLAMBAT</span>
                        </label>

                        <!-- Izin -->
                        <label class="relative flex flex-col items-center justify-center p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-blue-500 has-checked:bg-blue-50 has-checked:text-blue-900 border-slate-200 hover:border-slate-300 text-slate-700 text-center">
                            <input type="radio" name="status" value="IZIN" class="sr-only">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mb-1"></span>
                            <span class="text-xs font-extrabold">IZIN</span>
                        </label>

                        <!-- Sakit -->
                        <label class="relative flex flex-col items-center justify-center p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-purple-500 has-checked:bg-purple-50 has-checked:text-purple-900 border-slate-200 hover:border-slate-300 text-slate-700 text-center">
                            <input type="radio" name="status" value="SAKIT" class="sr-only">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500 mb-1"></span>
                            <span class="text-xs font-extrabold">SAKIT</span>
                        </label>

                        <!-- Alpa -->
                        <label class="relative flex flex-col items-center justify-center p-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-rose-500 has-checked:bg-rose-50 has-checked:text-rose-900 border-slate-200 hover:border-slate-300 text-slate-700 text-center">
                            <input type="radio" name="status" value="ALPA" class="sr-only">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mb-1"></span>
                            <span class="text-xs font-extrabold">ALPA</span>
                        </label>
                    </div>
                </div>

                <!-- Catatan / Alasan Manual -->
                <div>
                    <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Alasan / Catatan Absen Manual
                    </label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Contoh: Siswa lupa membawa kartu RFID / Izin orang tua lewat telepon / Dispensasi tugas sekolah..."
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium focus:bg-white focus:border-indigo-500 outline-none transition"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold rounded-2xl shadow-lg shadow-indigo-600/20 text-sm tracking-wide transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Absensi Manual
                    </button>
                </div>
            </form>
        </div>

        <!-- Panduan & Ketentuan (Right / 1 Col) -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-3xl p-6 shadow-xl border border-indigo-800/40 space-y-4">
                <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-bold text-base">
                    ℹ️
                </div>
                <h3 class="font-black text-white text-base">Ketentuan Absensi Manual</h3>
                <ul class="text-xs text-indigo-200/90 space-y-2.5 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-bold">•</span>
                        <span>Absen manual langsung tervalidasi resmi (status <strong>DIVERIFIKASI</strong>).</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-bold">•</span>
                        <span>Nama pencatat dan waktu input tersimpan secara otomatis dalam audit trail sistem.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-bold">•</span>
                        <span>Wali Kelas berwenang menginput untuk kelas binaannya. Administrator memiliki akses ke seluruh rombel sekolah.</span>
                    </li>
                </ul>
            </div>

            <!-- Ringkasan Cepat Rombel Aktif -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h4 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider mb-3">Info Kelas Terpilih</h4>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Jumlah Siswa:</span>
                        <span class="font-bold text-slate-800">{{ count($students) }} Siswa</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Tahun Pelajaran:</span>
                        <span class="font-bold text-slate-800">2026/2027</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Metode Tersimpan:</span>
                        <span class="font-bold text-indigo-600">MANUAL</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Absen Manual Terakhir -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <h3 class="font-extrabold text-slate-900 text-base mb-4 flex items-center justify-between">
            <span>Riwayat Absen Manual Terbaru</span>
            <span class="text-xs font-semibold text-slate-500">{{ count($recentManuals) }} Data Terakhir</span>
        </h3>

        @if(count($recentManuals) === 0)
            <div class="p-8 text-center text-slate-400 text-xs">
                Belum ada catatan presensi manual untuk filter kelas ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Tanggal & Jam</th>
                            <th class="px-4 py-3">Siswa</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Catatan / Alasan</th>
                            <th class="px-4 py-3">Pencatat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentManuals as $item)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-3 font-medium whitespace-nowrap">
                                    {{ $item->date->format('d/m/Y') }} • {{ substr($item->time, 0, 5) }} WIB
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800">
                                    {{ $item->student_name }}
                                    <div class="text-[10px] font-normal text-slate-400">NIS: {{ $item->nis }}</div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-600 whitespace-nowrap">
                                    {{ $item->schoolClass?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold
                                        @if($item->status === 'HADIR') bg-emerald-100 text-emerald-800
                                        @elseif($item->status === 'TERLAMBAT') bg-amber-100 text-amber-800
                                        @elseif($item->status === 'IZIN') bg-blue-100 text-blue-800
                                        @elseif($item->status === 'SAKIT') bg-purple-100 text-purple-800
                                        @else bg-rose-100 text-rose-800 @endif">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $item->notes ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-500 whitespace-nowrap">
                                    {{ $item->approver?->name ?? 'Administrator' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
    let activeManualInitial = 'ALL';

    function openManualStudentDropdown() {
        const dropdown = document.getElementById('manual-student-dropdown');
        if (dropdown) {
            dropdown.classList.remove('hidden');
        }
    }

    function closeManualStudentDropdown() {
        const dropdown = document.getElementById('manual-student-dropdown');
        if (dropdown) {
            dropdown.classList.add('hidden');
        }
    }

    function resetManualStudentSearch() {
        const input = document.getElementById('manual_student_search_input');
        if (input) {
            input.value = '';
            filterManualStudents('');
            document.getElementById('manual-student-search-clear').classList.add('hidden');
            input.focus();
        }
    }

    function filterByManualInitial(letter) {
        activeManualInitial = letter;

        // Update button styles
        document.querySelectorAll('.manual-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === letter) {
                btn.className = 'manual-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition shadow-xs';
            } else {
                btn.className = 'manual-initial-chip px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-[11px] shrink-0 transition';
            }
        });

        openManualStudentDropdown();
        applyManualStudentFilters();
    }

    function filterManualStudents(keyword) {
        const clearBtn = document.getElementById('manual-student-search-clear');
        if (clearBtn) {
            if (keyword.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }
        applyManualStudentFilters();
    }

    function applyManualStudentFilters() {
        const keyword = (document.getElementById('manual_student_search_input')?.value || '').toLowerCase().trim();
        const items = document.querySelectorAll('.manual-student-item');
        let visibleCount = 0;

        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            const nis = (item.getAttribute('data-nis') || '').toLowerCase();
            const initials = (item.getAttribute('data-initials') || '').toLowerCase();
            const firstLetter = (item.getAttribute('data-firstletter') || '').toUpperCase();

            const matchInitial = (activeManualInitial === 'ALL') || (firstLetter === activeManualInitial);
            const matchKeyword = !keyword || name.includes(keyword) || nis.includes(keyword) || initials.includes(keyword);

            if (matchInitial && matchKeyword) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const emptyNotice = document.getElementById('manual-student-empty');
        if (emptyNotice) {
            emptyNotice.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function selectManualStudent(id, name, meta, initials) {
        document.getElementById('student_id').value = id;

        // Update card preview
        document.getElementById('manual-student-preview-name').innerText = name;
        document.getElementById('manual-student-preview-meta').innerText = meta;
        document.getElementById('manual-student-preview-avatar').innerText = initials;

        document.getElementById('manual-student-selected-preview').classList.remove('hidden');
        document.getElementById('manual-student-search-wrapper').classList.add('hidden');
        document.getElementById('manual-initials-bar').classList.add('hidden');
        closeManualStudentDropdown();
    }

    function clearManualStudentSelection() {
        document.getElementById('student_id').value = '';
        document.getElementById('manual-student-selected-preview').classList.add('hidden');
        document.getElementById('manual-student-search-wrapper').classList.remove('hidden');
        document.getElementById('manual-initials-bar').classList.remove('hidden');

        const searchInput = document.getElementById('manual_student_search_input');
        if (searchInput) {
            searchInput.value = '';
            filterManualStudents('');
            searchInput.focus();
        }
    }

    // Close dropdown when clicked outside
    document.addEventListener('click', function (e) {
        const combobox = document.getElementById('manual-student-combobox');
        if (combobox && !combobox.contains(e.target)) {
            closeManualStudentDropdown();
        }
    });
</script>
@endsection
