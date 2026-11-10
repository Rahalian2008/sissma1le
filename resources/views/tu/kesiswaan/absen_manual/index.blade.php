@extends('layouts.app')

@section('title', 'Cetak Lembar Presensi Manual Siswa (Model SIKATAS)')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 text-white shadow-xl shadow-emerald-500/10">
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-200">
                <a href="{{ route('tu.dashboard') }}" class="hover:underline">Adm. Tata Usaha</a>
                <span>/</span>
                <a href="{{ route('tu.kesiswaan.index') }}" class="hover:underline">Kesiswaan</a>
                <span>/</span>
                <span>Cetak Absen Manual</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight flex items-center gap-2.5">
                <span>📋 Cetak Lembar Presensi Manual Siswa</span>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-white/20 text-white backdrop-blur-md">Model SIKATAS</span>
            </h1>
            <p class="text-xs sm:text-sm text-emerald-100 max-w-2xl">
                Cetak lembar daftar hadir kelas manual siap print di kertas F4 / A4 landscape. Mendukung pilihan periode bulanan, mingguan, atau rentang tanggal kustom dengan penanda otomatis hari libur dan hari Minggu.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('tu.kesiswaan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Kesiswaan</span>
            </a>
        </div>
    </div>

    <!-- Alert Edukasi Standar SIKATAS -->
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 font-bold text-sm">
            💡
        </div>
        <div class="text-xs text-emerald-900 dark:text-emerald-200 space-y-1">
            <p class="font-bold">Standar Administrasi Kehadiran Manual (SIKATAS SMAN 1 Lengkong):</p>
            <p class="text-emerald-700 dark:text-emerald-300">
                Lembar presensi kelas ini digunakan untuk pencatatan kehadiran manual harian oleh Wali Kelas atau Sekretaris Kelas, pengawasan kegiatan belajar mengajar (KBM), serta lampiran rekapitulasi ketatausahaan per semester. Kolom tanggal hari Minggu dan hari libur nasional/sekolah diarsir abu-abu secara otomatis.
            </p>
        </div>
    </div>

    <!-- Form Pengaturan & Filter Cetak -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6">
        <form action="{{ route('tu.kesiswaan.absen_manual.print') }}" method="GET" target="_blank" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pilihan Rombongan Belajar (Kelas) -->
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        1. Pilih Rombongan Belajar / Kelas <span class="text-rose-500">*</span>
                    </label>
                    <select name="class_id" id="class_select" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm font-bold text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$selectedClassId === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->name }} (Tingkat {{ $c->grade ?? 'SMA' }}) - Wali Kelas: {{ $c->homeroomTeacher?->name ?? 'Belum Ditentukan' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400">Pilih rombel yang ingin dicetak lembar presensinya.</p>
                </div>

                <!-- Pilihan Mode Periode -->
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        2. Model Periode Presensi <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <label class="flex items-center gap-2 p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 cursor-pointer transition hover:border-emerald-400 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-950/30 has-[:checked]:text-emerald-800 dark:has-[:checked]:text-emerald-200 shadow-2xs">
                            <input type="radio" name="period_type" value="monthly" {{ $periodType === 'monthly' ? 'checked' : '' }} onchange="togglePeriodFields('monthly')" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-bold leading-tight">📅 Bulanan (1 Bln)</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 cursor-pointer transition hover:border-emerald-400 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-950/30 has-[:checked]:text-emerald-800 dark:has-[:checked]:text-emerald-200 shadow-2xs">
                            <input type="radio" name="period_type" value="weekly" {{ $periodType === 'weekly' ? 'checked' : '' }} onchange="togglePeriodFields('weekly')" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-bold leading-tight">📆 Mingguan (6 Hari)</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 cursor-pointer transition hover:border-emerald-400 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-950/30 has-[:checked]:text-emerald-800 dark:has-[:checked]:text-emerald-200 shadow-2xs">
                            <input type="radio" name="period_type" value="custom" {{ $periodType === 'custom' ? 'checked' : '' }} onchange="togglePeriodFields('custom')" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-bold leading-tight">⏱️ Rentang Kustom</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Bagian Opsi Berdasarkan Periode -->
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-4">
                
                <!-- Opsi Bulanan -->
                <div id="section-monthly" class="{{ $periodType === 'monthly' ? 'grid' : 'hidden' }} grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Bulan</label>
                        <select name="month" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-white">
                            @php
                                $months = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ (int)$currentMonth === $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tahun</label>
                        <select name="year" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-white">
                            @for($y = (int)date('Y') - 1; $y <= (int)date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ (int)$currentYear === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Opsi Mingguan -->
                <div id="section-weekly" class="{{ $periodType === 'weekly' ? 'block' : 'hidden' }} space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Tanggal Acuan Pekan (Senin - Sabtu)</label>
                    <input type="date" name="week_ref_date" value="{{ date('Y-m-d') }}" class="w-full sm:w-80 px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-white">
                    <p class="text-[11px] text-slate-400">Sistem akan otomatis mengambil 6 hari kerja (Senin s.d. Sabtu) pada pekan dari tanggal ini.</p>
                </div>

                <!-- Opsi Rentang Kustom -->
                <div id="section-custom" class="{{ $periodType === 'custom' ? 'grid' : 'hidden' }} grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-white">
                        <p class="text-[10px] text-slate-400 mt-1">Maksimal 31 hari per lembar cetak agar tabel muat proporsional di kertas landscape.</p>
                    </div>
                </div>

            </div>

            <!-- Pengaturan Format Cetak & KOP -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Ukuran Kertas Kedinasan
                    </label>
                    <select name="paper" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-white">
                        <option value="f4" selected>📄 Kertas F4 / Folio (215 x 330 mm) - Landscape (Standar)</option>
                        <option value="a4">📄 Kertas A4 (210 x 297 mm) - Landscape</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        KOP Surat Lembaga
                    </label>
                    <select name="kop" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-white">
                        <option value="1" selected>🏢 Tampilkan KOP Resmi SMA Negeri 1 Lengkong</option>
                        <option value="0">📄 Tanpa KOP (Hanya Judul & Data Rombel)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Pengesahan / Tanda Tangan
                    </label>
                    <select name="signer" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-white">
                        <option value="wakasek" selected>Wakasek Kesiswaan & Wali Kelas</option>
                        <option value="kepala_sekolah">Kepala Sekolah & Wali Kelas</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('tu.kesiswaan.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 flex items-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>🖨️ Pratinjau & Cetak Lembar Presensi (F4/A4)</span>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function togglePeriodFields(type) {
        document.getElementById('section-monthly').classList.add('hidden');
        document.getElementById('section-monthly').classList.remove('grid');
        document.getElementById('section-weekly').classList.add('hidden');
        document.getElementById('section-custom').classList.add('hidden');
        document.getElementById('section-custom').classList.remove('grid');

        if (type === 'monthly') {
            document.getElementById('section-monthly').classList.remove('hidden');
            document.getElementById('section-monthly').classList.add('grid');
        } else if (type === 'weekly') {
            document.getElementById('section-weekly').classList.remove('hidden');
        } else if (type === 'custom') {
            document.getElementById('section-custom').classList.remove('hidden');
            document.getElementById('section-custom').classList.add('grid');
        }
    }
</script>
@endsection

