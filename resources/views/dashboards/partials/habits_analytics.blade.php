<!-- Modul Karakter: 7 Kebiasaan Anak Indonesia Hebat (Diagram & Persentase Capaian) -->
<div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 sm:p-7 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-teal-500 animate-pulse"></span>
                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Diagram & Persentase Karakter (7 Kebiasaan Anak Hebat)</h3>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Monitoring realtime ketercapaian 7 pilar pembiasaan karakter peserta didik per {{ \Carbon\Carbon::today()->isoFormat('dddd, D MMMM Y') }}.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->role === 'super_admin')
                <button type="button" onclick="document.getElementById('modal-target-times-dash').classList.remove('hidden')" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1 shadow-xs cursor-pointer">
                    ⚙️ Atur Target Jam
                </button>
            @endif
            <a href="{{ route('habits.charts') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition inline-flex items-center gap-1">
                Diagram Detail <span>→</span>
            </a>
            @if(Route::has('habits.report'))
                <a href="{{ route('habits.report') }}" class="px-3.5 py-1.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl text-xs transition shadow-xs">
                    Laporan Karakter
                </a>
            @endif
        </div>
    </div>

    <!-- Visual Diagram Row: Donut Gauge + Multi-Pillar Comparison Bar Diagram -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
        <!-- 1. Donut Gauge Diagram Capaian Keseluruhan -->
        <div class="flex flex-col items-center justify-center p-6 bg-gradient-to-b from-teal-50/50 to-slate-50 dark:from-teal-950/20 dark:to-slate-850 rounded-3xl border border-teal-100/80 dark:border-teal-900/40 text-center">
            <div class="relative w-44 h-44 flex items-center justify-center">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <!-- Background Ring -->
                    <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="12" class="text-slate-200 dark:text-slate-700" fill="transparent"/>
                    <!-- Progress Segment -->
                    <circle cx="50" cy="50" r="40" stroke="#0d9488" stroke-width="12" stroke-linecap="round" fill="transparent"
                        stroke-dasharray="251.2"
                        stroke-dashoffset="{{ 251.2 - (251.2 * min(100, $overallHabitPercentage) / 100) }}"/>
                </svg>
                <div class="absolute flex flex-col items-center justify-center">
                    <span class="text-3xl font-black text-slate-900 dark:text-white tabular-num">{{ $overallHabitPercentage }}%</span>
                    <span class="text-[10px] font-bold text-teal-700 dark:text-teal-400 uppercase tracking-wider mt-0.5">KONSISTENSI</span>
                </div>
            </div>

            <div class="text-xs text-slate-600 dark:text-slate-400 mt-4 space-y-1">
                <div class="font-bold text-slate-800 dark:text-slate-200 tabular-num">
                    {{ $totalCompletedHabits ?? $habitStats->sum('completed_count') }} / {{ $totalExpectedHabits ?? ($habitStats->first()['total_students'] ?? 0) * 7 }} Checklist
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold inline-block
                    {{ $overallHabitPercentage >= 80 ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60' : ($overallHabitPercentage >= 60 ? 'bg-teal-100 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 border border-teal-200 dark:border-teal-800/60' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60') }}">
                    {{ $overallHabitPercentage >= 80 ? 'Teladan Unggul' : ($overallHabitPercentage >= 60 ? 'Berkembang Konsisten' : 'Perlu Pembiasaan') }}
                </span>
            </div>
        </div>

        <!-- 2. Diagram Batang Perbandingan Capaian 7 Pilar -->
        <div class="lg:col-span-2 p-5 bg-slate-50/80 dark:bg-slate-800/40 rounded-3xl border border-slate-200/80 dark:border-slate-800 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">Diagram Capaian Tiap Pilar Karakter</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Persentase keberhasilan pembiasaan siswa pada masing-masing dari 7 kebiasaan.</p>
                </div>
                <span class="text-[10px] font-bold text-slate-400 font-mono">Realtime Today</span>
            </div>

            <div class="space-y-2 pt-1">
                @foreach($habitStats as $hs)
                    @php
                        $h = $hs['habit'];
                        $barColors = [
                            1 => ['bar' => 'bg-amber-500', 'text' => 'text-amber-700 dark:text-amber-400'],
                            2 => ['bar' => 'bg-emerald-500', 'text' => 'text-emerald-700 dark:text-emerald-400'],
                            3 => ['bar' => 'bg-blue-500', 'text' => 'text-blue-700 dark:text-blue-400'],
                            4 => ['bar' => 'bg-rose-500', 'text' => 'text-rose-700 dark:text-rose-400'],
                            5 => ['bar' => 'bg-purple-500', 'text' => 'text-purple-700 dark:text-purple-400'],
                            6 => ['bar' => 'bg-indigo-500', 'text' => 'text-indigo-700 dark:text-indigo-400'],
                            7 => ['bar' => 'bg-cyan-500', 'text' => 'text-cyan-700 dark:text-cyan-400'],
                        ];
                        $col = $barColors[$h->order_number] ?? ['bar' => 'bg-teal-500', 'text' => 'text-teal-700 dark:text-teal-400'];
                    @endphp
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 truncate mr-2">
                                <span class="w-5 h-5 rounded-md {{ $col['bar'] }} text-white text-[10px] font-black flex items-center justify-center shrink-0">
                                    K{{ $h->order_number }}
                                </span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $h->name }}</span>
                                @if($h->target_time)
                                    <span class="text-[10px] text-slate-400 hidden sm:inline font-mono">({{ $h->target_time }})</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 font-mono">{{ $hs['completed_count'] }}/{{ $hs['total_students'] }}</span>
                                <span class="font-black text-xs {{ $col['text'] }} font-mono w-11 text-right tabular-num">{{ $hs['percentage'] }}%</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-200/90 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                            <div class="{{ $col['bar'] }} h-full rounded-full transition-all duration-700" style="width: {{ $hs['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 7 Pilar Mini Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
        @foreach($habitStats as $hs)
            @php
                $h = $hs['habit'];
                $orderColors = [
                    1 => ['bg' => 'bg-amber-500', 'light' => 'bg-amber-50/60 dark:bg-amber-950/20 border-amber-200 dark:border-amber-900/40 text-amber-900 dark:text-amber-200', 'badge' => 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300'],
                    2 => ['bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-900/40 text-emerald-900 dark:text-emerald-200', 'badge' => 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300'],
                    3 => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-50/60 dark:bg-blue-950/20 border-blue-200 dark:border-blue-900/40 text-blue-900 dark:text-blue-200', 'badge' => 'bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300'],
                    4 => ['bg' => 'bg-rose-500', 'light' => 'bg-rose-50/60 dark:bg-rose-950/20 border-rose-200 dark:border-rose-900/40 text-rose-900 dark:text-rose-200', 'badge' => 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300'],
                    5 => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-50/60 dark:bg-purple-950/20 border-purple-200 dark:border-purple-900/40 text-purple-900 dark:text-purple-200', 'badge' => 'bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300'],
                    6 => ['bg' => 'bg-indigo-500', 'light' => 'bg-indigo-50/60 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-900/40 text-indigo-900 dark:text-indigo-200', 'badge' => 'bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300'],
                    7 => ['bg' => 'bg-cyan-500', 'light' => 'bg-cyan-50/60 dark:bg-cyan-950/20 border-cyan-200 dark:border-cyan-900/40 text-cyan-900 dark:text-cyan-200', 'badge' => 'bg-cyan-100 dark:bg-cyan-950/60 text-cyan-800 dark:text-cyan-300'],
                ];
                $theme = $orderColors[$h->order_number] ?? ['bg' => 'bg-teal-500', 'light' => 'bg-teal-50 dark:bg-teal-950/20 border-teal-200 dark:border-teal-900/40 text-teal-900 dark:text-teal-200', 'badge' => 'bg-teal-100 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300'];
            @endphp
            <div class="p-3.5 rounded-2xl border {{ $theme['light'] }} flex flex-col justify-between space-y-2">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="w-5 h-5 rounded-md {{ $theme['bg'] }} text-white font-black text-[10px] flex items-center justify-center">
                            K{{ $h->order_number }}
                        </span>
                        <span class="text-[10px] font-bold font-mono {{ $theme['badge'] }} px-1.5 py-0.5 rounded tabular-num">
                            {{ $hs['percentage'] }}%
                        </span>
                    </div>
                    <h5 class="font-extrabold text-[11px] text-slate-800 dark:text-slate-100 mt-2 line-clamp-1" title="{{ $h->name }}">{{ $h->name }}</h5>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 line-clamp-1 font-mono">
                        {{ $h->target_time ?? 'Fleksibel' }}
                    </p>
                </div>

                <div class="text-[10px] text-slate-600 dark:text-slate-400 font-semibold pt-1 border-t border-slate-200/50 dark:border-slate-800 flex justify-between">
                    <span>Selesai:</span>
                    <span class="font-mono font-bold tabular-num">{{ $hs['completed_count'] }}/{{ $hs['total_students'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Quick Footer Links -->
    <div class="flex flex-wrap items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 gap-2">
        <div class="flex items-center gap-2">
            <span>Standar Pembiasaan Nasional Siswa Hebat • Target jam dapat diatur oleh Super Admin</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('habits.journal') }}" class="font-bold text-teal-700 dark:text-teal-400 hover:underline inline-flex items-center gap-1">
                Buka Jurnal Siswa <span>→</span>
            </a>
            <a href="{{ route('habits.summary') }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1">
                Ringkasan <span>→</span>
            </a>
        </div>
    </div>
</div>

@if(Auth::user()->role === 'super_admin')
<!-- Modal Atur Target Jam 7 Kebiasaan Langsung dari Dashboard -->
<div id="modal-target-times-dash" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
            <div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Pengaturan Target Jam 7 Kebiasaan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Konfigurasi rentang target waktu pembiasaan (Khusus Super Admin).</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-target-times-dash').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center font-bold cursor-pointer">
                ✕
            </button>
        </div>

        <form action="{{ route('habits.target_times.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                @foreach($habits as $idx => $h)
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-xs text-slate-800 dark:text-slate-200">K{{ $h->order_number }}: {{ $h->name }}</span>
                            <input type="hidden" name="targets[{{ $idx }}][id]" value="{{ $h->id }}">
                        </div>
                        <input type="text" name="targets[{{ $idx }}][target_time]" value="{{ $h->target_time }}" placeholder="Contoh: 04:30 - 05:30 WIB" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-target-times-dash').classList.add('hidden')" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl text-xs transition shadow-xs cursor-pointer">
                    Simpan Target Waktu
                </button>
            </div>
        </form>
    </div>
</div>
@endif
