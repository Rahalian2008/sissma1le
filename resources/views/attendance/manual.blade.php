@extends('layouts.app')

@push('styles')
<style>
    /* Styling radio presensi manual saat checked */
    .roster-radio-hadir:checked + span {
        background-color: #059669 !important;
        border-color: #047857 !important;
        color: #ffffff !important;
    }
    .roster-radio-terlambat:checked + span {
        background-color: #d97706 !important;
        border-color: #b45309 !important;
        color: #ffffff !important;
    }
    .roster-radio-izin:checked + span {
        background-color: #2563eb !important;
        border-color: #1d4ed8 !important;
        color: #ffffff !important;
    }
    .roster-radio-sakit:checked + span {
        background-color: #9333ea !important;
        border-color: #7e22ce !important;
        color: #ffffff !important;
    }
    .roster-radio-alpa:checked + span {
        background-color: #e11d48 !important;
        border-color: #be123c !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    @include('attendance.partials.nav')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Absensi Manual Siswa
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pencatatan dan penyesuaian absensi manual siswa untuk seluruh rombel/kelas.
            </p>
        </div>

        <!-- Mode Toggle -->
        <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl border border-slate-200 dark:border-slate-700">
            <button type="button" onclick="switchManualMode('form')" id="tab-form-btn"
                class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-indigo-600 text-white shadow-xs">
                <span>✍️ Form Input Absensi</span>
            </button>
            <button type="button" onclick="switchManualMode('roster')" id="tab-roster-btn"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">
                <span>📋 Tabel Rombel</span>
            </button>
        </div>
    </div>

    @if(isset($schedule))
    <!-- Schedule Info Banner -->
    <div class="p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full {{ $schedule['is_active'] ? 'bg-amber-500 animate-pulse' : 'bg-rose-500' }}"></span>
            <span class="font-bold text-slate-800 dark:text-slate-100">Jadwal Operasional Hari {{ $schedule['day_name'] }}:</span>
            <span class="px-2 py-0.5 rounded-md font-bold text-[10.5px] {{ $schedule['is_active'] ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300' : 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300' }}">
                {{ $schedule['is_active'] ? 'Hari Aktif' : ($schedule['is_holiday'] ? 'Libur: '.$schedule['holiday_name'] : 'Hari Libur / Non-Aktif') }}
            </span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200/70 dark:border-emerald-800/70">
                🟢 Masuk: {{ $schedule['in_start'] }} - {{ $schedule['in_late_cutoff'] }} WIB (Tepat Waktu s.d. {{ $schedule['in_on_time'] }})
            </span>
            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200/70 dark:border-indigo-800/70">
                🔵 Pulang: {{ $schedule['out_start'] }} - {{ $schedule['out_end'] }} WIB
            </span>
        </div>
    </div>
    @endif

    @if(!($isEnabled ?? true))
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <span class="font-bold">Metode Presensi Manual Sedang Dinonaktifkan</span>
                <p class="text-[11px] text-amber-700 dark:text-amber-300 mt-0.5">Pihak sekolah saat ini menonaktifkan fitur absensi manual. Silakan aktifkan metode presensi manual di menu Pengaturan Sekolah jika ingin menggunakannya.</p>
            </div>
        </div>
    @endif

    <!-- ==================== MODE UTAMA: FORM INPUT ABSENSI MANUAL (SESUAI MOCKUP) ==================== -->
    <div id="mode-form-section" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-6">
        <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm shadow-2xs">
                ✍️
            </span>
            <span>Form Input Absensi Manual</span>
        </h2>

        <form action="{{ route('attendance.manual.store') }}" method="POST" enctype="multipart/form-data" id="singleAttendanceForm" class="space-y-6">
            @csrf
            <input type="hidden" name="selfie_image" id="single_selfie_image">

            <!-- ROW 1: KELAS / ROMBEL * & TANGGAL PRESENSI * -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label for="manual_form_class_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        KELAS / ROMBEL <span class="text-rose-500">*</span>
                    </label>
                    <select name="class_id" id="manual_form_class_id" onchange="onManualClassChange(this.value)"
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold text-slate-800 dark:text-slate-200 outline-none focus:border-indigo-500 transition cursor-pointer">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$selectedClassId === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="manual_form_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        TANGGAL PRESENSI <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="date" id="manual_form_date" value="{{ $selectedDate }}"
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold text-slate-800 dark:text-slate-200 outline-none focus:border-indigo-500 transition cursor-pointer">
                </div>
            </div>

            <!-- ROW 2: PILIH SISWA * & JAM MASUK / ABSEN -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- PILIH SISWA (col-span-2) -->
                <div class="lg:col-span-2 relative" id="manual-student-combobox">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        PILIH SISWA <span class="text-rose-500">*</span>
                        <span class="text-[11px] font-normal text-slate-400 ml-1">(BISA CARI NAMA, NIS, ATAU FILTER INISIAL)</span>
                    </label>
                    <input type="hidden" name="student_id" id="student_id" required value="{{ old('student_id') }}">

                    <!-- Selected Student Preview -->
                    <div id="manual-student-selected-preview" class="hidden mb-2.5 p-3.5 bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-900 rounded-2xl flex items-center justify-between transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div id="manual-student-preview-avatar" class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                                -
                            </div>
                            <div class="min-w-0">
                                <div id="manual-student-preview-name" class="font-extrabold text-slate-900 dark:text-white truncate text-sm"></div>
                                <div id="manual-student-preview-meta" class="text-xs text-indigo-700 dark:text-indigo-300 truncate"></div>
                            </div>
                        </div>
                        <button type="button" onclick="clearManualStudentSelection()" class="px-3 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-950 rounded-xl transition shrink-0 cursor-pointer">
                            Ganti Siswa ✕
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative" id="manual-student-search-wrapper">
                        <input type="text" id="manual_student_search_input"
                            placeholder="🔍 Ketik nama, NIS, atau inisial siswa..."
                            autocomplete="off"
                            onfocus="openManualStudentDropdown()"
                            oninput="filterManualStudents(this.value)"
                            class="w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-semibold text-slate-700 dark:text-slate-200 focus:bg-white focus:border-indigo-500 outline-none transition">
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
                                class="manual-initial-chip px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition" data-letter="{{ $char }}">
                                {{ $char }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Dropdown List of Students -->
                    <div id="manual-student-dropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-64 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl z-50 divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($allStudents as $s)
                            @php
                                $words = explode(' ', trim($s->name));
                                $initials = collect($words)->map(fn($w) => substr($w, 0, 1))->join('');
                                $firstChar = strtoupper(substr(trim($s->name), 0, 1));
                            @endphp
                            <div class="manual-student-item px-3.5 py-2.5 hover:bg-indigo-50 dark:hover:bg-indigo-950 cursor-pointer flex items-center justify-between transition"
                                 data-id="{{ $s->id }}"
                                 data-class-id="{{ $s->class_id }}"
                                 data-name="{{ $s->name }}"
                                 data-nis="{{ $s->nis }}"
                                 data-class="{{ $s->schoolClass?->name ?? 'Umum' }}"
                                 data-initials="{{ strtolower($initials) }}"
                                 data-firstletter="{{ $firstChar }}"
                                 onclick="selectManualStudent('{{ $s->id }}', '{{ addslashes($s->name) }}', 'Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • NIS: {{ $s->nis }}', '{{ strtoupper(substr($initials, 0, 2)) }}')">
                                <div class="flex items-center gap-2.5 truncate mr-2">
                                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($initials, 0, 2)) }}
                                    </span>
                                    <div class="truncate">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 truncate text-xs sm:text-sm">{{ $s->name }}</div>
                                        <div class="text-[11px] text-slate-400">Kelas {{ $s->schoolClass?->name ?? 'Umum' }} • NIS: {{ $s->nis }}</div>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-mono shrink-0 font-bold">
                                    [{{ strtoupper($initials) }}]
                                </span>
                            </div>
                        @endforeach
                        <div id="manual-student-empty" class="hidden p-4 text-center text-xs text-slate-400 italic">
                            Tidak ada siswa yang cocok dengan inisial atau kata kunci tersebut pada kelas terpilih.
                        </div>
                    </div>
                </div>

                <!-- JAM MASUK / ABSEN (col-span-1) -->
                <div>
                    <label for="single_time" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        JAM MASUK / ABSEN
                    </label>
                    <input type="time" name="time" id="single_time" value="{{ date('H:i') }}"
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold text-slate-800 dark:text-white focus:bg-white focus:border-indigo-500 outline-none transition">
                </div>
            </div>

            <!-- ROW 3: STATUS PRESENSI * (5 PILL CARDS SEPERTI MOCKUP) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">
                    STATUS PRESENSI <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    <label class="relative flex items-center justify-center gap-2 py-3.5 px-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-emerald-500 has-checked:bg-emerald-50 dark:has-checked:bg-emerald-950/60 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <input type="radio" name="status" value="HADIR" class="sr-only" checked>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200 tracking-wide">HADIR</span>
                    </label>
                    <label class="relative flex items-center justify-center gap-2 py-3.5 px-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-amber-500 has-checked:bg-amber-50 dark:has-checked:bg-amber-950/60 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <input type="radio" name="status" value="TERLAMBAT" class="sr-only">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200 tracking-wide">TERLAMBAT</span>
                    </label>
                    <label class="relative flex items-center justify-center gap-2 py-3.5 px-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-blue-500 has-checked:bg-blue-50 dark:has-checked:bg-blue-950/60 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <input type="radio" name="status" value="IZIN" class="sr-only">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200 tracking-wide">IZIN</span>
                    </label>
                    <label class="relative flex items-center justify-center gap-2 py-3.5 px-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-purple-500 has-checked:bg-purple-50 dark:has-checked:bg-purple-950/60 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <input type="radio" name="status" value="SAKIT" class="sr-only">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shrink-0"></span>
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200 tracking-wide">SAKIT</span>
                    </label>
                    <label class="relative flex items-center justify-center gap-2 py-3.5 px-3 rounded-2xl border-2 cursor-pointer transition has-checked:border-rose-500 has-checked:bg-rose-50 dark:has-checked:bg-rose-950/60 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <input type="radio" name="status" value="ALPA" class="sr-only">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200 tracking-wide">ALPA</span>
                    </label>
                </div>
            </div>

            <!-- ROW 4: ALASAN / CATATAN ABSEN MANUAL -->
            <div>
                <label for="single_notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    ALASAN / CATATAN ABSEN MANUAL
                </label>
                <textarea name="notes" id="single_notes" rows="3"
                    placeholder="Contoh: Siswa lupa membawa kartu RFID / Izin orang tua lewat telepon / Dispensasi tugas sekolah..."
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-indigo-500 outline-none transition"></textarea>
            </div>

            <!-- FITUR FOTO SELFIE SISWA (LIVE WEBCAM & FALLBACK) -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">
                            Foto Selfie Siswa / Bukti Fisik @if($isSecretary)<span class="text-rose-500">*</span>@else<span class="text-slate-400 font-normal text-[11px]">(Opsional)</span>@endif
                        </label>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            Ambil foto wajah siswa secara langsung dengan kamera perangkat atau unggah file.
                        </p>
                    </div>
                    <span id="selfie-status-pill" class="hidden px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                        ✓ Foto Terlampir
                    </span>
                </div>

                <!-- Camera / Preview Box -->
                <div class="relative w-full max-w-sm mx-auto aspect-4/3 bg-slate-950 rounded-2xl overflow-hidden shadow-inner border-2 border-slate-800 flex items-center justify-center">
                    <!-- Live Video Element -->
                    <video id="manual-camera-feed" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100 hidden"></video>
                    
                    <!-- Captured Image Preview -->
                    <img id="manual-selfie-preview" class="hidden w-full h-full object-cover" alt="Preview Foto Selfie">

                    <!-- Hidden Canvas -->
                    <canvas id="manual-camera-canvas" class="hidden"></canvas>

                    <!-- Face Oval Guide -->
                    <div id="manual-face-guide" class="hidden absolute inset-0 pointer-events-none flex flex-col items-center justify-center p-3">
                        <div class="w-32 h-44 border-2 border-dashed border-emerald-400/80 rounded-full flex items-center justify-center">
                            <span class="text-[9px] font-bold text-emerald-300 bg-slate-950/80 px-2 py-0.5 rounded-full">
                                Wajah Siswa
                            </span>
                        </div>
                    </div>

                    <!-- Placeholder when camera is inactive -->
                    <div id="manual-camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center p-4 text-center text-white bg-slate-900/90">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-slate-200">Kamera Belum Aktif</p>
                        <p class="text-[10px] text-slate-400 mt-0.5 mb-3">Klik tombol untuk menyalakan kamera live selfie</p>
                        <button type="button" onclick="startManualCamera()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>Buka Kamera</span>
                        </button>
                    </div>
                </div>

                <!-- Camera Control Buttons -->
                <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                    <button type="button" id="btn-manual-snap" onclick="takeManualSnapshot()" class="hidden px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-xs transition items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Jepret Foto</span>
                    </button>

                    <button type="button" id="btn-manual-flip" onclick="flipManualCamera()" class="hidden px-3 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-xs transition items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Putar Kamera</span>
                    </button>

                    <button type="button" id="btn-manual-retake" onclick="retakeManualSnapshot()" class="hidden px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 font-bold rounded-xl text-xs border border-rose-200 dark:border-rose-900 transition items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Ambil Ulang</span>
                    </button>

                    <!-- Upload File Fallback -->
                    <label class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Unggah File Foto</span>
                        <input type="file" name="selfie_file" accept="image/*" class="sr-only" onchange="handleSelfieFileSelect(this)">
                    </label>
                </div>
            </div>

            <!-- ROW 5: SUBMIT BUTTON (FULL WIDTH PURPLE SEPERTI MOCKUP) -->
            <div>
                @if(!($isEnabled ?? true))
                    <button type="button" disabled
                        class="w-full py-4 bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400 font-black rounded-2xl cursor-not-allowed text-base tracking-wide flex items-center justify-center gap-2">
                        <span>Presensi Manual Dinonaktifkan</span>
                    </button>
                @else
                    <button type="submit"
                        class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.99] text-white font-black rounded-2xl shadow-lg shadow-indigo-500/25 text-base tracking-wide transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Absensi Manual</span>
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- ==================== MODE 2: TABEL PRESIENSI ROMBEL (OPSIONAL) ==================== -->
    <div id="mode-roster-section" class="hidden space-y-6">
        <!-- Filter Kelas & Tanggal Roster -->
        <form method="GET" action="{{ route('attendance.manual') }}" id="rosterFilterForm" class="flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-400 uppercase">Pilih Rombel:</span>
                <select name="class_id" onchange="document.getElementById('rosterFilterForm').submit()"
                    class="px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 outline-none">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ (string)$selectedClassId === (string)$c->id ? 'selected' : '' }}>
                            Kelas {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-400 uppercase">Tanggal:</span>
                <input type="date" name="date" value="{{ $selectedDate }}" onchange="document.getElementById('rosterFilterForm').submit()"
                    class="px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 outline-none">
            </div>
        </form>

        <!-- Form Masal Rombel -->
        @if($students->isNotEmpty())
        <form action="{{ route('attendance.manual.bulk') }}" method="POST" id="bulkAttendanceForm" class="space-y-4">
            @csrf
            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
            <input type="hidden" name="date" value="{{ $selectedDate }}">

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                            Roster Siswa Kelas {{ $selectedClass?->name }} ({{ $students->count() }} Siswa)
                        </span>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="setAllRosterStatus('HADIR')" class="text-xs px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold transition">
                            ✓ Semua Hadir
                        </button>
                        <button type="button" onclick="resetRosterStatus()" class="text-xs px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold transition">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Bilah Aksi Massal Pilihan Siswa Roster -->
                <div class="p-3 bg-gradient-to-r from-indigo-50/90 via-slate-50 to-indigo-50/90 dark:from-indigo-950/40 dark:via-slate-900/40 dark:to-indigo-950/40 border-b border-slate-200/80 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="flex items-center gap-2 cursor-pointer font-extrabold text-indigo-950 dark:text-indigo-200 select-none">
                            <input type="checkbox" id="check-all-roster" onchange="toggleSelectAllRoster(this.checked)" class="w-4 h-4 rounded text-indigo-600 focus:ring-0 cursor-pointer border-slate-300 dark:border-slate-700">
                            <span>Pilih Semua Siswa</span>
                        </label>
                        <span id="selected-roster-count" class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-800 dark:text-indigo-300 transition">
                            0 Terpilih
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mr-0.5">Terapkan ke Siswa Terpilih:</span>
                        <button type="button" onclick="setRosterStatusesForSelected('HADIR')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>✓</span><span>HADIR</span>
                        </button>
                        <button type="button" onclick="setRosterStatusesForSelected('TERLAMBAT')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-amber-600 hover:bg-amber-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>⏰</span><span>TERLAMBAT</span>
                        </button>
                        <button type="button" onclick="setRosterStatusesForSelected('IZIN')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>📝</span><span>IZIN</span>
                        </button>
                        <button type="button" onclick="setRosterStatusesForSelected('SAKIT')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-purple-600 hover:bg-purple-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>🏥</span><span>SAKIT</span>
                        </button>
                        <button type="button" onclick="setRosterStatusesForSelected('ALPA')" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-white shadow-xs transition cursor-pointer flex items-center gap-1">
                            <span>❌</span><span>ALPA</span>
                        </button>
                        <button type="button" onclick="clearRosterStatusesForSelected()" class="text-[11px] font-bold px-2 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-3 w-16 text-center">
                                    <span class="sr-only">Pilih</span>
                                </th>
                                <th class="px-4 py-3">Nama Siswa</th>
                                <th class="px-4 py-3 text-center">Status Kehadiran</th>
                                <th class="px-4 py-3">Catatan / Alasan</th>
                            </tr>
                        </thead>
                        <tbody id="rosterTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($students as $idx => $st)
                                @php
                                    $existingAtt = $attendances->get($st->id);
                                    $currentStatus = $existingAtt?->status ?? '';
                                    $currentNote = $existingAtt?->notes ?? '';
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition roster-row">
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <input type="checkbox" class="roster-checkbox w-4 h-4 rounded text-indigo-600 focus:ring-0 cursor-pointer border-slate-300 dark:border-slate-700" data-student-id="{{ $st->id }}" onchange="updateSelectedRosterCounter()">
                                            <span class="font-bold text-slate-400 font-mono text-[11px]">{{ $idx + 1 }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-extrabold text-slate-800 dark:text-white">{{ $st->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">NIS: {{ $st->nis }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1.5">
                                            @foreach(['HADIR', 'TERLAMBAT', 'IZIN', 'SAKIT', 'ALPA'] as $stVal)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="statuses[{{ $st->id }}]" value="{{ $stVal }}"
                                                        class="roster-radio-{{ strtolower($stVal) }} sr-only"
                                                        {{ $currentStatus === $stVal ? 'checked' : '' }}>
                                                    <span class="px-2 py-1 rounded-lg border text-[10px] font-bold block transition border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300">
                                                        {{ $stVal[0] }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="notes[{{ $st->id }}]" value="{{ $currentNote }}"
                                            placeholder="Catatan..."
                                            class="w-full px-2.5 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs outline-none">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-xs transition">
                        ✓ Simpan Absen Rombel
                    </button>
                </div>
            </div>
        </form>
        @endif
    </div>

    <!-- Riwayat Absen Manual Terakhir -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xs p-6">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-base mb-4 flex items-center justify-between">
            <span>Riwayat Absen Manual Terbaru</span>
            <span class="text-xs font-semibold text-slate-500">{{ count($recentManuals) }} Data Terakhir</span>
        </h3>

        @if(count($recentManuals) === 0)
            <div class="p-8 text-center text-slate-400 dark:text-slate-500 text-xs">
                Belum ada catatan presensi manual untuk filter kelas ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Tanggal & Jam</th>
                            <th class="px-4 py-3">Siswa</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Catatan / Alasan</th>
                            <th class="px-4 py-3">Pencatat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($recentManuals as $item)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                <td class="px-4 py-3 font-medium whitespace-nowrap text-slate-700 dark:text-slate-300">
                                    {{ $item->date->format('d/m/Y') }} • {{ substr($item->time, 0, 5) }} WIB
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800 dark:text-white">
                                    <div class="flex items-center gap-2.5">
                                        @if($item->selfie_path)
                                            <a href="{{ asset('storage/' . $item->selfie_path) }}" target="_blank" class="w-8 h-8 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 shadow-2xs hover:scale-110 transition shrink-0" title="Foto Selfie Siswa">
                                                <img src="{{ asset('storage/' . $item->selfie_path) }}" class="w-full h-full object-cover" alt="Selfie">
                                            </a>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="truncate">{{ $item->student_name }}</div>
                                            <div class="text-[10px] font-normal text-slate-400">NIS: {{ $item->nis }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                    {{ $item->schoolClass?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold
                                        @if($item->status === 'HADIR') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                        @elseif($item->status === 'TERLAMBAT') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                        @elseif($item->status === 'IZIN') bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300
                                        @elseif($item->status === 'SAKIT') bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300
                                        @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 max-w-xs truncate">
                                    {{ $item->notes ?: '-' }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-500 whitespace-nowrap">
                                    {{ $item->approver?->name ?? 'Sistem' }}
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
    let activeManualClassId = "{{ $selectedClassId }}";
    let activeManualInitial = 'ALL';
    let manualVideoStream = null;
    let manualFacingMode = 'user';

    function switchManualMode(mode) {
        const formSec = document.getElementById('mode-form-section');
        const rosterSec = document.getElementById('mode-roster-section');
        const formBtn = document.getElementById('tab-form-btn');
        const rosterBtn = document.getElementById('tab-roster-btn');

        if (mode === 'form') {
            formSec.classList.remove('hidden');
            rosterSec.classList.add('hidden');
            formBtn.className = 'px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-indigo-600 text-white shadow-xs';
            rosterBtn.className = 'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white';
        } else {
            formSec.classList.add('hidden');
            rosterSec.classList.remove('hidden');
            rosterBtn.className = 'px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-indigo-600 text-white shadow-xs';
            formBtn.className = 'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white';
        }
    }

    function onManualClassChange(classId) {
        activeManualClassId = classId;
        clearManualStudentSelection();
        applyManualStudentFilters();
    }

    function openManualStudentDropdown() {
        const dropdown = document.getElementById('manual-student-dropdown');
        if (dropdown) dropdown.classList.remove('hidden');
    }

    function closeManualStudentDropdown() {
        const dropdown = document.getElementById('manual-student-dropdown');
        if (dropdown) dropdown.classList.add('hidden');
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
        document.querySelectorAll('.manual-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === letter) {
                btn.className = 'manual-initial-chip px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[11px] shrink-0 transition shadow-2xs';
            } else {
                btn.className = 'manual-initial-chip px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-[11px] shrink-0 transition';
            }
        });
        openManualStudentDropdown();
        applyManualStudentFilters();
    }

    function filterManualStudents(keyword) {
        const clearBtn = document.getElementById('manual-student-search-clear');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', !keyword || keyword.length === 0);
        }
        applyManualStudentFilters();
    }

    function applyManualStudentFilters() {
        const keyword = (document.getElementById('manual_student_search_input')?.value || '').toLowerCase().trim();
        const items = document.querySelectorAll('.manual-student-item');
        let visibleCount = 0;

        items.forEach(item => {
            const itemClassId = item.getAttribute('data-class-id');
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            const nis = (item.getAttribute('data-nis') || '').toLowerCase();
            const initials = (item.getAttribute('data-initials') || '').toLowerCase();
            const firstLetter = (item.getAttribute('data-firstletter') || '').toUpperCase();

            const matchClass = !activeManualClassId || (String(itemClassId) === String(activeManualClassId));
            const matchInitial = (activeManualInitial === 'ALL') || (firstLetter === activeManualInitial);
            const matchKeyword = !keyword || name.includes(keyword) || nis.includes(keyword) || initials.includes(keyword);

            if (matchClass && matchInitial && matchKeyword) {
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

    document.addEventListener('click', function (e) {
        const combobox = document.getElementById('manual-student-combobox');
        if (combobox && !combobox.contains(e.target)) {
            closeManualStudentDropdown();
        }
    });

    // Camera Selfie Handling
    async function startManualCamera() {
        try {
            if (manualVideoStream) {
                manualVideoStream.getTracks().forEach(track => track.stop());
            }
            manualVideoStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: manualFacingMode,
                    width: { ideal: 720 },
                    height: { ideal: 720 }
                },
                audio: false
            });
            const video = document.getElementById('manual-camera-feed');
            video.srcObject = manualVideoStream;
            video.classList.remove('hidden');
            document.getElementById('manual-camera-placeholder').classList.add('hidden');
            document.getElementById('manual-selfie-preview').classList.add('hidden');
            document.getElementById('manual-face-guide').classList.remove('hidden');
            
            const btnSnap = document.getElementById('btn-manual-snap');
            btnSnap.classList.remove('hidden');
            btnSnap.classList.add('inline-flex');
            
            const btnFlip = document.getElementById('btn-manual-flip');
            btnFlip.classList.remove('hidden');
            btnFlip.classList.add('inline-flex');
            
            const btnRetake = document.getElementById('btn-manual-retake');
            btnRetake.classList.add('hidden');
            btnRetake.classList.remove('inline-flex');
        } catch (err) {
            alert("Gagal mengakses kamera: " + err.message + ". Silakan periksa izin kamera atau gunakan tombol Unggah File Foto.");
        }
    }

    function flipManualCamera() {
        manualFacingMode = manualFacingMode === 'user' ? 'environment' : 'user';
        const video = document.getElementById('manual-camera-feed');
        if (manualFacingMode === 'user') {
            video.classList.add('-scale-x-100');
        } else {
            video.classList.remove('-scale-x-100');
        }
        startManualCamera();
    }

    function takeManualSnapshot() {
        const video = document.getElementById('manual-camera-feed');
        const canvas = document.getElementById('manual-camera-canvas');
        const preview = document.getElementById('manual-selfie-preview');
        const hiddenInput = document.getElementById('single_selfie_image');
        const pill = document.getElementById('selfie-status-pill');

        if (!video || !manualVideoStream) return;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');

        if (manualFacingMode === 'user') {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        hiddenInput.value = dataUrl;

        preview.src = dataUrl;
        preview.classList.remove('hidden');
        video.classList.add('hidden');
        document.getElementById('manual-face-guide').classList.add('hidden');

        document.getElementById('btn-manual-snap').classList.add('hidden');
        document.getElementById('btn-manual-snap').classList.remove('inline-flex');
        
        document.getElementById('btn-manual-flip').classList.add('hidden');
        document.getElementById('btn-manual-flip').classList.remove('inline-flex');

        const btnRetake = document.getElementById('btn-manual-retake');
        btnRetake.classList.remove('hidden');
        btnRetake.classList.add('inline-flex');

        if (pill) pill.classList.remove('hidden');
    }

    function retakeManualSnapshot() {
        document.getElementById('single_selfie_image').value = '';
        const pill = document.getElementById('selfie-status-pill');
        if (pill) pill.classList.add('hidden');
        startManualCamera();
    }

    function handleSelfieFileSelect(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('single_selfie_image').value = e.target.result;
            const preview = document.getElementById('manual-selfie-preview');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            document.getElementById('manual-camera-feed').classList.add('hidden');
            document.getElementById('manual-camera-placeholder').classList.add('hidden');
            document.getElementById('manual-face-guide').classList.add('hidden');
            const pill = document.getElementById('selfie-status-pill');
            if (pill) pill.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    // Multi-Select Students Checkboxes & Bulk Actions for Roster Mode
    function toggleSelectAllRoster(checked) {
        document.querySelectorAll('.roster-checkbox').forEach(cb => cb.checked = checked);
        updateSelectedRosterCounter();
    }

    function updateSelectedRosterCounter() {
        const allBoxes = document.querySelectorAll('.roster-checkbox');
        const checkedBoxes = document.querySelectorAll('.roster-checkbox:checked');
        const counterEl = document.getElementById('selected-roster-count');
        if (counterEl) {
            counterEl.textContent = `${checkedBoxes.length} Terpilih`;
            if (checkedBoxes.length > 0) {
                counterEl.classList.remove('bg-indigo-100', 'text-indigo-800', 'dark:bg-indigo-900/60', 'dark:text-indigo-300');
                counterEl.classList.add('bg-indigo-600', 'text-white');
            } else {
                counterEl.classList.remove('bg-indigo-600', 'text-white');
                counterEl.classList.add('bg-indigo-100', 'text-indigo-800', 'dark:bg-indigo-900/60', 'dark:text-indigo-300');
            }
        }
        const checkAll = document.getElementById('check-all-roster');
        if (checkAll) {
            checkAll.checked = (allBoxes.length > 0 && checkedBoxes.length === allBoxes.length);
            checkAll.indeterminate = (checkedBoxes.length > 0 && checkedBoxes.length < allBoxes.length);
        }
    }

    function setRosterStatusesForSelected(status) {
        const checkedBoxes = document.querySelectorAll('.roster-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Silakan centang satu atau beberapa siswa terlebih dahulu menggunakan kotak pilihan (checkbox).');
            return;
        }

        checkedBoxes.forEach(cb => {
            const studentId = cb.getAttribute('data-student-id');
            const radio = document.querySelector(`input[name="statuses[${studentId}]"][value="${status}"]`);
            if (radio) {
                radio.checked = true;
                const row = cb.closest('.roster-row');
                if (row) {
                    row.classList.add('bg-indigo-50/80', 'dark:bg-indigo-950/40');
                    setTimeout(() => row.classList.remove('bg-indigo-50/80', 'dark:bg-indigo-950/40'), 800);
                }
            }
        });
    }

    function clearRosterStatusesForSelected() {
        const checkedBoxes = document.querySelectorAll('.roster-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Silakan centang satu atau beberapa siswa terlebih dahulu.');
            return;
        }

        checkedBoxes.forEach(cb => {
            const studentId = cb.getAttribute('data-student-id');
            document.querySelectorAll(`input[name="statuses[${studentId}]"]`).forEach(r => r.checked = false);
        });
    }

    function setAllRosterStatus(status) {
        document.querySelectorAll('#rosterTableBody tr').forEach(row => {
            const radio = row.querySelector('.roster-radio-' + status.toLowerCase());
            if (radio) radio.checked = true;
        });
    }

    function resetRosterStatus() {
        document.querySelectorAll('#rosterTableBody input[type="radio"]').forEach(r => r.checked = false);
        document.querySelectorAll('.roster-checkbox').forEach(cb => cb.checked = false);
        updateSelectedRosterCounter();
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        applyManualStudentFilters();
    });
</script>
@endsection
