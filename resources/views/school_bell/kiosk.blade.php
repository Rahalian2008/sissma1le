<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kios Bel Sekolah Terpadu - SMAN 1 Lengkong</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Fonts & Tailwind CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.8; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.02); }
        }
        .animate-pulse-glow {
            animation: pulse-glow 3s infinite ease-in-out;
        }
    </style>
</head>
<body class="h-full text-slate-100 bg-slate-950 antialiased selection:bg-amber-500 selection:text-slate-950 overflow-x-hidden" x-data="bellKioskApp()">

    <!-- KIOSK CONTAINER -->
    <div class="min-h-screen flex flex-col justify-between p-4 sm:p-6 lg:p-8 relative overflow-hidden bg-radial from-indigo-950/60 via-slate-950 to-slate-950">
        
        <!-- Background Ambient Lights -->
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- TOP BAR: Brand, Standby Status, Quick Action -->
        <header class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4 pb-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center text-slate-950 font-black text-xl shadow-lg shadow-amber-500/20 shrink-0">
                    🔔
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-black text-white tracking-tight">KIOS BEL SEKOLAH TERPADU</h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-400/30">
                            STANDALONE DEVICE
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">SMAN 1 Lengkong • Output Audio Aktif Otomatis Terhubung ke Sound System</p>
                </div>
            </div>

            <!-- Header Controls: Audio Readiness, Fullscreen, Back to Admin -->
            <div class="flex items-center gap-2.5 flex-wrap justify-center">
                <!-- Status Audio Aktif Indicator -->
                <div class="px-3.5 py-1.5 rounded-xl border flex items-center gap-2 text-xs font-bold"
                     :class="isAudioUnlocked ? 'bg-emerald-950/60 border-emerald-500/40 text-emerald-300' : 'bg-rose-950/60 border-rose-500/40 text-rose-300 animate-pulse'">
                    <span class="w-2.5 h-2.5 rounded-full" :class="isAudioUnlocked ? 'bg-emerald-400 shadow-sm shadow-emerald-400' : 'bg-rose-500'"></span>
                    <span x-text="isAudioUnlocked ? 'Sistem Suara: SIAGA AKTIF' : 'Audio Terkunci: Klik Tombol di Bawah'"></span>
                </div>

                <!-- Fullscreen Toggle -->
                <button type="button" @click="toggleFullscreen()" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 text-xs font-semibold border border-white/10 transition flex items-center gap-1.5 cursor-pointer">
                    <span x-text="isFullscreen ? '🗗 Keluar Layar Penuh' : '🗖 Layar Penuh (F11)'"></span>
                </button>

                <!-- Link Kembali ke Dashboard Bel -->
                <a href="{{ route('school_bell.index') }}" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 text-xs font-semibold border border-white/10 transition flex items-center gap-1.5">
                    <span>⚙️ Kelola Jadwal</span>
                </a>
            </div>
        </header>

        <!-- MAIN KIOSK HERO: BIG DIGITAL CLOCK & NEXT BELL HIGHLIGHT -->
        <main class="relative z-10 my-auto py-6 sm:py-8 space-y-6">

            <!-- BANNER NOTIFIKASI AKTIFKAN AUDIO UNLOCK (Browser Policy Safeguard) -->
            <div x-show="!isAudioUnlocked" class="p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 text-slate-950 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4 animate-bounce">
                <div class="flex items-center gap-3.5 text-center md:text-left">
                    <span class="text-3xl">🔊</span>
                    <div>
                        <div class="text-sm sm:text-base font-black tracking-tight">KLIK UNTUK MENGAKTIFKAN OUTPUT SUARA BEL DI DEVICE INI</div>
                        <div class="text-xs font-semibold text-slate-900/90">Browser mewajibkan 1 klik interaksi pertama agar bel dan lagu dapat diputar secara otomatis tanpa halangan.</div>
                    </div>
                </div>
                <button type="button" @click="unlockAudioEngine()" class="px-6 py-3 rounded-2xl bg-slate-950 hover:bg-slate-900 text-amber-400 font-black text-sm shadow-xl transition cursor-pointer shrink-0 border border-amber-300/40">
                    ▶️ AKTIFKAN SUARA BEL SEKARANG
                </button>
            </div>

            <!-- LIVE RINGING POPUP BANNER (Tampil Saat Bel Sedang Berbunyi) -->
            <div x-show="isPlaying" x-cloak class="p-5 sm:p-6 rounded-3xl bg-gradient-to-r from-rose-600 via-amber-600 to-emerald-600 text-white shadow-2xl border-2 border-white/30 flex flex-col sm:flex-row items-center justify-between gap-4 animate-pulse-glow">
                <div class="flex items-center gap-4 text-center sm:text-left">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl shrink-0 animate-spin">
                        🔔
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-widest font-black text-amber-200">SEDANG BERBUNYI DI PENGANTAR SUARA</div>
                        <div class="text-xl sm:text-2xl font-black tracking-tight text-white mt-0.5" x-text="currentAudioTitle"></div>
                        <div class="text-xs text-slate-100 font-medium mt-0.5" x-text="'Tipe: ' + currentAudioType + (ringingRemainingSeconds > 0 ? ' • Durasi sisa: ' + ringingRemainingSeconds + ' detik' : ' • Streaming Audio Live')"></div>
                    </div>
                </div>
                <button type="button" @click="stopAllAudio()" class="px-5 py-2.5 rounded-xl bg-slate-950 text-white hover:bg-slate-900 font-black text-xs transition cursor-pointer border border-white/20 shrink-0">
                    ⏹️ Hentikan Bunyi Sekarang
                </button>
            </div>

            <!-- HERO GRID: CLOCK WIDGET & NEXT BELL COUNTDOWN -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- JAM DIGITAL BESAR (WIB) -->
                <div class="lg:col-span-7 bg-white/5 backdrop-blur-xl rounded-3xl p-6 sm:p-8 border border-white/10 flex flex-col justify-center items-center text-center shadow-2xl relative overflow-hidden">
                    <div class="text-xs sm:text-sm font-bold uppercase tracking-widest text-amber-400 mb-2">
                        WAKTU RESMI SEKOLAH (WIB)
                    </div>
                    
                    <!-- Jam HH:MM:SS Super Besar -->
                    <div class="text-5xl sm:text-7xl xl:text-8xl font-black font-mono tracking-tight text-white my-1 tabular-nums drop-shadow-md flex items-center justify-center">
                        <span x-text="timeHours">00</span>
                        <span class="animate-pulse text-amber-400">:</span>
                        <span x-text="timeMinutes">00</span>
                        <span class="animate-pulse text-amber-400">:</span>
                        <span x-text="timeSeconds" class="text-cyan-300">00</span>
                    </div>

                    <!-- Hari & Tanggal Lengkap -->
                    <div class="text-sm sm:text-lg font-bold text-slate-300 mt-2" x-text="fullDateDisplay">
                        {{ $todayName }}, {{ date('d F Y') }}
                    </div>

                    <!-- Audio Volume Bar -->
                    <div class="mt-6 pt-5 border-t border-white/10 w-full flex items-center justify-center gap-4 text-xs">
                        <span class="text-slate-400">Volume Sound:</span>
                        <input type="range" min="10" max="100" x-model="volumePercent" @input="updateVolume()" class="w-36 accent-amber-400 cursor-pointer">
                        <span class="font-mono font-bold text-amber-400" x-text="volumePercent + '%'"></span>
                    </div>
                </div>

                <!-- COUNTDOWN & INFORMASI BEL BERIKUTNYA -->
                <div class="lg:col-span-5 bg-gradient-to-br from-amber-500/10 via-indigo-950/40 to-slate-900/80 backdrop-blur-xl rounded-3xl p-6 sm:p-8 border border-amber-500/30 flex flex-col justify-between shadow-2xl">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-400/30">
                                BEL BERIKUTNYA
                            </span>
                            <span class="text-xs font-mono font-bold text-cyan-300" x-text="nextBellTime ? nextBellTime + ' WIB' : '--:-- WIB'"></span>
                        </div>

                        <div class="text-2xl sm:text-3xl font-black text-white mt-1 leading-tight" x-text="nextBellTitle || 'Semua Jadwal Selesai Hari Ini'"></div>
                        <div class="text-xs text-slate-300 mt-1 leading-relaxed" x-text="nextBellDesc || 'Tidak ada jadwal bel aktif yang tersisa untuk hari ini.'"></div>
                    </div>

                    <!-- Countdown Box -->
                    <div class="mt-6 p-4 rounded-2xl bg-slate-950/70 border border-white/10 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] uppercase font-bold tracking-widest text-slate-400">HITUNG MUNDUR</div>
                            <div class="text-xl sm:text-2xl font-black font-mono text-amber-400 mt-0.5" x-text="countdownText">
                                Memuat...
                            </div>
                        </div>
                        <div class="text-3xl">⏱️</div>
                    </div>

                    <!-- Tombol Uji Bunyi Cepat -->
                    <div class="mt-5 pt-4 border-t border-white/10 flex items-center gap-2 flex-wrap">
                        <span class="text-[11px] text-slate-400 font-semibold mr-1">Uji Suara:</span>
                        <button type="button" @click="testSound('chime')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-[11px] font-bold text-white transition cursor-pointer">
                            🔔 Nada Chime
                        </button>
                        <button type="button" @click="testSound('westminster')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-[11px] font-bold text-amber-300 transition cursor-pointer">
                            🏛️ Westminster
                        </button>
                        <button type="button" @click="testSpeech('Uji coba sistem pengeras suara bel SMA Negeri 1 Lengkong berhasil.')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-[11px] font-bold text-cyan-300 transition cursor-pointer">
                            🗣️ Ucapan
                        </button>
                    </div>
                </div>
            </div>

            <!-- JADWAL LENGKAP BEL HARI INI -->
            <div class="bg-white/5 backdrop-blur-xl rounded-3xl p-6 sm:p-7 border border-white/10 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-white/10 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="text-lg">📋</span>
                        <h2 class="text-base font-bold text-white">Jadwal Bel Aktif Hari Ini ({{ $todayName }})</h2>
                    </div>
                    <span class="text-xs text-slate-400 font-mono">{{ $todayBells->count() }} Jadwal Terdaftar</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    @forelse($todayBells as $bell)
                        <div class="p-3.5 rounded-2xl border transition relative overflow-hidden"
                             :class="{
                                 'bg-amber-500/20 border-amber-400/60 shadow-lg ring-1 ring-amber-400/50': isNextSchedule('{{ $bell->time }}'),
                                 'bg-slate-900/60 border-white/10 text-slate-400': isPassedSchedule('{{ $bell->time }}'),
                                 'bg-white/5 border-white/10 text-white': !isNextSchedule('{{ $bell->time }}') && !isPassedSchedule('{{ $bell->time }}')
                             }">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono font-bold text-sm"
                                      :class="isNextSchedule('{{ $bell->time }}') ? 'text-amber-300 font-black text-base' : (isPassedSchedule('{{ $bell->time }}') ? 'text-slate-500' : 'text-cyan-300')">
                                    {{ $bell->time }} WIB
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[9.5px] font-extrabold uppercase"
                                      :class="{
                                          'bg-amber-400 text-slate-950 font-black': isNextSchedule('{{ $bell->time }}'),
                                          'bg-slate-800 text-slate-400': isPassedSchedule('{{ $bell->time }}'),
                                          'bg-white/10 text-slate-300': !isNextSchedule('{{ $bell->time }}') && !isPassedSchedule('{{ $bell->time }}')
                                      }">
                                    <template x-if="isPassedSchedule('{{ $bell->time }}')"><span>✓ Selesai</span></template>
                                    <template x-if="isNextSchedule('{{ $bell->time }}')"><span>⚡ Berikutnya</span></template>
                                    <template x-if="!isNextSchedule('{{ $bell->time }}') && !isPassedSchedule('{{ $bell->time }}')"><span>Menunggu</span></template>
                                </span>
                            </div>

                            <div class="font-bold text-xs truncate" :class="isPassedSchedule('{{ $bell->time }}') ? 'text-slate-400 line-through' : 'text-white'" title="{{ $bell->title }}">
                                {{ $bell->title }}
                            </div>

                            <div class="flex items-center justify-between text-[10px] text-slate-400 mt-2 pt-2 border-t border-white/5">
                                <span class="capitalize">
                                    @if($bell->sound_type === 'murottal')
                                        @php
                                            $sCount = is_array($bell->surah_numbers) ? count($bell->surah_numbers) : ($bell->surah_number ? 1 : 0);
                                        @endphp
                                        @if($sCount > 1)
                                            📖 Murottal ({{ $sCount }} Surat)
                                        @else
                                            📖 Murottal ({{ $bell->surah_name ?? 'Surah' }})
                                        @endif
                                    @elseif($bell->sound_type === 'song')
                                        @php
                                            $aCount = is_array($bell->anthem_ids) ? count($bell->anthem_ids) : ($bell->anthem_id ? 1 : 0);
                                        @endphp
                                        @if($aCount > 1)
                                            🎶 Playlist ({{ $aCount }} Lagu)
                                        @else
                                            🎶 Lagu / Mars
                                        @endif
                                    @elseif($bell->sound_type === 'custom')
                                        🎵 Audio Kustom
                                    @elseif($bell->sound_type === 'voice')
                                        🗣️ Suara Narasi
                                    @elseif($bell->sound_type === 'siren')
                                        🚨 Sirine
                                    @else
                                        🔔 Chime Bel
                                    @endif
                                </span>
                                <button type="button" @click="previewSchedule({{ json_encode($bell) }})" class="text-amber-400 hover:text-amber-300 font-bold hover:underline cursor-pointer">
                                    ▶ Tes
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400 text-xs bg-slate-900/40 rounded-2xl border border-white/5">
                            Tidak ada jadwal bel yang aktif untuk hari {{ $todayName }}. Silakan atur di menu Kelola Jadwal Bel.
                        </div>
                    @endforelse
                </div>
            </div>
            <!-- SOUNDBOARD, LAGU NASIONAL & MUROTTAL AL-QUR'AN PANEL -->
            <div class="bg-white/5 backdrop-blur-xl rounded-3xl p-6 sm:p-7 border border-white/10 shadow-2xl space-y-6" 
                 x-data="{ 
                     kioskTab: 'soundboard',
                     murottalSearch: '',
                     murottalCategory: 'all',
                     allSurahsList: @json(array_values($allSurahs)),
                     matchesMurottal(surah) {
                         const q = this.murottalSearch.trim().toLowerCase();
                         if (this.murottalCategory === 'juz30' && (surah.number < 78 || surah.number > 114)) return false;
                         if (this.murottalCategory === 'popular' && ![1, 18, 36, 55, 56, 67, 78, 93, 112, 113, 114].includes(surah.number)) return false;
                         if (this.murottalCategory === '3qul' && ![112, 113, 114].includes(surah.number)) return false;

                         if (!q) return true;
                         return surah.name.toLowerCase().includes(q) ||
                                String(surah.number).includes(q) ||
                                (surah.translation && surah.translation.toLowerCase().includes(q)) ||
                                (surah.type && surah.type.toLowerCase().includes(q));
                     }
                 }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-white/10">
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" @click="kioskTab = 'soundboard'" :class="kioskTab === 'soundboard' ? 'bg-amber-500 text-slate-950 font-black shadow-lg shadow-amber-500/20' : 'bg-white/10 text-slate-300 hover:bg-white/20 font-bold'" class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer">
                            <span>🎛️ Soundboard Instan</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950/40 text-amber-900 font-mono font-bold">{{ count($instantSounds) }} Jenis</span>
                        </button>
                        <button type="button" @click="kioskTab = 'anthems'" :class="kioskTab === 'anthems' ? 'bg-red-600 text-white font-black shadow-lg shadow-red-600/20' : 'bg-white/10 text-slate-300 hover:bg-white/20 font-bold'" class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer">
                            <span>🇮🇩 Lagu-Lagu Nasional</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-mono font-bold">10 Lagu</span>
                        </button>
                        <button type="button" @click="kioskTab = 'murottal'" :class="kioskTab === 'murottal' ? 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/20' : 'bg-white/10 text-slate-300 hover:bg-white/20 font-bold'" class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer">
                            <span>📖 Murottal Al-Qur'an</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950/40 text-emerald-950 font-mono font-bold">114 Surah</span>
                        </button>
                    </div>

                    <div class="text-xs text-slate-400">
                        Klik tombol mana pun untuk membunyikan seketika ke sound system sekolah tanpa menunggu jadwal.
                    </div>
                </div>

                <!-- SUB-PANEL 1: 12 JENIS SOUNDBOARD INSTAN -->
                <div x-show="kioskTab === 'soundboard'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                    @foreach($instantSounds as $sound)
                        <button type="button" @click="playKioskInstantSound('{{ $sound['sound_type'] }}', '{{ addslashes($sound['name']) }}', '{{ addslashes($sound['voice_text'] ?? '') }}')" class="p-4 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-amber-400/50 transition flex flex-col justify-between text-left group cursor-pointer">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-2xl group-hover:scale-110 transition">{{ $sound['icon'] }}</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-white/10 text-amber-300">
                                        {{ $sound['badge'] }}
                                    </span>
                                </div>
                                <div class="font-extrabold text-xs text-white line-clamp-1 group-hover:text-amber-300 transition">{{ $sound['name'] }}</div>
                                <div class="text-[10px] text-slate-400 mt-1 line-clamp-2 leading-tight">{{ $sound['description'] }}</div>
                            </div>
                            <div class="mt-3 pt-2 border-t border-white/10 flex items-center justify-between text-[10px] text-amber-400 font-bold">
                                <span>▶ Bunyikan</span>
                                <span>⚡ Instan</span>
                            </div>
                        </button>
                    @endforeach
                </div>

                <!-- SUB-PANEL 2: 10 LAGU-LAGU NASIONAL INDONESIA -->
                <div x-show="kioskTab === 'anthems'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                    @foreach($nationalAnthems as $idx => $anthem)
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-2xl">🇮🇩</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-red-500/20 text-red-300 border border-red-500/30">
                                        {{ $anthem['category'] }}
                                    </span>
                                </div>
                                <div class="font-black text-xs text-white">{{ $anthem['title'] }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Cipt: {{ $anthem['composer'] }}</div>
                                <div class="text-[10px] text-amber-300/80 mt-1 font-medium">{{ $anthem['usage'] }}</div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-white/10 flex items-center justify-between gap-2">
                                <button type="button" @click="playKioskAnthem({{ json_encode($anthem) }})" class="w-full py-1.5 px-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                    <span>▶ Putar Lagu</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- SUB-PANEL 3: MUROTTAL AL-QUR'AN 114 SURAH LENGKAP -->
                <div x-show="kioskTab === 'murottal'" class="space-y-4">
                    <!-- Filter & Pencarian Cepat -->
                    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 p-3.5 rounded-2xl bg-slate-950/60 border border-white/10">
                        <div class="relative flex-1">
                            <input type="text" x-model="murottalSearch" placeholder="Cari nomor, nama surat (misal: Yasin, Al-Mulk, Ar-Rahman), atau arti..." 
                                   class="w-full px-4 py-2.5 pl-9 text-xs bg-white/5 border border-white/10 rounded-xl outline-none focus:border-emerald-400 text-white placeholder-slate-400">
                            <span class="absolute left-3 top-3 text-slate-400 text-xs">🔍</span>
                            <button type="button" x-show="murottalSearch" @click="murottalSearch = ''" class="absolute right-3 top-2.5 text-slate-400 hover:text-white text-xs cursor-pointer">✕</button>
                        </div>

                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" @click="murottalCategory = 'all'" 
                                    :class="murottalCategory === 'all' ? 'bg-emerald-500 text-slate-950 font-black' : 'bg-white/5 text-slate-300 hover:bg-white/10 font-medium'" 
                                    class="px-3 py-1.5 rounded-xl text-xs transition cursor-pointer">
                                Semua (114)
                            </button>
                            <button type="button" @click="murottalCategory = 'juz30'" 
                                    :class="murottalCategory === 'juz30' ? 'bg-emerald-500 text-slate-950 font-black' : 'bg-white/5 text-slate-300 hover:bg-white/10 font-medium'" 
                                    class="px-3 py-1.5 rounded-xl text-xs transition cursor-pointer">
                                Juz 30 (37 Surat)
                            </button>
                            <button type="button" @click="murottalCategory = 'popular'" 
                                    :class="murottalCategory === 'popular' ? 'bg-emerald-500 text-slate-950 font-black' : 'bg-white/5 text-slate-300 hover:bg-white/10 font-medium'" 
                                    class="px-3 py-1.5 rounded-xl text-xs transition cursor-pointer">
                                ⭐ Surat Utama
                            </button>
                            <button type="button" @click="murottalCategory = '3qul'" 
                                    :class="murottalCategory === '3qul' ? 'bg-emerald-500 text-slate-950 font-black' : 'bg-white/5 text-slate-300 hover:bg-white/10 font-medium'" 
                                    class="px-3 py-1.5 rounded-xl text-xs transition cursor-pointer">
                                3 Qul
                            </button>
                        </div>
                    </div>

                    <!-- Banner Status Murottal Sedang Diputar -->
                    <div x-show="isPlaying && currentPlayingSurahNumber" class="p-4 rounded-2xl bg-gradient-to-r from-emerald-950 via-teal-950 to-slate-950 border border-emerald-400/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg animate-pulse">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-xl shrink-0">
                                📖
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-emerald-300 tracking-wider">SEDANG MEMUTAR MUROTTAL SECARA MANUAL</div>
                                <div class="text-sm font-black text-white" x-text="currentAudioTitle"></div>
                                <div class="text-[11px] text-emerald-200/70">Qari: Syaikh Mishary Rashid Alafasy • Audio High Quality Streaming</div>
                            </div>
                        </div>
                        <button type="button" @click="stopAllAudio()" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
                            <span>⏹️ Hentikan Murottal</span>
                        </button>
                    </div>

                    <!-- Grid 114 Surah -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 max-h-[620px] overflow-y-auto pr-1">
                        <template x-for="surah in allSurahsList" :key="surah.number">
                            <div x-show="matchesMurottal(surah)" 
                                 class="p-4 rounded-2xl border transition flex flex-col justify-between group relative overflow-hidden"
                                 :class="currentPlayingSurahNumber === surah.number && isPlaying 
                                    ? 'bg-emerald-950/70 border-emerald-400 shadow-lg shadow-emerald-500/20 ring-1 ring-emerald-400' 
                                    : 'bg-white/5 hover:bg-white/10 border-white/10 hover:border-emerald-400/40'">
                                <div>
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-8 h-8 rounded-xl bg-white/10 border border-white/10 text-emerald-300 font-mono font-black text-xs flex items-center justify-center shrink-0" 
                                                  x-text="surah.number"></span>
                                            <div>
                                                <div class="font-extrabold text-xs text-white group-hover:text-emerald-300 transition" x-text="surah.name"></div>
                                                <div class="text-[10px] text-slate-400" x-text="surah.translation"></div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <div class="text-base font-bold text-emerald-400" x-text="surah.arabic" dir="rtl"></div>
                                            <div class="text-[9px] text-slate-400" x-text="surah.ayah_count + ' Ayat • ' + surah.type"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between gap-2">
                                    <button type="button" 
                                            @click="currentPlayingSurahNumber === surah.number && isPlaying ? stopAllAudio() : playKioskSurah(surah)" 
                                            :class="currentPlayingSurahNumber === surah.number && isPlaying 
                                                ? 'bg-rose-600 hover:bg-rose-500 text-white' 
                                                : 'bg-emerald-600 hover:bg-emerald-500 text-white group-hover:shadow-md group-hover:shadow-emerald-600/30'" 
                                            class="w-full py-1.5 px-3 rounded-xl font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <template x-if="currentPlayingSurahNumber === surah.number && isPlaying">
                                            <span class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                                <span>⏹️ Hentikan Murottal</span>
                                            </span>
                                        </template>
                                        <template x-if="!(currentPlayingSurahNumber === surah.number && isPlaying)">
                                            <span class="flex items-center gap-1.5">
                                                <span>▶️ Putar Murottal</span>
                                            </span>
                                        </template>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </main>

        <!-- FOOTER BAR -->
        <footer class="relative z-10 pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400">
            <div>
                SISSMA 1 Lengkong • Kios Bel Otomatis &bull; Biarkan layar tab ini tetap terbuka pada laptop/PC pengeras suara sekolah.
            </div>
            <div class="font-mono text-[11px] text-amber-300/80">
                Auto-Sync Interval: 1 Detik &bull; Web Audio API &bull; Mode Siaga Aktif
            </div>
        </footer>
    </div>

    <!-- AUDIO PLAYER ENGINE (Alpine.js) -->
    <script>
        function bellKioskApp() {
            return {
                currentTime: '{{ $currentTime }}:00',
                timeHours: '00',
                timeMinutes: '00',
                timeSeconds: '00',
                fullDateDisplay: '',
                isAudioUnlocked: false,
                isFullscreen: false,
                isPlaying: false,
                currentAudioTitle: '',
                currentAudioType: '',
                currentPlayingSurahNumber: null,
                ringingRemainingSeconds: 0,
                volumePercent: 100,
                audioContext: null,
                activeAudioElement: null,
                timerInterval: null,
                countdownText: 'Menghitung...',
                nextBellTitle: '{{ $nextBell?->title ?? "" }}',
                nextBellTime: '{{ $nextBell?->time ?? "" }}',
                nextBellDesc: '{{ $nextBell?->description ?? "" }}',
                schedules: @json($todayBells),

                init() {
                    this.updateClock();
                    this.timerInterval = setInterval(() => {
                        this.updateClock();
                        this.checkSchedules();
                        this.updateCountdown();
                    }, 1000);

                    // Dengarkan user gesture jika mengklik di mana pun untuk unlock audio otomatis
                    document.addEventListener('click', () => {
                        if (!this.isAudioUnlocked) {
                            this.unlockAudioEngine();
                        }
                    }, { once: true });
                },

                updateClock() {
                    const now = new Date();
                    const h = String(now.getHours()).padStart(2, '0');
                    const m = String(now.getMinutes()).padStart(2, '0');
                    const s = String(now.getSeconds()).padStart(2, '0');

                    this.timeHours = h;
                    this.timeMinutes = m;
                    this.timeSeconds = s;
                    this.currentTime = `${h}:${m}:${s}`;

                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    this.fullDateDisplay = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
                },

                unlockAudioEngine() {
                    try {
                        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                        if (AudioContextClass) {
                            if (!this.audioContext) {
                                this.audioContext = new AudioContextClass();
                            }
                            if (this.audioContext.state === 'suspended') {
                                this.audioContext.resume();
                            }
                        }
                        this.isAudioUnlocked = true;
                    } catch (e) {
                        console.warn('AudioContext init error:', e);
                        this.isAudioUnlocked = true;
                    }
                },

                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {
                            console.warn(`Error attempting to enable fullscreen: ${err.message}`);
                        });
                        this.isFullscreen = true;
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                            this.isFullscreen = false;
                        }
                    }
                },

                isPassedSchedule(timeStr) {
                    const currentHM = `${this.timeHours}:${this.timeMinutes}`;
                    return currentHM > timeStr;
                },

                isNextSchedule(timeStr) {
                    return this.nextBellTime === timeStr;
                },

                updateCountdown() {
                    const currentHM = `${this.timeHours}:${this.timeMinutes}`;
                    const upcoming = this.schedules
                        .filter(s => s.time >= currentHM)
                        .sort((a, b) => a.time.localeCompare(b.time));

                    if (upcoming.length > 0) {
                        const next = upcoming[0];
                        this.nextBellTime = next.time;
                        this.nextBellTitle = next.title;
                        this.nextBellDesc = next.description || `Jadwal bel berbunyi pukul ${next.time} WIB.`;

                        // Hitung selisih detik
                        const [nextH, nextM] = next.time.split(':').map(Number);
                        const [curH, curM, curS] = [Number(this.timeHours), Number(this.timeMinutes), Number(this.timeSeconds)];

                        const diffSeconds = ((nextH * 3600) + (nextM * 60)) - ((curH * 3600) + (curM * 60) + curS);
                        if (diffSeconds > 0) {
                            const m = Math.floor(diffSeconds / 60);
                            const s = diffSeconds % 60;
                            this.countdownText = `${m} Menit ${s} Detik Lagi`;
                        } else {
                            this.countdownText = `Saatnya Berbunyi!`;
                        }
                    } else {
                        this.nextBellTime = '';
                        this.nextBellTitle = 'Tuntas untuk Hari Ini';
                        this.nextBellDesc = 'Tidak ada jadwal bel tersisa. Sistem siap siaga untuk hari berikutnya.';
                        this.countdownText = 'Tuntas';
                    }
                },

                checkSchedules() {
                    const currentHMS = `${this.timeHours}:${this.timeMinutes}:${this.timeSeconds}`;
                    
                    // Trigger hanya pada detik :00
                    if (this.timeSeconds === '00') {
                        const currentHM = `${this.timeHours}:${this.timeMinutes}`;
                        const matchingBell = this.schedules.find(s => s.time === currentHM);

                        if (matchingBell) {
                            this.triggerBell(matchingBell);
                        }
                    }
                },

                triggerBell(bell) {
                    this.unlockAudioEngine();
                    this.isPlaying = true;
                    this.currentAudioTitle = bell.title;
                    this.currentAudioType = bell.sound_type;
                    this.ringingRemainingSeconds = bell.duration_seconds || 15;

                    const countdownTimer = setInterval(() => {
                        this.ringingRemainingSeconds--;
                        if (this.ringingRemainingSeconds <= 0) {
                            clearInterval(countdownTimer);
                        }
                    }, 1000);

                    // 1. Jika ada playlist multi-surah atau multi-lagu
                    const items = bell.playlist_items || [];
                    if (items.length > 1) {
                        this.playPlaylistQueue(items, bell.duration_seconds, bell.voice_text);
                    }
                    // 2. Jika ada audio_url tunggal (file upload lagu/custom/murottal)
                    else if (bell.audio_url) {
                        this.playAudioFile(bell.audio_url, bell.duration_seconds, bell.voice_text);
                    } 
                    // 3. Jika tipe voice narasi
                    else if (bell.sound_type === 'voice') {
                        this.playChime('chime', () => {
                            this.speakText(bell.voice_text || bell.title);
                        });
                    }
                    // 4. Jika sirine darurat
                    else if (bell.sound_type === 'siren') {
                        this.playSiren(bell.duration_seconds || 15);
                    }
                    // 5. Default: Nada Chime bel
                    else {
                        this.playChime('westminster', () => {
                            if (bell.voice_text) {
                                this.speakText(bell.voice_text);
                            }
                        });
                    }
                },

                playPlaylistQueue(items, durationSeconds, voiceTextAfter) {
                    this.stopAllAudio();
                    let currentIndex = 0;

                    const playNextInQueue = () => {
                        if (currentIndex >= items.length) {
                            this.isPlaying = false;
                            if (voiceTextAfter) {
                                this.speakText(voiceTextAfter);
                            }
                            return;
                        }

                        const item = items[currentIndex];
                        this.currentAudioTitle = `[${currentIndex + 1}/${items.length}] ${item.title}`;
                        const audio = new Audio(item.audio_url);
                        audio.volume = this.volumePercent / 100;
                        this.activeAudioElement = audio;

                        audio.play().catch(err => {
                            console.warn('Playlist playback error:', err);
                            currentIndex++;
                            playNextInQueue();
                        });

                        audio.onended = () => {
                            currentIndex++;
                            playNextInQueue();
                        };
                    };

                    playNextInQueue();

                    if (durationSeconds && durationSeconds > 0) {
                        setTimeout(() => {
                            if (this.isPlaying) {
                                this.stopAllAudio();
                            }
                        }, durationSeconds * 1000);
                    }
                },

                previewSchedule(bell) {
                    this.triggerBell(bell);
                },

                playAudioFile(url, durationSeconds, voiceTextAfter) {
                    this.stopAllAudio();
                    const audio = new Audio(url);
                    audio.volume = this.volumePercent / 100;
                    this.activeAudioElement = audio;

                    audio.play().catch(err => {
                        console.warn('Audio file playback prevented:', err);
                    });

                    // Batasi durasi jika ditentukan
                    const maxDuration = (durationSeconds || 30) * 1000;
                    const stopTimeout = setTimeout(() => {
                        if (this.activeAudioElement === audio) {
                            audio.pause();
                            this.isPlaying = false;
                            if (voiceTextAfter) {
                                this.speakText(voiceTextAfter);
                            }
                        }
                    }, maxDuration);

                    audio.onended = () => {
                        clearTimeout(stopTimeout);
                        this.isPlaying = false;
                        if (voiceTextAfter) {
                            this.speakText(voiceTextAfter);
                        }
                    };
                },

                playChime(type = 'westminster', onEndedCallback = null) {
                    this.unlockAudioEngine();
                    if (!this.audioContext) {
                        if (onEndedCallback) onEndedCallback();
                        return;
                    }

                    const ctx = this.audioContext;
                    const now = ctx.currentTime;
                    const masterGain = ctx.createGain();
                    masterGain.gain.setValueAtTime((this.volumePercent / 100) * 0.4, now);
                    masterGain.connect(ctx.destination);

                    if (type === 'westminster') {
                        // Pola melodi 4 nada Westminster Chime (E - C - D - G)
                        const notes = [
                            { f: 329.63, t: 0.0, d: 0.7 }, // E4
                            { f: 261.63, t: 0.8, d: 0.7 }, // C4
                            { f: 293.66, t: 1.6, d: 0.7 }, // D4
                            { f: 196.00, t: 2.4, d: 1.4 }, // G3
                        ];

                        notes.forEach(n => {
                            const osc = ctx.createOscillator();
                            const g = ctx.createGain();
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(n.f, now + n.t);

                            g.gain.setValueAtTime(0.001, now + n.t);
                            g.gain.exponentialRampToValueAtTime(1.0, now + n.t + 0.05);
                            g.gain.exponentialRampToValueAtTime(0.0001, now + n.t + n.d);

                            osc.connect(g);
                            g.connect(masterGain);
                            osc.start(now + n.t);
                            osc.stop(now + n.t + n.d + 0.1);
                        });

                        setTimeout(() => {
                            this.isPlaying = false;
                            if (onEndedCallback) onEndedCallback();
                        }, 4000);
                    } else {
                        // Standard 3-Tone chime (Ding - Dang - Dong)
                        const notes = [
                            { f: 523.25, t: 0.0, d: 0.5 }, // C5
                            { f: 659.25, t: 0.5, d: 0.5 }, // E5
                            { f: 783.99, t: 1.0, d: 0.9 }, // G5
                        ];

                        notes.forEach(n => {
                            const osc = ctx.createOscillator();
                            const g = ctx.createGain();
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(n.f, now + n.t);

                            g.gain.setValueAtTime(0.001, now + n.t);
                            g.gain.exponentialRampToValueAtTime(1.0, now + n.t + 0.04);
                            g.gain.exponentialRampToValueAtTime(0.0001, now + n.t + n.d);

                            osc.connect(g);
                            g.connect(masterGain);
                            osc.start(now + n.t);
                            osc.stop(now + n.t + n.d + 0.1);
                        });

                        setTimeout(() => {
                            this.isPlaying = false;
                            if (onEndedCallback) onEndedCallback();
                        }, 2200);
                    }
                },

                playSiren(durationSeconds = 10) {
                    this.unlockAudioEngine();
                    if (!this.audioContext) return;

                    const ctx = this.audioContext;
                    const now = ctx.currentTime;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    gain.gain.setValueAtTime((this.volumePercent / 100) * 0.35, now);
                    gain.connect(ctx.destination);

                    osc.type = 'sawtooth';
                    // Siren frequency modulation
                    const cycle = 1.2;
                    for (let i = 0; i < durationSeconds; i += cycle) {
                        osc.frequency.setValueAtTime(450, now + i);
                        osc.frequency.linearRampToValueAtTime(950, now + i + (cycle / 2));
                        osc.frequency.linearRampToValueAtTime(450, now + i + cycle);
                    }

                    osc.connect(gain);
                    osc.start(now);
                    osc.stop(now + durationSeconds);

                    setTimeout(() => {
                        this.isPlaying = false;
                    }, durationSeconds * 1000);
                },

                speakText(text) {
                    if (!('speechSynthesis' in window) || !text) return;
                    window.speechSynthesis.cancel();

                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.95;
                    utterance.volume = this.volumePercent / 100;

                    const voices = window.speechSynthesis.getVoices();
                    const idVoice = voices.find(v => v.lang.includes('id') || v.lang.includes('ID'));
                    if (idVoice) {
                        utterance.voice = idVoice;
                    }

                    window.speechSynthesis.speak(utterance);
                },

                // Kiosk Instant Soundboard Handler (12 Jenis)
                playKioskInstantSound(type, name, text = null) {
                    this.stopAllAudio();
                    this.unlockAudioEngine();
                    this.isPlaying = true;
                    this.currentAudioTitle = name;
                    this.currentAudioType = type;
                    this.ringingRemainingSeconds = text ? 25 : 10;

                    const ctx = this.audioContext;
                    if (!ctx) {
                        if (text) this.speakText(text);
                        return;
                    }

                    const now = ctx.currentTime;
                    const masterGain = ctx.createGain();
                    masterGain.gain.setValueAtTime((this.volumePercent / 100) * 0.45, now);
                    masterGain.connect(ctx.destination);

                    if (type === 'whistle') {
                        [2400, 2600].forEach(freq => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(freq, now);
                            gain.gain.setValueAtTime(0.01, now);
                            gain.gain.exponentialRampToValueAtTime(0.5, now + 0.05);
                            gain.gain.exponentialRampToValueAtTime(0.001, now + 1.6);
                            osc.connect(gain);
                            gain.connect(masterGain);
                            osc.start(now);
                            osc.stop(now + 1.7);
                        });
                        setTimeout(() => { this.isPlaying = false; }, 1800);
                    } else if (type === 'fanfare') {
                        const notes = [
                            { f: 261.63, d: 0.28 },
                            { f: 329.63, d: 0.28 },
                            { f: 392.00, d: 0.28 },
                            { f: 523.25, d: 0.55 },
                            { f: 392.00, d: 0.28 },
                            { f: 523.25, d: 1.20 }
                        ];
                        let t = now;
                        notes.forEach(n => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = 'sawtooth';
                            osc.frequency.setValueAtTime(n.f, t);
                            gain.gain.setValueAtTime(0.01, t);
                            gain.gain.exponentialRampToValueAtTime(0.4, t + 0.04);
                            gain.gain.exponentialRampToValueAtTime(0.001, t + n.d);
                            osc.connect(gain);
                            gain.connect(masterGain);
                            osc.start(t);
                            osc.stop(t + n.d + 0.05);
                            t += n.d + 0.06;
                        });
                        setTimeout(() => { this.isPlaying = false; }, (t - now) * 1000 + 300);
                    } else if (type === 'gong') {
                        [110, 165, 275, 440].forEach((freq, idx) => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = idx === 0 ? 'sine' : 'triangle';
                            osc.frequency.setValueAtTime(freq, now);
                            const maxGain = idx === 0 ? 0.7 : 0.25;
                            gain.gain.setValueAtTime(0.001, now);
                            gain.gain.exponentialRampToValueAtTime(maxGain, now + 0.03);
                            gain.gain.exponentialRampToValueAtTime(0.0001, now + 4.2);
                            osc.connect(gain);
                            gain.connect(masterGain);
                            osc.start(now);
                            osc.stop(now + 4.3);
                        });
                        setTimeout(() => { this.isPlaying = false; }, 4500);
                    } else if (type === 'siren_fire' || type === 'siren') {
                        this.playSiren(12);
                    } else if (type === 'siren_earthquake') {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sawtooth';
                        gain.gain.setValueAtTime(0.35, now);
                        let t = now;
                        for (let i = 0; i < 4; i++) {
                            osc.frequency.setValueAtTime(380, t);
                            osc.frequency.linearRampToValueAtTime(780, t + 0.9);
                            osc.frequency.linearRampToValueAtTime(380, t + 1.8);
                            t += 1.8;
                        }
                        gain.gain.setValueAtTime(0, t);
                        osc.connect(gain);
                        gain.connect(masterGain);
                        osc.start(now);
                        osc.stop(t);
                        setTimeout(() => { this.isPlaying = false; }, (t - now) * 1000);
                    } else if (type === 'threetone' || type === 'voice_kbm_start' || type === 'voice_home') {
                        this.playChime('threetone', () => {
                            if (text) this.speakText(text);
                        });
                    } else if (type === 'twotone' || type === 'voice_break' || type === 'voice_prayer') {
                        this.playChime('twotone', () => {
                            if (text) this.speakText(text);
                        });
                    } else {
                        this.playChime('westminster', () => {
                            if (text) this.speakText(text);
                        });
                    }
                },

                // Play Murottal Surah in Kiosk Manual
                playKioskSurah(surah) {
                    this.stopAllAudio();
                    this.unlockAudioEngine();
                    this.isPlaying = true;
                    this.currentAudioTitle = `Surah ${surah.number}. ${surah.name} (${surah.arabic}) - ${surah.translation}`;
                    this.currentAudioType = "Murottal Al-Qur'an";
                    this.currentPlayingSurahNumber = surah.number;
                    this.ringingRemainingSeconds = 0;

                    const audio = new Audio(surah.audio_url);
                    audio.volume = this.volumePercent / 100;
                    this.activeAudioElement = audio;

                    audio.play().catch(err => {
                        console.warn('Murottal stream failed, trying fallback:', err);
                        if (surah.audio_fallback) {
                            audio.src = surah.audio_fallback;
                            audio.play().catch(e => console.error('Audio fallback also failed', e));
                        }
                    });

                    audio.onended = () => {
                        this.isPlaying = false;
                        this.currentPlayingSurahNumber = null;
                    };
                },

                // Play National Anthem in Kiosk
                playKioskAnthem(anthem) {
                    this.stopAllAudio();
                    this.unlockAudioEngine();
                    this.isPlaying = true;
                    this.currentAudioTitle = `Lagu: ${anthem.title}`;
                    this.currentAudioType = 'Lagu Nasional';
                    this.ringingRemainingSeconds = 180;

                    const audio = new Audio(anthem.audio_url);
                    audio.volume = this.volumePercent / 100;
                    this.activeAudioElement = audio;

                    audio.play().catch(err => {
                        console.warn('Anthem stream failed, trying fallback:', err);
                        if (anthem.audio_fallback) {
                            audio.src = anthem.audio_fallback;
                            audio.play().catch(() => this.playSynthMelody(anthem.melody_notes));
                        } else {
                            this.playSynthMelody(anthem.melody_notes);
                        }
                    });

                    audio.onended = () => {
                        this.isPlaying = false;
                    };
                },

                playSynthMelody(notes) {
                    if (!notes || !notes.length) return;
                    this.unlockAudioEngine();
                    const ctx = this.audioContext;
                    if (!ctx) return;
                    let t = ctx.currentTime;
                    const masterGain = ctx.createGain();
                    masterGain.gain.value = (this.volumePercent / 100) * 0.35;
                    masterGain.connect(ctx.destination);

                    notes.forEach(n => {
                        const osc = ctx.createOscillator();
                        const g = ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(n.f, t);
                        g.gain.setValueAtTime(0.001, t);
                        g.gain.exponentialRampToValueAtTime(0.8, t + 0.04);
                        g.gain.exponentialRampToValueAtTime(0.001, t + n.d);
                        osc.connect(g);
                        g.connect(masterGain);
                        osc.start(t);
                        osc.stop(t + n.d + 0.05);
                        t += n.d + 0.08;
                    });

                    setTimeout(() => {
                        this.isPlaying = false;
                    }, (t - ctx.currentTime) * 1000);
                },

                testSound(type) {
                    this.isPlaying = true;
                    this.currentAudioTitle = `Uji Coba Suara: ${type.toUpperCase()}`;
                    this.currentAudioType = type;
                    this.ringingRemainingSeconds = 5;
                    this.playChime(type);
                },

                testSpeech(text) {
                    this.unlockAudioEngine();
                    this.speakText(text);
                },

                updateVolume() {
                    if (this.activeAudioElement) {
                        this.activeAudioElement.volume = this.volumePercent / 100;
                    }
                },

                stopAllAudio() {
                    if (this.activeAudioElement) {
                        this.activeAudioElement.pause();
                        this.activeAudioElement.currentTime = 0;
                        this.activeAudioElement = null;
                    }
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                    }
                    this.isPlaying = false;
                    this.currentAudioTitle = '';
                    this.currentAudioType = '';
                    this.currentPlayingSurahNumber = null;
                    this.ringingRemainingSeconds = 0;
                }
            };
        }
    </script>
</body>
</html>

