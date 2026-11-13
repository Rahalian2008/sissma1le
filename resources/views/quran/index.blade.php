@extends('layouts.app')

@section('title', 'Al-Qur\'an Digital 30 Juz & Terjemahan Lengkap')

@push('styles')
<style>
    .font-arabic {
        font-family: 'Amiri', 'Traditional Arabic', 'Scheherazade New', 'Noto Naskh Arabic', serif;
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="quranIndexApp()">

    <!-- HERO HEADER: ELEGAN, ISLAMI & MODERN -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-900 via-teal-950 to-slate-950 p-6 sm:p-8 text-white border border-emerald-500/30 shadow-2xl">
        <!-- Ambient decorative lights & Islamic pattern aura -->
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-80 h-80 rounded-full bg-teal-500/15 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 shadow-xs">
                        📖 MUSHAF DIGITAL 30 JUZ
                    </span>
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-500/20 text-amber-300 border border-amber-400/30">
                        TERJEMAHAN KEMENAG RI
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-slate-300">
                        Semua Role
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-white leading-tight">
                    Al-Qur'an Digital & Terjemahan
                </h1>

                <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
                    Mushaf Al-Qur'an lengkap 114 Surat beserta terjemahan Bahasa Indonesia, transliterasi Latin, audio murottal merdu, tafsir ringkas, dan penanda terakhir baca.
                </p>

                <!-- Ayat Mutiara Al-Qur'an -->
                <div class="pt-2 flex items-start gap-2.5 text-xs text-emerald-200/80 italic border-t border-white/10">
                    <span class="text-lg leading-none">✨</span>
                    <span>"Kitab (Al-Qur'an) ini tidak ada keraguan padanya; petunjuk bagi mereka yang bertakwa." (QS. Al-Baqarah: 2)</span>
                </div>
            </div>

            <!-- Ringkasan Statistik Al-Qur'an -->
            <div class="grid grid-cols-3 gap-2.5 sm:gap-3 shrink-0">
                <div class="p-3.5 sm:p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-center">
                    <div class="text-2xl sm:text-3xl font-black text-amber-300 font-mono">{{ $stats['total_surahs'] }}</div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-300 mt-0.5">Surat</div>
                </div>
                <div class="p-3.5 sm:p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-center">
                    <div class="text-2xl sm:text-3xl font-black text-emerald-300 font-mono">{{ $stats['total_juz'] }}</div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-300 mt-0.5">Juz</div>
                </div>
                <div class="p-3.5 sm:p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-center">
                    <div class="text-2xl sm:text-3xl font-black text-teal-300 font-mono">6.236</div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-300 mt-0.5">Ayat</div>
                </div>
            </div>
        </div>
    </div>

    <!-- WIDGET TERAKHIR DIBACA (BOOKMARK OTOMATIS LOCALSTORAGE) -->
    <div x-show="lastRead && lastRead.surahNumber" x-cloak class="p-4 sm:p-5 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-amber-500/10 via-emerald-500/10 to-teal-500/10 border border-amber-500/30 dark:border-amber-400/20 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-2xl shrink-0">
                🔖
            </div>
            <div>
                <div class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">TERAKHIR DIBACA</div>
                <div class="text-sm sm:text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span x-text="'Surat ' + lastRead.surahName"></span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-normal" x-text="'(Ayat ke-' + lastRead.ayahNumber + ')'"></span>
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400" x-text="'Tersimpan otomatis pada ' + lastRead.savedAt"></div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a :href="'{{ url('/quran/surah') }}/' + lastRead.surahNumber + '#ayah-' + lastRead.ayahNumber" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-md shadow-amber-500/20 transition flex items-center gap-1.5 cursor-pointer">
                <span>Lanjutkan Membaca</span>
                <span>➔</span>
            </a>
            <button type="button" @click="clearLastRead()" class="p-2 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition text-xs cursor-pointer" title="Hapus Penanda">
                ✕
            </button>
        </div>
    </div>

    <!-- PENCARIAN & FILTER KATEGORI SURAT -->
    <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <!-- Search Input -->
            <div class="relative flex-1">
                <input type="text" x-model="search" placeholder="Cari nama surat (misal: Yasin, Al-Mulk, Al-Kahf), nomor, atau arti..." 
                       class="w-full px-4 py-2.5 pl-10 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl outline-none focus:border-emerald-500 text-slate-900 dark:text-white transition">
                <span class="absolute left-3.5 top-3 text-slate-400 text-sm">🔍</span>
                <button type="button" x-show="search" @click="search = ''" class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Filter Dropdown Juz -->
            <div class="flex items-center gap-2 shrink-0">
                <select x-model="selectedJuz" class="px-3.5 py-2.5 text-xs font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl outline-none focus:border-emerald-500 text-slate-700 dark:text-slate-300 cursor-pointer">
                    <option value="">-- Pilih Berdasarkan Juz (1 - 30) --</option>
                    @for($i = 1; $i <= 30; $i++)
                        <option value="{{ $i }}">Juz {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Filter Category Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-thin text-xs">
            <button type="button" @click="category = 'all'; selectedJuz = ''" 
                    :class="(category === 'all' && !selectedJuz) ? 'bg-emerald-600 text-white shadow-xs font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700'" 
                    class="px-3.5 py-2 rounded-xl transition cursor-pointer whitespace-nowrap">
                Semua Surat (114)
            </button>
            <button type="button" @click="category = 'juz30'; selectedJuz = ''" 
                    :class="category === 'juz30' ? 'bg-emerald-600 text-white shadow-xs font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700'" 
                    class="px-3.5 py-2 rounded-xl transition cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                <span>Juz 30 (Juz 'Amma)</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-black/10 dark:bg-white/10">37</span>
            </button>
            <button type="button" @click="category = 'featured'; selectedJuz = ''" 
                    :class="category === 'featured' ? 'bg-emerald-600 text-white shadow-xs font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700'" 
                    class="px-3.5 py-2 rounded-xl transition cursor-pointer whitespace-nowrap">
                ⭐ Surat Pilihan & Populer
            </button>
            <button type="button" @click="category = '3qul'; selectedJuz = ''" 
                    :class="category === '3qul' ? 'bg-emerald-600 text-white shadow-xs font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700'" 
                    class="px-3.5 py-2 rounded-xl transition cursor-pointer whitespace-nowrap">
                3 Qul
            </button>
            <button type="button" @click="category = 'makkiyah'; selectedJuz = ''" 
                    :class="category === 'makkiyah' ? 'bg-emerald-600 text-white shadow-xs font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700'" 
                    class="px-3.5 py-2 rounded-xl transition cursor-pointer whitespace-nowrap">
                Makkiyah (86)
            </button>
            <button type="button" @click="category = 'madaniyah'; selectedJuz = ''" 
                    :class="category === 'madaniyah' ? 'bg-emerald-600 text-white shadow-xs font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700'" 
                    class="px-3.5 py-2 rounded-xl transition cursor-pointer whitespace-nowrap">
                Madaniyah (28)
            </button>
        </div>
    </div>

    <!-- GRID 114 SURAT AL-QUR'AN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($allSurahs as $surah)
            <div x-show="matchesFilter({{ json_encode($surah) }})" 
                 class="group p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-emerald-500/50 dark:hover:border-emerald-500/40 shadow-xs hover:shadow-xl transition-all duration-200 flex flex-col justify-between relative overflow-hidden">
                
                <!-- Ambient Accent Hover Glow -->
                <div class="absolute -right-12 -top-12 w-28 h-28 bg-emerald-500/5 group-hover:bg-emerald-500/10 rounded-full blur-xl pointer-events-none transition"></div>

                <div>
                    <!-- Header Kartu: Nomor, Nama Latin, Kaligrafi Arab -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <!-- Nomor Surat Badge -->
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 font-black text-sm flex flex-col items-center justify-center shrink-0 group-hover:scale-105 group-hover:bg-emerald-600 group-hover:text-white transition leading-none py-1">
                                <span class="font-bold">{{ $surah['number'] }}</span>
                                <span class="font-arabic text-[11px] opacity-80 pt-0.5">{{ $surah['number_arabic'] ?? App\Services\QuranDataService::toArabicDigits($surah['number']) }}</span>
                            </div>

                            <div>
                                <h3 class="font-black text-sm sm:text-base text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition leading-snug">
                                    {{ $surah['name'] }}
                                </h3>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                    {{ $surah['translation'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Kaligrafi Teks Arab -->
                        <div class="text-right shrink-0">
                            <span class="font-arabic text-xl sm:text-2xl font-bold text-emerald-800 dark:text-emerald-400 block" dir="rtl">
                                {{ $surah['arabic'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Metadata Bar: Tempat Turun, Jumlah Ayat, Juz -->
                    <div class="flex items-center gap-2 flex-wrap text-[10px] text-slate-500 dark:text-slate-400 mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-semibold">
                            {{ $surah['type'] }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-semibold">
                            {{ $surah['ayah_count'] }} Ayat <span class="font-arabic font-bold text-emerald-600 dark:text-emerald-400">({{ $surah['ayah_count_arabic'] ?? App\Services\QuranDataService::toArabicDigits($surah['ayah_count']) }})</span>
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 font-bold">
                            Juz {{ $surah['juz'] }} <span class="font-arabic font-bold">({{ App\Services\QuranDataService::toArabicDigits($surah['juz']) }})</span>
                        </span>
                    </div>
                </div>

                <!-- Footer Kartu: Tombol Baca & Audio -->
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <a href="{{ route('quran.show', $surah['number']) }}" class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>📖 Baca Surat</span>
                    </a>
                    <button type="button" @click="playSurahAudio('{{ $surah['audio_url'] }}', '{{ addslashes($surah['name']) }}')" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition cursor-pointer" title="Dengarkan Murottal Penuh">
                        <span>▶️</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- AUDIO PLAYER FLOATING BOTTOM BAR (Jika Memutar Murottal dari Index) -->
    <div x-show="audioPlaying" x-cloak class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:w-96 z-50 p-4 rounded-3xl bg-slate-900/95 backdrop-blur-xl text-white border border-emerald-500/40 shadow-2xl space-y-2">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 truncate">
                <span class="text-xl animate-pulse">🔊</span>
                <div class="truncate">
                    <div class="text-[9.5px] uppercase font-bold text-emerald-400 tracking-wider">MUROTTAL AUDIO (ALAFASY)</div>
                    <div class="text-xs font-black truncate" x-text="audioTitle"></div>
                </div>
            </div>
            <div class="flex items-center gap-1 shrink-0">
                <button type="button" @click="toggleAudioPlay()" class="p-1.5 rounded-lg bg-emerald-500 text-slate-950 font-bold text-xs cursor-pointer">
                    <span x-text="audioPaused ? '▶' : '⏸'"></span>
                </button>
                <button type="button" @click="stopAudio()" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs cursor-pointer">
                    ✕
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function quranIndexApp() {
        return {
            search: '',
            category: 'all',
            selectedJuz: '',
            lastRead: null,
            audioPlaying: false,
            audioPaused: false,
            audioTitle: '',
            activeAudio: null,

            init() {
                this.loadLastRead();
            },

            loadLastRead() {
                try {
                    const saved = localStorage.getItem('sissma1le_quran_last_read');
                    if (saved) {
                        this.lastRead = JSON.parse(saved);
                    }
                } catch (e) {
                    console.warn('LocalStorage error:', e);
                }
            },

            clearLastRead() {
                localStorage.removeItem('sissma1le_quran_last_read');
                this.lastRead = null;
            },

            matchesFilter(surah) {
                // Filter Juz
                if (this.selectedJuz && String(surah.juz) !== String(this.selectedJuz)) {
                    return false;
                }

                // Filter Kategori
                if (this.category === 'juz30' && (surah.number < 78 || surah.number > 114)) {
                    return false;
                }
                if (this.category === 'featured' && ![1, 18, 32, 36, 55, 56, 67, 76, 78, 93, 112, 113, 114].includes(surah.number)) {
                    return false;
                }
                if (this.category === '3qul' && ![112, 113, 114].includes(surah.number)) {
                    return false;
                }
                if (this.category === 'makkiyah' && surah.type !== 'Makkiyah') {
                    return false;
                }
                if (this.category === 'madaniyah' && surah.type !== 'Madaniyah') {
                    return false;
                }

                // Filter Pencarian
                if (this.search) {
                    const q = this.search.trim().toLowerCase();
                    const matchName = surah.name.toLowerCase().includes(q);
                    const matchNumber = String(surah.number).includes(q);
                    const matchTrans = surah.translation && surah.translation.toLowerCase().includes(q);
                    const matchArabic = surah.arabic && surah.arabic.includes(q);
                    return matchName || matchNumber || matchTrans || matchArabic;
                }

                return true;
            },

            playSurahAudio(url, name) {
                if (this.activeAudio) {
                    this.activeAudio.pause();
                }
                this.activeAudio = new Audio(url);
                this.audioTitle = 'Surat ' + name;
                this.audioPlaying = true;
                this.audioPaused = false;
                this.activeAudio.play().catch(e => console.warn('Audio playback error:', e));
                this.activeAudio.onended = () => {
                    this.audioPlaying = false;
                };
            },

            toggleAudioPlay() {
                if (!this.activeAudio) return;
                if (this.activeAudio.paused) {
                    this.activeAudio.play();
                    this.audioPaused = false;
                } else {
                    this.activeAudio.pause();
                    this.audioPaused = true;
                }
            },

            stopAudio() {
                if (this.activeAudio) {
                    this.activeAudio.pause();
                    this.activeAudio = null;
                }
                this.audioPlaying = false;
                this.audioPaused = false;
            }
        };
    }
</script>
@endsection

