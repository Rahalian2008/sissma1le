<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kuesioner Tracer Study Alumni - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    @php
        $compFavicon = $schoolFaviconUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
        $compLogo = $schoolLogoUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
    @endphp
    <link rel="icon" type="image/png" href="{{ $compFavicon }}">
    <link rel="shortcut icon" href="{{ $compFavicon }}">
    <meta name="description" content="Formulir resmi penelusuran tamatan dan tracer study alumni SMAN 1 Lengkong standar Kemdikbudristek. Pemetaan studi lanjut, dunia kerja, dan evaluasi kompetensi lulusan.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Header -->
    <header class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-18 flex items-center justify-between gap-4">
            <a href="{{ route('website.home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 via-indigo-600 to-emerald-400 p-0.5 shadow-md shrink-0">
                    <div class="w-full h-full bg-white dark:bg-slate-900 rounded-[14px] flex items-center justify-center overflow-hidden p-1">
                        @if($compLogo)
                            <img src="{{ $compLogo }}" alt="Logo {{ $school->name ?? 'Sekolah' }}" class="w-full h-full object-contain">
                        @else
                            <span class="font-heading font-black text-indigo-600 text-base">S1L</span>
                        @endif
                    </div>
                </div>
                <div>
                    <h1 class="font-heading font-black text-slate-900 dark:text-white text-base sm:text-lg tracking-tight leading-none group-hover:text-indigo-600 transition-colors">
                        {{ $school->name ?? 'SMAN 1 LENGKONG' }}
                    </h1>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Tracer Study &amp; Penelusuran Tamatan Standar Kemdikbudristek
                    </p>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('website.home') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Website Sekolah</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-4xl mx-auto w-full px-4 sm:px-6 py-8">
        
        <!-- Hero Header -->
        <div class="bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden mb-8 border border-indigo-800/40">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 mb-3">
                    🎓 Layanan Bimbingan Konseling (BK) &amp; Alumni
                </span>
                <h2 class="font-heading font-black text-2xl sm:text-3xl tracking-tight text-white mb-2">
                    Kuesioner Tracer Study Alumni
                </h2>
                <p class="text-sm text-slate-300 max-w-3xl leading-relaxed">
                    Halo Rekan Alumni Kebanggaan {{ $school->name ?? 'SMAN 1 Lengkong' }}! Mohon kesediaan Anda meluangkan waktu sejenak untuk mengisi formulir penelusuran tamatan standar Kemdikbudristek ini. Data Anda sangat berharga untuk pemetaan mutu lulusan, akreditasi sekolah, serta pengembangan kurikulum dan bimbingan karier adik-adik kelas Anda.
                </p>
                <div class="flex flex-wrap items-center gap-3 mt-4 text-xs text-slate-300">
                    <span class="inline-flex items-center gap-1 bg-white/10 px-2.5 py-1 rounded-lg">📋 5 Bagian Kuesioner Baku</span>
                    <span class="inline-flex items-center gap-1 bg-white/10 px-2.5 py-1 rounded-lg">⏱️ Estimasi Pengisian: 2-3 Menit</span>
                    <span class="inline-flex items-center gap-1 bg-white/10 px-2.5 py-1 rounded-lg">🔒 Kerahasiaan Data Terjamin</span>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-sm">
            <div class="font-bold flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Mohon periksa kembali isian formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Formulir Tracer Study -->
        <form action="{{ route('bk.tracer.public_store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- ================= BAGIAN 1: IDENTITAS & LATAR BELAKANG ALUMNI ================= -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">
                        01
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Identitas &amp; Latar Belakang Alumni</h3>
                        <p class="text-xs text-slate-500">Informasi pribadi, tahun lulus, peminatan sekolah, dan pembiayaan pendidikan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <!-- Nama Lengkap -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                               placeholder="Masukkan nama lengkap sesuai ijazah..."
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>

                    <!-- NISN & NIS -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            NISN (Nomor Induk Siswa Nasional)
                        </label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" placeholder="Contoh: 0051234567"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            NIS Sekolah (Opsional)
                        </label>
                        <input type="text" name="nis" value="{{ old('nis') }}" placeholder="Contoh: 212210001"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 cursor-pointer hover:bg-indigo-50/50 dark:hover:bg-slate-700/50 transition">
                                <input type="radio" name="gender" value="L" {{ old('gender', 'L') === 'L' ? 'checked' : '' }} required class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">Laki-laki</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 cursor-pointer hover:bg-indigo-50/50 dark:hover:bg-slate-700/50 transition">
                                <input type="radio" name="gender" value="P" {{ old('gender') === 'P' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">Perempuan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tahun Kelulusan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tahun Lulus <span class="text-rose-500">*</span>
                        </label>
                        <select name="graduation_year" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">-- Pilih Tahun Lulus --</option>
                            @foreach($graduationYears as $year)
                                <option value="{{ $year }}" {{ old('graduation_year') == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Program Studi / Jurusan di SMA -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Program Studi / Peminatan di SMA <span class="text-rose-500">*</span>
                        </label>
                        <select name="high_school_major" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">-- Pilih Jurusan / Peminatan SMA --</option>
                            <option value="MIPA (Matematika &amp; Ilmu Pengetahuan Alam)" {{ old('high_school_major') === 'MIPA (Matematika & Ilmu Pengetahuan Alam)' ? 'selected' : '' }}>MIPA (Matematika &amp; IPA)</option>
                            <option value="IPS (Ilmu Pengetahuan Sosial)" {{ old('high_school_major') === 'IPS (Ilmu Pengetahuan Sosial)' ? 'selected' : '' }}>IPS (Ilmu Pengetahuan Sosial)</option>
                            <option value="Bahasa &amp; Budaya" {{ old('high_school_major') === 'Bahasa & Budaya' ? 'selected' : '' }}>Bahasa &amp; Budaya</option>
                            <option value="Kurikulum Merdeka (Fase F - Peminatan Pilihan)" {{ old('high_school_major') === 'Kurikulum Merdeka (Fase F - Peminatan Pilihan)' ? 'selected' : '' }}>Kurikulum Merdeka (Fase F - Peminatan Pilihan)</option>
                            <option value="Lainnya" {{ old('high_school_major') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Kelas Terakhir di SMA -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Rombel / Kelas Terakhir di SMA
                        </label>
                        <input type="text" name="last_class" value="{{ old('last_class') }}" placeholder="Contoh: XII Merdeka 7 / XII MIPA 1"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>

                    <!-- No WhatsApp / HP -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 081234567890"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Alamat Email Aktif (Opsional)
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="alumni@gmail.com"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>

                    <!-- Kota Domisili Sekarang -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Kota / Kabupaten Domisili Saat Ini
                        </label>
                        <input type="text" name="current_city" value="{{ old('current_city') }}" placeholder="Contoh: Sukabumi / Bandung / Jakarta"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>

                    <!-- Sumber Pembiayaan Pendidikan di SMA -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Sumber Utama Pembiayaan Selama Sekolah di SMA <span class="text-rose-500">*</span>
                        </label>
                        <select name="study_funding_source" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">-- Pilih Sumber Pembiayaan --</option>
                            <option value="Biaya sendiri / keluarga" {{ old('study_funding_source') === 'Biaya sendiri / keluarga' ? 'selected' : '' }}>Biaya sendiri / keluarga</option>
                            <option value="Beasiswa pemerintah (KIP / PIP / Pemda)" {{ old('study_funding_source') === 'Beasiswa pemerintah (KIP / PIP / Pemda)' ? 'selected' : '' }}>Beasiswa pemerintah (KIP / PIP / Pemda)</option>
                            <option value="Beasiswa swasta / yayasan" {{ old('study_funding_source') === 'Beasiswa swasta / yayasan' ? 'selected' : '' }}>Beasiswa swasta / yayasan</option>
                            <option value="Lainnya" {{ old('study_funding_source') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ================= BAGIAN 2: STATUS KEGIATAN SAAT INI ================= -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm">
                        02
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Status Kegiatan Saat Ini</h3>
                        <p class="text-xs text-slate-500">Pilih salah satu status kegiatan utama yang sedang Anda jalani saat ini</p>
                    </div>
                </div>

                <!-- 5 Pilihan Status Utama Standar Kemdikbudristek -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
                    <!-- 1. Bekerja -->
                    <label class="activity-option p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 cursor-pointer hover:border-emerald-500 transition relative flex flex-col justify-between">
                        <input type="radio" name="activity_status" value="BEKERJA" {{ old('activity_status') === 'BEKERJA' ? 'checked' : '' }} required class="absolute top-3 right-3 text-emerald-600 focus:ring-emerald-500">
                        <div class="text-2xl mb-2">💼</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">Bekerja (Full time / Part time)</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Karyawan swasta, ASN, BUMN, instansi pemerintah, institusi</div>
                        </div>
                    </label>

                    <!-- 2. Wirausaha -->
                    <label class="activity-option p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 cursor-pointer hover:border-amber-500 transition relative flex flex-col justify-between">
                        <input type="radio" name="activity_status" value="WIRAUSAHA" {{ old('activity_status') === 'WIRAUSAHA' ? 'checked' : '' }} class="absolute top-3 right-3 text-amber-600 focus:ring-amber-500">
                        <div class="text-2xl mb-2">🚀</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">Wiraswasta / Berwirausaha</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Membuka usaha sendiri, founder bisnis, freelance profesional, mitra usaha</div>
                        </div>
                    </label>

                    <!-- 3. Kuliah / Studi Lanjut -->
                    <label class="activity-option p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 cursor-pointer hover:border-indigo-500 transition relative flex flex-col justify-between">
                        <input type="radio" name="activity_status" value="KULIAH" {{ old('activity_status', 'KULIAH') === 'KULIAH' ? 'checked' : '' }} class="absolute top-3 right-3 text-indigo-600 focus:ring-indigo-500">
                        <div class="text-2xl mb-2">🎓</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">Melanjutkan Pendidikan (Kuliah)</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">PTN, PTS, Kedinasan, Politeknik, Vokasi / Akademik</div>
                        </div>
                    </label>

                    <!-- 4. Belum Memungkinkan Bekerja -->
                    <label class="activity-option p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 cursor-pointer hover:border-rose-500 transition relative flex flex-col justify-between">
                        <input type="radio" name="activity_status" value="BELUM_MEMUNGKINKAN" {{ old('activity_status') === 'BELUM_MEMUNGKINKAN' ? 'checked' : '' }} class="absolute top-3 right-3 text-rose-600 focus:ring-rose-500">
                        <div class="text-2xl mb-2">🏡</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">Belum Memungkinkan Bekerja</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Misal: mengurus keluarga, kondisi kesehatan, atau persiapan khusus</div>
                        </div>
                    </label>

                    <!-- 5. Tidak Bekerja tetapi Sedang Mencari Kerja -->
                    <label class="activity-option p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 cursor-pointer hover:border-slate-500 transition relative flex flex-col justify-between sm:col-span-2 lg:col-span-2">
                        <input type="radio" name="activity_status" value="MENCARI_KERJA" {{ old('activity_status') === 'MENCARI_KERJA' ? 'checked' : '' }} class="absolute top-3 right-3 text-slate-600 focus:ring-slate-500">
                        <div class="text-2xl mb-2">🔍</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">Tidak Bekerja tetapi Sedang Mencari Kerja</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Sedang aktif melamar pekerjaan, proses seleksi kerja, atau gap year persiapan karier</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- ================= BAGIAN 3: TRANSISI KE DUNIA KERJA (BEKERJA & WIRAUSAHA) ================= -->
            <div id="section-kerja-wirausaha" class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
                        03
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Transisi ke Dunia Kerja &amp; Informasi Pekerjaan</h3>
                        <p class="text-xs text-slate-500">Khusus alumni yang saat ini berstatus Bekerja atau Wiraswasta</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <!-- Kapan mulai mencari kerja -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Kapan Anda Mulai Mencari Pekerjaan?
                        </label>
                        <select name="job_search_start" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Pilih Waktu Mulai Mencari --</option>
                            <option value="Sebelum lulus" {{ old('job_search_start') === 'Sebelum lulus' ? 'selected' : '' }}>Sebelum lulus dari sekolah</option>
                            <option value="Setelah lulus" {{ old('job_search_start') === 'Setelah lulus' ? 'selected' : '' }}>Setelah lulus resmi</option>
                            <option value="Tidak mencari kerja" {{ old('job_search_start') === 'Tidak mencari kerja' ? 'selected' : '' }}>Tidak mencari kerja (langsung berwirausaha / ditawari kerja)</option>
                        </select>
                    </div>

                    <!-- Waktu tunggu -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Berapa Lama Waktu Tunggu Hingga Mendapat Pekerjaan Pertama?
                        </label>
                        <select name="job_waiting_time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Pilih Waktu Tunggu --</option>
                            <option value="Sebelum Lulus (Langsung Diterima)" {{ old('job_waiting_time') === 'Sebelum Lulus (Langsung Diterima)' ? 'selected' : '' }}>Sebelum Lulus (Langsung Diterima)</option>
                            <option value="Kurang dari 1 Bulan setelah lulus" {{ old('job_waiting_time') === 'Kurang dari 1 Bulan setelah lulus' ? 'selected' : '' }}>Kurang dari 1 Bulan setelah lulus</option>
                            <option value="1 - 3 Bulan setelah lulus" {{ old('job_waiting_time') === '1 - 3 Bulan setelah lulus' ? 'selected' : '' }}>1 - 3 Bulan setelah lulus</option>
                            <option value="3 - 6 Bulan setelah lulus" {{ old('job_waiting_time') === '3 - 6 Bulan setelah lulus' ? 'selected' : '' }}>3 - 6 Bulan setelah lulus</option>
                            <option value="Lebih dari 6 Bulan setelah lulus" {{ old('job_waiting_time') === 'Lebih dari 6 Bulan setelah lulus' ? 'selected' : '' }}>Lebih dari 6 Bulan setelah lulus</option>
                        </select>
                    </div>

                    <!-- Cara mendapatkan pekerjaan pertama -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Bagaimana Cara Anda Mendapatkan Pekerjaan Pertama?
                        </label>
                        <select name="job_search_method" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Pilih Cara Mendapatkan Kerja --</option>
                            <option value="Melalui bursa kerja / Job Fair sekolah / kampus" {{ old('job_search_method') === 'Melalui bursa kerja / Job Fair sekolah / kampus' ? 'selected' : '' }}>Melalui bursa kerja / Job Fair sekolah / kampus</option>
                            <option value="Relasi / Rekomendasi Guru / Dosen / Keluarga / Teman" {{ old('job_search_method') === 'Relasi / Rekomendasi Guru / Dosen / Keluarga / Teman' ? 'selected' : '' }}>Relasi / Rekomendasi Guru / Dosen / Keluarga / Teman</option>
                            <option value="Iklan lowongan online / Job Portal (Jobstreet, Glints, LinkedIn, dll)" {{ old('job_search_method') === 'Iklan lowongan online / Job Portal (Jobstreet, Glints, LinkedIn, dll)' ? 'selected' : '' }}>Iklan lowongan online / Job Portal (Jobstreet, Glints, LinkedIn, dll)</option>
                            <option value="Magang / PKL sebelumnya di tempat yang sama" {{ old('job_search_method') === 'Magang / PKL sebelumnya di tempat yang sama' ? 'selected' : '' }}>Magang / PKL sebelumnya di tempat yang sama</option>
                            <option value="Membangun usaha sendiri / berwirausaha" {{ old('job_search_method') === 'Membangun usaha sendiri / berwirausaha' ? 'selected' : '' }}>Membangun usaha sendiri / berwirausaha</option>
                            <option value="Lainnya" {{ old('job_search_method') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Jenis instansi/perusahaan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Jenis Instansi / Perusahaan Tempat Bekerja
                        </label>
                        <select name="company_type" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Pilih Jenis Instansi --</option>
                            <option value="Instansi pemerintah / ASN / TNI / Polri" {{ old('company_type') === 'Instansi pemerintah / ASN / TNI / Polri' ? 'selected' : '' }}>Instansi pemerintah / ASN / TNI / Polri</option>
                            <option value="BUMN / BUMD" {{ old('company_type') === 'BUMN / BUMD' ? 'selected' : '' }}>BUMN / BUMD</option>
                            <option value="Perusahaan swasta nasional / multinasional" {{ old('company_type') === 'Perusahaan swasta nasional / multinasional' ? 'selected' : '' }}>Perusahaan swasta nasional / multinasional</option>
                            <option value="Organisasi non-profit / LSM / Yayasan" {{ old('company_type') === 'Organisasi non-profit / LSM / Yayasan' ? 'selected' : '' }}>Organisasi non-profit / LSM / Yayasan</option>
                            <option value="Wiraswasta / Perusahaan sendiri" {{ old('company_type') === 'Wiraswasta / Perusahaan sendiri' ? 'selected' : '' }}>Wiraswasta / Perusahaan sendiri</option>
                        </select>
                    </div>

                    <!-- Tipe Kerja (Full/Part Time) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Status Ikatan Kerja
                        </label>
                        <select name="work_type" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="Full Time (Purna Waktu)" {{ old('work_type') === 'Full Time (Purna Waktu)' ? 'selected' : '' }}>Full Time (Purna Waktu)</option>
                            <option value="Part Time (Paruh Waktu)" {{ old('work_type') === 'Part Time (Paruh Waktu)' ? 'selected' : '' }}>Part Time (Paruh Waktu)</option>
                            <option value="Freelance / Kontrak Kerja Proyek" {{ old('work_type') === 'Freelance / Kontrak Kerja Proyek' ? 'selected' : '' }}>Freelance / Kontrak Kerja Proyek</option>
                        </select>
                    </div>

                    <!-- Nama Perusahaan / Instansi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Perusahaan / Instansi / Usaha
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Contoh: PT. Astra Honda / RSUD / Kantor Desa / Usaha Mandiri"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <!-- Lokasi Perusahaan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Lokasi Tempat Bekerja (Kota / Provinsi)
                        </label>
                        <input type="text" name="company_location" value="{{ old('company_location') }}" placeholder="Contoh: Sukabumi, Jawa Barat / Jakarta"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <!-- Jabatan / Posisi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Jabatan / Posisi Pekerjaan
                        </label>
                        <input type="text" name="job_position" value="{{ old('job_position') }}" placeholder="Contoh: Staf Administrasi / Teknisi / Kasir / Operator / Owner"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <!-- Bidang Sektor -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Bidang Industri / Sektor Pekerjaan
                        </label>
                        <input type="text" name="work_field" value="{{ old('work_field') }}" placeholder="Contoh: Retail, Manufaktur, Kesehatan, TI, Jasa, Pertanian"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <!-- Kisaran Pendapatan (Take Home Pay) -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Kisaran Pendapatan Rata-rata per Bulan (Take Home Pay)
                        </label>
                        <select name="monthly_income_range" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Pilih Rentang Pendapatan --</option>
                            <option value="Di Bawah Rp 2.000.000" {{ old('monthly_income_range') === 'Di Bawah Rp 2.000.000' ? 'selected' : '' }}>Di Bawah Rp 2.000.000</option>
                            <option value="Rp 2.000.000 - Rp 3.500.000 (Setara UMK)" {{ old('monthly_income_range') === 'Rp 2.000.000 - Rp 3.500.000 (Setara UMK)' ? 'selected' : '' }}>Rp 2.000.000 - Rp 3.500.000 (Setara UMK)</option>
                            <option value="Rp 3.500.000 - Rp 5.000.000" {{ old('monthly_income_range') === 'Rp 3.500.000 - Rp 5.000.000' ? 'selected' : '' }}>Rp 3.500.000 - Rp 5.000.000</option>
                            <option value="Rp 5.000.000 - Rp 10.000.000" {{ old('monthly_income_range') === 'Rp 5.000.000 - Rp 10.000.000' ? 'selected' : '' }}>Rp 5.000.000 - Rp 10.000.000</option>
                            <option value="Di Atas Rp 10.000.000" {{ old('monthly_income_range') === 'Di Atas Rp 10.000.000' ? 'selected' : '' }}>Di Atas Rp 10.000.000</option>
                            <option value="Rahasia / Tidak Ingin Menyebutkan" {{ old('monthly_income_range') === 'Rahasia / Tidak Ingin Menyebutkan' ? 'selected' : '' }}>Rahasia / Tidak Ingin Menyebutkan</option>
                        </select>
                    </div>

                    <!-- Khusus Wirausaha Tambahan -->
                    <div id="sub-wirausaha-extra" class="sm:col-span-2 pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Estimasi Omzet Usaha Bulanan (Khusus Wirausaha)
                            </label>
                            <select name="business_revenue_range" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-amber-500">
                                <option value="">-- Pilih Range Omzet --</option>
                                <option value="< Rp 5.000.000" {{ old('business_revenue_range') === '< Rp 5.000.000' ? 'selected' : '' }}>&lt; Rp 5.000.000</option>
                                <option value="Rp 5.000.000 - Rp 15.000.000" {{ old('business_revenue_range') === 'Rp 5.000.000 - Rp 15.000.000' ? 'selected' : '' }}>Rp 5.000.000 - Rp 15.000.000</option>
                                <option value="Rp 15.000.000 - Rp 50.000.000" {{ old('business_revenue_range') === 'Rp 15.000.000 - Rp 50.000.000' ? 'selected' : '' }}>Rp 15.000.000 - Rp 50.000.000</option>
                                <option value="> Rp 50.000.000" {{ old('business_revenue_range') === '> Rp 50.000.000' ? 'selected' : '' }}>&gt; Rp 50.000.000</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Jumlah Tenaga Kerja / Karyawan yang Diberdayakan
                            </label>
                            <input type="number" min="0" name="employee_count" value="{{ old('employee_count', 0) }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= BAGIAN 4: STUDI LANJUT (KULIAH / PERGURUAN TINGGI) ================= -->
            <div id="section-kuliah" class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-sm">
                        04
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Studi Lanjut (Khusus Melanjutkan Pendidikan / Kuliah)</h3>
                        <p class="text-xs text-slate-500">Rincian nama perguruan tinggi, jenjang, program studi, jalur masuk, dan sumber pembiayaan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <!-- Jenjang Pendidikan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Jenjang Pendidikan yang Ditempuh <span class="text-rose-500">*</span>
                        </label>
                        <select name="college_level" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="S1" {{ old('college_level') === 'S1' ? 'selected' : '' }}>Sarjana (S1)</option>
                            <option value="D4" {{ old('college_level') === 'D4' ? 'selected' : '' }}>Sarjana Terapan (D4)</option>
                            <option value="D3" {{ old('college_level') === 'D3' ? 'selected' : '' }}>Diploma 3 (D3)</option>
                            <option value="Kedinasan" {{ old('college_level') === 'Kedinasan' ? 'selected' : '' }}>Ikatan Dinas (Kedinasan)</option>
                            <option value="Profesi" {{ old('college_level') === 'Profesi' ? 'selected' : '' }}>Program Profesi / Spesialis</option>
                            <option value="S2" {{ old('college_level') === 'S2' ? 'selected' : '' }}>Magister (S2)</option>
                            <option value="S3" {{ old('college_level') === 'S3' ? 'selected' : '' }}>Doktor (S3)</option>
                        </select>
                    </div>

                    <!-- Tanggal / Tahun Mulai Masuk -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Waktu / Tanggal Mulai Masuk Studi Lanjut
                        </label>
                        <input type="text" name="college_start_date" value="{{ old('college_start_date') }}" placeholder="Contoh: September 2025 / 2025-08"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Nama Kampus -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Perguruan Tinggi / Kampus <span class="text-rose-500">*</span>
                        </label>
                        <input list="universities_list" type="text" name="campus_name" value="{{ old('campus_name') }}" placeholder="Cari nama perguruan tinggi atau ketik manual..."
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        <datalist id="universities_list">
                            @foreach($universities as $uni)
                                <option value="{{ $uni['name'] }}">{{ $uni['short_name'] }} - {{ $uni['province'] }} ({{ $uni['type'] }})</option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Program Studi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Program Studi / Jurusan yang Diambil <span class="text-rose-500">*</span>
                        </label>
                        <input list="majors_list" type="text" name="study_program" value="{{ old('study_program') }}" placeholder="Cari jurusan atau ketik manual..."
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        <datalist id="majors_list">
                            @foreach($standardMajors as $group => $majors)
                                @foreach($majors as $m)
                                    <option value="{{ $m }}">{{ $group }}</option>
                                @endforeach
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Jalur Masuk -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Jalur Penerimaan Masuk
                        </label>
                        <select name="admission_track" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Pilih Jalur --</option>
                            <option value="SNBP (Prestasi / Rapor)" {{ old('admission_track') === 'SNBP (Prestasi / Rapor)' ? 'selected' : '' }}>SNBP (Prestasi / Rapor)</option>
                            <option value="SNBT (UTBK / Tes Nasional)" {{ old('admission_track') === 'SNBT (UTBK / Tes Nasional)' ? 'selected' : '' }}>SNBT (UTBK / Tes Nasional)</option>
                            <option value="Jalur Mandiri PTN" {{ old('admission_track') === 'Jalur Mandiri PTN' ? 'selected' : '' }}>Jalur Mandiri PTN</option>
                            <option value="PTS Reguler / Seleksi Mandiri" {{ old('admission_track') === 'PTS Reguler / Seleksi Mandiri' ? 'selected' : '' }}>PTS Reguler / Seleksi Mandiri</option>
                            <option value="Sekolah Kedinasan" {{ old('admission_track') === 'Sekolah Kedinasan' ? 'selected' : '' }}>Sekolah Kedinasan</option>
                        </select>
                    </div>

                    <!-- Sumber Biaya Pendidikan Studi Lanjut -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Sumber Biaya Pendidikan Studi Lanjut <span class="text-rose-500">*</span>
                        </label>
                        <select name="college_funding_source" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="Mandiri / Biaya Sendiri / Keluarga" {{ old('college_funding_source') === 'Mandiri / Biaya Sendiri / Keluarga' ? 'selected' : '' }}>Mandiri / Biaya Sendiri / Keluarga</option>
                            <option value="Beasiswa KIP-Kuliah (Pemerintah)" {{ old('college_funding_source') === 'Beasiswa KIP-Kuliah (Pemerintah)' ? 'selected' : '' }}>Beasiswa KIP-Kuliah (Pemerintah)</option>
                            <option value="Beasiswa Pemerintah Lainnya (Unggulan, Pemda)" {{ old('college_funding_source') === 'Beasiswa Pemerintah Lainnya (Unggulan, Pemda)' ? 'selected' : '' }}>Beasiswa Pemerintah Lainnya (Unggulan, Pemda)</option>
                            <option value="Beasiswa Swasta / Kampus / Yayasan" {{ old('college_funding_source') === 'Beasiswa Swasta / Kampus / Yayasan' ? 'selected' : '' }}>Beasiswa Swasta / Kampus / Yayasan</option>
                        </select>
                    </div>

                    <!-- Keselarasan Minat -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Keselarasan Jurusan Kuliah dengan Peminatan Saat di SMA
                        </label>
                        <select name="major_alignment" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="Sangat Selaras (Sesuai Peminatan/Cita-cita)" {{ old('major_alignment') === 'Sangat Selaras (Sesuai Peminatan/Cita-cita)' ? 'selected' : '' }}>Sangat Selaras (Sesuai Peminatan/Cita-cita)</option>
                            <option value="Cukup Selaras" {{ old('major_alignment') === 'Cukup Selaras' ? 'selected' : '' }}>Cukup Selaras</option>
                            <option value="Lintas Jurusan / Minat Baru" {{ old('major_alignment') === 'Lintas Jurusan / Minat Baru' ? 'selected' : '' }}>Lintas Jurusan / Minat Baru</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ================= BAGIAN 5: RELEVANSI PENDIDIKAN & EVALUASI KOMPETENSI ================= -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-sm">
                        05
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Relevansi Pendidikan &amp; Evaluasi Kompetensi</h3>
                        <p class="text-xs text-slate-500">Penilaian hubungan bidang studi, penguasaan kompetensi lulusan, dan evaluasi pembelajaran</p>
                    </div>
                </div>

                <div class="space-y-6 text-xs">
                    <!-- Hubungan Bidang Studi dengan Pekerjaan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Seberapa Erat Hubungan antara Bidang Studi/Peminatan dengan Aktivitas/Pekerjaan Saat Ini? <span class="text-rose-500">*</span>
                            </label>
                            <select name="job_major_relevance" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-purple-500">
                                <option value="Sangat Erat" {{ old('job_major_relevance', 'Sangat Erat') === 'Sangat Erat' ? 'selected' : '' }}>Sangat Erat</option>
                                <option value="Erat" {{ old('job_major_relevance') === 'Erat' ? 'selected' : '' }}>Erat</option>
                                <option value="Cukup Erat" {{ old('job_major_relevance') === 'Cukup Erat' ? 'selected' : '' }}>Cukup Erat</option>
                                <option value="Kurang Erat" {{ old('job_major_relevance') === 'Kurang Erat' ? 'selected' : '' }}>Kurang Erat</option>
                                <option value="Tidak Sama Sekali" {{ old('job_major_relevance') === 'Tidak Sama Sekali' ? 'selected' : '' }}>Tidak Sama Sekali</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Tingkat Pendidikan Apa yang Paling Sesuai untuk Pekerjaan Anda Saat Ini?
                            </label>
                            <select name="job_education_level_match"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-purple-500">
                                <option value="Setingkat lebih tinggi" {{ old('job_education_level_match') === 'Setingkat lebih tinggi' ? 'selected' : '' }}>Setingkat lebih tinggi</option>
                                <option value="Tingkat yang sama (SMA/SMK Sederajat)" {{ old('job_education_level_match', 'Tingkat yang sama (SMA/SMK Sederajat)') === 'Tingkat yang sama (SMA/SMK Sederajat)' ? 'selected' : '' }}>Tingkat yang sama (SMA/SMK Sederajat)</option>
                                <option value="Setingkat lebih rendah" {{ old('job_education_level_match') === 'Setingkat lebih rendah' ? 'selected' : '' }}>Setingkat lebih rendah</option>
                                <option value="Tidak perlu pendidikan tinggi khusus" {{ old('job_education_level_match') === 'Tidak perlu pendidikan tinggi khusus' ? 'selected' : '' }}>Tidak perlu pendidikan tinggi khusus</option>
                            </select>
                        </div>
                    </div>

                    <!-- Alasan jika tidak sesuai -->
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Jika Pekerjaan/Aktivitas Saat Ini Tidak Sesuai dengan Latar Belakang Pendidikan, Apa Alasan Utamanya?
                        </label>
                        <select name="job_mismatch_reason"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-purple-500">
                            <option value="Sesuai dengan Latar Belakang Pendidikan" {{ old('job_mismatch_reason') === 'Sesuai dengan Latar Belakang Pendidikan' ? 'selected' : '' }}>Sesuai dengan Latar Belakang Pendidikan</option>
                            <option value="Belum mendapat pekerjaan yang sesuai" {{ old('job_mismatch_reason') === 'Belum mendapat pekerjaan yang sesuai' ? 'selected' : '' }}>Belum mendapat pekerjaan yang sesuai</option>
                            <option value="Prospek karier dan pendapatan/gaji lebih baik" {{ old('job_mismatch_reason') === 'Prospek karier dan pendapatan/gaji lebih baik' ? 'selected' : '' }}>Prospek karier dan pendapatan/gaji lebih baik</option>
                            <option value="Lokasi kerja lebih dekat dan kondusif" {{ old('job_mismatch_reason') === 'Lokasi kerja lebih dekat dan kondusif' ? 'selected' : '' }}>Lokasi kerja lebih dekat dan kondusif</option>
                            <option value="Minat pribadi / passion baru" {{ old('job_mismatch_reason') === 'Minat pribadi / passion baru' ? 'selected' : '' }}>Minat pribadi / passion baru</option>
                            <option value="Alasan keluarga / Lainnya" {{ old('job_mismatch_reason') === 'Alasan keluarga / Lainnya' ? 'selected' : '' }}>Alasan keluarga / Lainnya</option>
                        </select>
                    </div>

                    <!-- Tabel A: Penilaian Kompetensi (Skala 1 - 5) -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-bold text-slate-800 dark:text-slate-200">
                                A. Penilaian Kompetensi Lulusan (Skala 1 s.d. 5)
                            </label>
                            <span class="text-[11px] text-slate-400">1: Sangat Rendah &bull; 5: Sangat Tinggi</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3">Seberapa baik sekolah membekali kompetensi berikut saat Anda lulus dan dibutuhkan saat ini:</p>
                        
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-100 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="p-3">Aspek Kompetensi</th>
                                        <th class="p-3 text-center w-40">Tingkat Penguasaan (1 - 5)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @php
                                        $competencies = \App\Models\BkTracerAlumnus::standardCompetencies();
                                    @endphp
                                    @foreach($competencies as $key => $title)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                        <td class="p-3 font-medium text-slate-800 dark:text-slate-200">
                                            {{ $title }}
                                        </td>
                                        <td class="p-3 text-center">
                                            <div class="inline-flex items-center gap-2">
                                                @for($v = 1; $v <= 5; $v++)
                                                    <label class="flex flex-col items-center gap-0.5 cursor-pointer">
                                                        <input type="radio" name="competencies_evaluation[{{ $key }}]" value="{{ $v }}" {{ old("competencies_evaluation.{$key}", 4) == $v ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                                        <span class="text-[10px] text-slate-400 font-mono">{{ $v }}</span>
                                                    </label>
                                                @endfor
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel B: Evaluasi Pembelajaran (Skala 1 - 5) -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-bold text-slate-800 dark:text-slate-200">
                                B. Evaluasi Penekanan Metode Pembelajaran (Skala 1 s.d. 5)
                            </label>
                            <span class="text-[11px] text-slate-400">1: Kurang Ditekankan &bull; 5: Sangat Ditekankan</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3">Seberapa besar penekanan metode pembelajaran berikut selama masa studi Anda di sekolah:</p>
                        
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-100 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="p-3">Metode Pembelajaran</th>
                                        <th class="p-3 text-center w-40">Penekanan (1 - 5)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @php
                                        $learningMethods = \App\Models\BkTracerAlumnus::standardLearningMethods();
                                    @endphp
                                    @foreach($learningMethods as $lKey => $lTitle)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                        <td class="p-3 font-medium text-slate-800 dark:text-slate-200">
                                            {{ $lTitle }}
                                        </td>
                                        <td class="p-3 text-center">
                                            <div class="inline-flex items-center gap-2">
                                                @for($lv = 1; $lv <= 5; $lv++)
                                                    <label class="flex flex-col items-center gap-0.5 cursor-pointer">
                                                        <input type="radio" name="learning_methods_evaluation[{{ $lKey }}]" value="{{ $lv }}" {{ old("learning_methods_evaluation.{$lKey}", 4) == $lv ? 'checked' : '' }} class="text-purple-600 focus:ring-purple-500">
                                                        <span class="text-[10px] text-slate-400 font-mono">{{ $lv }}</span>
                                                    </label>
                                                @endfor
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Evaluasi Layanan Bimbingan Konseling (BK) -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-4">
                        <div>
                            <label class="block font-bold text-slate-800 dark:text-slate-200 mb-2">
                                Seberapa Bermanfaat Layanan Bimbingan Konseling (BK) Selama Anda Bersekolah?
                            </label>
                            <div class="flex items-center gap-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="flex flex-col items-center gap-1 cursor-pointer">
                                        <input type="radio" name="bk_service_rating" value="{{ $i }}" {{ old('bk_service_rating', 5) == $i ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-400">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $i }} ⭐</span>
                                    </label>
                                @endfor
                                <span class="text-xs text-slate-400 ml-3">(1: Kurang Membantu s.d 5: Sangat Membantu)</span>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1.5">
                                Bagaimana Dampak Bimbingan Karier &amp; Konseling Terhadap Langkah Masa Depan Anda?
                            </label>
                            <textarea name="bk_career_guidance_impact" rows="2" placeholder="Contoh: Konseling karier membantu saya memantapkan pilihan jurusan kuliah dan persiapan kerja..."
                                      class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-indigo-500">{{ old('bk_career_guidance_impact') }}</textarea>
                        </div>
                    </div>

                    <!-- Saran & Masukan Peningkatan Mutu -->
                    <div>
                        <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1.5">
                            Saran &amp; Masukan: Apa Hal yang Paling Perlu Ditingkatkan oleh Sekolah?
                        </label>
                        <textarea name="improvement_suggestions" rows="3" placeholder="Tuliskan saran untuk kurikulum, fasilitas praktikum, program bahasa asing, bursa kerja/magang, atau bekal yang diperlukan siswa..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-indigo-500">{{ old('improvement_suggestions') }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1.5">
                            Pesan &amp; Harapan untuk Kemajuan Almamater {{ $school->name ?? 'SMAN 1 Lengkong' }}
                        </label>
                        <textarea name="school_feedback" rows="2" placeholder="Tuliskan pesan motivasi untuk almamater dan adik-adik kelas..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-indigo-500">{{ old('school_feedback') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Tombol Kirim Form -->
            <div class="flex items-center justify-between gap-4 pt-2">
                <a href="{{ route('website.home') }}" class="px-5 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    ← Kembali
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-extrabold text-sm shadow-lg shadow-indigo-600/30 transition flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Kirim Kuesioner Tracer Alumni</span>
                </button>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-6 text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} {{ $school->name ?? 'SMAN 1 Lengkong' }} &bull; Sistem Informasi Sekolah Terpadu (SIS-SMA1LE)</p>
    </footer>

    <script>
        function updateActivitySections() {
            const selectedStatus = document.querySelector('input[name="activity_status"]:checked')?.value;
            const secKuliah = document.getElementById('section-kuliah');
            const secKerjaWirausaha = document.getElementById('section-kerja-wirausaha');
            const subWirausahaExtra = document.getElementById('sub-wirausaha-extra');

            if (selectedStatus === 'KULIAH') {
                if (secKuliah) secKuliah.style.display = 'block';
                if (secKerjaWirausaha) secKerjaWirausaha.style.display = 'none';
            } else if (selectedStatus === 'BEKERJA') {
                if (secKuliah) secKuliah.style.display = 'none';
                if (secKerjaWirausaha) secKerjaWirausaha.style.display = 'block';
                if (subWirausahaExtra) subWirausahaExtra.style.display = 'none';
            } else if (selectedStatus === 'WIRAUSAHA') {
                if (secKuliah) secKuliah.style.display = 'none';
                if (secKerjaWirausaha) secKerjaWirausaha.style.display = 'block';
                if (subWirausahaExtra) subWirausahaExtra.style.display = 'grid';
            } else {
                // BELUM_MEMUNGKINKAN or MENCARI_KERJA
                if (secKuliah) secKuliah.style.display = 'none';
                if (secKerjaWirausaha) secKerjaWirausaha.style.display = 'none';
            }
        }

        document.querySelectorAll('input[name="activity_status"]').forEach(el => {
            el.addEventListener('change', updateActivitySections);
        });

        // Run on page load
        document.addEventListener('DOMContentLoaded', updateActivitySections);
    </script>
</body>
</html>
