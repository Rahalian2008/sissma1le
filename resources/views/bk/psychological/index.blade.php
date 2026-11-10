@extends('layouts.app')

@section('title', 'Asesmen & Tes Psikotes Siswa - BK')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Tes Psikotes Siswa</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Asesmen & Tes Psikotes Siswa</h1>
            <p class="text-xs text-slate-400 mt-0.5">Instrumen asesmen psikologis standar (VAK, Holland RIASEC, & Gardner) untuk pemetaan minat, bakat, gaya belajar, dan karier masa depan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.psychological.class-report') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Peta Profil Kelas</span>
            </a>
            <a href="{{ route('bk.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs hover:bg-slate-200 transition">
                <span>Kembali ke Dashboard BK</span>
            </a>
        </div>
    </div>

    <!-- Banner Info Siswa / Instrumen -->
    @if(auth()->user()->isStudent() || auth()->user()->student)
        <div class="p-5 rounded-3xl bg-linear-to-r from-emerald-600 to-teal-700 text-white shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-white/20 text-xs font-semibold uppercase tracking-wider">Ruang Asesmen Diri Siswa</span>
                    <h2 class="text-xl font-bold">Halo, {{ auth()->user()->student->name ?? auth()->user()->name }}!</h2>
                    <p class="text-xs text-emerald-100 max-w-2xl leading-relaxed">
                        Pilihlah instrumen asesmen psikotes di bawah ini untuk mengenali potensi gaya belajar dominan, minat jurusan kuliah, dan kecerdasan majemukmu. Kerjakan dengan santai, jujur, dan tanpa beban!
                    </p>
                </div>
                <div class="shrink-0 flex items-center gap-3 bg-white/10 backdrop-blur-xs p-3.5 rounded-2xl border border-white/20">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center font-black text-lg">
                        {{ auth()->user()->student->schoolClass->name ?? 'Siswa' }}
                    </div>
                    <div class="text-xs">
                        <div class="font-bold">NISN: {{ auth()->user()->student->nisn ?? '-' }}</div>
                        <div class="text-emerald-100">Status: Aktif Belajar</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Cards 3 Instrumen Psikotes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($tests as $test)
            @php
                $theme = match($test->slug) {
                    'vak' => [
                        'color' => 'emerald',
                        'border' => 'border-emerald-200 dark:border-emerald-900',
                        'badge' => 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-400',
                        'btn' => 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
                    ],
                    'riasec' => [
                        'color' => 'indigo',
                        'border' => 'border-indigo-200 dark:border-indigo-900',
                        'badge' => 'bg-indigo-100 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-400',
                        'btn' => 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-600/20',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                    ],
                    'gardner' => [
                        'color' => 'purple',
                        'border' => 'border-purple-200 dark:border-purple-900',
                        'badge' => 'bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-400',
                        'btn' => 'bg-purple-600 hover:bg-purple-700 shadow-purple-600/20',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
                    ],
                    default => [
                        'color' => 'teal',
                        'border' => 'border-teal-200 dark:border-teal-900',
                        'badge' => 'bg-teal-100 dark:bg-teal-950/70 text-teal-700 dark:text-teal-400',
                        'btn' => 'bg-teal-600 hover:bg-teal-700 shadow-teal-600/20',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
                    ],
                };
            @endphp

            <div class="rounded-3xl bg-white dark:bg-slate-900 border {{ $theme['border'] }} p-6 shadow-xs flex flex-col justify-between transition hover:shadow-lg">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="inline-block px-3 py-1 rounded-xl text-xs font-bold {{ $theme['badge'] }}">
                            {{ $test->category }}
                        </span>
                        <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-800 text-{{ $theme['color'] }}-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $theme['icon'] !!}
                            </svg>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">{{ $test->name }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-3 leading-relaxed">
                            {{ $test->short_desc }}
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1.5 font-medium">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $test->questions_count ?? $test->total_questions }} Butir Soal
                        </span>
                        <span class="flex items-center gap-1.5 font-medium">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            ± {{ $test->duration_minutes }} Menit
                        </span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    @if(auth()->user()->isStudent() || auth()->user()->student)
                        <a href="{{ route('bk.psychological.take', $test->slug) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-white font-bold text-xs shadow-md {{ $theme['btn'] }} transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Mulai Mengerjakan Tes</span>
                        </a>
                    @else
                        <div class="flex items-center gap-2">
                            <a href="{{ route('bk.psychological.index', ['test_id' => $test->id]) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                <span>Filter Hasil {{ $test->name }}</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filter & Riwayat Hasil Tes Siswa -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    {{ $student ? 'Riwayat Hasil Asesmen Anda' : 'Bank Data Hasil Asesmen Psikotes Siswa' }}
                </h2>
                <p class="text-xs text-slate-400">
                    {{ $student ? 'Daftar hasil tes yang pernah Anda kerjakan beserta laporan analisis psikologisnya' : 'Daftar asesmen yang telah diselesaikan oleh peserta didik untuk dianalisis oleh Guru BK' }}
                </p>
            </div>
        </div>

        <!-- Filter Bar untuk Guru / Konselor -->
        @if(! $student)
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-wrap items-center justify-between gap-3">
                <form action="{{ route('bk.psychological.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                    <select name="class_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">-- Semua Kelas / Rombel --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <select name="test_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">-- Semua Jenis Instrumen --</option>
                        @foreach($tests as $t)
                            <option value="{{ $t->id }}" {{ request('test_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa / NISN..." class="text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 pl-8 pr-3 py-2 w-52">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700 transition">Filter</button>
                    @if(request()->anyFilled(['class_id', 'test_id', 'search']))
                        <a href="{{ route('bk.psychological.index') }}" class="text-xs text-slate-500 hover:text-slate-800 dark:hover:text-white underline ml-1">Reset</a>
                    @endif
                </form>
            </div>
        @endif

        <!-- Tabel Hasil Tes -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Waktu Asesmen</th>
                            <th class="py-3 px-4">Nama Siswa & Kelas</th>
                            <th class="py-3 px-4">Instrumen Tes</th>
                            <th class="py-3 px-4">Profil Dominan</th>
                            <th class="py-3 px-4">Tipe / Kode Khusus</th>
                            <th class="py-3 px-4">Status & Catatan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($results as $res)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-3.5 px-4 font-mono text-slate-500">
                                    {{ $res->taken_at ? $res->taken_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $res->student->name ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        NISN: {{ $res->student->nisn ?? '-' }} • Kelas: {{ $res->student->schoolClass->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-block px-2.5 py-0.5 rounded-lg text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $res->test->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $res->profile_label }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        Dominan: <span class="font-semibold">{{ $res->dominant_dimension }}</span>
                                        @if($res->secondary_dimension)
                                            • Sekunder: {{ $res->secondary_dimension }}
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($res->holland_code)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md font-mono font-bold text-xs bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            {{ $res->holland_code }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">{{ $res->dominant_dimension }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300">
                                        SELESAI DITEST
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('bk.psychological.result', $res) }}" class="p-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 hover:bg-teal-100 transition" title="Lihat Laporan Lengkap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('bk.psychological.print', $res) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition" title="Cetak Surat Laporan Resmi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="font-medium text-xs">Belum ada riwayat hasil asesmen psikotes yang ditemukan.</p>
                                        @if(auth()->user()->isStudent() || auth()->user()->student)
                                            <p class="text-[11px] text-slate-500">Silakan klik tombol <b>"Mulai Mengerjakan Tes"</b> pada salah satu instrumen di atas.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($results->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $results->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

