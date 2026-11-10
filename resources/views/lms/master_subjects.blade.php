@extends('layouts.app')

@section('title', 'Master Mata Pelajaran & Penugasan LMS - Permendikdasmen No. 13 Tahun 2025')

@section('content')
<div class="space-y-6">
    @include('lms.partials.nav')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('lms.index') }}" class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition">&larr; Kembali ke LMS</a>
                <span class="text-slate-400">•</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20">
                    ⚖️ Permendikdasmen No. 13 Tahun 2025
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">Master Mata Pelajaran & Penugasan Kelas LMS</h1>
            <p class="text-xs text-slate-500 max-w-3xl leading-relaxed">
                Struktur kurikulum SMA sesuai regulasi terbaru Permendikdasmen No. 13 Tahun 2025: <strong>Mata Pelajaran Wajib</strong>, <strong>Mata Pelajaran Pilihan</strong> (Fase F tanpa sekat penjurusan kaku IPA/IPS), dan <strong>Muatan Lokal</strong>.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($isAdminOrKurikulum)
                <form action="{{ route('lms.master.subjects.generate_standard') }}" method="POST" onsubmit="return confirm('Generate / sinkronkan seluruh master mata pelajaran sesuai Permendikdasmen No. 13 Tahun 2025?')" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Generate Mapel Permendikdasmen 13/2025</span>
                    </button>
                </form>
                <button onclick="document.getElementById('modal-add-subject').classList.remove('hidden')" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Manual</span>
                </button>
            @endif
            @if($isAdminOrKurikulum || $isTeacher)
                <button onclick="document.getElementById('modal-add-course').classList.remove('hidden')" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>{{ $isTeacher && !$isAdminOrKurikulum ? 'Tugaskan Diri ke Kelas' : 'Tugaskan Guru ke Kelas' }}</span>
                </button>
            @endif
        </div>

    </div>

    <!-- Section 1: Daftar Mata Pelajaran SMA -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span id="mapel-header-title">{{ ($isTeacher && !$isAdminOrKurikulum) ? 'Mata Pelajaran yang Anda Ampu' : 'Daftar Mata Pelajaran Terdaftar' }}</span>
                    <span id="badge-mapel-count" class="px-2 py-0.5 rounded-full text-xs font-black bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300">
                        {{ $subjects->count() }} Mapel
                    </span>
                </h2>
                <p id="mapel-header-desc" class="text-xs text-slate-500">
                    @if($isTeacher && !$isAdminOrKurikulum)
                        Mata pelajaran yang Anda ampu pada kelas pembelajaran tahun ini
                    @else
                        Mata Pelajaran Wajib (Umum), Pilihan (Lintas Disiplin Fase F), dan Muatan Lokal
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if($isTeacher && !$isAdminOrKurikulum)
                    <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-800 p-1 text-xs">
                        <button type="button" onclick="showSubjectTab('my')" id="tab-btn-my" class="px-3 py-1.5 rounded-lg font-bold transition bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs">
                            Mapel yang Saya Ampu ({{ $mySubjects->count() }})
                        </button>
                        <button type="button" onclick="showSubjectTab('all')" id="tab-btn-all" class="px-3 py-1.5 rounded-lg font-medium text-slate-500 hover:text-slate-900 dark:hover:text-white transition">
                            Semua Mapel Kurikulum ({{ $allSubjects->count() }})
                        </button>
                    </div>
                @else
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 px-3 py-1.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        Fase E = Kelas X • Fase F = Kelas XI & XII
                    </div>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 uppercase font-extrabold text-slate-500">
                    <tr>
                        <th class="p-3">Kode</th>
                        <th class="p-3">Nama Mata Pelajaran</th>
                        <th class="p-3">Kelompok Mapel (Permendikdasmen 13/2025)</th>
                        <th class="p-3">Tingkat / Fase</th>
                        <th class="p-3 text-center">KKTP</th>
                        <th class="p-3 text-center">{{ ($isTeacher && !$isAdminOrKurikulum) ? 'Kelas yang Anda Ajar' : 'Jumlah Kelas' }}</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>

                {{-- Tabel Mapel Guru (Default untuk Guru) --}}
                <tbody id="tbody-my-subjects" class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($subjects as $sb)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                            <td class="p-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $sb->code }}</td>
                            <td class="p-3 font-bold text-slate-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ str_contains(strtolower($sb->group), 'pilihan') ? 'bg-emerald-500' : (str_contains(strtolower($sb->group), 'lokal') ? 'bg-amber-500' : 'bg-indigo-500') }}"></span>
                                    <span>{{ $sb->name }}</span>
                                </div>
                            </td>
                            <td class="p-3">
                                @if(str_contains(strtolower($sb->group), 'pilihan'))
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20 inline-flex items-center gap-1.5">
                                        <span>📘</span>
                                        <span>{{ $sb->group }}</span>
                                    </span>
                                @elseif(str_contains(strtolower($sb->group), 'lokal'))
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/20 inline-flex items-center gap-1.5">
                                        <span>🌿</span>
                                        <span>{{ $sb->group }}</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20 inline-flex items-center gap-1.5">
                                        <span>🏛️</span>
                                        <span>{{ $sb->group }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ $sb->grade }}
                                </span>
                            </td>
                            <td class="p-3 text-center font-bold text-slate-800 dark:text-slate-200">{{ $sb->kkm }}</td>
                            <td class="p-3 text-center">
                                @if($isTeacher && !$isAdminOrKurikulum && $currentTeacher)
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $courseClasses->where('subject_id', $sb->id)->count() }}
                                    </span>
                                    <span class="text-slate-400">Kelas</span>
                                @else
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $sb->course_classes_count }}</span>
                                    <span class="text-slate-400">Kelas</span>
                                @endif
                            </td>
                            <td class="p-3 text-right whitespace-nowrap">
                                @if($isAdminOrKurikulum)
                                    <button type="button" onclick="openEditSubjectModal({{ json_encode($sb) }})" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 p-1 mr-2 transition">
                                        Edit
                                    </button>
                                    <form action="{{ route('lms.master.subjects.destroy', $sb->id) }}" method="POST" onsubmit="return confirm('Hapus mata pelajaran ini? Semua kelas terkait akan terhapus.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-rose-500 hover:text-rose-700 p-1 transition">Hapus</button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                                        ✓ Anda Ampu
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                @if($isTeacher && !$isAdminOrKurikulum)
                                    <div class="max-w-md mx-auto space-y-2">
                                        <div class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum Ada Mata Pelajaran yang Anda Ampu</div>
                                        <p class="text-xs text-slate-500">Anda belum mengampu mata pelajaran. Silakan gunakan tombol <strong>"Tugaskan Diri ke Kelas"</strong> untuk memilih mata pelajaran dan kelas pembelajaran Anda.</p>
                                    </div>
                                @else
                                    Belum ada mata pelajaran terdaftar.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($isTeacher && !$isAdminOrKurikulum)
                    {{-- Tabel Seluruh Mapel Kurikulum (Bisa dibuka guru via tab) --}}
                    <tbody id="tbody-all-subjects" class="divide-y divide-slate-100 dark:divide-slate-800 hidden">
                        @forelse($allSubjects as $sb)
                            @php
                                $isTaughtByMe = $mySubjects->contains('id', $sb->id);
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $sb->code }}</td>
                                <td class="p-3 font-bold text-slate-900 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ str_contains(strtolower($sb->group), 'pilihan') ? 'bg-emerald-500' : (str_contains(strtolower($sb->group), 'lokal') ? 'bg-amber-500' : 'bg-indigo-500') }}"></span>
                                        <span>{{ $sb->name }}</span>
                                        @if($isTaughtByMe)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30">Anda Ampu</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-3">
                                    @if(str_contains(strtolower($sb->group), 'pilihan'))
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20 inline-flex items-center gap-1.5">
                                            <span>📘</span>
                                            <span>{{ $sb->group }}</span>
                                        </span>
                                    @elseif(str_contains(strtolower($sb->group), 'lokal'))
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/20 inline-flex items-center gap-1.5">
                                            <span>🌿</span>
                                            <span>{{ $sb->group }}</span>
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20 inline-flex items-center gap-1.5">
                                            <span>🏛️</span>
                                            <span>{{ $sb->group }}</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $sb->grade }}
                                    </span>
                                </td>
                                <td class="p-3 text-center font-bold text-slate-800 dark:text-slate-200">{{ $sb->kkm }}</td>
                                <td class="p-3 text-center">
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $sb->course_classes_count }}</span>
                                    <span class="text-slate-400">Kelas</span>
                                </td>
                                <td class="p-3 text-right whitespace-nowrap">
                                    @if($isTaughtByMe)
                                        <span class="text-[11px] text-cyan-600 dark:text-cyan-400 font-bold px-2.5 py-1 rounded-lg bg-cyan-500/10 border border-cyan-500/20">
                                            Aktif Anda Ampu
                                        </span>
                                    @else
                                        <button type="button" onclick="selectSubjectForCourse({{ $sb->id }})" class="px-2.5 py-1 rounded-lg text-xs font-bold text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-500/30 transition">
                                            + Ampu Mapel Ini
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">Belum ada mata pelajaran terdaftar dalam kurikulum.</td>
                            </tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>
    </div>

    <!-- Section 2: Penugasan Guru ke Kelas (Course Classes) -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>{{ ($isTeacher && !$isAdminOrKurikulum) ? 'Alokasi & Penugasan Kelas yang Anda Ampu' : 'Alokasi & Penugasan Guru Pengampu Kelas' }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-black bg-cyan-100 dark:bg-cyan-950/80 text-cyan-700 dark:text-cyan-300">
                        {{ $courseClasses->count() }} Kelas
                    </span>
                </h2>
                <p class="text-xs text-slate-500">
                    {{ ($isTeacher && !$isAdminOrKurikulum) ? 'Daftar rombel/kelas, jadwal belajar, dan tautan langsung ke LMS untuk kelas yang Anda ampu' : 'Kombinasi mata pelajaran, rombel/kelas, guru pengampu, serta jadwal pembelajaran mingguan' }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 uppercase font-extrabold text-slate-500">
                    <tr>
                        <th class="p-3">Kelas / Rombel</th>
                        <th class="p-3">Mata Pelajaran</th>
                        <th class="p-3">Kelompok Mapel</th>
                        <th class="p-3">Guru Pengampu</th>
                        <th class="p-3">Jadwal Belajar</th>
                        <th class="p-3">Tahun Pelajaran</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($courseClasses as $cc)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                            <td class="p-3 font-bold text-slate-900 dark:text-white">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20">
                                    {{ $cc->schoolClass->name }}
                                </span>
                            </td>
                            <td class="p-3 font-bold text-slate-800 dark:text-slate-200">
                                {{ $cc->subject->name }}
                            </td>
                            <td class="p-3">
                                <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400">
                                    {{ $cc->subject->group }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-700 dark:text-slate-300 font-semibold">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $cc->teacher->name }}</span>
                                    @if($currentTeacher && $cc->teacher_id === $currentTeacher->id)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30">Anda</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3 text-slate-500 font-medium">
                                <div class="flex flex-col gap-1">
                                    @foreach($cc->getSchedulesList() as $s)
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 shrink-0"></span>
                                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $s['day'] ?? 'Senin' }}:</span>
                                            <span class="font-mono text-[11px] text-slate-500 dark:text-slate-400">{{ $s['start_time'] ?? '07:30' }} - {{ $s['end_time'] ?? '09:00' }} WIB</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-3 text-slate-400">
                                {{ $cc->academicYear->name ?? '-' }}
                            </td>
                            <td class="p-3 text-right whitespace-nowrap">
                                <a href="{{ route('lms.course', $cc->id) }}" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-xs mr-2 transition inline-flex items-center gap-1">
                                    <span>Buka LMS</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                                @if($isAdminOrKurikulum || ($isTeacher && $currentTeacher && $cc->teacher_id === $currentTeacher->id))
                                    <form action="{{ route('lms.master.course_classes.destroy', $cc->id) }}" method="POST" onsubmit="return confirm('Hapus penugasan kelas ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold p-1 transition text-xs">Hapus</button>
                                    </form>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                <div class="max-w-md mx-auto space-y-2">
                                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                        {{ ($isTeacher && !$isAdminOrKurikulum) ? 'Belum Ada Kelas yang Anda Ampu' : 'Belum Ada Penugasan Guru ke Kelas' }}
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        {{ ($isTeacher && !$isAdminOrKurikulum) ? 'Anda belum memiliki alokasi rombel kelas. Klik tombol "Tugaskan Diri ke Kelas" di atas untuk menambahkan alokasi mengajar Anda.' : 'Belum ada guru yang dialokasikan ke kelas.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL: TAMBAH MATA PELAJARAN (PERMENDIKDASMEN 13/2025)     -->
<!-- ========================================================= -->
<div id="modal-add-subject" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tambah Mata Pelajaran Baru</h3>
                <p class="text-[11px] text-slate-500">Standar Kurikulum Nasional Permendikdasmen No. 13 Tahun 2025</p>
            </div>
            <button onclick="document.getElementById('modal-add-subject').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="mt-3 p-3 rounded-2xl bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200/60 dark:border-indigo-800/60 text-[11px] text-indigo-900 dark:text-indigo-200 leading-relaxed">
            <span class="font-bold">Ketentuan Permendikdasmen 13/2025:</span> Tidak ada lagi sekat peminatan kaku IPA/IPS. Kelompok mata pelajaran terdiri dari <strong>Mata Pelajaran Wajib (Umum)</strong>, <strong>Mata Pelajaran Pilihan</strong> (MIPA, IPS, Bahasa, Vokasi), dan <strong>Muatan Lokal</strong>.
        </div>

        <form action="{{ route('lms.master.subjects.store') }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Mapel *</label>
                <input type="text" name="code" required placeholder="Contoh: BIO-SMA atau MAT-SMA" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Mata Pelajaran *</label>
                <input type="text" name="name" required placeholder="Contoh: Biologi, Sosiologi, atau Informatika" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kelompok Mapel *</label>
                    <select name="group" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-medium">
                        <option value="Mata Pelajaran Wajib">Mata Pelajaran Wajib (Umum)</option>
                        <option value="Mata Pelajaran Pilihan">Mata Pelajaran Pilihan (MIPA, IPS, Bahasa, Vokasi)</option>
                        <option value="Muatan Lokal">Muatan Lokal (Potensi Daerah)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat / Fase *</label>
                    <select name="grade" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-medium">
                        <option value="Semua Tingkat">Semua Tingkat (Fase E & F)</option>
                        <option value="Fase E (Kelas X)">Fase E (Kelas X)</option>
                        <option value="Fase F (Kelas XI)">Fase F (Kelas XI)</option>
                        <option value="Fase F (Kelas XII)">Fase F (Kelas XII)</option>
                        <option value="Fase F (Kelas XI-XII)">Fase F (Kelas XI-XII)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) *</label>
                <input type="number" name="kkm" value="75" min="50" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-add-subject').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs">Simpan Mapel</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL: EDIT MATA PELAJARAN                                -->
<!-- ========================================================= -->
<div id="modal-edit-subject" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Edit Mata Pelajaran</h3>
                <p class="text-[11px] text-slate-500">Sesuaikan dengan Permendikdasmen No. 13 Tahun 2025</p>
            </div>
            <button onclick="document.getElementById('modal-edit-subject').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form id="form-edit-subject" method="POST" class="mt-4 space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Mapel *</label>
                <input type="text" id="edit_code" name="code" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Mata Pelajaran *</label>
                <input type="text" id="edit_name" name="name" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kelompok Mapel *</label>
                    <select id="edit_group" name="group" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-medium">
                        <option value="Mata Pelajaran Wajib">Mata Pelajaran Wajib (Umum)</option>
                        <option value="Mata Pelajaran Pilihan">Mata Pelajaran Pilihan (MIPA, IPS, Bahasa, Vokasi)</option>
                        <option value="Muatan Lokal">Muatan Lokal (Potensi Daerah)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat / Fase *</label>
                    <select id="edit_grade" name="grade" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-medium">
                        <option value="Semua Tingkat">Semua Tingkat (Fase E & F)</option>
                        <option value="Fase E (Kelas X)">Fase E (Kelas X)</option>
                        <option value="Fase F (Kelas XI)">Fase F (Kelas XI)</option>
                        <option value="Fase F (Kelas XII)">Fase F (Kelas XII)</option>
                        <option value="Fase F (Kelas XI-XII)">Fase F (Kelas XI-XII)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) *</label>
                <input type="number" id="edit_kkm" name="kkm" min="50" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-mono">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-edit-subject').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs">Perbarui Mapel</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL: PENUGASAN GURU KE KELAS (COURSE CLASSES)           -->
<!-- ========================================================= -->
<div id="modal-add-course" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                    {{ $isTeacher && !$isAdminOrKurikulum ? 'Tugaskan Diri ke Kelas Belajar' : 'Tugaskan Guru Pengampu ke Kelas' }}
                </h3>
                <p class="text-[11px] text-slate-500">
                    {{ $isTeacher && !$isAdminOrKurikulum ? 'Alokasikan mata pelajaran yang Anda ampu ke rombel siswa' : 'Alokasikan mapel ke rombel belajar siswa' }}
                </p>
            </div>
            <button onclick="document.getElementById('modal-add-course').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>
        <form action="{{ route('lms.master.course_classes.store') }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Mata Pelajaran *</label>
                <select id="modal_select_subject" name="subject_id" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-medium">
                    @foreach($allSubjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }}) — {{ $s->group }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Rombel / Kelas *</label>
                <select name="class_id" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-medium">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Guru Pengampu *</label>
                @if($isAdminOrKurikulum)
                    <select name="teacher_id" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-medium">
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ ($currentTeacher && $currentTeacher->id == $t->id) ? 'selected' : '' }}>
                                {{ $t->name }} (NIP: {{ $t->nip ?: '-' }})
                            </option>
                        @endforeach
                    </select>
                @elseif($currentTeacher)
                    <input type="hidden" name="teacher_id" value="{{ $currentTeacher->id }}">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800">
                        <div class="w-8 h-8 rounded-lg bg-cyan-600 text-white font-black text-xs flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($currentTeacher->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $currentTeacher->name }}</div>
                            <div class="text-[11px] text-cyan-700 dark:text-cyan-300 font-mono">NIP: {{ $currentTeacher->nip ?: '-' }} • Anda (Guru Pengampu)</div>
                        </div>
                    </div>
                @else
                    <select name="teacher_id" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-medium">
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} (NIP: {{ $t->nip ?: '-' }})</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Pelajaran *</label>
                <select name="academic_year_id" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $ay->is_active ? 'selected' : '' }}>{{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Jadwal Pembelajaran (Bisa Lebih dari 1 Hari / Minggu) *
                    </label>
                    <button type="button" onclick="addCourseScheduleRow()" class="text-[11px] font-bold text-cyan-600 dark:text-cyan-400 hover:underline flex items-center gap-1">
                        <span>+ Tambah Hari Lain</span>
                    </button>
                </div>
                <div id="course-schedule-rows-container" class="space-y-2">
                    <div class="course-schedule-row grid grid-cols-12 gap-2 items-center bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <div class="col-span-5">
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Hari</label>
                            <select name="schedule_days[]" required class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-medium">
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Jam Mulai</label>
                            <input type="time" name="schedule_starts[]" value="07:30" required class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-mono">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Jam Selesai</label>
                            <input type="time" name="schedule_ends[]" value="09:00" required class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-mono">
                        </div>
                        <div class="col-span-1 pt-3.5 text-center">
                            <button type="button" onclick="removeCourseScheduleRow(this)" class="text-slate-400 hover:text-rose-500 text-xs font-bold transition" title="Hapus jadwal hari ini">✕</button>
                        </div>
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Gunakan tombol <strong>+ Tambah Hari Lain</strong> jika mapel memiliki 2 atau lebih pertemuan dalam 1 minggu (contoh: Senin & Selasa).</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan / Deskripsi Kelas (Opsional)</label>
                <input type="text" name="description" placeholder="Contoh: Pendalaman materi dan persiapan asesmen sumatif" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-add-course').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-bold shadow-xs">
                    {{ $isTeacher && !$isAdminOrKurikulum ? 'Simpan Penugasan Kelas Saya' : 'Simpan Penugasan Guru' }}
                </button>
            </div>

        </form>
    </div>
</div>

<script>
function showSubjectTab(tab) {
    const myTbody = document.getElementById('tbody-my-subjects');
    const allTbody = document.getElementById('tbody-all-subjects');
    const myBtn = document.getElementById('tab-btn-my');
    const allBtn = document.getElementById('tab-btn-all');
    const countBadge = document.getElementById('badge-mapel-count');
    const headerTitle = document.getElementById('mapel-header-title');

    if (tab === 'my') {
        if (myTbody) myTbody.classList.remove('hidden');
        if (allTbody) allTbody.classList.add('hidden');
        if (myBtn) {
            myBtn.className = "px-3 py-1.5 rounded-lg font-bold transition bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs";
        }
        if (allBtn) {
            allBtn.className = "px-3 py-1.5 rounded-lg font-medium text-slate-500 hover:text-slate-900 dark:hover:text-white transition";
        }
        if (countBadge) countBadge.textContent = '{{ $mySubjects->count() }} Mapel';
        if (headerTitle) headerTitle.textContent = 'Mata Pelajaran yang Anda Ampu';
    } else {
        if (myTbody) myTbody.classList.add('hidden');
        if (allTbody) allTbody.classList.remove('hidden');
        if (allBtn) {
            allBtn.className = "px-3 py-1.5 rounded-lg font-bold transition bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs";
        }
        if (myBtn) {
            myBtn.className = "px-3 py-1.5 rounded-lg font-medium text-slate-500 hover:text-slate-900 dark:hover:text-white transition";
        }
        if (countBadge) countBadge.textContent = '{{ $allSubjects->count() }} Mapel';
        if (headerTitle) headerTitle.textContent = 'Semua Mata Pelajaran Kurikulum';
    }
}

function selectSubjectForCourse(subjectId) {
    const select = document.getElementById('modal_select_subject');
    if (select) {
        select.value = subjectId;
    }
    document.getElementById('modal-add-course').classList.remove('hidden');
}

function openEditSubjectModal(subject) {
    const form = document.getElementById('form-edit-subject');
    form.action = '/lms/master/subjects/' + subject.id;
    document.getElementById('edit_code').value = subject.code;
    document.getElementById('edit_name').value = subject.name;
    document.getElementById('edit_group').value = subject.group;
    document.getElementById('edit_grade').value = subject.grade;
    document.getElementById('edit_kkm').value = subject.kkm;
    document.getElementById('modal-edit-subject').classList.remove('hidden');
}

function addCourseScheduleRow() {
    const container = document.getElementById('course-schedule-rows-container');
    const newRow = document.createElement('div');
    newRow.className = 'course-schedule-row grid grid-cols-12 gap-2 items-center bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-2xl border border-slate-200 dark:border-slate-700';
    newRow.innerHTML = `
        <div class="col-span-5">
            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Hari</label>
            <select name="schedule_days[]" required class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-medium">
                <option value="Senin">Senin</option>
                <option value="Selasa" selected>Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
                <option value="Minggu">Minggu</option>
            </select>
        </div>
        <div class="col-span-3">
            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Jam Mulai</label>
            <input type="time" name="schedule_starts[]" value="09:45" required class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-mono">
        </div>
        <div class="col-span-3">
            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Jam Selesai</label>
            <input type="time" name="schedule_ends[]" value="10:20" required class="w-full text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2 py-1.5 text-slate-900 dark:text-white font-mono">
        </div>
        <div class="col-span-1 pt-3.5 text-center">
            <button type="button" onclick="removeCourseScheduleRow(this)" class="text-slate-400 hover:text-rose-500 text-xs font-bold transition" title="Hapus jadwal hari ini">✕</button>
        </div>
    `;
    container.appendChild(newRow);
}

function removeCourseScheduleRow(btn) {
    const rows = document.querySelectorAll('.course-schedule-row');
    if (rows.length > 1) {
        btn.closest('.course-schedule-row').remove();
    } else {
        alert('Minimal harus ada 1 jadwal hari pembelajaran.');
    }
}
</script>
@endsection
