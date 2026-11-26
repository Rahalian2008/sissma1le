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
    <meta name="description" content="Formulir resmi penelusuran tamatan dan alumni SMAN 1 Lengkong. Bantu almamater memetakan sebaran pendidikan tinggi, dunia kerja, dan wirausaha tamatan.">

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
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Tracer Study &amp; Penelusuran Tamatan
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
                <p class="text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Halo Alumni Kebanggaan SMAN 1 Lengkong! Mohon kesediaan Anda meluangkan waktu 2-3 menit untuk mengisi formulir penelusuran tamatan ini. Data ini sangat berharga untuk pemetaan mutu lulusan, akreditasi sekolah, serta pengembangan bimbingan karier adik-adik kelas Anda.
                </p>
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

            <!-- BAGIAN 1: IDENTITAS ALUMNI -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">
                        01
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Identitas Diri Alumni</h3>
                        <p class="text-xs text-slate-500">Informasi pribadi untuk verifikasi dan kontak almamater</p>
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
                                <input type="radio" name="gender" value="L" {{ old('gender') === 'L' ? 'checked' : '' }} required class="text-indigo-600 focus:ring-indigo-500">
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
                            Tahun Kelulusan <span class="text-rose-500">*</span>
                        </label>
                        <select name="graduation_year" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">-- Pilih Tahun Lulus --</option>
                            @foreach($graduationYears as $year)
                                <option value="{{ $year }}" {{ old('graduation_year') == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kelas Terakhir -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Kelas / Rombel Terakhir di SMA
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
                            Alamat Email (Opsional)
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
                </div>
            </div>

            <!-- BAGIAN 2: STATUS AKTIVITAS SAAT INI -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm">
                        02
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Status Aktivitas Utama Saat Ini</h3>
                        <p class="text-xs text-slate-500">Pilih salah satu kondisi utama yang sedang Anda jalani pasca kelulusan</p>
                    </div>
                </div>

                <!-- Pilihan Status Radio Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                    <label class="activity-option p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 cursor-pointer hover:border-indigo-500 transition relative flex flex-col justify-between">
                        <input type="radio" name="activity_status" value="KULIAH" {{ old('activity_status', 'KULIAH') === 'KULIAH' ? 'checked' : '' }} required class="absolute top-3 right-3 text-indigo-600 focus:ring-indigo-500">
                        <div class="text-2xl mb-2">🎓</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">Kuliah / Studi Lanjut</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">PTN, PTS, Kedinasan, Politeknik</div>
                        </div>
                    </label>

                    <label class="activity-option p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 cursor-pointer hover:border-indigo-500 transition relative flex flex-col justify-between">
                        <input type="radio" name="activity_status" value="BEKERJA" {{ old('activity_status') === 'BEKERJA' ? 'checked' : '' }} class="absolute top-3 right-3 text-indigo-600 focus:ring-indigo-500">
                        <div class="text-2xl mb-2">💼</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">Bekerja / Karyawan</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Perusahaan, BUMN, Instansi, ASN/TNI/Polri</div>
                        </div>
                    </label>

                    <label class="activity-option p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 cursor-pointer hover:border-indigo-500 transition relative flex flex-col justify-between">
                        <input type="radio" name="activity_status" value="WIRAUSAHA" {{ old('activity_status') === 'WIRAUSAHA' ? 'checked' : '' }} class="absolute top-3 right-3 text-indigo-600 focus:ring-indigo-500">
                        <div class="text-2xl mb-2">🚀</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">Wirausaha / Bisnis</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Usaha sendiri, Freelance, Mitra</div>
                        </div>
                    </label>

                    <label class="activity-option p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 cursor-pointer hover:border-indigo-500 transition relative flex flex-col justify-between">
                        <input type="radio" name="activity_status" value="KURSUS_PELATIHAN" {{ old('activity_status') === 'KURSUS_PELATIHAN' ? 'checked' : '' }} class="absolute top-3 right-3 text-indigo-600 focus:ring-indigo-500">
                        <div class="text-2xl mb-2">📚</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">Kursus / Pelatihan</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">BLK, Lembaga Kursus, Magang Jepang/LN</div>
                        </div>
                    </label>

                    <label class="activity-option p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 cursor-pointer hover:border-indigo-500 transition relative flex flex-col justify-between sm:col-span-2">
                        <input type="radio" name="activity_status" value="MENCARI_KERJA" {{ old('activity_status') === 'MENCARI_KERJA' ? 'checked' : '' }} class="absolute top-3 right-3 text-indigo-600 focus:ring-indigo-500">
                        <div class="text-2xl mb-2">🔍</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">Mencari Kerja / Persiapan Kuliah</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Gap year, proses seleksi pekerjaan, les UTBK/SNBT</div>
                        </div>
                    </label>
                </div>

                <!-- SUB FORM A: DETAIL KULIAH -->
                <div id="section-kuliah" class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                        <span>🎓 Rincian Perguruan Tinggi &amp; Program Studi</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Jenjang Pendidikan <span class="text-rose-500">*</span>
                            </label>
                            <select name="college_level" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="S1" {{ old('college_level') === 'S1' ? 'selected' : '' }}>Sarjana (S1)</option>
                                <option value="D4" {{ old('college_level') === 'D4' ? 'selected' : '' }}>Sarjana Terapan (D4)</option>
                                <option value="D3" {{ old('college_level') === 'D3' ? 'selected' : '' }}>Diploma 3 (D3)</option>
                                <option value="Kedinasan" {{ old('college_level') === 'Kedinasan' ? 'selected' : '' }}>Ikatan Dinas (Kedinasan)</option>
                                <option value="S2" {{ old('college_level') === 'S2' ? 'selected' : '' }}>Magister (S2)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Jalur Penerimaan Masuk
                            </label>
                            <select name="admission_track" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Jalur --</option>
                                <option value="SNBP (Prestasi / Rapor)" {{ old('admission_track') === 'SNBP (Prestasi / Rapor)' ? 'selected' : '' }}>SNBP (Prestasi / Rapor)</option>
                                <option value="SNBT (UTBK / Tes)" {{ old('admission_track') === 'SNBT (UTBK / Tes)' ? 'selected' : '' }}>SNBT (UTBK / Tes)</option>
                                <option value="Jalur Mandiri PTN" {{ old('admission_track') === 'Jalur Mandiri PTN' ? 'selected' : '' }}>Jalur Mandiri PTN</option>
                                <option value="PTS Reguler / Seleksi Rapor" {{ old('admission_track') === 'PTS Reguler / Seleksi Rapor' ? 'selected' : '' }}>PTS Reguler / Seleksi Rapor</option>
                                <option value="Beasiswa KIP-Kuliah" {{ old('admission_track') === 'Beasiswa KIP-Kuliah' ? 'selected' : '' }}>Beasiswa KIP-Kuliah</option>
                                <option value="Beasiswa Swasta / Pemda" {{ old('admission_track') === 'Beasiswa Swasta / Pemda' ? 'selected' : '' }}>Beasiswa Swasta / Pemda</option>
                                <option value="Sekolah Kedinasan" {{ old('admission_track') === 'Sekolah Kedinasan' ? 'selected' : '' }}>Sekolah Kedinasan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Nama Perguruan Tinggi / Kampus <span class="text-rose-500">*</span>
                            </label>
                            <input list="universities_list" type="text" name="campus_name" value="{{ old('campus_name') }}" placeholder="Cari nama kampus atau ketik manual..."
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                            <datalist id="universities_list">
                                @foreach($universities as $uni)
                                    <option value="{{ $uni['name'] }}">{{ $uni['short_name'] }} - {{ $uni['province'] }} ({{ $uni['type'] }})</option>
                                @endforeach
                            </datalist>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Program Studi / Jurusan <span class="text-rose-500">*</span>
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

                <!-- SUB FORM B: DETAIL BEKERJA -->
                <div id="section-bekerja" class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800 hidden">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                        <span>💼 Rincian Pekerjaan &amp; Instansi Tempat Bekerja</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Nama Perusahaan / Instansi / Kantor <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Contoh: PT. Sumber Alfaria Trijaya / RSUD / Kantor Desa"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Posisi / Jabatan Pekerjaan
                            </label>
                            <input type="text" name="job_position" value="{{ old('job_position') }}" placeholder="Contoh: Staf Administrasi / Teknisi / Kasir / Operator"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Bidang Industri / Sektor
                            </label>
                            <input type="text" name="work_field" value="{{ old('work_field') }}" placeholder="Contoh: Retail, Manufaktur, Kesehatan, Pendidikan, Jasa"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Waktu Tunggu Memperoleh Pekerjaan
                            </label>
                            <select name="job_waiting_time" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="Langsung Bekerja (< 1 Bulan)" {{ old('job_waiting_time') === 'Langsung Bekerja (< 1 Bulan)' ? 'selected' : '' }}>Langsung Bekerja (< 1 Bulan)</option>
                                <option value="1 - 3 Bulan Pasca Lulus" {{ old('job_waiting_time') === '1 - 3 Bulan Pasca Lulus' ? 'selected' : '' }}>1 - 3 Bulan Pasca Lulus</option>
                                <option value="3 - 6 Bulan Pasca Lulus" {{ old('job_waiting_time') === '3 - 6 Bulan Pasca Lulus' ? 'selected' : '' }}>3 - 6 Bulan Pasca Lulus</option>
                                <option value="> 6 Bulan" {{ old('job_waiting_time') === '> 6 Bulan' ? 'selected' : '' }}>> 6 Bulan</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Estimasi Rentang Penghasilan Bulanan
                            </label>
                            <select name="monthly_income_range" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="Di Bawah Rp 2.000.000" {{ old('monthly_income_range') === 'Di Bawah Rp 2.000.000' ? 'selected' : '' }}>Di Bawah Rp 2.000.000</option>
                                <option value="Rp 2.000.000 - Rp 3.500.000 (Setara UMK)" {{ old('monthly_income_range') === 'Rp 2.000.000 - Rp 3.500.000 (Setara UMK)' ? 'selected' : '' }}>Rp 2.000.000 - Rp 3.500.000 (Setara UMK)</option>
                                <option value="Rp 3.500.000 - Rp 5.000.000" {{ old('monthly_income_range') === 'Rp 3.500.000 - Rp 5.000.000' ? 'selected' : '' }}>Rp 3.500.000 - Rp 5.000.000</option>
                                <option value="Di Atas Rp 5.000.000" {{ old('monthly_income_range') === 'Di Atas Rp 5.000.000' ? 'selected' : '' }}>Di Atas Rp 5.000.000</option>
                                <option value="Rahasia / Tidak Ingin Menyebutkan" {{ old('monthly_income_range') === 'Rahasia / Tidak Ingin Menyebutkan' ? 'selected' : '' }}>Rahasia / Tidak Ingin Menyebutkan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SUB FORM C: DETAIL WIRAUSAHA -->
                <div id="section-wirausaha" class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800 hidden">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                        <span>🚀 Rincian Usaha Mandiri / Bisnis</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Nama Usaha / Brand / Toko <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="business_name" value="{{ old('business_name') }}" placeholder="Contoh: Kopi Lengkong / Berkah Digital"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Bidang Usaha
                            </label>
                            <input type="text" name="business_field" value="{{ old('business_field') }}" placeholder="Contoh: Kuliner, Agribisnis, Fashion, Jasa Desain/IT"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Estimasi Omzet Bulanan
                            </label>
                            <select name="business_revenue_range" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="< Rp 5.000.000" {{ old('business_revenue_range') === '< Rp 5.000.000' ? 'selected' : '' }}>&lt; Rp 5.000.000</option>
                                <option value="Rp 5.000.000 - Rp 15.000.000" {{ old('business_revenue_range') === 'Rp 5.000.000 - Rp 15.000.000' ? 'selected' : '' }}>Rp 5.000.000 - Rp 15.000.000</option>
                                <option value="Rp 15.000.000 - Rp 50.000.000" {{ old('business_revenue_range') === 'Rp 15.000.000 - Rp 50.000.000' ? 'selected' : '' }}>Rp 15.000.000 - Rp 50.000.000</option>
                                <option value="> Rp 50.000.000" {{ old('business_revenue_range') === '> Rp 50.000.000' ? 'selected' : '' }}>&gt; Rp 50.000.000</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Jumlah Tenaga Kerja / Karyawan
                            </label>
                            <input type="number" min="0" name="employee_count" value="{{ old('employee_count', 0) }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: EVALUASI & MASUKAN UNTUK SEKOLAH & BK -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
                        03
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900 dark:text-white">Evaluasi &amp; Masukan untuk Almamater</h3>
                        <p class="text-xs text-slate-500">Apresiasi dan saran konstruktif untuk layanan Bimbingan Konseling dan mutu sekolah</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Rating Layanan BK -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
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

                    <!-- Dampak BK Terhadap Karier -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Bagaimana Dampak Bimbingan Karier &amp; Konseling Terhadap Langkah Masa Depan Anda?
                        </label>
                        <textarea name="bk_career_guidance_impact" rows="2" placeholder="Contoh: Konseling membantu saya mantap memilih jurusan kuliah dan mempersiapkan seleksi SNBP..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">{{ old('bk_career_guidance_impact') }}</textarea>
                    </div>

                    <!-- Saran & Masukan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Saran &amp; Harapan untuk Kemajuan {{ $school->name ?? 'SMAN 1 Lengkong' }}
                        </label>
                        <textarea name="school_feedback" rows="3" placeholder="Tuliskan saran untuk program sekolah, fasilitas, atau pesan untuk adik-adik kelas..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">{{ old('school_feedback') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Tombol Kirim Form -->
            <div class="flex items-center justify-between gap-4 pt-2">
                <a href="{{ route('website.home') }}" class="px-5 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    ← Kembali
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-extrabold text-sm shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Kirim Data Tracer Alumni</span>
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
            const secBekerja = document.getElementById('section-bekerja');
            const secWirausaha = document.getElementById('section-wirausaha');

            // Hide all sub forms first
            if (secKuliah) secKuliah.classList.add('hidden');
            if (secBekerja) secBekerja.classList.add('hidden');
            if (secWirausaha) secWirausaha.classList.add('hidden');

            if (selectedStatus === 'KULIAH') {
                if (secKuliah) secKuliah.classList.remove('hidden');
            } else if (selectedStatus === 'BEKERJA') {
                if (secBekerja) secBekerja.classList.remove('hidden');
            } else if (selectedStatus === 'WIRAUSAHA') {
                if (secWirausaha) secWirausaha.classList.remove('hidden');
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
