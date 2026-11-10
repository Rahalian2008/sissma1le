@extends('layouts.app')

@section('title', 'Presensi & Absensi Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Cetak Absen Siswa Per Kelas</h1>
            <p class="text-xs text-slate-500 mt-0.5">Cetak lembar presensi kelas resmi untuk 1 bulan ke depan maupun pencatatan kehadiran harian siswa.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('sis.sync-attendance') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow transition cursor-pointer" title="Tarik data presensi selfie/QR dari SIS-SMA1LE untuk tanggal {{ $date }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    ⚡ Tarik Presensi dari SIS
                </button>
            </form>
            <button type="button" onclick="openCustomPrintModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Cetak Lembar Presensi Kustom
            </button>
            <a href="{{ route('students.attendance.print', ['rombel' => $selectedRombel, 'month' => $currentMonth, 'year' => $currentYear, 'mode' => 'recap']) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Cetak Rekap Harian ({{ $currentMonthLabel }})
            </a>
        </div>
    </div>

    <!-- Filter & Summary Bar -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
        <form action="{{ route('students.attendance.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Pilih Kelas / Rombel</label>
                    <select name="rombel" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                        @foreach ($rombels as $r)
                            <option value="{{ $r }}" {{ $selectedRombel === $r ? 'selected' : '' }}>Kelas {{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Tanggal Presensi</label>
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 font-semibold focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Mini KPI Badges -->
            <div class="flex items-center gap-2 text-xs">
                <div class="px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">
                    <span class="font-bold text-sm">{{ $summary['Hadir'] }}</span> Hadir
                </div>
                <div class="px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-xl text-blue-800">
                    <span class="font-bold text-sm">{{ $summary['Sakit'] }}</span> Sakit
                </div>
                <div class="px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-800">
                    <span class="font-bold text-sm">{{ $summary['Izin'] }}</span> Izin
                </div>
                <div class="px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-800">
                    <span class="font-bold text-sm">{{ $summary['Alpa'] }}</span> Alpa
                </div>
                <div class="px-3 py-1.5 bg-purple-50 border border-purple-200 rounded-xl text-purple-800">
                    <span class="font-bold text-sm">{{ $summary['Dispensasi'] }}</span> Dispen
                </div>
            </div>
        </form>
    </div>

    <!-- Attendance Entry Form -->
    <form action="{{ route('students.attendance.store-batch') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="rombel" value="{{ $selectedRombel }}">

        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                Daftar Siswa Kelas {{ $selectedRombel }} &bull; {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="setAllAttendance('Hadir')" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-300 rounded-lg text-xs text-slate-700 font-semibold cursor-pointer">
                    Set Semua Hadir
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100/70 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3 w-28">NIS / NISN</th>
                        <th class="px-4 py-3">Nama Lengkap Siswa</th>
                        <th class="px-4 py-3 text-center">L/P</th>
                        <th class="px-4 py-3 text-center">Status Kehadiran</th>
                        <th class="px-4 py-3">Keterangan / Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $index => $s)
                        @php
                            $currentStatus = $existingAttendances[$s->id]->status ?? 'Hadir';
                            $currentNote = $existingAttendances[$s->id]->notes ?? '';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 text-center font-mono text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono font-medium text-slate-600">
                                {{ $s->nis }}
                                <div class="text-[10px] text-slate-400">{{ $s->nisn ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">
                                {{ $s->name }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold {{ $s->gender === 'L' ? 'text-blue-600' : 'text-pink-600' }}">
                                {{ $s->gender }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <label class="inline-flex items-center gap-1 cursor-pointer">
                                        <input type="radio" name="attendances[{{ $s->id }}]" value="Hadir" {{ $currentStatus === 'Hadir' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-emerald-700 font-semibold">Hadir</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1 cursor-pointer ml-1">
                                        <input type="radio" name="attendances[{{ $s->id }}]" value="Sakit" {{ $currentStatus === 'Sakit' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                        <span class="text-blue-700 font-semibold">Sakit</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1 cursor-pointer ml-1">
                                        <input type="radio" name="attendances[{{ $s->id }}]" value="Izin" {{ $currentStatus === 'Izin' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                                        <span class="text-amber-700 font-semibold">Izin</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1 cursor-pointer ml-1">
                                        <input type="radio" name="attendances[{{ $s->id }}]" value="Alpa" {{ $currentStatus === 'Alpa' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                                        <span class="text-rose-700 font-semibold">Alpa</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1 cursor-pointer ml-1">
                                        <input type="radio" name="attendances[{{ $s->id }}]" value="Dispensasi" {{ $currentStatus === 'Dispensasi' ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                        <span class="text-purple-700 font-semibold">Dispen</span>
                                    </label>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="notes[{{ $s->id }}]" value="{{ $currentNote }}" placeholder="Catatan khusus..." class="w-full px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Tidak ada siswa aktif pada kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->isNotEmpty())
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-500">Total: <strong>{{ $students->count() }}</strong> Siswa Terdata</span>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Presensi Kelas {{ $selectedRombel }}
                </button>
            </div>
        @endif
    </form>
</div>

<!-- Modal Cetak Presensi Kustom Sesuai Kebutuhan -->
<div id="customPrintModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Cetak Lembar Presensi Kustom</h3>
                    <p class="text-xs text-slate-500">Sesuaikan rentang tanggal, kelas, dan format cetak presensi</p>
                </div>
            </div>
            <button type="button" onclick="closeCustomPrintModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('students.attendance.print') }}" method="GET" target="_blank" class="space-y-4">
            <!-- Preset Pilihan Cepat -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Preset Waktu Cepat</label>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="applyPreset('next_month')" class="px-2.5 py-1.5 text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition">
                        Bulan Depan (1 Bln)
                    </button>
                    <button type="button" onclick="applyPreset('this_month')" class="px-2.5 py-1.5 text-xs font-semibold bg-slate-50 text-slate-700 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="applyPreset('next_2weeks')" class="px-2.5 py-1.5 text-xs font-semibold bg-slate-50 text-slate-700 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                        2 Minggu ke Depan
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" id="printStartDate" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" id="printEndDate" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kelas / Rombongan Belajar</label>
                <select name="rombel" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    @foreach ($rombels as $r)
                        <option value="{{ $r }}" {{ $selectedRombel === $r ? 'selected' : '' }}>Kelas {{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Format Lembar</label>
                <div class="grid grid-cols-2 gap-3 mt-1">
                    <label class="flex items-center gap-2 p-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50">
                        <input type="radio" name="mode" value="blank" checked class="text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Format Kosong (Blank)</div>
                            <div class="text-[10px] text-slate-500">Siap dicetak untuk absen di kelas</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50">
                        <input type="radio" name="mode" value="recap" class="text-indigo-600 focus:ring-indigo-500">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Rekapitulasi</div>
                            <div class="text-[10px] text-slate-500">Rekap kehadiran yang sudah tersimpan</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeCustomPrintModal()" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Buka Lembar Cetak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function setAllAttendance(status) {
        document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(el => {
            el.checked = true;
        });
    }

    function openCustomPrintModal() {
        // Default to next month if empty
        const startInput = document.getElementById('printStartDate');
        const endInput = document.getElementById('printEndDate');
        if (!startInput.value || !endInput.value) {
            applyPreset('next_month');
        }
        document.getElementById('customPrintModal').classList.remove('hidden');
    }

    function closeCustomPrintModal() {
        document.getElementById('customPrintModal').classList.add('hidden');
    }

    function applyPreset(type) {
        const today = new Date();
        let start, end;

        if (type === 'next_month') {
            const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
            const lastDayNextMonth = new Date(today.getFullYear(), today.getMonth() + 2, 0);
            start = formatDate(nextMonth);
            end = formatDate(lastDayNextMonth);
        } else if (type === 'this_month') {
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            start = formatDate(firstDay);
            end = formatDate(lastDay);
        } else if (type === 'next_2weeks') {
            const nextDay = new Date(today);
            const futureDay = new Date(today);
            futureDay.setDate(today.getDate() + 14);
            start = formatDate(nextDay);
            end = formatDate(futureDay);
        }

        if (start && end) {
            document.getElementById('printStartDate').value = start;
            document.getElementById('printEndDate').value = end;
        }
    }

    function formatDate(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
</script>
@endsection

