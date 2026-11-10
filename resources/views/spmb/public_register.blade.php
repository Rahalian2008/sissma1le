<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran SPMB Online - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    <meta name="description" content="Formulir resmi Pendaftaran Peserta Didik Baru (SPMB) Online SMA Negeri 1 Lengkong: Upload Berkas KTP, KK, Akta, Rapor SMP Semester 1-5, dan Sertifikat TKA.">

    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Header -->
    <header class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-18 sm:h-20 flex items-center justify-between gap-4">
            <a href="{{ route('website.home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-emerald-400 p-0.5 shadow-md">
                    <div class="w-full h-full bg-white dark:bg-slate-900 rounded-[14px] flex items-center justify-center overflow-hidden font-heading font-black text-indigo-600 text-base">
                        1LE
                    </div>
                </div>
                <div>
                    <h1 class="font-heading font-black text-slate-900 dark:text-white text-base sm:text-lg tracking-tight leading-none group-hover:text-indigo-600 transition-colors">
                        {{ $school->name ?? 'SMAN 1 LENGKONG' }}
                    </h1>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        Portal SPMB Online &bull; Jenjang SMA
                    </p>
                </div>
            </a>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('website.home') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="hidden sm:inline">Website Utama</span>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 text-xs font-bold transition">
                    <span>Login Pegawai / Siswa</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-5xl mx-auto px-4 sm:px-6 py-8 w-full space-y-8">
        
        <!-- Hero Banner SPMB -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-sky-800 p-6 sm:p-10 text-white shadow-xl shadow-blue-600/10">
            <div class="absolute -right-10 -bottom-10 h-72 w-72 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-sky-100 mb-3 border border-white/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Penerimaan Murid Baru (SPMB) Tahun Pelajaran Baru</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-heading font-black tracking-tight leading-tight">
                    Penerimaan Siswa Baru {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}
                </h2>
                <p class="text-sky-100 text-xs sm:text-base mt-2 leading-relaxed">
                    Selamat datang calon peserta didik generasi unggul! Silakan isi formulir secara cermat, lengkapi entri nilai rapor SMP/MTs (Semester 1–5), serta unggah berkas dokumen resmi (KTP Orang Tua, KK, Akta Lahir, Berkas Rapor, dan Sertifikat TKA).
                </p>

                <!-- Ringkasan Info Gelombang & Jalur -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-5 border-t border-white/15 text-xs">
                    <div class="bg-white/10 backdrop-blur-xs rounded-xl p-2.5">
                        <div class="text-[10px] text-sky-200 uppercase tracking-wider font-bold">Daya Tampung Total</div>
                        <div class="text-lg font-black text-white mt-0.5">{{ $totalCapacity }} Kursi</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xs rounded-xl p-2.5">
                        <div class="text-[10px] text-sky-200 uppercase tracking-wider font-bold">Gelombang Terbuka</div>
                        <div class="text-lg font-black text-white mt-0.5">{{ $selectedWave ? $selectedWave->name : 'Reguler' }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xs rounded-xl p-2.5">
                        <div class="text-[10px] text-sky-200 uppercase tracking-wider font-bold">Batas Pendaftaran</div>
                        <div class="text-xs font-bold text-white mt-1">
                            {{ $selectedWave ? \Carbon\Carbon::parse($selectedWave->end_date)->translatedFormat('d M Y') : 'Sesuai Jadwal' }}
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xs rounded-xl p-2.5">
                        <div class="text-[10px] text-sky-200 uppercase tracking-wider font-bold">Format Berkas</div>
                        <div class="text-xs font-bold text-emerald-300 mt-1">PDF / JPG / PNG (Maks 4MB)</div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('info'))
            <div class="p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-200 text-xs sm:text-sm font-semibold flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>{{ session('info') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 text-xs sm:text-sm shadow-sm space-y-1">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>Terdapat beberapa isian atau berkas yang belum lengkap / belum sesuai:</span>
                </div>
                <ul class="list-disc list-inside text-xs pl-2 space-y-0.5 text-rose-800 dark:text-rose-300">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulir Pendaftaran Online -->
        <form action="{{ route('spmb.public.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-8">
            @csrf
            <input type="hidden" name="is_public_form" value="1">

            <!-- 1. PILIH GELOMBANG & JALUR SELEKSI -->
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center">1</span>
                    <div>
                        <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">Pilih Gelombang & Jalur Pendaftaran *</h3>
                        <p class="text-xs text-slate-500">Pilih gelombang seleksi aktif dan jalur masuk yang sesuai dengan kriteria Anda.</p>
                    </div>
                </div>

                <!-- Pilihan Gelombang Pendaftaran -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Gelombang Pendaftaran Aktif
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @forelse($waves as $w)
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition {{ ($selectedWave && $selectedWave->id === $w->id) ? 'border-indigo-600 bg-indigo-50/60 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200' : 'border-slate-200 dark:border-slate-800 hover:border-indigo-300' }}">
                                <input type="radio" name="spmb_wave_id" value="{{ $w->id }}"
                                       {{ ($selectedWave && $selectedWave->id === $w->id) ? 'checked' : '' }}
                                       onchange="window.location.href = '{{ route('spmb.public.register') }}?wave_id=' + this.value"
                                       class="sr-only">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $w->isOpen() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $w->isOpen() ? '🟢 SEDANG BUKA' : ($w->isUpcoming() ? '🟡 SEGERA BUKA' : '⚪ SELESAI') }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400">Kuota: {{ $w->quota_seats }} Siswa</span>
                                </div>
                                <h4 class="font-black text-sm text-slate-900 dark:text-white">{{ $w->name }}</h4>
                                <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 space-y-0.5">
                                    <div>🗓️ Periode: {{ \Carbon\Carbon::parse($w->start_date)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($w->end_date)->translatedFormat('d M Y') }}</div>
                                    @if($w->announcement_date)
                                        <div>📢 Pengumuman: {{ \Carbon\Carbon::parse($w->announcement_date)->translatedFormat('d M Y') }}</div>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <div class="col-span-full p-4 rounded-2xl bg-amber-50 text-amber-900 text-xs">
                                Gelombang pendaftaran otomatis aktif untuk tahun ajaran baru.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pilihan Jalur Pendaftaran -->
                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Pilih Jalur Masuk SPMB <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        @foreach($tracks as $t)
                            <label class="relative flex flex-col justify-between p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 hover:border-indigo-500 cursor-pointer transition-all has-checked:border-indigo-600 has-checked:bg-indigo-50/50 dark:has-checked:bg-indigo-950/40">
                                <input type="radio" name="spmb_track_id" value="{{ $t->id }}" 
                                       {{ (old('spmb_track_id', $tracks->first()->id ?? 0) == $t->id) ? 'checked' : '' }}
                                       onchange="onTrackChanged({{ json_encode($t) }})"
                                       class="sr-only">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Kuota {{ $t->quota_percentage }}%</span>
                                        @if($t->is_tka)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                                🎯 Asesmen TKA
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="font-black text-sm text-slate-900 dark:text-white mb-1">{{ $t->name }}</h4>
                                    <p class="text-xs text-slate-500 leading-relaxed line-clamp-3">{{ $t->description }}</p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 flex items-center justify-between">
                                    <span>{{ $t->getCalculatedSeats($totalCapacity) }} Kuota Kursi</span>
                                    <span class="text-xs font-black">Pilih Jalur →</span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- Banner Informasi Jalur Terpilih -->
                    <div id="track-info-banner" class="mt-3 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs">
                        <div class="font-bold text-amber-900 dark:text-amber-200 flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
                            <span id="track-info-title">Ketentuan Jalur Pendaftaran</span>
                        </div>
                        <p id="track-info-desc" class="text-amber-800 dark:text-amber-300 leading-relaxed"></p>
                    </div>
                </div>
            </div>

            <!-- 2. DATA IDENTITAS CALON SISWA -->
            <div>
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center">2</span>
                    <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">Identitas Calon Peserta Didik Baru</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4 text-xs">
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap (Sesuai Ijazah/Akta) *</label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Contoh: Muhammad Rizki Pratama" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin *</label>
                        <select name="gender" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
                            <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NISN (10 Digit) *</label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" placeholder="00xxxxxxxx" maxlength="20" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIK Siswa (16 Digit Sesuai KK) *</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" placeholder="3202xxxxxxxxxxxx" maxlength="20" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir *</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place') }}" placeholder="Sukabumi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir *</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Asal Sekolah (SMP / MTs) *</label>
                        <input type="text" name="school_origin" value="{{ old('school_origin') }}" placeholder="Contoh: SMP Negeri 1 Lengkong" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
                    </div>
                </div>
            </div>

            <!-- 3. DATA ORANG TUA / WALI & ALAMAT DOMISILI -->
            <div>
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center">3</span>
                    <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">Alamat Domisili & Data Orang Tua</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4 text-xs">
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap (Sesuai Kartu Keluarga) *</label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="Nama Jalan, Kampung, RT/RW, Desa/Kelurahan, Kecamatan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs" required>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Estimasi Jarak ke SMAN 1 Lengkong (KM)</label>
                        <input type="number" step="0.01" name="distance_km" value="{{ old('distance_km', 1.5) }}" placeholder="Contoh: 2.3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Orang Tua / Wali *</label>
                        <input type="text" name="parent_name" value="{{ old('parent_name') }}" placeholder="Nama Ayah / Ibu / Wali" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp / Telepon Aktif *</label>
                        <input type="text" name="parent_phone" value="{{ old('parent_phone') }}" placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold" required>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pekerjaan Orang Tua</label>
                        <input type="text" name="parent_job" value="{{ old('parent_job') }}" placeholder="Wiraswasta / PNS / Buruh / Petani" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-semibold">
                    </div>
                </div>
            </div>

            <!-- 4. UPLOAD BERKAS IDENTITAS KEPENDUDUKAN & DOKUMEN UTAMA (WAJIB) -->
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center">4</span>
                        <div>
                            <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">Upload Berkas Identitas Kependudukan *</h3>
                            <p class="text-xs text-slate-500">Unggah berkas pindaian asli atau foto jelas (Format: PDF, JPG, JPEG, PNG • Maksimal 4 MB per berkas).</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                        Wajib Diunggah
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                    <!-- KTP Orang Tua -->
                    <div class="p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 hover:border-indigo-500 transition">
                        <div class="flex items-center gap-2 mb-2 font-bold text-xs text-slate-800 dark:text-slate-100">
                            <span class="text-base">🪪</span>
                            <span>KTP Orang Tua / Wali <span class="text-rose-500">*</span></span>
                        </div>
                        <p class="text-[11px] text-slate-400 mb-3">Scan/Foto KTP Ayah, Ibu, atau Wali yang berlaku.</p>
                        <input type="file" name="ktp_parent_file" accept=".pdf,.jpg,.jpeg,.png" required
                               class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100 cursor-pointer">
                    </div>

                    <!-- Kartu Keluarga (KK) -->
                    <div class="p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 hover:border-indigo-500 transition">
                        <div class="flex items-center gap-2 mb-2 font-bold text-xs text-slate-800 dark:text-slate-100">
                            <span class="text-base">👨‍👩‍👦</span>
                            <span>Kartu Keluarga (KK) <span class="text-rose-500">*</span></span>
                        </div>
                        <p class="text-[11px] text-slate-400 mb-3">Scan/Foto Kartu Keluarga resmi dari Disdukcapil.</p>
                        <input type="file" name="kk_file" accept=".pdf,.jpg,.jpeg,.png" required
                               class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100 cursor-pointer">
                    </div>

                    <!-- Akte Kelahiran -->
                    <div class="p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 hover:border-indigo-500 transition">
                        <div class="flex items-center gap-2 mb-2 font-bold text-xs text-slate-800 dark:text-slate-100">
                            <span class="text-base">📜</span>
                            <span>Akte Kelahiran Siswa <span class="text-rose-500">*</span></span>
                        </div>
                        <p class="text-[11px] text-slate-400 mb-3">Scan/Foto Akte Kelahiran calon siswa.</p>
                        <input type="file" name="birth_cert_file" accept=".pdf,.jpg,.jpeg,.png" required
                               class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100 cursor-pointer">
                    </div>

                    <!-- Kartu PIP / KIP / PKH (Opsional) -->
                    <div class="p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 hover:border-amber-500 transition">
                        <div class="flex items-center gap-2 mb-2 font-bold text-xs text-slate-800 dark:text-slate-100">
                            <span class="text-base">💳</span>
                            <span>Kartu PIP / KIP / PKH</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mb-3">Wajib bagi pendaftar Jalur Afirmasi KETM (Opsional lainnya).</p>
                        <input type="file" name="pip_kip_file" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-800 dark:file:bg-amber-950 dark:file:text-amber-300 hover:file:bg-amber-100 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- 5. ENTRI NILAI & UPLOAD BERKAS RAPOR SMP/MTs (SEMESTER 1 s.d. 5 WAJIB, SEMESTER 6 OPSIONAL) -->
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center">5</span>
                        <div>
                            <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">Entri Nilai & Upload Berkas Rapor SMP/MTs *</h3>
                            <p class="text-xs text-slate-500">Masukkan nilai rata-rata tiap semester (Skala 0–100) dan unggah bukti scan halaman nilai rapor (Semester 1–5 Wajib, Semester 6 Opsional).</p>
                        </div>
                    </div>
                    <!-- Live Auto-Calculated Average Badge -->
                    <div class="text-right">
                        <div class="text-[10px] uppercase font-bold text-slate-400">Rata-rata Terkalkulasi</div>
                        <div class="text-base sm:text-xl font-black text-indigo-600 dark:text-indigo-400 font-heading">
                            <span id="calculated-average-display">0.00</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                    <!-- Semester 1 (Wajib) -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-xs text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 flex items-center justify-center text-[11px] font-bold">1</span>
                                Semester 1 (Kelas VII Gasal)
                            </span>
                            <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-rose-100 text-rose-800">Wajib</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Nilai Rata-rata Semester 1 *</label>
                            <input type="number" step="0.01" min="0" max="100" name="report_score_sem1" id="report_score_sem1" value="{{ old('report_score_sem1') }}"
                                   placeholder="Contoh: 85.50" required oninput="calculateLiveReportAverage()"
                                   class="report-score-input w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Unggah Berkas Rapor Sem. 1 *</label>
                            <input type="file" name="report_file_sem1" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200 hover:file:bg-slate-200 cursor-pointer">
                        </div>
                    </div>

                    <!-- Semester 2 (Wajib) -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-xs text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 flex items-center justify-center text-[11px] font-bold">2</span>
                                Semester 2 (Kelas VII Genap)
                            </span>
                            <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-rose-100 text-rose-800">Wajib</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Nilai Rata-rata Semester 2 *</label>
                            <input type="number" step="0.01" min="0" max="100" name="report_score_sem2" id="report_score_sem2" value="{{ old('report_score_sem2') }}"
                                   placeholder="Contoh: 86.00" required oninput="calculateLiveReportAverage()"
                                   class="report-score-input w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Unggah Berkas Rapor Sem. 2 *</label>
                            <input type="file" name="report_file_sem2" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200 hover:file:bg-slate-200 cursor-pointer">
                        </div>
                    </div>

                    <!-- Semester 3 (Wajib) -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-xs text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 flex items-center justify-center text-[11px] font-bold">3</span>
                                Semester 3 (Kelas VIII Gasal)
                            </span>
                            <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-rose-100 text-rose-800">Wajib</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Nilai Rata-rata Semester 3 *</label>
                            <input type="number" step="0.01" min="0" max="100" name="report_score_sem3" id="report_score_sem3" value="{{ old('report_score_sem3') }}"
                                   placeholder="Contoh: 87.20" required oninput="calculateLiveReportAverage()"
                                   class="report-score-input w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Unggah Berkas Rapor Sem. 3 *</label>
                            <input type="file" name="report_file_sem3" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200 hover:file:bg-slate-200 cursor-pointer">
                        </div>
                    </div>

                    <!-- Semester 4 (Wajib) -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-xs text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 flex items-center justify-center text-[11px] font-bold">4</span>
                                Semester 4 (Kelas VIII Genap)
                            </span>
                            <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-rose-100 text-rose-800">Wajib</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Nilai Rata-rata Semester 4 *</label>
                            <input type="number" step="0.01" min="0" max="100" name="report_score_sem4" id="report_score_sem4" value="{{ old('report_score_sem4') }}"
                                   placeholder="Contoh: 88.00" required oninput="calculateLiveReportAverage()"
                                   class="report-score-input w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Unggah Berkas Rapor Sem. 4 *</label>
                            <input type="file" name="report_file_sem4" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200 hover:file:bg-slate-200 cursor-pointer">
                        </div>
                    </div>

                    <!-- Semester 5 (Wajib) -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-xs text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 flex items-center justify-center text-[11px] font-bold">5</span>
                                Semester 5 (Kelas IX Gasal)
                            </span>
                            <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-rose-100 text-rose-800">Wajib</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Nilai Rata-rata Semester 5 *</label>
                            <input type="number" step="0.01" min="0" max="100" name="report_score_sem5" id="report_score_sem5" value="{{ old('report_score_sem5') }}"
                                   placeholder="Contoh: 89.50" required oninput="calculateLiveReportAverage()"
                                   class="report-score-input w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Unggah Berkas Rapor Sem. 5 *</label>
                            <input type="file" name="report_file_sem5" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200 hover:file:bg-slate-200 cursor-pointer">
                        </div>
                    </div>

                    <!-- Semester 6 (Opsional) -->
                    <div class="p-4 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-xs text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-md bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 flex items-center justify-center text-[11px] font-bold">6</span>
                                Semester 6 (Kelas IX Genap)
                            </span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">Opsional</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Nilai Rata-rata Semester 6 (Jika Ada)</label>
                            <input type="number" step="0.01" min="0" max="100" name="report_score_sem6" id="report_score_sem6" value="{{ old('report_score_sem6') }}"
                                   placeholder="Opsional jika nilai keluar" oninput="calculateLiveReportAverage()"
                                   class="report-score-input w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Unggah Berkas Rapor Sem. 6 (Opsional)</label>
                            <input type="file" name="report_file_sem6" accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200 hover:file:bg-slate-200 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. NILAI & SERTIFIKAT TKA (TES KEMAMPUAN AKADEMIK) -->
            <div id="section-tka-assessment" class="p-5 rounded-3xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-amber-500 text-white font-black text-xs flex items-center justify-center">6</span>
                        <div>
                            <h3 class="font-black text-sm sm:text-base text-amber-950 dark:text-amber-200">Asesmen & Sertifikat Tes Kemampuan Akademik (TKA)</h3>
                            <p class="text-xs text-amber-800/80 dark:text-amber-300">Wajib diperhatikan untuk pendaftar Jalur TKA / Prestasi Akademik.</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-200 text-amber-950 uppercase">
                        Jalur TKA
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-amber-950 dark:text-amber-200 mb-1">
                            Skor Nilai TKA (Jika Sudah Memiliki Nilai Awal)
                        </label>
                        <input type="number" step="0.01" min="0" max="100" name="tka_score_total" value="{{ old('tka_score_total') }}"
                               placeholder="Contoh: 82.50 (Boleh dikosongkan jika ikut ujian sekolah)"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 dark:border-amber-700 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white">
                        <p class="text-[11px] text-amber-800 dark:text-amber-400 mt-1">Jika belum mengikuti tes, jadwal ujian CBT di laboratorium SMAN 1 Lengkong akan tertera pada kartu pendaftaran.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-amber-950 dark:text-amber-200 mb-1">
                            Unggah Sertifikat / Surat Hasil TKA (PDF/JPG/PNG)
                        </label>
                        <input type="file" name="tka_certificate_file" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-amber-900 dark:text-amber-200 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-900 dark:file:bg-amber-900 dark:file:text-amber-200 hover:file:bg-amber-200 cursor-pointer">
                        <p class="text-[11px] text-amber-800 dark:text-amber-400 mt-1">Unggah bukti sertifikat atau hasil tes kemampuan akademik resmi.</p>
                    </div>
                </div>
            </div>

            <!-- 7. LAMPIRAN KHUSUS / JALUR TAMBAHAN LAINNYA -->
            <div id="section-extra-fields">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center">7</span>
                    <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">Informasi Tambahan Sesuai Jalur</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4 text-xs">
                    <div id="field-kip">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Kartu KIP / PKH / KKS (Afirmasi)</label>
                        <input type="text" name="kip_pip_number" value="{{ old('kip_pip_number') }}" placeholder="Nomor KIP/PKH bagi jalur afirmasi" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                    </div>

                    <div id="field-achievement">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kejuaraan / Sertifikat Prestasi</label>
                        <input type="text" name="achievement_name" value="{{ old('achievement_name') }}" placeholder="Juara 1 OSN Matematika Kab. Sukabumi" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                    </div>

                    <div id="field-transfer">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Instansi Mutasi Tugas Orang Tua</label>
                        <input type="text" name="parent_transfer_institution" value="{{ old('parent_transfer_institution') }}" placeholder="Nama Instansi/Lembaga Pemindah Tugas" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                    </div>
                </div>
            </div>

            <!-- Pernyataan & Tombol Daftar -->
            <div class="p-4 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-800/60 text-xs">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" required class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-slate-700 dark:text-slate-300 font-medium leading-relaxed">
                        Dengan ini saya menyatakan bahwa data yang diisikan serta berkas dokumen yang diunggah dalam formulir pendaftaran SPMB ini adalah <strong>benar, sah, dan dapat dipertanggungjawabkan</strong> sesuai dokumen asli. Apabila di kemudian hari ditemukan pemalsuan data, saya bersedia menerima sanksi pembatalan kelulusan seleksi.
                    </span>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('website.home') }}" class="text-xs text-slate-500 hover:text-slate-800 dark:hover:text-white transition">
                    ← Kembali ke Beranda Sekolah
                </a>

                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm shadow-xl shadow-emerald-600/30 transition-all hover:scale-[1.02] cursor-pointer">
                    🚀 Kirim Pendaftaran SPMB & Unggah Berkas
                </button>
            </div>
        </form>

    </main>

    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Panitia SPMB {{ $school->name ?? 'SMAN 1 Lengkong' }} &bull; Sistem Informasi Akademik Sekolah Terpadu
    </footer>

    <script>
        function onTrackChanged(track) {
            const banner = document.getElementById('track-info-banner');
            const title = document.getElementById('track-info-title');
            const desc = document.getElementById('track-info-desc');

            title.innerText = track.name + (track.is_tka ? ' (Wajib Mengikuti Asesmen TKA)' : '');
            let message = track.description || '';
            if (track.is_tka) {
                message += ' • Perhatian: Pendaftar jalur ini akan secara otomatis dijadwalkan mengikuti Tes Kemampuan Akademik (TKA) di Laboratorium Komputer SMAN 1 Lengkong.';
            }
            if (track.requirements) {
                message += ' • Berkas: ' + track.requirements.replace(/\n/g, ', ');
            }
            desc.innerText = message;

            const tkaCard = document.getElementById('section-tka-assessment');
            if (tkaCard) {
                if (track.is_tka) {
                    tkaCard.classList.remove('opacity-75');
                    tkaCard.classList.add('ring-2', 'ring-amber-500');
                } else {
                    tkaCard.classList.remove('ring-2', 'ring-amber-500');
                    tkaCard.classList.add('opacity-75');
                }
            }
        }

        // Kalkulasi Rata-rata Nilai Rapor secara Real-time di Browser (Vanilla JS)
        function calculateLiveReportAverage() {
            const inputs = [
                document.getElementById('report_score_sem1'),
                document.getElementById('report_score_sem2'),
                document.getElementById('report_score_sem3'),
                document.getElementById('report_score_sem4'),
                document.getElementById('report_score_sem5')
            ];
            const sem6Input = document.getElementById('report_score_sem6');

            let total = 0;
            let count = 0;

            inputs.forEach(input => {
                if (input && input.value !== '') {
                    const val = parseFloat(input.value);
                    if (!isNaN(val) && val >= 0 && val <= 100) {
                        total += val;
                        count++;
                    }
                }
            });

            if (sem6Input && sem6Input.value !== '') {
                const val6 = parseFloat(sem6Input.value);
                if (!isNaN(val6) && val6 >= 0 && val6 <= 100) {
                    total += val6;
                    count++;
                }
            }

            const display = document.getElementById('calculated-average-display');
            if (display) {
                if (count > 0) {
                    const avg = (total / count).toFixed(2);
                    display.innerText = avg;
                } else {
                    display.innerText = '0.00';
                }
            }
        }

        // Initialize with default selected track
        document.addEventListener('DOMContentLoaded', function () {
            const checkedRadio = document.querySelector('input[name="spmb_track_id"]:checked');
            if (checkedRadio) {
                checkedRadio.dispatchEvent(new Event('change'));
            }
            calculateLiveReportAverage();
        });
    </script>
</body>
</html>
