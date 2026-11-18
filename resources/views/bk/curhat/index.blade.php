@extends('layouts.app')

@section('title', 'Ruang Curhat & Konseling Online BK - SMAN 1 Lengkong')

@section('content')
<div class="w-full max-w-7xl mx-auto px-1 sm:px-2 lg:px-4 py-2 sm:py-4">
    <!-- Navigasi Standar BK -->
    @include('bk.partials.nav')

    <!-- Banner Ruang Curhat & Privasi Aman -->
    <div class="mb-4 bg-gradient-to-r from-emerald-950 via-teal-900 to-slate-900 rounded-2xl p-4 sm:p-5 text-white border border-emerald-800/60 shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-2xl shrink-0 shadow-inner">
                💬
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-base sm:text-lg font-black tracking-wide text-white">Ruang Curhat &amp; Konseling Online BK</h1>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        ONLINE WHATSAPP STYLE
                    </span>
                </div>
                <p class="text-xs text-emerald-100/80 mt-0.5 flex items-center gap-1.5 flex-wrap">
                    <span>🔒</span>
                    <span><strong>Asas Kerahasiaan Terjamin:</strong> Percakapan bersifat privat, aman, dan langsung didampingi Koordinator BK &amp; Guru BK SMAN 1 Lengkong.</span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-start md:self-center shrink-0">
            <button type="button" onclick="openNewChatModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-md shadow-emerald-700/40 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Mulai Topik Curhat Baru</span>
            </button>
        </div>
    </div>

    <!-- MAIN CHAT APP CONTAINER (WhatsApp Web Style) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden flex flex-col md:flex-row h-[78vh] min-h-[580px] max-h-[820px] relative">
        
        <!-- ================= KOLOM KIRI: DAFTAR PERCAKAPAN (SIDEBAR) ================= -->
        <div id="chat-sidebar" class="w-full md:w-80 lg:w-96 border-r border-slate-200 dark:border-slate-800 flex flex-col bg-slate-50/70 dark:bg-slate-950/70 h-full {{ $activeConsultation ? 'hidden md:flex' : 'flex' }}">
            <!-- Header Sidebar -->
            <div class="p-3.5 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black text-sm shadow-md">
                        {{ $isCounselor ? 'BK' : substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800 dark:text-white truncate max-w-[170px]">
                            {{ $isCounselor ? ($bkCoordinator['name'] ?? 'Koordinator BK') : Auth::user()->name }}
                        </h3>
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $isCounselor ? 'Ruang Guru BK' : 'Akun Peserta Didik' }}
                        </span>
                    </div>
                </div>
                <button type="button" onclick="openNewChatModal()" title="Mulai Percakapan Baru" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
            </div>

            <!-- Search & Filter Bar -->
            <div class="p-2.5 bg-white/50 dark:bg-slate-900/50 border-b border-slate-200/80 dark:border-slate-800/80 space-y-2">
                <div class="relative">
                    <input type="text" id="conversation-search" placeholder="Cari siswa, kelas, topik..." 
                           class="w-full pl-8 pr-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs border border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 transition outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                
                @if($isCounselor)
                <!-- Filter Pills untuk Konselor -->
                <div class="flex items-center gap-1 overflow-x-auto scrollbar-none pb-0.5 text-[11px]">
                    <a href="{{ route('bk.curhat.index') }}" class="px-2.5 py-1 rounded-lg font-bold whitespace-nowrap {{ !request('status') ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-300' }}">Semua</a>
                    <a href="{{ route('bk.curhat.index', ['status' => 'unread']) }}" class="px-2.5 py-1 rounded-lg font-bold whitespace-nowrap {{ request('status') === 'unread' ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-300' }}">Belum Dijawab</a>
                    <a href="{{ route('bk.curhat.index', ['status' => 'active']) }}" class="px-2.5 py-1 rounded-lg font-bold whitespace-nowrap {{ request('status') === 'active' ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-300' }}">Aktif</a>
                    <a href="{{ route('bk.curhat.index', ['status' => 'resolved']) }}" class="px-2.5 py-1 rounded-lg font-bold whitespace-nowrap {{ request('status') === 'resolved' ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-300' }}">Selesai</a>
                </div>
                @endif
            </div>

            <!-- List Percakapan -->
            <div id="conversation-list" class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 scrollbar-thin">
                @forelse($consultations as $conv)
                    @php
                        $isActive = $activeConsultation && $activeConsultation->id === $conv->id;
                        $unread = $isCounselor ? $conv->unread_counselor_count : $conv->unread_student_count;
                    @endphp
                    <a href="{{ route('bk.curhat.index', ['c' => $conv->id]) }}" 
                       data-conversation-id="{{ $conv->id }}"
                       data-search-target="{{ strtolower(($conv->is_anonymous && $isCounselor ? 'anonim rahasia' : ($conv->student?->name ?? '')) . ' ' . ($conv->student?->schoolClass?->name ?? '') . ' ' . $conv->topic) }}"
                       class="conversation-item p-3 flex items-start gap-3 transition cursor-pointer hover:bg-emerald-50/50 dark:hover:bg-slate-800/50 {{ $isActive ? 'bg-emerald-100/60 dark:bg-slate-800 border-l-4 border-emerald-500' : '' }}">
                        
                        <!-- Avatar -->
                        <div class="relative shrink-0">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center font-bold text-white shadow-sm {{ $conv->is_anonymous ? 'bg-slate-700' : 'bg-gradient-to-br from-emerald-500 to-teal-600' }}">
                                @if($conv->is_anonymous)
                                    <span>🕵️</span>
                                @else
                                    <span>{{ substr($conv->student?->name ?? 'S', 0, 1) }}</span>
                                @endif
                            </div>
                            @if($conv->status === 'OPEN' || $conv->status === 'ACTIVE')
                                <span class="w-3 h-3 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900 absolute -bottom-0.5 -right-0.5"></span>
                            @else
                                <span class="w-3 h-3 rounded-full bg-slate-400 border-2 border-white dark:border-slate-900 absolute -bottom-0.5 -right-0.5"></span>
                            @endif
                        </div>

                        <!-- Info & Preview -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                    {{ $conv->display_name }}
                                </h4>
                                <span class="text-[10px] text-slate-400 whitespace-nowrap shrink-0">
                                    {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true, true) : $conv->created_at->diffForHumans(null, true, true) }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="px-1.5 py-0.2 text-[9px] font-extrabold rounded bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 truncate max-w-[120px]">
                                    {{ $conv->topic }}
                                </span>
                                @if($conv->status === 'RESOLVED')
                                    <span class="px-1 py-0.2 text-[8.5px] font-bold rounded bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400">Selesai</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-2 mt-1">
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate flex-1">
                                    {{ $conv->last_message_preview ?: 'Mulai percakapan curhat...' }}
                                </p>
                                @if($unread > 0)
                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-600 text-white shrink-0 shadow-xs">
                                        {{ $unread }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <div class="text-3xl mb-2">💬</div>
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum Ada Sesi Curhat</p>
                        <p class="text-[11px] text-slate-400 mt-1">Klik tombol di atas untuk memulai konsultasi pertama.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ================= KOLOM KANAN: RUANG CHAT AKTIF (ACTIVE CHAT WINDOW) ================= -->
        <div id="chat-window" class="flex-1 flex flex-col h-full bg-[#efeae2] dark:bg-[#0b141a] {{ $activeConsultation ? 'flex' : 'hidden md:flex' }}">
            @if($activeConsultation)
                <!-- Header Chat Aktif -->
                <div class="p-3 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2 shrink-0 z-10 shadow-xs">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Tombol Kembali (Mobile) -->
                        <button type="button" onclick="showSidebarOnMobile()" class="md:hidden p-1.5 -ml-1 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-white shrink-0 shadow-sm {{ $activeConsultation->is_anonymous ? 'bg-slate-700' : 'bg-gradient-to-br from-emerald-500 to-teal-600' }}">
                            @if($activeConsultation->is_anonymous)
                                <span>🕵️</span>
                            @else
                                <span>{{ substr($activeConsultation->student?->name ?? 'S', 0, 1) }}</span>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white truncate">
                                    {{ $activeConsultation->display_name }}
                                </h3>
                                @if($activeConsultation->is_anonymous)
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-500/20 text-amber-500 border border-amber-500/30">Anonim</span>
                                @endif
                                @if($activeConsultation->assignedCounselor)
                                    <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.2 rounded-full text-[9.5px] font-bold bg-teal-500/15 text-teal-700 dark:text-teal-300 border border-teal-500/30">
                                        <span>🎯 {{ $activeConsultation->assignedCounselor->name }}</span>
                                    </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1 truncate">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $activeConsultation->topic }}</span>
                                <span>&bull;</span>
                                <span>Kode: {{ $activeConsultation->consultation_code }}</span>
                                @if($activeConsultation->assignedCounselor)
                                    <span class="sm:hidden text-teal-600 dark:text-teal-400">&bull; {{ $activeConsultation->assignedCounselor->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        @if($isCounselor && $activeConsultation->student)
                            <a href="{{ route('bk.student.profile360', $activeConsultation->student->id) }}" target="_blank" title="Buka Profil Siswa 360°" 
                               class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition">
                                <span>🔍 Profil 360°</span>
                            </a>
                        @endif

                        @if($activeConsultation->status !== 'RESOLVED')
                            <form action="{{ route('bk.curhat.resolve', $activeConsultation) }}" method="POST" onsubmit="return confirm('Tandai sesi curhat ini sebagai selesai? Siswa tetap dapat membaca riwayat konsultasi.')">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 rounded-xl text-[11px] font-bold bg-emerald-600 hover:bg-emerald-500 text-white transition flex items-center gap-1 shadow-xs cursor-pointer">
                                    <span>✓ Selesai</span>
                                </button>
                            </form>
                        @else
                            <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                Sesi Telah Ditutup
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Chat Messages Scroll Canvas -->
                <div id="messages-container" 
                     data-consultation-id="{{ $activeConsultation->id }}" 
                     class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-3 scrollbar-thin relative"
                     style="background-image: radial-gradient(rgba(0,0,0,0.04) 1px, transparent 1px); background-size: 20px 20px;">
                    
                    <!-- Bubble Kerahasiaan BK -->
                    <div class="text-center my-2">
                        <span class="inline-block px-3 py-1 rounded-xl bg-amber-100/80 dark:bg-amber-950/40 border border-amber-300/60 dark:border-amber-800 text-amber-900 dark:text-amber-300 text-[10.5px] font-semibold max-w-md shadow-xs">
                            🔒 <strong>Pemberitahuan Konseling:</strong> Percakapan ini dilindungi asas kerahasiaan Bimbingan dan Konseling. Berbicaralah dengan jujur dan terbuka.
                        </span>
                    </div>

                    @foreach($activeConsultation->messages as $msg)
                        @php
                            $isMine = (int)$msg->sender_id === (int)Auth::id();
                        @endphp
                        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} message-row" data-message-id="{{ $msg->id }}">
                            <div class="max-w-[85%] sm:max-w-[70%] rounded-2xl p-3 shadow-md relative {{ $isMine ? 'bg-emerald-600 dark:bg-[#005c4b] text-white rounded-tr-xs' : 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-tl-xs border border-slate-200/60 dark:border-slate-700/60' }}">
                                
                                <!-- Sender Header (Jika pesan dari lawan bicara) -->
                                @if(!$isMine)
                                    <div class="flex items-center gap-1.5 mb-1 pb-1 border-b border-black/10 dark:border-white/10">
                                        <span class="text-[11px] font-black {{ $msg->sender_role === 'COUNSELOR' ? 'text-teal-600 dark:text-teal-300' : 'text-emerald-700 dark:text-emerald-400' }}">
                                            {{ $msg->sender_role === 'COUNSELOR' ? ($msg->sender?->formatted_name ?: ($msg->sender?->name ?: 'Guru BK')) : ($activeConsultation->is_anonymous ? 'Siswa Rahasia' : ($activeConsultation->student?->name ?: 'Siswa')) }}
                                        </span>
                                        @if($msg->sender_role === 'COUNSELOR')
                                            <span class="px-1 py-0.2 rounded text-[8px] font-extrabold bg-teal-500/20 text-teal-600 dark:text-teal-300">GURU BK</span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Media: Image (Gambar) -->
                                @if($msg->is_image && $msg->attachment_url)
                                    <div class="mb-2 rounded-xl overflow-hidden cursor-pointer" onclick="openLightbox('{{ $msg->attachment_url }}')">
                                        <img src="{{ $msg->attachment_url }}" alt="{{ $msg->attachment_name }}" class="max-h-72 w-auto object-cover rounded-xl hover:opacity-95 transition">
                                    </div>
                                @endif

                                <!-- Media: Video -->
                                @if($msg->is_video && $msg->attachment_url)
                                    <div class="mb-2 rounded-xl overflow-hidden bg-black max-w-sm">
                                        <video controls class="w-full max-h-72 rounded-xl">
                                            <source src="{{ $msg->attachment_url }}" type="{{ $msg->attachment_mime ?: 'video/mp4' }}">
                                            Browser Anda tidak mendukung pemutar video.
                                        </video>
                                    </div>
                                @endif

                                <!-- Media: Audio / Voice Note (Pesan Suara) -->
                                @if($msg->is_audio && $msg->attachment_url)
                                    <div class="mb-2 p-2.5 rounded-2xl bg-black/10 dark:bg-white/10 flex flex-col gap-1.5 min-w-[220px] sm:min-w-[280px]">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm shrink-0">
                                                🎙️
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-bold truncate">{{ $msg->attachment_name ?: 'Pesan Suara (Voice Note)' }}</p>
                                                <span class="text-[9.5px] opacity-75">{{ $msg->formatted_size ?: 'Audio Rekaman' }}</span>
                                            </div>
                                        </div>
                                        <audio controls class="w-full h-8 rounded-lg outline-none" preload="metadata">
                                            <source src="{{ $msg->attachment_url }}" type="{{ $msg->attachment_mime ?: 'audio/webm' }}">
                                            Browser Anda tidak mendukung pemutar audio.
                                        </audio>
                                    </div>
                                @endif

                                <!-- Media: Document File -->
                                @if($msg->is_document && $msg->attachment_url)
                                    <a href="{{ $msg->attachment_url }}" target="_blank" download="{{ $msg->attachment_name }}" 
                                       class="mb-2 p-2.5 rounded-xl bg-black/10 dark:bg-white/10 flex items-center gap-2.5 hover:bg-black/15 dark:hover:bg-white/15 transition group">
                                        <div class="w-9 h-9 rounded-lg bg-red-500/20 text-red-500 flex items-center justify-center font-black text-sm shrink-0">
                                            📄
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold truncate group-hover:underline">{{ $msg->attachment_name }}</p>
                                            <span class="text-[10px] opacity-75">{{ $msg->formatted_size ?: 'Dokumen File' }}</span>
                                        </div>
                                        <svg class="w-4 h-4 opacity-75 group-hover:translate-y-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                @endif

                                <!-- Text Message -->
                                @if($msg->message)
                                    <div class="text-xs sm:text-[13px] leading-relaxed break-words">
                                        {!! $msg->rendered_message_html !!}
                                    </div>
                                @endif

                                <!-- Timestamp & Read Receipt -->
                                <div class="mt-1 flex items-center justify-end gap-1 text-[9.5px] {{ $isMine ? 'text-emerald-100 dark:text-emerald-200' : 'text-slate-400' }}">
                                    <span>{{ $msg->created_at->format('H:i') }} WIB</span>
                                    @if($isMine)
                                        @if($msg->is_read)
                                            <span class="text-sky-300 font-bold" title="Sudah dibaca">✓✓</span>
                                        @else
                                            <span class="opacity-80" title="Terkirim">✓</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Attachment Preview Tag (Sebelum Dikirim) -->
                <div id="attachment-preview-bar" class="hidden px-4 py-2 bg-slate-100 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <span id="preview-icon" class="text-lg">📎</span>
                        <div class="min-w-0">
                            <p id="preview-filename" class="text-xs font-bold text-slate-800 dark:text-white truncate max-w-xs sm:max-w-md">filename.ext</p>
                            <span id="preview-filesize" class="text-[10px] text-slate-500 dark:text-slate-400">0 KB</span>
                        </div>
                    </div>
                    <button type="button" onclick="clearAttachment()" class="p-1 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Template Balasan Cepat (Quick Responses) -->
                <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-900/90 border-t border-slate-200/60 dark:border-slate-800/60 flex items-center gap-1.5 overflow-x-auto scrollbar-none text-[11px]">
                    <span class="text-slate-400 font-bold text-[10px] shrink-0">⚡ Respon Cepat:</span>
                    @if($isCounselor)
                        <button type="button" onclick="insertQuickResponse('Halo ananda, terima kasih sudah menghubungi BK. Ada hal yang bisa Ibu/Bapak bantu?')" class="px-2.5 py-1 rounded-full bg-slate-200/80 dark:bg-slate-800 hover:bg-emerald-100 dark:hover:bg-emerald-950 text-slate-700 dark:text-slate-300 font-medium whitespace-nowrap transition cursor-pointer">Sapaan Hangat</button>
                        <button type="button" onclick="insertQuickResponse('Jangan khawatir ya, semua yang kamu ceritakan di sini dijamin kerahasiaannya.')" class="px-2.5 py-1 rounded-full bg-slate-200/80 dark:bg-slate-800 hover:bg-emerald-100 dark:hover:bg-emerald-950 text-slate-700 dark:text-slate-300 font-medium whitespace-nowrap transition cursor-pointer">Asas Kerahasiaan</button>
                        <button type="button" onclick="insertQuickResponse('Bisa kita jadwalkan pertemuan tatap muka di ruang BK besok saat jam istirahat?')" class="px-2.5 py-1 rounded-full bg-slate-200/80 dark:bg-slate-800 hover:bg-emerald-100 dark:hover:bg-emerald-950 text-slate-700 dark:text-slate-300 font-medium whitespace-nowrap transition cursor-pointer">Undang Tatap Muka</button>
                        <button type="button" onclick="insertQuickResponse('Tetap semangat ya, kamu sudah berusaha sebaik mungkin sejauh ini!')" class="px-2.5 py-1 rounded-full bg-slate-200/80 dark:bg-slate-800 hover:bg-emerald-100 dark:hover:bg-emerald-950 text-slate-700 dark:text-slate-300 font-medium whitespace-nowrap transition cursor-pointer">Motivasi</button>
                    @else
                        <button type="button" onclick="insertQuickResponse('Halo Bapak/Ibu Guru BK, saya ingin berkonsultasi mengenai ')" class="px-2.5 py-1 rounded-full bg-slate-200/80 dark:bg-slate-800 hover:bg-emerald-100 dark:hover:bg-emerald-950 text-slate-700 dark:text-slate-300 font-medium whitespace-nowrap transition cursor-pointer">Mulai Curhat</button>
                        <button type="button" onclick="insertQuickResponse('Saya merasa kesulitan membagi waktu belajar dan tugas sekolah...')" class="px-2.5 py-1 rounded-full bg-slate-200/80 dark:bg-slate-800 hover:bg-emerald-100 dark:hover:bg-emerald-950 text-slate-700 dark:text-slate-300 font-medium whitespace-nowrap transition cursor-pointer">Kesulitan Belajar</button>
                        <button type="button" onclick="insertQuickResponse('Bolehkah saya minta saran mengenai pilihan jurusan kuliah/karier setelah lulus?')" class="px-2.5 py-1 rounded-full bg-slate-200/80 dark:bg-slate-800 hover:bg-emerald-100 dark:hover:bg-emerald-950 text-slate-700 dark:text-slate-300 font-medium whitespace-nowrap transition cursor-pointer">Rencana Kuliah</button>
                    @endif
                </div>

                <!-- Input Box Bar (WhatsApp Web Style) -->
                <form id="chat-input-form" action="{{ route('bk.curhat.message', $activeConsultation) }}" method="POST" enctype="multipart/form-data" 
                      class="p-2 sm:p-3 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-end gap-1.5 sm:gap-2 shrink-0">
                    @csrf
                    
                    <!-- Tombol Lampiran (+) -->
                    <div class="relative shrink-0">
                        <button type="button" id="btn-toggle-attach" onclick="toggleAttachMenu()" 
                                class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950 hover:text-emerald-600 flex items-center justify-center transition cursor-pointer shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>

                        <!-- Menu Pilihan Lampiran Popup -->
                        <div id="attach-menu" class="hidden absolute bottom-12 left-0 w-48 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 p-2 space-y-1 z-30 animate-fade-in">
                            <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-emerald-50 dark:hover:bg-slate-700/60 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                                <span class="p-1.5 bg-blue-500/15 text-blue-500 rounded-lg">📷</span>
                                <span>Foto / Gambar</span>
                                <input type="file" name="attachment" id="input-attachment-image" accept="image/*" class="hidden" onchange="handleFileSelected(this, 'IMAGE')">
                            </label>
                            <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-emerald-50 dark:hover:bg-slate-700/60 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                                <span class="p-1.5 bg-amber-500/15 text-amber-500 rounded-lg">🎵</span>
                                <span>File Rekaman / Audio</span>
                                <input type="file" name="attachment" id="input-attachment-audio" accept="audio/*,.mp3,.wav,.m4a,.aac,.ogg,.webm" class="hidden" onchange="handleFileSelected(this, 'AUDIO')">
                            </label>
                            <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-emerald-50 dark:hover:bg-slate-700/60 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                                <span class="p-1.5 bg-rose-500/15 text-rose-500 rounded-lg">🎥</span>
                                <span>Video Singkat</span>
                                <input type="file" name="attachment" id="input-attachment-video" accept="video/*" class="hidden" onchange="handleFileSelected(this, 'VIDEO')">
                            </label>
                            <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-emerald-50 dark:hover:bg-slate-700/60 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                                <span class="p-1.5 bg-purple-500/15 text-purple-500 rounded-lg">📄</span>
                                <span>Dokumen / File</span>
                                <input type="file" name="attachment" id="input-attachment-doc" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip" class="hidden" onchange="handleFileSelected(this, 'DOCUMENT')">
                            </label>
                        </div>
                    </div>

                    <!-- Voice Recording Bar (Muncul saat merekam suara langsung) -->
                    <div id="voice-recording-bar" class="hidden flex-1 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 rounded-2xl px-3 py-2 flex items-center justify-between gap-2 animate-pulse-subtle">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                            <span class="text-xs font-black text-rose-700 dark:text-rose-300">Merekam Suara...</span>
                            <span id="voice-record-timer" class="px-2 py-0.5 rounded-lg bg-rose-200/80 dark:bg-rose-900/60 text-rose-900 dark:text-rose-200 font-mono text-xs font-black">00:00</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" onclick="cancelVoiceRecording()" class="px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-rose-200 hover:text-rose-800 transition">
                                ✕ Batal
                            </button>
                            <button type="button" onclick="stopAndSendVoiceRecording()" class="px-3 py-1 rounded-xl text-xs font-black bg-rose-600 hover:bg-rose-500 text-white shadow-md transition flex items-center gap-1 cursor-pointer">
                                <span>Kirim Suara</span>
                                <svg class="w-3.5 h-3.5 rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Input Pesan Textarea (Auto-expand) -->
                    <div id="text-input-container" class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-2xl p-2 focus-within:bg-white dark:focus-within:bg-slate-950 border border-transparent focus-within:border-emerald-500 transition shadow-inner">
                        <textarea id="message-input" name="message" rows="1" placeholder="Ketik curhatan atau pesan di sini... (Enter untuk kirim, Shift+Enter untuk baris baru)" 
                                  class="w-full bg-transparent text-slate-800 dark:text-white text-xs sm:text-sm outline-none resize-none max-h-32 scrollbar-thin"
                                  onkeydown="handleTextareaKeydown(event)"></textarea>
                    </div>

                    <!-- Tombol Rekam Suara Langsung (Mic Voice Note) -->
                    <button type="button" id="btn-mic-record" onclick="toggleVoiceRecording()" title="Rekam Suara (Voice Note)"
                            class="w-10 h-10 rounded-2xl bg-amber-500/15 hover:bg-amber-500/25 text-amber-600 dark:text-amber-400 border border-amber-500/30 flex items-center justify-center transition shadow-xs shrink-0 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                    </button>

                    <!-- Tombol Kirim Pesan (WhatsApp Style) -->
                    <button type="submit" id="btn-send-message" 
                            class="w-10 h-10 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white flex items-center justify-center transition shadow-lg shadow-emerald-600/30 shrink-0 cursor-pointer">
                        <svg class="w-5 h-5 rotate-45 -mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </form>
            @else
                <!-- Empty State (Belum Pilih Percakapan) -->
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-50 dark:bg-slate-950">
                    <div class="w-20 h-20 rounded-3xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-4xl mb-4 shadow-inner">
                        💬
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-800 dark:text-white">Ruang Curhat Siswa &amp; Konseling Online BK</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mt-1 leading-relaxed">
                        Pilih percakapan dari daftar di sebelah kiri untuk melihat pesan atau klik tombol di bawah untuk memulai sesi curhat baru.
                    </p>
                    <button type="button" onclick="openNewChatModal()" class="mt-4 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-lg shadow-emerald-600/30 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Mulai Topik Curhat Baru</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ================= MODAL MULAI CURHAT BARU ================= -->
<div id="modal-new-chat" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl relative animate-scale-up overflow-hidden my-auto">
        <!-- Header Modal (Fixed) -->
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between shrink-0 bg-slate-50/60 dark:bg-slate-900/60">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-500 flex items-center justify-center text-xl shrink-0">
                    💌
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white truncate">Buka Sesi Curhat &amp; Konseling Baru</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Pilih konselor dan topik yang ingin kamu ceritakan atau diskusikan.</p>
                </div>
            </div>
            <button type="button" onclick="closeNewChatModal()" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('bk.curhat.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
            @csrf

            <!-- Scrollable Form Body -->
            <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1 scrollbar-thin">
                @if($isCounselor)
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Peserta Didik <span class="text-rose-500">*</span></label>
                        <select name="student_id" required class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                            <option value="">-- Cari &amp; Pilih Siswa --</option>
                            @foreach(\App\Models\Student::with('schoolClass')->where('is_active', true)->orderBy('name')->get() as $std)
                                <option value="{{ $std->id }}">{{ $std->name }} ({{ $std->schoolClass?->name ?? 'Tanpa Rombel' }} - {{ $std->nisn }})</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <!-- Pilihan Konselor / Guru BK Yang Dituju -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Pilih Guru BK / Konselor yang Dituju <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10.5px] text-emerald-600 dark:text-emerald-400 font-semibold">Master Data Guru &amp; PTK</span>
                        </div>
                        
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            <!-- Pilihan 1: Semua Tim BK -->
                            <label class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between cursor-pointer hover:border-emerald-500 transition has-checked:border-emerald-500 has-checked:bg-emerald-50/70 dark:has-checked:bg-emerald-950/40">
                                <div class="flex items-center gap-3 min-w-0">
                                    <input type="radio" name="assigned_counselor_id" value="" checked class="rounded-full text-emerald-600 focus:ring-emerald-500 shrink-0">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="text-xs font-black text-slate-800 dark:text-white">🌟 Semua Tim BK</p>
                                            <span class="px-1.5 py-0.2 rounded text-[8.5px] font-black bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">Fleksibel</span>
                                        </div>
                                        <p class="text-[10.5px] text-slate-500 dark:text-slate-400 leading-snug mt-0.5">Siapa saja dari tim BK yang sedang aktif dapat langsung merespons dan mendampingi curhatmu.</p>
                                    </div>
                                </div>
                            </label>

                            <!-- Pilihan Konselor dari Master Data Guru & PTK -->
                            @foreach($bkCounselors as $counselor)
                                <label class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between cursor-pointer hover:border-emerald-500 transition has-checked:border-emerald-500 has-checked:bg-emerald-50/70 dark:has-checked:bg-emerald-950/40">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <input type="radio" name="assigned_counselor_id" value="{{ $counselor['user_id'] }}" class="rounded-full text-emerald-600 focus:ring-emerald-500 shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                            {{ substr($counselor['name'], 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <p class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $counselor['name'] }}</p>
                                                @if($counselor['is_coordinator'])
                                                    <span class="px-1.5 py-0.2 rounded text-[8.5px] font-black bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30 shrink-0">Koordinator BK</span>
                                                @else
                                                    <span class="px-1.5 py-0.2 rounded text-[8.5px] font-bold bg-teal-500/20 text-teal-700 dark:text-teal-300 border border-teal-500/30 shrink-0">Guru BK</span>
                                                @endif
                                            </div>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">NIP: {{ $counselor['nip'] ?: '-' }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Kategori / Bidang Bimbingan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori Bimbingan <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5">
                        <label class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 flex flex-col items-center gap-1 cursor-pointer hover:border-emerald-500 transition text-center has-checked:border-emerald-500 has-checked:bg-emerald-50 dark:has-checked:bg-emerald-950/40">
                            <input type="radio" name="category" value="PRIBADI" checked class="hidden">
                            <span class="text-base">🧠</span>
                            <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">Pribadi</span>
                        </label>
                        <label class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 flex flex-col items-center gap-1 cursor-pointer hover:border-emerald-500 transition text-center has-checked:border-emerald-500 has-checked:bg-emerald-50 dark:has-checked:bg-emerald-950/40">
                            <input type="radio" name="category" value="BELAJAR" class="hidden">
                            <span class="text-base">📚</span>
                            <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">Belajar</span>
                        </label>
                        <label class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 flex flex-col items-center gap-1 cursor-pointer hover:border-emerald-500 transition text-center has-checked:border-emerald-500 has-checked:bg-emerald-50 dark:has-checked:bg-emerald-950/40">
                            <input type="radio" name="category" value="SOSIAL" class="hidden">
                            <span class="text-base">👥</span>
                            <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">Sosial</span>
                        </label>
                        <label class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 flex flex-col items-center gap-1 cursor-pointer hover:border-emerald-500 transition text-center has-checked:border-emerald-500 has-checked:bg-emerald-50 dark:has-checked:bg-emerald-950/40">
                            <input type="radio" name="category" value="KARIER" class="hidden">
                            <span class="text-base">🎓</span>
                            <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">Karier</span>
                        </label>
                    </div>
                </div>

                <!-- Topik Singkat -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul / Pokok Topik Curhat <span class="text-rose-500">*</span></label>
                    <input type="text" name="topic" required placeholder="Contoh: Bingung memilih jurusan kuliah, masalah pertemanan..." 
                           class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                </div>

                <!-- Pesan Pembuka -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pesan Awal / Cerita <span class="text-rose-500">*</span></label>
                    <textarea name="message" rows="3" required placeholder="Tuliskan apa yang sedang kamu rasakan atau alami..." 
                              class="w-full px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white outline-none focus:border-emerald-500 resize-none"></textarea>
                </div>

                <!-- Opsi Lampiran -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Lampirkan Foto/Dokumen/Audio (Opsional)</label>
                    <input type="file" name="attachment" accept="image/*,video/*,audio/*,.mp3,.wav,.m4a,.aac,.ogg,.webm,.pdf,.doc,.docx" 
                           class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-slate-800 dark:file:text-emerald-400">
                </div>
            </div>

            <!-- Footer Modal (Fixed & Visible) -->
            <div class="p-3.5 sm:p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeNewChatModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/30 transition flex items-center gap-1.5 cursor-pointer">
                    <span>Mulai Curhat Sekarang &rarr;</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= LIGHTBOX MODAL UNTUK PREVIEW GAMBAR ================= -->
<div id="lightbox-modal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 cursor-pointer" onclick="closeLightbox()">
    <div class="max-w-4xl max-h-[90vh] relative" onclick="event.stopPropagation()">
        <button type="button" onclick="closeLightbox()" class="absolute -top-10 right-0 text-white text-2xl font-bold hover:text-rose-400 transition">&times;</button>
        <img id="lightbox-img" src="" alt="Preview Gambar" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl object-contain">
    </div>
</div>

<!-- ================= JAVASCRIPT LOGIC REALTIME CHAT WHATSAPP STYLE ================= -->
<script>
    let activeConsultationId = {{ $activeConsultation ? $activeConsultation->id : 'null' }};
    let lastMessageId = 0;
    let pollInterval = null;
    let currentUserId = {{ Auth::id() }};

    document.addEventListener('DOMContentLoaded', () => {
        // Tentukan message ID terakhir yang sudah ada di layar
        const rows = document.querySelectorAll('.message-row');
        if (rows.length > 0) {
            const lastRow = rows[rows.length - 1];
            lastMessageId = parseInt(lastRow.getAttribute('data-message-id')) || 0;
        }

        scrollToBottom(false);

        // Setup filter search conversation
        const searchInput = document.getElementById('conversation-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                const items = document.querySelectorAll('.conversation-item');
                items.forEach(item => {
                    const target = item.getAttribute('data-search-target') || '';
                    if (!query || target.includes(query)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Mulai live polling jika ada konsultasi aktif
        if (activeConsultationId) {
            startPolling();
        }

        // Setup AJAX Submit untuk Form Chat (Instant Delivery)
        const chatForm = document.getElementById('chat-input-form');
        if (chatForm) {
            chatForm.addEventListener('submit', handleFormSubmit);
        }
    });

    // Auto scroll ke bawah
    function scrollToBottom(smooth = true) {
        const container = document.getElementById('messages-container');
        if (container) {
            if (smooth) {
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            } else {
                container.scrollTop = container.scrollHeight;
            }
        }
    }

    // Toggle Menu Lampiran
    function toggleAttachMenu() {
        const menu = document.getElementById('attach-menu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    // Handle File Dipilih
    function handleFileSelected(input, type) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        
        const previewBar = document.getElementById('attachment-preview-bar');
        const filenameElem = document.getElementById('preview-filename');
        const filesizeElem = document.getElementById('preview-filesize');
        const iconElem = document.getElementById('preview-icon');

        if (filenameElem) filenameElem.textContent = file.name;
        if (filesizeElem) filesizeElem.textContent = formatBytes(file.size);
        if (iconElem) {
            if (type === 'IMAGE') iconElem.textContent = '🖼️';
            else if (type === 'VIDEO') iconElem.textContent = '🎥';
            else if (type === 'AUDIO') iconElem.textContent = '🎵';
            else iconElem.textContent = '📄';
        }

        if (previewBar) previewBar.classList.remove('hidden');
        toggleAttachMenu();
    }

    // Bersihkan Lampiran
    function clearAttachment() {
        const inputs = [
            document.getElementById('input-attachment-image'),
            document.getElementById('input-attachment-audio'),
            document.getElementById('input-attachment-video'),
            document.getElementById('input-attachment-doc'),
        ];
        inputs.forEach(input => {
            if (input) input.value = '';
        });

        const previewBar = document.getElementById('attachment-preview-bar');
        if (previewBar) previewBar.classList.add('hidden');
    }

    function formatBytes(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return bytes + ' B';
    }

    // Sisipkan Balasan Cepat
    function insertQuickResponse(text) {
        const textarea = document.getElementById('message-input');
        if (textarea) {
            textarea.value = text;
            textarea.focus();
        }
    }

    // Handle Enter untuk Kirim (Shift+Enter untuk baris baru)
    function handleTextareaKeydown(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            const form = document.getElementById('chat-input-form');
            if (form) {
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }
    }

    // Submit AJAX Form Chat
    async function handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const textarea = document.getElementById('message-input');
        const text = textarea ? textarea.value.trim() : '';
        const previewBar = document.getElementById('attachment-preview-bar');
        const hasAttachment = previewBar && !previewBar.classList.contains('hidden');

        if (!text && !hasAttachment) {
            return;
        }

        const formData = new FormData(form);
        const submitBtn = document.getElementById('btn-send-message');
        if (submitBtn) submitBtn.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const result = await response.json();
            if (result.success) {
                // Reset input
                if (textarea) textarea.value = '';
                clearAttachment();

                // Append pesan yang baru dikirim ke container
                appendMessage(result.data, true);
                lastMessageId = Math.max(lastMessageId, result.data.id);
                scrollToBottom(true);
            } else {
                alert(result.message || 'Gagal mengirim pesan.');
            }
        } catch (error) {
            console.error('Send message error:', error);
            // Fallback submit form biasa
            form.submit();
        } finally {
            if (submitBtn) submitBtn.disabled = false;
            if (textarea) textarea.focus();
        }
    }

    // Polling Realtime
    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(async () => {
            if (!activeConsultationId) return;

            try {
                const url = `{{ url('bk/curhat') }}/${activeConsultationId}/messages?after_id=${lastMessageId}`;
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.messages && data.messages.length > 0) {
                        let hasIncoming = false;
                        data.messages.forEach(msg => {
                            if (msg.id > lastMessageId) {
                                appendMessage(msg, msg.is_mine);
                                lastMessageId = Math.max(lastMessageId, msg.id);
                                if (!msg.is_mine) hasIncoming = true;
                            }
                        });

                        scrollToBottom(true);

                        if (hasIncoming) {
                            playChime();
                        }
                    }
                }
            } catch (err) {
                console.warn('Poll error:', err);
            }
        }, 3500);
    }

    // Tambah Bubble Pesan ke DOM
    function appendMessage(msg, isMine) {
        const container = document.getElementById('messages-container');
        if (!container) return;

        // Cek apakah pesan sudah tertera
        if (document.querySelector(`[data-message-id="${msg.id}"]`)) return;

        const row = document.createElement('div');
        row.className = `flex ${isMine ? 'justify-end' : 'justify-start'} message-row animate-fade-in`;
        row.setAttribute('data-message-id', msg.id);

        let mediaHtml = '';
        if (msg.is_image && msg.attachment_url) {
            mediaHtml += `
                <div class="mb-2 rounded-xl overflow-hidden cursor-pointer" onclick="openLightbox('${msg.attachment_url}')">
                    <img src="${msg.attachment_url}" alt="${msg.attachment_name || 'Gambar'}" class="max-h-72 w-auto object-cover rounded-xl hover:opacity-95 transition">
                </div>
            `;
        } else if (msg.is_video && msg.attachment_url) {
            mediaHtml += `
                <div class="mb-2 rounded-xl overflow-hidden bg-black max-w-sm">
                    <video controls class="w-full max-h-72 rounded-xl">
                        <source src="${msg.attachment_url}">
                    </video>
                </div>
            `;
        } else if (msg.is_audio && msg.attachment_url) {
            mediaHtml += `
                <div class="mb-2 p-2.5 rounded-2xl bg-black/10 dark:bg-white/10 flex flex-col gap-1.5 min-w-[220px] sm:min-w-[280px]">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm shrink-0">🎙️</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold truncate">${msg.attachment_name || 'Pesan Suara (Voice Note)'}</p>
                            <span class="text-[9.5px] opacity-75">${msg.formatted_size || 'Audio Rekaman'}</span>
                        </div>
                    </div>
                    <audio controls class="w-full h-8 rounded-lg outline-none" preload="metadata">
                        <source src="${msg.attachment_url}" type="${msg.attachment_mime || 'audio/webm'}">
                        Browser Anda tidak mendukung pemutar audio.
                    </audio>
                </div>
            `;
        } else if (msg.is_document && msg.attachment_url) {
            mediaHtml += `
                <a href="${msg.attachment_url}" target="_blank" download="${msg.attachment_name || 'file'}" 
                   class="mb-2 p-2.5 rounded-xl bg-black/10 dark:bg-white/10 flex items-center gap-2.5 hover:bg-black/15 dark:hover:bg-white/15 transition group">
                    <div class="w-9 h-9 rounded-lg bg-red-500/20 text-red-500 flex items-center justify-center font-black text-sm shrink-0">📄</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold truncate group-hover:underline">${msg.attachment_name || 'Dokumen'}</p>
                        <span class="text-[10px] opacity-75">${msg.formatted_size || 'File'}</span>
                    </div>
                    <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </a>
            `;
        }

        let senderHeaderHtml = '';
        if (!isMine) {
            senderHeaderHtml = `
                <div class="flex items-center gap-1.5 mb-1 pb-1 border-b border-black/10 dark:border-white/10">
                    <span class="text-[11px] font-black ${msg.sender_role === 'COUNSELOR' ? 'text-teal-600 dark:text-teal-300' : 'text-emerald-700 dark:text-emerald-400'}">
                        ${msg.sender_name || 'Pengirim'}
                    </span>
                    ${msg.sender_role === 'COUNSELOR' ? '<span class="px-1 py-0.2 rounded text-[8px] font-extrabold bg-teal-500/20 text-teal-600 dark:text-teal-300">GURU BK</span>' : ''}
                </div>
            `;
        }

        const bubbleClasses = isMine 
            ? 'bg-emerald-600 dark:bg-[#005c4b] text-white rounded-tr-xs' 
            : 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-tl-xs border border-slate-200/60 dark:border-slate-700/60';

        const checkMarkHtml = isMine ? '<span class="opacity-80">✓</span>' : '';

        row.innerHTML = `
            <div class="max-w-[85%] sm:max-w-[70%] rounded-2xl p-3 shadow-md relative ${bubbleClasses}">
                ${senderHeaderHtml}
                ${mediaHtml}
                ${msg.rendered_html ? `<div class="text-xs sm:text-[13px] leading-relaxed break-words">${msg.rendered_html}</div>` : ''}
                <div class="mt-1 flex items-center justify-end gap-1 text-[9.5px] ${isMine ? 'text-emerald-100 dark:text-emerald-200' : 'text-slate-400'}">
                    <span>${msg.created_at || 'Baru saja'}</span>
                    ${checkMarkHtml}
                </div>
            </div>
        `;

        container.appendChild(row);
    }

    // Suara Bell Chime Notifikasi Halus Menggunakan Web Audio API
    function playChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const now = ctx.currentTime;
            
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, now); // D5
            osc.frequency.exponentialRampToValueAtTime(880, now + 0.12); // A5
            
            gain.gain.setValueAtTime(0.08, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            osc.start(now);
            osc.stop(now + 0.36);
        } catch (e) {
            // Audio context mungkin butuh interaksi user
        }
    }

    // ================= FITUR KIRIM SUARA (VOICE NOTE RECORDING) =================
    let mediaRecorder = null;
    let audioChunks = [];
    let recordTimerInterval = null;
    let recordStartTime = null;

    async function toggleVoiceRecording() {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            stopAndSendVoiceRecording();
            return;
        }
        startVoiceRecording();
    }

    async function startVoiceRecording() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Browser Anda tidak mendukung perekaman mikrofon audio.');
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            
            // Pilih mime type audio yang didukung browser
            let options = {};
            if (window.MediaRecorder) {
                if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) {
                    options = { mimeType: 'audio/webm;codecs=opus' };
                } else if (MediaRecorder.isTypeSupported('audio/ogg;codecs=opus')) {
                    options = { mimeType: 'audio/ogg;codecs=opus' };
                } else if (MediaRecorder.isTypeSupported('audio/mp4')) {
                    options = { mimeType: 'audio/mp4' };
                }
            }

            mediaRecorder = new MediaRecorder(stream, options);
            audioChunks = [];

            mediaRecorder.ondataavailable = (event) => {
                if (event.data && event.data.size > 0) {
                    audioChunks.push(event.data);
                }
            };

            mediaRecorder.onstop = () => {
                try {
                    stream.getTracks().forEach(track => track.stop());
                } catch (e) {}
            };

            mediaRecorder.start(250); // potong per 250ms
            recordStartTime = Date.now();

            // Ubah UI: sembunyikan text input dan mic button, tampilkan recording bar
            const recordingBar = document.getElementById('voice-recording-bar');
            const textContainer = document.getElementById('text-input-container');
            const micBtn = document.getElementById('btn-mic-record');
            const sendBtn = document.getElementById('btn-send-message');

            if (recordingBar) recordingBar.classList.remove('hidden');
            if (textContainer) textContainer.classList.add('hidden');
            if (micBtn) micBtn.classList.add('hidden');
            if (sendBtn) sendBtn.classList.add('hidden');

            // Jalankan timer
            updateRecordTimer();
            if (recordTimerInterval) clearInterval(recordTimerInterval);
            recordTimerInterval = setInterval(updateRecordTimer, 1000);

        } catch (err) {
            console.error('Microphone access error:', err);
            alert('Izin akses mikrofon ditolak atau mikrofon tidak tersedia. Mohon izinkan mikrofon di browser untuk merekam suara.');
        }
    }

    function updateRecordTimer() {
        if (!recordStartTime) return;
        const diffSec = Math.floor((Date.now() - recordStartTime) / 1000);
        const mins = String(Math.floor(diffSec / 60)).padStart(2, '0');
        const secs = String(diffSec % 60).padStart(2, '0');
        const timerElem = document.getElementById('voice-record-timer');
        if (timerElem) {
            timerElem.textContent = `${mins}:${secs}`;
        }
    }

    function resetVoiceRecordingUI() {
        if (recordTimerInterval) clearInterval(recordTimerInterval);
        recordTimerInterval = null;
        recordStartTime = null;

        const recordingBar = document.getElementById('voice-recording-bar');
        const textContainer = document.getElementById('text-input-container');
        const micBtn = document.getElementById('btn-mic-record');
        const sendBtn = document.getElementById('btn-send-message');
        const timerElem = document.getElementById('voice-record-timer');

        if (timerElem) timerElem.textContent = '00:00';
        if (recordingBar) recordingBar.classList.add('hidden');
        if (textContainer) textContainer.classList.remove('hidden');
        if (micBtn) micBtn.classList.remove('hidden');
        if (sendBtn) sendBtn.classList.remove('hidden');
    }

    function cancelVoiceRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            try { mediaRecorder.stop(); } catch (e) {}
        }
        audioChunks = [];
        resetVoiceRecordingUI();
    }

    function stopAndSendVoiceRecording() {
        if (!mediaRecorder || mediaRecorder.state === 'inactive') {
            resetVoiceRecordingUI();
            return;
        }

        const mime = mediaRecorder.mimeType || 'audio/webm';

        mediaRecorder.onstop = async () => {
            if (mediaRecorder.stream) {
                try { mediaRecorder.stream.getTracks().forEach(track => track.stop()); } catch (e) {}
            }

            if (audioChunks.length === 0) {
                resetVoiceRecordingUI();
                return;
            }

            const audioBlob = new Blob(audioChunks, { type: mime });
            audioChunks = [];
            resetVoiceRecordingUI();

            if (audioBlob.size < 200) {
                alert('Durasi rekaman suara terlalu pendek.');
                return;
            }

            // Tentukan ekstensi file
            let ext = 'webm';
            if (mime.includes('ogg')) ext = 'ogg';
            else if (mime.includes('mp4')) ext = 'mp4';

            const audioFile = new File([audioBlob], `voicenote_${Date.now()}.${ext}`, { type: mime });

            // Kirim langsung via AJAX
            await sendAudioMessage(audioFile);
        };

        try {
            mediaRecorder.stop();
        } catch (e) {
            resetVoiceRecordingUI();
        }
    }

    async function sendAudioMessage(file) {
        const form = document.getElementById('chat-input-form');
        if (!form) return;

        const formData = new FormData();
        const tokenInput = form.querySelector('input[name="_token"]');
        if (tokenInput) formData.append('_token', tokenInput.value);
        formData.append('attachment', file);
        formData.append('message', '🎙️ Pesan Suara');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const result = await response.json();
            if (result.success) {
                appendMessage(result.data, true);
                lastMessageId = Math.max(lastMessageId, result.data.id);
                scrollToBottom(true);
            } else {
                alert(result.message || 'Gagal mengirim rekaman suara.');
            }
        } catch (error) {
            console.error('Send audio error:', error);
            alert('Terjadi kesalahan saat mengunggah rekaman suara.');
        }
    }

    // Modal Mulai Curhat Baru
    function openNewChatModal() {
        const modal = document.getElementById('modal-new-chat');
        if (modal) modal.classList.remove('hidden');
    }
    function closeNewChatModal() {
        const modal = document.getElementById('modal-new-chat');
        if (modal) modal.classList.add('hidden');
    }

    // Lightbox Gambar
    function openLightbox(url) {
        const modal = document.getElementById('lightbox-modal');
        const img = document.getElementById('lightbox-img');
        if (modal && img) {
            img.src = url;
            modal.classList.remove('hidden');
        }
    }
    function closeLightbox() {
        const modal = document.getElementById('lightbox-modal');
        if (modal) modal.classList.add('hidden');
    }

    // Navigasi Mobile (Beralih ke Sidebar / List)
    function showSidebarOnMobile() {
        const sidebar = document.getElementById('chat-sidebar');
        const chatWindow = document.getElementById('chat-window');
        if (sidebar && chatWindow) {
            sidebar.classList.remove('hidden');
            chatWindow.classList.add('hidden');
            chatWindow.classList.remove('flex');
        }
    }
</script>

<style>
    @keyframes scaleUp {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .animate-scale-up {
        animation: scaleUp 0.18s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes pulseSubtle {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }
    .animate-pulse-subtle {
        animation: pulseSubtle 1.8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endsection
