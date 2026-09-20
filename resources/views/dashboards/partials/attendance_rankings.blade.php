<!-- Papan Peringkat Presensi: Siswa Paling Sering Alpa, Izin, dan Sakit -->
<div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-4 gap-2">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                <h3 class="text-lg font-black text-slate-800">Rangking Ketidakhadiran Siswa (Alpa, Izin, Sakit)</h3>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan siswa dengan frekuensi ketidakhadiran tertinggi untuk intervensi dini dan bimbingan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('attendance.report') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                Rekap Detail Presensi →
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- 1. Top Siswa Sering Alpa -->
        <div class="p-5 rounded-2xl bg-rose-50/50 border border-rose-100 space-y-4">
            <div class="flex items-center justify-between border-b border-rose-200/60 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-500 text-white flex items-center justify-center text-xs font-black">A</span>
                    <h4 class="font-extrabold text-sm text-rose-950">Sering Alpa (Tanpa Izin)</h4>
                </div>
                <span class="px-2 py-0.5 bg-rose-200/80 text-rose-900 text-[10px] font-bold rounded-md uppercase">Prioritas</span>
            </div>

            <div class="space-y-2.5">
                @forelse($topAlpaStudents as $idx => $s)
                    <div class="p-3 rounded-xl bg-white border border-rose-100 shadow-2xs flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2.5 truncate mr-2">
                            <span class="w-5 h-5 rounded-full {{ $idx === 0 ? 'bg-rose-600 text-white' : 'bg-rose-100 text-rose-700' }} font-bold text-[10px] flex items-center justify-center shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <div class="truncate">
                                <div class="font-bold text-slate-900 truncate">{{ $s->name }}</div>
                                <div class="text-[10px] text-slate-400">Kelas {{ $s->schoolClass?->name ?? '-' }}</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-rose-100 text-rose-800 font-black rounded-lg text-xs shrink-0">
                            {{ $s->count }}x Alpa
                        </span>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400 italic">
                        Tidak ada siswa dengan catatan alpa.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2. Top Siswa Sering Izin -->
        <div class="p-5 rounded-2xl bg-sky-50/50 border border-sky-100 space-y-4">
            <div class="flex items-center justify-between border-b border-sky-200/60 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-sky-500 text-white flex items-center justify-center text-xs font-black">I</span>
                    <h4 class="font-extrabold text-sm text-sky-950">Sering Izin Resmi</h4>
                </div>
                <span class="px-2 py-0.5 bg-sky-200/80 text-sky-900 text-[10px] font-bold rounded-md uppercase">Terpantau</span>
            </div>

            <div class="space-y-2.5">
                @forelse($topIzinStudents as $idx => $s)
                    <div class="p-3 rounded-xl bg-white border border-sky-100 shadow-2xs flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2.5 truncate mr-2">
                            <span class="w-5 h-5 rounded-full {{ $idx === 0 ? 'bg-sky-600 text-white' : 'bg-sky-100 text-sky-700' }} font-bold text-[10px] flex items-center justify-center shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <div class="truncate">
                                <div class="font-bold text-slate-900 truncate">{{ $s->name }}</div>
                                <div class="text-[10px] text-slate-400">Kelas {{ $s->schoolClass?->name ?? '-' }}</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-sky-100 text-sky-800 font-black rounded-lg text-xs shrink-0">
                            {{ $s->count }}x Izin
                        </span>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400 italic">
                        Tidak ada siswa dengan catatan izin.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3. Top Siswa Sering Sakit -->
        <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-100 space-y-4">
            <div class="flex items-center justify-between border-b border-amber-200/60 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-500 text-white flex items-center justify-center text-xs font-black">S</span>
                    <h4 class="font-extrabold text-sm text-amber-950">Sering Sakit</h4>
                </div>
                <span class="px-2 py-0.5 bg-amber-200/80 text-amber-900 text-[10px] font-bold rounded-md uppercase">Kesehatan</span>
            </div>

            <div class="space-y-2.5">
                @forelse($topSakitStudents as $idx => $s)
                    <div class="p-3 rounded-xl bg-white border border-amber-100 shadow-2xs flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2.5 truncate mr-2">
                            <span class="w-5 h-5 rounded-full {{ $idx === 0 ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-700' }} font-bold text-[10px] flex items-center justify-center shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <div class="truncate">
                                <div class="font-bold text-slate-900 truncate">{{ $s->name }}</div>
                                <div class="text-[10px] text-slate-400">Kelas {{ $s->schoolClass?->name ?? '-' }}</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 font-black rounded-lg text-xs shrink-0">
                            {{ $s->count }}x Sakit
                        </span>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400 italic">
                        Tidak ada siswa dengan catatan sakit.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
