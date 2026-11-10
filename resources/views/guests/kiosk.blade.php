<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buku Tamu Digital | {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}</title>
    @if(!empty($school->logo_url))
        <link rel="icon" type="image/png" href="{{ $school->logo_url }}">
    @endif
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
    <header class="w-full bg-slate-900/80 backdrop-blur-md border-b border-slate-800/80 px-4 sm:px-8 py-3.5 flex items-center justify-between sticky top-0 z-40">
        <div class="flex items-center space-x-3">
            @if(!empty($school->logo_url))
                <img src="{{ $school->logo_url }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl bg-white/10 p-1 shadow-md">
            @else
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center text-white font-extrabold text-xl shadow-lg shadow-indigo-500/20">
                    {{ substr($school->name ?? 'S', 0, 1) }}
                </div>
            @endif
            <div>
                <div class="text-sm font-extrabold text-white tracking-wide leading-tight">{{ $school->name ?? 'SMA NEGERI 1 LENGKONG' }}</div>
                <div class="text-[11px] text-indigo-400 font-semibold tracking-wider uppercase">Buku Tamu Digital &bull; Kiosk Resepsionis</div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Kiosk Ecosystem Switcher -->
            <div class="hidden md:flex items-center gap-1.5 bg-slate-950/80 border border-slate-800 rounded-xl p-1 text-xs">
                <a href="{{ route('kiosk.index') }}" target="_blank" class="px-2.5 py-1 rounded-lg font-bold text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1">
                    <span>📺 Kios TV</span>
                </a>
                <a href="{{ route('library.guest_book.index') }}" target="_blank" class="px-2.5 py-1 rounded-lg font-bold text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1">
                    <span>📚 Kios Perpus</span>
                </a>
                <a href="{{ route('guests.kiosk') }}" class="px-2.5 py-1 rounded-lg font-bold bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 flex items-center gap-1">
                    <span>👥 Kios Tamu</span>
                </a>
            </div>

            <button onclick="openCheckoutModal()" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white rounded-xl text-xs font-bold border border-slate-700 transition cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Check-Out Tamu
            </button>
            <a href="{{ route('login') }}" class="px-3 py-1.5 bg-indigo-600/30 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-xl text-xs font-semibold border border-indigo-500/30 transition">
                Masuk Petugas
            </a>
        </div>
    </header>

    <!-- Main Content Form -->
    <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto w-full">
        
        <!-- Welcome Hero Banner -->
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Pelayanan Tamu Terpadu SIKATAS
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
            <form action="{{ route('guests.kiosk.store') }}" method="POST" enctype="multipart/form-data" id="guestbook-form" class="space-y-8 text-xs">
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
                            <label class="block font-bold text-slate-200 mb-2">Ambil Foto Selfie Tamu <span class="text-rose-400">*</span></label>
                            
                            <div class="camera-container mb-3 shadow-inner border-2 border-dashed border-slate-700 flex items-center justify-center">
                                <video id="webcam-video" autoplay playsinline class="hidden"></video>
                                <img id="selfie-preview" class="hidden" alt="Pratinjau Foto Selfie">
                                <canvas id="webcam-canvas" class="hidden"></canvas>
                                
                                <div id="camera-placeholder" class="p-6 text-slate-500 space-y-2">
                                    <svg class="w-12 h-12 mx-auto text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <div class="text-[11px] leading-tight">Tekan tombol di bawah untuk menyalakan kamera selfie.</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap justify-center">
                                <button type="button" id="btn-start-camera" onclick="startCamera()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    Buka Kamera
                                </button>
                                <button type="button" id="btn-capture-photo" onclick="captureSelfie()" class="hidden px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                    Ambil Foto
                                </button>
                                <button type="button" id="btn-retake-photo" onclick="retakeSelfie()" class="hidden px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold transition cursor-pointer">
                                    Foto Ulang
                                </button>
                            </div>

                            <!-- Opsi fallback upload file jika kamera tidak diizinkan -->
                            <div class="mt-3 pt-3 border-t border-slate-800/80 w-full text-[11px] text-slate-400">
                                <span>Atau unggah file foto:</span>
                                <input type="file" name="selfie_file" accept="image/*" class="mt-1 block w-full text-slate-400 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-slate-800 file:text-indigo-300 hover:file:bg-slate-700 cursor-pointer">
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
                            <label class="block font-semibold text-slate-300 mb-1">Ditujukan Kepada Bagian / Pejabat <span class="text-rose-400">*</span></label>
                            <select name="destination_department" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-indigo-500">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ old('destination_department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Nama Pegawai / Guru yang Dituju <span class="text-slate-500 font-normal">(Jika spesifik)</span></label>
                            <input type="text" name="destination_person_name" value="{{ old('destination_person_name') }}" placeholder="Contoh: {{ $school->headmaster_name ?? 'Kepala Sekolah' }}" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-indigo-500">
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
        const video = document.getElementById('webcam-video');
        const preview = document.getElementById('selfie-preview');
        const canvas = document.getElementById('webcam-canvas');
        const placeholder = document.getElementById('camera-placeholder');
        const btnStart = document.getElementById('btn-start-camera');
        const btnCapture = document.getElementById('btn-capture-photo');
        const btnRetake = document.getElementById('btn-retake-photo');
        const hiddenSelfieInput = document.getElementById('selfie-image-data');

        async function startCamera() {
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    },
                    audio: false
                });
                video.srcObject = videoStream;
                video.classList.remove('hidden');
                placeholder.classList.add('hidden');
                preview.classList.add('hidden');
                btnStart.classList.add('hidden');
                btnCapture.classList.remove('hidden');
                btnRetake.classList.add('hidden');
            } catch (err) {
                alert('Tidak dapat mengakses kamera: ' + err.message + '. Silakan gunakan opsi unggah file foto di bawah.');
            }
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

            hiddenSelfieInput.value = dataUrl;

            // Matikan stream video
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
            }

            btnCapture.classList.add('hidden');
            btnRetake.classList.remove('hidden');
        }

        function retakeSelfie() {
            hiddenSelfieInput.value = '';
            startCamera();
        }

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
            if (hasSignature) {
                document.getElementById('signature-image-data').value = sigCanvas.toDataURL('image/png');
            }
        }

        function openCheckoutModal() {
            document.getElementById('modal-checkout').classList.remove('hidden');
        }

        function closeCheckoutModal() {
            document.getElementById('modal-checkout').classList.add('hidden');
        }
    </script>
</body>
</html>
