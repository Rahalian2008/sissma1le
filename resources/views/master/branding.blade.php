@extends('layouts.app')

@section('title', 'Logo, KOP Surat & Nama Aplikasi - ' . $appName)

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full text-xs font-bold">
                    BRANDING & IDENTITAS RESMI
                </span>
                <span class="px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-xs font-bold">
                    Provinsi {{ $schoolProvince }}
                </span>
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold">
                    {{ $schoolBranch }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white">Pengaturan Branding & Nama Aplikasi</h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Sesuaikan nama aplikasi, logo visual sistem, dan gambar KOP surat resmi sekolah untuk seluruh lembar cetak dokumen laporan.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('master.school_profile') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl text-xs flex items-center gap-2 transition backdrop-blur-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Profil Sekolah
            </a>
            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Menu Laporan
            </a>
        </div>
    </div>

    @if(session('error') || $errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-300 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <strong>Gagal Memproses Berkas:</strong> {{ session('error') ?? $errors->first() }}
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <div>
                <strong>Berhasil:</strong> {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Bagian 1: Edit Nama Aplikasi & Tagline -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-7">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Identitas & Nama Aplikasi</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Nama yang tampil pada Sidebar, Mobile Header, Tab Browser, dan Halaman Login</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-xs rounded-full border border-indigo-100 dark:border-indigo-800">
                Aktif: {{ $appName }}
            </span>
        </div>

        <form action="{{ route('master.branding.app_name.update') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf
            <div class="lg:col-span-2 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Aplikasi Utama <span class="text-rose-500">*</span></label>
                    <input type="text" name="app_name" id="inputAppName" value="{{ old('app_name', $appName) }}" required maxlength="100"
                        placeholder="Contoh: SISWA SMA1LE"
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition">
                    <p class="text-[11px] text-slate-400 mt-1">Nama ringkas atau akronim aplikasi yang tampil sebagai judul utama sistem.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Sub-nama / Tagline Aplikasi</label>
                    <input type="text" name="app_tagline" id="inputAppTagline" value="{{ old('app_tagline', $appTagline) }}" maxlength="255"
                        placeholder="Contoh: Sistem Informasi Siswa Terpadu SMAN 1 Lengkong"
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-white outline-none focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition">
                    <p class="text-[11px] text-slate-400 mt-1">Deskripsi singkat sistem yang tampil pada header dan halaman login.</p>
                </div>

                <div>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-md transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Nama Aplikasi
                    </button>
                </div>
            </div>

            <!-- Live Mockup Box -->
            <div class="bg-slate-900 text-white p-5 rounded-2xl flex flex-col justify-between space-y-4 border border-slate-800">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pratinjau Tampilan Header</div>
                    <div class="flex items-center gap-3 p-3 bg-slate-800/80 rounded-xl border border-slate-700/60">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-emerald-400 p-0.5 shrink-0 flex items-center justify-center font-black text-xs">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" class="w-full h-full object-contain rounded-[10px] p-0.5 bg-slate-950" alt="Logo">
                            @else
                                <span class="text-white">1LE</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-black text-white truncate" id="livePreviewAppName">{{ $appName }}</div>
                            <div class="text-[10px] text-slate-400 truncate" id="livePreviewAppTagline">{{ $schoolName }}</div>
                        </div>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400 bg-slate-950/60 p-3 rounded-xl border border-slate-800">
                    💡 Perubahan nama aplikasi otomatis diterapkan ke seluruh antarmuka (Sidebar, Mobile Drawer, Login, & Dokumen).
                </div>
            </div>
        </form>
    </div>

    <!-- Grid 2 Kolom: Logo & KOP Surat -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kolom Kiri: Logo Aplikasi (Menggantikan 1LE) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 dark:text-white">Logo Resmi Aplikasi</h2>
                            <p class="text-[11px] text-slate-500">Ikon resmi sekolah (menggantikan default 1LE)</p>
                        </div>
                    </div>
                    @if($logoUrl)
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold">
                            Logo Kustom
                        </span>
                    @else
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-full text-[10px] font-bold">
                            Default (1LE)
                        </span>
                    @endif
                </div>

                <!-- Petunjuk Ketentuan & Spesifikasi File Logo -->
                <div class="bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/60 rounded-2xl p-4 text-xs space-y-2">
                    <div class="font-bold text-blue-900 dark:text-blue-300 flex items-center gap-1.5">
                        <span>📋</span> Ketentuan & Spesifikasi File Logo:
                    </div>
                    <ul class="text-[11px] text-blue-800 dark:text-blue-200 space-y-1.5 list-disc list-inside">
                        <li><strong>Jenis/Format File:</strong> <span class="font-mono bg-blue-100 dark:bg-blue-900 px-1 py-0.5 rounded text-[10px]">PNG</span>, <span class="font-mono bg-blue-100 dark:bg-blue-900 px-1 py-0.5 rounded text-[10px]">JPG</span>, <span class="font-mono bg-blue-100 dark:bg-blue-900 px-1 py-0.5 rounded text-[10px]">WEBP</span>, atau <span class="font-mono bg-blue-100 dark:bg-blue-900 px-1 py-0.5 rounded text-[10px]">SVG</span>.</li>
                        <li><strong>Ukuran File Maksimal:</strong> 2 Megabytes (2.048 KB).</li>
                        <li><strong>Dimensi Rekomendasi:</strong> <strong>512 x 512 piksel</strong> (Rasio 1:1 persegi sempurna).</li>
                        <li><strong>Dimensi Minimal:</strong> 128 x 128 piksel.</li>
                        <li><strong>Saran Desain:</strong> Sangat disarankan berlatar transparan (PNG transparan) agar serasi dengan sidebar gelap dan terang.</li>
                    </ul>
                </div>

                <!-- Preview Box -->
                <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-6 text-center flex flex-col items-center justify-center min-h-[160px]">
                    <div id="logoPreviewContainer">
                        @if($logoUrl)
                            <div class="relative group">
                                <img id="logoPreviewImg" src="{{ $logoUrl }}" alt="Logo Aplikasi" class="w-24 h-24 object-contain rounded-2xl bg-white dark:bg-slate-900 p-2 shadow-md border border-slate-200 dark:border-slate-700 mx-auto">
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200 mt-3">Logo Kustom Sedang Aktif</p>
                        @else
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-blue-500/20 mx-auto">
                                1LE
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200 mt-3">Logo Standar Bawaan (1LE)</p>
                        @endif
                    </div>
                    <!-- Dynamic feedback on select -->
                    <div id="logoFileInfo" class="hidden mt-3 p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-left text-[11px] w-full"></div>
                </div>

                <!-- Form Upload Logo -->
                <form action="{{ route('master.branding.logo.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih File Logo Baru</label>
                        <input type="file" name="logo" id="logoInput" required accept="image/png,image/jpeg,image/webp,image/svg+xml"
                            class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 dark:file:bg-blue-950 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 cursor-pointer">
                    </div>

                    <button type="submit" id="btnSubmitLogo" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Simpan & Pasang Logo
                    </button>
                </form>

                @if($logoUrl)
                    <form action="{{ route('master.branding.logo.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset logo ke default 1LE?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2 px-3 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Reset ke Default (1LE)
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: KOP Surat Resmi (Dokumen Cetak) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 dark:text-white">KOP Surat Resmi Dokumen Cetak</h2>
                            <p class="text-[11px] text-slate-500">Header seluruh dokumen cetak laporan A4 (Presensi, Tatib, 7 Kebiasaan, & Gabungan)</p>
                        </div>
                    </div>
                    @if($kopUrl)
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold">
                            Gambar KOP Aktif
                        </span>
                    @else
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[10px] font-bold">
                            Format Teks Jawa Barat V
                        </span>
                    @endif
                </div>

                <!-- Petunjuk Ketentuan & Spesifikasi File KOP Surat -->
                <div class="bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900/60 rounded-2xl p-4 text-xs space-y-2">
                    <div class="font-bold text-indigo-900 dark:text-indigo-300 flex items-center gap-1.5">
                        <span>📋</span> Ketentuan & Spesifikasi File KOP Surat:
                    </div>
                    <ul class="text-[11px] text-indigo-800 dark:text-indigo-200 space-y-1.5 list-disc list-inside">
                        <li><strong>Jenis/Format File:</strong> <span class="font-mono bg-indigo-100 dark:bg-indigo-900 px-1 py-0.5 rounded text-[10px]">PNG</span>, <span class="font-mono bg-indigo-100 dark:bg-indigo-900 px-1 py-0.5 rounded text-[10px]">JPG</span>, <span class="font-mono bg-indigo-100 dark:bg-indigo-900 px-1 py-0.5 rounded text-[10px]">JPEG</span>, atau <span class="font-mono bg-indigo-100 dark:bg-indigo-900 px-1 py-0.5 rounded text-[10px]">WEBP</span>.</li>
                        <li><strong>Ukuran File Maksimal:</strong> 3 Megabytes (3.072 KB).</li>
                        <li><strong>Dimensi Rekomendasi:</strong> <strong>1500 x 250 px</strong> s/d <strong>2000 x 300 px</strong> (Format memanjang/landscape, rasio lebar:tinggi ~6:1).</li>
                        <li><strong>Dimensi Minimal:</strong> Lebar minimal <strong>1000 piksel</strong>, tinggi antara 150 px s/d 350 px.</li>
                        <li><strong>Kualitas Cetak:</strong> Disarankan resolusi tajam (minimal 150–300 DPI) agar teks instansi dan logo Pemprov Jabar tidak pecah saat dicetak ke kertas A4 atau PDF.</li>
                    </ul>
                </div>

                <!-- Preview Box KOP Surat -->
                <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-4 sm:p-5 text-center flex flex-col items-center justify-center min-h-[180px]">
                    <div id="kopPreviewContainer" class="w-full">
                        @if($kopUrl)
                            <div class="w-full bg-white dark:bg-slate-900 p-3 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                                <img id="kopPreviewImg" src="{{ $kopUrl }}" alt="KOP Surat Resmi" class="w-full max-h-36 object-contain mx-auto">
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200 mt-3">Gambar KOP Surat Resmi Sedang Digunakan</p>
                        @else
                            <div class="w-full bg-white dark:bg-slate-900 p-4 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-center font-serif text-xs">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">PEMERINTAH DAERAH PROVINSI {{ $province ?? 'JAWA BARAT' }}</div>
                                <div class="text-[11px] font-bold uppercase text-slate-700 dark:text-slate-300">DINAS PENDIDIKAN</div>
                                <div class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $schoolBranch ?? 'CABANG DINAS PENDIDIKAN WILAYAH V' }}</div>
                                <div class="text-xs font-black uppercase tracking-wide text-slate-900 dark:text-white mt-1">{{ $schoolName }}</div>
                                <div class="text-[9.5px] font-bold text-slate-700 dark:text-slate-300 mt-0.5">
                                    NPSN: {{ $schoolNpsn ?? '20539123' }} • NSS: {{ $schoolNss ?? '301051408001' }} • Akreditasi: {{ $schoolAccreditation ?? 'A (Unggul)' }}
                                </div>
                                <div class="text-[9.5px] text-slate-600 dark:text-slate-300 mt-0.5 leading-tight">
                                    {{ $schoolAddress }}
                                </div>
                                <div class="text-[9px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    Email: {{ $schoolEmail }} | Website: {{ $schoolWebsite }}
                                </div>
                                <div class="border-b-2 border-slate-800 dark:border-slate-400 mt-2"></div>
                                <div class="border-b border-slate-800 dark:border-slate-400 mt-0.5"></div>
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200 mt-3">Format Teks Standar Jawa Barat Wilayah V (Aktif)</p>
                        @endif
                    </div>
                    <!-- Dynamic feedback on select -->
                    <div id="kopFileInfo" class="hidden mt-3 p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-left text-[11px] w-full"></div>
                </div>

                <!-- Form Upload KOP Surat -->
                <form action="{{ route('master.branding.kop.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Gambar KOP Surat Baru</label>
                        <input type="file" name="kop" id="kopInput" required accept="image/png,image/jpeg,image/webp"
                            class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 dark:file:bg-indigo-950 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 cursor-pointer">
                    </div>

                    <button type="submit" id="btnSubmitKop" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.99] text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Simpan & Terapkan KOP Surat
                    </button>
                </form>

                @if($kopUrl)
                    <form action="{{ route('master.branding.kop.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar KOP dan kembali ke format teks standar Jawa Barat Wilayah V?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2 px-3 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus Gambar KOP (Kembali ke Teks Standar Wilayah V)
                        </button>
                    </form>
                @endif

                <!-- Uji Pratinjau Cetak -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-2">Uji Cepat Pratinjau Dokumen Cetak:</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('reports.print.attendance') }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-semibold flex items-center gap-1.5 transition">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Cetak Presensi
                        </a>
                        <a href="{{ route('reports.print.discipline') }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-semibold flex items-center gap-1.5 transition">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Cetak Tata Tertib
                        </a>
                        <a href="{{ route('reports.print.habits') }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-semibold flex items-center gap-1.5 transition">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Cetak 7 Kebiasaan
                        </a>
                        <a href="{{ route('reports.print.combined') }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-[11px] font-semibold flex items-center gap-1.5 transition">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Cetak 1 Dokumen Terpadu
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Live input preview for Application Name
    const inputAppName = document.getElementById('inputAppName');
    const inputAppTagline = document.getElementById('inputAppTagline');
    const livePreviewAppName = document.getElementById('livePreviewAppName');
    const livePreviewAppTagline = document.getElementById('livePreviewAppTagline');

    if (inputAppName && livePreviewAppName) {
        inputAppName.addEventListener('input', (e) => {
            livePreviewAppName.textContent = e.target.value.trim() || 'SISWA SMA1LE';
        });
    }
    if (inputAppTagline && livePreviewAppTagline) {
        inputAppTagline.addEventListener('input', (e) => {
            livePreviewAppTagline.textContent = e.target.value.trim() || '{{ $schoolName }}';
        });
    }

    // Client-side Dimension & File Validation for Logo
    const logoInput = document.getElementById('logoInput');
    const logoFileInfo = document.getElementById('logoFileInfo');

    if (logoInput && logoFileInfo) {
        logoInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (!file) {
                logoFileInfo.classList.add('hidden');
                return;
            }

            const fileSizeKb = Math.round(file.size / 1024);
            const isSvg = file.type === 'image/svg+xml';

            if (isSvg) {
                logoFileInfo.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700 dark:text-slate-200">File SVG: ${file.name}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Format Vektor Ideal ✓</span>
                    </div>
                    <div class="text-slate-500 mt-1">Ukuran File: <strong>${fileSizeKb} KB</strong> (Batas maks: 2.048 KB). Format vektor scalable sempurna untuk semua resolusi layar.</div>
                `;
                logoFileInfo.classList.remove('hidden');
                return;
            }

            const img = new Image();
            const objectUrl = URL.createObjectURL(file);
            img.onload = function() {
                const w = this.naturalWidth;
                const h = this.naturalHeight;
                const ratio = (w / h).toFixed(2);
                const isSquare = Math.abs(w - h) <= 10;
                const isMinValid = w >= 128 && h >= 128;
                const isSizeValid = fileSizeKb <= 2048;

                let statusBadge = isSquare && isMinValid && isSizeValid
                    ? `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Dimensi Sangat Baik ✓</span>`
                    : `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Perhatikan Rasio / Ukuran ⚠️</span>`;

                logoFileInfo.innerHTML = `
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-800 dark:text-slate-100 truncate">${file.name}</span>
                        ${statusBadge}
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[10.5px] text-slate-600 dark:text-slate-300">
                        <div>Dimensi: <strong>${w} x ${h} px</strong> (Rasio: ${ratio}:1)</div>
                        <div>Ukuran: <strong>${fileSizeKb} KB</strong> / 2 MB</div>
                    </div>
                    ${!isSquare ? '<div class="text-amber-600 dark:text-amber-400 mt-1 text-[10px]">⚠️ Gambar tidak bujursangkar (1:1). Logo mungkin tampak agak terdistorsi jika rasio tidak sama.</div>' : ''}
                    ${!isMinValid ? '<div class="text-rose-600 dark:text-rose-400 mt-1 text-[10px]">❌ Dimensi di bawah minimal 128x128 px, disarankan minimal 512x512 px.</div>' : ''}
                `;
                logoFileInfo.classList.remove('hidden');
                URL.revokeObjectURL(objectUrl);
            };
            img.src = objectUrl;
        });
    }

    // Client-side Dimension & File Validation for KOP Surat
    const kopInput = document.getElementById('kopInput');
    const kopFileInfo = document.getElementById('kopFileInfo');

    if (kopInput && kopFileInfo) {
        kopInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (!file) {
                kopFileInfo.classList.add('hidden');
                return;
            }

            const fileSizeKb = Math.round(file.size / 1024);
            const img = new Image();
            const objectUrl = URL.createObjectURL(file);

            img.onload = function() {
                const w = this.naturalWidth;
                const h = this.naturalHeight;
                const ratio = (w / h).toFixed(2);
                const isLandscape = w > h;
                const isWidthGood = w >= 1000;
                const isSizeValid = fileSizeKb <= 3072;

                let statusBadge = isLandscape && isWidthGood && isSizeValid
                    ? `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Dimensi KOP Sangat Baik ✓</span>`
                    : `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Perhatikan Dimensi KOP ⚠️</span>`;

                kopFileInfo.innerHTML = `
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-800 dark:text-slate-100 truncate">${file.name}</span>
                        ${statusBadge}
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[10.5px] text-slate-600 dark:text-slate-300">
                        <div>Dimensi: <strong>${w} x ${h} px</strong> (Rasio: ${ratio}:1)</div>
                        <div>Ukuran: <strong>${fileSizeKb} KB</strong> / 3 MB</div>
                    </div>
                    ${!isLandscape ? '<div class="text-rose-600 dark:text-rose-400 mt-1 text-[10px]">❌ Gambar bukan format landscape horizontal memanjang.</div>' : ''}
                    ${!isWidthGood ? '<div class="text-amber-600 dark:text-amber-400 mt-1 text-[10px]">⚠️ Lebar di bawah 1000 px, hasil cetak mungkin agak buram pada kertas A4. Disarankan 1500–2000 px.</div>' : ''}
                `;
                kopFileInfo.classList.remove('hidden');
                URL.revokeObjectURL(objectUrl);
            };
            img.src = objectUrl;
        });
    }
</script>
@endsection
