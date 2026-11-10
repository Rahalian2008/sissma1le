@php
    $lms = $lmsLibraryData['lms'] ?? [];
    $lib = $lmsLibraryData['library'] ?? [];
    $navUser = Auth::user();
    $isStudent = $navUser && ($navUser->role === 'siswa' || in_array('siswa', $navUser->all_roles));
    $isTeacher = $navUser && ($navUser->role === 'guru' || in_array('guru', $navUser->all_roles));
    $canManageLib = $navUser && $navUser->canManageLibrary();
    $canManageKurikulum = $navUser && $navUser->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum']);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 mt-6">
    <!-- ==========================================
         CARD 1: LMS PEMBELAJARAN
         ========================================== -->
    <div class="rounded-3xl bg-white dark:bg-slate-900 border border-cyan-500/20 dark:border-cyan-500/30 p-5 sm:p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between relative overflow-hidden group">
        <!-- Top Glow Accent -->
        <div class="absolute -right-12 -top-12 w-44 h-44 bg-cyan-500/10 dark:bg-cyan-500/15 rounded-full blur-2xl pointer-events-none group-hover:bg-cyan-500/20 transition-colors"></div>

        <div>
            <!-- Card Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-cyan-600 to-blue-600 flex items-center justify-center text-white shadow-md shadow-cyan-500/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white">LMS Pembelajaran</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-cyan-100 dark:bg-cyan-950/80 text-cyan-700 dark:text-cyan-300 border border-cyan-300 dark:border-cyan-800">E-Learning</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Modul digital, penugasan siswa, ujian CBT & presensi mapel</p>
                    </div>
                </div>

                <a href="{{ route('lms.index') }}" class="text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 flex items-center gap-1 shrink-0 transition" title="Buka LMS">
                    <span>Masuk</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- Stats Grid Mini -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 my-4">
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 text-center">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas Mapel</div>
                    <div class="text-base sm:text-lg font-black text-slate-900 dark:text-white mt-0.5">{{ $lms['total_courses'] ?? 0 }}</div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 text-center">
                    <div class="text-[10px] font-bold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider">Materi & Video</div>
                    <div class="text-base sm:text-lg font-black text-cyan-600 dark:text-cyan-400 mt-0.5">{{ $lms['total_materials'] ?? 0 }}</div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 text-center">
                    <div class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">Tugas Daring</div>
                    <div class="text-base sm:text-lg font-black text-amber-500 mt-0.5">{{ $lms['total_assignments'] ?? 0 }}</div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 text-center">
                    <div class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">Kuis CBT</div>
                    <div class="text-base sm:text-lg font-black text-indigo-500 mt-0.5">{{ $lms['total_quizzes'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Dynamic Role-Specific Section -->
            @if($isStudent && !empty($lms['user_pending_assignments']) && $lms['user_pending_assignments']->isNotEmpty())
                <!-- Siswa: Tugas Mendekati Deadline -->
                <div class="mt-2 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Tugas yang Belum Dikumpulkan:
                        </span>
                        <span class="text-[11px] text-slate-400">{{ $lms['user_pending_assignments']->count() }} Tugas</span>
                    </div>
                    <div class="space-y-1.5">
                        @foreach($lms['user_pending_assignments'] as $assign)
                            <div class="p-2.5 rounded-xl bg-amber-500/5 border border-amber-500/20 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $assign->title }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                        {{ $assign->courseClass->subject->name ?? 'Mata Pelajaran' }} &bull; 
                                        Deadline: {{ $assign->due_date ? \Carbon\Carbon::parse($assign->due_date)->format('d M H:i') : 'Tanpa Batas' }}
                                    </div>
                                </div>
                                <a href="{{ route('lms.assignments.show', [$assign->course_class_id, $assign->id]) }}" class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[11px] shrink-0 shadow-xs transition">
                                    Kerjakan
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($isTeacher && !empty($lms['teacher_pending_grading_count']) && $lms['teacher_pending_grading_count'] > 0)
                <!-- Guru: Tugas yang Menunggu Penilaian -->
                <div class="p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 flex items-center justify-between gap-3 text-xs mt-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="text-lg">✍️</span>
                        <div>
                            <div class="font-bold text-indigo-900 dark:text-indigo-200">
                                {{ $lms['teacher_pending_grading_count'] }} Tugas Siswa Menunggu Dinilai
                            </div>
                            <div class="text-[11px] text-indigo-700/80 dark:text-indigo-400">
                                Kumpulkan dan berikan feedback nilai untuk pengumpulan terbaru.
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('lms.index') }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shrink-0 shadow-xs transition">
                        Beri Nilai
                    </a>
                </div>
            @else
                <!-- Default Info / Highlight Materi Terbaru -->
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800 text-xs flex items-center justify-between gap-3 mt-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="text-base">🚀</span>
                        <div class="truncate">
                            <span class="font-bold text-slate-800 dark:text-slate-200">KBM Daring SMAN 1 Lengkong</span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Akses modul, penugasan terstruktur, dan CBT terstandar kurikulum nasional.</p>
                        </div>
                    </div>
                    <a href="{{ route('lms.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 text-white font-semibold text-xs shrink-0 transition">
                        Buka Ruang Belajar
                    </a>
                </div>
            @endif
        </div>

        <!-- Card Footer Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('lms.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-xs transition">
                <span>🎓</span>
                <span>Ruang Kelas Mapel</span>
            </a>
            <a href="{{ route('rapor_kurnas.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-xs transition">
                <span>📊</span>
                <span>Rapor Kurnas</span>
            </a>
            @if($canManageKurikulum)
                <a href="{{ route('lms.master.subjects') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                    <span>📚</span>
                    <span>Kelola Mapel & Guru</span>
                </a>
            @endif
        </div>
    </div>

    <!-- ==========================================
         CARD 2: PERPUSTAKAAN TERPADU & DIGITAL
         ========================================== -->
    <div class="rounded-3xl bg-white dark:bg-slate-900 border border-amber-500/20 dark:border-amber-500/30 p-5 sm:p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between relative overflow-hidden group">
        <!-- Top Glow Accent -->
        <div class="absolute -right-12 -top-12 w-44 h-44 bg-amber-500/10 dark:bg-amber-500/15 rounded-full blur-2xl pointer-events-none group-hover:bg-amber-500/20 transition-colors"></div>

        <div>
            <!-- Card Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-md shadow-amber-500/20 shrink-0">
                        <svg class="w-5 h-5 text-slate-950 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white">Perpustakaan Digital & Fisik</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">Pustaka</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Katalog koleksi, E-Book Flip 3D interaktif & sirkulasi mandiri</p>
                    </div>
                </div>

                <a href="{{ route('library.index') }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 flex items-center gap-1 shrink-0 transition" title="Buka Perpustakaan">
                    <span>Masuk</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- Stats Grid Mini -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 my-4">
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 text-center">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Judul Buku</div>
                    <div class="text-base sm:text-lg font-black text-slate-900 dark:text-white mt-0.5">{{ $lib['total_books'] ?? 0 }}</div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 text-center">
                    <div class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">E-Book Flip 3D</div>
                    <div class="text-base sm:text-lg font-black text-amber-500 mt-0.5">{{ $lib['digital_books_count'] ?? 0 }}</div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 text-center">
                    <div class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">Dipinjam</div>
                    <div class="text-base sm:text-lg font-black text-indigo-500 mt-0.5">{{ $lib['active_loans_count'] ?? 0 }}</div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 text-center">
                    <div class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">Pengunjung Hari Ini</div>
                    <div class="text-base sm:text-lg font-black text-emerald-500 mt-0.5">{{ $lib['today_visitors_count'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Dynamic Role-Specific Section -->
            @if(!empty($lib['user_active_loans']) && $lib['user_active_loans']->isNotEmpty())
                <!-- Peminjaman Buku Saya Aktif -->
                <div class="mt-2 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                            Buku yang Sedang Dipinjam:
                        </span>
                        <a href="{{ route('library.loans.my') }}" class="text-[11px] text-slate-400 hover:text-indigo-500">Lihat Semua</a>
                    </div>
                    <div class="space-y-1.5">
                        @foreach($lib['user_active_loans'] as $loan)
                            <div class="p-2.5 rounded-xl bg-indigo-500/5 border border-indigo-500/20 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $loan->book->title ?? 'Buku Perpustakaan' }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                        Tgl Jatuh Tempo: {{ $loan->due_date ? \Carbon\Carbon::parse($loan->due_date)->isoFormat('D MMMM Y') : '-' }} &bull;
                                        Status: <span class="font-bold {{ $loan->status === 'TERLAMBAT' ? 'text-rose-500' : 'text-emerald-500' }}">{{ $loan->status }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('library.loans.my') }}" class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[11px] shrink-0 shadow-xs transition">
                                    Detail
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($canManageLib && !empty($lib['pending_loans_count']) && $lib['pending_loans_count'] > 0)
                <!-- Pengelola: Antrean Pinjam Menunggu Persetujuan -->
                <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-center justify-between gap-3 text-xs mt-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="text-lg">📢</span>
                        <div>
                            <div class="font-bold text-amber-900 dark:text-amber-200">
                                {{ $lib['pending_loans_count'] }} Pengajuan Peminjaman Online Menunggu Persetujuan
                            </div>
                            <div class="text-[11px] text-amber-700/80 dark:text-amber-400">
                                Konfirmasi atau tolak permintaan pinjam buku mandiri dari pemustaka.
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('library.loans.index') }}" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shrink-0 shadow-xs transition">
                        Proses Antrean
                    </a>
                </div>
            @else
                <!-- Rekomendasi E-Book Digital Flip 3D -->
                <div class="p-3 rounded-2xl bg-gradient-to-r from-amber-500/10 via-orange-500/5 to-transparent border border-amber-500/20 text-xs flex items-center justify-between gap-3 mt-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="text-lg">📖</span>
                        <div class="truncate">
                            <span class="font-bold text-slate-900 dark:text-white">E-Book Interaktif 3D Flip HTML</span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Membaca buku layaknya buku fisik dengan efek lembaran halaman 3D dan suara kertas nyata.</p>
                        </div>
                    </div>
                    <a href="{{ route('library.books.index', ['book_type' => 'digital_flip']) }}" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shrink-0 shadow-xs transition flex items-center gap-1.5">
                        <span>✨ Baca Flip 3D</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Card Footer Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('library.books.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-xs transition">
                <span>📚</span>
                <span>Katalog Buku</span>
            </a>
            <a href="{{ route('library.books.index', ['book_type' => 'digital_flip']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                <span>✨</span>
                <span>E-Book Flip 3D</span>
            </a>
            <a href="{{ route('library.leaderboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                <span>🏆</span>
                <span>Peringkat Literasi</span>
            </a>
        </div>
    </div>
</div>

