@extends('layouts.app')

@section('title', "Surat {$surah['name']} ({$surah['translation']}) - Al-Qur'an Digital")

@push('styles')
<style>
    .font-arabic {
        font-family: 'Amiri', 'Traditional Arabic', 'Scheherazade New', 'Noto Naskh Arabic', serif;
        line-height: 2.2;
    }
    .ayah-highlight {
        animation: highlightPulse 2s ease-in-out;
    }
    @keyframes highlightPulse {
        0%, 100% { background-color: transparent; }
        50% { background-color: rgba(16, 185, 129, 0.2); }
    }
</style>
@endpush

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="quranReaderApp()">

    <!-- TOP NAV BAR: KEMBALI, PILIH SURAT & NAVIGASI -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-2">
            <a href="{{ route('quran.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>⬅</span>
                <span class="hidden sm:inline">Daftar Surat</span>
            </a>

            <!-- Dropdown Pindah Surat Cepat -->
            <select @change="window.location.href = '{{ url('/quran/surah') }}/' + $event.target.value" class="px-3 py-2 text-xs font-bold bg-slate-100 dark:bg-slate-800 border-none rounded-xl outline-none text-slate-900 dark:text-white cursor-pointer max-w-[220px]">
                @foreach($allSurahs as $s)
                    <option value="{{ $s['number'] }}" {{ $s['number'] == $surah['number'] ? 'selected' : '' }}>
                        {{ $s['number'] }}. {{ $s['name'] }} ({{ $s['ayah_count'] }} ay.)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-1.5 self-end sm:self-center">
            @if(!empty($surah['prev_surah']))
                <a href="{{ route('quran.show', $surah['prev_surah']['number']) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-1" title="Surat Sebelumnya">
                    <span>⬅</span>
                    <span class="hidden md:inline">{{ $surah['prev_surah']['name'] }}</span>
                </a>
            @endif

            @if(!empty($surah['next_surah']))
                <a href="{{ route('quran.show', $surah['next_surah']['number']) }}" class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center gap-1 shadow-xs" title="Surat Berikutnya">
                    <span class="hidden md:inline">{{ $surah['next_surah']['name'] }}</span>
                    <span>➡</span>
                </a>
            @endif
        </div>
    </div>

    <!-- HERO HEADER SURAT & FULL AUDIO PLAYER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-900 via-teal-950 to-slate-950 p-6 sm:p-8 text-white border border-emerald-500/30 shadow-2xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-emerald-500/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 text-center space-y-4 max-w-2xl mx-auto">
            <!-- Badges -->
            <div class="flex items-center justify-center gap-2 flex-wrap">
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                    SURAT KE-{{ $surah['number'] }}
                </span>
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/10 text-slate-200">
                    {{ $surah['revelation'] }}
                </span>
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-500/20 text-teal-300 border border-teal-400/30">
                    {{ $surah['ayah_count'] }} AYAT
                </span>
            </div>

            <!-- Kaligrafi & Nama Surat -->
            <div class="font-arabic text-4xl sm:text-6xl font-bold text-amber-300 tracking-wide drop-shadow-md" dir="rtl">
                {{ $surah['arabic'] }}
            </div>

            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Surat {{ $surah['name'] }}
                </h1>
                <div class="text-sm text-emerald-200 font-medium mt-1">
                    Arti: <span class="font-bold text-amber-300">"{{ $surah['translation'] }}"</span>
                </div>
            </div>

            <!-- AUDIO PLAYER 1 SURAT PENUH -->
            @if(!empty($surah['audio_full']))
                <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 max-w-lg mx-auto space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <button type="button" @click="toggleFullSurahAudio()" class="w-11 h-11 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 flex items-center justify-center text-lg font-black shadow-lg transition cursor-pointer shrink-0">
                                <span x-text="isFullAudioPlaying ? '⏸' : '▶'"></span>
                            </button>
                            <div class="text-left">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-300">AUDIO MUROTTAL 1 SURAT LENGKAP</div>
                                <div class="text-xs font-black text-white truncate">Syaikh Mishary Rashid Alafasy</div>
                            </div>
                        </div>

                        <div class="text-right font-mono text-[11px] text-emerald-200">
                            <span x-text="audioCurrentFormatted">00:00</span> / <span x-text="audioDurationFormatted">--:--</span>
                        </div>
                    </div>

                    <!-- Audio Progress Bar -->
                    <div class="w-full bg-white/20 rounded-full h-2 cursor-pointer relative" @click="seekFullAudio($event)">
                        <div class="bg-amber-400 h-2 rounded-full transition-all" :style="'width: ' + audioProgressPercent + '%'"></div>
                    </div>
                </div>
            @endif

            <!-- Deskripsi / Asbabun Nuzul Toggle -->
            @if(!empty($surah['description']))
                <div class="pt-2">
                    <button type="button" @click="showDescription = !showDescription" class="text-xs font-bold text-emerald-300 hover:text-white transition flex items-center gap-1.5 mx-auto cursor-pointer">
                        <span x-text="showDescription ? '▼ Sembunyikan Deskripsi & Sejarah Surat' : '▶ Baca Penjelasan & Sejarah Surat'"></span>
                    </button>
                    <div x-show="showDescription" x-transition class="mt-3 p-4 rounded-2xl bg-slate-950/70 border border-white/10 text-left text-xs text-slate-200 leading-relaxed max-w-2xl mx-auto">
                        {{ $surah['description'] }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- STICKY TOOLBAR PENGATURAN BACA (UKURAN FONT, TOGGLE LATIN, TOGGLE ARTI, LOMPAT AYAT) -->
    <div class="sticky top-20 z-30 p-3 sm:p-4 rounded-2xl sm:rounded-3xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Pengatur Ukuran Font Teks Arab -->
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1">Teks Arab:</span>
            <button type="button" @click="fontSize = 'text-2xl'" :class="fontSize === 'text-2xl' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer">
                Kecil
            </button>
            <button type="button" @click="fontSize = 'text-3xl'" :class="fontSize === 'text-3xl' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer">
                Normal
            </button>
            <button type="button" @click="fontSize = 'text-4xl'" :class="fontSize === 'text-4xl' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer">
                Besar
            </button>
            <button type="button" @click="fontSize = 'text-5xl'" :class="fontSize === 'text-5xl' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer">
                Sangat Besar
            </button>
        </div>

        <!-- Toggles: Latin & Terjemah -->
        <div class="flex items-center gap-2 flex-wrap text-xs">
            <!-- Mode Baca Switcher -->
            <div class="flex items-center gap-1 p-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-[11px]">
                <button type="button" @click="showLatin = true; showTranslation = true" :class="(showLatin && showTranslation) ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 font-black shadow-xs' : 'text-slate-500'" class="px-2 py-1 rounded-lg transition cursor-pointer">
                    Lengkap
                </button>
                <button type="button" @click="showLatin = false; showTranslation = false" :class="(!showLatin && !showTranslation) ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 font-black shadow-xs' : 'text-slate-500'" class="px-2 py-1 rounded-lg transition cursor-pointer">
                    Mushaf (Arab)
                </button>
            </div>

            <label class="flex items-center gap-1.5 cursor-pointer select-none text-slate-700 dark:text-slate-300">
                <input type="checkbox" x-model="showLatin" class="rounded text-emerald-600 focus:ring-0">
                <span>Latin</span>
            </label>

            <label class="flex items-center gap-1.5 cursor-pointer select-none text-slate-700 dark:text-slate-300">
                <input type="checkbox" x-model="showTranslation" class="rounded text-emerald-600 focus:ring-0">
                <span>Terjemah</span>
            </label>

            <!-- Lompat ke Ayat -->
            <select @change="scrollToAyah($event.target.value)" class="px-2.5 py-1 text-xs font-bold bg-slate-100 dark:bg-slate-800 border-none rounded-xl outline-none text-slate-700 dark:text-slate-300 cursor-pointer">
                <option value="">Lompat Ayat</option>
                @for($a = 1; $a <= $surah['ayah_count']; $a++)
                    <option value="{{ $a }}">Ayat {{ $a }}</option>
                @endfor
            </select>
        </div>
    </div>

    <!-- BISMILLAH CALLIGRAPHY (KECUALI SURAT AT-TAUBAH NO 9) -->
    @if($surah['number'] != 9 && $surah['number'] != 1)
        <div class="py-6 sm:py-8 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="font-arabic text-3xl sm:text-5xl font-bold text-emerald-800 dark:text-emerald-400" dir="rtl">
                بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium italic">
                Dengan nama Allah Yang Maha Pengasih lagi Maha Penyayang
            </div>
        </div>
    @endif

    <!-- DAFTAR AYAT-AYAT SURAH -->
    <div class="space-y-4">
        @forelse($surah['verses'] as $verse)
            <div id="ayah-{{ $verse['number'] }}" 
                 class="p-5 sm:p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4 transition-all duration-200 hover:border-emerald-500/40 relative"
                 :class="{ 'ring-2 ring-emerald-500 bg-emerald-50/20 dark:bg-emerald-950/20': currentAyahPlaying === {{ $verse['number'] }} }">

                <!-- Header Ayat: Nomor Ayat & Tombol Aksi -->
                <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <!-- Nomor Ayat Badge -->
                        <span class="w-8 h-8 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 font-mono font-black text-xs flex items-center justify-center">
                            {{ $verse['number'] }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">
                            Ayat {{ $verse['number'] }}
                        </span>
                    </div>

                    <!-- Tombol Aksi Ayat: Audio, Bookmark, Salin, Tafsir -->
                    <div class="flex items-center gap-1">
                        @if(!empty($verse['audio']))
                            <button type="button" 
                                    @click="playAyahAudio('{{ $verse['audio'] }}', {{ $verse['number'] }})" 
                                    class="p-2 rounded-xl text-xs font-bold transition flex items-center gap-1 cursor-pointer"
                                    :class="currentAyahPlaying === {{ $verse['number'] }} ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                    title="Dengarkan Audio Ayat">
                                <span x-text="currentAyahPlaying === {{ $verse['number'] }} ? '⏸' : '▶️'"></span>
                            </button>
                        @endif

                        <!-- Bookmark Button -->
                        <button type="button" 
                                @click="saveBookmark({{ $surah['number'] }}, '{{ addslashes($surah['name']) }}', {{ $verse['number'] }})" 
                                class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition text-xs cursor-pointer"
                                title="Tandai Terakhir Dibaca">
                            <span>🔖</span>
                        </button>

                        <!-- Copy Button -->
                        <button type="button" 
                                @click="copyAyahText('{{ addslashes($verse['arabic']) }}', '{{ addslashes($verse['latin']) }}', '{{ addslashes($verse['translation']) }}', {{ $verse['number'] }})" 
                                class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition text-xs cursor-pointer"
                                title="Salin Teks Ayat">
                            <span>📋</span>
                        </button>

                        <!-- Tafsir Button -->
                        @if(isset($tafsir[$verse['number']]))
                            <button type="button" 
                                    @click="toggleTafsir({{ $verse['number'] }})" 
                                    class="px-2.5 py-1.5 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 text-teal-700 dark:text-teal-300 border border-teal-500/20 transition text-[11px] font-bold cursor-pointer"
                                    title="Baca Tafsir Kemenag">
                                <span>💡 Tafsir</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Teks Arab Ayat (Kemenag Utsmani) -->
                <div class="py-2 text-right" dir="rtl">
                    <span class="font-arabic font-normal text-slate-900 dark:text-emerald-100 leading-loose tracking-wide inline" :class="fontSize">
                        {{ $verse['arabic'] }}
                    </span>
                    <!-- Simbol Ayat Akhir Arab -->
                    <span class="font-arabic text-amber-600 dark:text-amber-400 mr-2 text-2xl select-none inline-block">
                        ۝{{ $verse['number'] }}
                    </span>
                </div>

                <!-- Transliterasi Latin (Cara Membaca) -->
                <div x-show="showLatin" x-transition class="text-xs sm:text-sm text-teal-600 dark:text-teal-300 font-medium italic leading-relaxed pt-1">
                    {{ $verse['latin'] }}
                </div>

                <!-- Terjemahan Bahasa Indonesia -->
                <div x-show="showTranslation" x-transition class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed pt-1">
                    <span class="font-bold text-slate-400 text-xs mr-1">Artinya:</span>
                    {{ $verse['translation'] }}
                </div>

                <!-- Box Tafsir Ringkas Kemenag (Jika Diklik) -->
                @if(isset($tafsir[$verse['number']]))
                    <div x-show="openTafsir === {{ $verse['number'] }}" x-transition class="p-4 rounded-2xl bg-teal-50/50 dark:bg-teal-950/30 border border-teal-200 dark:border-teal-800 text-xs text-slate-700 dark:text-teal-100 leading-relaxed space-y-1">
                        <div class="font-bold text-teal-700 dark:text-teal-300 flex items-center justify-between">
                            <span>Tafsir Ringkas Kemenag RI (Ayat {{ $verse['number'] }}):</span>
                            <button type="button" @click="openTafsir = null" class="text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
                        </div>
                        <p class="pt-1 text-slate-600 dark:text-slate-300">
                            {{ $tafsir[$verse['number']] }}
                        </p>
                    </div>
                @endif
            </div>
        @empty
            <div class="py-12 text-center text-slate-400 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                <div class="text-3xl mb-2">📖</div>
                <div class="text-sm font-bold">Sedang memuat data ayat...</div>
                <p class="text-xs text-slate-500 mt-1">Pastikan koneksi internet aktif untuk sinkronisasi pertama kali.</p>
            </div>
        @endforelse
    </div>

    <!-- BOTTOM BAR: NAVIGASI KE SURAT LAIN & TOP -->
    <div class="flex items-center justify-between gap-3 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div>
            @if(!empty($surah['prev_surah']))
                <a href="{{ route('quran.show', $surah['prev_surah']['number']) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-2">
                    <span>⬅</span>
                    <span>Surat Sebelumnya ({{ $surah['prev_surah']['name'] }})</span>
                </a>
            @else
                <a href="{{ route('quran.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                    ⬅ Kembali ke Index
                </a>
            @endif
        </div>

        <button type="button" @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="p-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 font-bold text-xs transition flex items-center gap-1 cursor-pointer">
            <span>⬆</span>
            <span class="hidden sm:inline">Ke Atas</span>
        </button>

        <div>
            @if(!empty($surah['next_surah']))
                <a href="{{ route('quran.show', $surah['next_surah']['number']) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-xs">
                    <span>Surat Selanjutnya ({{ $surah['next_surah']['name'] }})</span>
                    <span>➡</span>
                </a>
            @else
                <a href="{{ route('quran.index') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition">
                    Tuntas (Ke Index) ➔
                </a>
            @endif
        </div>
    </div>
</div>

<script>
    function quranReaderApp() {
        return {
            fontSize: 'text-3xl',
            showLatin: true,
            showTranslation: true,
            showDescription: false,
            openTafsir: null,
            currentAyahPlaying: null,
            activeAyahAudio: null,
            
            // Full Surah Audio State
            fullAudioElement: null,
            isFullAudioPlaying: false,
            audioCurrentFormatted: '00:00',
            audioDurationFormatted: '--:--',
            audioProgressPercent: 0,

            init() {
                // Check if there is an anchor hash in URL (#ayah-5)
                if (window.location.hash) {
                    setTimeout(() => {
                        const target = document.querySelector(window.location.hash);
                        if (target) {
                            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            target.classList.add('ayah-highlight');
                        }
                    }, 400);
                }

                // Setup Full Surah Audio
                @if(!empty($surah['audio_full']))
                    this.fullAudioElement = new Audio('{{ $surah['audio_full'] }}');
                    this.fullAudioElement.addEventListener('timeupdate', () => {
                        if (!this.fullAudioElement) return;
                        const cur = this.fullAudioElement.currentTime;
                        const dur = this.fullAudioElement.duration || 1;
                        this.audioCurrentFormatted = this.formatTime(cur);
                        this.audioDurationFormatted = this.formatTime(dur);
                        this.audioProgressPercent = (cur / dur) * 100;
                    });
                    this.fullAudioElement.addEventListener('ended', () => {
                        this.isFullAudioPlaying = false;
                        this.audioProgressPercent = 0;
                    });
                @endif
            },

            formatTime(seconds) {
                if (isNaN(seconds)) return '00:00';
                const m = Math.floor(seconds / 60);
                const s = Math.floor(seconds % 60);
                return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            },

            toggleFullSurahAudio() {
                if (!this.fullAudioElement) return;
                this.stopAyahAudio();

                if (this.isFullAudioPlaying) {
                    this.fullAudioElement.pause();
                    this.isFullAudioPlaying = false;
                } else {
                    this.fullAudioElement.play().catch(e => console.warn('Audio play failed:', e));
                    this.isFullAudioPlaying = true;
                }
            },

            seekFullAudio(e) {
                if (!this.fullAudioElement || !this.fullAudioElement.duration) return;
                const rect = e.currentTarget.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                this.fullAudioElement.currentTime = pos * this.fullAudioElement.duration;
            },

            playAyahAudio(url, ayahNum) {
                if (this.fullAudioElement && this.isFullAudioPlaying) {
                    this.fullAudioElement.pause();
                    this.isFullAudioPlaying = false;
                }

                if (this.activeAyahAudio) {
                    this.activeAyahAudio.pause();
                    if (this.currentAyahPlaying === ayahNum) {
                        this.currentAyahPlaying = null;
                        this.activeAyahAudio = null;
                        return;
                    }
                }

                this.activeAyahAudio = new Audio(url);
                this.currentAyahPlaying = ayahNum;
                this.activeAyahAudio.play().catch(e => console.warn('Ayah audio play error:', e));

                this.activeAyahAudio.onended = () => {
                    this.currentAyahPlaying = null;
                    this.activeAyahAudio = null;
                };
            },

            stopAyahAudio() {
                if (this.activeAyahAudio) {
                    this.activeAyahAudio.pause();
                    this.activeAyahAudio = null;
                    this.currentAyahPlaying = null;
                }
            },

            scrollToAyah(num) {
                if (!num) return;
                const el = document.getElementById('ayah-' + num);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.classList.add('ayah-highlight');
                    setTimeout(() => el.classList.remove('ayah-highlight'), 2500);
                }
            },

            saveBookmark(surahNum, surahName, ayahNum) {
                const now = new Date();
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const timeStr = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} pukul ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;

                const data = {
                    surahNumber: surahNum,
                    surahName: surahName,
                    ayahNumber: ayahNum,
                    savedAt: timeStr
                };

                try {
                    localStorage.setItem('sissma1le_quran_last_read', JSON.stringify(data));
                    alert(`✅ Berhasil Menandai Bookmark!\n\nSurat: ${surahName}\nAyat: ${ayahNum}\n\nAnda dapat melanjutkan membaca kapan saja dari halaman utama Al-Qur'an Digital.`);
                } catch (e) {
                    alert('Gagal menyimpan bookmark ke penyimpanan browser.');
                }
            },

            copyAyahText(arabic, latin, trans, num) {
                const text = `Surat {{ $surah['name'] }} Ayat ${num}\n\n${arabic}\n\nLatin: ${latin}\n\nArtinya:\n"${trans}"\n\n(Al-Qur'an Digital SMAN 1 Lengkong)`;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(() => {
                        alert('📋 Teks Ayat ' + num + ' berhasil disalin ke clipboard!');
                    }).catch(() => {
                        this.fallbackCopy(text);
                    });
                } else {
                    this.fallbackCopy(text);
                }
            },

            fallbackCopy(text) {
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                alert('📋 Teks ayat berhasil disalin!');
            },

            toggleTafsir(ayahNum) {
                if (this.openTafsir === ayahNum) {
                    this.openTafsir = null;
                } else {
                    this.openTafsir = ayahNum;
                }
            }
        };
    }
</script>
@endsection
