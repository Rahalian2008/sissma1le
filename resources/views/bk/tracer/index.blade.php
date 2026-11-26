@extends('layouts.app')

@section('title', 'Tracer Study Alumni - Bimbingan Konseling')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.dashboard') }}" class="hover:text-indigo-600 font-medium">Bimbingan Konseling</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Tracer Alumni</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Tracer Study &amp; Penelusuran Tamatan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Database pemetaan sebaran kelulusan siswa ke jenjang Perguruan Tinggi (PTN/PTS), Dunia Kerja, dan Wirausaha</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Salin Tautan Kuesioner Publik -->
            <button type="button" onclick="copyPublicLink()" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 font-bold text-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span id="copy-btn-text">Salin Tautan Form Alumni</span>
            </button>

            <!-- Buka Form Publik Tab Baru -->
            <a href="{{ route('bk.tracer.public_form') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Buka Form</span>
            </a>

            <!-- Cetak Laporan Resmi -->
            <a href="{{ route('bk.tracer.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan Tracer</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-bold flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Kartu Statistik Distribusi Alumni -->
    @php
        $total = $totalAlumni > 0 ? $totalAlumni : 1;
        $pctKuliah = round(($collegeCount / $total) * 100, 1);
        $pctKerja = round(($workCount / $total) * 100, 1);
        $pctWirausaha = round(($businessCount / $total) * 100, 1);
        $pctTraining = round(($trainingCount / $total) * 100, 1);
        $pctSeeking = round(($seekingCount / $total) * 100, 1);
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total Alumni -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-400">Total Terdata</span>
                <span class="text-base">📋</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalAlumni) }}</div>
            <span class="text-[10px] text-slate-400 font-semibold">Responden Alumni</span>
        </div>

        <!-- Kuliah -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-indigo-200/80 dark:border-indigo-900/50 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400">Kuliah (PT)</span>
                <span class="text-base">🎓</span>
            </div>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($collegeCount) }}</div>
            <span class="text-[10px] text-indigo-500 font-bold">{{ $pctKuliah }}% dari total</span>
        </div>

        <!-- Bekerja -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Bekerja</span>
                <span class="text-base">💼</span>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($workCount) }}</div>
            <span class="text-[10px] text-emerald-500 font-bold">{{ $pctKerja }}% dari total</span>
        </div>

        <!-- Wirausaha -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-amber-200/80 dark:border-amber-900/50 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Wirausaha</span>
                <span class="text-base">🚀</span>
            </div>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ number_format($businessCount) }}</div>
            <span class="text-[10px] text-amber-500 font-bold">{{ $pctWirausaha }}% dari total</span>
        </div>

        <!-- Kursus & Pelatihan -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-sky-200/80 dark:border-sky-900/50 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400">Pelatihan/Kursus</span>
                <span class="text-base">📚</span>
            </div>
            <div class="text-2xl font-black text-sky-600 dark:text-sky-400">{{ number_format($trainingCount) }}</div>
            <span class="text-[10px] text-sky-500 font-bold">{{ $pctTraining }}% dari total</span>
        </div>

        <!-- Mencari Kerja -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500">Mencari Kerja</span>
                <span class="text-base">🔍</span>
            </div>
            <div class="text-2xl font-black text-slate-700 dark:text-slate-300">{{ number_format($seekingCount) }}</div>
            <span class="text-[10px] text-slate-400 font-bold">{{ $pctSeeking }}% dari total</span>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('bk.tracer.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <!-- Filter Tahun Lulus -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Tahun Kelulusan</label>
                <select name="graduation_year" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    <option value="">Semua Tahun Lulus</option>
                    @foreach($graduationYears as $y)
                        <option value="{{ $y }}" {{ request('graduation_year') == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Aktivitas -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Status Aktivitas</label>
                <select name="activity_status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    <option value="">Semua Status</option>
                    <option value="KULIAH" {{ request('activity_status') === 'KULIAH' ? 'selected' : '' }}>Kuliah / PT</option>
                    <option value="BEKERJA" {{ request('activity_status') === 'BEKERJA' ? 'selected' : '' }}>Bekerja</option>
                    <option value="WIRAUSAHA" {{ request('activity_status') === 'WIRAUSAHA' ? 'selected' : '' }}>Wirausaha</option>
                    <option value="KURSUS_PELATIHAN" {{ request('activity_status') === 'KURSUS_PELATIHAN' ? 'selected' : '' }}>Kursus / Pelatihan</option>
                    <option value="MENCARI_KERJA" {{ request('activity_status') === 'MENCARI_KERJA' ? 'selected' : '' }}>Mencari Kerja</option>
                </select>
            </div>

            <!-- Kata Kunci Pencarian -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Cari Alumni / Kampus / Kantor</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama, NISN, kampus..."
                       class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['graduation_year', 'activity_status', 'search']))
                <a href="{{ route('bk.tracer.index') }}" class="py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Alumni -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                <span>Daftar Data Responden Tracer Alumni</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                    {{ $alumni->total() }} Data
                </span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-bold">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Lengkap &amp; NISN</th>
                        <th class="py-3 px-4">Lulusan</th>
                        <th class="py-3 px-4">Status &amp; Keterangan Utama</th>
                        <th class="py-3 px-4">Kontak &amp; Domisili</th>
                        <th class="py-3 px-4 text-center">Rating BK</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($alumni as $index => $item)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                        <td class="py-3 px-4 text-center text-slate-400 font-semibold">
                            {{ $alumni->firstItem() + $index }}
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('bk.tracer.show', $item) }}" class="font-bold text-slate-900 dark:text-white hover:text-indigo-600 block">
                                {{ $item->full_name }}
                            </a>
                            <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                <span>{{ $item->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                @if($item->nisn)
                                    <span>&bull;</span>
                                    <span>NISN: {{ $item->nisn }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                Thn {{ $item->graduation_year }}
                            </span>
                            @if($item->last_class)
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $item->last_class }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $item->activity_badge_class }}">
                                {{ $item->activity_status_label }}
                            </span>

                            @if($item->activity_status === 'KULIAH' && $item->campus_name)
                                <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-1">
                                    {{ $item->campus_name }}
                                </div>
                                @if($item->study_program)
                                    <div class="text-[11px] text-indigo-600 dark:text-indigo-400">
                                        Prodi: {{ $item->study_program }} ({{ $item->college_level ?? 'S1' }})
                                    </div>
                                @endif
                            @elseif($item->activity_status === 'BEKERJA' && $item->company_name)
                                <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-1">
                                    {{ $item->company_name }}
                                </div>
                                @if($item->job_position)
                                    <div class="text-[11px] text-emerald-600 dark:text-emerald-400">
                                        Posisi: {{ $item->job_position }}
                                    </div>
                                @endif
                            @elseif($item->activity_status === 'WIRAUSAHA' && $item->business_name)
                                <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-1">
                                    {{ $item->business_name }}
                                </div>
                                @if($item->business_field)
                                    <div class="text-[11px] text-amber-600 dark:text-amber-400">
                                        Bidang: {{ $item->business_field }}
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($item->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->phone) }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-500 font-bold">
                                    <span>📱 {{ $item->phone }}</span>
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                            @if($item->current_city)
                                <div class="text-[10px] text-slate-400 mt-0.5">📍 {{ $item->current_city }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($item->bk_service_rating)
                                <span class="font-bold text-amber-500">{{ $item->bk_service_rating }} ⭐</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('bk.tracer.show', $item) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('bk.tracer.edit', $item) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('bk.tracer.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tracer alumni ini?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition" title="Hapus Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="text-3xl mb-2">🎓</div>
                            <div class="font-bold text-sm text-slate-600 dark:text-slate-400">Belum ada data tracer alumni</div>
                            <div class="text-xs text-slate-400 mt-1">Bagikan tautan form kuesioner kepada para alumni atau sesuaikan filter pencarian Anda.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alumni->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $alumni->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    function copyPublicLink() {
        const link = "{{ route('bk.tracer.public_form') }}";
        navigator.clipboard.writeText(link).then(() => {
            const btnText = document.getElementById('copy-btn-text');
            if (btnText) {
                const original = btnText.innerText;
                btnText.innerText = '✓ Berhasil Disalin!';
                setTimeout(() => { btnText.innerText = original; }, 2500);
            }
        }).catch(err => {
            prompt("Salin tautan formulir alumni:", link);
        });
    }
</script>
@endsection
