@php
    $currentUser = Auth::user();
    $canManageSecurity = $currentUser && ($currentUser->isAdmin() || $currentUser->isWakasekKurikulum() || $currentUser->isSuperAdmin() || ($currentUser->teacher && (int)$currentUser->teacher->id === (int)$courseClass->teacher_id));
    $scopedType = $scopedType ?? null;
    $scopedId = $scopedId ?? null;

    $blocksQuery = \App\Models\LmsStudentSecurityBlock::where('course_class_id', $courseClass->id)
        ->where('is_blocked', true)
        ->with(['student.schoolClass']);

    if ($scopedType && $scopedId) {
        $blocksQuery->where('blockable_type', $scopedType)->where('blockable_id', $scopedId);
    }

    $activeSecurityBlocks = $blocksQuery->orderByDesc('blocked_at')->get();
@endphp

@if($canManageSecurity)
<div class="rounded-3xl border {{ $activeSecurityBlocks->isNotEmpty() ? 'border-rose-300 dark:border-rose-900/60 bg-rose-50/50 dark:bg-rose-950/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900' }} p-5 shadow-xs space-y-4">
    <div class="flex items-center justify-between flex-wrap gap-3 pb-3 border-b {{ $activeSecurityBlocks->isNotEmpty() ? 'border-rose-200 dark:border-rose-900/40' : 'border-slate-100 dark:border-slate-800' }}">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl {{ $activeSecurityBlocks->isNotEmpty() ? 'bg-rose-500 text-white animate-pulse' : ($courseClass->isSecurityEnabled() ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-200 dark:bg-slate-800 text-slate-500') }} flex items-center justify-center text-base font-bold shrink-0">
                🛡️
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Keamanan Integritas Pembelajaran Siswa</span>
                    </h3>
                    @if($courseClass->isSecurityEnabled())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            AKTIF
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                            NONAKTIF
                        </span>
                    @endif
                    @if($activeSecurityBlocks->isNotEmpty())
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-rose-500 text-white shadow-xs">
                            {{ $activeSecurityBlocks->count() }} Terblokir
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    @if($courseClass->isSecurityEnabled())
                        Siswa otomatis diblokir saat meminimalkan jendela browser, membuka tab baru, atau beralih aplikasi.
                    @else
                        Fitur sedang dinonaktifkan oleh guru mapel. Siswa dapat leluasa belajar tanpa penguncian browser.
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Tombol Toggle Aktif / Nonaktif oleh Guru Mapel -->
            <form action="{{ route('lms.security.toggle', $courseClass->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-3.5 py-1.5 rounded-xl {{ $courseClass->isSecurityEnabled() ? 'bg-amber-500/15 hover:bg-amber-500/25 text-amber-700 dark:text-amber-300 border border-amber-500/30' : 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs' }} font-bold text-xs transition flex items-center gap-1.5 cursor-pointer" title="{{ $courseClass->isSecurityEnabled() ? 'Klik untuk mematikan fitur pemblokiran otomatis' : 'Klik untuk mengaktifkan pemblokiran otomatis siswa' }}">
                    <span>{{ $courseClass->isSecurityEnabled() ? '⏸️ Nonaktifkan Keamanan' : '▶️ Aktifkan Keamanan' }}</span>
                </button>
            </form>

            @if($activeSecurityBlocks->isNotEmpty())
                <form action="{{ route('lms.security.unblock_all', $courseClass->id) }}" method="POST" onsubmit="return confirm('Buka seluruh blokir keamanan untuk {{ $activeSecurityBlocks->count() }} siswa sekaligus?')">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                        <span>🔓 Buka Semua Blokir</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($activeSecurityBlocks->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 border-b border-rose-200/60 dark:border-rose-900/40">
                        <th class="py-2.5 px-3">Siswa</th>
                        <th class="py-2.5 px-3">Item Pembelajaran</th>
                        <th class="py-2.5 px-3">Penyebab Pelanggaran</th>
                        <th class="py-2.5 px-3">Waktu Terdeteksi</th>
                        <th class="py-2.5 px-3 text-right">Aksi Pembukaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rose-100 dark:divide-rose-900/30 text-slate-700 dark:text-slate-300">
                    @foreach($activeSecurityBlocks as $block)
                        <tr class="hover:bg-rose-50/80 dark:hover:bg-rose-950/40 transition">
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $block->student->name ?? 'Siswa' }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    NISN: {{ $block->student->nisn ?? '-' }} • Kelas {{ $block->student->schoolClass->name ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                                    {{ $block->item_type_label }}
                                </span>
                                <div class="font-semibold text-xs mt-1 text-slate-800 dark:text-slate-200">
                                    {{ $block->item_title }}
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center gap-1 text-rose-600 dark:text-rose-400 font-bold">
                                    <span>⚠️</span> {{ $block->violation_label }}
                                </span>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                                    {{ $block->reason }}
                                </p>
                            </td>
                            <td class="py-3 px-3 font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                {{ $block->blocked_at ? $block->blocked_at->format('H:i:s d/m/Y') : '-' }} WIB
                            </td>
                            <td class="py-3 px-3 text-right">
                                <form action="{{ route('lms.security.unblock', $courseClass->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="block_id" value="{{ $block->id }}">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1 ml-auto cursor-pointer">
                                        <span>🔓 Buka Blokir</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="py-2 text-center text-xs text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2">
            <span class="text-emerald-500 font-bold">✓</span>
            <span>Tidak ada siswa yang sedang terblokir pada {{ $scopedType ? strtolower($scopedType) . ' ini' : 'mata pelajaran ini' }}. Seluruh siswa dapat belajar secara normal.</span>
        </div>
    @endif
</div>
@endif
