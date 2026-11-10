@extends('layouts.app')

@section('title', 'Integrasi & Simulator Scanner RFID')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-purple-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 text-xs font-bold border border-purple-500/30">
                INTEGRASI PERANGKAT KERAS
            </span>
            <h2 class="text-xl sm:text-2xl font-black text-white mt-1">Simulator & Pengujian Scanner RFID</h2>
            <p class="text-xs text-slate-300">Uji coba interaktif pengetukan kartu RFID fisik/kiosk yang terintegrasi dengan database pusat.</p>
        </div>
    </div>

    @if(isset($schedule))
    <!-- Schedule Info Banner -->
    <div class="p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full {{ $schedule['is_active'] ? 'bg-purple-500 animate-pulse' : 'bg-rose-500' }}"></span>
            <span class="font-bold text-slate-800 dark:text-slate-100">Jadwal Operasional Hari {{ $schedule['day_name'] }}:</span>
            <span class="px-2 py-0.5 rounded-md font-bold text-[10.5px] {{ $schedule['is_active'] ? 'bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300' : 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300' }}">
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

    @if(!$isEnabled)
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <span class="font-bold">Metode Presensi Kartu RFID Sedang Dinonaktifkan</span>
                <p class="text-[11px] text-amber-700 dark:text-amber-300 mt-0.5">Pihak sekolah saat ini menonaktifkan penerimaan presensi kartu RFID.</p>
            </div>
        </div>
    @endif

    <!-- 2 Column Simulator Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Simulator Panel -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Uji Tap Kartu RFID Siswa</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pilih kartu siswa terdaftar atau masukkan UID RFID fisik secara manual:</p>

            <div class="space-y-4">
                <div class="relative">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Pilih Siswa Contoh (Search & Inisial):
                    </label>

                    <!-- Search Input & Quick Initial Bar -->
                    <div class="mb-2">
                        <input type="text" id="rfid_student_search_input"
                            placeholder="🔍 Cari nama, UID, atau inisial siswa..."
                            oninput="filterRfidStudents(this.value)"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 outline-none focus:border-indigo-500 mb-1.5">

                        <div class="flex items-center gap-1 overflow-x-auto pb-1 text-[10px] no-scrollbar">
                            <button type="button" onclick="filterRfidByInitial('ALL')" class="rfid-initial-chip px-2 py-0.5 rounded-md bg-indigo-600 text-white font-bold shrink-0" data-letter="ALL">
                                Semua
                            </button>
                            @foreach(range('A', 'Z') as $char)
                                <button type="button" onclick="filterRfidByInitial('{{ $char }}')" class="rfid-initial-chip px-1.5 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold shrink-0" data-letter="{{ $char }}">
                                    {{ $char }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <select id="select-student" onchange="document.getElementById('rfid-uid-input').value = this.value"
                        class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Siswa Terdaftar (Total: {{ count($students) }}) --</option>
                        @foreach($students as $s)
                            @php
                                $firstChar = strtoupper(substr(trim($s->name), 0, 1));
                            @endphp
                            <option value="{{ $s->rfid_uid ?? 'TEST_UID_' . $s->id }}"
                                    data-name="{{ strtolower($s->name) }}"
                                    data-firstletter="{{ $firstChar }}"
                                    data-uid="{{ strtolower($s->rfid_uid ?? '') }}">
                                {{ $s->name }} ({{ $s->schoolClass?->name ?? 'Umum' }}) — UID: {{ $s->rfid_uid ?? 'Belum ada UID' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">UID Kartu RFID:</label>
                    <input type="text" id="rfid-uid-input" placeholder="{{ $isEnabled ? 'Contoh: A4B5C6D7' : 'Metode Kartu RFID dinonaktifkan' }}"
                        {{ !$isEnabled ? 'disabled' : '' }}
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono font-bold text-slate-800 dark:text-white outline-none focus:border-purple-500 disabled:bg-slate-100 dark:disabled:bg-slate-800/40 disabled:text-slate-400 dark:disabled:text-slate-500 disabled:cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Pilih Perangkat Mesin RFID (Tersedia: {{ $devices->count() }} Mesin):
                    </label>
                    <select id="select-device" {{ !$isEnabled ? 'disabled' : '' }} class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs outline-none focus:border-purple-500 font-bold text-slate-800 dark:text-slate-200 disabled:bg-slate-100 dark:disabled:bg-slate-800/40 disabled:text-slate-400 dark:disabled:text-slate-500 disabled:cursor-not-allowed">
                        @foreach($devices as $dev)
                            <option value="{{ $dev->device_code }}">
                                [{{ $dev->device_code }}] {{ $dev->device_name }} ({{ $dev->location_name ?? $dev->location }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="button" onclick="simulateTap()" id="btn-tap" {{ !$isEnabled ? 'disabled' : '' }}
                    class="w-full py-3.5 px-4 bg-purple-600 hover:bg-purple-500 disabled:bg-slate-200 dark:disabled:bg-slate-800 disabled:text-slate-400 dark:disabled:text-slate-500 disabled:cursor-not-allowed text-white font-bold rounded-2xl shadow-lg shadow-purple-600/20 text-sm transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                    {{ $isEnabled ? 'SIMULASI TAP KARTU SEKARANG' : 'METODE RFID DINONAKTIFKAN' }}
                </button>
            </div>

            <div id="sim-result" class="hidden p-4 rounded-2xl text-xs"></div>
        </div>

        <!-- Recent Logs & API Endpoint Info -->
        <div class="space-y-6">
            <!-- API Documentation Box -->
            <div class="bg-slate-900 rounded-3xl p-6 text-white border border-slate-800 text-xs space-y-3">
                <span class="px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 font-mono text-[10px] font-bold">
                    ENDPOINT API SCAN RFID
                </span>
                <div class="font-mono text-[11px] bg-slate-950 p-3 rounded-xl border border-slate-800 text-purple-300">
                    POST /attendance/rfid-scan
                </div>
                <p class="text-slate-400 text-[11px] leading-relaxed">
                    Payload JSON dari mikrokontroler (ESP32/RC522):
                </p>
                <pre class="bg-slate-950 p-3 rounded-xl border border-slate-800 font-mono text-[11px] text-emerald-400">{
  "rfid_uid": "A4B5C6D7",
  "device_code": "RFID-GATE-01"
}</pre>
            </div>

            <!-- Recent RFID Scan Table -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 overflow-hidden">
                <h4 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-3">Log Presensi RFID Hari Ini</h4>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($todayLogs as $log)
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs gap-3 min-w-0">
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block truncate">{{ $log->student_name }}</span>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 truncate">NIS: {{ $log->nis }} • {{ $log->notes }}</div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0 whitespace-nowrap {{ $log->status === 'HADIR' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300' }}">
                                {{ $log->status }} ({{ substr($log->time, 0, 5) }})
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 dark:text-slate-500 text-xs">Belum ada scan RFID hari ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeRfidInitial = 'ALL';

    function filterRfidByInitial(letter) {
        activeRfidInitial = letter;
        document.querySelectorAll('.rfid-initial-chip').forEach(btn => {
            if (btn.getAttribute('data-letter') === letter) {
                btn.className = 'rfid-initial-chip px-2 py-0.5 rounded-md bg-indigo-600 text-white font-bold shrink-0';
            } else {
                btn.className = 'rfid-initial-chip px-1.5 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold shrink-0';
            }
        });
        applyRfidFilters();
    }

    function filterRfidStudents(keyword) {
        applyRfidFilters();
    }

    function applyRfidFilters() {
        const keyword = (document.getElementById('rfid_student_search_input')?.value || '').toLowerCase().trim();
        const select = document.getElementById('select-student');
        if (!select) return;

        const options = select.querySelectorAll('option');
        options.forEach(opt => {
            if (!opt.value) return; // Keep placeholder

            const name = opt.getAttribute('data-name') || '';
            const uid = opt.getAttribute('data-uid') || '';
            const firstLetter = opt.getAttribute('data-firstletter') || '';

            const matchInitial = (activeRfidInitial === 'ALL') || (firstLetter === activeRfidInitial);
            const matchKeyword = !keyword || name.includes(keyword) || uid.includes(keyword);

            if (matchInitial && matchKeyword) {
                opt.hidden = false;
            } else {
                opt.hidden = true;
            }
        });
    }

    async function simulateTap() {
        const uid = document.getElementById('rfid-uid-input').value.trim();
        const device = document.getElementById('select-device').value;
        const resultDiv = document.getElementById('sim-result');

        if (!uid) {
            alert("Harap masukkan UID RFID terlebih dahulu.");
            return;
        }

        resultDiv.classList.remove('hidden');
        resultDiv.className = "p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-900 dark:text-indigo-200";
        resultDiv.innerText = "Memproses scan RFID ke server...";

        try {
            const response = await fetch("{{ route('attendance.rfid.scan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    rfid_uid: uid,
                    device_code: device
                })
            });

            const res = await response.json();

            if (response.ok && res.success) {
                resultDiv.className = "p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200";
                resultDiv.innerHTML = `
                    <div class="font-bold text-sm">TAP KARTU BERHASIL!</div>
                    <div class="mt-1">${res.message}</div>
                    <div class="text-[11px] text-emerald-700 dark:text-emerald-300 mt-1">Siswa: <strong>${res.student.name}</strong> • Kelas: ${res.student.class} • Status: <strong>${res.status}</strong></div>
                `;
                setTimeout(() => location.reload(), 1500);
            } else {
                resultDiv.className = "p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200";
                resultDiv.innerHTML = `<strong>Gagal:</strong> ${res.message || "Kartu RFID tidak dikenali."}`;
            }
        } catch (err) {
            resultDiv.className = "p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200";
            resultDiv.innerHTML = `<strong>Error:</strong> Gagal terhubung ke endpoint API RFID.`;
        }
    }
</script>
@endpush
