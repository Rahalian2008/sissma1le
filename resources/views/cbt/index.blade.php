@extends('layouts.app')

@section('title', 'Ujian Online (CBT) - SIS-SMA1LE')

@section('content')
<div class="space-y-6">

    <!-- HERO BANNER & STATS -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-900 via-slate-900 to-indigo-950 p-6 sm:p-8 text-white border border-indigo-500/20 shadow-xl">
        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-72 h-72 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-400/30">
                    <span>⚡ Computer-Based Test (CBT)</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Standar Kemdikbudristek</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Aplikasi Ujian Online & Bank Soal Asesmen
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    Sistem ujian mandiri berbasis CBT terintegrasi dengan 6 bentuk soal (Pilihan Ganda, PG Kompleks, B-S, Menjodohkan, Isian & Esai), generator AI, anti-cheat auto-block (anti buka tab/minimize), proteksi GPS geofence radius sekolah, serta otomasi dokumen Kisi-Kisi, Kartu Soal, Kartu Peserta, Daftar Hadir, dan Berita Acara Ujian.
                </p>
            </div>

            <!-- Tombol Aksi Utama -->
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <a href="{{ route('cbt.exams.create') }}" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-sm shadow-lg shadow-amber-500/20 transition flex items-center gap-2 cursor-pointer">
                    <span class="text-lg">➕</span>
                    <span>Buat Jadwal Ujian Baru</span>
                </a>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-6 pt-6 border-t border-white/10">
            <div class="p-3 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                <div class="text-[11px] text-slate-400 font-bold uppercase">Total Ujian</div>
                <div class="text-xl sm:text-2xl font-black text-white mt-0.5">{{ number_format($stats['total_exams']) }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                <div class="text-[11px] text-emerald-400 font-bold uppercase">Ujian Aktif</div>
                <div class="text-xl sm:text-2xl font-black text-emerald-300 mt-0.5">{{ number_format($stats['active_exams']) }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                <div class="text-[11px] text-amber-400 font-bold uppercase">Butir Soal</div>
                <div class="text-xl sm:text-2xl font-black text-amber-300 mt-0.5">{{ number_format($stats['total_questions']) }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                <div class="text-[11px] text-sky-400 font-bold uppercase">Peserta Mengerjakan</div>
                <div class="text-xl sm:text-2xl font-black text-sky-300 mt-0.5">{{ number_format($stats['total_participants']) }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                <div class="text-[11px] text-rose-400 font-bold uppercase">Terblokir Curang</div>
                <div class="text-xl sm:text-2xl font-black text-rose-400 mt-0.5">{{ number_format($stats['blocked_participants']) }}</div>
            </div>
        </div>
    </div>

    <!-- FILTER & PENCARIAN -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('cbt.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex-1 relative">
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}" 
                       placeholder="Cari judul ujian, kode ujian, atau token..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <select name="type" class="px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none">
                    <option value="">Semua Tipe Ujian</option>
                    <option value="UH" {{ request('type') === 'UH' ? 'selected' : '' }}>Ulangan Harian (UH)</option>
                    <option value="PTS" {{ request('type') === 'PTS' ? 'selected' : '' }}>Penilaian Tengah Semester (PTS)</option>
                    <option value="PAS" {{ request('type') === 'PAS' ? 'selected' : '' }}>Penilaian Akhir Semester (PAS)</option>
                    <option value="PAT" {{ request('type') === 'PAT' ? 'selected' : '' }}>Penilaian Akhir Tahun (PAT)</option>
                    <option value="US" {{ request('type') === 'US' ? 'selected' : '' }}>Ujian Sekolah (US)</option>
                    <option value="ANBK" {{ request('type') === 'ANBK' ? 'selected' : '' }}>Simulasi ANBK / Asesmen Nasional</option>
                    <option value="DIAGNOSTIK" {{ request('type') === 'DIAGNOSTIK' ? 'selected' : '' }}>Asesmen Diagnostik</option>
                </select>

                <select name="status" class="px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Diterbitkan (Aktif)</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                </select>

                <button type="submit" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition cursor-pointer">
                    Filter
                </button>

                @if(request()->hasAny(['q', 'type', 'status']))
                    <a href="{{ route('cbt.index') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- DAFTAR PAKET UJIAN -->
    @if($exams->isEmpty())
        <div class="p-12 text-center rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 space-y-3">
            <div class="text-5xl">📝</div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">Belum Ada Paket Ujian</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                Silakan buat paket ujian baru untuk memulai asesmen berbasis komputer bagi siswa Anda.
            </p>
            <div class="pt-2">
                <a href="{{ route('cbt.exams.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-500 transition">
                    ➕ Buat Jadwal Ujian Sekarang
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($exams as $exam)
                <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-indigo-500/40 shadow-xs hover:shadow-md transition flex flex-col justify-between overflow-hidden group">
                    
                    <div class="p-5 sm:p-6 space-y-4">
                        <!-- Header Card: Tipe, Status, Token -->
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20">
                                {{ $exam->exam_type }}
                            </span>

                            <div class="flex items-center gap-1.5">
                                @if(!empty($exam->token))
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-black bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30 tracking-wider" title="Token Masuk Ujian">
                                        🔑 {{ $exam->token }}
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $exam->status === 'published' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $exam->status === 'published' ? 'Aktif' : ucfirst($exam->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Judul & Mapel -->
                        <div>
                            <div class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $exam->subject?->name ?? 'Mata Pelajaran Umum' }}
                            </div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white mt-0.5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition leading-snug">
                                <a href="{{ route('cbt.exams.show', $exam->id) }}">
                                    {{ $exam->title }}
                                </a>
                            </h2>
                            <div class="text-[11px] font-mono text-slate-400 mt-1">
                                Kode: {{ $exam->exam_code }}
                            </div>
                        </div>

                        <!-- Kelas Target -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[11px] font-bold text-slate-400">Rombel:</span>
                            @forelse($exam->classes as $c)
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                    {{ $c->name }}
                                </span>
                            @empty
                                <span class="text-[10px] text-rose-500 italic">Belum ada kelas ditugaskan</span>
                            @endforelse
                        </div>

                        <!-- Fitur Keamanan & Geofence Badge -->
                        <div class="flex items-center gap-2 flex-wrap pt-2 border-t border-slate-100 dark:border-slate-800 text-[10px]">
                            @if($exam->enable_security_block)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold border border-rose-500/20" title="Anti Buka Tab Baru / Minimize Browser">
                                    🛡️ Anti-Cheat (Max {{ $exam->max_violations }}x)
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-400">
                                    🛡️ Anti-Cheat Nonaktif
                                </span>
                            @endif

                            @if($exam->enable_geofence)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-teal-500/10 text-teal-700 dark:text-teal-300 font-bold border border-teal-500/20" title="Radius {{ $exam->geofence_radius }}m dari Sekolah">
                                    📍 Lock Lokasi ({{ $exam->geofence_radius }}m)
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-400">
                                    📍 Lokasi Bebas
                                </span>
                            @endif
                        </div>

                        <!-- Info Durasi & Jumlah Soal -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold">Durasi Ujian:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">⏱️ {{ $exam->duration_minutes }} Menit</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold">Total Soal:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">📚 {{ $exam->questions_count }} Butir Soal</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Bar -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-850 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('cbt.exams.show', $exam->id) }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1 cursor-pointer">
                                <span>✏️ Kelola Soal</span>
                            </a>
                            <a href="{{ route('cbt.exams.monitor', $exam->id) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1" title="Live Monitor Pengawas">
                                <span>📡 Monitor</span>
                            </a>
                            <a href="{{ route('cbt.exams.results', $exam->id) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1" title="Rekap Nilai Siswa">
                                <span>📊 Nilai</span>
                            </a>
                        </div>

                        <!-- Dropdown Dokumen & Menu Lengkap -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @click.outside="open = false" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition cursor-pointer">
                                <span>⚙️ ▾</span>
                            </button>

                            <div x-show="open" x-transition class="absolute right-0 bottom-full mb-2 w-56 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-2 z-50 text-xs">
                                <div class="px-3 py-1 text-[10px] font-black uppercase text-slate-400">Dokumen Kurikulum</div>
                                <a href="{{ route('cbt.documents.kisi_kisi', $exam->id) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/40">
                                    <span>📑</span>
                                    <span>Cetak Kisi-Kisi Soal</span>
                                </a>
                                <a href="{{ route('cbt.documents.kartu_soal', $exam->id) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/40">
                                    <span>🎴</span>
                                    <span>Cetak Kartu Soal</span>
                                </a>

                                <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                                <div class="px-3 py-1 text-[10px] font-black uppercase text-slate-400">Dokumen Administrasi Ujian</div>
                                <a href="{{ route('cbt.documents.kartu_peserta', $exam->id) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/40">
                                    <span>🪪</span>
                                    <span>Cetak Kartu Peserta (Akun & PIN)</span>
                                </a>
                                <a href="{{ route('cbt.documents.daftar_hadir', $exam->id) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/40">
                                    <span>📋</span>
                                    <span>Cetak Daftar Hadir</span>
                                </a>
                                <a href="{{ route('cbt.documents.berita_acara', $exam->id) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/40">
                                    <span>📜</span>
                                    <span>Cetak Berita Acara (BAP)</span>
                                </a>

                                <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                                <a href="{{ route('cbt.exams.edit', $exam->id) }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                                    <span>🛠️</span>
                                    <span>Edit Konfigurasi</span>
                                </a>
                                <form action="{{ route('cbt.exams.destroy', $exam->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket ujian ini? Seluruh soal dan jawaban peserta akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 cursor-pointer">
                                        <span>🗑️</span>
                                        <span>Hapus Paket Ujian</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $exams->links() }}
        </div>
    @endif
</div>
@endsection
