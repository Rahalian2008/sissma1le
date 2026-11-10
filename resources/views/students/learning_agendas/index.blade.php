@extends('layouts.app')

@section('title', 'Agenda Pembelajaran Kelas (Jurnal KBM)')

@section('content')
<div class="space-y-6">
    <!-- Header Title & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span class="p-1.5 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-600 text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </span>
                Agenda & Jurnal Pembelajaran Kelas
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Format baku Kurikulum Merdeka: Catatan harian KBM, Tujuan Pembelajaran (TP), presensi kelas, dan verifikasi guru pengampu.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('students.learning-agendas.print', ['rombel' => $selectedRombel, 'month' => $selectedMonth, 'blank' => 1]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-xs rounded-xl border border-amber-300 transition shadow-2xs cursor-pointer">
                <svg class="w-4 h-4 mr-1.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                📄 Cetak Format Kosong (10 Baris Manual)
            </a>
            <a href="{{ route('students.learning-agendas.print', ['rombel' => $selectedRombel, 'month' => $selectedMonth, 'blank' => 0]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                🖨️ Cetak Jurnal Terisi
            </a>
            <button onclick="openModalCreate()" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Tambah Jurnal KBM
            </button>
        </div>
    </div>

    <!-- Rombel Selector Pills -->
    <div class="bg-white rounded-2xl p-3 shadow-2xs border border-slate-200 overflow-x-auto">
        <div class="flex items-center gap-2 min-w-max">
            <span class="text-xs font-bold text-slate-500 px-2 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Pilih Rombel:
            </span>
            @foreach($rombels as $r)
                <a href="{{ route('students.learning-agendas.index', ['rombel' => $r, 'month' => $selectedMonth]) }}" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition {{ $selectedRombel === $r ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    {{ $r }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Active Rombel Info Banner & Monthly Statistics -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-2xl p-5 text-white shadow-xs border border-slate-800 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold mb-2">
                <span>Rombel: {{ $selectedRombel }}</span>
                <span>•</span>
                <span>T.A. {{ $academicYear?->year ?? date('Y').'/'.(date('Y')+1) }} {{ $academicYear?->semester ?? 'Ganjil' }}</span>
            </div>
            <h2 class="text-xl font-black text-white flex items-center gap-2">
                Buku Jurnal KBM Kelas {{ $selectedRombel }}
            </h2>
            <p class="text-xs text-slate-300 mt-1 flex items-center gap-2">
                <span>Wali Kelas:</span>
                @if($waliKelas)
                    <strong class="text-emerald-300 bg-emerald-900/40 px-2 py-0.5 rounded border border-emerald-500/30">{{ $waliKelas->formatted_name }}</strong>
                    <span class="text-slate-400 text-[11px]">(NIP. {{ $waliKelas->nip ?: '-' }})</span>
                @else
                    <span class="text-amber-300 italic bg-amber-900/40 px-2 py-0.5 rounded border border-amber-500/30">Belum Ditentukan (Atur di Data Guru/Tendik)</span>
                @endif
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-white/5 border border-white/10 rounded-xl p-3 text-xs">
            <div class="text-center px-2 py-1">
                <span class="text-[10px] text-slate-400 block font-medium">Total Sesi KBM</span>
                <span class="text-base font-black text-white">{{ $totalSessions }} Pertemuan</span>
            </div>
            <div class="text-center px-2 py-1 border-l border-white/10">
                <span class="text-[10px] text-emerald-400 block font-medium">Tingkat Hadir</span>
                <span class="text-base font-black text-emerald-300">{{ $attendanceRate }}%</span>
            </div>
            <div class="text-center px-2 py-1 border-l border-white/10">
                <span class="text-[10px] text-amber-400 block font-medium">Sakit / Izin</span>
                <span class="text-base font-black text-amber-300">{{ $sumSick + $sumPermitted }}</span>
            </div>
            <div class="text-center px-2 py-1 border-l border-white/10">
                <span class="text-[10px] text-rose-400 block font-medium">Alpa (A)</span>
                <span class="text-base font-black text-rose-300">{{ $sumAbsent }}</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white rounded-2xl p-4 shadow-2xs border border-slate-200">
        <form method="GET" action="{{ route('students.learning-agendas.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <input type="hidden" name="rombel" value="{{ $selectedRombel }}">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Periode Bulan:</label>
                    <input type="month" name="month" value="{{ $selectedMonth }}" class="px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Filter Tanggal Spesifik:</label>
                    <input type="date" name="date" value="{{ $selectedDate }}" class="px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Cari Mata Pelajaran:</label>
                    <input type="text" name="subject" value="{{ $searchSubject }}" placeholder="Misal: Matematika, Biologi..." class="px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden w-48">
                </div>
            </div>
            <div class="flex items-center gap-2 self-end">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                    Terapkan Filter
                </button>
                @if($selectedDate || $searchSubject)
                    <a href="{{ route('students.learning-agendas.index', ['rombel' => $selectedRombel, 'month' => $selectedMonth]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Learning Agenda Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span>Catatan Jurnal Pembelajaran Rombel {{ $selectedRombel }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium">
                    {{ $agendas->total() }} Catatan
                </span>
            </h3>
            <span class="text-xs text-slate-500">Standar Kurikulum Merdeka</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 w-32">Hari / Tanggal</th>
                        <th class="py-3 px-4 w-28">Jam Ke-</th>
                        <th class="py-3 px-4 w-44">Mata Pelajaran & Guru</th>
                        <th class="py-3 px-4 min-w-[200px]">Tujuan Pembelajaran (TP) / Lingkup Materi</th>
                        <th class="py-3 px-4 min-w-[170px]">Kegiatan / Refleksi</th>
                        <th class="py-3 px-4 w-28 text-center">Presensi (H/S/I/A)</th>
                        <th class="py-3 px-4 min-w-[150px]">Nama Siswa Tidak Hadir / Ket.</th>
                        <th class="py-3 px-4 w-24 text-center">Paraf Guru</th>
                        <th class="py-3 px-4 w-20 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($agendas as $index => $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 text-center font-medium text-slate-400">
                                {{ $agendas->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $item->date ? $item->date->translatedFormat('l') : '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $item->date ? $item->date->format('d/m/Y') : '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 font-semibold border border-blue-200/60 text-[11px]">
                                    {{ $item->time_slot }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $item->subject }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="truncate">{{ $item->teacher?->formatted_name ?? $item->teacher_name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800 leading-snug">
                                    {{ $item->learning_topic }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if($item->activities_summary)
                                    <p class="text-slate-600 line-clamp-2 leading-relaxed">{{ $item->activities_summary }}</p>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                                @if($item->class_notes)
                                    <div class="mt-1 text-[10.5px] text-amber-700 bg-amber-50 p-1 rounded border border-amber-200/50">
                                        <strong>Catatan:</strong> {{ $item->class_notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1 text-[11px] font-bold">
                                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800" title="Hadir">H: {{ $item->students_present }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800" title="Sakit">S: {{ $item->students_sick }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-sky-100 text-sky-800" title="Izin">I: {{ $item->students_permitted }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-800" title="Alpa">A: {{ $item->students_absent }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if($item->absent_notes)
                                    <div class="text-[11.5px] font-medium text-rose-700 bg-rose-50/70 p-1.5 rounded-lg border border-rose-200/60">
                                        {{ $item->absent_notes }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Nihil (Semua Hadir)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->teacher_signed)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px]">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" 
                                            onclick='openModalEdit(@json($item))' 
                                            class="p-1 text-slate-400 hover:text-blue-600 rounded transition cursor-pointer" 
                                            title="Edit Jurnal KBM">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <form action="{{ route('students.learning-agendas.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan agenda pembelajaran ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded transition cursor-pointer" title="Hapus Jurnal KBM">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada agenda pembelajaran untuk rombel dan periode ini.</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik tombol "+ Tambah Jurnal KBM" di atas untuk menambahkan catatan pembelajaran kelas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($agendas->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $agendas->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Create / Edit Agenda -->
<div id="modal-agenda" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 transition-all my-8 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
            <div>
                <h3 id="modal-agenda-title" class="text-base font-bold text-slate-900">Catat Agenda Pembelajaran Kelas</h3>
                <p class="text-xs text-slate-500">Rombongan Belajar: <strong class="text-slate-800">{{ $selectedRombel }}</strong></p>
            </div>
            <button type="button" onclick="closeModalAgenda()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="form-agenda" method="POST" action="{{ route('students.learning-agendas.store') }}" class="space-y-4 text-xs">
            @csrf
            <div id="method-container"></div>

            <input type="hidden" name="rombel" id="input-rombel" value="{{ $selectedRombel }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal KBM <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" id="input-date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jam Pelajaran Ke- <span class="text-rose-500">*</span></label>
                    <input type="text" name="time_slot" id="input-time-slot" required placeholder="Misal: Jam Ke-1 s.d 2 (07.15 - 08.45)" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Mata Pelajaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="subject" id="input-subject" required placeholder="Misal: Matematika, Fisika, Biologi" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Guru Pengampu <span class="text-rose-500">*</span></label>
                    <select name="teacher_employee_id" id="input-teacher-id" onchange="onTeacherChange(this)" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                        <option value="">-- Pilih Guru dari Database PTK --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" data-name="{{ $t->formatted_name }}">{{ $t->formatted_name }} ({{ $t->nip ?: 'Non-NIP' }})</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="teacher_name" id="input-teacher-name" value="">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Tujuan Pembelajaran (TP) / Lingkup Materi Pokok <span class="text-rose-500">*</span></label>
                <textarea name="learning_topic" id="input-topic" rows="2" required placeholder="Tuliskan TP Kurikulum Merdeka atau materi yang dipelajari pada sesi ini..." class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Ringkasan Kegiatan Pembelajaran & Refleksi</label>
                <textarea name="activities_summary" id="input-activities" rows="2" placeholder="Diskusi kelompok, praktikum laboratorium, presentasi, asesmen formatif, dsb..." class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"></textarea>
            </div>

            <!-- Presensi Siswa Pada Jam KBM Tersebut -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2">
                <span class="block font-bold text-slate-800 text-[11px] uppercase tracking-wide">Presensi Siswa Pada Sesi KBM</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-emerald-700 mb-0.5">Hadir (H)</label>
                        <input type="number" min="0" name="students_present" id="input-present" value="{{ $totalStudentsInRombel > 0 ? $totalStudentsInRombel : 36 }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-amber-700 mb-0.5">Sakit (S)</label>
                        <input type="number" min="0" name="students_sick" id="input-sick" value="0" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-sky-700 mb-0.5">Izin (I)</label>
                        <input type="number" min="0" name="students_permitted" id="input-permitted" value="0" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-rose-700 mb-0.5">Alpa (A)</label>
                        <input type="number" min="0" name="students_absent" id="input-absent" value="0" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-medium text-slate-600 mb-0.5">Nama Siswa Tidak Hadir / Keterangan (Opsional):</label>
                    <input type="text" name="absent_notes" id="input-absent-notes" placeholder="Misal: Ahmad (Sakit demam), Rina (Izin dispensasi OSIS)" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Catatan Khusus Kelas / Kejadian Penting / Catatan Sarpras (Opsional)</label>
                <input type="text" name="class_notes" id="input-class-notes" placeholder="Misal: Proyektor kelas perlu diservis, kelas kondusif, dsb..." class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="teacher_signed" id="input-signed" value="1" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                <label for="input-signed" class="text-xs font-semibold text-slate-700">Paraf / Verifikasi langsung guru pengampu sesi ini</label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModalAgenda()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-xs">
                    Simpan Agenda KBM
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function onTeacherChange(selectElem) {
        const selectedOption = selectElem.options[selectElem.selectedIndex];
        const teacherNameInput = document.getElementById('input-teacher-name');
        if (selectedOption && selectedOption.dataset.name) {
            teacherNameInput.value = selectedOption.dataset.name;
        } else {
            teacherNameInput.value = '';
        }
    }

    function openModalCreate() {
        const modal = document.getElementById('modal-agenda');
        const form = document.getElementById('form-agenda');
        const title = document.getElementById('modal-agenda-title');
        const methodContainer = document.getElementById('method-container');

        title.textContent = 'Catat Agenda Pembelajaran Kelas Baru';
        form.action = "{{ route('students.learning-agendas.store') }}";
        methodContainer.innerHTML = '';

        // Reset form values
        document.getElementById('input-date').value = "{{ date('Y-m-d') }}";
        document.getElementById('input-time-slot').value = '';
        document.getElementById('input-subject').value = '';
        document.getElementById('input-teacher-id').value = '';
        document.getElementById('input-teacher-name').value = '';
        document.getElementById('input-topic').value = '';
        document.getElementById('input-activities').value = '';
        document.getElementById('input-present').value = {{ $totalStudentsInRombel > 0 ? $totalStudentsInRombel : 36 }};
        document.getElementById('input-sick').value = 0;
        document.getElementById('input-permitted').value = 0;
        document.getElementById('input-absent').value = 0;
        document.getElementById('input-absent-notes').value = '';
        document.getElementById('input-class-notes').value = '';
        document.getElementById('input-signed').checked = true;

        modal.classList.remove('hidden');
    }

    function openModalEdit(item) {
        const modal = document.getElementById('modal-agenda');
        const form = document.getElementById('form-agenda');
        const title = document.getElementById('modal-agenda-title');
        const methodContainer = document.getElementById('method-container');

        title.textContent = 'Edit Agenda Pembelajaran Kelas #' + item.id;
        form.action = "/students/learning-agendas/" + item.id;
        methodContainer.innerHTML = '@method("PUT")';

        // Prepopulate values
        document.getElementById('input-date').value = item.date ? item.date.substring(0, 10) : '';
        document.getElementById('input-time-slot').value = item.time_slot || '';
        document.getElementById('input-subject').value = item.subject || '';
        document.getElementById('input-teacher-id').value = item.teacher_employee_id || '';
        document.getElementById('input-teacher-name').value = item.teacher_name || '';
        document.getElementById('input-topic').value = item.learning_topic || '';
        document.getElementById('input-activities').value = item.activities_summary || '';
        document.getElementById('input-present').value = item.students_present ?? 0;
        document.getElementById('input-sick').value = item.students_sick ?? 0;
        document.getElementById('input-permitted').value = item.students_permitted ?? 0;
        document.getElementById('input-absent').value = item.students_absent ?? 0;
        document.getElementById('input-absent-notes').value = item.absent_notes || '';
        document.getElementById('input-class-notes').value = item.class_notes || '';
        document.getElementById('input-signed').checked = Boolean(item.teacher_signed);

        modal.classList.remove('hidden');
    }

    function closeModalAgenda() {
        document.getElementById('modal-agenda').classList.add('hidden');
    }
</script>
@endsection
