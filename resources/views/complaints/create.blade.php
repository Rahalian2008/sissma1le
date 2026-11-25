<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Layanan Pengaduan & Aspirasi Publik - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    @php
        $compFavicon = $schoolFaviconUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
        $compLogo = $schoolLogoUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
    @endphp
    <link rel="icon" type="image/png" href="{{ $compFavicon }}">
    <link rel="shortcut icon" href="{{ $compFavicon }}">
    <meta name="description" content="Kanal pengaduan masyarakat, orang tua, dan siswa SMAN 1 Lengkong. Sampaikan aspirasi, aduan fasilitas, layanan, atau pencegahan perundungan secara aman & terlindungi.">

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
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-rose-600 via-indigo-600 to-emerald-400 p-0.5 shadow-md shrink-0">
                    <div class="w-full h-full bg-white dark:bg-slate-900 rounded-[14px] flex items-center justify-center overflow-hidden p-1">
                        @if($compLogo)
                            <img src="{{ $compLogo }}" alt="Logo {{ $school->name ?? 'Sekolah' }}" class="w-full h-full object-contain">
                        @else
                            <span class="font-heading font-black text-rose-600 text-base">S1L</span>
                        @endif
                    </div>
                </div>
                <div>
                    <h1 class="font-heading font-black text-slate-900 dark:text-white text-base sm:text-lg tracking-tight leading-none group-hover:text-indigo-600 transition-colors">
                        {{ $school->name ?? 'SMAN 1 LENGKONG' }}
                    </h1>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        Kanal Pengaduan & Aspirasi Terpadu
                    </p>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ url('/pengaduan/tracking') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 border border-indigo-200 dark:border-indigo-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cek Status Tiket</span>
                </a>
                <a href="{{ route('website.home') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="hidden sm:inline">Website Sekolah</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-4xl mx-auto px-4 sm:px-6 py-8 w-full space-y-6">

        <!-- Hero Header -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 sm:p-10 text-white shadow-2xl border border-indigo-900/50">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-bold">
                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Kerahasiaan Terjamin &bull; Jalur Langsung Pimpinan
                </div>
                <h1 class="text-2xl sm:text-3xl font-heading font-black tracking-tight text-white">
                    Kanal Pengaduan, Aspirasi & Whistleblowing
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm max-w-2xl leading-relaxed">
                    Sampaikan masukan, aduan pelayanan, keluhan fasilitas, dugaan perundungan/bullying, atau aspirasi kemajuan sekolah secara aman. Isi pengaduan hanya dapat diakses langsung oleh <strong>Kepala Sekolah</strong> dan <strong>Wakil Kepala Sekolah</strong>.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-4 text-xs text-slate-300">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Tersedia Opsi Anonim
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Nomor Tiket Pelacakan Real-Time
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Dipantau Pimpinan Resmi
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mohon lengkapi data berikut sebelum mengirim:
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-2">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulir Pengaduan -->
        <form action="{{ url('/pengaduan') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/80 dark:border-slate-800 space-y-6">
            @csrf

            <!-- Section 1: Profil Pelapor & Kerahasiaan -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-6 space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-black">1</span>
                        Identitas & Kategori Pelapor
                    </h2>
                </div>

                <!-- Anonim Switcher -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-start sm:items-center justify-between gap-4">
                    <div class="space-y-0.5">
                        <label for="is_anonymous" class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 cursor-pointer">
                            <span>Kirim Sebagai Anonim (Rahasiakan Nama Saya)</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Privasi Ekstra</span>
                        </label>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Nama Anda tidak akan ditampilkan kepada pihak lain. Kami tetap memerlukan nomor WhatsApp aktif untuk verifikasi atau mengirimkan status penanganan pengaduan.
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }} onchange="toggleAnonymous(this.checked)" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kategori Pelapor -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Status / Hubungan dengan Sekolah <span class="text-rose-500">*</span>
                        </label>
                        <select name="reporter_type" required class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500">
                            <option value="orang_tua" {{ old('reporter_type') == 'orang_tua' ? 'selected' : '' }}>Orang Tua / Wali Siswa</option>
                            <option value="siswa" {{ old('reporter_type') == 'siswa' ? 'selected' : '' }}>Siswa / Siswi Aktif</option>
                            <option value="alumni" {{ old('reporter_type') == 'alumni' ? 'selected' : '' }}>Alumni SMAN 1 Lengkong</option>
                            <option value="masyarakat" {{ old('reporter_type', 'masyarakat') == 'masyarakat' ? 'selected' : '' }}>Masyarakat Umum / Tokoh</option>
                            <option value="mitra" {{ old('reporter_type') == 'mitra' ? 'selected' : '' }}>Mitra / Instansi Eksternal</option>
                            <option value="guru_staf" {{ old('reporter_type') == 'guru_staf' ? 'selected' : '' }}>Guru / Tenaga Administrasi (Internal)</option>
                        </select>
                    </div>

                    <!-- Nama Lengkap Pelapor -->
                    <div class="space-y-1.5" id="name_container">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Nama Lengkap Pelapor <span class="text-rose-500" id="name_required_mark">*</span>
                        </label>
                        <input type="text" id="reporter_name" name="reporter_name" value="{{ old('reporter_name') }}" placeholder="Contoh: Budi Santoso" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Nomor HP / WhatsApp -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel" name="reporter_phone" value="{{ old('reporter_phone') }}" required placeholder="Contoh: 081234567890" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[10px] text-slate-400">Diperlukan untuk pemberitahuan tindak lanjut via WhatsApp.</p>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Alamat Email (Opsional)
                        </label>
                        <input type="email" name="reporter_email" value="{{ old('reporter_email') }}" placeholder="Contoh: nama@gmail.com" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Section 2: Isi Pengaduan / Aspirasi -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-black">2</span>
                    Rincian Pengaduan / Aspirasi
                </h2>

                <!-- Pilihan Kategori -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Pilih Kategori Aduan / Aspirasi <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach ($categories as $key => $cat)
                            <label class="relative flex items-start gap-3 p-3 rounded-2xl border cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-slate-800/50 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/60 dark:has-[:checked]:bg-indigo-950/40 has-[:checked]:ring-1 has-[:checked]:ring-indigo-600 border-slate-200 dark:border-slate-700">
                                <input type="radio" name="category" value="{{ $key }}" {{ old('category', 'aspirasi_kemajuan') == $key ? 'checked' : '' }} class="mt-0.5 text-indigo-600 focus:ring-indigo-500 shrink-0">
                                <div class="space-y-0.5">
                                    <div class="text-xs font-bold text-slate-900 dark:text-white">
                                        {{ $cat['title'] }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                                        {{ $cat['desc'] }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Judul Pengaduan -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Judul Singkat / Pokok Permasalahan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Usulan Perbaikan Lampu Kelas X-1 / Masukan Program Literasi Sekolah" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Tanggal & Lokasi Kejadian (Opsional) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Waktu / Tanggal Kejadian (Opsional)
                        </label>
                        <input type="date" name="incident_date" value="{{ old('incident_date') }}" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Tempat / Lokasi Kejadian (Opsional)
                        </label>
                        <input type="text" name="incident_location" value="{{ old('incident_location') }}" placeholder="Contoh: Gedung Lab Komputer, Lapangan Upacara, Ruang Kelas" class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Deskripsi / Kronologi Lengkap -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Uraian Lengkap / Kronologi Permasalahan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description" rows="5" required placeholder="Tuliskan secara jelas fakta yang terjadi, siapa pihak yang terlibat (jika relevan), waktu kejadian, serta harapan penyelesaian atau saran perbaikan..." class="w-full text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl p-3.5 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500 leading-relaxed">{{ old('description') }}</textarea>
                    <p class="text-[11px] text-slate-400">Minimal 15 karakter. Semakin jelas rincian yang disampaikan, semakin cepat pimpinan sekolah dapat menindaklanjuti.</p>
                </div>

                <!-- Upload Lampiran / Foto Bukti -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Foto Bukti / Dokumen Pendukung (Opsional)
                    </label>
                    <div class="p-4 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 text-center space-y-2">
                        <svg class="w-8 h-8 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <div class="text-xs text-slate-600 dark:text-slate-300 font-medium">
                            <label for="attachment" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline cursor-pointer">Pilih berkas dari perangkat</label> atau seret ke sini
                        </div>
                        <input type="file" id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx" class="hidden" onchange="previewFileName(this)">
                        <div id="file_name_display" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hidden"></div>
                        <p class="text-[10px] text-slate-400">Mendukung format gambar (JPG, PNG, WebP) atau dokumen (PDF, Word) maksimal 10 MB.</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>Dengan mengirim, data akan diterima secara langsung oleh Kepala Sekolah & Tim Pimpinan.</span>
                </div>

                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 rounded-2xl bg-gradient-to-r from-rose-600 via-indigo-600 to-indigo-700 hover:from-rose-500 hover:to-indigo-600 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Kirim Pengaduan Sekarang</span>
                </button>
            </div>
        </form>

    </main>

    <!-- Footer Simple -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
        &copy; {{ date('Y') }} {{ $school->name ?? 'SMAN 1 Lengkong' }} &bull; Sistem Informasi Sekolah Terpadu (SIS-SMA1LE)
    </footer>

    <script>
        function toggleAnonymous(isAnon) {
            const nameInput = document.getElementById('reporter_name');
            const reqMark = document.getElementById('name_required_mark');
            if (isAnon) {
                nameInput.placeholder = "Identitas Anda dirahasiakan (Anonim)";
                nameInput.removeAttribute('required');
                reqMark.classList.add('hidden');
            } else {
                nameInput.placeholder = "Contoh: Budi Santoso";
                nameInput.setAttribute('required', 'required');
                reqMark.classList.remove('hidden');
            }
        }

        function previewFileName(input) {
            const display = document.getElementById('file_name_display');
            if (input.files && input.files[0]) {
                display.textContent = 'Berkas dipilih: ' + input.files[0].name + ' (' + (input.files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
                display.classList.remove('hidden');
            } else {
                display.classList.add('hidden');
            }
        }

        // Jalankan saat initial load jika old value is_anonymous true
        document.addEventListener('DOMContentLoaded', function() {
            const anonCheckbox = document.getElementById('is_anonymous');
            if (anonCheckbox && anonCheckbox.checked) {
                toggleAnonymous(true);
            }
        });
    </script>
</body>
</html>
