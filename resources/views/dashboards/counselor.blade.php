@extends('layouts.app')

@section('title', 'Dashboard Bimbingan Konseling (BK)')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header BK -->
    <div class="bg-gradient-to-r from-slate-900 via-purple-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-purple-300 border border-white/10">
                Pusat Layanan Bimbingan & Konseling
            </span>
            <h2 class="text-2xl font-extrabold text-white mt-2">Bimbingan Konseling SMAN 1 Lengkong</h2>
            <p class="text-xs text-slate-300 mt-1">Pendampingan Karakter, Penanganan Kasus Tata Tertib, dan Konseling Preventif</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('guidance.create') }}" class="px-5 py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-2xl shadow-lg shadow-purple-600/30 text-xs sm:text-sm flex items-center gap-2">
                <span>+</span> Buka Catatan Pembinaan Baru
            </a>
        </div>
    </div>

    <!-- Tata Tertib Guidance Threshold Tiers -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 mb-1">Ambang Batas Tindakan Pembinaan (Tata Tertib SMAN 1 Lengkong)</h3>
        <p class="text-xs text-slate-500 mb-4">Sistem memberikan <strong>NOTIFIKASI PERLU TINDAKAN</strong> saat siswa mencapai batas poin berikut:</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-2xl">
                <span class="font-bold text-yellow-800">75 - 124 Poin</span>
                <p class="text-[11px] text-yellow-700 mt-1">Bimbingan Wali Kelas</p>
            </div>
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl">
                <span class="font-bold text-amber-800">125 - 175 Poin</span>
                <p class="text-[11px] text-amber-700 mt-1">SP 1 - BK</p>
            </div>
            <div class="p-3 bg-orange-50 border border-orange-200 rounded-2xl">
                <span class="font-bold text-orange-800">176 - 199 Poin</span>
                <p class="text-[11px] text-orange-700 mt-1">SP 2 - BK</p>
            </div>
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl">
                <span class="font-bold text-rose-800">200 Poin</span>
                <p class="text-[11px] text-rose-700 mt-1">SP 3 - BK & Kesiswaan</p>
            </div>
            <div class="p-3 bg-red-100 border border-red-300 rounded-2xl">
                <span class="font-bold text-red-900">> 200 Poin</span>
                <p class="text-[11px] text-red-800 mt-1">Rapat Khusus Sekolah</p>
            </div>
        </div>
    </div>

    <!-- Students Requiring Action (NOTIFIKASI PERLU TINDAKAN) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar Siswa Mencapai Ambang Pembinaan</h3>
                <p class="text-xs text-slate-500">Daftar siswa dengan akumulasi poin pelanggaran ≥ 75 poin</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-700 font-bold text-xs">
                {{ $studentsNeedingAction->count() }} Kasus Perlu Tindakan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Nama Siswa</th>
                        <th class="py-3.5 px-4">Kelas</th>
                        <th class="py-3.5 px-4">Total Poin Pelanggaran</th>
                        <th class="py-3.5 px-4">Tahap Tindakan</th>
                        <th class="py-3.5 px-4">Riwayat Kasus</th>
                        <th class="py-3.5 px-4 text-center">Tindakan BK</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($studentsNeedingAction as $student)
                        @php $gStatus = $student->guidance_status; @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $student->name }}</div>
                                <div class="text-[11px] text-slate-400">NIS: {{ $student->nis }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 font-semibold">{{ $student->schoolClass?->name }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-black text-rose-600 text-sm">{{ $student->totalViolationPoints() }} Poin</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                    {{ $gStatus['level'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-600">{{ $student->violations->count() }} Catatan Kejadian</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('guidance.create', ['student_id' => $student->id]) }}" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs transition">
                                        Proses Bimbingan
                                    </a>
                                    <a href="{{ route('reports.student.dossier', $student->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs" title="Lihat Dossier">
                                        Dossier
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400">
                                Alhamdulillah! Tidak ada siswa yang melampaui batas ambang pembinaan (≥75 poin).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Guidance Cases & Agreements -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Catatan Pembinaan yang Sedang Berjalan (Dalam Proses)</h3>
                <p class="text-xs text-slate-500">Monitoring komitmen dan surat perjanjian siswa</p>
            </div>
            <a href="{{ route('guidance.index') }}" class="text-xs font-bold text-indigo-600">Semua Catatan Pembinaan →</a>
        </div>

        <div class="space-y-3">
            @forelse($activeGuidanceCases as $case)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-bold text-slate-900">{{ $case->student->name }}</span>
                            <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 font-bold text-[10px]">{{ $case->followUpLabel() }}</span>
                            <span class="text-slate-400">• {{ $case->date->format('d M Y') }}</span>
                        </div>
                        <p class="text-slate-600 leading-relaxed">{{ $case->recommendation }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('guidance.show', $case->id) }}" class="px-3 py-1.5 bg-slate-900 text-white rounded-xl font-bold hover:bg-indigo-600 transition">
                            Detail Kasus
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-slate-400 text-xs">
                    Tidak ada catatan pembinaan yang berstatus dalam proses.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Papan Peringkat Presensi Siswa (Alpa, Izin, Sakit) -->
    @include('dashboards.partials.attendance_rankings')

    <!-- Analisis Tata Tertib (Rangking, Sering Terjadi, Ambang Batas) -->
    @include('dashboards.partials.discipline_analytics')

    <!-- Capaian 7 Kebiasaan Anak Indonesia Hebat (Diagram & Persentase) -->
    @include('dashboards.partials.habits_analytics')
</div>
@endsection
