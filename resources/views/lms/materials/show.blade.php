@extends('layouts.app')

@section('title', $material->title . ' - LMS')

@section('content')
@php
    $interactiveData = null;
    if (!empty($material->content)) {
        $decoded = json_decode($material->content, true);
        if (is_array($decoded) && (isset($decoded['kartu_konsep']) || isset($decoded['cek_pemahaman']) || isset($decoded['ringkasan_materi']) || isset($decoded['elemen']) || isset($decoded['capaian_pembelajaran']))) {
            $interactiveData = $decoded;
        }
    }

    $videoUrl = $material->external_url ?: $material->video_url;
    $isYouTube = false;
    $ytVideoId = null;
    if ($videoUrl && preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $videoUrl, $ytMatches)) {
        $isYouTube = true;
        $ytVideoId = $ytMatches[1];
    }

    $isHtml5Video = false;
    $html5VideoSrc = null;
    if ($material->file_path && in_array($material->getDocumentExtension(), ['mp4', 'webm', 'ogg', 'mov'])) {
        $isHtml5Video = true;
        $html5VideoSrc = asset('storage/' . $material->file_path);
    } elseif ($videoUrl && preg_match('/\.(mp4|webm|ogg)($|\?)/i', $videoUrl)) {
        $isHtml5Video = true;
        $html5VideoSrc = $videoUrl;
    }

    $hasVideo = $isYouTube || $isHtml5Video || ($material->content_type === 'video');
    $videoQuestions = $material->video_questions ?: [];

    $docExt = $material->getDocumentExtension();
    $isDocument = $material->file_path && ($material->isEmbeddedDocument() || in_array($docExt, ['pdf', 'ppt', 'pptx', 'doc', 'docx']));
@endphp

<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi']) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Modul {{ $courseClass->subject->name }}</span>
        </a>
        <div class="flex items-center gap-2">
            @if($interactiveData)
                <span class="px-3 py-1 rounded-full text-xs font-black bg-gradient-to-r from-indigo-500/15 via-purple-500/15 to-cyan-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-300/40 dark:border-indigo-700/40 flex items-center gap-1.5 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
                    <span>Standar Permendikdasmen No. 13 Tahun 2025</span>
                </span>
            @endif
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                {{ $material->topic }}
            </span>
        </div>
    </div>

    @if($canManage)
        @include('lms.security.unblock_panel', [
            'courseClass' => $courseClass,
            'scopedType' => 'material',
            'scopedId' => $material->id,
        ])
    @endif

    <!-- Header & Action Card -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-2 max-w-3xl">
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $typeLabels = [
                        'video' => 'VIDEO PEMBELAJARAN',
                        'document' => 'DOKUMEN MODUL',
                        'link' => 'TAUTAN EDUKASI',
                        'text' => 'ARTIKEL TEKS',
                    ];
                    $typeName = $typeLabels[$material->content_type] ?? strtoupper($material->content_type);
                @endphp
                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $material->content_type === 'video' ? 'bg-rose-500/20 text-rose-600' : ($interactiveData ? 'bg-indigo-600 text-white' : 'bg-cyan-500/20 text-cyan-600') }}">
                    {{ $interactiveData ? '✨ MODUL INTERAKTIF (' . $typeName . ')' : $typeName }}
                </span>
                <span class="text-xs text-slate-400 font-medium">Kelas: <strong>{{ $courseClass->schoolClass->name }}</strong></span>
                <span class="text-slate-400">•</span>
                <span class="text-xs text-slate-400">Guru Pengampu: <strong>{{ $courseClass->teacher->name }}</strong></span>
                @if($material->start_time)
                    <span class="text-slate-400">•</span>
                    <span class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg border border-indigo-200 dark:border-indigo-800">⏰ Waktu Dimulai: {{ $material->start_time->translatedFormat('l, d F Y - H:i') }} WIB</span>
                @endif
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $material->title }}</h1>
            @if($material->description)
                <p class="text-xs text-slate-500 leading-relaxed">{{ $material->description }}</p>
            @endif

            @if(!empty($interactiveData['catatan_guru']))
                <div class="mt-2 p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/60 text-xs text-amber-900 dark:text-amber-200 flex items-start gap-2.5">
                    <span class="text-base shrink-0">💡</span>
                    <div>
                        <span class="font-bold uppercase tracking-wider text-[10px] text-amber-700 dark:text-amber-400">Arahan Khusus Guru Pengampu:</span>
                        <p class="mt-0.5 leading-relaxed">{{ $interactiveData['catatan_guru'] }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if($canManage)
                <button type="button" onclick="openEditMaterialModal()" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Materi</span>
                </button>
            @endif

            @if($student)
                @if($isCompleted)
                    <span class="px-3.5 py-2 bg-emerald-500/15 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 font-bold text-xs rounded-xl inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Telah Selesai</span>
                    </span>
                    @if($nextMaterial)
                        <a href="{{ route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $nextMaterial->id]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                            <span>Materi Berikutnya</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                @elseif($hasVideo)
                    <span class="px-3.5 py-2 bg-amber-500/15 border border-amber-500/30 text-amber-700 dark:text-amber-300 font-bold text-xs rounded-xl inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Wajib Simak Video Hingga Selesai di Bawah</span>
                    </span>
                @else
                    <form action="{{ route('lms.materials.read', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Tandai Selesai & Lanjut</span>
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <!-- 1. VIDEO PEMBELAJARAN INTERAKTIF & ANTI-SKIP (JIKA TIPE KONTEN VIDEO / URL VIDEO / BERKAS VIDEO) -->
    @if($hasVideo)
        <div class="p-6 sm:p-7 bg-slate-900 border border-slate-800 rounded-3xl shadow-xl text-white space-y-6">
            <!-- Header Video Info -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-rose-600 to-red-500 text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-rose-500/25 shrink-0">
                        🎬
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base sm:text-lg font-black text-white">
                                Pemutar Video Pembelajaran Interaktif
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                Mode Menyimak Wajib
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Sistem anti-skip: Video tidak dapat dipercepat, diperlambat, di-pause, atau dilompati menitnya.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if(count($videoQuestions) > 0)
                        <span class="px-3 py-1.5 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 text-xs font-bold flex items-center gap-1.5 shadow-xs">
                            <span>❓ {{ count($videoQuestions) }} Titik Pertanyaan Interaktif</span>
                        </span>
                    @endif
                    <button type="button" onclick="toggleVideoFullscreen()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition flex items-center gap-1.5 border border-slate-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        <span class="hidden sm:inline">Layar Penuh</span>
                    </button>
                </div>
            </div>

            <!-- Petunjuk Siswa -->
            <div class="p-3.5 rounded-2xl bg-slate-800/60 border border-slate-700/60 text-xs text-slate-300 flex items-start gap-2.5">
                <span class="text-amber-400 font-bold shrink-0 text-base">⚠️</span>
                <div class="leading-relaxed">
                    <strong>Ketentuan Menyimak:</strong> Simak penjelasan materi secara utuh. Jika muncul pertanyaan di menit tertentu, jawab dengan benar agar video berlanjut. Jika salah, video otomatis mundur ke menit materi terkait. Di akhir video, tombol <strong>Finish</strong> akan aktif sebagai bukti kehadiran & ketuntasan belajar Anda.
                </div>
            </div>

            <!-- RESPONSIVE VIDEO PLAYER CONTAINER (16:9 aspect ratio, utuh di semua perangkat) -->
            <div id="video-player-container" class="relative w-full max-w-5xl mx-auto aspect-video rounded-3xl overflow-hidden shadow-2xl border-2 border-slate-800 bg-black flex items-center justify-center select-none group">
                <!-- Video Element (YouTube target or HTML5) -->
                @if($isYouTube)
                    <div id="yt-strict-player-target" class="w-full h-full"></div>
                @elseif($html5VideoSrc)
                    <video id="html5-strict-player" class="w-full h-full object-contain" playsinline preload="auto">
                        <source src="{{ $html5VideoSrc }}" type="video/mp4">
                        Peramban Anda tidak mendukung pemutar video HTML5.
                    </video>
                @else
                    <div class="text-center p-6 text-slate-400 space-y-2">
                        <p class="text-sm">Video tidak dapat dimuat langsung. Buka tautan:</p>
                        <a href="{{ $videoUrl }}" target="_blank" class="text-indigo-400 underline font-bold">{{ $videoUrl }}</a>
                    </div>
                @endif

                <!-- Warning Toast Notification (Anti-skip / Anti-pause / Anti-speed) -->
                <div id="video-strict-toast" class="absolute top-4 inset-x-4 max-w-md mx-auto z-40 p-3 rounded-2xl bg-rose-600/95 backdrop-blur-md text-white text-xs font-bold text-center shadow-2xl border border-rose-400/40 transition-all duration-300 opacity-0 pointer-events-none transform -translate-y-2">
                    <span id="video-toast-msg">🚫 Dilarang melompati menit video! Anda wajib menyimak secara utuh.</span>
                </div>

                <!-- Live Status Watermark (Top Left) -->
                <div class="absolute top-3 left-3 z-20 flex items-center gap-2 pointer-events-none">
                    <span class="px-2.5 py-1 rounded-xl bg-black/60 backdrop-blur-md border border-white/10 text-[11px] font-bold text-slate-200 font-mono" id="video-live-timer">
                        00:00 / 00:00
                    </span>
                    <span class="px-2 py-1 rounded-xl bg-rose-600/80 backdrop-blur-md text-[10px] font-black uppercase tracking-wider text-white flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                        <span>Wajib Tuntas</span>
                    </span>
                </div>

                <!-- Big Start Overlay (User gesture required to start with sound) -->
                <div id="video-start-overlay" class="absolute inset-0 z-20 bg-slate-950/80 backdrop-blur-xs flex flex-col items-center justify-center p-6 text-center space-y-4">
                    <button type="button" onclick="startStrictVideoPlayback()" class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-rose-600 to-red-500 hover:scale-105 active:scale-95 text-white flex items-center justify-center font-bold text-3xl shadow-2xl shadow-rose-600/50 transition-all duration-300 group-hover:ring-4 group-hover:ring-rose-500/30">
                        <svg class="w-10 h-10 ml-1 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                    <div>
                        <h4 class="text-base sm:text-lg font-black text-white">Klik untuk Memulai Video Pembelajaran</h4>
                        <p class="text-xs text-slate-400 max-w-md mt-1">Video akan berputar tanpa jeda (pause) dan tidak dapat dipercepat/dilompati.</p>
                    </div>
                </div>

                <!-- INTERACTIVE IN-VIDEO QUESTION OVERLAY (Muncul di menit yang ditentukan guru) -->
                <div id="video-question-overlay" class="absolute inset-0 z-30 bg-slate-950/92 backdrop-blur-md flex items-center justify-center p-4 sm:p-8 hidden animate-fadeIn">
                    <div class="w-full max-w-xl bg-slate-900/95 border-2 border-indigo-500/50 rounded-3xl p-5 sm:p-7 shadow-2xl text-left space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 font-black text-xs">
                                    ❓ Pertanyaan Interaktif
                                </span>
                                <span class="text-xs text-slate-400" id="q-timestamp-label">Menit 00:00</span>
                            </div>
                            <span class="text-[11px] text-slate-400 font-medium">Jawab untuk melanjutkan video</span>
                        </div>

                        <div>
                            <h4 id="q-text-content" class="text-sm sm:text-base font-bold text-white leading-relaxed">
                                Teks Pertanyaan...
                            </h4>
                        </div>

                        <!-- Feedback Message Alert Box -->
                        <div id="q-feedback-box" class="p-3.5 rounded-2xl text-xs font-bold leading-relaxed hidden">
                            <span id="q-feedback-text"></span>
                        </div>

                        <!-- Options Container -->
                        <div id="q-options-container" class="grid grid-cols-1 gap-2.5 pt-1">
                            <!-- Injected dynamically via JS -->
                        </div>
                    </div>
                </div>

                <!-- END OF VIDEO FINISH OVERLAY (Muncul saat video telah tuntas ditonton) -->
                <div id="video-finish-overlay" class="absolute inset-0 z-30 bg-slate-950/95 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center space-y-4 hidden animate-fadeIn">
                    <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center text-3xl shadow-xl shadow-emerald-500/30 animate-bounce">
                        🏆
                    </div>
                    <div class="space-y-1.5 max-w-md">
                        <h4 class="text-lg sm:text-xl font-black text-white">
                            🎉 Selamat! Anda Telah Selesai Menonton Video
                        </h4>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Seluruh materi video telah Anda ikuti secara utuh. Ceklis konfirmasi di bawah lalu <strong>Klik Finish / Selesaikan Materi Video Ini</strong> sebagai bukti penyelesaian materi pembelajaran.
                        </p>
                    </div>

                    @if($isCompleted)
                        <div class="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Materi ini telah Anda selesaikan sebelumnya.</span>
                        </div>
                        @if($nextMaterial)
                            <a href="{{ route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $nextMaterial->id]) }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-2xl shadow-lg transition flex items-center gap-2">
                                <span>Lanjut ke Materi Berikutnya &rarr;</span>
                            </a>
                        @endif
                    @else
                        <div class="p-3 bg-slate-900/90 border border-slate-700/80 rounded-xl flex items-center gap-2.5 max-w-md text-left">
                            <input type="checkbox" id="chk-video-confirmed-overlay" onchange="syncVideoCheckbox(this.checked)" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 bg-slate-800 border-slate-600 cursor-pointer">
                            <label for="chk-video-confirmed-overlay" class="text-xs text-slate-200 font-semibold cursor-pointer select-none">
                                Saya telah tuntas menyimak video pembelajaran ini
                            </label>
                        </div>
                        <form action="{{ route('lms.materials.read', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" method="POST" id="form-video-finish-overlay" onsubmit="return validateVideoFinishSubmit(event)">
                            @csrf
                            <button type="submit" id="btn-video-finish-overlay" disabled class="px-8 py-3.5 bg-slate-700 opacity-50 cursor-not-allowed text-slate-300 font-black text-sm rounded-2xl shadow-xl transition-all flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span id="btn-video-finish-overlay-label">🔒 Klik Finish / Selesaikan Materi Video Ini (Terkunci Sebelum Diceklis Tuntas)</span>
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Custom Bottom Control Strip (Visual Only - Non scrubbable) -->
                <div class="absolute bottom-0 inset-x-0 z-20 bg-gradient-to-t from-black/90 via-black/50 to-transparent p-3 pt-6 flex flex-col gap-2 pointer-events-auto">
                    <!-- Progress track with markers -->
                    <div class="relative w-full h-2 bg-white/20 rounded-full overflow-hidden select-none pointer-events-none" id="video-progress-track">
                        <div id="video-progress-bar" class="h-full bg-gradient-to-r from-rose-600 to-red-500 rounded-full w-0 transition-all duration-150"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-300">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-[11px]" id="video-ctrl-timer">00:00 / 00:00</span>
                            <span class="text-[10px] text-slate-400 bg-white/10 px-2 py-0.5 rounded-md hidden sm:inline">Kecepatan: 1.0x (Terkunci)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="toggleVideoMute()" id="btn-video-mute" class="p-1 rounded-lg hover:bg-white/10 text-slate-300 transition" title="Bisu / Nyalakan Suara">
                                🔊
                            </button>
                            <button type="button" onclick="toggleVideoFullscreen()" class="p-1 rounded-lg hover:bg-white/10 text-slate-300 transition" title="Layar Penuh">
                                ⛶
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu Status & Tombol Finish di Bawah Video -->
            <div class="p-5 sm:p-6 bg-slate-800/80 border border-slate-700/80 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl {{ $isCompleted ? 'bg-emerald-600' : 'bg-rose-600' }} text-white flex items-center justify-center font-bold text-xl shadow-md shrink-0">
                        {{ $isCompleted ? '✓' : '🎬' }}
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-white">
                            {{ $isCompleted ? 'Video Pembelajaran Telah Selesai Ditonton' : 'Status Menyimak Video Pembelajaran' }}
                        </h4>
                        <p class="text-xs text-slate-400 mt-0.5" id="video-finish-status-note">
                            {{ $isCompleted ? 'Anda telah menyelesaikan materi video ini secara utuh.' : 'Tombol Finish akan aktif otomatis saat video selesai disimak hingga detik terakhir.' }}
                        </p>
                    </div>
                </div>

                <div class="shrink-0 w-full sm:w-auto">
                    @if($isCompleted)
                        <div class="flex items-center gap-2 justify-end">
                            <span class="px-4 py-2.5 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold text-xs flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Tuntas Disimak</span>
                            </span>
                            @if($nextMaterial)
                                <a href="{{ route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $nextMaterial->id]) }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                                    <span>Materi Berikutnya &rarr;</span>
                                </a>
                            @endif
                        </div>
                    @else
                        <div id="video-external-finish-wrapper" class="w-full sm:w-auto flex flex-col items-start sm:items-end gap-2.5">
                            <div class="p-3 bg-slate-900 border border-slate-700/80 rounded-xl flex items-center gap-2.5 w-full sm:w-auto">
                                <input type="checkbox" id="chk-video-confirmed" onchange="syncVideoCheckbox(this.checked)" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 bg-slate-800 border-slate-600 cursor-pointer">
                                <label for="chk-video-confirmed" class="text-xs text-slate-200 font-semibold cursor-pointer select-none">
                                    Saya telah tuntas menyimak video pembelajaran ini
                                </label>
                            </div>
                            <form action="{{ route('lms.materials.read', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" method="POST" id="form-video-finish" onsubmit="return validateVideoFinishSubmit(event)" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" id="btn-video-finish" disabled class="w-full sm:w-auto px-6 py-3 bg-slate-700 opacity-50 cursor-not-allowed text-slate-300 font-black text-xs sm:text-sm rounded-xl transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span id="btn-video-finish-label">🔒 Selesaikan & Buka Materi Berikutnya (Terkunci)</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- PANEL KHUSUS GURU: KELOLA PERTANYAAN INTERAKTIF DI MENIT VIDEO -->
            @if($canManage)
                <div class="p-6 bg-slate-950/70 border border-slate-800 rounded-3xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                <h4 class="text-sm font-black text-white">
                                    ⚙️ Kelola Pertanyaan Interaktif di Video (Khusus Guru)
                                </h4>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Atur menit pertanyaan muncul. Jika jawaban siswa benar video lanjut, jika salah video otomatis mundur ke menit yang ditentukan.
                            </p>
                        </div>
                        <button type="button" onclick="openAddVideoQuestionModal()" class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5 shrink-0">
                            <span>+ Tambah Pertanyaan di Menit Video</span>
                        </button>
                    </div>

                    @if(empty($videoQuestions))
                        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 text-center text-xs text-slate-400">
                            Belum ada pertanyaan interaktif yang ditambahkan di video ini. Klik tombol di atas untuk menambah pertanyaan pada menit tertentu.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-slate-400 border-b border-slate-800">
                                        <th class="py-2.5 px-3">Menit Muncul</th>
                                        <th class="py-2.5 px-3">Pertanyaan & Opsi</th>
                                        <th class="py-2.5 px-3">Kunci</th>
                                        <th class="py-2.5 px-3">Mundur Jika Salah</th>
                                        <th class="py-2.5 px-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60">
                                    @foreach($videoQuestions as $idx => $vq)
                                        <tr class="hover:bg-slate-900/60">
                                            <td class="py-3 px-3 font-mono font-bold text-amber-300 whitespace-nowrap">
                                                ⏱️ {{ $vq['timestamp_formatted'] ?? '00:00' }}
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="font-bold text-white max-w-sm">{{ $vq['question'] }}</div>
                                                <div class="text-[11px] text-slate-400 mt-1 flex flex-wrap gap-2">
                                                    @foreach($vq['options'] ?? [] as $optKey => $optVal)
                                                        <span class="px-2 py-0.5 rounded-md {{ $optKey === ($vq['correct_answer'] ?? '') ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40' : 'bg-slate-800 text-slate-300' }}">
                                                            {{ $optKey }}: {{ Str::limit($optVal, 30) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="py-3 px-3 whitespace-nowrap">
                                                <span class="px-2 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 font-black border border-emerald-500/40">
                                                    Opsi {{ $vq['correct_answer'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 font-mono text-rose-300 whitespace-nowrap">
                                                ⏪ Menit {{ $vq['rewind_formatted'] ?? '00:00' }}
                                            </td>
                                            <td class="py-3 px-3 text-right whitespace-nowrap">
                                                <form action="{{ route('lms.materials.video_questions.destroy', ['courseClass' => $courseClass->id, 'material' => $material->id, 'questionIndex' => $idx]) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan di menit {{ $vq['timestamp_formatted'] }} ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 rounded-lg text-xs font-bold transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @elseif($material->external_url)
        <!-- External Website Link (Non-video) -->
        <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900 dark:text-white">Tautan Sumber Belajar Daring / Website Edukasi</div>
                    <div class="text-[11px] text-slate-500 truncate max-w-lg">{{ $material->external_url }}</div>
                </div>
            </div>
            <a href="{{ $material->external_url }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs shrink-0">
                <span>Kunjungi Sumber Belajar &rarr;</span>
            </a>
        </div>
    @endif

    <!-- 2. PEMBACA DOKUMEN OTOMATIS & FINISH DI AKHIR DOKUMEN (PDF, PPT, DOC) -->
    @if($isDocument)
        <div id="doc-viewer-container" class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
            <!-- Header Dokumen -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-md shrink-0">
                        @if($docExt === 'pdf')
                            📕
                        @elseif(in_array($docExt, ['ppt', 'pptx']))
                            📊
                        @else
                            📄
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                                DOKUMEN MODUL: {{ basename($material->file_name ?: $material->file_path) }}
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                Format: {{ strtoupper($docExt) }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                Wajib Dibaca
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            File otomatis terbuka di peramban tanpa perlu diunduh. Silakan pelajari seluruh isi modul hingga selesai.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleDocFullscreen()" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        <span>Layar Penuh (Fullscreen)</span>
                    </button>
                    <a id="btn-open-doc-newtab" href="{{ route('lms.materials.preview', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 text-xs font-bold transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Buka di Tab Baru</span>
                    </a>
                    <a id="btn-download-doc" href="{{ route('lms.materials.preview', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" target="_blank" download class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh Modul</span>
                    </a>
                </div>
            </div>

            <!-- EMBEDDED DOCUMENT VIEWER TANPA AUTO-DOWNLOAD -->
            <div id="doc-viewer-container" class="relative w-full rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-950 shadow-inner">
                @if($docExt === 'pdf')
                    <!-- PDF.js Embedded Interactive Canvas Viewer & Native In-Browser Viewer -->
                    <div id="pdf-viewer-wrapper" class="p-3 sm:p-4 space-y-3">
                        <div id="pdf-toolbar" class="flex flex-wrap items-center justify-between gap-2 p-3 bg-slate-900 text-white rounded-2xl shadow-sm text-xs border border-slate-800">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="pdfPrevPage()" id="pdf-btn-prev" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed font-bold transition">
                                    &larr; Hal Sebelumnya
                                </button>
                                <span class="text-xs text-slate-300 font-mono px-2 py-1 rounded-lg bg-slate-800/80">
                                    Halaman <span id="pdf-page-num" class="font-bold text-white">1</span> / <span id="pdf-page-count" class="font-bold text-white">--</span>
                                </span>
                                <button type="button" onclick="pdfNextPage()" id="pdf-btn-next" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed font-bold transition">
                                    Hal Selanjutnya &rarr;
                                </button>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="pdfZoomOut()" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition" title="Perkecil Tampilan">
                                    🔍 -
                                </button>
                                <span id="pdf-zoom-label" class="font-mono text-[11px] text-slate-300 px-1.5">100%</span>
                                <button type="button" onclick="pdfZoomIn()" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition" title="Perbesar Tampilan">
                                    🔍 +
                                </button>
                                <button type="button" onclick="pdfFitWidth()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition" title="Sesuaikan dengan Lebar Layar">
                                    Sesuaikan Lebar
                                </button>
                                <button type="button" id="btn-toggle-native-pdf" onclick="togglePdfViewerMode()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition flex items-center gap-1.5" title="Ganti Mode Penampil (Canvas / Frame Native)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    <span id="btn-toggle-native-text">Mode Native</span>
                                </button>
                                <a href="{{ route('lms.materials.preview', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" target="_blank" download class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh PDF</span>
                                </a>
                            </div>
                        </div>

                        <div id="pdf-scroll-container" class="relative w-full overflow-auto bg-slate-800/80 rounded-2xl min-h-[550px] max-h-[820px] flex items-center justify-center p-2 sm:p-4">
                            <div id="pdf-loading-spinner" class="flex flex-col items-center justify-center py-16 space-y-3">
                                <div class="w-9 h-9 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-xs font-semibold text-slate-300">Menyiapkan lembar dokumen PDF di peramban...</span>
                            </div>
                            <canvas id="pdf-canvas" class="shadow-2xl rounded-lg mx-auto hidden"></canvas>
                            <iframe id="pdf-embed-frame" name="doc-viewer-frame" class="doc-viewer-frame w-full h-[650px] sm:h-[780px] rounded-xl border-0 hidden bg-white"></iframe>
                        </div>
                    </div>
                @elseif($docExt === 'docx')
                    <!-- DOCX Mammoth In-Browser Reader -->
                    <div id="docx-reader-container" class="w-full min-h-[500px] max-h-[820px] overflow-y-auto p-6 sm:p-10 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 leading-relaxed text-sm">
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">📄 Lembar Modul Dokumen (Word DOCX)</span>
                            <a href="{{ route('lms.materials.preview', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" target="_blank" download class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 text-xs font-bold transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh Berkas Asli</span>
                            </a>
                        </div>
                        <div id="docx-loading" class="flex flex-col items-center justify-center py-16 space-y-3">
                            <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-xs font-semibold text-slate-500">Membuka lembar dokumen modul di peramban...</span>
                        </div>
                        <div id="docx-content" class="prose dark:prose-invert max-w-none"></div>
                    </div>
                @elseif($docExt === 'doc')
                    <!-- Binary Legacy DOC Preview Card (No auto-download) -->
                    <div class="p-8 sm:p-12 text-center space-y-4 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
                        <div class="w-16 h-16 mx-auto rounded-3xl bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/30 flex items-center justify-center text-3xl shadow-sm">
                            📄
                        </div>
                        <div class="space-y-1.5 max-w-md mx-auto">
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">Dokumen Modul: {{ basename($material->file_name ?: $material->file_path) }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Berkas berformat Microsoft Word (.DOC). Berkas telah siap untuk dibuka dan dipelajari.</p>
                        </div>
                        <div class="flex items-center justify-center gap-3 pt-2">
                            <a href="{{ route('lms.materials.preview', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" target="_blank" download class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Buka / Unduh Berkas Word (.DOC)</span>
                            </a>
                        </div>
                    </div>
                @elseif(in_array($docExt, ['pptx', 'ppt']))
                    <!-- PPTX / PPT Slide Deck Presentation Viewer -->
                    <div id="pptx-viewer-wrapper" class="p-3 sm:p-4 space-y-3 bg-slate-900 text-white">
                        <div class="flex flex-wrap items-center justify-between gap-2 p-3 bg-slate-800 text-white rounded-2xl shadow-sm text-xs border border-slate-700">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="pptxPrevSlide()" id="pptx-btn-prev" class="px-3 py-1.5 rounded-xl bg-slate-700 hover:bg-slate-600 disabled:opacity-40 disabled:cursor-not-allowed font-bold transition">
                                    &larr; Slide Sebelumnya
                                </button>
                                <span class="text-xs text-slate-300 font-mono px-2 py-1 rounded-lg bg-slate-900">
                                    Slide <span id="pptx-slide-num" class="font-bold text-amber-400">1</span> / <span id="pptx-slide-count" class="font-bold text-white">--</span>
                                </span>
                                <button type="button" onclick="pptxNextSlide()" id="pptx-btn-next" class="px-3 py-1.5 rounded-xl bg-slate-700 hover:bg-slate-600 disabled:opacity-40 disabled:cursor-not-allowed font-bold transition">
                                    Slide Selanjutnya &rarr;
                                </button>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('lms.materials.preview', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" target="_blank" download class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs flex items-center gap-1.5 shadow-sm transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh Berkas PPT Asli</span>
                                </a>
                            </div>
                        </div>

                        <div id="pptx-slide-container" class="relative w-full overflow-hidden bg-slate-950 border border-slate-800 rounded-2xl min-h-[450px] p-6 sm:p-10 flex flex-col justify-center">
                            <div id="pptx-loading" class="flex flex-col items-center justify-center py-16 space-y-3">
                                <div class="w-9 h-9 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-xs font-semibold text-slate-300">Menyiapkan tayangan slide presentasi di peramban...</span>
                            </div>
                            <div id="pptx-slide-content" class="w-full max-w-3xl mx-auto space-y-4 hidden"></div>
                        </div>
                    </div>
                @else
                    <!-- General Document Preview -->
                    <div class="p-8 text-center space-y-4 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
                        <div class="w-16 h-16 mx-auto rounded-3xl bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-3xl shadow-sm">
                            📄
                        </div>
                        <div class="space-y-1.5 max-w-md mx-auto">
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">Dokumen: {{ basename($material->file_name ?: $material->file_path) }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Format: {{ strtoupper($docExt) }}. Klik tombol di bawah untuk membuka berkas dokumen.</p>
                        </div>
                        <div class="flex items-center justify-center gap-3 pt-2">
                            <a href="{{ route('lms.materials.preview', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" target="_blank" download class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Buka / Unduh Berkas</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- KONFIRMASI PENYELESAIAN DI AKHIR DOKUMEN -->
            <div class="mt-4 p-5 sm:p-6 bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-indigo-500/10 border-2 border-emerald-500/30 rounded-2xl shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-md shrink-0">
                        📖
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 dark:text-white">
                            Akhir Dokumen Modul Pembelajaran
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Jika Anda telah selesai membaca seluruh isi dokumen di atas, silakan klik tombol Finish di bawah ini untuk mengonfirmasi ketuntasan materi.
                        </p>
                    </div>
                </div>

                <div class="shrink-0 w-full sm:w-auto">
                    @if($isCompleted)
                        <div class="flex items-center gap-2 justify-end">
                            <span class="px-4 py-2.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-300 font-bold text-xs flex items-center gap-1.5 shadow-xs">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Dokumen Selesai Dibaca</span>
                            </span>
                            @if($nextMaterial)
                                <a href="{{ route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $nextMaterial->id]) }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                                    <span>Materi Berikutnya &rarr;</span>
                                </a>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('lms.materials.read', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs sm:text-sm rounded-xl shadow-lg shadow-emerald-500/20 hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Klik Finish / Selesai Membaca Dokumen</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @elseif($material->file_path && !in_array($docExt, ['mp4', 'webm', 'ogg', 'mov']))
        <!-- Lampiran Berkas Non-Dokumen / Non-Video (ZIP, RAR, dsb) -->
        <div class="p-5 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900 dark:text-white">Berkas Lampiran Tambahan</div>
                    <div class="text-[11px] text-slate-500">{{ basename($material->file_path) }} • {{ $material->file_size ? round($material->file_size / 1024, 1) . ' KB' : 'Berkas' }}</div>
                </div>
            </div>
            <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" download class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Unduh Berkas</span>
            </a>
        </div>
    @endif

    <!-- MODUL INTERAKTIF BERBASIS PERMENDIKDASMEN NO. 13 TAHUN 2025 -->
    @if($interactiveData)
        <!-- Bagian 1: Kerangka Kurikulum (CP, TP, Profil Pelajar Pancasila) -->
        <div class="p-6 bg-gradient-to-br from-indigo-950 via-slate-900 to-slate-950 border border-indigo-800/40 rounded-3xl shadow-xl text-white space-y-5">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-indigo-600 text-white shadow-sm">
                    Kurikulum Nasional SMA
                </span>
                @if(!empty($interactiveData['target_fase']))
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-400/30">
                        🎯 {{ $interactiveData['target_fase'] }}
                    </span>
                @endif
                @if(!empty($interactiveData['elemen']))
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-400/30">
                        🧩 Elemen: {{ $interactiveData['elemen'] }}
                    </span>
                @endif
                <span class="text-xs text-indigo-300 font-semibold sm:ml-auto">Permendikdasmen RI No. 13 Tahun 2025</span>
            </div>

            @if(!empty($interactiveData['capaian_pembelajaran']))
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black text-cyan-300 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Capaian Pembelajaran (CP) Elemen:</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-200 leading-relaxed bg-white/5 border border-white/10 rounded-2xl p-4">
                        {{ $interactiveData['capaian_pembelajaran'] }}
                    </p>
                </div>
            @endif

            @if(!empty($interactiveData['alur_tujuan_pembelajaran']))
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black text-purple-300 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span>Alur Tujuan Pembelajaran (ATP):</span>
                    </h3>
                    <div class="text-xs sm:text-sm text-slate-200 leading-relaxed bg-white/5 border border-white/10 rounded-2xl p-4">
                        {{ $interactiveData['alur_tujuan_pembelajaran'] }}
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <!-- Tujuan Pembelajaran -->
                @if(!empty($interactiveData['tujuan_pembelajaran']))
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2.5">
                        <h4 class="text-xs font-bold text-amber-300 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Tujuan Pembelajaran (TP) Bermakna:</span>
                        </h4>
                        @if(is_array($interactiveData['tujuan_pembelajaran']))
                            <ul class="space-y-1.5 text-xs text-slate-300">
                                @foreach($interactiveData['tujuan_pembelajaran'] as $tp)
                                    <li class="flex items-start gap-2">
                                        <span class="text-cyan-400 font-bold shrink-0 mt-0.5">🎯</span>
                                        <span>{{ $tp }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-slate-300 leading-relaxed whitespace-pre-line">{{ $interactiveData['tujuan_pembelajaran'] }}</p>
                        @endif
                    </div>
                @endif

                <!-- Dimensi Profil Pelajar Pancasila -->
                @if(!empty($interactiveData['profil_pelajar_pancasila']) && is_array($interactiveData['profil_pelajar_pancasila']))
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2.5">
                        <h4 class="text-xs font-bold text-emerald-300 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Profil Pelajar Pancasila:</span>
                        </h4>
                        <ul class="space-y-1.5 text-xs text-slate-300">
                            @foreach($interactiveData['profil_pelajar_pancasila'] as $p3)
                                <li class="flex items-start gap-2">
                                    <span class="text-emerald-400 font-bold shrink-0 mt-0.5">✨</span>
                                    <span>{{ $p3 }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bagian 2: Penjelasan & Uraian Materi Terperinci (Deep Learning Permendikdasmen 13/2025) -->
        @if(!empty($interactiveData['uraian_materi']) && is_array($interactiveData['uraian_materi']))
            <div class="space-y-6">
                <!-- Section Header Banner -->
                <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-indigo-600 animate-pulse"></span>
                                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                                    📘 Penjelasan & Uraian Materi Terperinci
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60">
                                    Lengkap & Mudah Dipahami
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Disusun sistematis dengan uraian mendalam, analogi kontekstual, dan rangkuman poin kunci</p>
                        </div>
                        <div class="flex items-center gap-1.5 self-start sm:self-center">
                            <span class="text-xs font-bold text-slate-400">Total: <strong>{{ count($interactiveData['uraian_materi']) }} Bahasan Pokok Materi</strong></span>
                        </div>
                    </div>

                    @if(!empty($interactiveData['ringkasan_materi']))
                        <div class="mt-4 p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                            <span class="font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-wider text-[10px] block mb-1">
                                🧭 Orientasi & Pengantar Konseptual:
                            </span>
                            {{ $interactiveData['ringkasan_materi'] }}
                        </div>
                    @endif
                </div>

                <!-- Pembahasan Materi Terperinci -->
                <div class="space-y-6">
                    @foreach($interactiveData['uraian_materi'] as $uIdx => $chapter)
                        @php
                            $cleanTitle = trim(preg_replace('/^Sub-Bab\s*\d+\s*[:\-–]?\s*/i', '', $chapter['subjudul'] ?? ('Bahasan Materi ' . ($uIdx + 1))));
                        @endphp
                        <div class="p-6 sm:p-7 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-5 hover:border-indigo-300/80 transition-all duration-300">
                            <!-- Header Pembahasan Materi -->
                            <div class="pb-3 border-b border-slate-100 dark:border-slate-800">
                                <h4 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">
                                    {{ $cleanTitle }}
                                </h4>
                            </div>

                            <!-- Konten Uraian Lengkap & Terperinci -->
                            <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line space-y-3 font-normal">
                                {{ $chapter['konten_lengkap'] ?? '' }}
                            </div>

                            <!-- Analogi Sederhana (Agar Mudah Dipahami) -->
                            @if(!empty($chapter['analogi']))
                                <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-400/5 to-amber-500/10 border border-amber-300/70 dark:border-amber-700/50 flex items-start gap-3 shadow-xs">
                                    <span class="text-2xl shrink-0 mt-0.5">💡</span>
                                    <div class="space-y-1">
                                        <div class="text-[11px] font-black uppercase tracking-wider text-amber-800 dark:text-amber-300">
                                            Analogi Sederhana (Mudah Dipahami):
                                        </div>
                                        <p class="text-xs text-amber-950 dark:text-amber-200 leading-relaxed font-medium">
                                            {{ $chapter['analogi'] }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Poin Kunci Penguasaan Materi -->
                            @if(!empty($chapter['poin_kunci']) && is_array($chapter['poin_kunci']))
                                <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-200/60 dark:border-indigo-800/60 space-y-2.5">
                                    <div class="text-[11px] font-black uppercase tracking-wider text-indigo-700 dark:text-indigo-300 flex items-center gap-1.5">
                                        <span>📌 Poin Kunci Penguasaan Materi:</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                        @foreach($chapter['poin_kunci'] as $pk)
                                             <div class="flex items-start gap-2 text-slate-700 dark:text-slate-300 p-2 rounded-xl bg-white dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                                                <span class="text-emerald-600 dark:text-emerald-400 font-black mt-0.5">✓</span>
                                                <span class="leading-relaxed">{{ $pk }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif(!empty($interactiveData['ringkasan_materi']))
            <!-- Fallback jika modul lawas hanya memuat ringkasan -->
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Ulasan Materi Esensial & Konsep Kunci</h3>
                </div>
                <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line space-y-3">
                    {{ $interactiveData['ringkasan_materi'] }}
                </div>
            </div>
        @endif

        <!-- Bagian 3: Kartu Konsep Interaktif (Interactive Flashcards) -->
        @if(!empty($interactiveData['kartu_konsep']) && is_array($interactiveData['kartu_konsep']))
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>🃏 Kartu Konsep Interaktif (Flashcards)</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">Sentuh kartu untuk membuka materi</span>
                        </h3>
                        <p class="text-xs text-slate-500">Uji daya ingat dan pemahaman konsep secara mandiri (Active Recall)</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 flex-wrap">
                        <button type="button" onclick="expandAllCards(true)" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 text-slate-700 dark:text-slate-300 hover:text-indigo-600 transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Buka Semua</span>
                        </button>
                        <button type="button" onclick="expandAllCards(false)" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            <span>Tutup Semua</span>
                        </button>
                        <button type="button" onclick="openFlashcardModal(0)" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white transition flex items-center gap-1.5 shadow-xs">
                            <span>🎯 Mode Belajar (Layar Penuh)</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="concept-cards-grid">
                    @foreach($interactiveData['kartu_konsep'] as $kIdx => $card)
                        <div class="concept-card group relative p-5 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-indigo-500 hover:shadow-lg transition-all duration-300 cursor-pointer flex flex-col justify-between min-h-[220px]" onclick="toggleCard(this)" data-opened="false">
                            <!-- Card Header -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="w-7 h-7 rounded-xl bg-indigo-600/15 text-indigo-700 dark:text-indigo-300 font-black text-xs flex items-center justify-center">
                                        {{ $kIdx + 1 }}
                                    </span>
                                    <span class="card-status-badge text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600 dark:group-hover:bg-indigo-950/60 dark:group-hover:text-indigo-400 transition flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 group-hover:bg-indigo-500"></span>
                                        <span class="status-label">Klik untuk Buka</span>
                                    </span>
                                </div>

                                <h4 class="text-base font-black text-slate-900 dark:text-white tracking-tight mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                    {{ $card['istilah'] ?? 'Konsep' }}
                                </h4>

                                <!-- Closed State Teaser Prompt -->
                                <div class="card-teaser text-xs text-slate-500 dark:text-slate-400 py-3.5 px-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-dashed border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-center space-y-1 my-2 transition-all duration-300">
                                    <span class="text-xl">💡</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-xs">Sentuh / Klik Kartu Ini</span>
                                    <span class="text-[11px] text-slate-400">untuk membuka penjelasan & contoh kontekstual</span>
                                </div>

                                <!-- Opened State Details (Initially Hidden) -->
                                <div class="card-details text-xs space-y-3 hidden transition-all duration-300">
                                    <div class="p-3.5 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/40 text-slate-800 dark:text-slate-200 leading-relaxed font-normal">
                                        {{ $card['definisi'] ?? '' }}
                                    </div>
                                    @if(!empty($card['contoh']))
                                        <div class="p-3 rounded-xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/60 text-[11px] text-amber-900 dark:text-amber-200">
                                            <span class="font-extrabold uppercase tracking-wider block text-amber-800 dark:text-amber-300 mb-0.5">💡 Contoh Nyata:</span>
                                            <span>{{ $card['contoh'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Footer Action -->
                            <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] font-bold">
                                <span class="card-action-text text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                                    <span class="action-label">Buka Materi</span>
                                    <svg class="action-icon w-3.5 h-3.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                                <button type="button" onclick="event.stopPropagation(); openFlashcardModal({{ $kIdx }})" title="Pelajari dalam Mode Layar Penuh" class="text-slate-400 hover:text-indigo-600 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Bagian 4: Cek Pemahaman Mandiri Interaktif (Instant Feedback Mini-Quiz) -->
        @if(!empty($interactiveData['cek_pemahaman']) && is_array($interactiveData['cek_pemahaman']))
            <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>⚡ Cek Pemahaman Konsep (Interaktif)</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600">Umpan Balik Langsung</span>
                        </h3>
                        <p class="text-xs text-slate-500">Pilih opsi jawaban untuk langsung mengecek kebenaran dan pembahasan ilmiah</p>
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach($interactiveData['cek_pemahaman'] as $qIdx => $quizItem)
                        <div class="quiz-item p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60 space-y-3" data-correct="{{ $quizItem['kunci'] ?? 'A' }}">
                            <div class="flex items-start gap-2.5">
                                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    {{ $qIdx + 1 }}
                                </span>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white leading-relaxed">
                                    {{ $quizItem['pertanyaan'] ?? '' }}
                                </h4>
                            </div>

                            @if(!empty($quizItem['opsi']) && is_array($quizItem['opsi']))
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                    @foreach($quizItem['opsi'] as $optKey => $optText)
                                        <button type="button" class="quiz-option text-left p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-indigo-400 text-xs text-slate-800 dark:text-slate-200 transition flex items-start gap-2.5" onclick="checkMiniQuiz(this, '{{ $optKey }}')">
                                            <span class="opt-badge font-black text-xs px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                {{ $optKey }}
                                            </span>
                                            <span class="flex-1 leading-snug">{{ $optText }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            <div class="quiz-feedback hidden p-3 rounded-xl text-xs transition">
                                <div class="feedback-status font-black mb-1"></div>
                                <div class="feedback-expl text-slate-600 dark:text-slate-300 leading-relaxed">
                                    <strong>Pembahasan Ilmiah:</strong> {{ $quizItem['penjelasan'] ?? '' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Bagian 5: Studi Kasus Kontekstual & Deep Learning -->
        @if(!empty($interactiveData['studi_kasus']))
            <div class="p-6 bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-amber-500/10 border border-amber-300/60 dark:border-amber-700/40 rounded-3xl space-y-2.5">
                <h3 class="text-xs font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    <span>Studi Kasus Kontekstual & Penerapan Nyata:</span>
                </h3>
                <p class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line font-medium">
                    {{ $interactiveData['studi_kasus'] }}
                </p>
            </div>
        @endif

        <!-- Bagian 6: Glosarium Istilah & Refleksi Siswa -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if(!empty($interactiveData['glosarium']) && is_array($interactiveData['glosarium']))
                <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl space-y-3 shadow-sm">
                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                        <span>📖 Glosarium Istilah:</span>
                    </h4>
                    <div class="space-y-2 text-xs">
                        @foreach($interactiveData['glosarium'] as $glos)
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <strong class="text-indigo-600 dark:text-indigo-400">{{ $glos['kata'] ?? '' }}:</strong>
                                <span class="text-slate-600 dark:text-slate-300 ml-1">{{ $glos['arti'] ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($interactiveData['refleksi_siswa']))
                <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl space-y-3 shadow-sm">
                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                        <span>🤔 Refleksi Diri Peserta Didik:</span>
                    </h4>
                    <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed bg-indigo-50/40 dark:bg-indigo-950/30 p-3 rounded-xl border border-indigo-200/60 dark:border-indigo-800/60">
                        {{ $interactiveData['refleksi_siswa'] }}
                    </p>
                </div>
            @endif
        </div>
    @elseif($material->content_text || $material->content)
        <!-- Standard Content Text Viewer (jika bukan JSON interaktif) -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Rangkuman Materi & Catatan Guru:</h3>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                {{ $material->content_text ?: $material->content }}
            </div>
        </div>
    @endif

    <!-- Pemantauan Siswa Membaca Modul (Guru & Admin) -->
    @if(!$student)
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pemantauan Siswa Membaca Modul Ini</h3>
                    <p class="text-xs text-slate-500">{{ $material->reads->count() }} siswa telah menyelesaikan pembelajaran</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400">
                    Progres: {{ $courseClass->schoolClass->students->count() > 0 ? round(($material->reads->count() / $courseClass->schoolClass->students->count()) * 100) : 0 }}%
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5">
                @foreach($courseClass->schoolClass->students as $st)
                    @php
                        $stRead = $material->reads->firstWhere('student_id', $st->id);
                    @endphp
                    <div class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-xs flex items-center justify-between gap-2 {{ $stRead ? 'bg-emerald-50/40 dark:bg-emerald-950/20' : 'bg-slate-50/40 dark:bg-slate-800/20' }}">
                        <span class="font-medium text-slate-800 dark:text-slate-200 truncate">{{ $st->name }}</span>
                        <span class="text-[10px] font-bold shrink-0 {{ $stRead ? 'text-emerald-600' : 'text-slate-400' }}">
                            {{ $stRead ? '✓ Selesai' : 'Belum' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Sequential Progression Navigation Footer (Siswa) -->
    @if($student)
        <div class="p-6 rounded-3xl bg-gradient-to-br from-indigo-50/80 via-white to-emerald-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-emerald-950/20 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg {{ $isCompleted ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center text-xs font-black shadow-xs">
                        {{ $isCompleted ? '✓' : '📖' }}
                    </span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $isCompleted ? 'Materi Ini Telah Tuntas Dipelajari' : 'Selesaikan Materi untuk Membuka Tahap Berikutnya' }}
                    </h3>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    @if($isCompleted)
                        @if($nextMaterial)
                            Materi berikutnya telah dibuka. Lanjutkan belajar secara berurutan sesuai alur kurikulum.
                        @else
                            Luar biasa! Anda telah menyelesaikan seluruh rangkaian materi pelajaran di kelas ini.
                        @endif
                    @else
                        Sistem pembelajaran berjalan secara terstruktur dan berurutan. Klik tombol konfirmasi untuk membuka materi berikutnya.
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if(! $isCompleted)
                    <form action="{{ route('lms.materials.read', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Selesaikan & Buka Materi Berikutnya</span>
                        </button>
                    </form>
                @elseif($nextMaterial)
                    <a href="{{ route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $nextMaterial->id]) }}" class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition flex items-center gap-2">
                        <span>Lanjut: {{ Str::limit($nextMaterial->title, 28) }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'tugas']) }}" class="px-5 py-3 rounded-2xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs transition flex items-center gap-2">
                        <span>Ke Agenda Tugas & PR &rarr;</span>
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- ======================================================== -->
<!-- MODAL: STUDY MODE FLASHCARD (LAYAR PENUH) -->
<!-- ======================================================== -->
<div id="modal-flashcard-study" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm hidden" onclick="if(event.target === this) closeFlashcardModal()">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-2xl w-full shadow-2xl flex flex-col justify-between min-h-[440px] relative">
        <!-- Modal Top Bar -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-black flex items-center justify-center text-xs" id="modal-card-number">1</span>
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Mode Belajar Kartu Konsep (Active Recall)</h3>
                    <p class="text-[11px] text-slate-500">Sentuh / Klik kartu atau tekan Spasi untuk membalik</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300" id="modal-card-counter">1 / 4</span>
                <button type="button" onclick="closeFlashcardModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center text-xs font-bold transition">✕</button>
            </div>
        </div>

        <!-- Flippable Flashcard Canvas -->
        <div class="my-5 flex-1 flex items-center justify-center">
            <div id="modal-flashcard-box" class="w-full p-8 rounded-3xl border-2 border-indigo-500/40 bg-gradient-to-br from-indigo-50/50 via-white to-purple-50/30 dark:from-slate-800/80 dark:via-slate-900 dark:to-indigo-950/40 shadow-xl cursor-pointer transition-all duration-300 hover:scale-[1.01] hover:border-indigo-500 text-center flex flex-col items-center justify-center min-h-[250px] select-none" onclick="flipModalFlashcard()">
                <!-- Front Side -->
                <div id="modal-card-front" class="space-y-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">
                        Sisi Istilah / Konsep
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight" id="modal-card-title">
                        Konsep
                    </h2>
                    <p class="text-xs text-slate-400 pt-2 flex items-center justify-center gap-1.5 font-medium">
                        <span>👆 Sentuh / Klik di mana saja untuk membuka jawaban & contoh</span>
                    </p>
                </div>

                <!-- Back Side (Initially Hidden) -->
                <div id="modal-card-back" class="space-y-4 hidden text-left w-full">
                    <div class="flex items-center justify-between pb-2 border-b border-indigo-200/50 dark:border-indigo-800/50">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                            ✓ Penjelasan Konsep
                        </span>
                        <span class="text-[10px] text-slate-400">Klik untuk membalik kembali</span>
                    </div>
                    <p class="text-sm sm:text-base text-slate-800 dark:text-slate-200 leading-relaxed font-normal" id="modal-card-def">
                        Penjelasan definisi...
                    </p>
                    <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/60 text-xs text-amber-900 dark:text-amber-200" id="modal-card-example-box">
                        <div class="font-bold text-[10px] uppercase tracking-wider text-amber-700 dark:text-amber-400 mb-0.5">💡 Contoh Penerapan:</div>
                        <div id="modal-card-example" class="leading-relaxed">...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Navigation Controls -->
        <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="prevModalCard()" id="btn-prev-card" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Sebelumnya</span>
            </button>
            <button type="button" onclick="flipModalFlashcard()" class="px-4 py-2 rounded-xl text-xs font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 transition flex items-center gap-1.5 shadow-xs">
                <span>🔄 Balik Kartu (Spasi)</span>
            </button>
            <button type="button" onclick="nextModalCard()" id="btn-next-card" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center gap-1.5 shadow-xs">
                <span>Selanjutnya</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: IMAGE LIGHTBOX (ZOOM GAMBAR EDUKASI) -->
<!-- ======================================================== -->
<div id="modal-image-lightbox" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/85 backdrop-blur-md hidden" onclick="if(event.target === this) closeImageLightbox()">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl relative flex flex-col space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 text-white">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">🖼️</span>
                <h3 class="text-xs sm:text-sm font-bold truncate max-w-lg sm:max-w-2xl" id="lightbox-caption">Gambar Materi Pelajaran</h3>
            </div>
            <button type="button" onclick="closeImageLightbox()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-xs font-bold transition shrink-0">✕</button>
        </div>
        <div class="relative w-full max-h-[72vh] flex items-center justify-center overflow-hidden rounded-2xl bg-black/60 p-1">
            <img id="lightbox-img" src="" alt="Gambar Materi" class="max-w-full max-h-[70vh] object-contain rounded-xl shadow-lg transition-transform duration-300">
        </div>
        <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1 border-t border-slate-800/80">
            <span>🔍 Mode Penampil Gambar Beresolusi Tinggi</span>
            <span>Tekan <kbd class="px-1.5 py-0.5 bg-slate-800 rounded text-slate-300 border border-slate-700">ESC</kbd> untuk menutup</span>
        </div>
    </div>
</div>

<!-- Interactive JavaScript for Flashcards, Lightbox & Mini Quiz -->
<script>
function openImageLightbox(src, caption) {
    const modal = document.getElementById('modal-image-lightbox');
    const img = document.getElementById('lightbox-img');
    const cap = document.getElementById('lightbox-caption');
    if (img) img.src = src;
    if (cap) cap.textContent = caption || 'Gambar Pemahaman Materi';
    if (modal) modal.classList.remove('hidden');
}

function closeImageLightbox() {
    const modal = document.getElementById('modal-image-lightbox');
    if (modal) modal.classList.add('hidden');
}
const flashcardsData = @json($interactiveData['kartu_konsep'] ?? []);
let currentModalIndex = 0;
let isModalFlipped = false;

function toggleCard(cardEl) {
    const isOpened = cardEl.getAttribute('data-opened') === 'true';
    setCardState(cardEl, !isOpened);
}

function setCardState(cardEl, open) {
    const teaser = cardEl.querySelector('.card-teaser');
    const details = cardEl.querySelector('.card-details');
    const statusLabel = cardEl.querySelector('.status-label');
    const statusBadge = cardEl.querySelector('.card-status-badge');
    const actionLabel = cardEl.querySelector('.action-label');
    const actionIcon = cardEl.querySelector('.action-icon');

    if (open) {
        cardEl.setAttribute('data-opened', 'true');
        cardEl.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-500/20', 'bg-indigo-50/15', 'dark:bg-indigo-950/20');
        cardEl.classList.remove('border-slate-200', 'dark:border-slate-800');
        if (teaser) teaser.classList.add('hidden');
        if (details) details.classList.remove('hidden');
        if (statusLabel) statusLabel.textContent = 'Terbuka';
        if (statusBadge) {
            statusBadge.classList.add('bg-emerald-50', 'text-emerald-600', 'dark:bg-emerald-950/60', 'dark:text-emerald-400');
            statusBadge.classList.remove('bg-slate-100', 'text-slate-500', 'dark:bg-slate-800');
        }
        if (actionLabel) actionLabel.textContent = 'Tutup Kartu';
        if (actionIcon) actionIcon.classList.add('rotate-180');
    } else {
        cardEl.setAttribute('data-opened', 'false');
        cardEl.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-500/20', 'bg-indigo-50/15', 'dark:bg-indigo-950/20');
        cardEl.classList.add('border-slate-200', 'dark:border-slate-800');
        if (teaser) teaser.classList.remove('hidden');
        if (details) details.classList.add('hidden');
        if (statusLabel) statusLabel.textContent = 'Klik untuk Buka';
        if (statusBadge) {
            statusBadge.classList.remove('bg-emerald-50', 'text-emerald-600', 'dark:bg-emerald-950/60', 'dark:text-emerald-400');
            statusBadge.classList.add('bg-slate-100', 'text-slate-500', 'dark:bg-slate-800');
        }
        if (actionLabel) actionLabel.textContent = 'Buka Materi';
        if (actionIcon) actionIcon.classList.remove('rotate-180');
    }
}

function expandAllCards(open) {
    document.querySelectorAll('.concept-card').forEach(card => setCardState(card, open));
}

function openFlashcardModal(index) {
    if (!flashcardsData || flashcardsData.length === 0) return;
    currentModalIndex = Math.max(0, Math.min(index, flashcardsData.length - 1));
    isModalFlipped = false;
    renderModalCard();
    const modal = document.getElementById('modal-flashcard-study');
    if (modal) modal.classList.remove('hidden');
}

function closeFlashcardModal() {
    const modal = document.getElementById('modal-flashcard-study');
    if (modal) modal.classList.add('hidden');
}

function renderModalCard() {
    const card = flashcardsData[currentModalIndex];
    if (!card) return;

    const numEl = document.getElementById('modal-card-number');
    const counterEl = document.getElementById('modal-card-counter');
    const titleEl = document.getElementById('modal-card-title');
    const defEl = document.getElementById('modal-card-def');
    
    if (numEl) numEl.textContent = currentModalIndex + 1;
    if (counterEl) counterEl.textContent = (currentModalIndex + 1) + ' / ' + flashcardsData.length;
    if (titleEl) titleEl.textContent = card.istilah || 'Konsep';
    if (defEl) defEl.textContent = card.definisi || 'Tidak ada definisi.';
    
    const exBox = document.getElementById('modal-card-example-box');
    const exText = document.getElementById('modal-card-example');
    if (card.contoh) {
        if (exBox) exBox.classList.remove('hidden');
        if (exText) exText.textContent = card.contoh;
    } else {
        if (exBox) exBox.classList.add('hidden');
    }

    const front = document.getElementById('modal-card-front');
    const back = document.getElementById('modal-card-back');
    if (isModalFlipped) {
        if (front) front.classList.add('hidden');
        if (back) back.classList.remove('hidden');
    } else {
        if (front) front.classList.remove('hidden');
        if (back) back.classList.add('hidden');
    }

    const prevBtn = document.getElementById('btn-prev-card');
    const nextBtn = document.getElementById('btn-next-card');
    if (prevBtn) prevBtn.disabled = currentModalIndex === 0;
    if (nextBtn) nextBtn.disabled = currentModalIndex === flashcardsData.length - 1;
}

function flipModalFlashcard() {
    isModalFlipped = !isModalFlipped;
    const front = document.getElementById('modal-card-front');
    const back = document.getElementById('modal-card-back');
    if (isModalFlipped) {
        if (front) front.classList.add('hidden');
        if (back) back.classList.remove('hidden');
    } else {
        if (front) front.classList.remove('hidden');
        if (back) back.classList.add('hidden');
    }
}

function nextModalCard() {
    if (currentModalIndex < flashcardsData.length - 1) {
        currentModalIndex++;
        isModalFlipped = false;
        renderModalCard();
    }
}

function prevModalCard() {
    if (currentModalIndex > 0) {
        currentModalIndex--;
        isModalFlipped = false;
        renderModalCard();
    }
}

document.addEventListener('keydown', function(e) {
    const flashModal = document.getElementById('modal-flashcard-study');
    const imageModal = document.getElementById('modal-image-lightbox');

    if (imageModal && !imageModal.classList.contains('hidden')) {
        if (e.key === 'Escape') {
            closeImageLightbox();
            return;
        }
    }

    if (flashModal && !flashModal.classList.contains('hidden')) {
        if (e.key === 'Escape') {
            closeFlashcardModal();
        } else if (e.key === ' ' || e.code === 'Space') {
            e.preventDefault();
            flipModalFlashcard();
        } else if (e.key === 'ArrowRight') {
            nextModalCard();
        } else if (e.key === 'ArrowLeft') {
            prevModalCard();
        }
    }
});

function checkMiniQuiz(buttonEl, selectedKey) {
    const quizItem = buttonEl.closest('.quiz-item');
    const correctKey = quizItem.getAttribute('data-correct');
    const feedbackBox = quizItem.querySelector('.quiz-feedback');
    const statusText = quizItem.querySelector('.feedback-status');
    const allOptions = quizItem.querySelectorAll('.quiz-option');

    allOptions.forEach(opt => {
        opt.disabled = true;
        opt.classList.remove('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-950/40', 'border-rose-500', 'bg-rose-50', 'dark:bg-rose-950/40');
    });

    feedbackBox.classList.remove('hidden', 'bg-emerald-50', 'border-emerald-200', 'text-emerald-800', 'bg-rose-50', 'border-rose-200', 'text-rose-800');

    if (selectedKey === correctKey) {
        buttonEl.classList.add('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-950/40');
        buttonEl.querySelector('.opt-badge').classList.add('bg-emerald-600', 'text-white');
        feedbackBox.classList.add('bg-emerald-50/80', 'dark:bg-emerald-950/30', 'border', 'border-emerald-300', 'text-emerald-900', 'dark:text-emerald-200');
        statusText.innerHTML = '🎉 Jawaban Anda TEPAT SEKALI! (Opsi ' + selectedKey + ')';
    } else {
        buttonEl.classList.add('border-rose-500', 'bg-rose-50', 'dark:bg-rose-950/40');
        buttonEl.querySelector('.opt-badge').classList.add('bg-rose-600', 'text-white');
        feedbackBox.classList.add('bg-rose-50/80', 'dark:bg-rose-950/30', 'border', 'border-rose-300', 'text-rose-900', 'dark:text-rose-200');
        statusText.innerHTML = '❌ Belum Tepat. Kunci jawaban yang benar adalah Opsi ' + correctKey;
    }
}

// ========================================================
// STRICT VIDEO PLAYER CONTROLLER & INTERACTIVE IN-VIDEO QUESTIONS
// ========================================================
const isYouTubeVideo = @json($isYouTube ?? false);
const ytVideoId = @json($ytVideoId ?? null);
const hasHtml5Video = @json($isHtml5Video ?? false);
const rawVideoQuestions = @json($videoQuestions ?? []);

let videoQuestions = rawVideoQuestions.map((q, idx) => ({
    id: idx,
    timestamp_seconds: parseInt(q.timestamp_seconds, 10),
    timestamp_formatted: q.timestamp_formatted || '00:00',
    question: q.question,
    options: q.options || {},
    correct_answer: (q.correct_answer || 'A').toUpperCase(),
    rewind_seconds: parseInt(q.rewind_seconds, 10) || 0,
    rewind_formatted: q.rewind_formatted || '00:00',
    explanation: q.explanation || null,
    answered: false
}));

let maxWatchedTime = 0;
let isQuestionActive = false;
let isVideoFinished = @json($isCompleted ?? false);
let hasVideoStarted = false;
let strictPollTimer = null;
let toastTimeout = null;
let activeQuestion = null;

const html5Video = document.getElementById('html5-strict-player');
let ytStrictPlayer = null;

function formatSeconds(sec) {
    if (isNaN(sec) || sec < 0) sec = 0;
    const m = Math.floor(sec / 60);
    const s = Math.floor(sec % 60);
    return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
}

function showStrictToast(msg) {
    const toast = document.getElementById('video-strict-toast');
    const msgEl = document.getElementById('video-toast-msg');
    if (!toast || !msgEl) return;
    msgEl.textContent = msg;
    toast.classList.remove('opacity-0', '-translate-y-2', 'pointer-events-none');
    toast.classList.add('opacity-100', 'translate-y-0');
    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
        toast.classList.remove('opacity-100', 'translate-y-0');
        toast.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
    }, 2500);
}

function startStrictVideoPlayback() {
    const startOverlay = document.getElementById('video-start-overlay');
    if (startOverlay) startOverlay.classList.add('hidden');
    hasVideoStarted = true;

    if (isYouTubeVideo && ytStrictPlayer && typeof ytStrictPlayer.playVideo === 'function') {
        ytStrictPlayer.playVideo();
    } else if (html5Video) {
        html5Video.play().catch(e => console.warn('Autoplay error:', e));
    }
}

function toggleVideoMute() {
    const btn = document.getElementById('btn-video-mute');
    if (isYouTubeVideo && ytStrictPlayer && typeof ytStrictPlayer.isMuted === 'function') {
        if (ytStrictPlayer.isMuted()) {
            ytStrictPlayer.unMute();
            if (btn) btn.textContent = '🔊';
        } else {
            ytStrictPlayer.mute();
            if (btn) btn.textContent = '🔇';
        }
    } else if (html5Video) {
        html5Video.muted = !html5Video.muted;
        if (btn) btn.textContent = html5Video.muted ? '🔇' : '🔊';
    }
}

function toggleVideoFullscreen() {
    const container = document.getElementById('video-player-container');
    if (!container) return;
    if (!document.fullscreenElement) {
        if (container.requestFullscreen) container.requestFullscreen();
        else if (container.webkitRequestFullscreen) container.webkitRequestFullscreen();
        else if (container.msRequestFullscreen) container.msRequestFullscreen();
    } else {
        if (document.exitFullscreen) document.exitFullscreen();
    }
}

function toggleDocFullscreen() {
    const container = document.getElementById('doc-viewer-container');
    if (!container) return;
    if (!document.fullscreenElement) {
        if (container.requestFullscreen) container.requestFullscreen();
        else if (container.webkitRequestFullscreen) container.webkitRequestFullscreen();
        else if (container.msRequestFullscreen) container.msRequestFullscreen();
    } else {
        if (document.exitFullscreen) document.exitFullscreen();
    }
}

function openAddVideoQuestionModal() {
    const modal = document.getElementById('modal-add-video-question');
    if (modal) modal.classList.remove('hidden');
}

function closeAddVideoQuestionModal() {
    const modal = document.getElementById('modal-add-video-question');
    if (modal) modal.classList.add('hidden');
}

// Setup YouTube Player if applicable
if (isYouTubeVideo && ytVideoId) {
    if (!window.onYouTubeIframeAPIReady) {
        window.onYouTubeIframeAPIReady = function() {
            initYouTubeStrictPlayer();
        };
    }
    if (window.YT && window.YT.Player) {
        initYouTubeStrictPlayer();
    } else {
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        if (firstScriptTag && firstScriptTag.parentNode) {
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        } else {
            document.head.appendChild(tag);
        }
    }
}

function initYouTubeStrictPlayer() {
    const target = document.getElementById('yt-strict-player-target');
    if (!target || ytStrictPlayer || !ytVideoId) return;
    ytStrictPlayer = new YT.Player('yt-strict-player-target', {
        videoId: ytVideoId,
        playerVars: {
            autoplay: 0,
            controls: 0,
            disablekb: 1,
            fs: 0,
            modestbranding: 1,
            rel: 0,
            playsinline: 1
        },
        events: {
            onReady: onYTReady,
            onStateChange: onYTStateChange,
            onPlaybackRateChange: onYTRateChange
        }
    });
}

function onYTReady(event) {
    startStrictPolling();
}

function onYTRateChange(event) {
    if (ytStrictPlayer && typeof ytStrictPlayer.getPlaybackRate === 'function') {
        const rate = ytStrictPlayer.getPlaybackRate();
        if (rate !== 1) {
            ytStrictPlayer.setPlaybackRate(1);
            showStrictToast('⚠️ Kecepatan video terkunci pada 1.0x dan tidak dapat diubah.');
        }
    }
}

function onYTStateChange(event) {
    // YT.PlayerState.PAUSED === 2
    if (event.data === 2) {
        if (!isQuestionActive && !isVideoFinished && hasVideoStarted) {
            showStrictToast('⚠️ Video wajib ditonton tanpa jeda (pause) sesuai arahan pembelajaran.');
            setTimeout(() => {
                if (ytStrictPlayer && !isQuestionActive && !isVideoFinished) {
                    ytStrictPlayer.playVideo();
                }
            }, 150);
        }
    }
    // YT.PlayerState.ENDED === 0
    if (event.data === 0) {
        handleVideoCompletion();
    }
}

// Setup HTML5 Video listeners if applicable
if (html5Video) {
    html5Video.addEventListener('loadedmetadata', () => {
        startStrictPolling();
    });

    html5Video.addEventListener('ratechange', () => {
        if (html5Video.playbackRate !== 1.0) {
            html5Video.playbackRate = 1.0;
            showStrictToast('⚠️ Kecepatan video terkunci pada 1.0x dan tidak dapat diubah.');
        }
    });

    html5Video.addEventListener('seeking', () => {
        if (html5Video.currentTime > maxWatchedTime + 1.2) {
            html5Video.currentTime = maxWatchedTime;
            showStrictToast('🚫 Dilarang melompati menit video! Anda wajib menyimak secara utuh.');
        }
    });

    html5Video.addEventListener('pause', () => {
        if (!isQuestionActive && !isVideoFinished && hasVideoStarted && !html5Video.ended) {
            showStrictToast('⚠️ Video wajib ditonton tanpa jeda (pause) sesuai arahan pembelajaran.');
            setTimeout(() => {
                if (!isQuestionActive && !isVideoFinished) {
                    html5Video.play().catch(e => {});
                }
            }, 150);
        }
    });

    html5Video.addEventListener('ended', () => {
        handleVideoCompletion();
    });
}

function startStrictPolling() {
    if (strictPollTimer) clearInterval(strictPollTimer);
    strictPollTimer = setInterval(strictTick, 300);
}

function strictTick() {
    if (!hasVideoStarted || isQuestionActive || isVideoFinished) return;

    let cur = 0;
    let dur = 0;

    if (isYouTubeVideo && ytStrictPlayer && typeof ytStrictPlayer.getCurrentTime === 'function') {
        cur = ytStrictPlayer.getCurrentTime() || 0;
        dur = ytStrictPlayer.getDuration() || 0;
    } else if (html5Video) {
        cur = html5Video.currentTime || 0;
        dur = html5Video.duration || 0;
    }

    if (dur <= 0) return;

    // Update time indicators
    const formattedCur = formatSeconds(cur);
    const formattedDur = formatSeconds(dur);
    const liveTimer = document.getElementById('video-live-timer');
    const ctrlTimer = document.getElementById('video-ctrl-timer');
    if (liveTimer) liveTimer.textContent = formattedCur + ' / ' + formattedDur;
    if (ctrlTimer) ctrlTimer.textContent = formattedCur + ' / ' + formattedDur;

    // Update progress bar width
    const pct = Math.min(100, Math.max(0, (cur / dur) * 100));
    const pBar = document.getElementById('video-progress-bar');
    if (pBar) pBar.style.width = pct + '%';

    // Anti-skip check: If student seeks forward ahead of maxWatchedTime
    if (cur > maxWatchedTime + 1.8) {
        showStrictToast('🚫 Dilarang melompati menit video! Anda wajib menyimak secara utuh.');
        seekToStrict(maxWatchedTime);
    } else if (cur > maxWatchedTime) {
        maxWatchedTime = cur;
    }

    // Check Interactive Questions trigger
    for (let q of videoQuestions) {
        if (!q.answered && cur >= q.timestamp_seconds) {
            triggerVideoQuestion(q);
            break;
        }
    }

    // End of video check
    if (dur > 5 && cur >= (dur - 0.6)) {
        handleVideoCompletion();
    }
}

function seekToStrict(targetSec) {
    if (isYouTubeVideo && ytStrictPlayer && typeof ytStrictPlayer.seekTo === 'function') {
        ytStrictPlayer.seekTo(targetSec, true);
    } else if (html5Video) {
        html5Video.currentTime = targetSec;
    }
}

function triggerVideoQuestion(q) {
    activeQuestion = q;
    isQuestionActive = true;

    // Pause video
    if (isYouTubeVideo && ytStrictPlayer && typeof ytStrictPlayer.pauseVideo === 'function') {
        ytStrictPlayer.pauseVideo();
    } else if (html5Video) {
        html5Video.pause();
    }

    // Render Question overlay
    const overlay = document.getElementById('video-question-overlay');
    const timeLabel = document.getElementById('q-timestamp-label');
    const qText = document.getElementById('q-text-content');
    const optContainer = document.getElementById('q-options-container');
    const feedbackBox = document.getElementById('q-feedback-box');

    if (!overlay || !qText || !optContainer) return;

    if (timeLabel) timeLabel.textContent = 'Menit ' + (q.timestamp_formatted || formatSeconds(q.timestamp_seconds));
    qText.textContent = q.question;

    if (feedbackBox) {
        feedbackBox.classList.add('hidden');
        feedbackBox.className = 'p-3.5 rounded-2xl text-xs font-bold leading-relaxed hidden';
    }

    // Render options
    optContainer.innerHTML = '';
    const keys = ['A', 'B', 'C', 'D'];
    keys.forEach(k => {
        if (q.options && q.options[k]) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'q-opt-btn w-full p-3.5 rounded-2xl bg-slate-800/90 hover:bg-indigo-600/40 border border-slate-700 hover:border-indigo-500 text-left text-xs text-white transition flex items-center gap-3 group';
            btn.innerHTML = `
                <span class="opt-key w-7 h-7 rounded-xl bg-slate-700 group-hover:bg-indigo-600 text-white font-black flex items-center justify-center shrink-0 text-xs transition">${k}</span>
                <span class="opt-val flex-1 font-medium">${q.options[k]}</span>
            `;
            btn.onclick = () => submitVideoQuestionAnswer(k, btn);
            optContainer.appendChild(btn);
        }
    });

    overlay.classList.remove('hidden');
}

function submitVideoQuestionAnswer(selectedKey, clickedBtn) {
    if (!activeQuestion) return;

    // Disable all option buttons
    const allBtns = document.querySelectorAll('.q-opt-btn');
    allBtns.forEach(b => b.disabled = true);

    const feedbackBox = document.getElementById('q-feedback-box');
    const feedbackText = document.getElementById('q-feedback-text');

    const isCorrect = (selectedKey.toUpperCase() === activeQuestion.correct_answer.toUpperCase());

    if (isCorrect) {
        // Correct answer
        if (clickedBtn) {
            clickedBtn.classList.remove('bg-slate-800/90', 'border-slate-700');
            clickedBtn.classList.add('bg-emerald-600/30', 'border-emerald-500');
            const keyEl = clickedBtn.querySelector('.opt-key');
            if (keyEl) keyEl.classList.add('bg-emerald-600');
        }

        if (feedbackBox && feedbackText) {
            feedbackText.innerHTML = '🎉 Jawaban Anda Benar Sekali! Pemahaman Anda sangat baik. Video otomatis dilanjutkan...';
            feedbackBox.className = 'p-3.5 rounded-2xl text-xs font-bold leading-relaxed bg-emerald-500/20 border border-emerald-500/50 text-emerald-300 block animate-fadeIn';
        }

        activeQuestion.answered = true;

        setTimeout(() => {
            const overlay = document.getElementById('video-question-overlay');
            if (overlay) overlay.classList.add('hidden');
            isQuestionActive = false;

            // Resume playback
            if (isYouTubeVideo && ytStrictPlayer && typeof ytStrictPlayer.playVideo === 'function') {
                ytStrictPlayer.playVideo();
            } else if (html5Video) {
                html5Video.play().catch(e => {});
            }
        }, 1500);
    } else {
        // Wrong answer
        if (clickedBtn) {
            clickedBtn.classList.remove('bg-slate-800/90', 'border-slate-700');
            clickedBtn.classList.add('bg-rose-600/30', 'border-rose-500');
            const keyEl = clickedBtn.querySelector('.opt-key');
            if (keyEl) keyEl.classList.add('bg-rose-600');
        }

        const rewindText = activeQuestion.rewind_formatted || formatSeconds(activeQuestion.rewind_seconds);
        if (feedbackBox && feedbackText) {
            feedbackText.innerHTML = `❌ Jawaban Kurang Tepat! Materi akan diulang ke menit <strong>${rewindText}</strong> agar Anda dapat menyimak kembali penjelasan materi ini dengan seksama.`;
            feedbackBox.className = 'p-3.5 rounded-2xl text-xs font-bold leading-relaxed bg-rose-500/20 border border-rose-500/50 text-rose-300 block animate-fadeIn';
        }

        setTimeout(() => {
            const overlay = document.getElementById('video-question-overlay');
            if (overlay) overlay.classList.add('hidden');
            isQuestionActive = false;

            // Rewind video to rewind_seconds
            const targetSec = activeQuestion.rewind_seconds || 0;
            seekToStrict(targetSec);
            maxWatchedTime = targetSec;

            // Resume playback from rewind
            if (isYouTubeVideo && ytStrictPlayer && typeof ytStrictPlayer.playVideo === 'function') {
                ytStrictPlayer.playVideo();
            } else if (html5Video) {
                html5Video.play().catch(e => {});
            }
        }, 2200);
    }
}

function syncVideoCheckbox(checked) {
    const chk1 = document.getElementById('chk-video-confirmed');
    const chk2 = document.getElementById('chk-video-confirmed-overlay');
    if (chk1) chk1.checked = checked;
    if (chk2) chk2.checked = checked;
    updateVideoFinishState();
}

function updateVideoFinishState() {
    const chk = document.getElementById('chk-video-confirmed');
    const isChecked = chk && chk.checked;
    const finishBtn = document.getElementById('btn-video-finish');
    const finishLabel = document.getElementById('btn-video-finish-label');
    const overlayBtn = document.getElementById('btn-video-finish-overlay');
    const overlayLabel = document.getElementById('btn-video-finish-overlay-label');
    const note = document.getElementById('video-finish-status-note');

    if (!isVideoFinished) {
        if (finishBtn) {
            finishBtn.disabled = true;
            finishBtn.className = 'w-full sm:w-auto px-6 py-3 bg-slate-700 opacity-50 cursor-not-allowed text-slate-300 font-black text-xs sm:text-sm rounded-xl transition-all flex items-center justify-center gap-2';
        }
        if (finishLabel) {
            finishLabel.innerHTML = '⏳ Selesaikan & Buka Materi Berikutnya (Video Belum Selesai Disimak)';
        }
        if (overlayBtn) {
            overlayBtn.disabled = true;
            overlayBtn.className = 'px-8 py-3.5 bg-slate-700 opacity-50 cursor-not-allowed text-slate-300 font-black text-sm rounded-2xl shadow-xl transition-all flex items-center gap-2.5';
        }
        if (overlayLabel) {
            overlayLabel.innerHTML = '⏳ Menyimak Video Belum Selesai';
        }
        if (note) {
            note.innerHTML = 'Tombol Selesaikan & Buka Materi Berikutnya terkunci dan akan aktif setelah video selesai disimak serta dicentang konfirmasi.';
        }
        return;
    }

    if (!isChecked) {
        if (finishBtn) {
            finishBtn.disabled = true;
            finishBtn.className = 'w-full sm:w-auto px-6 py-3 bg-slate-700 opacity-60 cursor-not-allowed text-amber-300 font-black text-xs sm:text-sm rounded-xl transition-all flex items-center justify-center gap-2';
        }
        if (finishLabel) {
            finishLabel.innerHTML = '🔒 Selesaikan & Buka Materi Berikutnya (Wajib Ceklis Tuntas Disimak)';
        }
        if (overlayBtn) {
            overlayBtn.disabled = true;
            overlayBtn.className = 'px-8 py-3.5 bg-slate-700 opacity-60 cursor-not-allowed text-amber-300 font-black text-sm rounded-2xl shadow-xl transition-all flex items-center gap-2.5';
        }
        if (overlayLabel) {
            overlayLabel.innerHTML = '🔒 Wajib Ceklis Tuntas Disimak di Atas';
        }
        if (note) {
            note.innerHTML = '⚠️ Video selesai disimak! Tombol masih terkunci. Silakan centang kotak "Saya telah tuntas menyimak video pembelajaran ini" untuk membukanya.';
        }
        return;
    }

    // Both video finished AND checkbox checked!
    if (finishBtn) {
        finishBtn.disabled = false;
        finishBtn.className = 'w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs sm:text-sm rounded-xl shadow-lg shadow-emerald-500/25 cursor-pointer hover:scale-[1.02] transition-all flex items-center justify-center gap-2';
    }
    if (finishLabel) {
        finishLabel.innerHTML = '🎉 Selesaikan & Buka Materi Berikutnya';
    }
    if (overlayBtn) {
        overlayBtn.disabled = false;
        overlayBtn.className = 'px-8 py-3.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 hover:scale-105 active:scale-95 text-white font-black text-sm rounded-2xl shadow-xl shadow-emerald-500/30 transition-all flex items-center gap-2.5 cursor-pointer';
    }
    if (overlayLabel) {
        overlayLabel.innerHTML = '🎉 Selesaikan Materi & Buka Selanjutnya';
    }
    if (note) {
        note.innerHTML = '✨ Konfirmasi lengkap! Klik tombol di samping untuk menyelesaikan materi ini dan membuka materi selanjutnya.';
    }
}

function validateVideoFinishSubmit(e) {
    const chk = document.getElementById('chk-video-confirmed');
    if (!chk || !chk.checked) {
        if (e) e.preventDefault();
        alert('Tombol terkunci: Anda wajib mencentang konfirmasi "Saya telah tuntas menyimak video pembelajaran ini" sebelum menyelesaikan materi!');
        return false;
    }
    if (!isVideoFinished) {
        if (e) e.preventDefault();
        alert('Tombol terkunci: Anda belum menyelesaikan video pembelajaran hingga tuntas!');
        return false;
    }
    return true;
}

function handleVideoCompletion() {
    if (isVideoFinished) return;
    isVideoFinished = true;

    // Show in-video completion overlay
    const overlay = document.getElementById('video-finish-overlay');
    if (overlay) overlay.classList.remove('hidden');

    updateVideoFinishState();
}
</script>

<!-- MODAL TAMBAH PERTANYAAN DI MENIT VIDEO (KHUSUS GURU) -->
@if($canManage)
<div id="modal-add-video-question" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm hidden" onclick="if(event.target === this) closeAddVideoQuestionModal()">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl text-white space-y-4 relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-sm">❓</span>
                <h3 class="text-sm font-bold text-white">Tambah Pertanyaan di Menit Video</h3>
            </div>
            <button type="button" onclick="closeAddVideoQuestionModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-xs font-bold transition">✕</button>
        </div>

        <form action="{{ route('lms.materials.video_questions.store', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" method="POST" class="space-y-4">
            @csrf
            <!-- Titik Muncul Pertanyaan -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">
                    Waktu Muncul Pertanyaan (Menit & Detik):
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="text-[11px] text-slate-400 mb-1">Menit (0-300):</div>
                        <input type="number" name="timestamp_minute" min="0" max="300" value="1" required class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-400 mb-1">Detik (0-59):</div>
                        <input type="number" name="timestamp_second" min="0" max="59" value="0" required class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Teks Pertanyaan -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">
                    Teks Pertanyaan Interaktif:
                </label>
                <textarea name="question" rows="2" required placeholder="Contoh: Apa fungsi utama dari materi yang baru saja dijelaskan?" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:border-amber-500 focus:outline-none placeholder:text-slate-500"></textarea>
            </div>

            <!-- Pilihan Jawaban -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-300">
                    Pilihan Jawaban (A, B wajib; C, D opsional):
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <input type="text" name="option_a" required placeholder="Opsi A (Wajib)" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:border-amber-500 focus:outline-none placeholder:text-slate-500">
                    </div>
                    <div>
                        <input type="text" name="option_b" required placeholder="Opsi B (Wajib)" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:border-amber-500 focus:outline-none placeholder:text-slate-500">
                    </div>
                    <div>
                        <input type="text" name="option_c" placeholder="Opsi C (Opsional)" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:border-amber-500 focus:outline-none placeholder:text-slate-500">
                    </div>
                    <div>
                        <input type="text" name="option_d" placeholder="Opsi D (Opsional)" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:border-amber-500 focus:outline-none placeholder:text-slate-500">
                    </div>
                </div>
            </div>

            <!-- Kunci Jawaban & Menit Mundur jika Salah -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        Kunci Jawaban Benar:
                    </label>
                    <select name="correct_answer" required class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:border-amber-500 focus:outline-none">
                        <option value="A">Opsi A</option>
                        <option value="B">Opsi B</option>
                        <option value="C">Opsi C</option>
                        <option value="D">Opsi D</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        Jika Salah, Mundur ke Menit:
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="rewind_minute" min="0" max="300" value="0" required placeholder="Menit" class="w-full px-2.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs font-mono focus:border-amber-500 focus:outline-none">
                        <input type="number" name="rewind_second" min="0" max="59" value="0" required placeholder="Detik" class="w-full px-2.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Penjelasan Singkat (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">
                    Catatan / Penjelasan Tambahan (Opsional):
                </label>
                <input type="text" name="explanation" placeholder="Penjelasan singkat materi ini..." class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:border-amber-500 focus:outline-none placeholder:text-slate-500">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                <button type="button" onclick="closeAddVideoQuestionModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-black bg-amber-500 hover:bg-amber-400 text-slate-950 transition shadow-md">
                    Simpan Pertanyaan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@if($docExt === 'pdf')
<!-- PDF.js CDN & Interactive Viewer Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
let pdfDoc = null;
let pdfPageNum = 1;
let pdfPageRendering = false;
let pdfPageNumPending = null;
let pdfScale = 1.25;
let globalPdfBlobUrl = null;
let isNativePdfMode = false;
const pdfCanvas = document.getElementById('pdf-canvas');
const pdfCtx = pdfCanvas ? pdfCanvas.getContext('2d') : null;
const previewDocUrl = "{{ route('lms.materials.preview', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}";

function base64ToUint8Array(base64) {
    const raw = atob(base64);
    const rawLength = raw.length;
    const array = new Uint8Array(new ArrayBuffer(rawLength));
    for (let i = 0; i < rawLength; i++) {
        array[i] = raw.charCodeAt(i);
    }
    return array;
}

function showPdfIframeFallback(blobUrl) {
    isNativePdfMode = true;
    const canvas = document.getElementById('pdf-canvas');
    const iframe = document.getElementById('pdf-embed-frame');
    const spinner = document.getElementById('pdf-loading-spinner');
    const toggleBtnText = document.getElementById('btn-toggle-native-text');
    if (canvas) canvas.classList.add('hidden');
    if (spinner) spinner.classList.add('hidden');
    if (iframe) {
        if (!iframe.src || iframe.src === 'about:blank' || iframe.src === window.location.href) {
            iframe.src = blobUrl + '#toolbar=1&navpanes=0';
        }
        iframe.classList.remove('hidden');
    }
    if (toggleBtnText) toggleBtnText.textContent = 'Mode Interaktif';
}

function showPdfCanvasMode() {
    isNativePdfMode = false;
    const canvas = document.getElementById('pdf-canvas');
    const iframe = document.getElementById('pdf-embed-frame');
    const spinner = document.getElementById('pdf-loading-spinner');
    const toggleBtnText = document.getElementById('btn-toggle-native-text');
    if (iframe) iframe.classList.add('hidden');
    if (spinner) spinner.classList.add('hidden');
    if (canvas) canvas.classList.remove('hidden');
    if (toggleBtnText) toggleBtnText.textContent = 'Mode Native';
    if (pdfDoc) renderPdfPage(pdfPageNum);
}

function togglePdfViewerMode() {
    if (!globalPdfBlobUrl) return;
    if (isNativePdfMode) {
        if (pdfDoc) {
            showPdfCanvasMode();
        } else {
            showPdfIframeFallback(globalPdfBlobUrl);
        }
    } else {
        showPdfIframeFallback(globalPdfBlobUrl);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const jsonDocUrl = previewDocUrl + (previewDocUrl.includes('?') ? '&' : '?') + 'format=json';

    fetch(jsonDocUrl, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status + ': Gagal mengambil berkas');
        return res.json();
    })
    .then(payload => {
        if (!payload.success || !payload.base64) {
            throw new Error('Respon data dokumen tidak valid');
        }

        const bytes = base64ToUint8Array(payload.base64);
        const pdfBlob = new Blob([bytes], { type: 'application/pdf' });
        globalPdfBlobUrl = URL.createObjectURL(pdfBlob);

        // Bind tombol "Buka di Tab Baru" ke URL Blob lokal (bebas intersepsi IDM)
        const newTabBtn = document.getElementById('btn-open-doc-newtab');
        if (newTabBtn) {
            newTabBtn.href = globalPdfBlobUrl;
            newTabBtn.target = '_blank';
        }

        // Coba inisialisasi PDF.js canvas viewer
        if (typeof pdfjsLib !== 'undefined') {
            try {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            } catch(wErr) {
                console.warn('PDF worker config:', wErr);
            }

            pdfjsLib.getDocument({ data: bytes }).promise
                .then(pdf => {
                    pdfDoc = pdf;
                    const countEl = document.getElementById('pdf-page-count');
                    if (countEl) countEl.textContent = pdf.numPages;
                    const spinner = document.getElementById('pdf-loading-spinner');
                    if (spinner) spinner.classList.add('hidden');
                    if (pdfCanvas) pdfCanvas.classList.remove('hidden');
                    renderPdfPage(pdfPageNum);
                })
                .catch(err => {
                    console.warn('PDF.js gagal merender canvas, beralih ke native iframe viewer:', err);
                    showPdfIframeFallback(globalPdfBlobUrl);
                });
        } else {
            showPdfIframeFallback(globalPdfBlobUrl);
        }
    })
    .catch(err => {
        console.error('Gagal memuat dokumen PDF:', err);
        const spinner = document.getElementById('pdf-loading-spinner');
        if (spinner) {
            spinner.innerHTML = `
                <div class="p-6 text-center text-slate-300 space-y-3">
                    <div class="text-3xl">📕</div>
                    <p class="text-xs">Dokumen PDF siap dibaca.</p>
                    <a href="${previewDocUrl}" target="_blank" download class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-sm transition">
                        <span>Buka / Unduh Berkas PDF</span>
                    </a>
                </div>
            `;
        }
    });
});

function renderPdfPage(num) {
    if (!pdfDoc) return;
    pdfPageRendering = true;

    pdfDoc.getPage(num).then(page => {
        const viewport = page.getViewport({ scale: pdfScale });
        if (pdfCanvas) {
            pdfCanvas.height = viewport.height;
            pdfCanvas.width = viewport.width;
        }

        const renderContext = {
            canvasContext: pdfCtx,
            viewport: viewport
        };
        const renderTask = page.render(renderContext);

        renderTask.promise.then(() => {
            pdfPageRendering = false;
            if (pdfPageNumPending !== null) {
                renderPdfPage(pdfPageNumPending);
                pdfPageNumPending = null;
            }
        });
    });

    const numEl = document.getElementById('pdf-page-num');
    if (numEl) numEl.textContent = num;
    const prevBtn = document.getElementById('pdf-btn-prev');
    const nextBtn = document.getElementById('pdf-btn-next');
    if (prevBtn) prevBtn.disabled = (num <= 1);
    if (nextBtn) nextBtn.disabled = (num >= pdfDoc.numPages);
}

function queuePdfRenderPage(num) {
    if (pdfPageRendering) {
        pdfPageNumPending = num;
    } else {
        renderPdfPage(num);
    }
}

function pdfPrevPage() {
    if (isNativePdfMode && globalPdfBlobUrl) {
        showPdfCanvasMode();
    }
    if (pdfPageNum <= 1) return;
    pdfPageNum--;
    queuePdfRenderPage(pdfPageNum);
}

function pdfNextPage() {
    if (isNativePdfMode && globalPdfBlobUrl) {
        showPdfCanvasMode();
    }
    if (!pdfDoc || pdfPageNum >= pdfDoc.numPages) return;
    pdfPageNum++;
    queuePdfRenderPage(pdfPageNum);
}

function pdfZoomIn() {
    if (isNativePdfMode && globalPdfBlobUrl) {
        showPdfCanvasMode();
    }
    pdfScale = Math.min(3.0, pdfScale + 0.2);
    updatePdfZoomLabel();
    queuePdfRenderPage(pdfPageNum);
}

function pdfZoomOut() {
    if (isNativePdfMode && globalPdfBlobUrl) {
        showPdfCanvasMode();
    }
    pdfScale = Math.max(0.6, pdfScale - 0.2);
    updatePdfZoomLabel();
    queuePdfRenderPage(pdfPageNum);
}

function pdfFitWidth() {
    if (isNativePdfMode && globalPdfBlobUrl) {
        showPdfCanvasMode();
    }
    const container = document.getElementById('pdf-scroll-container');
    if (container && container.clientWidth) {
        pdfScale = Math.max(0.8, (container.clientWidth - 40) / 700);
        updatePdfZoomLabel();
        queuePdfRenderPage(pdfPageNum);
    }
}

function updatePdfZoomLabel() {
    const lbl = document.getElementById('pdf-zoom-label');
    if (lbl) lbl.textContent = Math.round(pdfScale * 100) + '%';
}
</script>
@endif

@if($docExt === 'docx')
<!-- Mammoth DOCX Reader -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewUrl = "{{ route('lms.materials.preview', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}";
    const jsonUrl = previewUrl + (previewUrl.includes('?') ? '&' : '?') + 'format=json';

    fetch(jsonUrl, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(payload => {
        if (!payload.success || !payload.base64) throw new Error('Data dokumen Word tidak valid');
        const raw = atob(payload.base64);
        const bytes = new Uint8Array(raw.length);
        for (let i = 0; i < raw.length; i++) {
            bytes[i] = raw.charCodeAt(i);
        }
        return mammoth.convertToHtml({ arrayBuffer: bytes.buffer });
    })
    .then(result => {
        const loading = document.getElementById('docx-loading');
        const content = document.getElementById('docx-content');
        if (loading) loading.classList.add('hidden');
        if (content) {
            content.innerHTML = result.value || '<p class="text-slate-500 italic">Dokumen kosong atau tidak memiliki teks yang dapat ditampilkan.</p>';
        }
    })
    .catch(err => {
        console.error('DOCX load error:', err);
        const loading = document.getElementById('docx-loading');
        const content = document.getElementById('docx-content');
        if (loading) loading.classList.add('hidden');
        if (content) {
            content.innerHTML = `
                <div class="p-8 text-center space-y-3">
                    <div class="text-4xl">📄</div>
                    <h4 class="font-bold text-slate-900 dark:text-white">Dokumen: {{ basename($material->file_name ?: $material->file_path) }}</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">Dokumen telah disiapkan di peramban. Anda dapat membuka atau membaca materi melalui penampil di tab baru.</p>
                    <a href="${previewUrl}" target="_blank" download class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition">
                        <span>Buka / Unduh Berkas Word</span>
                    </a>
                </div>
            `;
        }
    });
});
</script>
@endif

@if(in_array($docExt, ['pptx', 'ppt']))
<!-- JSZip & PPTX Interactive Slide Deck Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
let pptxSlides = [];
let currentPptxIndex = 0;
const previewPptxUrl = "{{ route('lms.materials.preview', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}";

document.addEventListener('DOMContentLoaded', function() {
    const jsonPptxUrl = previewPptxUrl + (previewPptxUrl.includes('?') ? '&' : '?') + 'format=json';

    fetch(jsonPptxUrl, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(payload => {
        if (!payload.success || !payload.base64) throw new Error('Data presentasi tidak valid');
        const raw = atob(payload.base64);
        const bytes = new Uint8Array(raw.length);
        for (let i = 0; i < raw.length; i++) {
            bytes[i] = raw.charCodeAt(i);
        }
        return JSZip.loadAsync(bytes.buffer);
    })
    .then(zip => {
        const slidePaths = [];
        zip.forEach((path) => {
            const match = path.match(/^ppt\/slides\/slide(\d+)\.xml$/);
            if (match) {
                slidePaths.push({ path: path, num: parseInt(match[1]) });
            }
        });

        slidePaths.sort((a, b) => a.num - b.num);

        if (slidePaths.length === 0) {
            throw new Error('Tidak ditemukan format XML slide di berkas ini');
        }

        const promises = slidePaths.map(sp => zip.file(sp.path).async('string'));
        return Promise.all(promises);
    })
    .then(slideXmls => {
        pptxSlides = slideXmls.map((xml, idx) => parsePptxSlideXml(xml, idx + 1));
        const countEl = document.getElementById('pptx-slide-count');
        if (countEl) countEl.textContent = pptxSlides.length;
        const spinner = document.getElementById('pptx-loading');
        if (spinner) spinner.classList.add('hidden');
        const content = document.getElementById('pptx-slide-content');
        if (content) content.classList.remove('hidden');
        renderPptxSlide(0);
    })
    .catch(err => {
        console.error('PPTX load error:', err);
        const spinner = document.getElementById('pptx-loading');
        if (spinner) {
            spinner.innerHTML = `
                <div class="p-6 text-center space-y-3">
                    <div class="text-4xl">📊</div>
                    <h4 class="font-bold text-white">Tayangan Presentasi Slide (PowerPoint)</h4>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">Dokumen tayangan slide telah disiapkan. Klik tombol di bawah untuk mengunduh dan menyimak di aplikasi PowerPoint.</p>
                    <a href="${previewPptxUrl}" target="_blank" download class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-md transition">
                        <span>Unduh Berkas Presentasi PPT</span>
                    </a>
                </div>
            `;
        }
    });
});

function parsePptxSlideXml(xml, slideNum) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(xml, 'text/xml');
    const textNodes = doc.querySelectorAll('t');
    const paragraphs = [];
    let currentPara = [];

    textNodes.forEach(t => {
        const str = t.textContent.trim();
        if (str) {
            currentPara.push(str);
        }
    });

    const fullText = currentPara.join(' ');
    return {
        num: slideNum,
        rawText: fullText,
        elements: currentPara
    };
}

function renderPptxSlide(index) {
    if (!pptxSlides || pptxSlides.length === 0) return;
    currentPptxIndex = Math.max(0, Math.min(index, pptxSlides.length - 1));
    const slide = pptxSlides[currentPptxIndex];
    const container = document.getElementById('pptx-slide-content');
    if (!container) return;

    let title = `Slide ${slide.num}`;
    let bodyElements = slide.elements;

    if (slide.elements.length > 0) {
        title = slide.elements[0];
        bodyElements = slide.elements.slice(1);
    }

    container.innerHTML = `
        <div class="space-y-4 animate-fadeIn">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 text-[11px] font-black uppercase">
                    Slide Pembelajaran #${slide.num}
                </span>
                <span class="text-xs text-slate-400 font-mono">${slide.num} dari ${pptxSlides.length} Slide</span>
            </div>
            <h3 class="text-lg sm:text-xl font-black text-white leading-snug">
                ${escapeDocHtml(title)}
            </h3>
            <div class="space-y-2.5 text-xs sm:text-sm text-slate-300 leading-relaxed pt-2">
                ${bodyElements.length > 0
                    ? bodyElements.map(el => `<div class="flex items-start gap-2.5 p-2 rounded-xl bg-slate-900/60 border border-slate-800/80"><span class="text-amber-400 font-bold">•</span><span>${escapeDocHtml(el)}</span></div>`).join('')
                    : '<p class="text-slate-500 italic">Slide ini memuat ilustrasi grafis/diagram visual.</p>'
                }
            </div>
        </div>
    `;

    const numEl = document.getElementById('pptx-slide-num');
    if (numEl) numEl.textContent = slide.num;
    const prevBtn = document.getElementById('pptx-btn-prev');
    const nextBtn = document.getElementById('pptx-btn-next');
    if (prevBtn) prevBtn.disabled = (currentPptxIndex <= 0);
    if (nextBtn) nextBtn.disabled = (currentPptxIndex >= pptxSlides.length - 1);
}

function pptxPrevSlide() {
    if (currentPptxIndex > 0) {
        renderPptxSlide(currentPptxIndex - 1);
    }
}

function pptxNextSlide() {
    if (currentPptxIndex < pptxSlides.length - 1) {
        renderPptxSlide(currentPptxIndex + 1);
    }
}

function escapeDocHtml(text) {
    if (!text) return '';
    return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}
</script>
@endif

<!-- MODAL EDIT MATERI (KHUSUS GURU / ADMIN) -->
@if($canManage)
<div id="modal-edit-material" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Edit Materi Pelajaran</h3>
            <button type="button" onclick="closeEditMaterialModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">✕</button>
        </div>
        <form action="{{ route('lms.materials.update', ['courseClass' => $courseClass->id, 'material' => $material->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Materi *</label>
                <input type="text" name="title" value="{{ $material->title }}" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bab / Topik Materi</label>
                <input type="text" name="topic" value="{{ $material->topic }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Konten *</label>
                <select name="content_type" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    <option value="document" {{ $material->content_type === 'document' ? 'selected' : '' }}>Dokumen (PDF, PPT, DOC, XLS)</option>
                    <option value="video" {{ $material->content_type === 'video' ? 'selected' : '' }}>Video (YouTube / MP4)</option>
                    <option value="link" {{ $material->content_type === 'link' ? 'selected' : '' }}>Tautan Edukasi (Website Luar)</option>
                    <option value="text" {{ $material->content_type === 'text' ? 'selected' : '' }}>Artikel Teks Langsung</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi / Petunjuk Belajar</label>
                <textarea name="description" rows="2" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white">{{ $material->description }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">URL Video / Tautan (Opsional)</label>
                <input type="text" name="video_url" value="{{ $material->external_url }}" placeholder="https://www.youtube.com/watch?v=..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Berkas Dokumen (Opsional)</label>
                <input type="file" name="attachment" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 dark:file:bg-indigo-950 dark:file:text-indigo-400">
                @if($material->file_path)
                    <p class="text-[11px] text-slate-400 mt-1">Berkas saat ini: <strong>{{ basename($material->file_name ?: $material->file_path) }}</strong></p>
                @endif
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Dimulai (Mulai Dapat Diakses Siswa)</label>
                <input type="datetime-local" name="start_time" value="{{ $material->start_time ? $material->start_time->format('Y-m-d\TH:i') : '' }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika materi dapat langsung dibuka kapan saja. Sekalipun waktu telah lewat, materi tetap tampil dan dapat diakses siswa.</p>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="edit_material_is_published" name="is_published" value="1" {{ $material->is_published ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <label for="edit_material_is_published" class="text-xs text-slate-600 dark:text-slate-400 cursor-pointer">Publikasikan materi ke siswa kelas</label>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeEditMaterialModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<script>
function openEditMaterialModal() {
    document.getElementById('modal-edit-material').classList.remove('hidden');
}
function closeEditMaterialModal() {
    document.getElementById('modal-edit-material').classList.add('hidden');
}
</script>
@endif

@include('lms.security.tracker_script', [
    'courseClass' => $courseClass,
    'blockableType' => 'material',
    'blockableId' => $material->id,
    'itemTitle' => $material->title,
])
@endsection
