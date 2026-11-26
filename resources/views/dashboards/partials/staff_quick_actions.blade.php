@php
    $currentUser = Auth::user();
    $canApproveLeaves = $currentUser && (
        $currentUser->hasAnyRole(['admin', 'super_admin', 'wali_kelas'])
        || in_array($currentUser->role, ['admin', 'super_admin', 'wali_kelas'], true)
    );
    $isPembinaEkstra = $currentUser && $currentUser->hasRole('pembina_ekstra');
    $canSeeKiosk = $currentUser && ! ($currentUser->hasRole('siswa', 'sekretaris_kelas', 'wali_kelas', 'koordinator_keagamaan') && ! $currentUser->hasRole('admin', 'super_admin'));
    $isAdmin = $currentUser && $currentUser->hasRole('admin', 'super_admin');

    $leavePendingCount = 0;
    $leaveApprovedCount = 0;
    $leaveRejectedCount = 0;
    $leaveTotalCount = 0;
    $leaveRoleLabel = 'Semua Kelas';
    $leaveScopeDescription = 'Persetujuan permohonan izin';
    $leaveTargetRoute = route('attendance.leaves.index');

    if ($canApproveLeaves) {
        $isHomeroomOnly = $currentUser->hasRole('wali_kelas') && ! $currentUser->hasAnyRole(['admin', 'super_admin', 'kesiswaan']);
        $homeroomClass = $currentUser->teacher?->homeroomClass;

        if ($isHomeroomOnly) {
            if ($homeroomClass) {
                $leaveRoleLabel = 'Wali Kelas • ' . $homeroomClass->name;
                $leaveScopeDescription = 'Ajuan izin kelas ' . $homeroomClass->name;
                $leaveTargetRoute = route('attendance.leaves.index', ['class_id' => $homeroomClass->id]);
            } else {
                $leaveRoleLabel = 'Wali Kelas (Belum Ada Rombel)';
                $leaveScopeDescription = 'Belum terhubung ke kelas binaan';
            }
        } elseif ($currentUser->hasAnyRole(['admin', 'super_admin'])) {
            $leaveRoleLabel = 'Admin • Seluruh Kelas';
            $leaveScopeDescription = 'Persetujuan izin seluruh siswa';
        } elseif ($currentUser->hasRole('kesiswaan')) {
            $leaveRoleLabel = 'Kesiswaan • Seluruh Kelas';
            $leaveScopeDescription = 'Pusat persetujuan izin kesiswaan';
        } elseif ($currentUser->hasRole('bk')) {
            $leaveRoleLabel = 'BK • Seluruh Kelas';
            $leaveScopeDescription = 'Pusat pantauan izin konseling';
        } else {
            if ($homeroomClass) {
                $leaveRoleLabel = 'Wali Kelas • ' . $homeroomClass->name;
                $leaveScopeDescription = 'Ajuan izin kelas ' . $homeroomClass->name;
                $leaveTargetRoute = route('attendance.leaves.index', ['class_id' => $homeroomClass->id]);
                $isHomeroomOnly = true;
            } else {
                $leaveRoleLabel = 'Guru / Piket • Seluruh Kelas';
                $leaveScopeDescription = 'Persetujuan izin seluruh siswa';
            }
        }

        $leaveBaseQuery = \App\Models\Attendance::where(function ($q) {
            $q->where('method', 'IZIN_SAKIT')
                ->orWhereIn('status', ['IZIN', 'SAKIT', 'PULANG_CEPAT'])
                ->orWhereNotNull('requested_status')
                ->orWhere('is_early_leave', true);
        });

        if ($isHomeroomOnly) {
            if ($homeroomClass) {
                $classId = $homeroomClass->id;
                $leaveBaseQuery->where(function ($q) use ($classId) {
                    $q->where('class_id', $classId)
                        ->orWhereHas('student', fn ($sq) => $sq->where('class_id', $classId));
                });
            } else {
                $leaveBaseQuery->whereRaw('1 = 0');
            }
        }

        $leavePendingCount = (clone $leaveBaseQuery)->where('verification_status', 'PERLU_VERIFIKASI')->count();
        $leaveApprovedCount = (clone $leaveBaseQuery)->where('verification_status', 'DIVERIFIKASI')->count();
        $leaveRejectedCount = (clone $leaveBaseQuery)->where('verification_status', 'DITOLAK')->count();
        $leaveTotalCount = (clone $leaveBaseQuery)->count();
    }
@endphp

<div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 p-3.5 sm:p-6 shadow-xs">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 sm:pb-3.5 mb-3.5 sm:mb-5 border-b border-slate-100 dark:border-slate-800 gap-2">
        <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-xs sm:text-sm shrink-0">
                ⚡
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="font-extrabold text-xs sm:text-base text-slate-900 dark:text-white tracking-tight truncate sm:whitespace-normal">
                    Pusat Navigasi & Aksi Operasional
                </h3>
                <p class="text-[10.5px] sm:text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                    Akses terpadu seluruh modul: Kurikulum, Kesiswaan, BK, dan Perpustakaan.
                </p>
            </div>
        </div>
        <span class="px-2.5 py-0.5 sm:py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-[9.5px] sm:text-[10px] font-bold text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60 self-start sm:self-center shrink-0">
            Struktur Terpadu
        </span>
    </div>

    <!-- Modular Operational Grid (Adaptive: 2 cols on mobile, 3 on tablet, 4 on laptop/desktop, 5 on wide 2k) -->
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-2.5 sm:gap-4">
        <!-- ================= KURIKULUM: LMS PEMBELAJARAN ================= -->
        <a href="{{ route('lms.index') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-cyan-50/40 dark:bg-cyan-950/20 border border-cyan-200/80 dark:border-cyan-900/60 hover:border-cyan-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-cyan-100 text-cyan-800 dark:bg-cyan-900/60 dark:text-cyan-300 shrink-0 whitespace-nowrap">
                        KURIKULUM
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition truncate">
                        LMS Pembelajaran
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Ruang kelas digital, materi & ujian CBT
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-cyan-200/60 dark:border-cyan-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-cyan-600 dark:text-cyan-400 truncate">KBM & Modul</span>
                <span class="font-bold text-cyan-600 dark:text-cyan-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Buka LMS →</span>
            </div>
        </a>

        <!-- ================= KURIKULUM: RAPOR KURNAS SMA ================= -->
        <a href="{{ route('rapor_kurnas.index') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-900/60 hover:border-emerald-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 shrink-0 whitespace-nowrap">
                        KURIKULUM
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate">
                        Rapor Kurnas
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        e-Rapor SMA 2025: tujuan & cetak rapor
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-emerald-200/60 dark:border-emerald-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-emerald-600 dark:text-emerald-400 truncate">Asesmen</span>
                <span class="font-bold text-emerald-600 dark:text-emerald-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Kelola →</span>
            </div>
        </a>

        <!-- ================= KESISWAAN: PRESENSI SISWA ================= -->
        <a href="{{ route('attendance.index') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-teal-50/40 dark:bg-teal-950/20 border border-teal-200/80 dark:border-teal-900/60 hover:border-teal-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-teal-100 text-teal-800 dark:bg-teal-900/60 dark:text-teal-300 shrink-0 whitespace-nowrap">
                        KESISWAAN
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition truncate">
                        Presensi
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Presensi KBM, scanner QR & pantauan
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-teal-200/60 dark:border-teal-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-teal-600 dark:text-teal-400 truncate">Kehadiran</span>
                <span class="font-bold text-teal-600 dark:text-teal-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Buka →</span>
            </div>
        </a>

        <!-- ================= KESISWAAN: APPROVE IZIN (JIKA MEMILIKI HAK) ================= -->
        @if($canApproveLeaves)
        <a href="{{ $leaveTargetRoute }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-900/60 hover:border-emerald-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    @if($leavePendingCount > 0)
                        <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 animate-pulse shrink-0 whitespace-nowrap shadow-2xs">
                            {{ $leavePendingCount }} MENUNGGU
                        </span>
                    @else
                        <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 shrink-0 whitespace-nowrap">
                            TUNTAS
                        </span>
                    @endif
                </div>

                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate">
                        Approve Izin
                    </h4>
                    <div class="flex items-center gap-1 text-[9.5px] sm:text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-100/70 dark:bg-emerald-900/40 px-1.5 sm:px-2 py-0.5 rounded-md w-fit max-w-full truncate mt-0.5 sm:mt-1">
                        <span class="truncate">{{ $leaveRoleLabel }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-emerald-200/60 dark:border-emerald-900/50 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                <span class="truncate">Total: <strong class="text-slate-800 dark:text-slate-200 font-black">{{ $leaveTotalCount }}</strong></span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">
                    Lihat →
                </span>
            </div>
        </a>
        @endif

        <!-- ================= KESISWAAN: TATA TERTIB SISWA ================= -->
        <a href="{{ route('violations.index') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-amber-50/40 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/60 hover:border-amber-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 shrink-0 whitespace-nowrap">
                        KESISWAAN
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition truncate">
                        Tata Tertib Siswa
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Poin pelanggaran & prestasi
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-amber-200/60 dark:border-amber-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-amber-600 dark:text-amber-400 truncate">Kedisiplinan</span>
                <span class="font-bold text-amber-600 dark:text-amber-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Kelola →</span>
            </div>
        </a>

        <!-- ================= KESISWAAN: HAFALAN AL-QUR'AN ================= -->
        <a href="{{ route('tahfidz.index') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-900/60 hover:border-emerald-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 shrink-0 whitespace-nowrap">
                        KESISWAAN
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate">
                        Hafalan Al-Qur'an
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Setoran Juz 30 & mutaba'ah
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-emerald-200/60 dark:border-emerald-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-emerald-600 dark:text-emerald-400 truncate">Tahfidz Juz 30</span>
                <span class="font-bold text-emerald-600 dark:text-emerald-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Buka →</span>
            </div>
        </a>

        <!-- ================= KESISWAAN: 7 KAIH ================= -->
        <a href="{{ route('habits.journal') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-teal-50/40 dark:bg-teal-950/20 border border-teal-200/80 dark:border-teal-900/60 hover:border-teal-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-teal-100 text-teal-800 dark:bg-teal-900/60 dark:text-teal-300 shrink-0 whitespace-nowrap">
                        KESISWAAN
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition truncate">
                        7 KAIH
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        7 Kebiasaan Anak Indonesia Hebat
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-teal-200/60 dark:border-teal-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-teal-600 dark:text-teal-400 truncate">Karakter</span>
                <span class="font-bold text-teal-600 dark:text-teal-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Jurnal →</span>
            </div>
        </a>

        <!-- ================= KESISWAAN: RAPORT DIGITAL ================= -->
        <a href="{{ route('raport.index') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-indigo-50/40 dark:bg-indigo-950/20 border border-indigo-200/80 dark:border-indigo-900/60 hover:border-indigo-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 shrink-0 whitespace-nowrap">
                        KESISWAAN
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition truncate">
                        Raport Digital
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Rekap capaian 8 pilar siswa
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-indigo-200/60 dark:border-indigo-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-indigo-600 dark:text-indigo-400 truncate">8 Pilar AI</span>
                <span class="font-bold text-indigo-600 dark:text-indigo-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Buka →</span>
            </div>
        </a>

        <!-- ================= KESISWAAN: SPMB BARU & TKA ================= -->
        <a href="{{ route('spmb.index') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-amber-50/40 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/60 hover:border-amber-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 shrink-0 whitespace-nowrap">
                        SPMB BARU
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition truncate">
                        SPMB & TKA
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Penerimaan siswa baru & seleksi TKA
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-amber-200/60 dark:border-amber-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-amber-600 dark:text-amber-400 truncate">Penerimaan</span>
                <span class="font-bold text-amber-600 dark:text-amber-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Kelola →</span>
            </div>
        </a>

        <!-- ================= BIMBINGAN KONSELING 360° ================= -->
        <a href="{{ route('bk.dashboard') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-pink-50/40 dark:bg-pink-950/20 border border-pink-200/80 dark:border-pink-900/60 hover:border-pink-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-pink-500/15 text-pink-600 dark:text-pink-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-pink-100 text-pink-800 dark:bg-pink-900/60 dark:text-pink-300 shrink-0 whitespace-nowrap">
                        BK 360°
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-pink-600 dark:group-hover:text-pink-400 transition truncate">
                        Bimbingan Konseling
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Konseling AKPD/AUM & psikotes
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-pink-200/60 dark:border-pink-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-pink-600 dark:text-pink-400 truncate">Layanan BK</span>
                <span class="font-bold text-pink-600 dark:text-pink-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Buka BK →</span>
            </div>
        </a>

        <!-- ================= PERPUSTAKAAN: PERPUSTAKAAN DIGITAL & FISIK ================= -->
        <a href="{{ route('library.index') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-amber-50/40 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/60 hover:border-amber-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 shrink-0 whitespace-nowrap">
                        PERPUSTAKAAN
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition truncate">
                        Perpustakaan
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Koleksi e-reader 3D & sirkulasi
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-amber-200/60 dark:border-amber-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-amber-600 dark:text-amber-400 truncate">Pustaka Digital</span>
                <span class="font-bold text-amber-600 dark:text-amber-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Masuk →</span>
            </div>
        </a>

        <!-- ================= ADM. KETATAUSAHAAN (KHUSUS TENAGA KEPENDIDIKAN / TU) ================= -->
        @if(auth()->user()?->canAccessTuModule())
        <a href="{{ route('tu.dashboard') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-purple-50/40 dark:bg-purple-950/20 border border-purple-200/80 dark:border-purple-900/60 hover:border-purple-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-purple-500/15 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v-2a2 2 0 012-2h4a2 2 0 012 2v2m-6 0h6m-9 3h12a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-300 shrink-0 whitespace-nowrap">
                        KETATAUSAHAAN
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition truncate">
                        Adm. Ketatausahaan
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Pusat kendali 5 modul TU, persuratan & aset
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-purple-200/60 dark:border-purple-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-purple-600 dark:text-purple-400 truncate">Pusat TU</span>
                <span class="font-bold text-purple-600 dark:text-purple-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Buka Modul TU →</span>
            </div>
        </a>
        @endif

        <!-- ================= KESISWAAN: DISPLAY KIOS TV (JIKA DIIZINKAN) ================= -->
        @if($canSeeKiosk)
        <a href="{{ route('kiosk.index') }}" target="_blank" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-amber-50/40 dark:bg-amber-950/20 border border-amber-300/80 dark:border-amber-700/60 hover:border-amber-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 shrink-0 whitespace-nowrap">
                        DISPLAY KIOSK
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition truncate">
                        Display Kios TV
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Monitor TV lobi real-time
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-amber-200/60 dark:border-amber-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-amber-600 dark:text-amber-400 truncate">Layar TV</span>
                <span class="font-bold text-amber-600 dark:text-amber-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Buka ↗</span>
            </div>
        </a>

        <!-- ================= RESEPSIONIS: KIOS BUKU TAMU ================= -->
        <a href="{{ route('guests.kiosk') }}" target="_blank" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-300/80 dark:border-emerald-700/60 hover:border-emerald-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 shrink-0 whitespace-nowrap">
                        RESEPSIONIS
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate">
                        Kios Buku Tamu
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Buku tamu mandiri selfie & ttd
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-emerald-200/60 dark:border-emerald-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-emerald-600 dark:text-emerald-400 truncate">Buku Tamu</span>
                <span class="font-bold text-emerald-600 dark:text-emerald-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Buka Kios ↗</span>
            </div>
        </a>
        @endif

        <!-- ================= MASTER DATA (JIKA ADMIN) ================= -->
        @if($isAdmin)
        <a href="{{ route('master.school_profile') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-indigo-50/40 dark:bg-indigo-950/20 border border-indigo-200/80 dark:border-indigo-900/60 hover:border-indigo-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 shrink-0 whitespace-nowrap">
                        ADMIN
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition truncate">
                        Master Data
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Konfigurasi identitas, rombel & akun
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-indigo-200/60 dark:border-indigo-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-indigo-600 dark:text-indigo-400 truncate">Basis Data</span>
                <span class="font-bold text-indigo-600 dark:text-indigo-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Kelola →</span>
            </div>
        </a>
        @endif

        <!-- ================= PEMBINA EKSTRAKURIKULER ================= -->
        @if($isPembinaEkstra)
        <a href="{{ route('extracurricular.index') }}" class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-cyan-50/40 dark:bg-cyan-950/20 border border-cyan-200/80 dark:border-cyan-900/60 hover:border-cyan-400 hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-start justify-between gap-1.5 sm:gap-2 mb-2 sm:mb-2.5">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold text-base sm:text-lg group-hover:scale-110 transition shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[10px] font-extrabold bg-cyan-100 text-cyan-800 dark:bg-cyan-900/60 dark:text-cyan-300 shrink-0 whitespace-nowrap">
                        EKSTRA
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition truncate">
                        Ekstrakurikuler
                    </h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-2">
                        Presensi & pembinaan ekstra
                    </p>
                </div>
            </div>
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-cyan-200/60 dark:border-cyan-900/40 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-cyan-600 dark:text-cyan-400 truncate">Kegiatan</span>
                <span class="font-bold text-cyan-600 dark:text-cyan-400 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5 shrink-0">Kelola →</span>
            </div>
        </a>
        @endif
    </div>
</div>
