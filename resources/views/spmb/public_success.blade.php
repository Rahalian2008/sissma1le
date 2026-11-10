<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran SPMB - {{ $spmb->full_name }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col items-center justify-center p-4 antialiased">

    <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden my-8">
        
        <!-- Header Banner Success -->
        <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-700 p-6 sm:p-8 text-white text-center relative">
            <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center mx-auto mb-3 text-3xl">
                ✓
            </div>
            <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider mb-2">
                Pendaftaran Berhasil Dikirim
            </span>
            <h1 class="text-xl sm:text-2xl font-heading font-black tracking-tight">
                Bukti Pendaftaran Peserta Didik Baru (SPMB)
            </h1>
            <p class="text-emerald-100 text-xs sm:text-sm mt-1">
                {{ $school->name ?? 'SMA Negeri 1 Lengkong' }} &bull; Tahun Ajaran 2026/2027
            </p>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs sm:text-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-200 text-xs sm:text-sm font-semibold">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Kartu Nomor Registrasi -->
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 text-center space-y-1">
                <span class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Nomor Registrasi Resmi</span>
                <div class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-wider">
                    {{ $spmb->registration_number }}
                </div>
                <div class="text-xs font-bold text-slate-500">
                    Jalur: <span class="text-slate-800 dark:text-slate-200">{{ $spmb->selection_track }}</span>
                </div>
            </div>

            <!-- Jika Jalur TKA, Tampilkan Kartu Jadwal Ujian TKA -->
            @if(!empty($spmb->tka_exam_number))
                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border-2 border-amber-300 dark:border-amber-700 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black uppercase text-amber-900 dark:text-amber-200">
                            🎯 Jadwal Ujian Tes Kemampuan Akademik (TKA)
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900">
                            Wajib Hadir
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                        <div>
                            <span class="text-slate-500 text-[11px]">No. Peserta Ujian:</span>
                            <div class="font-black text-slate-900 dark:text-white">{{ $spmb->tka_exam_number }}</div>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px]">Tanggal Ujian:</span>
                            <div class="font-black text-slate-900 dark:text-white">
                                {{ $spmb->tka_exam_date ? $spmb->tka_exam_date->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-500 text-[11px]">Sesi & Lokasi Ujian:</span>
                            <div class="font-bold text-slate-900 dark:text-white">{{ $spmb->tka_exam_session ?? 'Lab Komputer CBT SMAN 1 Lengkong' }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Rincian Data Siswa -->
            <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-xs space-y-2.5">
                <h4 class="font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-100 dark:border-slate-800">
                    Ringkasan Biodata Calon Siswa
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                        <span class="text-slate-400 text-[11px] block">Nama Lengkap</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $spmb->full_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">NISN</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $spmb->nisn }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Jenis Kelamin</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $spmb->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Asal Sekolah</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $spmb->school_origin }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Rata-rata Rapor</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($spmb->average_score, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Status Pendaftaran</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                            {{ $spmb->status }}
                        </span>
                    </div>
                    <div class="col-span-2 sm:col-span-3">
                        <span class="text-slate-400 text-[11px] block">Nama Orang Tua & WhatsApp</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $spmb->parent_name }} &bull; {{ $spmb->parent_phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Langkah Selanjutnya -->
            <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-800/60 text-xs text-slate-600 dark:text-slate-300 space-y-1.5 leading-relaxed">
                <div class="font-bold text-indigo-900 dark:text-indigo-200">Langkah Selanjutnya:</div>
                <ol class="list-decimal list-inside space-y-1 text-[11px]">
                    <li>Cetak / Simpan file PDF Kartu Bukti Pendaftaran ini sebagai tanda bukti resmi.</li>
                    <li>Bawalah kartu bukti pendaftaran dan berkas persyaratan asli saat verifikasi berkas di SMAN 1 Lengkong.</li>
                    <li>Bagi pendaftar jalur TKA, hadir 30 menit sebelum sesi ujian dimulai dengan membawa Kartu Peserta Ujian.</li>
                </ol>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('website.home') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-center text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition">
                    ← Kembali ke Beranda
                </a>

                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                    <a href="{{ route('spmb.public.register') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-center text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Daftar Calon Lain
                    </a>

                    <a href="{{ route('spmb.public.print', $spmb->registration_number) }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-black text-xs shadow-lg shadow-indigo-600/30 transition-all hover:scale-[1.02]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Pendaftaran</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>

