@extends('layouts.app')

@section('title', 'Bel Sekolah - SMAN 1 Lengkong')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12" x-data="schoolBellApp()">

    <!-- TOP HEADER HERO BANNER -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-4 sm:p-6 lg:p-8 shadow-2xl border border-indigo-500/30">
        <!-- Background Aura Ornaments -->
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 sm:gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 sm:px-3 py-1 rounded-full text-[11px] sm:text-xs font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-400/30 flex items-center gap-1.5 shrink-0">
                        <span class="w-2 h-2 rounded-full" :class="isAutoStandbyActive ? 'bg-emerald-400 animate-ping' : 'bg-slate-400'"></span>
                        <span x-text="isAutoStandbyActive ? 'Sistem Bel Otomatis: SIAGA AKTIF' : 'Sistem Bel: STANDBY NON-AKTIF'"></span>
                    </span>
                    <span class="px-2.5 sm:px-3 py-1 rounded-full text-[11px] sm:text-xs font-bold bg-white/10 text-slate-200 border border-white/10 shrink-0">
                        SMAN 1 Lengkong
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight text-white flex items-center gap-2 sm:gap-3">
                    <span>🔔 Bel Sekolah</span>
                </h1>
                <p class="text-xs sm:text-sm text-indigo-200 max-w-2xl mt-1.5 leading-relaxed">
                    Sistem otomasi jadwal bel sekolah digital, lagu-lagu nasional, narasi pengumuman suara, soundboard instan, dan audio murottal Al-Qur'an.
                </p>
            </div>

            <!-- Jam Realtime & Timer Bel Berikutnya (Responsif di HP, Tablet, Komputer) -->
            <div class="w-full lg:w-auto grid grid-cols-2 sm:flex sm:flex-row items-center justify-between sm:justify-end gap-3 sm:gap-4 bg-white/10 backdrop-blur-md p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-white/15">
                <div class="text-left sm:text-right min-w-0">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-amber-300">WAKTU SISTEM (WIB)</div>
                    <div class="text-xl sm:text-2xl lg:text-3xl font-black font-mono tracking-tight text-white mt-0.5 truncate" x-text="currentTimeDisplay">
                        {{ $currentTime }}:00
                    </div>
                    <div class="text-[10px] sm:text-[11px] text-slate-300 truncate" x-text="currentDateDisplay">
                        {{ $todayName }}, {{ date('d M Y') }}
                    </div>
                </div>

                <div class="h-10 w-px bg-white/20 hidden sm:block"></div>

                <div class="text-right sm:text-left min-w-0 sm:min-w-[150px]">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-cyan-300">BEL BERIKUTNYA</div>
                    <div class="text-xs sm:text-sm font-extrabold text-white mt-0.5 truncate max-w-[140px] sm:max-w-[200px]" x-text="nextBellTitle || 'Selesai Hari Ini'"></div>
                    <div class="text-[11px] sm:text-xs text-amber-300 font-mono font-bold mt-0.5 truncate" x-text="nextBellCountdownText || 'Tuntas'"></div>
                </div>
            </div>
        </div>

        <!-- AUTO-STANDBY ENGAGE BAR & MASTER CONTROLS -->
        <div class="relative z-10 mt-5 sm:mt-6 pt-4 sm:pt-5 border-t border-white/15 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Tombol Utama Pengaktifan Mode Siaga Otomatis & Kios Bel -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full lg:w-auto flex-wrap">
                <!-- Tombol Buka Kios Bel Standalone Full Screen -->
                <a href="{{ route('school_bell.kiosk') }}" target="_blank" class="w-full sm:w-auto justify-center text-center px-4 py-2.5 sm:px-5 rounded-xl sm:rounded-2xl bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500 hover:from-amber-300 hover:to-orange-400 text-slate-950 font-black text-xs sm:text-sm shadow-xl transition flex items-center gap-2 cursor-pointer border border-amber-300/60 active:scale-95">
                    <span class="text-base shrink-0">🖥️</span>
                    <span class="truncate">BUKA KIOS BEL (LAYAR PENUH)</span>
                </a>

                <button type="button" 
                        @click="toggleAutoStandby()" 
                        :class="isAutoStandbyActive ? 'bg-emerald-600 hover:bg-emerald-500 ring-2 ring-emerald-400' : 'bg-slate-800 hover:bg-slate-700 text-white'"
                        class="w-full sm:w-auto justify-center text-center px-4 py-2.5 sm:px-5 rounded-xl sm:rounded-2xl font-black text-xs sm:text-sm shadow-xl transition flex items-center gap-2 cursor-pointer border border-white/10 active:scale-95">
                    <span class="truncate" x-text="isAutoStandbyActive ? '🟢 SIAGA OTOMATIS AKTIF' : '▶️ AKTIFKAN MODE SIAGA'"></span>
                </button>

                <!-- Tombol Test Cepat (Grid rapi di HP) -->
                <div class="grid grid-cols-3 sm:flex items-center gap-2 w-full sm:w-auto">
                    <button type="button" @click="testChimeSound('westminster')" class="justify-center text-center px-2 sm:px-3.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white font-bold text-[11px] sm:text-xs transition flex items-center gap-1 cursor-pointer truncate" title="Coba Nada Bel">
                        <span>🔔</span>
                        <span class="hidden xs:inline">Test </span>Nada
                    </button>

                    <button type="button" @click="testSpeech('Perhatian, ini adalah uji coba pengumuman suara bel sekolah SMA Negeri 1 Lengkong.')" class="justify-center text-center px-2 sm:px-3.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white font-bold text-[11px] sm:text-xs transition flex items-center gap-1 cursor-pointer truncate" title="Coba Pengumuman Suara">
                        <span>🗣️</span>
                        <span class="hidden xs:inline">Test </span>Suara
                    </button>

                    <button type="button" @click="testChimeSound('siren')" class="justify-center text-center px-2 sm:px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-[11px] sm:text-xs shadow-md transition flex items-center gap-1 cursor-pointer truncate" title="Sirine Darurat">
                        <span>🚨</span>
                        <span>Sirine</span>
                    </button>
                </div>

                <button type="button" @click="stopAllAudio()" x-show="isPlaying" class="w-full sm:w-auto justify-center text-center px-3.5 py-2 rounded-xl bg-rose-500/80 hover:bg-rose-600 text-white font-bold text-xs border border-white/20 transition flex items-center gap-1.5 cursor-pointer">
                    <span>⏹️ Hentikan</span>
                </button>
            </div>

            <!-- Pengaturan Suara & Fitur Tambahan (Pill rapi & responsif) -->
            <div class="flex flex-col xs:flex-row items-stretch xs:items-center justify-between gap-3 text-xs w-full lg:w-auto bg-black/20 sm:bg-white/5 p-3 rounded-xl sm:rounded-2xl border border-white/10">
                <!-- Toggle Narasi Suara -->
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" x-model="enableVoiceAnnouncement" class="rounded text-amber-500 focus:ring-0">
                    <span class="text-slate-200 font-medium text-xs">Narasi Pengumuman Suara</span>
                </label>

                <!-- Master Volume Slider -->
                <div class="flex items-center gap-2 bg-white/10 sm:bg-transparent px-2.5 py-1.5 sm:p-0 rounded-lg">
                    <span class="text-slate-300">🔊</span>
                    <input type="range" min="10" max="100" x-model="masterVolume" class="w-24 xs:w-20 accent-amber-400">
                    <span class="text-[11px] font-mono text-amber-300 font-bold w-9 text-right" x-text="masterVolume + '%'"></span>
                </div>
            </div>
        </div>

        <!-- Animated Audio Status / Equalizer Banner -->
        <div x-show="isPlaying" class="relative z-10 mt-4 p-3 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-between text-xs text-amber-200">
            <div class="flex items-center gap-2">
                <div class="flex items-end gap-1 h-4">
                    <span class="w-1 bg-amber-400 animate-pulse h-3"></span>
                    <span class="w-1 bg-cyan-400 animate-pulse h-4" style="animation-delay: 150ms;"></span>
                    <span class="w-1 bg-emerald-400 animate-pulse h-2" style="animation-delay: 300ms;"></span>
                    <span class="w-1 bg-amber-400 animate-pulse h-4" style="animation-delay: 75ms;"></span>
                </div>
                <span class="font-bold text-white" x-text="'Sedang Berbunyi: ' + currentAudioTitle"></span>
            </div>
            <button type="button" @click="stopAllAudio()" class="px-3 py-1 rounded-lg bg-rose-500 text-white font-bold text-[11px] hover:bg-rose-600">
                Stop Bunyi
            </button>
        </div>
    </div>

    <!-- STATS OVERVIEW CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Jadwal Bel</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total'] }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Senin s.d. Jumat/Sabtu</div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-emerald-500/20 bg-emerald-500/5 shadow-xs">
            <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Aktif Hari Ini ({{ $todayName }})</div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['active_today'] }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Jadwal bel terjadwal</div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-rose-500/20 bg-rose-500/5 shadow-xs">
            <div class="text-[11px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Soundboard Instan</div>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ count($instantSounds) }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">12 Bunyi & Alarm Siap Pakai</div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-red-500/20 bg-red-500/5 shadow-xs">
            <div class="text-[11px] font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">Lagu Nasional</div>
            <div class="text-2xl font-black text-red-600 dark:text-red-400 mt-1">{{ count($nationalAnthems) }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Indonesia Raya dkk</div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-indigo-500/20 bg-indigo-500/5 shadow-xs col-span-2 sm:col-span-1">
            <div class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Murottal 114 Surah</div>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">114</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Al-Fatihah s.d. An-Nas</div>
        </div>
    </div>

    <!-- MAIN TABS NAVIGATION -->
    <div class="border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-px no-scrollbar scroll-smooth">
            <button type="button" @click="activeTab = 'schedules'" :class="activeTab === 'schedules' ? 'border-amber-500 text-amber-600 dark:text-amber-400 font-extrabold' : 'border-transparent text-slate-600 dark:text-slate-400 font-semibold hover:text-slate-900 hover:border-slate-300'" class="py-2.5 sm:py-3 px-3 sm:px-4 text-xs sm:text-sm border-b-2 transition flex items-center gap-1.5 sm:gap-2 cursor-pointer whitespace-nowrap">
                <span>🔔 Jadwal Bel Harian</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-mono">{{ $allBells->count() }}</span>
            </button>
            <button type="button" @click="activeTab = 'soundboard'" :class="activeTab === 'soundboard' ? 'border-amber-500 text-amber-600 dark:text-amber-400 font-extrabold' : 'border-transparent text-slate-600 dark:text-slate-400 font-semibold hover:text-slate-900 hover:border-slate-300'" class="py-2.5 sm:py-3 px-3 sm:px-4 text-xs sm:text-sm border-b-2 transition flex items-center gap-1.5 sm:gap-2 cursor-pointer whitespace-nowrap">
                <span>🎛️ Soundboard</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-mono">{{ count($instantSounds) }}</span>
            </button>
            <button type="button" @click="activeTab = 'anthems'" :class="activeTab === 'anthems' ? 'border-amber-500 text-amber-600 dark:text-amber-400 font-extrabold' : 'border-transparent text-slate-600 dark:text-slate-400 font-semibold hover:text-slate-900 hover:border-slate-300'" class="py-2.5 sm:py-3 px-3 sm:px-4 text-xs sm:text-sm border-b-2 transition flex items-center gap-1.5 sm:gap-2 cursor-pointer whitespace-nowrap">
                <span>🇮🇩 Lagu Nasional</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300 font-mono">10</span>
            </button>
            <button type="button" @click="activeTab = 'quran'" :class="activeTab === 'quran' ? 'border-amber-500 text-amber-600 dark:text-amber-400 font-extrabold' : 'border-transparent text-slate-600 dark:text-slate-400 font-semibold hover:text-slate-900 hover:border-slate-300'" class="py-2.5 sm:py-3 px-3 sm:px-4 text-xs sm:text-sm border-b-2 transition flex items-center gap-1.5 sm:gap-2 cursor-pointer whitespace-nowrap">
                <span>📖 Murottal 114 Surah</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-mono">114</span>
            </button>
        </div>

        <div class="shrink-0 w-full sm:w-auto flex flex-wrap items-center justify-end gap-2" x-show="activeTab === 'schedules'">
            <button type="button" @click="openAddModal()" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                <span>+ Tambah Jadwal Bel</span>
            </button>
            <form action="{{ route('school_bell.reset_defaults') }}" method="POST" onsubmit="return confirm('Kembalikan seluruh jadwal ke setelan resmi SMAN 1 Lengkong TP 2026/2027 (52 jadwal lengkap)? Jadwal saat ini akan ditimpa.')" class="inline">
                @csrf
                <button type="submit" class="px-3 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer" title="Muat Jadwal Resmi TP 2026/2027">
                    <span>🔄 Reset Standar (52 Jadwal)</span>
                </button>
            </form>
            @if($allBells->count() > 0)
            <form action="{{ route('school_bell.clear_all') }}" method="POST" onsubmit="return confirm('Peringatan! Apakah Anda yakin ingin menghapus SEMUA jadwal bel harian? Anda dapat mengisinya kembali atau memuat jadwal standar kapan saja.')" class="inline">
                @csrf
                <button type="submit" class="px-3 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer" title="Hapus Semua Jadwal">
                    <span>🗑️ Hapus Semua</span>
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- TAB 1: JADWAL BEL SEKOLAH HARIAN -->
    <div x-show="activeTab === 'schedules'" class="space-y-4">
        <!-- Filter Hari -->
        <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Pilih Hari:</span>
                <template x-for="day in ['Semua', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']" :key="day">
                    <button type="button" @click="filterDay = day" :class="filterDay === day ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer" x-text="day">
                    </button>
                </template>
            </div>
            <div class="text-xs text-slate-500">
                Bel berbunyi otomatis saat tab dalam mode Siaga Aktif
            </div>
        </div>

        <!-- Tabel Daftar Jadwal Bel -->
        <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4 w-28">Jam (WIB)</th>
                            <th class="py-3.5 px-4">Nama Peristiwa / Pengumuman Bel</th>
                            <th class="py-3.5 px-4 w-40">Tipe Audio / Nada</th>
                            <th class="py-3.5 px-4 w-44">Hari Berlaku</th>
                            <th class="py-3.5 px-4 w-24 text-center">Durasi</th>
                            <th class="py-3.5 px-4 w-24 text-center">Status</th>
                            <th class="py-3.5 px-4 w-36 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($allBells as $idx => $bell)
                            @php
                                $daysJson = json_encode($bell->days ?? []);
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition" x-show="shouldShowBell('{{ $daysJson }}')">
                                <td class="py-3 px-4 text-center font-mono font-medium text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 font-mono font-black text-sm">
                                        {{ $bell->time }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $bell->title }}</div>
                                    @if($bell->voice_text)
                                        <div class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5 flex items-center gap-1">
                                            <span>🗣️</span>
                                            <span>"{{ $bell->voice_text }}"</span>
                                        </div>
                                    @elseif($bell->description)
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $bell->description }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($bell->sound_type === 'murottal')
                                        @php
                                            $sCount = is_array($bell->surah_numbers) ? count($bell->surah_numbers) : ($bell->surah_number ? 1 : 0);
                                        @endphp
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/20 text-[11px]">
                                                <span>📖</span>
                                                @if($sCount > 1)
                                                    <span>Murottal {{ $sCount }} Surat (Playlist)</span>
                                                @else
                                                    <span>Murottal: {{ $bell->surah_name ?: ('Surah Ke-' . $bell->surah_number) }}</span>
                                                @endif
                                            </span>
                                            @if($sCount > 1 && $bell->surah_name)
                                                <div class="text-[10px] text-slate-500 truncate max-w-xs" title="{{ $bell->surah_name }}">{{ $bell->surah_name }}</div>
                                            @endif
                                        </div>
                                    @elseif($bell->sound_type === 'song')
                                        @php
                                            $aCount = is_array($bell->anthem_ids) ? count($bell->anthem_ids) : ($bell->anthem_id ? 1 : 0);
                                        @endphp
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-500/10 text-red-600 dark:text-red-400 font-bold border border-red-500/20 text-[11px]">
                                                <span>🇮🇩</span>
                                                @if($aCount > 1)
                                                    <span>Playlist {{ $aCount }} Lagu Nasional</span>
                                                @else
                                                    <span>Lagu Nasional</span>
                                                @endif
                                            </span>
                                            @if($bell->playlist_items && count($bell->playlist_items) > 1)
                                                <div class="text-[10px] text-slate-500 truncate max-w-xs" title="{{ collect($bell->playlist_items)->pluck('title')->join(', ') }}">
                                                    {{ collect($bell->playlist_items)->pluck('title')->join(', ') }}
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($bell->sound_type === 'custom')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold border border-indigo-500/20 text-[11px]">
                                            <span>🎵</span>
                                            <span>Audio Kustom</span>
                                        </span>
                                    @elseif($bell->sound_type === 'voice')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold border border-cyan-500/20 text-[11px]">
                                            <span>🗣️</span>
                                            <span>Pengumuman Suara</span>
                                        </span>
                                    @elseif($bell->sound_type === 'siren')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold border border-rose-500/20 text-[11px]">
                                            <span>🚨</span>
                                            <span>Sirine Evakuasi</span>
                                        </span>
                                    @elseif($bell->sound_type === 'soundboard')
                                        @php
                                            $sbItem = is_array($bell->playlist) && isset($bell->playlist[0]) ? $bell->playlist[0] : null;
                                            $sbTitle = $sbItem['title'] ?? 'Soundboard Instan';
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold border border-indigo-500/20 text-[11px]">
                                            <span>🎛️</span>
                                            <span>{{ $sbTitle }}</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold border border-amber-500/20 text-[11px]">
                                            <span>🔔</span>
                                            <span>Chime Westminster</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($bell->days ?? [] as $d)
                                            <span class="px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-semibold text-slate-600 dark:text-slate-300">
                                                {{ substr($d, 0, 3) }}
                                            </span>
                                        @empty
                                            <span class="text-[10px] text-slate-400">Setiap Hari</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center font-mono text-[11px]">
                                    @if($bell->duration_seconds >= 60)
                                        {{ round($bell->duration_seconds / 60) }} mnt
                                    @else
                                        {{ $bell->duration_seconds }} dtk
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('school_bell.toggle', $bell) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider transition cursor-pointer {{ $bell->is_active ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">
                                            {{ $bell->is_active ? 'AKTIF' : 'OFF' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Test Bunyi -->
                                        <button type="button" @click="playBellRecordById({{ $bell->id }})" class="p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 transition cursor-pointer" title="Uji Bunyikan Sekarang">
                                            ▶️
                                        </button>
                                        <!-- Edit -->
                                        <button type="button" @click="openEditModal({{ $bell->id }})" class="p-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 transition cursor-pointer" title="Edit Jadwal">
                                            ✏️
                                        </button>
                                        <!-- Hapus -->
                                        <form action="{{ route('school_bell.destroy', $bell) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal bel ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 transition cursor-pointer" title="Hapus">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">🔔</div>
                                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada jadwal bel yang terpasang</div>
                                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Seluruh jadwal telah dikosongkan. Anda dapat menambahkan jadwal harian baru sesuai kebutuhan sekolah atau memuat jadwal resmi SMAN 1 Lengkong TP 2026/2027.</p>
                                    <div class="flex items-center justify-center gap-3 mt-4">
                                        <button type="button" @click="openAddModal()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-xs transition cursor-pointer">
                                            + Tambah Jadwal Baru
                                        </button>
                                        <form action="{{ route('school_bell.reset_defaults') }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500/15 hover:bg-amber-500/25 text-amber-700 dark:text-amber-300 border border-amber-500/30 font-bold text-xs transition cursor-pointer">
                                                🔄 Muat Jadwal Resmi (52 Jadwal)
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: MUROTTAL AL-QUR'AN 114 SURAH LENGKAP -->
    <div x-show="activeTab === 'quran'" class="space-y-6">
        <!-- Floating / Sticky Audio Player Card -->
        <div class="p-5 rounded-3xl bg-gradient-to-r from-emerald-900 via-teal-950 to-slate-900 text-white shadow-xl border border-emerald-500/30">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-3xl shrink-0">
                        📖
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-300">AUDIO MUROTTAL AL-QUR'AN (SYAIKH MISHARY RASHID ALAFASY)</div>
                        <h2 class="text-lg font-black text-white" x-text="currentSurahName ? currentSurahName : 'Pilih Surah untuk Mulai Memutar'"></h2>
                        <div class="text-xs text-emerald-200 mt-0.5" x-text="currentSurahDetails">114 Surah Tersedia dengan Audio Streaming Berkualitas Tinggi</div>
                    </div>
                </div>

                <!-- Player Controls -->
                <div class="flex items-center gap-3">
                    <button type="button" @click="playPrevSurah()" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition cursor-pointer" title="Surah Sebelumnya">
                        ⏮️
                    </button>
                    <button type="button" @click="toggleMurottalPlay()" class="px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-sm shadow-lg transition flex items-center gap-2 cursor-pointer">
                        <span x-text="isPlayingMurottal ? '⏸️ Jeda' : '▶️ Putar'"></span>
                    </button>
                    <button type="button" @click="playNextSurah()" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition cursor-pointer" title="Surah Berikutnya">
                        ⏭️
                    </button>
                    <button type="button" @click="stopAllAudio()" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition cursor-pointer" title="Berhenti">
                        ⏹️
                    </button>
                </div>
            </div>

            <!-- Audio Progress Bar -->
            <div class="mt-4 pt-4 border-t border-white/15">
                <div class="flex items-center justify-between text-[11px] font-mono text-emerald-200 mb-1">
                    <span x-text="audioCurrentTimeFormatted">00:00</span>
                    <span x-text="audioDurationFormatted">00:00</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2 cursor-pointer relative" @click="seekAudio($event)">
                    <div class="bg-emerald-400 h-2 rounded-full transition-all" :style="'width: ' + audioProgressPercent + '%'"></div>
                </div>
            </div>
        </div>

        <!-- Filter & Pencarian Surah -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="w-full sm:w-80 relative">
                <input type="text" x-model="surahSearch" placeholder="Cari nomor, nama surah, atau arti..." class="w-full px-4 py-2 pl-9 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:border-emerald-500 text-slate-900 dark:text-white">
                <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" @click="surahCategory = 'all'" :class="surahCategory === 'all' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer">
                    Semua (114)
                </button>
                <button type="button" @click="surahCategory = 'featured'" :class="surahCategory === 'featured' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer">
                    ⭐ Surah Pilihan Sekolah
                </button>
                <button type="button" @click="surahCategory = 'juz30'" :class="surahCategory === 'juz30' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer">
                    Juz 30 (Juz 'Amma)
                </button>
            </div>
        </div>

        <!-- Grid 114 Surah Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($allSurahs as $surah)
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs hover:border-emerald-500/50 hover:shadow-md transition flex flex-col justify-between"
                     x-show="matchSurah({{ $surah['number'] }}, '{{ addslashes($surah['name']) }}', '{{ addslashes($surah['translation']) }}')">
                    
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-mono font-black text-sm flex items-center justify-center shrink-0">
                                {{ $surah['number'] }}
                            </div>
                            <div>
                                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $surah['name'] }}</h3>
                                <div class="text-[11px] text-slate-500">{{ $surah['translation'] }}</div>
                            </div>
                        </div>

                        <!-- Nama Arab Kaligrafi -->
                        <div class="text-right">
                            <span class="font-arabic text-xl font-bold text-emerald-700 dark:text-emerald-400">{{ $surah['arabic'] }}</span>
                            <div class="text-[10px] text-slate-400">{{ $surah['ayah_count'] }} Ayat • {{ $surah['type'] }}</div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                        <button type="button" @click="playSurahDirect({{ json_encode($surah) }})" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                            <span>▶️ Putar Murottal</span>
                        </button>
                        <button type="button" @click="setAsBellSchedule({{ json_encode($surah) }})" class="px-3 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold text-xs border border-amber-500/20 transition flex items-center gap-1 cursor-pointer" title="Pasang ke Jadwal Bel Otomatis">
                            <span>⏰ Jadikan Bel</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 3: SOUNDBOARD & BUNYI INSTAN (MINIMAL 10 JENIS - TOTAL 12 JENIS) -->
    <div x-show="activeTab === 'soundboard'" class="space-y-6">
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base font-black text-slate-900 dark:text-white">🎛️ Soundboard & Bunyi Instan (12 Jenis)</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                            MANUAL OVERRIDE
                        </span>
                        <button type="button" @click="activeTab = 'quran'" class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/25 transition cursor-pointer flex items-center gap-1.5">
                            <span>📖 Putar Murottal 114 Surah Manual</span>
                            <span class="text-[10px] bg-emerald-600 text-white px-1.5 py-0.5 rounded-full font-mono">114</span>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Bunyikan bel, lonceng, sirine darurat, terompet upacara, peluit, gong, atau narasi suara instan secara langsung ke speaker sekolah tanpa menunggu jadwal.
                    </p>
                </div>

                <!-- Kategori Filter & Pencarian Soundboard -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <div class="relative w-full sm:w-56">
                        <input type="text" x-model="soundboardSearch" placeholder="Cari 12 jenis suara..." class="w-full px-3 py-1.5 pl-8 text-xs bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                        <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" @click="soundboardCategory = 'all'" :class="soundboardCategory === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer">
                            Semua ({{ count($instantSounds) }})
                        </button>
                        <button type="button" @click="soundboardCategory = 'bel'" :class="soundboardCategory === 'bel' ? 'bg-amber-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer">
                            🔔 Bel & Interkom (3)
                        </button>
                        <button type="button" @click="soundboardCategory = 'darurat'" :class="soundboardCategory === 'darurat' ? 'bg-rose-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer">
                            🚨 Sirine Darurat (2)
                        </button>
                        <button type="button" @click="soundboardCategory = 'upacara'" :class="soundboardCategory === 'upacara' ? 'bg-purple-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer">
                            🎺 Upacara & Peluit (3)
                        </button>
                        <button type="button" @click="soundboardCategory = 'pengumuman'" :class="soundboardCategory === 'pengumuman' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer">
                            🗣️ Pengumuman KBM (4)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Grid 12 Jenis Soundboard -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($instantSounds as $sound)
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 hover:border-indigo-400 dark:hover:border-indigo-500 shadow-xs hover:shadow-md transition flex flex-col justify-between"
                         x-show="matchSoundboard({{ json_encode($sound) }})">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-3xl">{{ $sound['icon'] }}</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                    {{ $sound['badge'] }}
                                </span>
                            </div>
                            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white leading-snug">{{ $sound['name'] }}</h4>
                            <div class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mt-0.5">{{ $sound['category'] }}</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                                {{ $sound['description'] }}
                            </p>
                            @if(!empty($sound['voice_text']))
                                <div class="mt-2.5 p-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[10.5px] text-amber-700 dark:text-amber-300 font-medium italic line-clamp-2">
                                    "{{ $sound['voice_text'] }}"
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 mt-4 border-t border-slate-200/70 dark:border-slate-700/70 flex items-center gap-2">
                            <button type="button" @click="playInstantSound('{{ $sound['sound_type'] }}', '{{ addslashes($sound['name']) }}', '{{ addslashes($sound['voice_text'] ?? '') }}')" class="flex-1 py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>▶️ Bunyikan</span>
                            </button>
                            <button type="button" @click="setInstantSoundAsBell({{ json_encode($sound) }})" class="p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 transition cursor-pointer" title="Jadikan Jadwal Bel Otomatis">
                                <span>⏰ + Bel</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- TAB 4: LAGU-LAGU NASIONAL (10 LAGU) -->
    <div x-show="activeTab === 'anthems'" class="space-y-6">
        <!-- Floating / Sticky Anthem Player Card -->
        <div class="p-5 rounded-3xl bg-gradient-to-r from-red-900 via-rose-950 to-slate-900 text-white shadow-xl border border-red-500/30">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-red-500/20 border border-red-400/30 flex items-center justify-center text-3xl shrink-0">
                        🇮🇩
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-red-300">AUDIO LAGU NASIONAL INDONESIA</div>
                        <h2 class="text-lg font-black text-white" x-text="currentAnthemTitle ? currentAnthemTitle : 'Pilih Lagu Nasional untuk Memutar'"></h2>
                        <div class="text-xs text-red-200 mt-0.5" x-text="currentAnthemComposer ? currentAnthemComposer + ' • ' + currentAnthemUsage : '10 Lagu Nasional Siap untuk Bel Sekolah, Apel & Upacara Bendera'"></div>
                    </div>
                </div>

                <!-- Anthem Player Controls -->
                <div class="flex items-center gap-3">
                    <button type="button" @click="toggleAnthemPlay()" class="px-5 py-2.5 rounded-2xl bg-red-500 hover:bg-red-400 text-white font-black text-sm shadow-lg transition flex items-center gap-2 cursor-pointer">
                        <span x-text="isPlayingAnthem ? '⏸️ Jeda' : '▶️ Putar Lagu'"></span>
                    </button>
                    <button type="button" @click="stopAllAudio()" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition cursor-pointer" title="Berhenti">
                        ⏹️
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter & Header Lagu Nasional -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Kategori:</span>
                <button type="button" @click="anthemCategory = 'all'" :class="anthemCategory === 'all' ? 'bg-red-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer">
                    Semua (10 Lagu)
                </button>
                <button type="button" @click="anthemCategory = 'Lagu Kebangsaan'" :class="anthemCategory === 'Lagu Kebangsaan' ? 'bg-red-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer">
                    🇮🇩 Lagu Kebangsaan
                </button>
                <button type="button" @click="anthemCategory = 'Lagu Wajib Nasional'" :class="anthemCategory === 'Lagu Wajib Nasional' ? 'bg-red-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer">
                    🎖️ Lagu Wajib Nasional
                </button>
                <button type="button" @click="anthemCategory = 'Mars Perjuangan'" :class="anthemCategory === 'Mars Perjuangan' ? 'bg-red-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer">
                    ⚡ Mars Perjuangan
                </button>
            </div>
            <div class="text-xs text-slate-500">
                Lagu dapat dipasang otomatis sebagai jadwal bel masuk, istirahat, apel pagi, atau upacara.
            </div>
        </div>

        <!-- Grid 10 Lagu-Lagu Nasional -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($nationalAnthems as $idx => $anthem)
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs hover:border-red-500/50 hover:shadow-md transition flex flex-col justify-between"
                     x-show="anthemCategory === 'all' || anthemCategory === '{{ $anthem['category'] }}'">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 font-mono font-black text-sm flex items-center justify-center shrink-0">
                                    {{ $idx + 1 }}
                                </div>
                                <div>
                                    <h3 class="font-black text-sm sm:text-base text-slate-900 dark:text-white">{{ $anthem['title'] }}</h3>
                                    <div class="text-[11px] text-slate-500 font-medium">Cipt: {{ $anthem['composer'] }}</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-[9.5px] font-extrabold uppercase bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-900">
                                {{ $anthem['category'] }}
                            </span>
                        </div>

                        <div class="mt-3 space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
                            <div class="flex items-center gap-2 text-[11px]">
                                <span class="text-slate-400">Tempo:</span>
                                <span class="font-mono font-bold text-slate-700 dark:text-slate-200">{{ $anthem['tempo'] }}</span>
                            </div>
                            <div class="flex items-start gap-2 text-[11px]">
                                <span class="text-slate-400 shrink-0">Penggunaan:</span>
                                <span class="text-slate-700 dark:text-slate-200 font-medium">{{ $anthem['usage'] }}</span>
                            </div>
                        </div>

                        <!-- Lirik Preview -->
                        <div class="mt-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-[11px] text-slate-600 dark:text-slate-400 italic line-clamp-3 leading-relaxed">
                            "{!! nl2br(e(Str::limit($anthem['lyrics'], 110))) !!}"
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                        <button type="button" @click="playNationalAnthem({{ json_encode($anthem) }})" class="px-3 py-1.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                            <span>▶️ Putar Lagu</span>
                        </button>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="openLyricsModal({{ json_encode($anthem) }})" class="p-1.5 px-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition cursor-pointer" title="Lihat Lirik Lengkap">
                                <span>📜 Lirik</span>
                            </button>
                            <button type="button" @click="setAnthemAsBell({{ json_encode($anthem) }})" class="px-2.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold text-xs border border-amber-500/20 transition flex items-center gap-1 cursor-pointer" title="Pasang ke Jadwal Bel">
                                <span>⏰ Jadikan Bel</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL TAMBAH / EDIT JADWAL BEL -->
    <div id="bell-modal" x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs" style="display: none;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span x-text="isEdit ? '✏️ Edit Jadwal Bel' : '🔔 Tambah Jadwal Bel Baru'"></span>
                </h3>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-white text-lg p-1">✕</button>
            </div>

            <form :action="isEdit ? editUrl : '{{ route('school_bell.store') }}'" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PUT" x-bind:disabled="!isEdit">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul / Peristiwa Bel <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" x-model="formData.title" required placeholder="Contoh: Bel Masuk Kelas Pagi" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Bunyi (WIB) <span class="text-rose-500">*</span></label>
                        <input type="time" name="time" x-model="formData.time" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none font-mono font-bold text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Bunyi <span class="text-rose-500">*</span></label>
                        <select name="sound_type" x-model="formData.sound_type" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                            <option value="chime">🔔 Nada Chime Bel Standar</option>
                            <option value="soundboard">🎛️ Soundboard / Efek Suara Instan (12 Jenis)</option>
                            <option value="murottal">📖 Murottal Al-Qur'an 114 Surah</option>
                            <option value="voice">🗣️ Narasi Suara Pengumuman Saja</option>
                            <option value="siren">🚨 Sirine Peringatan / Darurat</option>
                            <option value="custom">🎵 File Audio Suara Bel Kustom</option>
                            <option value="song">🎶 Lagu / Instrumen / Mars Sekolah</option>
                        </select>
                    </div>
                </div>

                <!-- Teks Narasi Pengumuman Suara -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Teks Narasi Pengumuman Suara (Dibacakan Otomatis)</label>
                    <input type="text" name="voice_text" x-model="formData.voice_text" placeholder="Contoh: Saatnya jam pelajaran pertama dimulai. Selamat belajar." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                    <p class="text-[10px] text-slate-400 mt-1">Sistem akan membacakan teks ini dalam Bahasa Indonesia yang jernih saat bel berbunyi.</p>
                </div>

                <!-- Pilihan 12 Suara Soundboard Instan jika sound_type = soundboard -->
                <div x-show="formData.sound_type === 'soundboard'" class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <label class="block text-xs font-bold text-indigo-900 dark:text-indigo-300">Pilih Efek Suara Soundboard (12 Pilihan Siap Bunyi)</label>
                            <p class="text-[11px] text-indigo-700/80 dark:text-indigo-400/80">Pilih dari 12 nada bel, lonceng, sirine darurat, peluit, trompet, gong, atau narasi otomatis.</p>
                        </div>
                        <input type="hidden" name="soundboard_id" :value="formData.soundboard_id">
                    </div>

                    <!-- Kategori Filter Soundboard di Modal -->
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <button type="button" @click="soundboardModalCategory = 'all'" :class="soundboardModalCategory === 'all' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold border border-indigo-200 dark:border-indigo-800 transition cursor-pointer">
                            Semua (12)
                        </button>
                        <button type="button" @click="soundboardModalCategory = 'bel'" :class="soundboardModalCategory === 'bel' ? 'bg-amber-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold border border-indigo-200 dark:border-indigo-800 transition cursor-pointer">
                            🔔 Bel & Interkom (3)
                        </button>
                        <button type="button" @click="soundboardModalCategory = 'darurat'" :class="soundboardModalCategory === 'darurat' ? 'bg-rose-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold border border-indigo-200 dark:border-indigo-800 transition cursor-pointer">
                            🚨 Sirine (2)
                        </button>
                        <button type="button" @click="soundboardModalCategory = 'upacara'" :class="soundboardModalCategory === 'upacara' ? 'bg-purple-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold border border-indigo-200 dark:border-indigo-800 transition cursor-pointer">
                            🎺 Upacara & Sinyal (3)
                        </button>
                        <button type="button" @click="soundboardModalCategory = 'pengumuman'" :class="soundboardModalCategory === 'pengumuman' ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold border border-indigo-200 dark:border-indigo-800 transition cursor-pointer">
                            🗣️ Pengumuman Narasi (4)
                        </button>
                    </div>

                    <!-- Grid 12 Suara -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto p-1">
                        @foreach($instantSounds as $s)
                            <div class="p-3 rounded-xl border cursor-pointer transition flex items-start gap-2.5 relative select-none"
                                 x-show="matchSoundboardModalCategory('{{ $s['category'] }}')"
                                 :class="formData.soundboard_id === '{{ $s['id'] }}' ? 'bg-indigo-100 dark:bg-indigo-950/80 border-indigo-500 ring-2 ring-indigo-500/30' : 'bg-white dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 hover:border-indigo-300'"
                                 @click="selectSoundboardOption({{ json_encode($s) }})">
                                <span class="text-2xl shrink-0 mt-0.5">{{ $s['icon'] }}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <h5 class="text-xs font-extrabold text-slate-900 dark:text-white truncate">{{ $s['name'] }}</h5>
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 shrink-0">{{ $s['badge'] }}</span>
                                    </div>
                                    <p class="text-[10.5px] text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">{{ $s['description'] }}</p>
                                    <div class="flex items-center justify-between mt-2 pt-1 border-t border-slate-100 dark:border-slate-700/50">
                                        <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 truncate">{{ $s['category'] }}</span>
                                        <button type="button" @click.stop="playInstantSound('{{ $s['sound_type'] }}', '{{ addslashes($s['name']) }}', '{{ addslashes($s['voice_text'] ?? '') }}')" class="text-[10.5px] font-bold text-indigo-600 dark:text-indigo-300 hover:underline flex items-center gap-1 cursor-pointer shrink-0">
                                            <span>▶️ Dengar</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pilihan Surah Murottal jika tipe sound_type = murottal (Multi-Select Didukung) -->
                <div x-show="formData.sound_type === 'murottal'" class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <label class="block text-xs font-bold text-emerald-800 dark:text-emerald-300">Pilih Surat Al-Qur'an (Bisa Pilih Lebih Dari 1 Surat)</label>
                            <p class="text-[11px] text-emerald-700/80 dark:text-emerald-400/80">Jika memilih lebih dari 1 surat, sistem akan memutar surat-surat tersebut secara berurutan (playlist otomatis).</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-emerald-600 text-white shadow-xs shrink-0" x-text="formData.surah_numbers.length + ' Surat Dipilih'"></span>
                    </div>

                    <!-- Shortcut Pilihan Cepat -->
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mr-1">Pilih Cepat:</span>
                        <button type="button" @click="selectJuz30Surahs()" class="px-2 py-1 rounded-lg bg-emerald-600/15 hover:bg-emerald-600 text-emerald-800 hover:text-white dark:text-emerald-300 text-[10px] font-bold border border-emerald-500/30 transition cursor-pointer">
                            + Juz 30 (37 Surat)
                        </button>
                        <button type="button" @click="select3QulSurahs()" class="px-2 py-1 rounded-lg bg-emerald-600/15 hover:bg-emerald-600 text-emerald-800 hover:text-white dark:text-emerald-300 text-[10px] font-bold border border-emerald-500/30 transition cursor-pointer">
                            + 3 Qul (Ikhlas, Falaq, Nas)
                        </button>
                        <button type="button" @click="selectFeaturedSurahs()" class="px-2 py-1 rounded-lg bg-emerald-600/15 hover:bg-emerald-600 text-emerald-800 hover:text-white dark:text-emerald-300 text-[10px] font-bold border border-emerald-500/30 transition cursor-pointer">
                            + Surat Utama (Fatihah, Yasin, Rahman, Mulk, dll)
                        </button>
                        <button type="button" @click="clearSurahSelection()" class="px-2 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white text-[10px] font-bold border border-rose-500/30 transition cursor-pointer" x-show="formData.surah_numbers.length > 0">
                            ✕ Hapus Pilihan
                        </button>
                    </div>

                    <!-- Filter Pencarian di dalam Modal -->
                    <div class="relative">
                        <input type="text" x-model="surahModalSearch" placeholder="Cari nama atau nomor surat..." class="w-full px-3 py-1.5 pl-8 text-xs bg-white dark:bg-slate-800 border border-emerald-300 dark:border-emerald-700 rounded-xl outline-none text-slate-900 dark:text-white">
                        <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                    </div>

                    <!-- Daftar 114 Surat dengan Checkbox Multi-Pilihan -->
                    <div class="max-h-48 overflow-y-auto p-2 rounded-xl bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-800/60 grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-xs">
                        @foreach($allSurahs as $surah)
                            <label class="flex items-center gap-2 p-1.5 rounded-lg border cursor-pointer select-none transition"
                                   :class="formData.surah_numbers.includes({{ $surah['number'] }}) ? 'bg-emerald-100 dark:bg-emerald-950/70 border-emerald-500 font-bold text-emerald-900 dark:text-emerald-200' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700/60 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
                                   x-show="matchModalSurah({{ $surah['number'] }}, '{{ addslashes($surah['name']) }}')">
                                <input type="checkbox" name="surah_numbers[]" value="{{ $surah['number'] }}"
                                       :checked="formData.surah_numbers.includes({{ $surah['number'] }})"
                                       @change="toggleSurahSelection({{ $surah['number'] }})"
                                       class="rounded text-emerald-600 focus:ring-0">
                                <span class="font-mono text-[10px] text-emerald-600 dark:text-emerald-400">{{ $surah['number'] }}.</span>
                                <span class="truncate flex-1">{{ $surah['name'] }}</span>
                                <span class="font-arabic text-xs text-slate-500 dark:text-slate-400">{{ $surah['arabic'] }}</span>
                            </label>
                        @endforeach
                    </div>

                    <!-- Hidden input fallback untuk single surah_number -->
                    <input type="hidden" name="surah_number" :value="formData.surah_numbers[0] || ''">

                    <!-- Preview Badge Surat Terpilih -->
                    <template x-if="formData.surah_numbers.length > 0">
                        <div class="p-2.5 rounded-xl bg-emerald-100/60 dark:bg-emerald-950/30 border border-emerald-300 dark:border-emerald-800">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 mb-1.5 flex items-center justify-between">
                                <span>Urutan Pemutaran Playlist Murottal:</span>
                                <span class="font-mono" x-text="formData.surah_numbers.length + ' surat'"></span>
                            </div>
                            <div class="flex flex-wrap gap-1 max-h-24 overflow-y-auto">
                                <template x-for="(sNum, idx) in formData.surah_numbers" :key="sNum">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-600 text-white text-[10px] font-semibold">
                                        <span x-text="(idx + 1) + '. Surat Ke-' + sNum"></span>
                                        <button type="button" @click="toggleSurahSelection(sNum)" class="hover:text-amber-200 cursor-pointer">×</button>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Pilihan Lagu Nasional jika tipe sound_type = song (Multi-Select Didukung) -->
                <div x-show="formData.sound_type === 'song'" class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <label class="block text-xs font-bold text-red-800 dark:text-red-300">Pilih Lagu Nasional Indonesia (Bisa Pilih Lebih Dari 1 Lagu)</label>
                            <p class="text-[11px] text-red-700/80 dark:text-red-400/80">Jika memilih beberapa lagu, sistem akan memutar lagu-lagu tersebut secara berurutan (playlist lagu nasional).</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-red-600 text-white shadow-xs shrink-0" x-text="formData.anthem_ids.length + ' Lagu Dipilih'"></span>
                    </div>

                    <!-- Shortcut Pilihan Cepat Lagu -->
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <button type="button" @click="selectAllAnthems([{{ collect($nationalAnthems)->map(fn($a) => "'" . $a['id'] . "'")->join(',') }}])" class="px-2 py-1 rounded-lg bg-red-600/15 hover:bg-red-600 text-red-800 hover:text-white dark:text-red-300 text-[10px] font-bold border border-red-500/30 transition cursor-pointer">
                            + Pilih Semua (10 Lagu)
                        </button>
                        <button type="button" @click="clearAnthemSelection()" class="px-2 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white text-[10px] font-bold border border-rose-500/30 transition cursor-pointer" x-show="formData.anthem_ids.length > 0">
                            ✕ Hapus Pilihan
                        </button>
                    </div>

                    <!-- Daftar 10 Lagu Nasional dengan Checkbox Multi-Pilihan -->
                    <div class="max-h-48 overflow-y-auto p-2 rounded-xl bg-white dark:bg-slate-900 border border-red-200 dark:border-red-800/60 grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-xs">
                        @foreach($nationalAnthems as $anthem)
                            <label class="flex items-start gap-2 p-2 rounded-lg border cursor-pointer select-none transition"
                                   :class="formData.anthem_ids.includes('{{ $anthem['id'] }}') ? 'bg-red-100 dark:bg-red-950/70 border-red-500 font-bold text-red-900 dark:text-red-200' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700/60 text-slate-700 dark:text-slate-300 hover:bg-slate-100'">
                                <input type="checkbox" name="anthem_ids[]" value="{{ $anthem['id'] }}"
                                       :checked="formData.anthem_ids.includes('{{ $anthem['id'] }}')"
                                       @change="toggleAnthemSelection('{{ $anthem['id'] }}')"
                                       class="rounded text-red-600 focus:ring-0 mt-0.5">
                                <div class="truncate flex-1">
                                    <div class="font-bold truncate">{{ $anthem['title'] }}</div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">Cipt: {{ $anthem['composer'] }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- Hidden input fallback untuk single anthem_id -->
                    <input type="hidden" name="anthem_id" :value="formData.anthem_ids[0] || ''">

                    <!-- Preview Badge Lagu Terpilih -->
                    <template x-if="formData.anthem_ids.length > 0">
                        <div class="p-2.5 rounded-xl bg-red-100/60 dark:bg-red-950/30 border border-red-300 dark:border-red-800">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-red-800 dark:text-red-300 mb-1.5 flex items-center justify-between">
                                <span>Urutan Pemutaran Playlist Lagu:</span>
                                <span class="font-mono" x-text="formData.anthem_ids.length + ' lagu'"></span>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="(aid, idx) in formData.anthem_ids" :key="aid">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-600 text-white text-[10px] font-semibold">
                                        <span x-text="(idx + 1) + '. ' + aid"></span>
                                        <button type="button" @click="toggleAnthemSelection(aid)" class="hover:text-amber-200 cursor-pointer">×</button>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- File Audio Kustom jika sound_type = custom atau song -->
                <div x-show="formData.sound_type === 'custom' || formData.sound_type === 'song'" class="p-3.5 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 space-y-2">
                    <label class="block text-xs font-bold text-indigo-800 dark:text-indigo-300">Upload File Audio Bel atau Lagu (MP3, WAV, OGG, M4A, AAC)</label>
                    <input type="file" name="audio_file" accept=".mp3,.wav,.ogg,.m4a,.aac,audio/*" class="w-full text-xs text-slate-600 dark:text-slate-300">
                    <p class="text-[10px] text-slate-500">Maksimal 25MB. Jika memilih lagu nasional di atas dan tidak mengunggah file, sistem otomatis menggunakan audio resmi lagu nasional tersebut.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Hari Berlaku <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold cursor-pointer select-none hover:bg-slate-100 dark:hover:bg-slate-700/50 transition">
                                <input type="checkbox" name="days[]" value="{{ $day }}" x-model="formData.days" class="rounded text-indigo-600 focus:ring-0">
                                <span>{{ $day }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Durasi Putar (Detik)</label>
                        <input type="number" name="duration_seconds" x-model="formData.duration_seconds" min="5" max="3600" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Volume Bunyi (10 - 100%)</label>
                        <input type="number" name="volume" x-model="formData.volume" min="10" max="100" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan Tambahan</label>
                    <textarea name="description" x-model="formData.description" rows="2" placeholder="Catatan kegiatan saat bel berbunyi..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition">
                        Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL LIRIK LAGU NASIONAL -->
    <div x-show="showLyricsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-xs" style="display: none;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🇮🇩</span>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white" x-text="selectedAnthem?.title || 'Lirik Lagu Nasional'"></h3>
                        <p class="text-xs text-slate-500" x-text="selectedAnthem ? ('Ciptaan: ' + selectedAnthem.composer) : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showLyricsModal = false" class="text-slate-400 hover:text-white text-lg p-1 cursor-pointer">✕</button>
            </div>

            <div class="space-y-4">
                <div class="p-3.5 rounded-2xl bg-red-500/10 border border-red-500/20 text-xs text-red-700 dark:text-red-300 flex items-center justify-between">
                    <div>
                        <span class="font-bold">Tempo:</span> <span x-text="selectedAnthem?.tempo"></span>
                    </div>
                    <div>
                        <span class="font-bold">Kategori:</span> <span x-text="selectedAnthem?.category"></span>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 font-medium text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line text-center" x-text="selectedAnthem?.lyrics">
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2">
                    <button type="button" @click="if (selectedAnthem) { playNationalAnthem(selectedAnthem); showLyricsModal = false; }" class="justify-center px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <span>▶️ Putar Lagu Ini</span>
                    </button>
                    <button type="button" @click="if (selectedAnthem) { setAnthemAsBell(selectedAnthem); showLyricsModal = false; }" class="justify-center px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <span>⏰ Pasang Jadwal Bel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- SMART WEB AUDIO & RELIABLE AUTO-SCHEDULER SCRIPT -->
<script>
function schoolBellApp() {
    return {
        activeTab: 'schedules',
        filterDay: 'Semua',
        surahSearch: '',
        surahCategory: 'all',
        soundboardCategory: 'all',
        soundboardSearch: '',
        soundboardModalCategory: 'all',
        anthemCategory: 'all',

        currentTimeDisplay: '{{ $currentTime }}:00 WIB',
        currentDateDisplay: '{{ $todayName }}, {{ date('d M Y') }}',

        // Standby & Audio State
        isAutoStandbyActive: false,
        enableVoiceAnnouncement: true,
        masterVolume: 100,
        isPlaying: false,
        isPlayingMurottal: false,
        isPlayingAnthem: false,
        currentAudioTitle: '',
        currentSurahName: '',
        currentSurahDetails: '114 Surah Tersedia dengan Audio Streaming Berkualitas Tinggi',
        currentAnthemTitle: '',
        currentAnthemComposer: '',
        currentAnthemUsage: '',
        currentAnthemObject: null,
        audioCurrentTimeFormatted: '00:00',
        audioDurationFormatted: '00:00',
        audioProgressPercent: 0,
        audioElement: null,
        audioCtx: null,

        // In-memory schedules for instant reliability (No network delays)
        allBells: @json($allBells),
        todayBells: @json($todayBells),
        lastRungMinute: '',
        nextBellTitle: '{{ $nextBell?->title ?? '' }}',
        nextBellTime: '{{ $nextBell?->time ?? '' }}',
        nextBellCountdownText: '',
        wakeLockSentinel: null,

        // Modal
        showModal: false,
        isEdit: false,
        editUrl: '',
        showLyricsModal: false,
        selectedAnthem: null,
        surahModalSearch: '',
        playlistQueue: [],
        currentTrackIndex: 0,
        isPlaylistMode: false,
        formData: {
            title: '',
            time: '07:00',
            sound_type: 'chime',
            soundboard_id: 'chime-westminster',
            surah_number: '',
            surah_numbers: [],
            anthem_id: '',
            anthem_ids: [],
            voice_text: '',
            days: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
            duration_seconds: 15,
            volume: 100,
            description: ''
        },

        init() {
            window.bellAppInstance = this;
            this.audioElement = new Audio();
            this.audioElement.addEventListener('timeupdate', () => {
                if (this.audioElement.duration) {
                    this.audioProgressPercent = (this.audioElement.currentTime / this.audioElement.duration) * 100;
                    this.audioCurrentTimeFormatted = this.formatTime(this.audioElement.currentTime);
                    this.audioDurationFormatted = this.formatTime(this.audioElement.duration);
                }
            });
            this.audioElement.addEventListener('ended', () => {
                if (this.isPlaylistMode && this.playlistQueue.length > 0 && this.currentTrackIndex < this.playlistQueue.length - 1) {
                    this.currentTrackIndex++;
                    this.playTrackFromQueue(this.currentTrackIndex);
                } else {
                    this.isPlaylistMode = false;
                    this.isPlaying = false;
                    this.isPlayingMurottal = false;
                    this.isPlayingAnthem = false;
                    this.audioProgressPercent = 0;
                }
            });

            // Start High-Resolution Clock and Non-Throttled Auto-Scheduler Interval
            setInterval(() => {
                this.tickClock();
            }, 1000);

            this.updateNextBellCountdown();
        },

        tickClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const currentMinuteStr = `${hours}:${minutes}`;

            this.currentTimeDisplay = `${currentMinuteStr}:${seconds} WIB`;

            this.updateNextBellCountdown();

            // Scheduler trigger: periksa apakah menit ini cocok dengan jadwal aktif hari ini
            if (this.isAutoStandbyActive && this.lastRungMinute !== currentMinuteStr) {
                const match = this.todayBells.find(b => b.is_active && b.time === currentMinuteStr);
                if (match) {
                    this.lastRungMinute = currentMinuteStr;
                    this.playBellRecord(match);
                }
            }
        },

        updateNextBellCountdown() {
            if (!this.nextBellTime) {
                const now = new Date();
                const nowMin = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                const next = this.todayBells.find(b => b.is_active && b.time > nowMin) || this.todayBells[0];
                if (next) {
                    this.nextBellTitle = next.title;
                    this.nextBellTime = next.time;
                }
            }

            if (this.nextBellTime) {
                const now = new Date();
                const [targetH, targetM] = this.nextBellTime.split(':').map(Number);
                const target = new Date();
                target.setHours(targetH, targetM, 0, 0);

                let diffMs = target.getTime() - now.getTime();
                if (diffMs < 0) {
                    diffMs += 24 * 60 * 60 * 1000;
                }

                const diffSec = Math.floor(diffMs / 1000);
                const hrs = Math.floor(diffSec / 3600);
                const mins = Math.floor((diffSec % 3600) / 60);
                const secs = diffSec % 60;

                if (hrs > 0) {
                    this.nextBellCountdownText = `Pukul ${this.nextBellTime} (${hrs}j ${mins}m lagi)`;
                } else {
                    this.nextBellCountdownText = `Pukul ${this.nextBellTime} (${mins}m ${String(secs).padStart(2, '0')}s lagi)`;
                }
            }
        },

        async toggleAutoStandby() {
            this.isAutoStandbyActive = !this.isAutoStandbyActive;

            if (this.isAutoStandbyActive) {
                this.initAudioContext();
                this.testChimeSound('twotone', 'Mode siaga bel otomatis telah aktif.');

                try {
                    if ('wakeLock' in navigator) {
                        this.wakeLockSentinel = await navigator.wakeLock.request('screen');
                    }
                } catch (e) {
                    console.log('WakeLock error:', e);
                }
            } else {
                this.stopAllAudio();
                if (this.wakeLockSentinel) {
                    this.wakeLockSentinel.release();
                    this.wakeLockSentinel = null;
                }
            }
        },

        initAudioContext() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!this.audioCtx) {
                    this.audioCtx = new AudioContext();
                }
                if (this.audioCtx.state === 'suspended') {
                    this.audioCtx.resume();
                }
            } catch (e) {
                console.error(e);
            }
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = Math.floor(seconds % 60);
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        },

        seekAudio(event) {
            if (!this.audioElement || !this.audioElement.duration) return;
            const rect = event.currentTarget.getBoundingClientRect();
            const clickX = event.clientX - rect.left;
            const percent = clickX / rect.width;
            this.audioElement.currentTime = percent * this.audioElement.duration;
        },

        playBellRecord(bell) {
            this.stopAllAudio();
            this.isPlaying = true;
            this.currentAudioTitle = bell.title;

            const vol = (bell.volume || 100) * (this.masterVolume / 100) / 100;
            const items = bell.playlist_items || [];

            // Jika memiliki playlist multi-surah atau multi-lagu
            if (items.length > 0) {
                this.playlistQueue = items;
                this.currentTrackIndex = 0;
                this.isPlaylistMode = items.length > 1;

                if (bell.voice_text) {
                    this.testChimeSound('westminster');
                    if (this.enableVoiceAnnouncement) {
                        setTimeout(() => {
                            this.testSpeech(bell.voice_text);
                        }, 2800);
                        setTimeout(() => {
                            this.playTrackFromQueue(0, bell.volume);
                        }, 5800);
                    } else {
                        setTimeout(() => {
                            this.playTrackFromQueue(0, bell.volume);
                        }, 2800);
                    }
                } else {
                    this.playTrackFromQueue(0, bell.volume);
                }
            } else if (bell.sound_type === 'murottal' && bell.audio_url) {
                if (bell.voice_text) {
                    this.testChimeSound('westminster');
                    if (this.enableVoiceAnnouncement) {
                        setTimeout(() => { this.testSpeech(bell.voice_text); }, 2800);
                        setTimeout(() => { this.playMurottalUrl(bell.audio_url, bell.surah_name || bell.title); }, 5800);
                    } else {
                        setTimeout(() => { this.playMurottalUrl(bell.audio_url, bell.surah_name || bell.title); }, 2800);
                    }
                } else {
                    this.playMurottalUrl(bell.audio_url, bell.surah_name || bell.title);
                }
            } else if (bell.sound_type === 'siren') {
                this.testChimeSound('siren');
            } else if ((bell.sound_type === 'custom' || bell.sound_type === 'song') && bell.audio_url) {
                if (bell.voice_text) {
                    this.testChimeSound('westminster');
                    if (this.enableVoiceAnnouncement) {
                        setTimeout(() => { this.testSpeech(bell.voice_text); }, 2800);
                        setTimeout(() => {
                            this.audioElement.src = bell.audio_url;
                            this.audioElement.volume = vol;
                            this.audioElement.play().catch(e => console.error(e));
                        }, 5800);
                    } else {
                        setTimeout(() => {
                            this.audioElement.src = bell.audio_url;
                            this.audioElement.volume = vol;
                            this.audioElement.play().catch(e => console.error(e));
                        }, 2800);
                    }
                } else {
                    this.audioElement.src = bell.audio_url;
                    this.audioElement.volume = vol;
                    this.audioElement.play().catch(e => console.error(e));
                }
            } else if (bell.sound_type === 'soundboard') {
                const instantType = bell.playlist?.[0]?.sound_type || (bell.audio_url && bell.audio_url.startsWith('soundboard:') ? bell.audio_url.replace('soundboard:', '') : 'westminster');
                this.playInstantSound(instantType, bell.title, bell.voice_text);
            } else {
                this.testChimeSound('westminster');

                if (this.enableVoiceAnnouncement && bell.voice_text) {
                    setTimeout(() => {
                        this.testSpeech(bell.voice_text);
                    }, 2800);
                }
            }

            if (bell.duration_seconds && bell.duration_seconds > 0) {
                setTimeout(() => {
                    if (this.isPlaying && this.currentAudioTitle === bell.title) {
                        this.stopAllAudio();
                    }
                }, bell.duration_seconds * 1000);
            }
        },

        playTrackFromQueue(index, bellVolume = 100) {
            if (!this.playlistQueue[index]) return;
            const track = this.playlistQueue[index];
            const vol = (bellVolume || 100) * (this.masterVolume / 100) / 100;
            this.isPlaying = true;
            this.currentAudioTitle = `[Trek ${index + 1}/${this.playlistQueue.length}] ${track.title}`;

            if (track.type === 'murottal') {
                this.isPlayingMurottal = true;
                this.currentSurahName = track.title;
            } else {
                this.isPlayingAnthem = true;
                this.currentAnthemTitle = track.title;
            }

            this.audioElement.src = track.audio_url;
            this.audioElement.volume = vol;
            this.audioElement.play().catch(e => console.error(e));
        },

        playSingleTrack(track, bellVolume = 100, customTitle = null) {
            const vol = (bellVolume || 100) * (this.masterVolume / 100) / 100;
            this.currentAudioTitle = customTitle || track.title;

            if (track.type === 'murottal') {
                this.isPlayingMurottal = true;
                this.currentSurahName = track.title;
            } else {
                this.isPlayingAnthem = true;
                this.currentAnthemTitle = track.title;
            }

            this.audioElement.src = track.audio_url;
            this.audioElement.volume = vol;
            this.audioElement.play().catch(e => console.error(e));
        },

        nextTrackInPlaylist() {
            if (this.isPlaylistMode && this.currentTrackIndex < this.playlistQueue.length - 1) {
                this.currentTrackIndex++;
                this.playTrackFromQueue(this.currentTrackIndex);
            }
        },

        prevTrackInPlaylist() {
            if (this.isPlaylistMode && this.currentTrackIndex > 0) {
                this.currentTrackIndex--;
                this.playTrackFromQueue(this.currentTrackIndex);
            }
        },

        testSpeech(text) {
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.95;
            utterance.pitch = 1.0;
            utterance.volume = this.masterVolume / 100;

            const voices = window.speechSynthesis.getVoices();
            const idVoice = voices.find(v => v.lang.startsWith('id') || v.lang.startsWith('in'));
            if (idVoice) {
                utterance.voice = idVoice;
            }

            window.speechSynthesis.speak(utterance);
        },

        playSurahDirect(surah) {
            this.stopAllAudio();
            this.activeTab = 'quran';
            this.currentSurahName = `Surah ${surah.number}. ${surah.name} (${surah.arabic})`;
            this.currentSurahDetails = `Arti: ${surah.translation} • ${surah.ayah_count} Ayat • ${surah.type} (Juz ${surah.juz})`;
            this.playMurottalUrl(surah.audio_url, surah.name);
        },

        playMurottalUrl(url, name) {
            this.isPlaying = true;
            this.isPlayingMurottal = true;
            this.currentAudioTitle = `Murottal: ${name}`;
            this.audioElement.src = url;
            this.audioElement.volume = this.masterVolume / 100;

            const playPromise = this.audioElement.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.log('Audio stream fallback switch:', error);
                    if (url.includes('cdn.islamic.network')) {
                        const surahMatch = url.match(/\/(\d+)\.mp3$/);
                        if (surahMatch) {
                            const padded = String(surahMatch[1]).padStart(3, '0');
                            this.audioElement.src = `https://download.quranicaudio.com/quran/mishari_rashid_al-3afasi/${padded}.mp3`;
                            this.audioElement.play().catch(e => console.error(e));
                        }
                    }
                });
            }
        },

        toggleMurottalPlay() {
            if (!this.audioElement.src) {
                const defaultSurah = { number: 55, name: 'Ar-Rahman', arabic: 'الرحمن', translation: 'Maha Pemurah', ayah_count: 78, type: 'Madaniyah', juz: 27, audio_url: 'https://cdn.islamic.network/quran/audio-surah/128/ar.alafasy/55.mp3' };
                this.playSurahDirect(defaultSurah);
                return;
            }

            if (this.audioElement.paused) {
                this.audioElement.play();
                this.isPlaying = true;
                this.isPlayingMurottal = true;
            } else {
                this.audioElement.pause();
                this.isPlaying = false;
                this.isPlayingMurottal = false;
            }
        },

        playNationalAnthem(anthem) {
            this.stopAllAudio();
            this.initAudioContext();
            this.currentAnthemTitle = anthem.title;
            this.currentAnthemComposer = 'Cipt: ' + anthem.composer;
            this.currentAnthemUsage = anthem.usage;
            this.currentAnthemObject = anthem;
            this.currentAudioTitle = `Lagu: ${anthem.title}`;
            this.isPlaying = true;
            this.isPlayingAnthem = true;

            this.audioElement.src = anthem.audio_url;
            this.audioElement.volume = this.masterVolume / 100;

            const playPromise = this.audioElement.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.log('Anthem audio stream fallback switch:', error);
                    if (anthem.audio_fallback) {
                        this.audioElement.src = anthem.audio_fallback;
                        this.audioElement.play().catch(() => this.playSynthMelody(anthem.melody_notes));
                    } else {
                        this.playSynthMelody(anthem.melody_notes);
                    }
                });
            }
        },

        toggleAnthemPlay() {
            if (!this.currentAnthemObject) {
                const defaultAnthem = @json($nationalAnthems[0] ?? null);
                if (defaultAnthem) {
                    this.playNationalAnthem(defaultAnthem);
                }
                return;
            }

            if (this.audioElement.src && !this.audioElement.paused) {
                this.audioElement.pause();
                this.isPlaying = false;
                this.isPlayingAnthem = false;
            } else if (this.audioElement.src) {
                this.audioElement.play();
                this.isPlaying = true;
                this.isPlayingAnthem = true;
            }
        },

        playSynthMelody(notes) {
            if (!notes || !notes.length) return;
            this.initAudioContext();
            const ctx = this.audioCtx;
            let t = ctx.currentTime;
            const masterGain = ctx.createGain();
            masterGain.gain.value = (this.masterVolume / 100) * 0.35;
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
                this.isPlayingAnthem = false;
            }, (t - ctx.currentTime) * 1000);
        },

        // Play Instant Sounds (Soundboard - 12 Types)
        playInstantSound(type, name, text = null) {
            this.stopAllAudio();
            this.initAudioContext();
            this.isPlaying = true;
            this.currentAudioTitle = name;

            const ctx = this.audioCtx;
            const now = ctx.currentTime;
            const masterGain = ctx.createGain();
            masterGain.gain.setValueAtTime((this.masterVolume / 100) * 0.45, now);
            masterGain.connect(ctx.destination);

            if (type === 'whistle') {
                // High pitch whistle with 20Hz tremolo
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
                // Brass Horn Fanfare Arpeggio: C4, E4, G4, C5, G4, C5
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
                // Deep Bronze Gong: 110Hz + rich low harmonics with long resonance
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
                // Fire / Evacuation Siren
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sawtooth';
                gain.gain.setValueAtTime(0.3, now);
                let t = now;
                for (let i = 0; i < 7; i++) {
                    osc.frequency.setValueAtTime(600, t);
                    osc.frequency.linearRampToValueAtTime(950, t + 0.35);
                    t += 0.45;
                }
                gain.gain.setValueAtTime(0, t);
                osc.connect(gain);
                gain.connect(masterGain);
                osc.start(now);
                osc.stop(t);
                setTimeout(() => { this.isPlaying = false; }, (t - now) * 1000);
            } else if (type === 'siren_earthquake') {
                // Earthquake Siren: slow rising and falling pitch
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
                this.testChimeSound('threetone', text);
            } else if (type === 'twotone' || type === 'voice_break' || type === 'voice_prayer') {
                this.testChimeSound('twotone', text);
            } else {
                // Westminster default
                this.testChimeSound('westminster', text);
            }
        },

        stopAllAudio() {
            if (this.audioElement) {
                this.audioElement.pause();
                this.audioElement.currentTime = 0;
            }
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }
            this.isPlaying = false;
            this.isPlayingMurottal = false;
            this.isPlayingAnthem = false;
            this.currentAudioTitle = '';
        },

        testChimeSound(type, announcementText = null) {
            this.initAudioContext();
            this.stopAllAudio();
            this.isPlaying = true;
            this.currentAudioTitle = type === 'siren' ? 'Sirine Darurat' : 'Nada Bel Sekolah';

            try {
                const ctx = this.audioCtx;
                const gainMaster = ctx.createGain();
                gainMaster.gain.value = this.masterVolume / 100;
                gainMaster.connect(ctx.destination);

                if (type === 'siren') {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(gainMaster);
                    osc.type = 'sawtooth';
                    let t = ctx.currentTime;
                    gain.gain.setValueAtTime(0.25, t);
                    for (let i = 0; i < 6; i++) {
                        osc.frequency.setValueAtTime(650, t);
                        osc.frequency.exponentialRampToValueAtTime(950, t + 0.4);
                        t += 0.5;
                    }
                    gain.gain.setValueAtTime(0, t);
                    osc.start();
                    osc.stop(t);
                } else {
                    const notes = type === 'twotone' 
                        ? [659.25, 523.25] 
                        : (type === 'threetone' ? [523.25, 659.25, 783.99] : [659.25, 415.30, 739.99, 493.88, 659.25, 739.99, 830.61, 659.25]);

                    let t = ctx.currentTime;
                    notes.forEach((freq) => {
                        [1, 2.02].forEach((harmonic, hIdx) => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = hIdx === 0 ? 'sine' : 'triangle';
                            osc.frequency.value = freq * harmonic;
                            
                            const hGain = hIdx === 0 ? 0.4 : 0.12;
                            gain.gain.setValueAtTime(0.001, t);
                            gain.gain.exponentialRampToValueAtTime(hGain, t + 0.04);
                            gain.gain.exponentialRampToValueAtTime(0.0001, t + 1.25);
                            
                            osc.connect(gain);
                            gain.connect(gainMaster);
                            
                            osc.start(t);
                            osc.stop(t + 1.3);
                        });
                        t += 0.52;
                    });
                }

                if (announcementText && this.enableVoiceAnnouncement) {
                    setTimeout(() => {
                        this.testSpeech(announcementText);
                    }, (type === 'twotone' ? 1200 : (type === 'threetone' ? 1800 : 4200)));
                }

                setTimeout(() => {
                    if (!this.isPlayingMurottal && !this.isPlayingAnthem) {
                        this.isPlaying = false;
                    }
                }, 5000);
            } catch (e) {
                console.error(e);
                this.isPlaying = false;
            }
        },

        shouldShowBell(daysJson) {
            if (this.filterDay === 'Semua') return true;
            try {
                const days = JSON.parse(daysJson);
                return Array.isArray(days) && days.includes(this.filterDay);
            } catch (e) {
                return true;
            }
        },

        matchSurah(num, name, trans) {
            if (this.surahCategory === 'featured') {
                const featured = [1, 18, 36, 55, 56, 67, 78, 93, 112, 113, 114];
                if (!featured.includes(num)) return false;
            } else if (this.surahCategory === 'juz30') {
                if (num < 78) return false;
            }

            if (!this.surahSearch) return true;
            const q = this.surahSearch.toLowerCase();
            return String(num).includes(q) || name.toLowerCase().includes(q) || trans.toLowerCase().includes(q);
        },

        setAsBellSchedule(surah) {
            this.openAddModal();
            this.formData.title = `Murottal Surah ${surah.name}`;
            this.formData.sound_type = 'murottal';
            this.formData.surah_number = surah.number;
            this.formData.surah_numbers = [Number(surah.number)];
            this.formData.voice_text = `Murottal Al-Qur'an Surah ${surah.name}.`;
            this.formData.duration_seconds = 900;
            this.formData.description = `Murottal Al-Qur'an Surah ${surah.name} (${surah.translation})`;
        },

        setAnthemAsBell(anthem) {
            this.openAddModal();
            this.formData.title = `Lagu: ${anthem.title}`;
            this.formData.sound_type = 'song';
            this.formData.anthem_id = anthem.id;
            this.formData.anthem_ids = [anthem.id];
            this.formData.voice_text = `Memperdengarkan Lagu Nasional: ${anthem.title}.`;
            this.formData.duration_seconds = 180;
            this.formData.description = `${anthem.usage} (Cipt: ${anthem.composer})`;
        },

        setInstantSoundAsBell(sound) {
            this.openAddModal();
            this.formData.title = sound.name;
            this.formData.sound_type = 'soundboard';
            this.formData.soundboard_id = sound.id;
            this.formData.voice_text = sound.voice_text || '';
            this.formData.duration_seconds = sound.voice_text ? 30 : 15;
            this.formData.description = sound.description;
        },

        matchSoundboard(sound) {
            let catMatch = false;
            if (this.soundboardCategory === 'all') {
                catMatch = true;
            } else if (this.soundboardCategory === 'bel') {
                catMatch = ['Bel Jam Pelajaran', 'Bel Pengumuman', 'Bel Interkom'].includes(sound.category);
            } else if (this.soundboardCategory === 'darurat') {
                catMatch = ['Tanggap Darurat'].includes(sound.category);
            } else if (this.soundboardCategory === 'upacara') {
                catMatch = ['Tata Tertib Lapangan', 'Upacara & Seremoni'].includes(sound.category);
            } else if (this.soundboardCategory === 'pengumuman') {
                catMatch = ['Otomasi KBM', 'Ibadah & Karakter'].includes(sound.category);
            }

            if (!catMatch) return false;

            if (!this.soundboardSearch) return true;
            const q = this.soundboardSearch.toLowerCase();
            return (sound.name && sound.name.toLowerCase().includes(q)) ||
                   (sound.category && sound.category.toLowerCase().includes(q)) ||
                   (sound.description && sound.description.toLowerCase().includes(q)) ||
                   (sound.voice_text && sound.voice_text.toLowerCase().includes(q));
        },

        matchSoundboardModalCategory(category) {
            if (this.soundboardModalCategory === 'all') return true;
            if (this.soundboardModalCategory === 'bel') {
                return ['Bel Jam Pelajaran', 'Bel Pengumuman', 'Bel Interkom'].includes(category);
            }
            if (this.soundboardModalCategory === 'darurat') {
                return ['Tanggap Darurat'].includes(category);
            }
            if (this.soundboardModalCategory === 'upacara') {
                return ['Tata Tertib Lapangan', 'Upacara & Seremoni'].includes(category);
            }
            if (this.soundboardModalCategory === 'pengumuman') {
                return ['Otomasi KBM', 'Ibadah & Karakter'].includes(category);
            }
            return true;
        },

        selectSoundboardOption(sound) {
            this.formData.soundboard_id = sound.id;
            if (!this.formData.title || this.formData.title === '' || this.formData.title.startsWith('Bel ')) {
                this.formData.title = sound.name;
            }
            if (sound.voice_text && !this.formData.voice_text) {
                this.formData.voice_text = sound.voice_text;
            }
            if (!this.formData.description) {
                this.formData.description = sound.description;
            }
        },

        openLyricsModal(anthem) {
            this.selectedAnthem = anthem;
            this.showLyricsModal = true;
        },

        openAddModal() {
            this.isEdit = false;
            this.editUrl = '';
            this.surahModalSearch = '';
            this.soundboardModalCategory = 'all';
            this.formData = {
                title: '',
                time: '07:00',
                sound_type: 'chime',
                soundboard_id: 'chime-westminster',
                surah_number: '',
                surah_numbers: [],
                anthem_id: '',
                anthem_ids: [],
                voice_text: '',
                days: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                duration_seconds: 15,
                volume: 100,
                description: ''
            };
            this.showModal = true;
        },

        openEditModal(bellId) {
            const bell = this.allBells.find(b => b.id == bellId);
            if (!bell) return;
            this.isEdit = true;
            this.editUrl = `{{ url('/school-bell') }}/${bell.id}`;
            this.surahModalSearch = '';
            this.soundboardModalCategory = 'all';

            let parsedDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            if (Array.isArray(bell.days)) {
                parsedDays = [...bell.days];
            } else if (typeof bell.days === 'string') {
                try {
                    parsedDays = JSON.parse(bell.days);
                } catch (e) {
                    parsedDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                }
            }

            let parsedSurahs = [];
            if (Array.isArray(bell.surah_numbers)) {
                parsedSurahs = bell.surah_numbers.map(Number);
            } else if (typeof bell.surah_numbers === 'string') {
                try {
                    parsedSurahs = JSON.parse(bell.surah_numbers).map(Number);
                } catch (e) {
                    parsedSurahs = [];
                }
            }
            if (parsedSurahs.length === 0 && bell.surah_number) {
                parsedSurahs = [Number(bell.surah_number)];
            }

            let parsedAnthems = [];
            if (Array.isArray(bell.anthem_ids)) {
                parsedAnthems = [...bell.anthem_ids];
            } else if (typeof bell.anthem_ids === 'string') {
                try {
                    parsedAnthems = JSON.parse(bell.anthem_ids);
                } catch (e) {
                    parsedAnthems = [];
                }
            }
            if (parsedAnthems.length === 0 && bell.anthem_id) {
                parsedAnthems = [bell.anthem_id];
            }

            let parsedSoundboardId = 'chime-westminster';
            if (bell.sound_type === 'soundboard') {
                if (bell.playlist && bell.playlist[0] && bell.playlist[0].id) {
                    parsedSoundboardId = bell.playlist[0].id;
                } else if (bell.audio_url && bell.audio_url.startsWith('soundboard:')) {
                    const subType = bell.audio_url.replace('soundboard:', '');
                    const allSounds = @json($instantSounds);
                    const found = allSounds.find(s => s.sound_type === subType || s.id === subType);
                    if (found) parsedSoundboardId = found.id;
                }
            }

            this.formData = {
                title: bell.title || '',
                time: (bell.time || '07:00').substring(0, 5),
                sound_type: bell.sound_type || 'chime',
                soundboard_id: parsedSoundboardId,
                surah_number: bell.surah_number || (parsedSurahs[0] || ''),
                surah_numbers: parsedSurahs,
                anthem_id: bell.anthem_id || (parsedAnthems[0] || ''),
                anthem_ids: parsedAnthems,
                voice_text: bell.voice_text || '',
                days: parsedDays,
                duration_seconds: bell.duration_seconds || 15,
                volume: bell.volume || 100,
                description: bell.description || ''
            };
            this.showModal = true;
        },

        toggleSurahSelection(num) {
            num = Number(num);
            const idx = this.formData.surah_numbers.indexOf(num);
            if (idx > -1) {
                this.formData.surah_numbers.splice(idx, 1);
            } else {
                this.formData.surah_numbers.push(num);
            }
            this.formData.surah_number = this.formData.surah_numbers[0] || '';
        },

        selectJuz30Surahs() {
            for (let i = 78; i <= 114; i++) {
                if (!this.formData.surah_numbers.includes(i)) {
                    this.formData.surah_numbers.push(i);
                }
            }
            this.formData.surah_number = this.formData.surah_numbers[0] || '';
        },

        select3QulSurahs() {
            [112, 113, 114].forEach(i => {
                if (!this.formData.surah_numbers.includes(i)) {
                    this.formData.surah_numbers.push(i);
                }
            });
            this.formData.surah_number = this.formData.surah_numbers[0] || '';
        },

        selectFeaturedSurahs() {
            [1, 18, 36, 55, 56, 67, 78].forEach(i => {
                if (!this.formData.surah_numbers.includes(i)) {
                    this.formData.surah_numbers.push(i);
                }
            });
            this.formData.surah_number = this.formData.surah_numbers[0] || '';
        },

        clearSurahSelection() {
            this.formData.surah_numbers = [];
            this.formData.surah_number = '';
        },

        toggleAnthemSelection(id) {
            const idx = this.formData.anthem_ids.indexOf(id);
            if (idx > -1) {
                this.formData.anthem_ids.splice(idx, 1);
            } else {
                this.formData.anthem_ids.push(id);
            }
            this.formData.anthem_id = this.formData.anthem_ids[0] || '';
        },

        selectAllAnthems(ids) {
            this.formData.anthem_ids = [...ids];
            this.formData.anthem_id = this.formData.anthem_ids[0] || '';
        },

        clearAnthemSelection() {
            this.formData.anthem_ids = [];
            this.formData.anthem_id = '';
        },

        matchModalSurah(num, name) {
            if (!this.surahModalSearch) return true;
            const q = this.surahModalSearch.toLowerCase();
            return String(num).includes(q) || name.toLowerCase().includes(q);
        },

        playBellRecordById(bellId) {
            const bell = this.allBells.find(b => b.id == bellId);
            if (bell) {
                this.playBellRecord(bell);
            }
        },

        toggleDay(day) {
            const idx = this.formData.days.indexOf(day);
            if (idx > -1) {
                this.formData.days.splice(idx, 1);
            } else {
                this.formData.days.push(day);
            }
        }
    };
}

// Global click fallbacks
window.toggleAutoStandby = function() {
    if (window.bellAppInstance) window.bellAppInstance.toggleAutoStandby();
};
window.testChimeSound = function(type, speech) {
    if (window.bellAppInstance) window.bellAppInstance.testChimeSound(type, speech);
};
window.testSpeech = function(text) {
    if (window.bellAppInstance) window.bellAppInstance.testSpeech(text);
};
window.stopAllAudio = function() {
    if (window.bellAppInstance) window.bellAppInstance.stopAllAudio();
};
</script>
@endsection
