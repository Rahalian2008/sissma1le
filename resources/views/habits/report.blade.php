@extends('layouts.app')

@section('title', 'Laporan Karakter 7 Kebiasaan Anak Hebat - SISWA SMA1LE')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-teal-900 via-emerald-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-teal-300 border border-white/10">
                Pilar Pembiasaan Karakter Positif
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">Laporan 7 Kebiasaan Anak Indonesia Hebat</h2>
            <p class="text-xs text-slate-300 mt-1">
                Rekapitulasi pencapaian 7 pilar pembiasaan karakter peserta didik per kelas dan rentang waktu.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('reports.print.habits', ['class_id' => $classId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Lembar Resmi (A4)
            </a>
            <a href="{{ route('habits.charts') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl text-xs flex items-center gap-2 transition border border-white/20">
                Diagram & Visual →
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5">
        <form action="{{ route('habits.report') }}" method="GET" class="flex flex-wrap items-end gap-4">
            {{-- Class Selector: Hidden entirely for siswa and orang_tua --}}
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                <div class="w-full sm:w-64">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Rombongan Belajar (Kelas)</label>
                    @if(Auth::user()->role === 'wali_kelas')
                        <input type="text" value="{{ $selectedClass?->name ?? 'Kelas Binaan' }}" class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-not-allowed" readonly>
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                    @else
                        <select name="class_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                                    Kelas {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endif

            <div class="w-full sm:w-44">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
            </div>

            <div class="w-full sm:w-44">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl text-xs shadow-xs transition">
                    Tampilkan Rekap
                </button>
                <a href="{{ route('habits.report') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Overview Banner -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Kelas Ditinjau</span>
            <div class="text-lg font-black text-slate-800 mt-1">
                {{ $selectedClass ? 'Kelas '.$selectedClass->name : 'Seluruh Kelas' }}
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Rentang Hari</span>
            <div class="text-lg font-black text-slate-800 mt-1">{{ $days }} Hari Pengamatan</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Siswa</span>
            <div class="text-lg font-black text-teal-600 mt-1">{{ $studentReports->count() }} Peserta Didik</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Target Tiap Siswa</span>
            <div class="text-lg font-black text-indigo-600 mt-1">{{ 7 * $days }} Checklist</div>
        </div>
    </div>

    <!-- Table Rekapitulasi Capaian 7 Pilar -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Rekapitulasi 7 Pilar Karakter Siswa</h3>
                <p class="text-xs text-slate-500">Angka pada kolom K1 - K7 menunjukkan frekuensi hari terpenuhinya kebiasaan.</p>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">≥85% Teladan</span>
                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold">≥70% Konsisten</span>
                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">≥50% Berkembang</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-3 text-center w-10">No</th>
                        <th class="py-3.5 px-4">Nama Siswa / NIS</th>
                        <th class="py-3.5 px-3 text-center">Kelas</th>
                        <th class="py-3.5 px-2 text-center" title="K1: Bangun Pagi">K1</th>
                        <th class="py-3.5 px-2 text-center" title="K2: Beribadah Tepat Waktu">K2</th>
                        <th class="py-3.5 px-2 text-center" title="K3: Berolahraga">K3</th>
                        <th class="py-3.5 px-2 text-center" title="K4: Makan Sehat Bergizi">K4</th>
                        <th class="py-3.5 px-2 text-center" title="K5: Gemar Membaca">K5</th>
                        <th class="py-3.5 px-2 text-center" title="K6: Bermasyarakat & Peduli">K6</th>
                        <th class="py-3.5 px-2 text-center" title="K7: Tidur Tepat Waktu">K7</th>
                        <th class="py-3.5 px-3 text-center">Total</th>
                        <th class="py-3.5 px-3 text-center">% Capaian</th>
                        <th class="py-3.5 px-4 text-center">Predikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($studentReports as $idx => $sr)
                        @php
                            $s = $sr['student'];
                            $badgeColor = match ($sr['badge']) {
                                'Teladan Unggul' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'Konsisten' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'Berkembang' => 'bg-amber-100 text-amber-800 border-amber-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 text-center font-bold text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('habits.journal', ['student_id' => $s->id]) }}" class="font-bold text-slate-900 hover:text-teal-600 transition">
                                    {{ $s->name }}
                                </a>
                                <div class="text-[10px] text-slate-400">NIS: {{ $s->nis }}</div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px]">
                                    {{ $s->schoolClass?->name ?? '-' }}
                                </span>
                            </td>
                            @foreach($habits as $h)
                                <td class="py-3 px-2 text-center font-mono font-bold text-slate-700">
                                    {{ $sr['per_habit'][$h->id] ?? 0 }}
                                </td>
                            @endforeach
                            <td class="py-3 px-3 text-center font-mono font-black text-slate-800">
                                {{ $sr['completed'] }}/{{ $sr['target'] }}
                            </td>
                            <td class="py-3 px-3 text-center font-black {{ $sr['percentage'] >= 70 ? 'text-emerald-600' : 'text-slate-800' }}">
                                {{ $sr['percentage'] }}%
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeColor }}">
                                    {{ $sr['badge'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="py-8 text-center text-slate-400 italic">
                                Tidak ada data siswa yang ditemukan pada kriteria kelas yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
