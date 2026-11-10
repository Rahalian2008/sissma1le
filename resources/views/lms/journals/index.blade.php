@extends('layouts.app')

@section('title', 'Jurnal Mengajar Harian Guru - LMS SMAN 1 Lengkong')

@section('content')
<div class="space-y-6">
    @include('lms.partials.nav')

    <!-- Flash Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-900/40 p-6 sm:p-8 shadow-xl text-white">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>
        <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none hidden sm:block"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Agenda & Ketercapaian TP • SMAN 1 Lengkong
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    Jurnal Mengajar Harian Guru
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Dokumentasi keterlaksanaan KBM per tatap muka, ketercapaian Tujuan Pembelajaran (TP), rekap kehadiran siswa, catatan kendala kelas, serta supervisi akademik pimpinan sekolah.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($isTeacher || $isKurikulum)
                    <button onclick="openModal('modalCreateJournal')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Isi Jurnal Mengajar</span>
                    </button>
                    <a href="{{ route('lms.journals.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs sm:text-sm shadow-md transition border border-slate-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak Agenda Guru</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Quick Stats Matrix -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-6 pt-6 border-t border-slate-800/80">
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-slate-700/60">
                <div class="text-[11px] font-bold text-slate-400 uppercase">Total Pertemuan</div>
                <div class="text-xl sm:text-2xl font-black text-white mt-1">{{ $stats['total'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-emerald-600/40">
                <div class="text-[11px] font-bold text-emerald-400 uppercase">TP Tercapai</div>
                <div class="text-xl sm:text-2xl font-black text-emerald-300 mt-1">{{ $stats['tercapai'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-amber-600/40">
                <div class="text-[11px] font-bold text-amber-400 uppercase">Sebagian Tercapai</div>
                <div class="text-xl sm:text-2xl font-black text-amber-300 mt-1">{{ $stats['sebagian'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-rose-600/40">
                <div class="text-[11px] font-bold text-rose-400 uppercase">Belum Tercapai</div>
                <div class="text-xl sm:text-2xl font-black text-rose-300 mt-1">{{ $stats['belum'] }}</div>
            </div>
            <div class="p-3 rounded-2xl bg-slate-800/60 border border-indigo-600/40">
                <div class="text-[11px] font-bold text-indigo-400 uppercase">Terverifikasi Supervisi</div>
                <div class="text-xl sm:text-2xl font-black text-indigo-300 mt-1">{{ $stats['verified'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('lms.journals.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <select name="course_class_id" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                <option value="">-- Semua Kelas Pembelajaran --</option>
                @foreach($myClasses as $cls)
                    <option value="{{ $cls->id }}" {{ $classFilter == $cls->id ? 'selected' : '' }}>
                        {{ $cls->subject->name ?? '-' }} ({{ $cls->schoolClass->name ?? '-' }})
                    </option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                <option value="">-- Semua Status Ketercapaian --</option>
                <option value="TERCAPAI" {{ $statusFilter === 'TERCAPAI' ? 'selected' : '' }}>Tercapai</option>
                <option value="SEBAGIAN_TERCAPAI" {{ $statusFilter === 'SEBAGIAN_TERCAPAI' ? 'selected' : '' }}>Sebagian Tercapai</option>
                <option value="BELUM_TERCAPAI" {{ $statusFilter === 'BELUM_TERCAPAI' ? 'selected' : '' }}>Belum Tercapai</option>
            </select>
        </form>

        <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">
            Menampilkan {{ $journals->total() }} catatan jurnal
        </span>
    </div>

    <!-- Daftar Jurnal Mengajar -->
    @if($journals->isEmpty())
        <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
            <span class="text-4xl">📖</span>
            <h3 class="text-base font-bold text-slate-800 dark:text-white mt-3">Belum Ada Catatan Jurnal Mengajar</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                Guru dapat mengisi jurnal mengajar harian setelah sesi KBM selesai untuk mendokumentasikan ketercapaian Tujuan Pembelajaran dan dinamika kelas.
            </p>
            <button onclick="openModal('modalCreateJournal')" class="mt-4 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                + Isi Jurnal Sekarang
            </button>
        </div>
    @else
        <div class="space-y-4">
            @foreach($journals as $jrn)
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs space-y-4 hover:border-indigo-400 dark:hover:border-indigo-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-black text-sm flex items-center justify-center border border-indigo-200 dark:border-indigo-800">
                                #{{ $jrn->meeting_number }}
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $jrn->topic }}
                                </h3>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold">
                                    {{ $jrn->courseClass->subject->name ?? 'Mapel' }} • {{ $jrn->courseClass->schoolClass->name ?? 'Kelas' }}
                                    <span class="text-slate-400 font-normal">({{ $jrn->date->translatedFormat('l, d F Y') }} • {{ $jrn->time_slot }})</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Badge Status Ketercapaian TP -->
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider
                                {{ $jrn->learning_achievement_status === 'TERCAPAI' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800' : '' }}
                                {{ $jrn->learning_achievement_status === 'SEBAGIAN_TERCAPAI' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-300 dark:border-amber-800' : '' }}
                                {{ $jrn->learning_achievement_status === 'BELUM_TERCAPAI' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border border-rose-300 dark:border-rose-800' : '' }}">
                                {{ str_replace('_', ' ', $jrn->learning_achievement_status) }}
                            </span>

                            <!-- Status Supervisi Kurikulum -->
                            @if($jrn->is_verified_by_kurikulum)
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold border border-indigo-300 dark:border-indigo-800 flex items-center gap-1">
                                    <span>✓</span>
                                    <span>Terverifikasi Kurikulum</span>
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] font-semibold">
                                    Belum Disupervisi
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Kaitan TP & Modul Ajar -->
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        @if($jrn->learningObjective)
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 font-semibold">
                                <span>🎯 TP {{ $jrn->learningObjective->tp_code }}:</span>
                                <span>{{ $jrn->learningObjective->title }}</span>
                            </div>
                        @endif

                        @if($jrn->lessonPlan)
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                <span>📖 Modul: {{ $jrn->lessonPlan->title }}</span>
                            </div>
                        @endif

                        <!-- Rekap Presensi Siswa -->
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                            <span>👥 Kehadiran:</span>
                            <span class="font-bold">{{ $jrn->attendance_summary_text }}</span>
                        </div>
                    </div>

                    <!-- Uraian Aktivitas KBM -->
                    <div class="text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-slate-100 dark:border-slate-800 leading-relaxed whitespace-pre-line">
                        <div class="font-bold text-slate-900 dark:text-white mb-1">Aktivitas KBM Terlaksana:</div>
                        {{ $jrn->teaching_activities }}
                    </div>

                    <!-- Catatan Kendala & Tindak Lanjut -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        @if($jrn->classroom_dynamics_notes)
                            <div class="p-2.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 text-amber-900 dark:text-amber-300">
                                <span class="font-bold">⚠️ Dinamika & Hambatan Kelas:</span>
                                <p class="mt-0.5">{{ $jrn->classroom_dynamics_notes }}</p>
                            </div>
                        @endif

                        @if($jrn->follow_up_action)
                            <div class="p-2.5 rounded-xl bg-cyan-50/60 dark:bg-cyan-950/30 border border-cyan-200 dark:border-cyan-800/50 text-cyan-900 dark:text-cyan-300">
                                <span class="font-bold">💡 Rencana Tindak Lanjut:</span>
                                <p class="mt-0.5">{{ $jrn->follow_up_action }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Footer Toolbar & Supervisi -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="text-[11px] text-slate-500">
                            Guru Pengampu: <strong>{{ $jrn->teacher->user->name ?? $jrn->teacher->name ?? '-' }}</strong>
                            @if($jrn->verified_by)
                                • Diverifikasi oleh: <strong>{{ $jrn->verifier->name ?? 'Kurikulum' }}</strong> pada {{ $jrn->verified_at?->translatedFormat('d M Y H:i') }}
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Tombol Supervisi untuk Kurikulum / Admin -->
                            @if($isKurikulum && ! $jrn->is_verified_by_kurikulum)
                                <form method="POST" action="{{ route('lms.journals.verify', $jrn) }}">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Verifikasi catatan jurnal mengajar ini?')" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition shadow-xs">
                                        ✓ Verifikasi Supervisi
                                    </button>
                                </form>
                            @endif

                            <!-- Hapus (khusus pemilik / admin) -->
                            @if($isKurikulum || (Auth::user()->teacher && Auth::user()->teacher->id === $jrn->teacher_id))
                                <form method="POST" action="{{ route('lms.journals.destroy', $jrn) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus catatan jurnal ini?')" class="px-2.5 py-1.5 rounded-xl text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 font-semibold transition">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mt-4">
                {{ $journals->links() }}
            </div>
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- MODAL: ISI JURNAL MENGAJAR KBM            -->
<!-- ========================================== -->
<div id="modalCreateJournal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto my-8">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white">Isi Jurnal Mengajar Harian</h3>
                <p class="text-xs text-slate-500">Catat keterlaksanaan KBM, capaian TP, dan dinamika kelas.</p>
            </div>
            <button onclick="closeModal('modalCreateJournal')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('lms.journals.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kelas & Mata Pelajaran *</label>
                <select name="course_class_id" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                    <option value="">-- Pilih Kelas Pembelajaran --</option>
                    @foreach($myClasses as $cls)
                        <option value="{{ $cls->id }}" {{ $classFilter == $cls->id ? 'selected' : '' }}>
                            {{ $cls->subject->name ?? '-' }} ({{ $cls->schoolClass->name ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal KBM *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pertemuan Ke- *</label>
                    <input type="number" name="meeting_number" value="1" min="1" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alokasi / Jam Ke-</label>
                    <input type="text" name="time_slot" placeholder="07:30 - 09:00 WIB" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pokok Bahasan / Materi Pembelajaran *</label>
                <input type="text" name="topic" required placeholder="Contoh: Analisis Data dan Visualisasi Grafis" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
            </div>

            <!-- Uraian Aktivitas KBM -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Uraian Aktivitas Pembelajaran Terlaksana *</label>
                <textarea name="teaching_activities" rows="3" required placeholder="Apersepsi pemantik, penyampaian materi, diskusi kelompok kerja lembar tugas, dan kesimpulan pembelajaran..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
            </div>

            <!-- Rekap Presensi Siswa -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rekapitulasi Kehadiran Siswa Pertemuan Ini</label>
                <div class="grid grid-cols-4 gap-2">
                    <div>
                        <span class="text-[10px] text-emerald-600 font-bold block mb-0.5">Hadir</span>
                        <input type="number" name="hadir_count" value="32" min="0" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-center text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <span class="text-[10px] text-amber-600 font-bold block mb-0.5">Sakit</span>
                        <input type="number" name="sakit_count" value="0" min="0" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-center text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <span class="text-[10px] text-blue-600 font-bold block mb-0.5">Izin</span>
                        <input type="number" name="izin_count" value="0" min="0" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-center text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <span class="text-[10px] text-rose-600 font-bold block mb-0.5">Alfa</span>
                        <input type="number" name="alfa_count" value="0" min="0" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-center text-slate-900 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Status Ketercapaian TP -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Ketercapaian Tujuan Pembelajaran *</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 flex items-center gap-2 cursor-pointer font-bold text-xs">
                        <input type="radio" name="learning_achievement_status" value="TERCAPAI" checked class="text-emerald-600">
                        <span>Tercapai Penuh</span>
                    </label>
                    <label class="p-2.5 rounded-xl border border-amber-300 bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 flex items-center gap-2 cursor-pointer font-bold text-xs">
                        <input type="radio" name="learning_achievement_status" value="SEBAGIAN_TERCAPAI" class="text-amber-600">
                        <span>Sebagian Tercapai</span>
                    </label>
                    <label class="p-2.5 rounded-xl border border-rose-300 bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 flex items-center gap-2 cursor-pointer font-bold text-xs">
                        <input type="radio" name="learning_achievement_status" value="BELUM_TERCAPAI" class="text-rose-600">
                        <span>Belum Tercapai</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Hambatan / Dinamika Kelas</label>
                    <textarea name="classroom_dynamics_notes" rows="2" placeholder="Siswa antusias, beberapa membutuhkan penjelasan ulang konsep variabel..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rencana Tindak Lanjut</label>
                    <textarea name="follow_up_action" rows="2" placeholder="Praktik studi kasus lanjutan pada pertemuan berikutnya..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-slate-900 dark:text-white"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeModal('modalCreateJournal')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                    Simpan Jurnal KBM
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }
    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    }
</script>
@endsection

