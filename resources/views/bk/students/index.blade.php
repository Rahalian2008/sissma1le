@extends('layouts.app')

@section('title', 'Data Siswa & Cetak Buku Induk Siswa - Bimbingan Konseling')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.dashboard') }}" class="hover:text-indigo-600 font-medium">Bimbingan Konseling</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Data Siswa &amp; Buku Induk</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span>Directori Data Siswa &amp; Lembar Buku Induk</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                    {{ number_format($totalActiveStudents) }} Siswa Aktif
                </span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Akses terpadu profil siswa, catatan bimbingan konseling, dan cetak lembar Buku Induk Siswa resmi standar pendidikan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-1.5">
                <span>&larr; Dasbor BK</span>
            </a>
        </div>
    </div>

    <!-- Navigasi Modul BK -->
    @include('bk.partials.nav')

    <!-- Filter & Pencarian Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('bk.students.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search Input -->
            <div class="sm:col-span-6 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama siswa, NIS, NISN, atau NIK..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>

            <!-- Filter Tingkat -->
            <div class="sm:col-span-2">
                <select name="grade_level" onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Tingkat</option>
                    <option value="X" {{ request('grade_level') === 'X' ? 'selected' : '' }}>Kelas X (Fase E)</option>
                    <option value="XI" {{ request('grade_level') === 'XI' ? 'selected' : '' }}>Kelas XI (Fase F)</option>
                    <option value="XII" {{ request('grade_level') === 'XII' ? 'selected' : '' }}>Kelas XII (Fase F)</option>
                </select>
            </div>

            <!-- Filter Rombel -->
            <div class="sm:col-span-3">
                <select name="class_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Kelas / Rombel</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Submit / Reset -->
            <div class="sm:col-span-1 flex items-center gap-1.5">
                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition shadow-xs">
                    Cari
                </button>
                @if(request()->anyFilled(['search', 'grade_level', 'class_id']))
                    <a href="{{ route('bk.students.index') }}" class="p-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-600 dark:text-slate-300 text-xs transition" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Siswa & Tombol Cetak Buku Induk -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/50 text-slate-500 font-bold uppercase tracking-wider text-[10.5px]">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Lengkap &amp; Identitas Siswa</th>
                        <th class="py-3.5 px-4">Kelas &amp; Wali Kelas</th>
                        <th class="py-3.5 px-4">Data Orang Tua / Kontak</th>
                        <th class="py-3.5 px-4 text-center">Status Layanan BK</th>
                        <th class="py-3.5 px-4 text-center w-56">Aksi &amp; Cetak Buku Induk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($students as $idx => $s)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-mono">
                                {{ $students->firstItem() + $idx }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                        {{ $s->initials ?: substr($s->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('bk.student.profile360', $s) }}" class="font-extrabold text-slate-900 dark:text-white hover:text-indigo-600 transition text-sm block">
                                            {{ $s->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-400 font-mono flex items-center gap-2 mt-0.5">
                                            <span>NIS: <strong class="text-slate-600 dark:text-slate-300">{{ $s->nis }}</strong></span>
                                            <span>&bull;</span>
                                            <span>NISN: <span class="text-slate-600 dark:text-slate-300">{{ $s->nisn ?: '-' }}</span></span>
                                            <span>&bull;</span>
                                            <span class="px-1.5 py-0.2 rounded text-[10px] {{ $s->gender === 'L' ? 'bg-sky-100 dark:bg-sky-950/60 text-sky-700' : 'bg-pink-100 dark:bg-pink-950/60 text-pink-700' }}">
                                                {{ $s->gender === 'L' ? 'L' : 'P' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60 inline-block">
                                    {{ $s->schoolClass?->name ?? ('Kelas ' . $s->current_class_level) }}
                                </span>
                                <div class="text-[11px] text-slate-400 mt-1 truncate">
                                    {{ $s->schoolClass?->homeroomTeacher?->name ?? 'Wali Kelas -' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-slate-700 dark:text-slate-300 font-medium">
                                    Ayah: <span class="font-semibold">{{ $s->family?->father_name ?: ($s->father_name ?: '-') }}</span>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    Ibu: {{ $s->family?->mother_name ?: ($s->mother_name ?: '-') }}
                                    @if(!empty($s->phone))
                                        &bull; HP: <span class="font-mono text-slate-500">{{ $s->phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $sessCount = $s->counselingSessions->count();
                                @endphp
                                @if($sessCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                        <span>💬</span> {{ $sessCount }} Sesi Konseling
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700">
                                        Belum Konseling
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <!-- Tombol Cetak Buku Induk (Paling Utama) -->
                                    <a href="{{ route('bk.students.buku-induk.print', $s) }}" target="_blank"
                                       class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-xs transition flex items-center gap-1.5 transform hover:-translate-y-0.5"
                                       title="Cetak Lembar Buku Induk Siswa Lengkap">
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>Cetak Induk</span>
                                    </a>

                                    <!-- Tombol Detail Lembar Induk Digital -->
                                    <a href="{{ route('bk.students.buku-induk.show', $s) }}"
                                       class="p-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 transition"
                                       title="Buka Lembar Buku Induk Digital">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    <!-- Tombol Profil 360 -->
                                    <a href="{{ route('bk.student.profile360', $s) }}"
                                       class="p-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-600 dark:text-indigo-400 transition"
                                       title="Buka Profil Siswa 360°">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </a>

                                    <!-- Tombol Tambah Konseling -->
                                    <a href="{{ route('bk.counseling.create', ['student_id' => $s->id]) }}"
                                       class="p-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-600 dark:text-emerald-400 transition"
                                       title="Buat Sesi Konseling Baru">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                Tidak ada data siswa yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
