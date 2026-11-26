<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buku Tamu Digital | {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</title>
    @php
        $guestFavicon = $schoolFaviconUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
        $guestHeaderLogo = $schoolLogoUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
    @endphp
    <link rel="icon" type="image/png" href="{{ $guestFavicon }}">
    <link rel="shortcut icon" href="{{ $guestFavicon }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    <style>
        .camera-container {
            position: relative;
            width: 100%;
            max-width: 320px;
            aspect-ratio: 4/3;
            border-radius: 1.5rem;
            overflow: hidden;
            background: #0f172a;
        }
        #webcam-video, #selfie-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        #signature-canvas {
            touch-action: none;
            cursor: crosshair;
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-100 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 flex flex-col justify-between selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="w-full bg-slate-900/90 backdrop-blur-md border-b border-slate-800/80 px-3 sm:px-6 py-2.5 sm:py-3 sticky top-0 z-40 shadow-xl">
        <div class="max-w-7xl mx-auto flex flex-wrap sm:flex-nowrap items-center justify-between gap-3">
            <div class="flex items-center space-x-3 shrink-0 min-w-0">
                @if(!empty($guestHeaderLogo))
                    <img src="{{ $guestHeaderLogo }}" alt="Logo" class="w-9 h-9 sm:w-10 sm:h-10 object-contain rounded-xl bg-white/10 p-1 shadow-md shrink-0">
                @else
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center text-white font-extrabold text-lg sm:text-xl shadow-lg shadow-indigo-500/20 shrink-0">
                        {{ substr($school->name ?? 'S', 0, 1) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <div class="text-xs sm:text-sm font-extrabold text-white tracking-wide leading-tight truncate">{{ $school->name ?? 'SMA NEGERI 1 LENGKONG' }}</div>
                    <div class="text-[10px] sm:text-[11px] text-indigo-400 font-semibold tracking-wider uppercase truncate">Buku Tamu Digital &bull; Kiosk Resepsionis</div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0 flex-wrap justify-end">
                <!-- Unified Kiosk Ecosystem Switcher (Lengkap, Rapi & Fleksibel) -->
                <div class="flex items-center gap-1 bg-slate-950/80 border border-slate-800 rounded-xl p-1 text-[11px] font-bold overflow-x-auto no-scrollbar">
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios TV Display">
                        <span>📺 Kios TV</span>
                    </a>
                    <a href="{{ route('guests.kiosk') }}" class="px-2 py-1 rounded-lg bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 flex items-center gap-1 whitespace-nowrap">
                        <span>👥 Kios Tamu</span>
                    </a>
                    <a href="{{ route('library.guest_book.index') }}" target="_blank" class="px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1 whitespace-nowrap" title="Buka Kios Perpustakaan">
                        <span>📚 Kios Perpus</span>
                    </a>
                    <a href="{{ route('attendance.kiosk-satpam') }}" target="_blank" class="hidden sm:flex px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition items-center gap-1 whitespace-nowrap" title="Buka Kios Pos Satpam">
                        <span>🛡️ Kios Satpam</span>
                    </a>
                    <a href="{{ route('school_bell.index') }}" target="_blank" class="hidden md:flex px-2 py-1 rounded-lg text-amber-400 hover:text-amber-300 hover:bg-slate-800 transition items-center gap-1 whitespace-nowrap" title="Buka Bel Sekolah">
                        <span>🔔 Bel</span>
                    </a>
                    <a href="{{ route('website.home') }}" target="_blank" class="hidden lg:flex px-2 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition items-center gap-1 whitespace-nowrap" title="Kembali ke Portal Sekolah">
                        <span>🌐 Web</span>
                    </a>
                </div>

                <button type="button" onclick="openCheckoutModal()" class="px-2.5 sm:px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-slate-700 transition cursor-pointer flex items-center gap-1.5 shrink-0 whitespace-nowrap shadow-xs">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Check-Out</span>
                </button>
                <a href="{{ route('login') }}" class="px-2.5 sm:px-3 py-1.5 bg-indigo-600/30 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-xl text-xs font-semibold border border-indigo-500/30 transition shrink-0 whitespace-nowrap">
                    Petugas
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Form -->
    <main class="flex-1 py-6 sm:py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto w-full">
        
        <!-- Welcome Hero Banner -->
        <div class="text-center mb-6 sm:mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Pelayanan Tamu Terpadu SIS-SMA1LE
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Selamat Datang di Kampus Sekolah</h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-xl mx-auto mt-1.5">
                Demi keamanan, ketertiban dan pelayanan prima, mohon berkenan melengkapi data kunjungan dan mengambil foto selfie berikut.
            </p>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs">
                <div class="font-bold mb-1">Mohon lengkapi bagian formulir berikut:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-slate-900/90 rounded-3xl p-6 sm:p-10 border border-slate-800 shadow-2xl backdrop-blur-xl">
            <form action="{{ route('guests.kiosk.store') }}" method="POST" enctype="multipart/form-data" id="guestbook-form" onsubmit="return prepareSubmit(event)" class="space-y-8 text-xs">
                @csrf
                <input type="hidden" name="selfie_image" id="selfie-image-data">
                <input type="hidden" name="signature_data" id="signature-image-data">

                <!-- SECTION 1: FOTO SELFIE DAN TANDA TANGAN -->
                <div>
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-800 mb-6">
                        <div class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs">1</div>
                        <h2 class="text-sm font-bold text-white tracking-wide">Foto Wajah (Selfie) & Tanda Tangan Tamu</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                        <!-- Camera Selfie Module -->
                        <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800/80 flex flex-col items-center text-center">
                            <div class="flex items-center justify-between w-full mb-2">
                                <label class="font-bold text-slate-200 text-xs flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Ambil Foto Wajah (Selfie) Langsung</span>
                                    <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-semibold">Kamera Otomatis</span>
                            </div>
                            
                            <div id="camera-container" class="camera-container mb-3 shadow-inner border-2 border-dashed border-slate-700 flex items-center justify-center relative transition-all duration-300">
                                <video id="webcam-video" autoplay playsinline muted class="hidden"></video>
                                <img id="selfie-preview" class="hidden" alt="Pratinjau Foto Selfie">
                                <canvas id="webcam-canvas" class="hidden"></canvas>

                                <!-- Live Badge -->
                                <div id="camera-live-badge" class="hidden absolute top-3 left-3 bg-rose-600/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1.5 shadow-md backdrop-blur-xs z-10">
                                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                    <span>KAMERA LIVE</span>
                                </div>

                                <!-- Success Badge -->
                                <div id="camera-success-badge" class="hidden absolute top-3 left-3 bg-emerald-600/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1.5 shadow-md backdrop-blur-xs z-10">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span>FOTO SELFIE SIAP</span>
                                </div>
                                
                                <div id="camera-placeholder" class="p-6 text-slate-500 space-y-2">
                                    <div class="w-10 h-10 border-3 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                                    <div class="text-xs text-indigo-300 font-semibold">Sedang membuka kamera selfie otomatis...</div>
                                    <div class="text-[10px] text-slate-500">Mohon izinkan akses kamera jika peramban meminta izin.</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap justify-center w-full">
                                <button type="button" id="btn-start-camera" onclick="startCamera()" class="hidden px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>Nyalakan Kamera</span>
                                </button>
                                <button type="button" id="btn-capture-photo" onclick="captureSelfie()" class="hidden px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl font-extrabold text-xs transition flex items-center gap-2 shadow-lg shadow-emerald-500/25 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                    <span>Ambil Foto Selfie Sekarang</span>
                                </button>
                                <button type="button" id="btn-retake-photo" onclick="retakeSelfie()" class="hidden px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl font-bold transition flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <span>Foto Ulang</span>
                                </button>
                                <button type="button" id="btn-switch-camera" onclick="switchCamera()" class="hidden px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer" title="Ganti Kamera Depan/Belakang">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    <span>Ganti Kamera</span>
                                </button>
                            </div>

                            <div class="mt-3 text-[11px] text-slate-400 flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <span>Wajib foto selfie langsung di tempat via kamera resepsionis.</span>
                            </div>
                        </div>

                        <!-- Digital Signature Module -->
                        <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800/80 flex flex-col items-center text-center">
                            <label class="block font-bold text-slate-200 mb-2">Tanda Tangan Digital Tamu</label>
                            
                            <div class="w-full max-w-[320px] aspect-[4/3] rounded-2xl overflow-hidden bg-slate-900 border-2 border-dashed border-slate-700 relative mb-3">
                                <canvas id="signature-canvas" width="320" height="240" class="w-full h-full bg-slate-900/90"></canvas>
                                <div class="absolute bottom-2 left-2 text-[10px] text-slate-500 pointer-events-none select-none">Tanda tangani di area ini (Touch / Mouse)</div>
                            </div>

                            <button type="button" onclick="clearSignature()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold transition cursor-pointer flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus / Ulangi Tanda Tangan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: IDENTITAS TAMU & ASAL INSTANSI -->
                <div>
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-800 mb-6">
                        <div class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs">2</div>
                        <h2 class="text-sm font-bold text-white tracking-wide">Identitas Pribadi & Asal Instansi</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Nama Lengkap Tamu <span class="text-rose-400">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Dr. H. Ahmad Fauzi, M.Pd." required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Jenis Kelamin <span class="text-rose-400">*</span></label>
                            <select name="gender" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-indigo-500">
                                <option value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Jenis Identitas <span class="text-rose-400">*</span></label>
                            <select name="identity_type" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-indigo-500">
                                <option value="KTP">KTP Elektronik</option>
                                <option value="SIM">SIM (A/C)</option>
                                <option value="NIP">NIP Pegawai ASN</option>
                                <option value="Paspor">Paspor</option>
                                <option value="Lainnya">Kartu Pegawai / Identitas Lain</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-semibold text-slate-300 mb-1">Nomor Identitas (NIK / SIM / NIP) <span class="text-rose-400">*</span></label>
                            <input type="text" name="identity_number" value="{{ old('identity_number') }}" placeholder="3210xxxxxxxxxxxx" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 font-mono focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Nomor WhatsApp / HP Aktif <span class="text-rose-400">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 font-mono focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Alamat Email <span class="text-slate-500 font-normal">(Opsional)</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="tamu@instansi.go.id" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Nama Instansi / Lembaga / Perusahaan <span class="text-rose-400">*</span></label>
                            <input type="text" name="institution" value="{{ old('institution') }}" placeholder="Contoh: Balai Tikomdik Disdik Jabar / PT. Telkom" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Kategori Instansi <span class="text-rose-400">*</span></label>
                            <select name="institution_type" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-indigo-500">
                                @foreach($institutionTypes as $itype)
                                    <option value="{{ $itype }}" {{ old('institution_type') === $itype ? 'selected' : '' }}>{{ $itype }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: TUJUAN DAN KEPERLUAN KUNJUNGAN -->
                <div>
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-800 mb-6">
                        <div class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs">3</div>
                        <h2 class="text-sm font-bold text-white tracking-wide">Maksud, Tujuan & Logistik Kunjungan</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="destination_department" class="block font-semibold text-slate-300 mb-1">Ditujukan Kepada Bagian / Pejabat <span class="text-rose-400">*</span></label>
                            <select name="destination_department" id="destination_department" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-indigo-500">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ old('destination_department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative" id="staff-combobox-container">
                            <div class="flex items-center justify-between mb-1">
                                <label for="destination_person_name" class="block font-semibold text-slate-300">
                                    Nama Pegawai / Guru yang Dituju <span class="text-slate-500 font-normal">(Jika spesifik)</span>
                                </label>
                                <span class="text-[11px] text-emerald-400 font-medium hidden sm:inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Master Data Guru & TU
                                </span>
                            </div>

                            <!-- Input Combobox dengan Search Icon, Clear Button & Toggle Dropdown -->
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-slate-400 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </span>

                                <input type="text"
                                       name="destination_person_name"
                                       id="destination_person_name"
                                       autocomplete="off"
                                       list="staff_datalist"
                                       value="{{ old('destination_person_name') }}"
                                       placeholder="Ketik nama atau pilih dari daftar Guru & TU..."
                                       class="w-full pl-10 pr-24 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors text-sm">

                                <div class="absolute right-2 flex items-center gap-1">
                                    <button type="button"
                                            id="btn-clear-staff"
                                            class="p-1 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition hidden"
                                            title="Hapus / Reset"
                                            aria-label="Hapus isian">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    <button type="button"
                                            id="btn-toggle-staff-dropdown"
                                            class="px-2 py-1 text-xs bg-slate-800 hover:bg-indigo-600/50 text-slate-300 hover:text-white rounded-lg border border-slate-700 transition flex items-center gap-1 font-medium"
                                            title="Buka / Tutup Daftar Guru & TU">
                                        <span>Daftar</span>
                                        <svg id="chevron-staff" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Native HTML5 Datalist for mobile/fallback -->
                            <datalist id="staff_datalist">
                                @if(isset($staffMembers) && count($staffMembers) > 0)
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff['name'] }}">{{ $staff['role'] }} ({{ $staff['badge'] }})</option>
                                    @endforeach
                                @endif
                            </datalist>

                            <!-- Interactive Searchable Dropdown Popup -->
                            <div id="staff-dropdown-panel"
                                 class="hidden absolute z-50 left-0 right-0 mt-1.5 bg-slate-900/95 backdrop-blur-md border border-slate-700 rounded-xl shadow-2xl overflow-hidden ring-1 ring-black/50 text-xs">
                                <!-- Filter Pills Bar -->
                                <div class="p-2 border-b border-slate-800 bg-slate-950/80 flex items-center justify-between gap-1 flex-wrap">
                                    <div class="flex items-center gap-1">
                                        <button type="button" data-filter="all" class="staff-filter-btn px-2 py-0.5 rounded text-[11px] font-medium bg-indigo-600 text-white transition">Semua (<span id="count-all">{{ isset($staffMembers) ? count($staffMembers) : 0 }}</span>)</button>
                                        <button type="button" data-filter="guru" class="staff-filter-btn px-2 py-0.5 rounded text-[11px] font-medium bg-slate-800 text-slate-300 hover:text-white transition">Guru</button>
                                        <button type="button" data-filter="tu" class="staff-filter-btn px-2 py-0.5 rounded text-[11px] font-medium bg-slate-800 text-slate-300 hover:text-white transition">Staf TU</button>
                                    </div>
                                    <span class="text-[10px] text-slate-400">Ketik untuk mencari</span>
                                </div>

                                <!-- Items List -->
                                <div id="staff-items-list" class="max-h-60 overflow-y-auto divide-y divide-slate-800/60 scrollbar-thin scrollbar-thumb-slate-700">
                                    <!-- Rendered dynamically via JS for fast instant filtering -->
                                </div>

                                <!-- Footer Notice (Bisa Tulis Manual) -->
                                <div class="p-2 border-t border-slate-800 bg-slate-950/90 flex items-center justify-between text-[11px] text-slate-400">
                                    <span class="flex items-center gap-1.5 text-slate-400">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Jika tidak ada di daftar, ketik nama bebas secara manual.
                                    </span>
                                    <button type="button" id="btn-close-staff-dropdown" class="text-indigo-400 hover:text-indigo-300 hover:underline">Tutup</button>
                                </div>
                            </div>

                            <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                Data dari Master Guru & TU. Jika nama tidak terdaftar, ketik nama secara manual.
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold text-slate-300 mb-1">Kategori Agenda / Keperluan <span class="text-rose-400">*</span></label>
                        <select name="purpose" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-indigo-500">
                            @foreach($purposes as $purp)
                                <option value="{{ $purp }}" {{ old('purpose') === $purp ? 'selected' : '' }}>{{ $purp }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold text-slate-300 mb-1">Rincian / Uraian Maksud Kunjungan <span class="text-rose-400">*</span></label>
                        <textarea name="purpose_detail" rows="2" placeholder="Tuliskan secara ringkas maksud dan agenda kunjungan Anda..." required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-indigo-500">{{ old('purpose_detail') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Jumlah Rombongan (Orang) <span class="text-rose-400">*</span></label>
                            <input type="number" name="number_of_guests" value="{{ old('number_of_guests', 1) }}" min="1" max="100" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-indigo-500 font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Moda Transportasi</label>
                            <select name="vehicle_type" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-indigo-500">
                                <option value="Mobil Pribadi / Dinas">Mobil Pribadi / Dinas</option>
                                <option value="Sepeda Motor">Sepeda Motor</option>
                                <option value="Kendaraan Umum / Online">Kendaraan Umum / Online</option>
                                <option value="Jalan Kaki">Jalan Kaki</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Nomor Plat Kendaraan</label>
                            <input type="text" name="vehicle_license_plate" value="{{ old('vehicle_license_plate') }}" placeholder="Contoh: E 1234 XY" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono uppercase focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-[11px] text-slate-400 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Data kunjungan Anda dijamin kerahasiaannya dan diproteksi sistem SIKATAS.</span>
                    </div>

                    <button type="submit" onclick="prepareSubmit(event)" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-indigo-600 via-indigo-500 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-indigo-500/25 transition cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Simpan & Dapatkan Kartu Tamu Digital
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Modal Check-Out Mandiri Tamu -->
    <div id="modal-checkout" class="hidden fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center z-50 p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </div>
                    <h2 class="text-sm font-bold text-white">Check-Out Kunjungan Tamu</h2>
                </div>
                <button onclick="closeCheckoutModal()" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <p class="text-slate-400 mb-4 text-[11px] leading-relaxed">
                Silakan masukkan Nomor Tiket Tamu Anda (misal: <strong class="text-indigo-400 font-mono">TM-20260925-0001</strong>) atau Nomor WhatsApp yang didaftarkan saat kedatangan.
            </p>

            <form action="{{ route('guests.kiosk.checkout') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nomor Tiket Tamu atau Nomor HP</label>
                    <input type="text" name="ticket_or_phone" required placeholder="TM-20260925-0001 / 08123456789" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono placeholder-slate-600 focus:border-indigo-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeCheckoutModal()" class="px-4 py-2 bg-slate-800 text-slate-300 hover:text-white rounded-xl font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-bold cursor-pointer">Selesai Kunjungan (Check-Out)</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-[11px] text-slate-500 border-t border-slate-800/60">
        &copy; {{ date('Y') }} SIKATAS SMA 1 LE &bull; Sistem Informasi Manajemen Sekolah Terintegrasi
    </footer>

    <!-- Camera & Canvas Scripts -->
    <script>
        let videoStream = null;
        let currentFacingMode = 'user';
        const video = document.getElementById('webcam-video');
        const preview = document.getElementById('selfie-preview');
        const canvas = document.getElementById('webcam-canvas');
        const placeholder = document.getElementById('camera-placeholder');
        const cameraContainer = document.getElementById('camera-container');
        const liveBadge = document.getElementById('camera-live-badge');
        const successBadge = document.getElementById('camera-success-badge');
        const btnStart = document.getElementById('btn-start-camera');
        const btnCapture = document.getElementById('btn-capture-photo');
        const btnRetake = document.getElementById('btn-retake-photo');
        const btnSwitch = document.getElementById('btn-switch-camera');
        const hiddenSelfieInput = document.getElementById('selfie-image-data');

        async function startCamera(facing = currentFacingMode) {
            currentFacingMode = facing;
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }

            placeholder.innerHTML = `
                <div class="w-10 h-10 border-3 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                <div class="text-xs text-indigo-300 font-semibold">Sedang membuka kamera selfie otomatis...</div>
                <div class="text-[10px] text-slate-500">Mohon izinkan akses kamera pada perangkat ini.</div>
            `;
            placeholder.classList.remove('hidden');
            video.classList.add('hidden');
            preview.classList.add('hidden');
            if (liveBadge) liveBadge.classList.add('hidden');
            if (successBadge) successBadge.classList.add('hidden');
            btnStart.classList.add('hidden');
            btnCapture.classList.add('hidden');
            btnRetake.classList.add('hidden');
            if (btnSwitch) btnSwitch.classList.add('hidden');

            try {
                let stream = null;
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: currentFacingMode,
                            width: { ideal: 640 },
                            height: { ideal: 480 }
                        },
                        audio: false
                    });
                } catch (facingErr) {
                    // Fallback jika facingMode ditolak oleh browser/driver webcam PC
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: false
                    });
                }

                videoStream = stream;
                video.srcObject = videoStream;
                await video.play().catch(() => {});

                video.classList.remove('hidden');
                placeholder.classList.add('hidden');
                if (liveBadge) liveBadge.classList.remove('hidden');
                btnCapture.classList.remove('hidden');

                if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    const videoDevices = devices.filter(d => d.kind === 'videoinput');
                    if (videoDevices.length > 1 && btnSwitch) {
                        btnSwitch.classList.remove('hidden');
                    }
                }
            } catch (err) {
                console.error('Kamera error:', err);
                placeholder.innerHTML = `
                    <svg class="w-12 h-12 mx-auto text-rose-500/80 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div class="text-xs font-bold text-rose-400">Akses Kamera Belum Diizinkan / Tidak Terdeteksi</div>
                    <div class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">Pastikan browser diizinkan menggunakan kamera, lalu tekan tombol di bawah untuk mengaktifkan kembali.</div>
                `;
                placeholder.classList.remove('hidden');
                video.classList.add('hidden');
                if (liveBadge) liveBadge.classList.add('hidden');
                btnStart.classList.remove('hidden');
                btnCapture.classList.add('hidden');
            }
        }

        function switchCamera() {
            const nextMode = currentFacingMode === 'user' ? 'environment' : 'user';
            startCamera(nextMode);
        }

        function captureSelfie() {
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
            preview.src = dataUrl;
            preview.classList.remove('hidden');
            video.classList.add('hidden');
            if (liveBadge) liveBadge.classList.add('hidden');
            if (successBadge) successBadge.classList.remove('hidden');

            hiddenSelfieInput.value = dataUrl;

            // Matikan stream video kamera setelah foto diambil
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }

            btnCapture.classList.add('hidden');
            if (btnSwitch) btnSwitch.classList.add('hidden');
            btnRetake.classList.remove('hidden');
        }

        function retakeSelfie() {
            hiddenSelfieInput.value = '';
            if (successBadge) successBadge.classList.add('hidden');
            startCamera(currentFacingMode);
        }

        // Otomatis buka kamera langsung saat link / halaman dibuka
        document.addEventListener('DOMContentLoaded', function() {
            startCamera();
        });

        // Matikan kamera jika halaman ditutup
        window.addEventListener('beforeunload', function() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
            }
        });

        // SIGNATURE CANVAS
        const sigCanvas = document.getElementById('signature-canvas');
        const sigCtx = sigCanvas.getContext('2d');
        let isDrawing = false;
        let hasSignature = false;

        sigCtx.strokeStyle = '#ffffff';
        sigCtx.lineWidth = 2.5;
        sigCtx.lineCap = 'round';
        sigCtx.lineJoin = 'round';

        function getMousePos(canvasDom, mouseEvent) {
            const rect = canvasDom.getBoundingClientRect();
            return {
                x: (mouseEvent.clientX - rect.left) * (canvasDom.width / rect.width),
                y: (mouseEvent.clientY - rect.top) * (canvasDom.height / rect.height)
            };
        }

        function getTouchPos(canvasDom, touchEvent) {
            const rect = canvasDom.getBoundingClientRect();
            return {
                x: (touchEvent.touches[0].clientX - rect.left) * (canvasDom.width / rect.width),
                y: (touchEvent.touches[0].clientY - rect.top) * (canvasDom.height / rect.height)
            };
        }

        sigCanvas.addEventListener('mousedown', function(e) {
            isDrawing = true;
            hasSignature = true;
            const pos = getMousePos(sigCanvas, e);
            sigCtx.beginPath();
            sigCtx.moveTo(pos.x, pos.y);
        });

        sigCanvas.addEventListener('mousemove', function(e) {
            if (!isDrawing) return;
            const pos = getMousePos(sigCanvas, e);
            sigCtx.lineTo(pos.x, pos.y);
            sigCtx.stroke();
        });

        sigCanvas.addEventListener('mouseup', function() { isDrawing = false; });
        sigCanvas.addEventListener('mouseleave', function() { isDrawing = false; });

        // Touch events
        sigCanvas.addEventListener('touchstart', function(e) {
            e.preventDefault();
            isDrawing = true;
            hasSignature = true;
            const pos = getTouchPos(sigCanvas, e);
            sigCtx.beginPath();
            sigCtx.moveTo(pos.x, pos.y);
        }, { passive: false });

        sigCanvas.addEventListener('touchmove', function(e) {
            e.preventDefault();
            if (!isDrawing) return;
            const pos = getTouchPos(sigCanvas, e);
            sigCtx.lineTo(pos.x, pos.y);
            sigCtx.stroke();
        }, { passive: false });

        sigCanvas.addEventListener('touchend', function(e) {
            e.preventDefault();
            isDrawing = false;
        });

        function clearSignature() {
            sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
            hasSignature = false;
            document.getElementById('signature-image-data').value = '';
        }

        function prepareSubmit(e) {
            const selfieVal = document.getElementById('selfie-image-data').value;
            if (!selfieVal) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                alert('⚠️ Foto Wajah (Selfie) Tamu Wajib Diambil Terlebih Dahulu!\n\nSilakan tekan tombol "Ambil Foto Selfie Sekarang" di depan kamera resepsionis sebelum menyimpan data kunjungan.');
                if (cameraContainer) {
                    cameraContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    cameraContainer.classList.add('ring-4', 'ring-rose-500');
                    setTimeout(() => {
                        cameraContainer.classList.remove('ring-4', 'ring-rose-500');
                    }, 3500);
                }
                return false;
            }

            if (hasSignature) {
                document.getElementById('signature-image-data').value = sigCanvas.toDataURL('image/png');
            }

            return true;
        }

        function openCheckoutModal() {
            document.getElementById('modal-checkout').classList.remove('hidden');
        }

        function closeCheckoutModal() {
            document.getElementById('modal-checkout').classList.add('hidden');
        }

        // ==========================================================
        // COMBOBOX & PENCARIAN GURU & TENAGA KEPENDIDIKAN (TU)
        // ==========================================================
        const MASTER_STAFF = @json($staffMembers ?? []);
        const staffInput = document.getElementById('destination_person_name');
        const staffDropdown = document.getElementById('staff-dropdown-panel');
        const staffItemsList = document.getElementById('staff-items-list');
        const btnToggleStaff = document.getElementById('btn-toggle-staff-dropdown');
        const btnClearStaff = document.getElementById('btn-clear-staff');
        const btnCloseStaff = document.getElementById('btn-close-staff-dropdown');
        const chevronStaff = document.getElementById('chevron-staff');
        const staffContainer = document.getElementById('staff-combobox-container');
        const destinationDeptSelect = document.getElementById('destination_department');

        let currentStaffFilter = 'all';
        let focusedStaffIndex = -1;

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function highlightMatch(text, query) {
            if (!query || !text) return escapeHtml(text);
            const safeText = escapeHtml(text);
            const safeQuery = escapeHtml(query);
            const reg = new RegExp('(' + safeQuery.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&') + ')', 'gi');
            return safeText.replace(reg, '<span class="bg-indigo-500/30 text-indigo-300 font-bold px-0.5 rounded">$1</span>');
        }

        function renderStaffDropdown() {
            if (!staffItemsList) return;
            const query = (staffInput.value || '').trim().toLowerCase();

            let filtered = MASTER_STAFF.filter(item => {
                if (currentStaffFilter === 'guru' && item.is_tu) return false;
                if (currentStaffFilter === 'tu' && !item.is_tu) return false;

                if (!query) return true;
                const matchName = item.name.toLowerCase().includes(query);
                const matchRole = item.role.toLowerCase().includes(query);
                const matchNip = item.nip && item.nip.toLowerCase().includes(query);
                const matchBadge = item.badge && item.badge.toLowerCase().includes(query);
                return matchName || matchRole || matchNip || matchBadge;
            });

            if (filtered.length === 0) {
                staffItemsList.innerHTML = `
                    <div class="p-4 text-center">
                        <div class="w-9 h-9 mx-auto mb-2 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </div>
                        <p class="text-white font-medium text-xs mb-1">Nama "${escapeHtml(staffInput.value)}" tidak ditemukan di Master Data</p>
                        <p class="text-slate-400 text-[11px] mb-2.5">Anda tetap dapat menggunakan nama ini secara manual.</p>
                        <button type="button" id="btn-use-custom-name" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold shadow transition inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Gunakan Nama Ini (Tulis Manual)
                        </button>
                    </div>
                `;

                const btnUseCustom = document.getElementById('btn-use-custom-name');
                if (btnUseCustom) {
                    btnUseCustom.addEventListener('click', () => {
                        hideStaffDropdown();
                    });
                }
                return;
            }

            let html = '';
            filtered.forEach((item, idx) => {
                let badgeClass = 'bg-emerald-950/80 text-emerald-300 border-emerald-800';
                let iconColor = 'text-emerald-400';
                if (item.badge === 'KEPALA SEKOLAH') {
                    badgeClass = 'bg-amber-950/80 text-amber-300 border-amber-800';
                    iconColor = 'text-amber-400';
                } else if (item.badge === 'WAKASEK') {
                    badgeClass = 'bg-indigo-950/80 text-indigo-300 border-indigo-800';
                    iconColor = 'text-indigo-400';
                } else if (item.badge === 'STAF TU') {
                    badgeClass = 'bg-sky-950/80 text-sky-300 border-sky-800';
                    iconColor = 'text-sky-400';
                }

                html += `
                    <div class="staff-option-item p-2.5 hover:bg-indigo-950/50 cursor-pointer transition flex items-center justify-between gap-3 ${idx === focusedStaffIndex ? 'bg-indigo-900/40 ring-1 ring-inset ring-indigo-500' : ''}"
                         data-index="${idx}"
                         data-name="${escapeHtml(item.name)}"
                         data-dept="${escapeHtml(item.dept_suggestion || '')}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 ${iconColor} font-bold text-xs">
                                ${escapeHtml(item.name.charAt(0))}
                            </div>
                            <div class="min-w-0">
                                <div class="text-white font-medium text-xs truncate">
                                    ${highlightMatch(item.name, query)}
                                </div>
                                <div class="text-slate-400 text-[11px] truncate flex items-center gap-2">
                                    <span>${highlightMatch(item.role, query)}</span>
                                    ${item.nip && item.nip !== '-' ? `<span class="text-slate-500">NIP: ${escapeHtml(item.nip)}</span>` : ''}
                                </div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold border ${badgeClass} shrink-0">
                            ${escapeHtml(item.badge)}
                        </span>
                    </div>
                `;
            });

            staffItemsList.innerHTML = html;

            // Bind click events to items
            const optionEls = staffItemsList.querySelectorAll('.staff-option-item');
            optionEls.forEach(el => {
                el.addEventListener('click', function() {
                    const selName = this.getAttribute('data-name');
                    const selDept = this.getAttribute('data-dept');
                    selectStaff(selName, selDept);
                });
            });
        }

        function selectStaff(name, dept) {
            if (!staffInput) return;
            staffInput.value = name;
            if (dept && destinationDeptSelect) {
                // Auto-match department if currently default
                const matchingOption = Array.from(destinationDeptSelect.options).find(opt => opt.value === dept);
                if (matchingOption) {
                    destinationDeptSelect.value = dept;
                }
            }
            updateClearButton();
            hideStaffDropdown();
        }

        function showStaffDropdown() {
            if (!staffDropdown) return;
            staffDropdown.classList.remove('hidden');
            if (chevronStaff) chevronStaff.classList.add('rotate-180');
            renderStaffDropdown();
        }

        function hideStaffDropdown() {
            if (!staffDropdown) return;
            staffDropdown.classList.add('hidden');
            if (chevronStaff) chevronStaff.classList.remove('rotate-180');
            focusedStaffIndex = -1;
        }

        function updateClearButton() {
            if (!btnClearStaff || !staffInput) return;
            if (staffInput.value.trim().length > 0) {
                btnClearStaff.classList.remove('hidden');
            } else {
                btnClearStaff.classList.add('hidden');
            }
        }

        if (staffInput) {
            staffInput.addEventListener('focus', function() {
                showStaffDropdown();
            });

            staffInput.addEventListener('input', function() {
                updateClearButton();
                showStaffDropdown();
            });

            staffInput.addEventListener('keydown', function(e) {
                if (staffDropdown.classList.contains('hidden')) {
                    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                        e.preventDefault();
                        showStaffDropdown();
                        return;
                    }
                }

                const options = staffItemsList.querySelectorAll('.staff-option-item');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    focusedStaffIndex = Math.min(focusedStaffIndex + 1, options.length - 1);
                    renderStaffDropdown();
                    if (options[focusedStaffIndex]) {
                        options[focusedStaffIndex].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    focusedStaffIndex = Math.max(focusedStaffIndex - 1, 0);
                    renderStaffDropdown();
                    if (options[focusedStaffIndex]) {
                        options[focusedStaffIndex].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'Enter') {
                    if (!staffDropdown.classList.contains('hidden') && focusedStaffIndex >= 0 && options[focusedStaffIndex]) {
                        e.preventDefault();
                        const selName = options[focusedStaffIndex].getAttribute('data-name');
                        const selDept = options[focusedStaffIndex].getAttribute('data-dept');
                        selectStaff(selName, selDept);
                    }
                } else if (e.key === 'Escape') {
                    hideStaffDropdown();
                }
            });

            updateClearButton();
        }

        if (btnToggleStaff) {
            btnToggleStaff.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (staffDropdown.classList.contains('hidden')) {
                    staffInput.focus();
                    showStaffDropdown();
                } else {
                    hideStaffDropdown();
                }
            });
        }

        if (btnClearStaff) {
            btnClearStaff.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                staffInput.value = '';
                updateClearButton();
                staffInput.focus();
                showStaffDropdown();
            });
        }

        if (btnCloseStaff) {
            btnCloseStaff.addEventListener('click', function(e) {
                e.preventDefault();
                hideStaffDropdown();
            });
        }

        // Filter pills handler
        document.querySelectorAll('.staff-filter-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                document.querySelectorAll('.staff-filter-btn').forEach(b => {
                    b.classList.remove('bg-indigo-600', 'text-white');
                    b.classList.add('bg-slate-800', 'text-slate-300');
                });
                this.classList.remove('bg-slate-800', 'text-slate-300');
                this.classList.add('bg-indigo-600', 'text-white');

                currentStaffFilter = this.getAttribute('data-filter');
                focusedStaffIndex = -1;
                renderStaffDropdown();
            });
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (staffContainer && !staffContainer.contains(e.target)) {
                hideStaffDropdown();
            }
        });
    </script>
</body>
</html>
