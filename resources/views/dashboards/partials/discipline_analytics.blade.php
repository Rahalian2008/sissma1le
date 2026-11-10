<!-- Modul Tata Tertib Terpadu: Rangking, Frekuensi, Ambang Batas Penghargaan & Peringatan -->
<div class="space-y-6">
    <!-- Header Rekapitulasi Tata Tertib -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-5 rounded-3xl text-white shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-indigo-300 font-bold text-lg">
                ⚖
            </div>
            <div>
                <h3 class="text-base font-black text-white">Rekapitulasi Tata Tertib</h3>
                <p class="text-xs text-slate-300">Statistik kedisiplinan, prestasi, dan pemantauan tindak lanjut bimbingan konseling.</p>
            </div>
        </div>
        <div class="flex items-center gap-2 sm:gap-3 text-xs">
            <div class="px-3.5 py-2 bg-white/10 rounded-2xl border border-white/10 flex items-center gap-2">
                <span class="text-emerald-400 font-bold">Total Prestasi</span>
                <span class="font-black text-white ml-1 tabular-num">{{ $topAchievers->sum('count') }} Catatan</span>
            </div>
            <div class="px-3.5 py-2 bg-white/10 rounded-2xl border border-white/10 flex items-center gap-2">
                <span class="text-rose-400 font-bold">Total Pelanggaran</span>
                <span class="font-black text-white ml-1 tabular-num">{{ $topViolators->sum('count') }} Kasus</span>
            </div>
        </div>
    </div>

    <!-- Baris 1: Rangking Prestasi vs Rangking Pelanggaran -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Rangking Prestasi Siswa -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center font-black text-sm">★</span>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Rangking Siswa Berprestasi</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Peringkat akumulasi poin apresiasi prestasi siswa.</p>
                    </div>
                </div>
                <a href="{{ route('discipline.ranking') }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-500 inline-flex items-center gap-1">
                    Lihat Semua <span>→</span>
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse($topAchievers as $idx => $item)
                    @php $s = $item['student']; @endphp
                    <div class="p-3 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 hover:bg-white dark:hover:bg-slate-800/70 hover:shadow-xs transition flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3 truncate mr-2">
                            <span class="w-7 h-7 rounded-full font-black flex items-center justify-center text-xs shrink-0
                                {{ $idx === 0 ? 'bg-amber-400 text-slate-900 shadow-xs' : ($idx === 1 ? 'bg-slate-300 dark:bg-slate-700 text-slate-800 dark:text-slate-200' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400')) }}">
                                {{ $idx + 1 }}
                            </span>
                            <div class="truncate">
                                <div class="font-bold text-slate-900 dark:text-slate-100 truncate">{{ $s->name }}</div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500">Kelas {{ $s->schoolClass?->name ?? '-' }} • NIS: {{ $s->nis }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-transparent dark:border-amber-800/60 text-[10px] font-bold rounded-md tabular-num">
                                {{ $item['count'] }} Prestasi
                            </span>
                            <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-transparent dark:border-emerald-800/60 font-black rounded-lg text-xs tabular-num">
                                +{{ $item['points'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400 dark:text-slate-500 italic">
                        Belum ada data prestasi terverifikasi.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Rangking Pelanggaran Siswa -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 flex items-center justify-center font-black text-sm">⚠</span>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Rangking Pelanggaran Tata Tertib</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Peringkat akumulasi poin pelanggaran siswa tertinggi.</p>
                    </div>
                </div>
                <a href="{{ route('violations.index') }}" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:text-rose-500 inline-flex items-center gap-1">
                    Lihat Semua <span>→</span>
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse($topViolators as $idx => $item)
                    @php $s = $item['student']; @endphp
                    <div class="p-3 rounded-2xl border border-rose-100 dark:border-rose-900/30 bg-rose-50/40 dark:bg-rose-950/20 hover:bg-white dark:hover:bg-slate-800/70 hover:shadow-xs transition flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3 truncate mr-2">
                            <span class="w-7 h-7 rounded-full font-black flex items-center justify-center text-xs shrink-0
                                {{ $idx === 0 ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-200 dark:bg-rose-900 text-rose-800 dark:text-rose-200' }}">
                                {{ $idx + 1 }}
                            </span>
                            <div class="truncate">
                                <div class="font-bold text-slate-900 dark:text-slate-100 truncate">{{ $s->name }}</div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500">Kelas {{ $s->schoolClass?->name ?? '-' }} • NIS: {{ $s->nis }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-transparent dark:border-rose-800/60 text-[10px] font-bold rounded-md tabular-num">
                                {{ $item['count'] }} Kasus
                            </span>
                            <span class="px-2.5 py-1 bg-rose-600 text-white font-black rounded-lg text-xs tabular-num">
                                -{{ $item['points'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400 dark:text-slate-500 italic">
                        Belum ada catatan pelanggaran terverifikasi.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Baris 2: Prestasi & Pelanggaran Sering Dilakukan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Prestasi Sering Dilakukan -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center text-xs font-bold">🏆</span>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">Prestasi Sering Diraih</h4>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Kegiatan prestasi yang paling sering dicapai siswa.</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Top 5</span>
            </div>

            <div class="space-y-2">
                @forelse($frequentAchievements as $fa)
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 hover:bg-emerald-50/40 dark:hover:bg-slate-800/70 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs transition">
                        <div class="truncate mr-3">
                            <div class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $fa->title }}</div>
                            <div class="text-[10px] text-slate-400 dark:text-slate-500">Tingkat: {{ $fa->level ?? '-' }}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-transparent dark:border-emerald-800/60 rounded-md font-bold text-[10px] tabular-num">
                                {{ $fa->total_count }}x diraih
                            </span>
                            <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-transparent dark:border-amber-800/60 font-black rounded-md text-[10px] tabular-num">
                                +{{ $fa->total_points }} Poin
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400 dark:text-slate-500 italic">
                        Belum ada catatan aktivitas prestasi.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pelanggaran Sering Terjadi -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 flex items-center justify-center text-xs font-bold">🚫</span>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">Pelanggaran Sering Terjadi</h4>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Bentuk pelanggaran tata tertib dengan frekuensi tertinggi.</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-rose-700 dark:text-rose-400">Top 5</span>
            </div>

            <div class="space-y-2">
                @forelse($frequentViolations as $fv)
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 hover:bg-rose-50/40 dark:hover:bg-slate-800/70 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs transition">
                        <div class="truncate mr-3">
                            <div class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $fv->title }}</div>
                            <div class="text-[10px] text-slate-400 dark:text-slate-500">Kategori Tata Tertib</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-transparent dark:border-rose-800/60 rounded-md font-bold text-[10px] tabular-num">
                                {{ $fv->total_count }}x terjadi
                            </span>
                            <span class="px-2 py-0.5 bg-slate-900 dark:bg-slate-700 text-white font-black rounded-md text-[10px] tabular-num">
                                -{{ $fv->total_points }} Poin
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400 dark:text-slate-500 italic">
                        Belum ada data pelanggaran tercatat.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Baris 3: Siswa Mendekati/Mendapatkan Penghargaan & Peringatan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Siswa Mendekati / Mendapatkan Penghargaan -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 flex items-center justify-center text-xs font-bold">🎖️</span>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">Siswa Mendekati / Meraih Penghargaan</h4>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Ambang batas penghargaan (≥100 Poin mendekati, ≥125 Poin resmi).</p>
                    </div>
                </div>
                <a href="{{ route('discipline.report') }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline inline-flex items-center gap-1">
                    Laporan <span>→</span>
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse($approachingOrAwardedStudents as $item)
                    @php $s = $item['student']; @endphp
                    <div class="p-3 rounded-xl border {{ $item['is_awarded'] ? 'border-emerald-200 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-amber-200 dark:border-amber-800/60 bg-amber-50/50 dark:bg-amber-950/20' }} flex items-center justify-between text-xs">
                        <div class="truncate mr-2">
                            <div class="font-bold text-slate-900 dark:text-slate-100">{{ $s->name }}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400">Kelas {{ $s->schoolClass?->name ?? '-' }} • {{ $item['reward_status']['level'] }}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($item['is_awarded'])
                                <span class="px-2 py-0.5 bg-emerald-600 text-white font-bold rounded-md text-[10px] tabular-num">
                                    Terpenuhi ({{ $item['points'] }} Poin)
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-500 text-white font-bold rounded-md text-[10px] tabular-num">
                                    Mendekati ({{ $item['points'] }} Poin)
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400 dark:text-slate-500 italic">
                        Belum ada siswa yang mencapai ambang penghargaan (≥100 Poin).
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Siswa Mendekati / Mendapatkan Peringatan & Pembinaan -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 flex items-center justify-center text-xs font-bold">🚨</span>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">Siswa Mendekati / Mendapatkan Peringatan</h4>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Ambang batas sanksi (≥40 Poin peringatan dini, ≥75 Poin pembinaan BK).</p>
                    </div>
                </div>
                <a href="{{ route('guidance.index') }}" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline inline-flex items-center gap-1">
                    Kasus BK <span>→</span>
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse($approachingOrWarnedStudents as $item)
                    @php $s = $item['student']; @endphp
                    <div class="p-3 rounded-xl border {{ $item['is_warned'] ? 'border-rose-300 dark:border-rose-800/60 bg-rose-50 dark:bg-rose-950/30' : 'border-amber-200 dark:border-amber-800/60 bg-amber-50/50 dark:bg-amber-950/20' }} flex items-center justify-between text-xs">
                        <div class="truncate mr-2">
                            <div class="font-bold text-slate-900 dark:text-slate-100">{{ $s->name }}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400">Kelas {{ $s->schoolClass?->name ?? '-' }} • {{ $item['guidance_status']['level'] }}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($item['is_warned'])
                                <span class="px-2 py-0.5 bg-rose-600 text-white font-bold rounded-md text-[10px] tabular-num">
                                    Perlu Tindakan ({{ $item['points'] }} Poin)
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-500 text-white font-bold rounded-md text-[10px] tabular-num">
                                    Waspada ({{ $item['points'] }} Poin)
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400 dark:text-slate-500 italic">
                        Tidak ada siswa mendekati atau dalam ambang sanksi (≥40 Poin).
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
