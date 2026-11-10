@extends('layouts.app')

@section('title', 'Kelola Konten Website Sekolah - ' . ($settings['school_name'] ?? 'SMAN 1 Lengkong'))

@section('content')
@php
    $canTab = fn(string $t) => in_array($t, $allowedTabs ?? [], true);
    $canMasterNav = auth()->user()?->canAccessInActiveRole('master_data');
@endphp
<div class="space-y-6 max-w-7xl mx-auto pb-16">
    <!-- Header Hero Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-5 border border-indigo-500/20 relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-xs font-black uppercase tracking-wider">
                    MODUL KONTEN PORTAL
                </span>
                <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full text-xs font-bold">
                    {{ $settings['school_name'] ?? 'SMAN 1 Lengkong' }}
                </span>
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-xs font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Portal Publik Aktif
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-black text-white tracking-tight">Kelola Isi & Konten Website Sekolah</h1>
            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                Pusat kendali mandiri untuk mengatur publikasi berita, sambutan kepala sekolah, visi misi, gambar dokumentasi, kalender agenda, dan fasilitas sekolah tanpa koding.
            </p>
        </div>

        <div class="flex items-center gap-2.5 relative z-10 shrink-0">
            <a href="{{ route('website.home') }}" target="_blank" class="px-4 py-2.5 bg-gradient-to-r from-indigo-500 via-indigo-600 to-blue-600 hover:from-indigo-400 hover:to-blue-500 text-white font-bold rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-indigo-600/30 transition hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Lihat Website Asli ↗</span>
            </a>
            @if($canMasterNav)
            <a href="{{ route('master.school_profile') }}" class="px-3.5 py-2.5 bg-white/10 hover:bg-white/20 text-slate-200 font-bold rounded-xl text-xs flex items-center gap-1.5 transition backdrop-blur-md">
                <span>Profil Identitas</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 text-xs flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="text-base">✅</span>
                <div>
                    <strong>Berhasil Diperbarui:</strong> {{ session('success') }}
                </div>
            </div>
            <a href="{{ route('website.home') }}" target="_blank" class="px-3 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[11px] hover:bg-emerald-500 transition">
                Cek Perubahan ↗
            </a>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-300 text-xs flex items-center gap-3 shadow-xs">
            <span class="text-base shrink-0">⚠️</span>
            <div>
                <strong>Terdapat Kesalahan Pengisian:</strong>
                <ul class="list-disc list-inside mt-1 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Sleek Multi-Tab Segmented Navigation (Auto-wrapping & Responsif) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-2 sm:p-2.5 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
            <!-- 1. Berita -->
            @if($canTab('news'))
            <a href="{{ route('master.website_content', ['tab' => 'news']) }}" 
               class="px-3.5 py-2 sm:py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTab === 'news' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 ring-2 ring-indigo-500/50' : 'bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/80 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <span class="text-sm">📰</span>
                <span>Berita & Kabar</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'news' ? 'bg-white/20 text-white' : 'bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 border border-indigo-500/20' }}">
                    {{ count($newsArticles) }}
                </span>
            </a>
            @endif

            <!-- 2. Slogan & Hero -->
            @if($canTab('hero'))
            <a href="{{ route('master.website_content', ['tab' => 'hero']) }}" 
               class="px-3.5 py-2 sm:py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTab === 'hero' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 ring-2 ring-indigo-500/50' : 'bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/80 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <span class="text-sm">📣</span>
                <span>Slogan & Hero</span>
            </a>
            @endif

            <!-- 2b. Running Text Header -->
            @if($canTab('running_text'))
            <a href="{{ route('master.website_content', ['tab' => 'running_text']) }}" 
               class="px-3.5 py-2 sm:py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTab === 'running_text' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 ring-2 ring-indigo-500/50' : 'bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/80 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <span class="text-sm">📢</span>
                <span>Running Text Header</span>
            </a>
            @endif

            <!-- 3. Sambutan Kepala Sekolah -->
            @if($canTab('headmaster'))
            <a href="{{ route('master.website_content', ['tab' => 'headmaster']) }}" 
               class="px-3.5 py-2 sm:py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTab === 'headmaster' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 ring-2 ring-indigo-500/50' : 'bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/80 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <span class="text-sm">💬</span>
                <span>Sambutan Kepala Sekolah</span>
            </a>
            @endif

            <!-- 4. Visi & Misi -->
            @if($canTab('vision'))
            <a href="{{ route('master.website_content', ['tab' => 'vision']) }}" 
               class="px-3.5 py-2 sm:py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTab === 'vision' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 ring-2 ring-indigo-500/50' : 'bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/80 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <span class="text-sm">🏛️</span>
                <span>Visi & Misi</span>
            </a>
            @endif

            <!-- 5. Foto & Banner -->
            @if($canTab('media'))
            <a href="{{ route('master.website_content', ['tab' => 'media']) }}" 
               class="px-3.5 py-2 sm:py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTab === 'media' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 ring-2 ring-indigo-500/50' : 'bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/80 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <span class="text-sm">🖼️</span>
                <span>Foto & Banner</span>
            </a>
            @endif

            <!-- 6. Agenda -->
            @if($canTab('agendas'))
            <a href="{{ route('master.website_content', ['tab' => 'agendas']) }}" 
               class="px-3.5 py-2 sm:py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTab === 'agendas' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 ring-2 ring-indigo-500/50' : 'bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/80 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <span class="text-sm">📅</span>
                <span>Agenda</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'agendas' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                    {{ count($agendas) }}
                </span>
            </a>
            @endif

            <!-- 7. Fasilitas Sekolah -->
            @if($canTab('facilities'))
            <a href="{{ route('master.website_content', ['tab' => 'facilities']) }}" 
               class="px-3.5 py-2 sm:py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTab === 'facilities' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 ring-2 ring-indigo-500/50' : 'bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/80 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <span class="text-sm">🏫</span>
                <span>Fasilitas Sekolah</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'facilities' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                    {{ count($facilities) }}
                </span>
            </a>
            @endif

            <!-- 8. Guru & Staf TU -->
            @if($canTab('staff'))
            <a href="{{ route('master.website_content', ['tab' => 'staff']) }}" 
               class="px-3.5 py-2 sm:py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 shrink-0 {{ $activeTab === 'staff' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 ring-2 ring-indigo-500/50' : 'bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/80 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <span class="text-sm">👨‍🏫</span>
                <span>Guru & Staf TU</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'staff' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                    {{ count($staffMembers) }}
                </span>
            </a>
            @endif
        </div>
    </div>

    <!-- ==================== TAB: BERITA & KABAR SEKOLAH ==================== -->
    @if($activeTab === 'news' && $canTab('news'))
    <div class="space-y-6">
        
        <!-- Action Bar: Add Form Toggle & Reset -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div>
                <h3 class="font-heading font-black text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📰 Publikasi Berita & Artikel Sekolah</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                        {{ count($newsArticles) }} Artikel
                    </span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kelola artikel prestasi, pengumuman, dan liputan kegiatan yang tayang di portal publik.</p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" onclick="toggleNewsCompose()" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/25 flex items-center gap-2 transition cursor-pointer">
                    <span id="compose-btn-icon">➕</span>
                    <span id="compose-btn-text">Tulis Berita Baru</span>
                </button>

                <form action="{{ route('master.website_content.reset') }}" method="POST" onsubmit="return confirm('Reset semua berita ke artikel bawaan sistem?')">
                    @csrf
                    <input type="hidden" name="section" value="news">
                    <button type="submit" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-rose-600 hover:border-rose-300 text-xs font-semibold transition" title="Reset ke artikel awal">
                        ↺ Reset
                    </button>
                </form>
            </div>
        </div>

        <!-- Form Tambah Berita Baru (Collapsible) -->
        <div id="news-compose-card" class="hidden bg-white dark:bg-slate-900 rounded-3xl border border-indigo-200 dark:border-indigo-900/60 shadow-xl p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                        ✍️
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-slate-900 dark:text-white text-base" id="form-heading">Tulis Berita Baru</h3>
                        <p class="text-xs text-slate-500">Isi formulir di bawah ini untuk menerbitkan berita ke website sekolah.</p>
                    </div>
                </div>
                <button type="button" onclick="toggleNewsCompose(false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    ✕
                </button>
            </div>

            <form id="news-form" action="{{ route('master.website_content.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div id="form-method-container"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    
                    <!-- Judul & Kategori -->
                    <div class="lg:col-span-8 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Judul Berita / Artikel <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="news_title" id="input_news_title" required maxlength="255" placeholder="Contoh: SMAN 1 Lengkong Sukses Menyelenggarakan Gelar Karya P5" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Kategori <span class="text-rose-500">*</span>
                                </label>
                                <select name="news_category" id="input_news_category" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                                    <option value="Prestasi">🏆 Prestasi</option>
                                    <option value="Akademik">📚 Akademik</option>
                                    <option value="Kegiatan">🎪 Kegiatan</option>
                                    <option value="Pengumuman">📢 Pengumuman</option>
                                    <option value="Kesiswaan">🎒 Kesiswaan</option>
                                    <option value="Inspirasi">💡 Inspirasi</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Tanggal Rilis <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="news_date" id="input_news_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Penulis / Tim Liputan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="news_author" id="input_news_author" value="{{ auth()->user()->name ?? 'Humas SMAN 1 Lengkong' }}" required maxlength="100" placeholder="Humas Sekolah" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Ringkasan Singkat (Lead / Excerpt) <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="news_excerpt" id="input_news_excerpt" rows="2" required maxlength="1000" placeholder="Ringkasan 1-2 kalimat pengantar yang menarik pembaca..." class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:border-indigo-500 leading-relaxed"></textarea>
                        </div>
                    </div>

                    <!-- Upload Foto & Featured -->
                    <div class="lg:col-span-4 p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-4">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Foto / Thumbnail Berita
                        </label>
                        <div class="aspect-video rounded-xl overflow-hidden bg-slate-950 border border-slate-200 dark:border-slate-700 flex items-center justify-center relative">
                            <img id="news_preview_img" src="{{ asset('images/website/classroom.jpg') }}" alt="Preview" class="w-full h-full object-cover">
                        </div>
                        <input type="file" name="news_photo" id="input_news_photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                        <p class="text-[10px] text-slate-500">Maks. 5 MB (JPG, PNG, WebP). Jika dikosongkan, visual default digunakan.</p>

                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_featured" id="input_is_featured" value="1" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">⭐ Jadikan Berita Utama (Featured)</span>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Isi Lengkap Berita (Rich WYSIWYG Editor) -->
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Isi Lengkap Berita / Uraian Artikel <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">
                            Mendukung penyisipan gambar, tabel, pemformatan teks, dan tata letak paragraf.
                        </span>
                    </div>

                    <!-- Editor Box Container -->
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-slate-50 dark:bg-slate-800/80 shadow-xs focus-within:border-indigo-500 transition">
                        
                        <!-- Toolbar Baris 1: Format Heading, Ukuran Font, Gaya Huruf, Warna -->
                        <div class="p-2 bg-slate-100/90 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700/80 flex flex-wrap items-center gap-1.5 text-xs">
                            
                            <!-- Format Heading -->
                            <select onchange="execNewsCmd('formatBlock', this.value); this.value='';" title="Format Paragraf & Judul" class="px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none cursor-pointer">
                                <option value="">Format Paragraf...</option>
                                <option value="p">¶ Paragraf (Normal)</option>
                                <option value="h2">H2 — Judul Bagian Besar</option>
                                <option value="h3">H3 — Sub Judul</option>
                                <option value="h4">H4 — Poin Sorotan</option>
                                <option value="blockquote">❝ Kutipan / Quote</option>
                            </select>

                            <!-- Ukuran Huruf (Font Size) -->
                            <select onchange="execNewsCmd('fontSize', this.value); this.value='';" title="Ukuran Huruf" class="px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none cursor-pointer">
                                <option value="">Ukuran Huruf...</option>
                                <option value="1">Kecil (10pt)</option>
                                <option value="2">Standar (11pt)</option>
                                <option value="3">Sedang (12pt)</option>
                                <option value="4">Besar (14pt)</option>
                                <option value="5">Sangat Besar (18pt)</option>
                                <option value="6">Heading (24pt)</option>
                            </select>

                            <div class="w-px h-5 bg-slate-300 dark:bg-slate-700 mx-0.5"></div>

                            <!-- Gaya Huruf (B, I, U, S) -->
                            <button type="button" onclick="execNewsCmd('bold')" title="Tebal (Ctrl+B)" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-black hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer">
                                <strong>B</strong>
                            </button>
                            <button type="button" onclick="execNewsCmd('italic')" title="Miring (Ctrl+I)" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 italic font-serif font-bold hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer">
                                <em>I</em>
                            </button>
                            <button type="button" onclick="execNewsCmd('underline')" title="Garis Bawah (Ctrl+U)" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 underline font-bold hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer">
                                <u>U</u>
                            </button>
                            <button type="button" onclick="execNewsCmd('strikeThrough')" title="Coret Teks" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 line-through font-bold hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer">
                                <s>S</s>
                            </button>

                            <div class="w-px h-5 bg-slate-300 dark:bg-slate-700 mx-0.5"></div>

                            <!-- Warna Teks & Highlight -->
                            <div class="flex items-center gap-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1" title="Warna Teks">
                                <span class="font-bold text-[11px] text-slate-600 dark:text-slate-300">A</span>
                                <input type="color" onchange="execNewsCmd('foreColor', this.value)" value="#1e293b" class="w-5 h-5 border-0 p-0 rounded cursor-pointer bg-transparent">
                            </div>

                            <div class="flex items-center gap-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1" title="Warna Sorotan (Highlight)">
                                <span class="text-[11px]">🖍️</span>
                                <input type="color" onchange="execNewsCmd('hiliteColor', this.value)" value="#fef08a" class="w-5 h-5 border-0 p-0 rounded cursor-pointer bg-transparent">
                            </div>

                            <div class="w-px h-5 bg-slate-300 dark:bg-slate-700 mx-0.5"></div>

                            <!-- Perataan Paragraf -->
                            <button type="button" onclick="execNewsCmd('justifyLeft')" title="Rata Kiri" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer font-bold">
                                ⇤
                            </button>
                            <button type="button" onclick="execNewsCmd('justifyCenter')" title="Rata Tengah" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer font-bold">
                                ⇥⇤
                            </button>
                            <button type="button" onclick="execNewsCmd('justifyRight')" title="Rata Kanan" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer font-bold">
                                ⇥
                            </button>
                            <button type="button" onclick="execNewsCmd('justifyFull')" title="Rata Kanan-Kiri (Justify)" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer font-bold">
                                ≡
                            </button>

                            <div class="w-px h-5 bg-slate-300 dark:bg-slate-700 mx-0.5"></div>

                            <!-- Daftar & Butir -->
                            <button type="button" onclick="execNewsCmd('insertUnorderedList')" title="Daftar Butir Titik (Bullet)" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer font-bold">
                                •≡
                            </button>
                            <button type="button" onclick="execNewsCmd('insertOrderedList')" title="Daftar Nomor (Numbered)" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer font-bold">
                                1.≡
                            </button>
                            <button type="button" onclick="execNewsCmd('insertHorizontalRule')" title="Garis Pemisah Horizontal" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer font-bold">
                                ―
                            </button>

                            <div class="w-px h-5 bg-slate-300 dark:bg-slate-700 mx-0.5"></div>

                            <!-- SISIPKAN GAMBAR & TABEL & LINK -->
                            <button type="button" onclick="openNewsImageModal()" title="Sisipkan Gambar (Unggah atau URL)" class="px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-500 transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                                <span>🖼️</span>
                                <span>Sisipkan Gambar</span>
                            </button>

                            <button type="button" onclick="openNewsTableModal()" title="Sisipkan Tabel Berformat Rapi" class="px-2.5 py-1.5 rounded-lg bg-teal-600 text-white font-bold hover:bg-teal-500 transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                                <span>📊</span>
                                <span>Sisipkan Tabel</span>
                            </button>

                            <button type="button" onclick="insertNewsLink()" title="Sisipkan Link Tautan" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center cursor-pointer font-bold">
                                🔗
                            </button>

                            <button type="button" onclick="execNewsCmd('removeFormat')" title="Hapus Format (Clean Text)" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-rose-600 hover:bg-rose-50 transition flex items-center justify-center cursor-pointer font-bold">
                                🧹
                            </button>

                            <!-- Toggle HTML Code Mode -->
                            <button type="button" onclick="toggleNewsSourceCode()" id="btn_toggle_source" title="Lihat & Edit Kode HTML Mentah" class="ml-auto px-2.5 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-800 dark:text-slate-200 font-bold transition flex items-center gap-1 cursor-pointer">
                                <span>&lt;/&gt;</span>
                                <span id="label_toggle_source">HTML</span>
                            </button>
                        </div>

                        <!-- Editor Content Editable Canvas -->
                        <div id="news_editor_visual"
                             contenteditable="true"
                             class="w-full min-h-[260px] max-h-[500px] overflow-y-auto px-5 py-4 bg-white dark:bg-slate-900 text-xs sm:text-sm text-slate-900 dark:text-white outline-none leading-relaxed font-normal focus:bg-slate-50/50 transition">
                        </div>

                        <!-- Source Code Textarea (Toggled by HTML button) -->
                        <textarea id="news_source_textarea"
                                  rows="12"
                                  class="hidden w-full font-mono text-xs px-4 py-3 bg-slate-950 text-emerald-400 outline-none leading-relaxed border-t border-slate-700"></textarea>

                        <!-- Hidden Form Sync Textarea -->
                        <textarea name="news_content" id="input_news_content" class="hidden" required></textarea>

                        <!-- Footer Editor Status -->
                        <div class="px-4 py-1.5 bg-slate-100 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700/80 flex items-center justify-between text-[11px] text-slate-500">
                            <span id="news_word_count">0 Kata • 0 Karakter</span>
                            <span>💡 Tips: Anda dapat langsung menyalin (Ctrl+V) screenshot atau gambar ke dalam kotak editor.</span>
                        </div>

                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="toggleNewsCompose(false)" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition">
                        <span>🚀 Terbitkan Berita</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- MODAL 1: SISIPKAN GAMBAR INLINE -->
        <div id="modal-insert-news-image" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-black text-slate-800 dark:text-white flex items-center gap-2">
                        <span>🖼️</span> Sisipkan Gambar ke Berita
                    </h3>
                    <button type="button" onclick="closeNewsImageModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center text-sm cursor-pointer">✕</button>
                </div>

                <!-- Tab Pemilihan Sumber Gambar -->
                <div class="flex items-center gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold">
                    <button type="button" id="tab_btn_upload" onclick="switchImageTab('upload')" class="flex-1 py-1.5 rounded-lg bg-indigo-600 text-white transition cursor-pointer">
                        Unggah Berkas Komputer
                    </button>
                    <button type="button" id="tab_btn_url" onclick="switchImageTab('url')" class="flex-1 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition cursor-pointer">
                        Tautan URL Gambar
                    </button>
                </div>

                <!-- Pane Unggah -->
                <div id="pane_image_upload" class="space-y-3">
                    <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-5 text-center bg-slate-50 dark:bg-slate-800/40 hover:border-indigo-500 transition">
                        <input type="file" id="news_inline_image_file" accept="image/*" class="hidden" onchange="previewInlineUpload(this)">
                        <label for="news_inline_image_file" class="cursor-pointer space-y-2 block">
                            <div class="w-12 h-12 mx-auto rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl">
                                📤
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Klik untuk pilih gambar dari komputer</p>
                            <p class="text-[10px] text-slate-400">Format: JPG, PNG, WebP, GIF (Maks. 10 MB)</p>
                        </label>
                    </div>
                    <div id="upload_preview_area" class="hidden aspect-video rounded-xl overflow-hidden bg-slate-900 relative">
                        <img id="upload_preview_src" class="w-full h-full object-contain" alt="Preview">
                    </div>
                    <div id="upload_progress" class="hidden text-xs text-indigo-600 dark:text-indigo-400 font-bold flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Mengunggah gambar ke server...</span>
                    </div>
                </div>

                <!-- Pane URL -->
                <div id="pane_image_url" class="hidden space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">URL Gambar Lengkap</label>
                        <input type="url" id="news_inline_image_url" placeholder="https://domain.com/gambar.jpg" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                    </div>
                </div>

                <!-- Opsi Tambahan Gambar -->
                <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan Foto / Caption (Opsional)</label>
                        <input type="text" id="news_inline_image_caption" placeholder="Contoh: Suasana presentasi karya siswa di lab komputer" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ukuran Lebar</label>
                            <select id="news_inline_image_width" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                                <option value="w-full">Lebar Penuh (100%)</option>
                                <option value="w-3/4" selected>Sedang (75%)</option>
                                <option value="w-1/2">Ramping (50%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Posisi Perataan</label>
                            <select id="news_inline_image_align" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                                <option value="center" selected>Tengah (Center)</option>
                                <option value="left">Kiri (Left)</option>
                                <option value="right">Kanan (Right)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeNewsImageModal()" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">Batal</button>
                    <button type="button" onclick="commitInsertNewsImage()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/20 cursor-pointer">Sisipkan Gambar</button>
                </div>
            </div>
        </div>

        <!-- MODAL 2: SISIPKAN TABEL RAPI -->
        <div id="modal-insert-news-table" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-black text-slate-800 dark:text-white flex items-center gap-2">
                        <span>📊</span> Buat & Sisipkan Tabel
                    </h3>
                    <button type="button" onclick="closeNewsTableModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center text-sm cursor-pointer">✕</button>
                </div>

                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jumlah Baris</label>
                            <input type="number" id="news_table_rows" min="1" max="25" value="3" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white font-bold outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jumlah Kolom</label>
                            <input type="number" id="news_table_cols" min="1" max="10" value="3" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white font-bold outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/60 space-y-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="news_table_header" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Gunakan Baris Pertama Sebagai Header</span>
                        </label>
                        <p class="text-[10px] text-slate-400">Header akan diberi latar belakang kontras dan tebal agar tabel mudah dibaca.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeNewsTableModal()" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">Batal</button>
                    <button type="button" onclick="commitInsertNewsTable()" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs shadow-md shadow-teal-600/20 cursor-pointer">Buat Tabel</button>
                </div>
            </div>
        </div>

        <!-- Grid List of News Articles -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($newsArticles as $article)
                @php
                    $catColors = [
                        'Prestasi' => 'bg-amber-500/20 text-amber-600 dark:text-amber-400 border-amber-500/30',
                        'Akademik' => 'bg-sky-500/20 text-sky-600 dark:text-sky-400 border-sky-500/30',
                        'Kegiatan' => 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-500/30',
                        'Pengumuman' => 'bg-rose-500/20 text-rose-600 dark:text-rose-400 border-rose-500/30',
                        'Kesiswaan' => 'bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border-indigo-500/30',
                    ];
                    $badgeStyle = $catColors[$article['category'] ?? 'Kegiatan'] ?? 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30';
                @endphp
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                    <div>
                        <!-- Thumbnail Image -->
                        <div class="aspect-video relative overflow-hidden bg-slate-950">
                            <img src="{{ asset($article['image'] ?? 'images/website/classroom.jpg') }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                            
                            <!-- Badges -->
                            <div class="absolute top-3 left-3 flex items-center gap-1.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border backdrop-blur-md {{ $badgeStyle }}">
                                    {{ $article['category'] ?? 'Berita' }}
                                </span>
                                @if(!empty($article['is_featured']))
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-500 text-slate-950">
                                        ⭐ Featured
                                    </span>
                                @endif
                            </div>

                            <div class="absolute bottom-2.5 left-3 text-[10px] text-slate-300 flex items-center gap-2">
                                <span>📅 {{ \Carbon\Carbon::parse($article['date'] ?? now())->translatedFormat('d M Y') }}</span>
                                <span>•</span>
                                <span>✍️ {{ $article['author'] ?? 'Humas' }}</span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5 space-y-2">
                            <h4 class="font-heading font-black text-slate-900 dark:text-white text-sm sm:text-base line-clamp-2 leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                {{ $article['title'] }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed">
                                {{ $article['excerpt'] }}
                            </p>
                            @if(!empty($article['created_by_name']))
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                    <span class="text-xs">👤</span>
                                    <span>Diterbitkan oleh: <strong class="text-slate-700 dark:text-slate-200">{{ $article['created_by_name'] }}</strong></span>
                                    @if(!empty($article['created_by_role']))
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">({{ $article['created_by_role'] }})</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Footer -->
                    <div class="p-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-2 bg-slate-50/50 dark:bg-slate-900/50">
                        <button type="button" onclick='editNews({!! json_encode($article) !!})' class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 text-xs font-bold transition flex items-center gap-1.5">
                            <span>✏️</span>
                            <span>Edit</span>
                        </button>

                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('website.home') }}#berita" target="_blank" class="px-2.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 dark:hover:text-white text-xs font-medium transition" title="Lihat di Web">
                                👁️ Buka
                            </a>

                            <form action="{{ route('master.website_content.news.destroy', ['id' => $article['id']]) }}" method="POST" onsubmit="return confirm('Hapus artikel berita ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 flex items-center justify-center text-xs transition cursor-pointer" title="Hapus Berita">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center rounded-3xl border border-dashed border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 p-8 space-y-3">
                    <span class="text-4xl block">📰</span>
                    <h4 class="font-bold text-slate-800 dark:text-slate-200 text-sm">Belum Ada Berita yang Diterbitkan</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Klik tombol "Tulis Berita Baru" untuk menambahkan rilis kegiatan sekolah ke portal.</p>
                </div>
            @endforelse
        </div>

    </div>
    @endif

    <!-- ==================== TAB 1: SLOGAN & HERO BANNER ==================== -->
    @if($activeTab === 'hero' && $canTab('hero'))
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-8">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">
                    📣
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Headline Utama & Hero Banner</h2>
                    <p class="text-xs text-slate-500">Teks besar yang pertama kali dilihat pengunjung saat membuka website</p>
                </div>
            </div>
            
            <form action="{{ route('master.website_content.reset') }}" method="POST" onsubmit="return confirm('Reset bagian Slogan & Hero ke standar bawaan sistem?')">
                @csrf
                <input type="hidden" name="section" value="hero">
                <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-rose-600 hover:border-rose-300 text-xs font-semibold transition">
                    ↺ Reset Default
                </button>
            </form>
        </div>

        <form action="{{ route('master.website_content.hero') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Headline Besar / Slogan Utama <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="website_hero_headline" 
                    value="{{ old('website_hero_headline', $settings['website_hero_headline'] ?? 'Mencetak Generasi Cerdas, Berakhlak, & Berdaya Saing Digital.') }}" 
                    required maxlength="255" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                <p class="text-[11px] text-slate-500 mt-1">Dapat memuat kata kunci keunggulan sekolah (contoh: Cerdas, Berakhlak, Berdaya Saing Digital).</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Sub-headline / Deskripsi Pengantar Portal <span class="text-rose-500">*</span>
                </label>
                <textarea name="website_hero_subheadline" rows="3" required maxlength="1000" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition leading-relaxed">{{ old('website_hero_subheadline', $settings['website_hero_subheadline'] ?? ('Selamat datang di Official Portal ' . ($settings['school_name'] ?? 'SMAN 1 Lengkong') . ' Sukabumi. Pusat ekosistem pendidikan cerdas terpadu yang memadukan sistem kehadiran multi-metode, LMS berbasis AI, dan transparansi portal orang tua tanpa batas.')) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2 border-t border-slate-100 dark:border-slate-800">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Nomor WhatsApp Humas / Layanan Sekolah <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center">
                        <span class="px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800 border border-r-0 border-slate-200 dark:border-slate-700 rounded-l-xl text-xs font-bold text-slate-500">+</span>
                        <input type="text" name="website_humas_phone" 
                            value="{{ old('website_humas_phone', $settings['website_humas_phone'] ?? '6281234567890') }}" 
                            required maxlength="30" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-r-xl text-xs sm:text-sm font-mono font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Gunakan kode negara (contoh: 6281234567890).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Template Pesan Otomatis Chat WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="website_humas_message" 
                        value="{{ old('website_humas_message', $settings['website_humas_message'] ?? 'Halo Admin SMAN 1 Lengkong, saya ingin bertanya informasi sekolah.') }}" 
                        required maxlength="255" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                    <p class="text-[11px] text-slate-500 mt-1">Teks awal saat pengunjung mengklik tombol WhatsApp Humas.</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Jam Layanan Pelajar <span class="text-slate-400 font-normal">(Ditampilkan di section kontak &amp; layanan website)</span>
                    </label>
                    <input type="text" name="website_service_hours" 
                        value="{{ old('website_service_hours', $settings['website_service_hours'] ?? 'Senin - Jumat: 06.30 - 15.00 WIB') }}" 
                        maxlength="100" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                    <p class="text-[11px] text-slate-500 mt-1">Jadwal hari dan jam operasional pelayanan siswa (bawaan: Senin - Jumat: 06.30 - 15.00 WIB).</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition cursor-pointer">
                    <span>💾 Simpan Perubahan Slogan</span>
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- ==================== TAB: RUNNING TEXT HEADER WEBSITE ==================== -->
    @if($activeTab === 'running_text' && $canTab('running_text'))
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-lg">
                    📢
                </div>
                <div>
                    <h2 class="font-heading font-black text-slate-900 dark:text-white text-base sm:text-lg">
                        Kelola Running Text Header Website
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Teks pengumuman berjalan yang tampil di bilah atas (header) website publik menggantikan deretan menu navigasi.
                    </p>
                </div>
            </div>
            
            <form action="{{ route('master.website_content.reset') }}" method="POST" onsubmit="return confirm('Reset teks berjalan (running text) ke standar bawaan sistem?')">
                @csrf
                <input type="hidden" name="section" value="running_text">
                <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-rose-600 hover:border-rose-300 text-xs font-semibold transition cursor-pointer">
                    ↺ Reset Default
                </button>
            </form>
        </div>

        @php
            $currentRunningText = $settings['website_running_text'] ?? ('Selamat Datang di Official Portal ' . ($settings['school_name'] ?? 'SMAN 1 Lengkong') . ' Sukabumi • Pusat Ekosistem Pendidikan Digital, Presensi Terpadu & Layanan Ramah Siswa • Unggul dalam Mutu, Berkarakter, dan Berdaya Saing Global.');
            $currentBadge = $settings['website_running_text_badge'] ?? 'INFO SEKOLAH';
            $currentSpeed = $settings['website_running_text_speed'] ?? 'normal';
        @endphp

        <!-- Styles for Live Header Running Marquee -->
        <style>
            @keyframes adminHeaderRunning {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
            .admin-header-running-track {
                display: flex;
                width: max-content;
                animation: adminHeaderRunning 30s linear infinite;
                will-change: transform;
            }
            .admin-header-running-track:hover {
                animation-play-state: paused !important;
            }
        </style>

        <!-- Live Header Preview -->
        <div class="p-5 rounded-3xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-3">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span>👁️</span>
                    <span>Live Preview Tampilan Running Text di Header Website:</span>
                </span>
                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-500/20 font-bold">
                    ● Bergerak Otomatis
                </span>
            </div>

            <!-- Header Simulating Bar -->
            <div class="w-full p-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-md flex items-center overflow-hidden">
                <div class="shrink-0 flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold text-[11px] shadow-sm uppercase tracking-wider mr-3">
                    <span class="inline-block w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    <span id="preview-badge-text">{{ $currentBadge }}</span>
                </div>
                <div class="relative flex-1 overflow-hidden h-6 flex items-center">
                    <div class="admin-header-running-track flex items-center whitespace-nowrap text-xs font-semibold text-slate-800 dark:text-slate-200 select-none">
                        <span id="preview-running-text-1" class="inline-block px-4">{{ $currentRunningText }}</span>
                        <span class="inline-block px-4 text-emerald-500 font-bold">•</span>
                        <span id="preview-running-text-2" class="inline-block px-4">{{ $currentRunningText }}</span>
                        <span class="inline-block px-4 text-emerald-500 font-bold">•</span>
                    </div>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 italic">
                * Teks di atas bergerak terus-menerus di bilah navigasi atas website, menggantikan nama-nama tautan menu. Pengunjung dapat mengarahkan kursor untuk menjeda dan membaca.
            </p>
        </div>

        <form action="{{ route('master.website_content.running_text') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Label Badge Pengumuman <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="website_running_text_badge" 
                           id="input-running-badge"
                           oninput="updateRunningPreviewLive()"
                           value="{{ old('website_running_text_badge', $currentBadge) }}" 
                           placeholder="Contoh: INFO SEKOLAH / PENGUMUMAN"
                           required 
                           maxlength="50" 
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                    <p class="text-[11px] text-slate-500 mt-1">Badge sorotan di samping kiri teks berjalan.</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Kecepatan Gerakan Running Text
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <input type="radio" name="website_running_text_speed" value="slow" {{ old('website_running_text_speed', $currentSpeed) === 'slow' ? 'checked' : '' }} class="text-indigo-600">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">🐢 Santai / Lambat</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <input type="radio" name="website_running_text_speed" value="normal" {{ old('website_running_text_speed', $currentSpeed) === 'normal' ? 'checked' : '' }} class="text-indigo-600">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">🚶 Standar / Normal</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <input type="radio" name="website_running_text_speed" value="fast" {{ old('website_running_text_speed', $currentSpeed) === 'fast' ? 'checked' : '' }} class="text-indigo-600">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">⚡ Cepat</span>
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Isi Kalimat Running Text (Pengumuman Berjalan) <span class="text-rose-500">*</span>
                </label>
                <textarea name="website_running_text" 
                          id="input-running-text"
                          oninput="updateRunningPreviewLive()"
                          rows="4" 
                          required 
                          maxlength="1000" 
                          placeholder="Ketik teks pengumuman atau berita penting sekolah yang ingin dijalankan di bagian atas website..."
                          class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition leading-relaxed">{{ old('website_running_text', $currentRunningText) }}</textarea>
                <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1">
                    <span>Gunakan tanda pemisah titik tengah ( • ) di antara kalimat informasi agar mudah dibaca.</span>
                    <span>Maks. 1000 karakter</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition cursor-pointer">
                    <span>💾 Simpan Running Text</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function updateRunningPreviewLive() {
            const badgeInput = document.getElementById('input-running-badge');
            const textInput = document.getElementById('input-running-text');
            const previewBadge = document.getElementById('preview-badge-text');
            const previewText1 = document.getElementById('preview-running-text-1');
            const previewText2 = document.getElementById('preview-running-text-2');

            const textVal = (textInput ? textInput.value : '').trim() || 'Selamat Datang di Website Resmi SMAN 1 Lengkong...';
            const badgeVal = (badgeInput ? badgeInput.value : '').trim() || 'INFO SEKOLAH';

            if (previewBadge) previewBadge.innerText = badgeVal;
            if (previewText1) previewText1.innerText = textVal;
            if (previewText2) previewText2.innerText = textVal;
        }
    </script>
    @endif

    <!-- ==================== TAB 2: SAMBUTAN KEPALA SEKOLAH ==================== -->
    @if($activeTab === 'headmaster' && $canTab('headmaster'))
    @php
        $activeSpeechItem = collect($speeches)->firstWhere('is_active', true) ?: ($speeches[0] ?? null);
    @endphp
    <div class="space-y-6">
        <!-- Action Bar: Add Form Toggle & Reset -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div>
                <h3 class="font-heading font-black text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <span>💬 Sambutan & Amanat Kepala Sekolah</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                        {{ count($speeches) }} Sambutan Tersimpan
                    </span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kelola sambutan resmi kepala sekolah yang tayang di halaman depan portal publik (bisa tambah, edit, dan hapus).</p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" onclick="toggleSpeechCompose()" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/25 flex items-center gap-2 transition cursor-pointer">
                    <span id="speech-compose-btn-icon">➕</span>
                    <span id="speech-compose-btn-text">Tambah Sambutan Baru</span>
                </button>

                <form action="{{ route('master.website_content.reset') }}" method="POST" onsubmit="return confirm('Reset semua sambutan kepala sekolah ke standar bawaan sistem?')">
                    @csrf
                    <input type="hidden" name="section" value="headmaster">
                    <button type="submit" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-rose-600 hover:border-rose-300 text-xs font-semibold transition" title="Reset ke susunan awal">
                        ↺ Reset
                    </button>
                </form>
            </div>
        </div>

        <!-- Form Tambah Sambutan Baru (Collapsible) -->
        <div id="speech-compose-card" class="hidden bg-white dark:bg-slate-900 rounded-3xl border border-indigo-200 dark:border-indigo-900/60 shadow-xl p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                        ✍️
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-slate-900 dark:text-white text-base">Tambah Sambutan Baru</h3>
                        <p class="text-xs text-slate-500">Tulis naskah pidato, sambutan tahun ajaran baru, atau amanat pimpinan sekolah.</p>
                    </div>
                </div>
                <button type="button" onclick="toggleSpeechCompose(false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    ✕
                </button>
            </div>

            <form action="{{ route('master.website_content.speech.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Judul Sambutan / Topik Pidato <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="speech_title" required maxlength="255" placeholder="Contoh: Menghidupkan Potensi, Meneguhkan Budi Pekerti di Era Digital" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal Sambutan
                        </label>
                        <input type="date" name="speech_date" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Kepala Sekolah & Gelar <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="speaker_name" required maxlength="150" value="{{ $settings['website_headmaster_name'] ?? 'Pian Sopian, S.Pd., M.M.Pd.' }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Jabatan / Instansi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="speaker_title" required maxlength="150" value="{{ $settings['website_headmaster_title'] ?? ('Kepala ' . ($settings['school_name'] ?? 'SMAN 1 Lengkong')) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Unggah Foto Sambutan / Kepala Sekolah
                        </label>
                        <input type="file" name="speech_photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Kutipan Inspiratif / Mutiara Kata <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="speech_quote" required maxlength="500" placeholder="Pendidikan sejati bukan sekadar mengisi wadah dengan informasi, melainkan menyalakan lentera budi pekerti..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm italic text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition">
                </div>

                <div class="space-y-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Paragraf Pembuka & Salam <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="speech_p1" rows="3" required maxlength="2000" placeholder="Puji dan syukur kita panjatkan... Selamat datang di portal resmi sekolah..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition leading-relaxed"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Paragraf Pokok / Penegasan Kurikulum & Karakter
                        </label>
                        <textarea name="speech_p2" rows="3" maxlength="2000" placeholder="Di era kurikulum nasional dan pembelajaran mendalam (Deep Learning)..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition leading-relaxed"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Paragraf Penutup / Selengkapnya
                        </label>
                        <textarea name="speech_extended" rows="3" maxlength="3000" placeholder="Melalui aplikasi mandiri ini, mari kita bersama melangkah..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition leading-relaxed"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-indigo-600 rounded">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Langsung jadikan sambutan aktif di halaman utama website</span>
                    </label>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition cursor-pointer">
                        <span>💾 Simpan & Terbitkan Sambutan</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Daftar Kartu Sambutan (Cards) -->
        <div class="space-y-4">
            <h4 class="font-heading font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
                <span>📋 Daftar Riwayat & Arsip Sambutan Kepala Sekolah</span>
                <span class="text-xs font-normal text-slate-500">(Pilih sambutan yang ingin ditayangkan di beranda)</span>
            </h4>

            <div class="grid grid-cols-1 gap-4">
                @forelse($speeches as $sp)
                    @php
                        $isActive = !empty($sp['is_active']);
                        $spPhoto = !empty($sp['photo']) ? asset($sp['photo']) : asset('images/website/headmaster.jpg');
                    @endphp
                    <div class="p-6 rounded-3xl border {{ $isActive ? 'border-emerald-500/60 bg-emerald-500/5 dark:bg-emerald-950/20 ring-2 ring-emerald-500/30' : 'border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900' }} shadow-xs hover:shadow-md transition space-y-4">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-20 h-24 rounded-2xl overflow-hidden bg-slate-900 shrink-0 border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <img src="{{ $spPhoto }}" alt="{{ $sp['speaker_name'] ?? 'Kepala Sekolah' }}" class="w-full h-full object-cover object-top">
                                </div>
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($isActive)
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-black bg-emerald-600 text-white shadow-xs">
                                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                                <span>AKTIF TAYANG DI HOMEPAGE</span>
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                Arsip Sambutan
                                            </span>
                                        @endif
                                        <span class="text-xs text-slate-400">
                                            📅 {{ !empty($sp['speech_date']) ? \Carbon\Carbon::parse($sp['speech_date'])->translatedFormat('d F Y') : '-' }}
                                        </span>
                                    </div>
                                    <h4 class="font-heading font-black text-base text-slate-900 dark:text-white leading-snug">
                                        {{ $sp['title'] }}
                                    </h4>
                                    <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                                        {{ $sp['speaker_name'] }} • <span class="text-slate-500">{{ $sp['speaker_title'] }}</span>
                                    </p>
                                    <p class="text-xs italic text-slate-600 dark:text-slate-400 line-clamp-2 mt-1">
                                        "{{ $sp['quote'] }}"
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0 self-end md:self-start">
                                @if(!$isActive)
                                    <form action="{{ route('master.website_content.speech.activate', ['id' => $sp['id']]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer" title="Tampilkan sambutan ini di beranda">
                                            <span>⭐</span>
                                            <span>Jadikan Aktif</span>
                                        </button>
                                    </form>
                                @endif

                                <button type="button" onclick='openEditSpeechModal(@json($sp))' class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                                    <span>✏️</span>
                                    <span>Edit</span>
                                </button>

                                <form action="{{ route('master.website_content.speech.destroy', ['id' => $sp['id']]) }}" method="POST" onsubmit="return confirm('Hapus sambutan \'{{ addslashes($sp['title']) }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-9 h-9 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs transition cursor-pointer" title="Hapus Sambutan">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Ringkasan Paragraf Sambutan -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400 space-y-1">
                            <p class="line-clamp-2"><strong class="text-slate-800 dark:text-slate-200">Paragraf 1:</strong> {{ $sp['speech_p1'] }}</p>
                            @if(!empty($sp['speech_p2']))
                                <p class="line-clamp-1"><strong class="text-slate-800 dark:text-slate-200">Paragraf 2:</strong> {{ $sp['speech_p2'] }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 text-xs rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                        Belum ada data sambutan. Silakan klik tombol "Tambah Sambutan Baru" di atas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Edit Sambutan -->
    <div id="edit-speech-modal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-3xl w-full border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 space-y-5 my-8">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                        ✏️
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-slate-900 dark:text-white text-base">Edit Sambutan Kepala Sekolah</h3>
                        <p class="text-xs text-slate-500">Perbarui naskah sambutan, figur pimpinan, serta foto dokumentasi.</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditSpeechModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    ✕
                </button>
            </div>

            <form id="edit-speech-form" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Sambutan <span class="text-rose-500">*</span></label>
                        <input type="text" name="speech_title" id="edit-speech-title" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Sambutan</label>
                        <input type="date" name="speech_date" id="edit-speech-date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kepala Sekolah <span class="text-rose-500">*</span></label>
                        <input type="text" name="speaker_name" id="edit-speech-speaker" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jabatan / Instansi <span class="text-rose-500">*</span></label>
                        <input type="text" name="speaker_title" id="edit-speech-title-instansi" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Foto Resmi</label>
                        <input type="file" name="speech_photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-indigo-600 file:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kutipan Inspiratif / Mutiara Kata <span class="text-rose-500">*</span></label>
                    <input type="text" name="speech_quote" id="edit-speech-quote" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs italic text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Paragraf 1 (Salam & Pembuka) <span class="text-rose-500">*</span></label>
                    <textarea name="speech_p1" id="edit-speech-p1" rows="3" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Paragraf 2 (Kurikulum & Pokok)</label>
                    <textarea name="speech_p2" id="edit-speech-p2" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Paragraf Penutup (Selengkapnya)</label>
                    <textarea name="speech_extended" id="edit-speech-extended" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white"></textarea>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="edit-speech-active" value="1" class="w-4 h-4 text-indigo-600 rounded">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Jadikan sambutan aktif di halaman utama</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeEditSpeechModal()" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/30">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSpeechCompose(show = null) {
            const card = document.getElementById('speech-compose-card');
            const icon = document.getElementById('speech-compose-btn-icon');
            const text = document.getElementById('speech-compose-btn-text');
            if (!card) return;

            const isHidden = card.classList.contains('hidden');
            const shouldShow = show !== null ? show : isHidden;

            if (shouldShow) {
                card.classList.remove('hidden');
                if (icon) icon.innerText = '✕';
                if (text) text.innerText = 'Tutup Form';
                card.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                card.classList.add('hidden');
                if (icon) icon.innerText = '➕';
                if (text) text.innerText = 'Tambah Sambutan Baru';
            }
        }

        function openEditSpeechModal(speech) {
            const modal = document.getElementById('edit-speech-modal');
            const form = document.getElementById('edit-speech-form');
            if (!modal || !form || !speech) return;

            form.action = `/master/website-content/speech/${speech.id}`;
            document.getElementById('edit-speech-title').value = speech.title || '';
            document.getElementById('edit-speech-speaker').value = speech.speaker_name || '';
            document.getElementById('edit-speech-title-instansi').value = speech.speaker_title || '';
            document.getElementById('edit-speech-date').value = speech.speech_date || '';
            document.getElementById('edit-speech-quote').value = speech.quote || '';
            document.getElementById('edit-speech-p1').value = speech.speech_p1 || '';
            document.getElementById('edit-speech-p2').value = speech.speech_p2 || '';
            document.getElementById('edit-speech-extended').value = speech.speech_extended || '';
            document.getElementById('edit-speech-active').checked = !!speech.is_active;

            modal.classList.remove('hidden');
        }

        function closeEditSpeechModal() {
            const modal = document.getElementById('edit-speech-modal');
            if (modal) modal.classList.add('hidden');
        }
    </script>
    @endif

    <!-- ==================== TAB 3: VISI, MISI & PILAR ==================== -->
    @if($activeTab === 'vision' && $canTab('vision'))
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-8">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">
                    🏛️
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Visi & 4 Misi Utama Sekolah</h2>
                    <p class="text-xs text-slate-500">Pedoman filosofis dan capaian strategis jangka panjang sekolah</p>
                </div>
            </div>

            <form action="{{ route('master.website_content.reset') }}" method="POST" onsubmit="return confirm('Reset Visi & Misi ke standar bawaan?')">
                @csrf
                <input type="hidden" name="section" value="vision">
                <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-rose-600 hover:border-rose-300 text-xs font-semibold transition">
                    ↺ Reset Default
                </button>
            </form>
        </div>

        <form action="{{ route('master.website_content.vision_mission') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Visi -->
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Kalimat Visi Sekolah <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="website_visi" rows="3" required maxlength="1000" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500 transition leading-relaxed">{{ old('website_visi', $settings['website_visi'] ?? 'Terwujudnya Peserta Didik yang Beriman dan Bertaqwa, Berakhlak Mulia, Cerdas, Terampil, Mandiri, dan Berwawasan Lingkungan Global.') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Sub Visi / Slogan Karakter
                    </label>
                    <input type="text" name="website_visi_sub" 
                        value="{{ old('website_visi_sub', $settings['website_visi_sub'] ?? '(Unggul dalam IPTEK, Kokoh dalam IMTAQ, Harmonis dengan Alam Semesta)') }}" 
                        maxlength="500" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 outline-none focus:border-indigo-500 transition">
                </div>
            </div>

            <!-- 4 Misi Grid -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">4 Misi Unggulan Sekolah:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($missions as $idx => $misi)
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0">
                                    0{{ $idx + 1 }}
                                </span>
                                <input type="text" name="misi_title[]" value="{{ $misi['title'] }}" required maxlength="100" placeholder="Judul Misi (contoh: Deep Learning)" class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                            </div>
                            <textarea name="misi_desc[]" rows="3" required maxlength="500" placeholder="Uraian kegiatan misi..." class="w-full px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300 outline-none focus:border-indigo-500 leading-relaxed">{{ $misi['desc'] }}</textarea>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition cursor-pointer">
                    <span>💾 Simpan Visi & Misi</span>
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- ==================== TAB 4: FOTO & GAMBAR BANNER ==================== -->
    @if($activeTab === 'media' && $canTab('media'))
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-8">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">
                    🖼️
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Foto & Dokumentasi Visual Website</h2>
                    <p class="text-xs text-slate-500">Unggah foto gedung, ruangan, atau kegiatan sekolah untuk mengganti gambar di website</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- 1. Foto Gedung Utama / Hero Banner -->
            <div class="p-5 rounded-3xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Hero Banner Utama</span>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-extrabold">Landscape</span>
                    </div>
                    <div class="aspect-video rounded-2xl overflow-hidden bg-slate-950 mb-4 border border-slate-200 dark:border-slate-700 shadow-sm">
                        <img src="{{ asset($settings['website_hero_photo'] ?? 'images/website/hero_school.jpg') }}" alt="Gedung Sekolah" class="w-full h-full object-cover">
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs mb-1">Gedung / Gerbang Sekolah</h4>
                    <p class="text-[11px] text-slate-500 mb-4">Tampil sebagai gambar pembuka dan latar depan website.</p>
                </div>

                <form action="{{ route('master.website_content.media') }}" method="POST" enctype="multipart/form-data" class="space-y-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                    @csrf
                    <input type="hidden" name="image_type" value="hero">
                    <input type="file" name="photo" accept="image/*" required class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-200 dark:file:bg-slate-700 file:text-slate-800 dark:file:text-slate-200 cursor-pointer">
                    <button type="submit" class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">
                        Ganti Foto Gedung
                    </button>
                </form>
            </div>

            <!-- 2. Foto Ruang Kelas / Lab Digital -->
            <div class="p-5 rounded-3xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Modul LMS & Kelas</span>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 font-extrabold">Landscape</span>
                    </div>
                    <div class="aspect-video rounded-2xl overflow-hidden bg-slate-950 mb-4 border border-slate-200 dark:border-slate-700 shadow-sm">
                        <img src="{{ asset($settings['website_classroom_photo'] ?? 'images/website/classroom.jpg') }}" alt="Ruang Kelas" class="w-full h-full object-cover">
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs mb-1">Ruang Kelas / Lab Digital</h4>
                    <p class="text-[11px] text-slate-500 mb-4">Tampil pada bagian penjelasan LMS AI & Deep Learning.</p>
                </div>

                <form action="{{ route('master.website_content.media') }}" method="POST" enctype="multipart/form-data" class="space-y-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                    @csrf
                    <input type="hidden" name="image_type" value="classroom">
                    <input type="file" name="photo" accept="image/*" required class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-200 dark:file:bg-slate-700 file:text-slate-800 dark:file:text-slate-200 cursor-pointer">
                    <button type="submit" class="w-full py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs transition">
                        Ganti Foto Kelas
                    </button>
                </form>
            </div>

            <!-- 3. Foto Ekstrakurikuler -->
            <div class="p-5 rounded-3xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Ekstrakurikuler</span>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-extrabold">Landscape</span>
                    </div>
                    <div class="aspect-video rounded-2xl overflow-hidden bg-slate-950 mb-4 border border-slate-200 dark:border-slate-700 shadow-sm">
                        <img src="{{ asset($settings['website_extracurriculars_photo'] ?? 'images/website/extracurriculars.jpg') }}" alt="Kegiatan Ekskul" class="w-full h-full object-cover">
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs mb-1">Aktivitas Siswa & Ekskul</h4>
                    <p class="text-[11px] text-slate-500 mb-4">Tampil pada banner dokumentasi kesiswaan dan olahraga.</p>
                </div>

                <form action="{{ route('master.website_content.media') }}" method="POST" enctype="multipart/form-data" class="space-y-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                    @csrf
                    <input type="hidden" name="image_type" value="extracurriculars">
                    <input type="file" name="photo" accept="image/*" required class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-200 dark:file:bg-slate-700 file:text-slate-800 dark:file:text-slate-200 cursor-pointer">
                    <button type="submit" class="w-full py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition">
                        Ganti Foto Ekskul
                    </button>
                </form>
            </div>

        </div>
    </div>
    @endif

    <!-- ==================== TAB 5: AGENDA KEGIATAN ==================== -->
    @if($activeTab === 'agendas' && $canTab('agendas'))
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Form Tambah Agenda Baru -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-7">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base shrink-0 border border-indigo-500/20 shadow-2xs">
                    📅
                </div>
                <div>
                    <h3 class="font-heading font-black text-sm sm:text-base text-slate-900 dark:text-white">Tambah Agenda Baru</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Publikasikan kegiatan kalender akademik sekolah</p>
                </div>
            </div>

            <form action="{{ route('master.website_content.agenda.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Judul Agenda Kegiatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="agenda_title" required maxlength="200" placeholder="Contoh: Asesmen Sumatif Akhir Semester Genap" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition shadow-2xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Pelaksanaan <span class="text-rose-500">*</span></label>
                        <input type="date" name="agenda_date" required class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition shadow-2xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Berakhir <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <input type="date" name="agenda_end_date" class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition shadow-2xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kategori / Tag <span class="text-rose-500">*</span></label>
                    <input type="text" name="agenda_tag" required maxlength="50" placeholder="Contoh: Akademik / Kesiswaan / P5 / Ujian" class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition shadow-2xs">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Deskripsi Singkat Acara <span class="text-rose-500">*</span></label>
                    <textarea name="agenda_description" rows="3" required maxlength="1000" placeholder="Jelaskan sasaran peserta, ruangan, atau perlengkapan yang perlu dipersiapkan..." class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition leading-relaxed shadow-2xs"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Foto / Banner Pendukung Agenda <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="file" name="agenda_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maks 3MB.</p>
                </div>

                <button type="submit" class="w-full py-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-md shadow-indigo-600/30 flex items-center justify-center gap-2 transition hover:scale-[1.01] cursor-pointer">
                    <span>➕ Publikasikan Agenda</span>
                </button>
            </form>
        </div>

        <!-- Daftar Agenda Saat Ini -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-7">
            <div class="flex items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base shrink-0 border border-indigo-500/20 shadow-2xs">
                        🗓️
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-heading font-black text-sm sm:text-base text-slate-900 dark:text-white">Agenda yang Sedang Tayang</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                {{ count($agendas) }} Agenda
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Ditampilkan pada kalender akademik website publik</p>
                    </div>
                </div>

                <form action="{{ route('master.website_content.reset') }}" method="POST" onsubmit="return confirm('Reset semua agenda ke daftar awal?')">
                    @csrf
                    <input type="hidden" name="section" value="agendas">
                    <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-rose-600 hover:border-rose-300 dark:hover:text-rose-400 text-xs font-semibold transition cursor-pointer flex items-center gap-1.5" title="Reset ke daftar awal">
                        <span>↺</span>
                        <span class="hidden sm:inline">Reset Default</span>
                    </button>
                </form>
            </div>

            <div class="space-y-3">
                @forelse($agendas as $idx => $item)
                    @php
                        $dayStr = '01';
                        $monthStr = 'AGENDA';
                        $displayDateStr = !empty($item['date']) ? $item['date'] : 'Jadwal fleksibel';
                        try {
                            if (!empty($item['date'])) {
                                $cDate = \Carbon\Carbon::parse($item['date']);
                                $dayStr = $cDate->format('d');
                                $monthStr = $cDate->translatedFormat('M');
                                
                                if (!empty($item['end_date']) && $item['end_date'] !== $item['date']) {
                                    $cEnd = \Carbon\Carbon::parse($item['end_date']);
                                    if ($cDate->format('Y-m') === $cEnd->format('Y-m')) {
                                        $dayStr = $cDate->format('d') . '-' . $cEnd->format('d');
                                        $displayDateStr = $cDate->format('d') . ' - ' . $cEnd->translatedFormat('d F Y');
                                    } else {
                                        $displayDateStr = $cDate->translatedFormat('d M') . ' - ' . $cEnd->translatedFormat('d M Y');
                                    }
                                } else {
                                    $displayDateStr = $cDate->translatedFormat('d F Y');
                                }
                            }
                        } catch (\Exception $e) {
                            $dayStr = '--';
                            $monthStr = 'TBA';
                            $displayDateStr = $item['date'] ?? 'TBA';
                        }
                    @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 hover:bg-white dark:hover:bg-slate-800/80 hover:border-indigo-300 dark:hover:border-indigo-700/60 shadow-2xs hover:shadow-md flex items-start justify-between gap-3 group transition-all duration-200">
                        <div class="flex items-start gap-3.5">
                            @if(!empty($item['image']))
                                <div class="w-14 h-14 rounded-2xl overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 group-hover:scale-105 transition-transform">
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-12 h-12 rounded-2xl bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex flex-col items-center justify-center shrink-0 border border-indigo-500/20 group-hover:scale-105 transition-transform">
                                    <span class="text-sm font-black leading-none">{{ $dayStr }}</span>
                                    <span class="text-[9px] font-bold uppercase mt-0.5 leading-none">{{ $monthStr }}</span>
                                </div>
                            @endif

                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-heading font-bold text-xs sm:text-sm text-slate-900 dark:text-white leading-tight">{{ $item['title'] }}</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                        {{ $item['tag'] ?? 'Kegiatan' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        📅 {{ $displayDateStr }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">{{ $item['description'] ?? '' }}</p>
                                @if(!empty($item['created_by_name']))
                                    <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-1">
                                        <span>👤 Diterbitkan oleh: <strong class="text-slate-700 dark:text-slate-200">{{ $item['created_by_name'] }}</strong></span>
                                        @if(!empty($item['created_by_role']))
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">({{ $item['created_by_role'] }})</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <!-- Edit Button -->
                            <button type="button" 
                                data-agenda="{{ json_encode($item) }}" 
                                onclick="openEditAgendaModal(JSON.parse(this.getAttribute('data-agenda')))" 
                                class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 flex items-center justify-center text-xs transition cursor-pointer" title="Edit Agenda">
                                ✏️
                            </button>

                            <!-- Delete Form -->
                            <form action="{{ route('master.website_content.agenda.destroy', ['id' => $item['id'] ?? $idx]) }}" method="POST" onsubmit="return confirm('Hapus agenda ini dari kalender akademik?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-xl bg-slate-200/50 hover:bg-rose-100 dark:bg-slate-800 dark:hover:bg-rose-950/80 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 flex items-center justify-center text-xs transition-colors cursor-pointer shrink-0" title="Hapus Agenda">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 text-xs rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                        Belum ada agenda yang tersimpan. Gunakan formulir di sebelah kiri untuk menambahkan agenda baru.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Edit Agenda Modal -->
    <div id="edit-agenda-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-xl p-6 sm:p-7 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">✏️</span>
                    <h3 class="font-heading font-black text-slate-900 dark:text-white text-base">Edit Agenda Kegiatan</h3>
                </div>
                <button type="button" onclick="closeEditAgendaModal()" class="w-8 h-8 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center">
                    ✕
                </button>
            </div>

            <form id="edit-agenda-form" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Judul Agenda Kegiatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="agenda_title" id="edit-agenda-title" required maxlength="200" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Pelaksanaan <span class="text-rose-500">*</span></label>
                        <input type="date" name="agenda_date" id="edit-agenda-date" required class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Berakhir <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <input type="date" name="agenda_end_date" id="edit-agenda-end-date" class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kategori / Tag <span class="text-rose-500">*</span></label>
                    <input type="text" name="agenda_tag" id="edit-agenda-tag" required maxlength="50" class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Deskripsi Singkat Acara <span class="text-rose-500">*</span></label>
                    <textarea name="agenda_description" id="edit-agenda-description" rows="3" required maxlength="1000" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white outline-none focus:border-indigo-500 leading-relaxed"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ganti Foto / Gambar Pendukung <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <div id="edit-agenda-image-preview" class="mb-2 hidden">
                        <img id="edit-agenda-image-tag" src="" alt="Preview Agenda" class="w-20 h-20 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                    </div>
                    <input type="file" name="agenda_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeEditAgendaModal()" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30">
                        💾 Simpan Perubahan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditAgendaModal(item) {
            const modal = document.getElementById('edit-agenda-modal');
            const form = document.getElementById('edit-agenda-form');
            if (!modal || !form || !item) return;

            form.action = `/master/website-content/agenda/${item.id}`;
            document.getElementById('edit-agenda-title').value = item.title || '';
            document.getElementById('edit-agenda-date').value = item.date || '';
            document.getElementById('edit-agenda-end-date').value = item.end_date || '';
            document.getElementById('edit-agenda-tag').value = item.tag || '';
            document.getElementById('edit-agenda-description').value = item.description || '';

            const previewDiv = document.getElementById('edit-agenda-image-preview');
            const imgTag = document.getElementById('edit-agenda-image-tag');
            if (item.image) {
                imgTag.src = '/' + item.image.replace(/^\//, '');
                previewDiv.classList.remove('hidden');
            } else {
                previewDiv.classList.add('hidden');
            }

            modal.classList.remove('hidden');
        }

        function closeEditAgendaModal() {
            const modal = document.getElementById('edit-agenda-modal');
            if (modal) modal.classList.add('hidden');
        }
    </script>
    @endif

    <!-- ==================== TAB 6: FASILITAS SEKOLAH ==================== -->
    @if($activeTab === 'facilities' && $canTab('facilities'))
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Form Tambah Fasilitas Baru -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-7">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base shrink-0 border border-indigo-500/20 shadow-2xs">
                    ➕
                </div>
                <div>
                    <h3 class="font-heading font-black text-sm sm:text-base text-slate-900 dark:text-white">Tambah Fasilitas Baru</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Tambahkan sarana fisik, laboratorium, atau ruang unggulan sekolah</p>
                </div>
            </div>

            <form action="{{ route('master.website_content.facility.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Fasilitas / Gedung <span class="text-rose-500">*</span></label>
                    <input type="text" name="facility_name" required maxlength="150" placeholder="Contoh: Laboratorium Robotika & AI" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition shadow-2xs">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Uraian / Deskripsi Kelengkapan <span class="text-rose-500">*</span></label>
                    <textarea name="facility_desc" rows="4" required maxlength="1000" placeholder="Jelaskan spesifikasi sarana, perangkat pendukung, pendingin ruangan AC, atau kapasitas siswa..." class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition leading-relaxed shadow-2xs"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Foto Sarana / Fasilitas <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="file" name="facility_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500 cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maks 3MB.</p>
                </div>

                <button type="submit" class="w-full py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-xs shadow-md shadow-emerald-600/30 flex items-center justify-center gap-2 transition hover:scale-[1.01] cursor-pointer">
                    <span>➕ Tambahkan ke Website</span>
                </button>
            </form>
        </div>

        <!-- Daftar Fasilitas Saat Ini -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-6 sm:p-7">
            <div class="flex items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base shrink-0 border border-emerald-500/20 shadow-2xs">
                        🏫
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-heading font-black text-sm sm:text-base text-slate-900 dark:text-white">Daftar Sarana Prasarana</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                {{ count($facilities) }} Sarana
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Ditampilkan pada galeri fasilitas kampus di website publik</p>
                    </div>
                </div>

                <form action="{{ route('master.website_content.reset') }}" method="POST" onsubmit="return confirm('Reset semua fasilitas ke daftar awal standar sekolah?')">
                    @csrf
                    <input type="hidden" name="section" value="facilities">
                    <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-rose-600 hover:border-rose-300 dark:hover:text-rose-400 text-xs font-semibold transition cursor-pointer flex items-center gap-1.5" title="Reset ke daftar awal">
                        <span>↺</span>
                        <span class="hidden sm:inline">Reset Default</span>
                    </button>
                </form>
            </div>

            <div class="space-y-3">
                @forelse($facilities as $idx => $item)
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 hover:bg-white dark:hover:bg-slate-800/80 hover:border-indigo-300 dark:hover:border-indigo-700/60 shadow-2xs hover:shadow-md flex items-start justify-between gap-3 group transition-all duration-200">
                        <div class="flex items-start gap-3.5">
                            @if(!empty($item['image']))
                                <div class="w-14 h-14 rounded-2xl overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 group-hover:scale-105 transition-transform">
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-11 h-11 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-500/20 text-lg group-hover:scale-105 transition-transform">
                                    {{ $item['icon'] ?? '🏛️' }}
                                </div>
                            @endif

                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                        #{{ $idx + 1 }}
                                    </span>
                                    <h4 class="font-heading font-bold text-xs sm:text-sm text-slate-900 dark:text-white leading-tight">
                                        {{ $item['name'] }}
                                    </h4>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                    {{ $item['desc'] }}
                                </p>
                                @if(!empty($item['created_by_name']))
                                    <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-1">
                                        <span>👤 Diterbitkan oleh: <strong class="text-slate-700 dark:text-slate-200">{{ $item['created_by_name'] }}</strong></span>
                                        @if(!empty($item['created_by_role']))
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">({{ $item['created_by_role'] }})</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <!-- Edit Button -->
                            <button type="button" 
                                data-facility="{{ json_encode($item) }}" 
                                onclick="openEditFacilityModal(JSON.parse(this.getAttribute('data-facility')))" 
                                class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 flex items-center justify-center text-xs transition cursor-pointer" title="Edit Fasilitas">
                                ✏️
                            </button>

                            <!-- Delete Form -->
                            <form action="{{ route('master.website_content.facility.destroy', ['id' => $item['id'] ?? $idx]) }}" method="POST" onsubmit="return confirm('Hapus fasilitas ini dari daftar sarana prasarana?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-xl bg-slate-200/50 hover:bg-rose-100 dark:bg-slate-800 dark:hover:bg-rose-950/80 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 flex items-center justify-center text-xs transition-colors cursor-pointer shrink-0" title="Hapus Fasilitas">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 text-xs rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                        Belum ada fasilitas yang tersimpan. Gunakan formulir di sebelah kiri untuk menambahkan fasilitas baru.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Edit Facility Modal -->
    <div id="edit-facility-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-xl p-6 sm:p-7 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">✏️</span>
                    <h3 class="font-heading font-black text-slate-900 dark:text-white text-base">Edit Fasilitas Sekolah</h3>
                </div>
                <button type="button" onclick="closeEditFacilityModal()" class="w-8 h-8 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center">
                    ✕
                </button>
            </div>

            <form id="edit-facility-form" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Fasilitas / Gedung <span class="text-rose-500">*</span></label>
                    <input type="text" name="facility_name" id="edit-facility-name" required maxlength="150" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Uraian / Deskripsi Kelengkapan <span class="text-rose-500">*</span></label>
                    <textarea name="facility_desc" id="edit-facility-desc" rows="4" required maxlength="1000" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white outline-none focus:border-emerald-500 leading-relaxed"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ganti Foto Sarana / Fasilitas <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <div id="edit-facility-image-preview" class="mb-2 hidden">
                        <img id="edit-facility-image-tag" src="" alt="Preview Fasilitas" class="w-20 h-20 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                    </div>
                    <input type="file" name="facility_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500 cursor-pointer">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeEditFacilityModal()" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30">
                        💾 Simpan Perubahan Fasilitas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditFacilityModal(item) {
            const modal = document.getElementById('edit-facility-modal');
            const form = document.getElementById('edit-facility-form');
            if (!modal || !form || !item) return;

            form.action = `/master/website-content/facility/${item.id}`;
            document.getElementById('edit-facility-name').value = item.name || '';
            document.getElementById('edit-facility-desc').value = item.desc || '';

            const previewDiv = document.getElementById('edit-facility-image-preview');
            const imgTag = document.getElementById('edit-facility-image-tag');
            if (item.image) {
                imgTag.src = '/' + item.image.replace(/^\//, '');
                previewDiv.classList.remove('hidden');
            } else {
                previewDiv.classList.add('hidden');
            }

            modal.classList.remove('hidden');
        }

        function closeEditFacilityModal() {
            const modal = document.getElementById('edit-facility-modal');
            if (modal) modal.classList.add('hidden');
        }
    </script>
    @endif

    <!-- ==================== TAB: DEWAN GURU & STAF TATA USAHA ==================== -->
    @if($activeTab === 'staff' && $canTab('staff'))
    <div class="space-y-6">
        
        <!-- Action Bar: Add Form Toggle, Master Sync, and Reset -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div>
                <h3 class="font-heading font-black text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <span>👨‍🏫 Dewan Guru & Staf Tata Usaha (TU)</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                        {{ count($staffMembers) }} Total Pegawai
                    </span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Kelola data identitas guru (mapel & tugas) serta staf TU (tugas pengganti mapel) beserta foto resmi yang ditampilkan di website sekolah.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" onclick="toggleStaffCompose()" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/25 flex items-center gap-2 transition cursor-pointer">
                    <span id="staff-compose-btn-icon">➕</span>
                    <span id="staff-compose-btn-text">Tambah Pegawai</span>
                </button>

                <form action="{{ route('master.website_content.staff.sync') }}" method="POST" onsubmit="return confirm('Sinkronkan data dari Master Guru? Data baru yang belum ada akan ditambahkan otomatis.')">
                    @csrf
                    <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm shadow-emerald-600/20" title="Impor otomatis dari Master Guru">
                        <span>🔄</span>
                        <span>Sinkronkan Master</span>
                    </button>
                </form>

                <form action="{{ route('master.website_content.reset') }}" method="POST" onsubmit="return confirm('Reset semua data guru & staf TU ke standar bawaan sistem?')">
                    @csrf
                    <input type="hidden" name="section" value="staff">
                    <button type="submit" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-rose-600 hover:border-rose-300 text-xs font-semibold transition" title="Reset ke susunan awal">
                        ↺ Reset
                    </button>
                </form>
            </div>
        </div>

        <!-- Form Tambah / Edit Pegawai (Collapsible) -->
        <div id="staff-compose-card" class="hidden bg-white dark:bg-slate-900 rounded-3xl border border-indigo-200 dark:border-indigo-900/60 shadow-xl p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                        👨‍🏫
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-slate-900 dark:text-white text-base" id="staff-form-heading">Tambah Pegawai Baru (Guru / TU)</h3>
                        <p class="text-xs text-slate-500">Isi formulir identitas, posisi, mapel (guru) atau tugas (TU), serta unggah foto resmi.</p>
                    </div>
                </div>
                <button type="button" onclick="toggleStaffCompose(false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    ✕
                </button>
            </div>

            <form id="staff-form" action="{{ route('master.website_content.staff.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div id="staff-form-method-container"></div>

                <!-- PILIH DARI MASTER DATA GURU & PTK (INTEGRASI FITUR SISSMA1LE) -->
                @if(!empty($ptkMasterList) && count($ptkMasterList) > 0)
                <div class="p-4 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/40 dark:to-purple-950/30 rounded-2xl border-2 border-indigo-200 dark:border-indigo-800 shadow-xs">
                    <label class="block text-xs font-black text-indigo-950 dark:text-indigo-200 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs">⚡</span>
                            <span>Tarik Cepat dari Master Data (Data Guru & PTK)</span>
                        </span>
                        <span class="text-[10.5px] font-normal text-indigo-600 dark:text-indigo-400">Tersedia {{ count($ptkMasterList) }} Pegawai Terdaftar</span>
                    </label>
                    <select id="ptk-master-select" onchange="fillFromPtkMaster(this)" class="w-full text-xs rounded-xl border border-indigo-300 dark:border-indigo-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 py-2.5 px-3 font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Guru atau Staf TU untuk Mengisi Formulir Otomatis --</option>
                        <optgroup label="👨‍🏫 Dewan Guru (Tenaga Pendidik)">
                            @foreach($ptkMasterList as $ptk)
                                @if($ptk['type'] === 'guru')
                                    <option value="{{ $ptk['id'] }}" data-json="{{ json_encode($ptk) }}">
                                        {{ $ptk['name'] }} &bull; NIP: {{ $ptk['nip'] }} &bull; {{ $ptk['duty'] }}
                                    </option>
                                @endif
                            @endforeach
                        </optgroup>
                        <optgroup label="💼 Tenaga Kependidikan (Staf TU)">
                            @foreach($ptkMasterList as $ptk)
                                @if($ptk['type'] === 'staf_tu')
                                    <option value="{{ $ptk['id'] }}" data-json="{{ json_encode($ptk) }}">
                                        {{ $ptk['name'] }} &bull; NIP: {{ $ptk['nip'] }} &bull; {{ $ptk['duty'] }}
                                    </option>
                                @endif
                            @endforeach
                        </optgroup>
                    </select>
                    <p class="text-[11px] text-indigo-700 dark:text-indigo-300 mt-1.5">
                        Memilih nama guru/staf akan otomatis mengisi formulir nama, NIP, jenis kelamin, mapel, tugas pokok, nomor kontak, dan status kepegawaian.
                    </p>
                </div>
                @endif

                <!-- 1. Pemilihan Posisi: GURU vs STAF TU -->
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                        Pilih Posisi / Kategori Pegawai <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex items-center gap-3 p-4 rounded-2xl border cursor-pointer transition has-checked:border-emerald-500 has-checked:bg-emerald-50/50 dark:has-checked:bg-emerald-950/30 border-slate-200 dark:border-slate-800">
                            <input type="radio" name="category" value="guru" id="radio_cat_guru" checked onchange="onStaffCategoryChange('guru')" class="w-4 h-4 text-emerald-600">
                            <div>
                                <span class="block text-xs sm:text-sm font-black text-slate-900 dark:text-white">👨‍🏫 Guru / Tenaga Pendidik</span>
                                <span class="block text-[11px] text-slate-500">Memiliki formulir Mata Pelajaran (Mapel) yang dipegang & tugas tambahan.</span>
                            </div>
                        </label>
                        <label class="relative flex items-center gap-3 p-4 rounded-2xl border cursor-pointer transition has-checked:border-amber-500 has-checked:bg-amber-50/50 dark:has-checked:bg-amber-950/30 border-slate-200 dark:border-slate-800">
                            <input type="radio" name="category" value="staf_tu" id="radio_cat_tu" onchange="onStaffCategoryChange('staf_tu')" class="w-4 h-4 text-amber-600">
                            <div>
                                <span class="block text-xs sm:text-sm font-black text-slate-900 dark:text-white">💼 Staf Tata Usaha (TU)</span>
                                <span class="block text-[11px] text-slate-500">Mengisi nama penugasan / unit kerja TU sebagai pengganti form mapel.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 2. Identitas Pokok Pegawai -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Nama Lengkap & Gelar Akademik <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="input_staff_name" required placeholder="Contoh: Drs. H. Ahmad Sanusi, M.Pd." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            NIP / NUPTK / No. Identitas Pegawai
                        </label>
                        <input type="text" name="nip" id="input_staff_nip" placeholder="Contoh: 198009132008011005 atau -" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select name="gender" id="input_staff_gender" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Status Kepegawaian
                        </label>
                        <select name="employment_status" id="input_staff_employment_status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="PNS / ASN">PNS / ASN</option>
                            <option value="PPPK">PPPK</option>
                            <option value="Guru Tetap Yayasan">Guru Tetap</option>
                            <option value="Guru Honorer / GTT">Guru Honorer / GTT</option>
                            <option value="Tenaga Honorer / PTT">Tenaga Honorer / PTT</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            No. Telepon / Kontak WA (Opsional)
                        </label>
                        <input type="text" name="phone" id="input_staff_phone" placeholder="Contoh: 08123456789" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- 3. FORMULIR KHUSUS: MAPEL (GURU) VS TUGAS PENGGANTI MAPEL (STAF TU) -->
                
                <!-- A. Container Guru: Mapel & Tugas Tambahan -->
                <div id="guru-specific-fields" class="p-5 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/60 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📚</span>
                        <div>
                            <span class="text-xs font-black text-emerald-900 dark:text-emerald-300 block">Formulir Penugasan Guru (Tenaga Pendidik)</span>
                            <span class="text-[11px] text-emerald-700 dark:text-emerald-400">Entri mata pelajaran yang dipegang dan tugas tambahan guru.</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">
                                Mata Pelajaran (Mapel) yang Dipegang <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="mapel" id="input_staff_mapel" placeholder="Contoh: Kimia & Sains Terpadu / Matematika / PJOK" class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 dark:border-emerald-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="text-[10px] text-slate-500 mt-1 block">Mata pelajaran utama atau keahlian kurikulum yang diampu.</span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">
                                Tugas / Jabatan Tambahan Guru
                            </label>
                            <input type="text" name="duty" id="input_staff_duty" placeholder="Contoh: Kepala Sekolah / Wakasek Kurikulum / Wali Kelas XII-7" class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 dark:border-emerald-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="text-[10px] text-slate-500 mt-1 block">Kosongkan jika hanya guru mapel biasa.</span>
                        </div>
                    </div>
                </div>

                <!-- B. Container Staf TU: Nama Tugas Pengganti Form Mapel -->
                <div id="tu-specific-fields" class="hidden p-5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/60 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-base">💼</span>
                        <div>
                            <span class="text-xs font-black text-amber-900 dark:text-amber-300 block">Formulir Penugasan Staf Tata Usaha (TU)</span>
                            <span class="text-[11px] text-amber-700 dark:text-amber-400">Pengganti form mapel: Isikan nama tugas atau unit kerja staf tata usaha.</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">
                            Nama Tugas / Penugasan Tata Usaha (Pengganti Form Mapel) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="tu_duty" id="input_staff_tu_duty" placeholder="Contoh: Pengadministrasi Layanan Kesiswaan & Operator Dapodik / Bendahara Sekolah" class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <div class="flex items-center gap-2 mt-1.5 text-[11px] text-amber-800 dark:text-amber-300 font-medium">
                            <span>💡 Contoh tugas:</span>
                            <span class="underline cursor-pointer hover:text-amber-600" onclick="setTuDutyPreset('Kepala Urusan Tata Usaha (Kaur TU)')">Kaur TU</span> •
                            <span class="underline cursor-pointer hover:text-amber-600" onclick="setTuDutyPreset('Pengadministrasi Layanan Kesiswaan & Operator Dapodik')">Kesiswaan & Dapodik</span> •
                            <span class="underline cursor-pointer hover:text-amber-600" onclick="setTuDutyPreset('Bendahara Pengeluaran Pembantu & BOP/BOS')">Bendahara Sekolah</span> •
                            <span class="underline cursor-pointer hover:text-amber-600" onclick="setTuDutyPreset('Pengadministrasi Layanan Kepegawaian & Persuratan')">Persuratan & Kepegawaian</span> •
                            <span class="underline cursor-pointer hover:text-amber-600" onclick="setTuDutyPreset('Teknisi Jaringan, Server CBT & Laboran IT')">Teknisi Lab IT</span>
                        </div>
                    </div>
                </div>

                <!-- 4. Unggah Foto & Pengaturan Tampilan -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="sm:col-span-2 space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Foto Resmi Pegawai (Guru / TU)
                        </label>
                        <input type="file" name="photo" id="input_staff_photo" accept="image/png,image/jpeg,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900">
                        <p class="text-[11px] text-slate-400">Format: JPG, PNG, atau WebP (Rasio potret 3:4 disarankan, maks. 5MB). Jika dikosongkan, website akan menampilkan avatar inisial modern.</p>
                        
                        <div class="flex items-center gap-6 pt-2">
                            <div class="w-32">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Urutan Tampil</label>
                                <input type="number" name="order" id="input_staff_order" value="1" min="1" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer mt-5">
                                <input type="checkbox" name="is_active" id="input_staff_is_active" value="1" checked class="w-4 h-4 rounded text-indigo-600">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Tampilkan di Website</span>
                            </label>
                        </div>
                    </div>

                    <!-- Photo Preview Box -->
                    <div class="flex flex-col items-center justify-center p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-center">
                        <span class="text-[10px] uppercase font-bold text-slate-400 mb-2">Pratinjau Foto</span>
                        <div class="w-24 h-32 rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-700 border-2 border-slate-300 dark:border-slate-600 shadow-sm flex items-center justify-center">
                            <img id="staff_preview_img" src="{{ asset('images/website/headmaster.jpg') }}" alt="Foto Pegawai" class="w-full h-full object-cover">
                        </div>
                        <span class="text-[10px] text-slate-400 mt-2">Pratinjau Kartu</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="toggleStaffCompose(false)" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/25 transition">
                        Simpan Data Pegawai
                    </button>
                </div>
            </form>
        </div>

        <!-- Filter & Search Bar in Admin -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 text-xs font-bold">
                <button type="button" onclick="filterStaffTableAdmin('all', this)" class="staff-admin-filter-btn px-3.5 py-1.5 rounded-xl bg-indigo-600 text-white shadow-xs transition">
                    Semua ({{ count($staffMembers) }})
                </button>
                <button type="button" onclick="filterStaffTableAdmin('guru', this)" class="staff-admin-filter-btn px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    👨‍🏫 Guru ({{ collect($staffMembers)->where('category', 'guru')->count() }})
                </button>
                <button type="button" onclick="filterStaffTableAdmin('staf_tu', this)" class="staff-admin-filter-btn px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    💼 Staf TU ({{ collect($staffMembers)->where('category', 'staf_tu')->count() }})
                </button>
            </div>

            <div class="w-full sm:w-64">
                <input type="text" id="staff-admin-search-input" oninput="filterStaffTableAdminLive()" placeholder="Cari nama, NIP, mapel, atau tugas..." class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <!-- Table of Staff Members in Admin -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="p-4 w-12 text-center">Urutan</th>
                            <th class="p-4">Pegawai (Nama & Identitas)</th>
                            <th class="p-4">Posisi</th>
                            <th class="p-4">Mapel (Guru) / Tugas (TU)</th>
                            <th class="p-4">Status Kerja</th>
                            <th class="p-4 text-center">Tayang</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800" id="staff-admin-tbody">
                        @forelse($staffMembers as $member)
                            @php
                                $isGuru = ($member['category'] ?? '') === 'guru';
                                $searchText = strtolower(($member['name'] ?? '') . ' ' . ($member['nip'] ?? '') . ' ' . ($member['mapel'] ?? '') . ' ' . ($member['duty'] ?? '') . ' ' . ($member['tu_duty'] ?? ''));
                            @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition staff-row" data-category="{{ $member['category'] ?? 'guru' }}" data-search="{{ $searchText }}">
                                <td class="p-4 text-center font-mono font-bold text-slate-400">
                                    {{ $member['order'] ?? $loop->iteration }}
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Photo / Avatar -->
                                        @php
                                            $adminPhotoClean = !empty($member['photo']) ? explode('?', $member['photo'])[0] : null;
                                            $adminPhotoExists = $adminPhotoClean && file_exists(public_path($adminPhotoClean));
                                        @endphp
                                        <div class="w-11 h-14 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 flex items-center justify-center font-heading font-black text-sm text-indigo-600">
                                            @if($adminPhotoExists)
                                                <img src="{{ asset($adminPhotoClean) . '?v=' . filemtime(public_path($adminPhotoClean)) }}" alt="{{ $member['name'] }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-indigo-500/20 to-purple-500/20 text-indigo-600 dark:text-indigo-400 font-bold">
                                                    {{ substr($member['name'], 0, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <strong class="font-heading font-bold text-slate-900 dark:text-white block text-sm">
                                                {{ $member['name'] }}
                                            </strong>
                                            <span class="text-[11px] text-slate-400 font-mono block">
                                                NIP: {{ $member['nip'] ?? '-' }} • {{ ($member['gender'] ?? 'L') === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if($isGuru)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            👨‍🏫 Guru / Pendidik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            💼 Staf Tata Usaha
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($isGuru)
                                        <div>
                                            <span class="px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-[11px] border border-indigo-200 dark:border-indigo-800">
                                                📚 {{ $member['mapel'] ?? '-' }}
                                            </span>
                                            @if(!empty($member['duty']) && $member['duty'] !== 'Guru Pengajar')
                                                <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                                    📌 {{ $member['duty'] }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div>
                                            <span class="px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 font-bold text-[11px] border border-amber-200 dark:border-amber-800">
                                                💼 {{ $member['tu_duty'] ?? $member['duty'] ?? 'Staf Tata Usaha' }}
                                            </span>
                                            <span class="block text-[10px] text-slate-400 mt-0.5">(Nama Tugas Pengganti Mapel)</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">
                                        {{ $member['employment_status'] ?? 'PNS / ASN' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    @if($member['is_active'] ?? true)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 dark:bg-slate-800">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" onclick="editStaff({{ json_encode($member) }})" class="p-2 rounded-xl text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 transition" title="Edit Pegawai">
                                            ✏️
                                        </button>
                                        <form action="{{ route('master.website_content.staff.destroy', $member['id']) }}" method="POST" onsubmit="return confirm('Hapus pegawai {{ $member['name'] }} dari profil website?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition" title="Hapus Pegawai">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-12 text-center text-slate-400">
                                    Belum ada data pegawai. Gunakan tombol "Sinkronkan Master" atau "Tambah Pegawai" di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @endif

</div>

<!-- Client-side Interactive Controllers for News & Staff -->
<script>
    // ==================== NEWS CONTROLLERS ====================
    function toggleNewsCompose(forceState) {
        const card = document.getElementById('news-compose-card');
        const icon = document.getElementById('compose-btn-icon');
        const text = document.getElementById('compose-btn-text');
        if (!card) return;

        const isHidden = card.classList.contains('hidden');
        const shouldShow = (typeof forceState === 'boolean') ? forceState : isHidden;

        if (shouldShow) {
            card.classList.remove('hidden');
            if (icon) icon.innerText = '✕';
            if (text) text.innerText = 'Tutup Formulir';
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            card.classList.add('hidden');
            if (icon) icon.innerText = '➕';
            if (text) text.innerText = 'Tulis Berita Baru';
            resetNewsForm();
        }
    }

    // ==================== RICH TEXT EDITOR FOR NEWS ====================
    let isNewsCodeMode = false;
    let savedNewsEditorSelection = null;
    let currentImageTab = 'upload';

    function execNewsCmd(command, value = null) {
        const visual = document.getElementById('news_editor_visual');
        if (isNewsCodeMode || !visual) return;
        visual.focus();
        document.execCommand(command, false, value);
        syncNewsContent();
    }

    function syncNewsContent() {
        const visual = document.getElementById('news_editor_visual');
        const hiddenInput = document.getElementById('input_news_content');
        const sourceTextarea = document.getElementById('news_source_textarea');
        const wordCount = document.getElementById('news_word_count');
        if (!visual || !hiddenInput) return;

        if (!isNewsCodeMode) {
            hiddenInput.value = visual.innerHTML;
            if (sourceTextarea) sourceTextarea.value = visual.innerHTML;
        } else if (sourceTextarea) {
            hiddenInput.value = sourceTextarea.value;
            visual.innerHTML = sourceTextarea.value;
        }

        if (wordCount) {
            const rawText = visual.innerText.trim();
            const words = rawText ? rawText.split(/\s+/).length : 0;
            const chars = rawText.length;
            wordCount.innerText = `${words} Kata • ${chars} Karakter`;
        }
    }

    function toggleNewsSourceCode() {
        const visual = document.getElementById('news_editor_visual');
        const sourceTextarea = document.getElementById('news_source_textarea');
        const btn = document.getElementById('btn_toggle_source');
        const label = document.getElementById('label_toggle_source');
        if (!visual || !sourceTextarea) return;

        if (!isNewsCodeMode) {
            // Masuk ke Mode HTML Code
            sourceTextarea.value = visual.innerHTML;
            visual.classList.add('hidden');
            sourceTextarea.classList.remove('hidden');
            sourceTextarea.focus();
            isNewsCodeMode = true;
            if (label) label.innerText = 'Visual';
            if (btn) btn.classList.add('bg-indigo-600', 'text-white');
        } else {
            // Kembali ke Mode Visual WYSIWYG
            visual.innerHTML = sourceTextarea.value;
            syncNewsContent();
            sourceTextarea.classList.add('hidden');
            visual.classList.remove('hidden');
            visual.focus();
            isNewsCodeMode = false;
            if (label) label.innerText = 'HTML';
            if (btn) btn.classList.remove('bg-indigo-600', 'text-white');
        }
    }

    function insertNewsLink() {
        const url = prompt('Masukkan URL Link (contoh: https://smalengkong.sch.id):');
        if (url && url.trim()) {
            execNewsCmd('createLink', url.trim());
        }
    }

    // Modal Gambar
    function openNewsImageModal() {
        saveNewsSelection();
        const modal = document.getElementById('modal-insert-news-image');
        if (modal) modal.classList.remove('hidden');
    }

    function closeNewsImageModal() {
        const modal = document.getElementById('modal-insert-news-image');
        if (modal) modal.classList.add('hidden');
    }

    function switchImageTab(tab) {
        currentImageTab = tab;
        const btnUpload = document.getElementById('tab_btn_upload');
        const btnUrl = document.getElementById('tab_btn_url');
        const paneUpload = document.getElementById('pane_image_upload');
        const paneUrl = document.getElementById('pane_image_url');

        if (tab === 'upload') {
            btnUpload.className = 'flex-1 py-1.5 rounded-lg bg-indigo-600 text-white transition cursor-pointer';
            btnUrl.className = 'flex-1 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition cursor-pointer';
            paneUpload.classList.remove('hidden');
            paneUrl.classList.add('hidden');
        } else {
            btnUrl.className = 'flex-1 py-1.5 rounded-lg bg-indigo-600 text-white transition cursor-pointer';
            btnUpload.className = 'flex-1 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition cursor-pointer';
            paneUrl.classList.remove('hidden');
            paneUpload.classList.add('hidden');
        }
    }

    function previewInlineUpload(input) {
        const area = document.getElementById('upload_preview_area');
        const img = document.getElementById('upload_preview_src');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (img) img.src = e.target.result;
                if (area) area.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    async function commitInsertNewsImage() {
        let imageUrl = null;
        const caption = document.getElementById('news_inline_image_caption')?.value?.trim() || '';
        const widthClass = document.getElementById('news_inline_image_width')?.value || 'w-3/4';
        const align = document.getElementById('news_inline_image_align')?.value || 'center';

        if (currentImageTab === 'upload') {
            const fileInput = document.getElementById('news_inline_image_file');
            if (!fileInput.files || !fileInput.files[0]) {
                alert('Silakan pilih berkas gambar yang ingin diunggah.');
                return;
            }

            const progress = document.getElementById('upload_progress');
            if (progress) progress.classList.remove('hidden');

            const formData = new FormData();
            formData.append('image', fileInput.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch("{{ route('master.website_content.news.upload_inline_image') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();
                if (progress) progress.classList.add('hidden');

                if (data.success && data.url) {
                    imageUrl = data.url;
                } else {
                    alert(data.message || 'Gagal mengunggah gambar ke server.');
                    return;
                }
            } catch (err) {
                if (progress) progress.classList.add('hidden');
                alert('Terjadi kesalahan saat mengunggah gambar.');
                return;
            }
        } else {
            imageUrl = document.getElementById('news_inline_image_url')?.value?.trim();
            if (!imageUrl) {
                alert('Silakan masukkan tautan URL gambar.');
                return;
            }
        }

        if (imageUrl) {
            let alignClass = 'mx-auto text-center';
            if (align === 'left') alignClass = 'mr-auto text-left';
            if (align === 'right') alignClass = 'ml-auto text-right';

            let widthStyle = 'width: 75%;';
            if (widthClass === 'w-full') widthStyle = 'width: 100%;';
            if (widthClass === 'w-1/2') widthStyle = 'width: 50%;';

            const figureHtml = `
                <figure class="my-4 ${alignClass}" style="${widthStyle} max-width: 100%;">
                    <img src="${imageUrl}" alt="${caption || 'Foto Berita'}" class="rounded-2xl border border-slate-200 dark:border-slate-700 shadow-md w-full h-auto object-cover" />
                    ${caption ? `<figcaption class="text-[11px] text-slate-500 italic mt-1.5 text-center">${caption}</figcaption>` : ''}
                </figure>
                <p><br></p>
            `;

            restoreNewsSelection();
            execNewsCmd('insertHTML', figureHtml);
            closeNewsImageModal();
            syncNewsContent();
        }
    }

    // Modal Tabel
    function openNewsTableModal() {
        saveNewsSelection();
        const modal = document.getElementById('modal-insert-news-table');
        if (modal) modal.classList.remove('hidden');
    }

    function closeNewsTableModal() {
        const modal = document.getElementById('modal-insert-news-table');
        if (modal) modal.classList.add('hidden');
    }

    function commitInsertNewsTable() {
        const rows = parseInt(document.getElementById('news_table_rows')?.value || '3', 10);
        const cols = parseInt(document.getElementById('news_table_cols')?.value || '3', 10);
        const hasHeader = document.getElementById('news_table_header')?.checked ?? true;

        if (rows < 1 || cols < 1) {
            alert('Jumlah baris dan kolom minimal 1.');
            return;
        }

        let tableHtml = '<div class="overflow-x-auto my-4"><table class="w-full border-collapse border border-slate-300 dark:border-slate-700 text-xs">';
        
        for (let r = 0; r < rows; r++) {
            tableHtml += '<tr>';
            for (let c = 0; c < cols; c++) {
                if (r === 0 && hasHeader) {
                    tableHtml += `<th class="border border-slate-300 dark:border-slate-700 p-2.5 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-left">Header Kolom ${c + 1}</th>`;
                } else {
                    tableHtml += `<td class="border border-slate-300 dark:border-slate-700 p-2.5 text-slate-700 dark:text-slate-300">Data Baris ${r + 1}, Kolom ${c + 1}</td>`;
                }
            }
            tableHtml += '</tr>';
        }
        tableHtml += '</table></div><p><br></p>';

        restoreNewsSelection();
        execNewsCmd('insertHTML', tableHtml);
        closeNewsTableModal();
        syncNewsContent();
    }

    function saveNewsSelection() {
        const sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            savedNewsEditorSelection = sel.getRangeAt(0);
        }
    }

    function restoreNewsSelection() {
        const visual = document.getElementById('news_editor_visual');
        if (visual) visual.focus();
        if (savedNewsEditorSelection) {
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(savedNewsEditorSelection);
        }
    }

    function resetNewsForm() {
        const form = document.getElementById('news-form');
        const heading = document.getElementById('form-heading');
        const methodContainer = document.getElementById('form-method-container');
        if (!form) return;

        form.action = "{{ route('master.website_content.news.store') }}";
        if (methodContainer) methodContainer.innerHTML = '';
        if (heading) heading.innerText = 'Tulis Berita Baru';

        form.reset();
        const preview = document.getElementById('news_preview_img');
        if (preview) preview.src = "{{ asset('images/website/classroom.jpg') }}";

        const visual = document.getElementById('news_editor_visual');
        const hiddenInput = document.getElementById('input_news_content');
        const sourceTextarea = document.getElementById('news_source_textarea');
        if (visual) visual.innerHTML = '';
        if (hiddenInput) hiddenInput.value = '';
        if (sourceTextarea) sourceTextarea.value = '';
        syncNewsContent();
    }

    function editNews(article) {
        toggleNewsCompose(true);
        const form = document.getElementById('news-form');
        const heading = document.getElementById('form-heading');
        const methodContainer = document.getElementById('form-method-container');
        if (!form) return;

        form.action = "/master/website-content/news/" + article.id;
        if (methodContainer) {
            methodContainer.innerHTML = '@method("PUT")';
        }
        if (heading) {
            heading.innerText = 'Edit Berita: ' + article.title;
        }

        document.getElementById('input_news_title').value = article.title || '';
        document.getElementById('input_news_category').value = article.category || 'Kegiatan';
        document.getElementById('input_news_date').value = article.date || '';
        document.getElementById('input_news_author').value = article.author || 'Humas SMAN 1 Lengkong';
        document.getElementById('input_news_excerpt').value = article.excerpt || '';
        
        const contentVal = article.content || '';
        const visual = document.getElementById('news_editor_visual');
        const hiddenInput = document.getElementById('input_news_content');
        const sourceTextarea = document.getElementById('news_source_textarea');
        if (visual) visual.innerHTML = contentVal;
        if (hiddenInput) hiddenInput.value = contentVal;
        if (sourceTextarea) sourceTextarea.value = contentVal;
        syncNewsContent();

        document.getElementById('input_is_featured').checked = !!article.is_featured;

        const preview = document.getElementById('news_preview_img');
        if (preview && article.image) {
            preview.src = article.image.startsWith('http') ? article.image : ('/' + article.image.replace(/^\/+/, ''));
        }
    }

    // Inisialisasi event listener editor berita saat DOM dimuat
    document.addEventListener('DOMContentLoaded', function () {
        const visual = document.getElementById('news_editor_visual');
        const form = document.getElementById('news-form');

        if (visual) {
            visual.addEventListener('input', syncNewsContent);
            visual.addEventListener('paste', function (e) {
                // Tangani paste gambar langsung dari clipboard (Ctrl+V)
                if (e.clipboardData && e.clipboardData.items) {
                    const items = e.clipboardData.items;
                    for (let i = 0; i < items.length; i++) {
                        if (items[i].type.indexOf('image') !== -1) {
                            const blob = items[i].getAsFile();
                            const formData = new FormData();
                            formData.append('image', blob);
                            formData.append('_token', '{{ csrf_token() }}');

                            fetch("{{ route('master.website_content.news.upload_inline_image') }}", {
                                method: 'POST',
                                body: formData,
                                headers: { 'Accept': 'application/json' }
                            })
                            .then(r => r.json())
                            .then(res => {
                                if (res.success && res.url) {
                                    execNewsCmd('insertHTML', `<img src="${res.url}" alt="Foto Sisipan" class="rounded-2xl border border-slate-200 dark:border-slate-700 shadow-md w-3/4 mx-auto my-3 h-auto" /><p><br></p>`);
                                    syncNewsContent();
                                }
                            })
                            .catch(() => {});
                        }
                    }
                }
                setTimeout(syncNewsContent, 50);
            });
        }

        if (form) {
            form.addEventListener('submit', function (e) {
                syncNewsContent();
                const hiddenInput = document.getElementById('input_news_content');
                if (!hiddenInput || !hiddenInput.value.trim() || hiddenInput.value.trim() === '<br>') {
                    e.preventDefault();
                    alert('Isi lengkap berita tidak boleh kosong.');
                    return false;
                }
            });
        }
    });

    // ==================== STAFF CONTROLLERS ====================
    function toggleStaffCompose(forceState) {
        const card = document.getElementById('staff-compose-card');
        const icon = document.getElementById('staff-compose-btn-icon');
        const text = document.getElementById('staff-compose-btn-text');
        if (!card) return;

        const isHidden = card.classList.contains('hidden');
        const shouldShow = (typeof forceState === 'boolean') ? forceState : isHidden;

        if (shouldShow) {
            card.classList.remove('hidden');
            if (icon) icon.innerText = '✕';
            if (text) text.innerText = 'Tutup Formulir';
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            card.classList.add('hidden');
            if (icon) icon.innerText = '➕';
            if (text) text.innerText = 'Tambah Pegawai';
            resetStaffForm();
        }
    }

    function resetStaffForm() {
        const form = document.getElementById('staff-form');
        const heading = document.getElementById('staff-form-heading');
        const methodContainer = document.getElementById('staff-form-method-container');
        if (!form) return;

        form.action = "{{ route('master.website_content.staff.store') }}";
        if (methodContainer) methodContainer.innerHTML = '';
        if (heading) heading.innerText = 'Tambah Pegawai Baru (Guru / TU)';

        form.reset();
        document.getElementById('radio_cat_guru').checked = true;
        onStaffCategoryChange('guru');

        const preview = document.getElementById('staff_preview_img');
        if (preview) preview.src = "{{ asset('images/website/headmaster.jpg') }}";
    }

    function onStaffCategoryChange(category) {
        const guruFields = document.getElementById('guru-specific-fields');
        const tuFields = document.getElementById('tu-specific-fields');
        const mapelInput = document.getElementById('input_staff_mapel');
        const tuDutyInput = document.getElementById('input_staff_tu_duty');

        if (category === 'guru') {
            if (guruFields) guruFields.classList.remove('hidden');
            if (tuFields) tuFields.classList.add('hidden');
            if (mapelInput) mapelInput.required = true;
            if (tuDutyInput) tuDutyInput.required = false;
        } else {
            if (guruFields) guruFields.classList.add('hidden');
            if (tuFields) tuFields.classList.remove('hidden');
            if (mapelInput) mapelInput.required = false;
            if (tuDutyInput) tuDutyInput.required = true;
        }
    }

    function setTuDutyPreset(dutyName) {
        const input = document.getElementById('input_staff_tu_duty');
        if (input) input.value = dutyName;
    }

    function fillFromPtkMaster(select) {
        const option = select.options[select.selectedIndex];
        if (!option || !option.dataset.json) return;
        try {
            const ptk = JSON.parse(option.dataset.json);

            if (ptk.type === 'staf_tu') {
                document.getElementById('radio_cat_tu').checked = true;
                onStaffCategoryChange('staf_tu');
                document.getElementById('input_staff_tu_duty').value = ptk.duty || '';
            } else {
                document.getElementById('radio_cat_guru').checked = true;
                onStaffCategoryChange('guru');
                document.getElementById('input_staff_mapel').value = ptk.mapel || '';
                document.getElementById('input_staff_duty').value = ptk.duty || '';
            }

            document.getElementById('input_staff_name').value = ptk.name || '';
            document.getElementById('input_staff_nip').value = ptk.nip || '-';
            document.getElementById('input_staff_gender').value = ptk.gender || 'L';
            document.getElementById('input_staff_employment_status').value = ptk.employment_status || (ptk.type === 'guru' ? 'PNS / ASN' : 'Staf TU');
            document.getElementById('input_staff_phone').value = ptk.phone || '';

            const preview = document.getElementById('staff_preview_img');
            if (preview && ptk.photo) {
                preview.src = ptk.photo.startsWith('http') ? ptk.photo : ('/' + ptk.photo.replace(/^\/+/, ''));
            }
        } catch (e) {
            console.error('Error parsing PTK master data:', e);
        }
    }

    function editStaff(member) {
        toggleStaffCompose(true);
        const form = document.getElementById('staff-form');
        const heading = document.getElementById('staff-form-heading');
        const methodContainer = document.getElementById('staff-form-method-container');
        if (!form) return;

        form.action = "/master/website-content/staff/" + member.id;
        if (methodContainer) {
            methodContainer.innerHTML = '@method("PUT")';
        }
        if (heading) {
            heading.innerText = 'Edit Pegawai: ' + member.name;
        }

        const isTu = member.category === 'staf_tu';
        if (isTu) {
            document.getElementById('radio_cat_tu').checked = true;
            onStaffCategoryChange('staf_tu');
        } else {
            document.getElementById('radio_cat_guru').checked = true;
            onStaffCategoryChange('guru');
        }

        document.getElementById('input_staff_name').value = member.name || '';
        document.getElementById('input_staff_nip').value = member.nip || '';
        document.getElementById('input_staff_gender').value = member.gender || 'L';
        document.getElementById('input_staff_employment_status').value = member.employment_status || (isTu ? 'PPPK' : 'PNS / ASN');
        document.getElementById('input_staff_phone').value = member.phone || '';
        document.getElementById('input_staff_order').value = member.order || 1;
        document.getElementById('input_staff_is_active').checked = member.is_active !== false;

        document.getElementById('input_staff_mapel').value = member.mapel || '';
        document.getElementById('input_staff_duty').value = member.duty || '';
        document.getElementById('input_staff_tu_duty').value = member.tu_duty || member.duty || '';

        const preview = document.getElementById('staff_preview_img');
        if (preview && member.photo) {
            preview.src = member.photo.startsWith('http') ? member.photo : ('/' + member.photo.replace(/^\/+/, ''));
        }
    }

    let activeAdminStaffCat = 'all';

    function filterStaffTableAdmin(category, btn) {
        activeAdminStaffCat = category;
        const buttons = document.querySelectorAll('.staff-admin-filter-btn');
        buttons.forEach(b => {
            b.className = 'staff-admin-filter-btn px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer';
        });
        if (btn) {
            btn.className = 'staff-admin-filter-btn px-3.5 py-1.5 rounded-xl bg-indigo-600 text-white shadow-xs transition cursor-pointer';
        }
        filterStaffTableAdminLive();
    }

    function filterStaffTableAdminLive() {
        const query = (document.getElementById('staff-admin-search-input')?.value || '').trim().toLowerCase();
        const rows = document.querySelectorAll('.staff-row');

        rows.forEach(row => {
            const rowCat = row.getAttribute('data-category') || '';
            const rowSearch = row.getAttribute('data-search') || '';

            const matchesCat = (activeAdminStaffCat === 'all') || (rowCat === activeAdminStaffCat);
            const matchesQuery = !query || rowSearch.includes(query);

            if (matchesCat && matchesQuery) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    // Dynamic Image Preview on File Input Change
    document.addEventListener('DOMContentLoaded', function () {
        const fileInputNews = document.getElementById('input_news_photo');
        const previewImgNews = document.getElementById('news_preview_img');
        if (fileInputNews && previewImgNews) {
            fileInputNews.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImgNews.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        const fileInputStaff = document.getElementById('input_staff_photo');
        const previewImgStaff = document.getElementById('staff_preview_img');
        if (fileInputStaff && previewImgStaff) {
            fileInputStaff.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImgStaff.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endsection
