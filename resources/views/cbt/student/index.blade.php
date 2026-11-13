@extends('layouts.app')

@section('title', 'Portal Ujian Online (CBT) Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                Portal Ujian Online (CBT) Siswa
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Selamat datang di ruang asesmen berbasis komputer SMA Negeri 1 Lengkong.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Sesi Ujian Aktif
            </span>
        </div>
    </div>

    <div class="space-y-6" x-data="{
        selectedExam: null,
        tokenInput: '',
        userLat: null,
        userLng: null,
        geoLoading: false,
        geoError: '',
        openStartModal(exam) {
            this.selectedExam = exam;
            this.tokenInput = '';
            this.geoError = '';
            if (exam.enable_geofence) {
                this.detectLocation();
            }
        },
        detectLocation() {
            if (!navigator.geolocation) {
                this.geoError = 'Browser Anda tidak mendukung deteksi lokasi GPS.';
                return;
            }
            this.geoLoading = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.userLat = pos.coords.latitude;
                    this.userLng = pos.coords.longitude;
                    this.geoLoading = false;
                },
                (err) => {
                    this.geoLoading = false;
                    this.geoError = 'Izin lokasi GPS diperlukan untuk mengikuti ujian ini. Silakan izinkan akses lokasi pada browser Anda.';
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }
    }">
        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs sm:text-sm font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200 text-xs sm:text-sm font-semibold flex items-center justify-between">
                <span>{{ session('info') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700">&times;</button>
            </div>
        @endif

        <!-- KARTU IDENTITAS PESERTA SISWA -->
        <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-600/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center overflow-hidden flex-shrink-0 text-xl font-black">
                        @if($student->photo)
                            <img src="{{ asset('storage/'.$student->photo) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($student->name, 0, 2) }}
                        @endif
                    </div>
                    <div>
                        <div class="text-xs text-indigo-300 font-bold uppercase tracking-wider">Kartu Peserta Asesmen</div>
                        <h2 class="text-xl font-black tracking-tight text-white mt-0.5">{{ $student->name }}</h2>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-indigo-200 mt-1">
                            <span>NIS: <strong>{{ $student->nis }}</strong></span>
                            <span>•</span>
                            <span>Kelas: <strong>{{ $student->schoolClass?->name ?? '-' }}</strong></span>
                            <span>•</span>
                            <span>Tahun Ajaran: <strong>2026/2027</strong></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 border border-white/10 rounded-2xl p-4 backdrop-blur-sm flex items-center gap-4 text-xs">
                    <div>
                        <div class="text-indigo-300 font-medium">Status Keamanan</div>
                        <div class="font-bold text-emerald-400 flex items-center gap-1.5 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Anti-Cheat Siaga
                        </div>
                    </div>
                    <div class="h-8 w-px bg-white/20"></div>
                    <div>
                        <div class="text-indigo-300 font-medium">Validasi Lokasi</div>
                        <div class="font-bold text-sky-300 flex items-center gap-1.5 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            Radius Sekolah
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DAFTAR UJIAN YANG TERSEDIA -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Ujian Tersedia Untuk Kelas Anda
                </h3>
                <span class="text-xs text-slate-500 font-medium">{{ $exams->count() }} Jadwal Ditemukan</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($exams as $exam)
                    @php
                        $session = $sessions->get($exam->id);
                        $isSubmitted = $session && $session->status === 'submitted';
                        $isInProgress = $session && $session->status === 'in_progress';
                        $isBlocked = $session && $session->is_blocked;
                        $now = now();
                        $isNotStartedYet = $exam->start_time && $now->lt($exam->start_time);
                        $isExpired = $exam->end_time && $now->gt($exam->end_time);
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-bold text-[11px] border border-indigo-200/60 dark:border-indigo-800/60">
                                    {{ $exam->subject?->name ?? 'Mata Pelajaran' }}
                                </span>
                                @if($isSubmitted)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 font-bold text-[11px]">
                                        ✓ Selesai
                                    </span>
                                @elseif($isBlocked)
                                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 font-bold text-[11px]">
                                        🚫 Terblokir
                                    </span>
                                @elseif($isInProgress)
                                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 font-bold text-[11px] animate-pulse">
                                        Sedang Berlangsung
                                    </span>
                                @elseif($isNotStartedYet)
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 font-medium text-[11px]">
                                        Belum Dimulai
                                    </span>
                                @elseif($isExpired)
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 font-medium text-[11px]">
                                        Waktu Habis
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-400 font-bold text-[11px]">
                                        Siap Dikerjakan
                                    </span>
                                @endif
                            </div>

                            <div>
                                <h4 class="font-black text-slate-900 dark:text-white text-base leading-snug">{{ $exam->title }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                                    {{ $exam->description ?: 'Ujian berbasis komputer kurikulum merdeka.' }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/50 p-3 rounded-2xl">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Durasi Ujian</span>
                                    <strong class="font-bold">{{ $exam->duration_minutes }} Menit</strong>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Jumlah Soal</span>
                                    <strong class="font-bold">{{ $exam->questions_count ?? $exam->questions()->count() }} Butir</strong>
                                </div>
                                <div class="col-span-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                                    <span class="text-[10px] text-slate-400 block">Jadwal Pelaksanaan</span>
                                    <span class="font-medium text-[11px]">
                                        {{ $exam->start_time ? $exam->start_time->translatedFormat('d M Y H:i') : 'Fleksibel' }}
                                        s.d
                                        {{ $exam->end_time ? $exam->end_time->translatedFormat('d M Y H:i') : 'Selesai' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 text-[11px]">
                                @if($exam->enable_security_block)
                                    <span class="text-rose-600 dark:text-rose-400 flex items-center gap-1 font-semibold" title="Auto-block jika pindah tab atau aplikasi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        Anti-Cheat Max {{ $exam->max_violations ?? 3 }}x
                                    </span>
                                @endif
                                @if($exam->enable_geofence)
                                    <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-semibold" title="Wajib berada di radius sekolah">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        GPS Radius
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60">
                            @if($isSubmitted)
                                <a href="{{ route('cbt.student.result', $exam->id) }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Lihat Hasil Ujian
                                </a>
                            @elseif($isBlocked)
                                <a href="{{ route('cbt.student.take', $exam->id) }}" class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Ujian Terblokir (Lihat Status)
                                </a>
                            @elseif($isInProgress)
                                <a href="{{ route('cbt.student.take', $exam->id) }}" class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    Lanjutkan Pengerjaan
                                </a>
                            @elseif($isNotStartedYet)
                                <button disabled class="w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 font-bold text-xs cursor-not-allowed">
                                    Belum Waktunya
                                </button>
                            @elseif($isExpired)
                                <button disabled class="w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 font-bold text-xs cursor-not-allowed">
                                    Waktu Ujian Berakhir
                                </button>
                            @else
                                <button type="button" @click="openStartModal({{ $exam->toJson() }})" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-md shadow-indigo-600/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Masuk Ujian
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 p-8">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <h4 class="font-bold text-slate-700 dark:text-slate-300 text-sm">Tidak Ada Jadwal Ujian Aktif</h4>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Saat ini belum ada ujian yang ditugaskan untuk kelas {{ $student->schoolClass?->name ?? 'Anda' }}.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- MODAL KONFIRMASI MULAI UJIAN (TOKEN & GPS) -->
        <div x-show="selectedExam !== null" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 text-left space-y-5" @click.away="selectedExam = null">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white" x-text="selectedExam ? selectedExam.title : ''"></h3>
                            <p class="text-xs text-slate-500">Konfirmasi Persiapan Ujian CBT</p>
                        </div>
                    </div>
                    <button type="button" @click="selectedExam = null" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form method="POST" :action="selectedExam ? '{{ url('cbt/exams') }}/' + selectedExam.id + '/start' : '#'" class="space-y-4">
                    @csrf
                    <input type="hidden" name="latitude" :value="userLat">
                    <input type="hidden" name="longitude" :value="userLng">

                    <div class="bg-slate-50 dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300 space-y-2">
                        <div class="font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Peraturan & Tata Tertib Ujian:
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-slate-500 dark:text-slate-400">
                            <li>Ujian akan berjalan dalam mode layar penuh (Full Screen).</li>
                            <li class="text-rose-600 dark:text-rose-400 font-semibold">Dilarang berpindah tab, meminimalkan browser, atau membuka aplikasi lain.</li>
                            <li>Pelanggaran berturut-turut akan menyebabkan <strong class="text-rose-600">ujian Anda terkunci otomatis</strong> dan harus dibuka oleh pengawas.</li>
                            <li>Jawaban tersimpan secara otomatis setiap kali Anda memilih atau mengisi opsi.</li>
                        </ul>
                    </div>

                    <!-- INPUT TOKEN JIKA ADA -->
                    <template x-if="selectedExam && selectedExam.token">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Masukkan Token Ujian <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="token" x-model="tokenInput" required placeholder="Contoh: CBT26A" class="w-full text-center uppercase tracking-widest font-mono font-bold text-base rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 py-2.5 px-4 text-indigo-600 dark:text-indigo-400 focus:ring-2 focus:ring-indigo-500">
                            <p class="text-[11px] text-slate-400 text-center">Tanyakan 6 digit token kepada pengawas ruang ujian Anda.</p>
                        </div>
                    </template>

                    <!-- VALIDASI GEOFENCE -->
                    <template x-if="selectedExam && selectedExam.enable_geofence">
                        <div class="p-3 rounded-2xl border text-xs" :class="userLat && userLng ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-800 dark:text-emerald-300' : (geoError ? 'bg-rose-50 dark:bg-rose-950/30 border-rose-200 text-rose-800 dark:text-rose-300' : 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 text-amber-800 dark:text-amber-300')">
                            <div class="flex items-center gap-2 font-bold">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span>Verifikasi Lokasi GPS Sekolah:</span>
                            </div>
                            <div class="mt-1" x-show="geoLoading">
                                <span class="inline-flex items-center gap-1.5 font-medium animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Mengambil koordinat GPS Anda...
                                </span>
                            </div>
                            <div class="mt-1 font-semibold" x-show="userLat && userLng">
                                ✓ Lokasi terdeteksi (<span x-text="userLat ? userLat.toFixed(6) : ''"></span>, <span x-text="userLng ? userLng.toFixed(6) : ''"></span>)
                            </div>
                            <div class="mt-1 font-medium" x-show="geoError">
                                <span x-text="geoError"></span>
                                <button type="button" @click="detectLocation()" class="underline ml-1 font-bold">Coba Lagi</button>
                            </div>
                        </div>
                    </template>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="selectedExam = null" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700">
                            Batal
                        </button>
                        <button type="submit" :disabled="selectedExam && selectedExam.enable_geofence && (!userLat || !userLng)" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white shadow-md shadow-indigo-600/20 transition">
                            Mulai Mengerjakan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
