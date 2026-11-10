@extends('layouts.app')

@section('title', 'Laporan Buku Tamu Pengunjung Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
        <div>
            <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Laporan & Rekapitulasi</div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>📊 Laporan Buku Tamu Pengunjung</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Laporan kunjungan perpustakaan dengan filter kustom periode: Harian, Mingguan, Bulanan, dan Rentang Tanggal Bebas.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('library.guest_book.index') }}" target="_blank" 
               class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-1.5">
                <span>🖥️ Buka Kiosk Tamu</span>
            </a>
            
            <a href="{{ route('library.reports.visitors_print', request()->query()) }}" target="_blank" 
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                <span>🖨️ Cetak Dokumen Resmi (A4)</span>
            </a>
        </div>
    </div>

    <!-- Filter Periode Kustom (Harian, Mingguan, Bulanan, Rentang Bebas) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs">
        <form action="{{ route('library.reports.visitors') }}" method="GET" class="space-y-4">
            
            <!-- 1. Pilihan Jenis Periode -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">Pilih Jenis Periode Laporan</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    <label class="cursor-pointer">
                        <input type="radio" name="period" value="daily" {{ $period === 'daily' ? 'checked' : '' }} onchange="switchPeriodMode('daily')" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 text-center transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50/70 dark:peer-checked:bg-indigo-950/40 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400 font-bold text-xs">
                            <div class="text-base mb-0.5">📅</div>
                            <div>Harian</div>
                            <div class="text-[10px] text-slate-400 font-normal">Per Tanggal</div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="period" value="weekly" {{ $period === 'weekly' ? 'checked' : '' }} onchange="switchPeriodMode('weekly')" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 text-center transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50/70 dark:peer-checked:bg-indigo-950/40 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400 font-bold text-xs">
                            <div class="text-base mb-0.5">📆</div>
                            <div>Mingguan</div>
                            <div class="text-[10px] text-slate-400 font-normal">7 Hari / Pekan</div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="period" value="monthly" {{ $period === 'monthly' ? 'checked' : '' }} onchange="switchPeriodMode('monthly')" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 text-center transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50/70 dark:peer-checked:bg-indigo-950/40 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400 font-bold text-xs">
                            <div class="text-base mb-0.5">📊</div>
                            <div>Bulanan</div>
                            <div class="text-[10px] text-slate-400 font-normal">Per Bulan & Tahun</div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="period" value="custom" {{ $period === 'custom' ? 'checked' : '' }} onchange="switchPeriodMode('custom')" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 text-center transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50/70 dark:peer-checked:bg-indigo-950/40 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400 font-bold text-xs">
                            <div class="text-base mb-0.5">⚙️</div>
                            <div>Rentang Bebas</div>
                            <div class="text-[10px] text-slate-400 font-normal">Mulai s/d Selesai</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 2. Kotak Filter Tanggal Sesuai Periode -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
                
                <!-- Periode Harian -->
                <div id="period-box-daily" class="{{ $period === 'daily' ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Tanggal Kunjungan</label>
                    <input type="date" name="date" value="{{ $date }}" class="w-full sm:w-64 px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white font-semibold">
                </div>

                <!-- Periode Mingguan -->
                <div id="period-box-weekly" class="{{ $period === 'weekly' ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Salah Satu Tanggal di Pekan Tersebut</label>
                    <input type="date" name="week_date" value="{{ $weekDate }}" class="w-full sm:w-64 px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white font-semibold">
                </div>

                <!-- Periode Bulanan -->
                <div id="period-box-monthly" class="{{ $period === 'monthly' ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Bulan</label>
                        <select name="month" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Tahun</label>
                        <select name="year" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                            @for($y = date('Y') + 1; $y >= 2023; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Periode Rentang Bebas -->
                <div id="period-box-custom" class="{{ $period === 'custom' ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-lg">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white font-semibold">
                    </div>
                </div>
            </div>

            <!-- 3. Filter Tambahan: Tipe Pengunjung, Keperluan & Pencarian -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Pengunjung</label>
                    <select name="visitor_type" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                        <option value="all" {{ $visitorType === 'all' ? 'selected' : '' }}>Semua Tipe Pengunjung</option>
                        <option value="siswa" {{ $visitorType === 'siswa' ? 'selected' : '' }}>Siswa / Siswi</option>
                        <option value="guru" {{ $visitorType === 'guru' ? 'selected' : '' }}>Guru / Tenaga Pendidik</option>
                        <option value="umum" {{ $visitorType === 'umum' ? 'selected' : '' }}>Pengunjung Umum / Luar</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Keperluan Kunjungan</label>
                    <select name="purpose" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                        <option value="all" {{ $purpose === 'all' ? 'selected' : '' }}>Semua Keperluan</option>
                        <option value="Membaca Buku" {{ $purpose === 'Membaca Buku' ? 'selected' : '' }}>Membaca Buku</option>
                        <option value="Pinjam / Balik Buku" {{ $purpose === 'Pinjam / Balik Buku' ? 'selected' : '' }}>Pinjam / Balik Buku</option>
                        <option value="Mengerjakan Tugas" {{ $purpose === 'Mengerjakan Tugas' ? 'selected' : '' }}>Mengerjakan Tugas</option>
                        <option value="Diskusi Kelompok" {{ $purpose === 'Diskusi Kelompok' ? 'selected' : '' }}>Diskusi Kelompok</option>
                        <option value="Komputer & E-Book" {{ $purpose === 'Komputer & E-Book' ? 'selected' : '' }}>Komputer & E-Book</option>
                        <option value="Riset / Penelitian" {{ $purpose === 'Riset / Penelitian' ? 'selected' : '' }}>Riset / Penelitian</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Cari Kata Kunci</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama, NISN, NIP, kelas..." 
                           class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                    Filter Aktif: {{ $periodLabel }}
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('library.reports.visitors') }}" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                        Reset Filter
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition cursor-pointer">
                        🔍 Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Ringkasan Statistik Laporan -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kunjungan</div>
            <div class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($totalVisitors) }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $periodLabel }}</div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kunjungan Siswa</div>
            <div class="text-2xl sm:text-3xl font-black text-cyan-600 mt-1">{{ number_format($studentVisitors) }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $totalVisitors > 0 ? round(($studentVisitors / $totalVisitors) * 100, 1) : 0 }}% dari total</div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kunjungan Guru / Tendik</div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1">{{ number_format($teacherVisitors) }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $totalVisitors > 0 ? round(($teacherVisitors / $totalVisitors) * 100, 1) : 0 }}% dari total</div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengunjung Umum</div>
            <div class="text-2xl sm:text-3xl font-black text-purple-600 mt-1">{{ number_format($generalVisitors) }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $totalVisitors > 0 ? round(($generalVisitors / $totalVisitors) * 100, 1) : 0 }}% dari total</div>
        </div>
    </div>

    <!-- Tabel Data Pengunjung -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    Daftar Rincian Pengunjung Perpustakaan
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Menampilkan data sesuai filter: {{ $periodLabel }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                Total: {{ $visitors->total() }} Data
            </span>
        </div>

        @if($visitors->isEmpty())
            <div class="p-12 text-center text-slate-400">
                <div class="text-4xl mb-2">📭</div>
                <div class="text-sm font-semibold">Tidak ada riwayat pengunjung pada periode ini.</div>
                <div class="text-xs mt-1 text-slate-500">Coba ubah filter periode atau kata kunci pencarian.</div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-slate-500 font-extrabold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5">Waktu Kunjungan</th>
                            <th class="px-4 py-3.5">Nama Pengunjung</th>
                            <th class="px-4 py-3.5">Identitas (NISN / NIP)</th>
                            <th class="px-4 py-3.5">Kategori & Kelas</th>
                            <th class="px-4 py-3.5">Keperluan Kunjungan</th>
                            <th class="px-4 py-3.5">Catatan / Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @php $no = $visitors->firstItem(); @endphp
                        @foreach($visitors as $v)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3.5 text-center text-slate-400 font-bold">{{ $no++ }}</td>
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-300">
                                    <div class="font-bold">{{ $v->visited_at ? $v->visited_at->format('d/m/Y') : '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $v->visited_at ? $v->visited_at->format('H:i') : '-' }} WIB</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $v->name }}</div>
                                    @if($v->member)
                                        <div class="text-[10px] text-indigo-500 font-mono">{{ $v->member->member_code }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-500">
                                    {{ $v->identity_number ?: '-' }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $v->visitor_type === 'siswa' ? 'bg-cyan-500/15 text-cyan-600' : ($v->visitor_type === 'guru' ? 'bg-emerald-500/15 text-emerald-600' : 'bg-purple-500/15 text-purple-600') }}">
                                        {{ $v->visitor_type }}
                                    </span>
                                    <div class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">{{ $v->class_or_institution ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60">
                                        {{ $v->purpose }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-slate-500 max-w-xs truncate">
                                    {{ $v->notes ?: '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $visitors->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function switchPeriodMode(mode) {
        ['daily', 'weekly', 'monthly', 'custom'].forEach(m => {
            const el = document.getElementById(`period-box-${m}`);
            if (el) {
                if (m === mode) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            }
        });
    }
</script>
@endsection

