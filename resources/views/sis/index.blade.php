@extends('layouts.app')

@section('title', 'Integrasi & Sinkronisasi SIS-SMA1LE')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-black tracking-wide bg-indigo-100 text-indigo-800 border border-indigo-200">
                    SIS BRIDGE
                </span>
                <h1 class="text-xl font-bold text-slate-900">Sinkronisasi SIS-SMA1LE</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Jembatan otomatis untuk menarik data Presensi Selfie/QR, Catatan Poin Pelanggaran, dan Catatan Prestasi Siswa langsung dari portal <a href="{{ config('services.sis_sma1le.url', 'https://sissma1le.sman1lengkong.school') }}" target="_blank" class="text-indigo-600 font-semibold hover:underline">SIS-SMA1LE</a>.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('students.attendance.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                📋 Cek Absensi SIKATAS
            </a>
            <a href="{{ route('achievements.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                🏆 Cek Prestasi SIKATAS
            </a>
            <a href="{{ route('violations.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                ⚠️ Cek Pelanggaran
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3 text-emerald-800 text-xs shadow-sm">
        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="font-medium leading-relaxed">{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3 text-rose-800 text-xs shadow-sm">
        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="font-medium leading-relaxed">{{ session('error') }}</div>
    </div>
    @endif

    <!-- Connection Status Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl sm:rounded-3xl p-6 text-white shadow-xl border border-indigo-900/50">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    @if(($connection['status'] ?? '') === 'success')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Terhubung Aktif (Online)
                        </span>
                        <span class="text-xs text-slate-400 font-mono">Latency: {{ $connection['latency_ms'] ?? 0 }} ms</span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                            Gagal Terhubung
                        </span>
                    @endif
                </div>
                <h2 class="text-lg sm:text-xl font-black tracking-tight">Portal SIS-SMA1LE (Sistem Informasi Siswa)</h2>
                <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                    Target URL: <code class="bg-black/30 px-2 py-0.5 rounded text-indigo-300">{{ $connection['url'] ?? config('services.sis_sma1le.url') }}</code>
                    &bull; Akun Terautentikasi: <code class="bg-black/30 px-2 py-0.5 rounded text-emerald-300">{{ config('services.sis_sma1le.username') }} (Super Admin)</code>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('sis.sync-students') }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Tarik master siswa aktual dari SIS-SMA1LE ke Buku Induk SIKATAS?')" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-1.5 cursor-pointer">
                        <span>👥 Sinkron Master Siswa</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Summary Metrics -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-5 border-t border-slate-800 text-xs">
            <div class="bg-white/5 backdrop-blur-md rounded-xl p-3 border border-white/5">
                <div class="text-slate-400 text-[11px]">Total Siswa di SIKATAS</div>
                <div class="text-lg font-black text-white mt-0.5">{{ number_format($stats['total_students']) }}</div>
            </div>
            <div class="bg-white/5 backdrop-blur-md rounded-xl p-3 border border-white/5">
                <div class="text-slate-400 text-[11px]">Presensi Hari Ini</div>
                <div class="text-lg font-black text-emerald-400 mt-0.5">{{ number_format($stats['today_attendances']) }}</div>
            </div>
            <div class="bg-white/5 backdrop-blur-md rounded-xl p-3 border border-white/5">
                <div class="text-slate-400 text-[11px]">Total Prestasi Siswa</div>
                <div class="text-lg font-black text-amber-400 mt-0.5">{{ number_format($stats['total_achievements']) }}</div>
            </div>
            <div class="bg-white/5 backdrop-blur-md rounded-xl p-3 border border-white/5">
                <div class="text-slate-400 text-[11px]">Total Poin Pelanggaran</div>
                <div class="text-lg font-black text-rose-400 mt-0.5">{{ number_format($stats['total_violations']) }}</div>
            </div>
        </div>
    </div>

    <!-- 3 Action Sync Modules -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Module 1: Absensi Siswa -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-base border border-blue-100">
                        📸
                    </span>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 font-extrabold text-[10px] rounded-md">
                        Harian & Rekap
                    </span>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Sinkronisasi Presensi Siswa</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Menarik data presensi selfie kamera, scan QR, atau RFID yang tercatat di SIS-SMA1LE. Mencocokkan status Hadir, Terlambat, Sakit, Izin, dan Alpa secara presisi.
                </p>

                <form action="{{ route('sis.sync-attendance') }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Tanggal Absensi</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Jangkauan Halaman (Pages)</label>
                        <select name="pages" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800">
                            <option value="1">1 Halaman Terakhir (20 Data)</option>
                            <option value="3" selected>3 Halaman Terakhir (60 Data)</option>
                            <option value="5">5 Halaman Terakhir (100 Data)</option>
                            <option value="10">10 Halaman Terakhir (200 Data)</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer mt-2">
                        <span>⚡ Tarik Presensi Sekarang</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Module 2: Prestasi Siswa -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-base border border-amber-100">
                        🏆
                    </span>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 font-extrabold text-[10px] rounded-md">
                        R1 - R10
                    </span>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Sinkronisasi Prestasi Siswa</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Menarik catatan penghargaan, pembiasaan positif, dan prestasi akademik/non-akademik siswa yang telah diverifikasi guru pembina di SIS-SMA1LE.
                </p>

                <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600">
                    <div class="font-bold text-slate-800 mb-1">Cakupan Data:</div>
                    <ul class="space-y-1 text-[11px] list-disc list-inside text-slate-500">
                        <li>Butir Prestasi (Shalat Dhuha, Petugas Upacara, Juara)</li>
                        <li>Poin Prestasi (+5, +10, dsb.)</li>
                        <li>Status Verifikasi & Bukti</li>
                    </ul>
                </div>
            </div>

            <form action="{{ route('sis.sync-achievements') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <span>⚡ Tarik Prestasi Siswa</span>
                </button>
            </form>
        </div>

        <!-- Module 3: Pelanggaran Siswa -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-black text-base border border-rose-100">
                        ⚠️
                    </span>
                    <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-extrabold text-[10px] rounded-md">
                        P1 - P6 (Tatib)
                    </span>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Sinkronisasi Poin Pelanggaran</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Menarik catatan pelanggaran tata tertib yang diinput oleh Guru Piket atau Tim Tatib di SIS-SMA1LE, langsung siap cetak SP1/SP2/SP3 di SIKATAS.
                </p>

                <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600">
                    <div class="font-bold text-slate-800 mb-1">Cakupan Data:</div>
                    <ul class="space-y-1 text-[11px] list-disc list-inside text-slate-500">
                        <li>Bentuk & Kategori Pelanggaran Siswa</li>
                        <li>Bobot Poin Pelanggaran</li>
                        <li>Lokasi Kejadian & Status Pembinaan</li>
                    </ul>
                </div>
            </div>

            <form action="{{ route('sis.sync-violations') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <span>⚡ Tarik Pelanggaran Siswa</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Module 4: Dapodik Kemendikbudristek Excel Sync -->
    <div class="bg-linear-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-200 rounded-3xl p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-emerald-600 text-white font-black text-[10px] rounded-lg tracking-wider uppercase">
                        DAPODIK KEMENDIKBUDRISTEK
                    </span>
                    <span class="text-xs font-bold text-emerald-800">Format Resmi Excel (.xlsx)</span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900">Perbarui Identitas & Demografi Siswa dari Rekap Daftar PD</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Sinkronkan identitas lengkap siswa ke dalam <strong>Buku Induk Digital</strong> & <strong>Data Demografi</strong>. Mencakup pemutakhiran NIPD, NIK, No. Kartu Keluarga, TTL, Agama, Alamat Lengkap, RT/RW, Dusun, Kelurahan, Kode Pos, Sekolah Asal SMP, No. Seri Ijazah SMP, serta Biodata Lengkap Orang Tua & Wali (Nama Ayah & Ibu, NIK, Pendidikan, Pekerjaan, Penghasilan).
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <form action="{{ route('sis.sync-dapodik') }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Perbarui identitas seluruh siswa dari berkas Dapodik di folder Downloads komputer?')" class="w-full sm:w-auto px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span>⚡ Update dari Berkas Downloads</span>
                    </button>
                </form>

                <div x-data="{ openUpload: false }" class="relative">
                    <button @click="openUpload = !openUpload" type="button" class="w-full sm:w-auto px-4 py-3 bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-800 font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <span>Unggah File Baru (.xlsx)</span>
                    </button>

                    <div x-show="openUpload" @click.away="openUpload = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-200 p-4 z-20">
                        <h4 class="text-xs font-bold text-slate-800 mb-2">Unggah Berkas Excel Dapodik</h4>
                        <form action="{{ route('sis.sync-dapodik') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <input type="file" name="dapodik_file" accept=".xlsx" required class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                            <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition">
                                Mulai Proses Pembaruan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduled Automation Guide -->
    <div class="bg-indigo-50/70 border border-indigo-200 rounded-2xl p-5 text-xs text-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="font-extrabold text-indigo-900 text-sm flex items-center gap-1.5">
                    <span>⏰</span> Otomatisasi CLI & Terminal (Background Scheduler / Artisan)
                </span>
                <p class="text-slate-600 text-[11.5px] mt-0.5">
                    Anda juga dapat menjalankan sinkronisasi otomatis atau import Dapodik di terminal tanpa browser:
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="bg-slate-900 text-emerald-400 font-mono px-3 py-1.5 rounded-xl text-[11px] font-semibold shadow-inner select-all">
                    php artisan student:import-dapodik
                </div>
                <div class="bg-slate-900 text-indigo-300 font-mono px-3 py-1.5 rounded-xl text-[11px] font-semibold shadow-inner select-all">
                    php artisan sis:sync --all
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Synced Records Tables -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Rekaman Presensi Terakhir di SIKATAS</h3>
            <span class="text-[11px] text-slate-400">10 data terbaru</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100/70 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-4">Tanggal</th>
                        <th class="py-2.5 px-4">Nama Siswa</th>
                        <th class="py-2.5 px-4">NIS / NISN</th>
                        <th class="py-2.5 px-4">Kelas / Rombel</th>
                        <th class="py-2.5 px-4">Status</th>
                        <th class="py-2.5 px-4">Keterangan / Metode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentAttendances as $att)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-2.5 px-4 font-semibold text-slate-800 whitespace-nowrap">{{ $att->date ? \Carbon\Carbon::parse($att->date)->translatedFormat('d M Y') : '-' }}</td>
                        <td class="py-2.5 px-4 font-bold text-slate-900">{{ $att->student?->name ?? 'Siswa' }}</td>
                        <td class="py-2.5 px-4 text-slate-500 font-mono text-[11px]">{{ $att->student?->nisn ?? $att->student?->nis ?? '-' }}</td>
                        <td class="py-2.5 px-4 text-slate-700 font-semibold">{{ $att->rombel ?? $att->student?->current_rombel ?? '-' }}</td>
                        <td class="py-2.5 px-4 whitespace-nowrap">
                            @if($att->status === 'Hadir')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Hadir</span>
                            @elseif($att->status === 'Sakit')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Sakit</span>
                            @elseif($att->status === 'Izin')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Izin</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">{{ $att->status }}</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-4 text-slate-600 text-[11px]">{{ $att->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400">Belum ada rekaman presensi. Silakan klik tombol "Tarik Presensi Sekarang".</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
