@extends('layouts.app')

@section('title', $assignment->title . ' - LMS')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'tugas']) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Tugas {{ $courseClass->subject->name }}</span>
        </a>
        <div class="flex items-center gap-2">
            @if($canManage)
                <button type="button" onclick="openEditAssignmentModal()" class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Tugas</span>
                </button>
            @endif
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                Maks Skor: {{ $assignment->max_score }}
            </span>
        </div>
    </div>

    @if($canManage)
        @include('lms.security.unblock_panel', [
            'courseClass' => $courseClass,
            'scopedType' => 'assignment',
            'scopedId' => $assignment->id,
        ])
    @endif

    <!-- Assignment Info Card -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div class="space-y-1.5 max-w-3xl">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 uppercase">
                        {{ $courseClass->schoolClass->name }} • {{ $courseClass->subject->name }}
                    </span>
                    @if($assignment->start_time)
                        <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg border border-indigo-200 dark:border-indigo-800">
                            ⏳ Waktu Dimulai: {{ $assignment->start_time->translatedFormat('l, d F Y - H:i') }} WIB
                        </span>
                    @endif
                    @php $isLate = now()->greaterThan($assignment->due_date); @endphp
                    <span class="text-xs font-semibold {{ $isLate ? 'text-rose-500' : 'text-slate-500' }}">
                        Batas Waktu: {{ $assignment->due_date->translatedFormat('l, d F Y - H:i') }} WIB
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ $assignment->title }}</h1>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                @if($canManage)
                    <button type="button" onclick="openEditAssignmentModal()" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Edit Tugas</span>
                    </button>
                @endif
                <div class="text-right">
                    <div class="text-xs text-slate-400">Guru Pengampu</div>
                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $courseClass->teacher->name }}</div>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
            {!! \App\Models\LmsQuizQuestion::renderRichText($assignment->instructions ?? $assignment->description) !!}
        </div>

        <!-- Ketentuan Format Jawaban Siswa -->
        <div class="flex flex-wrap items-center gap-2.5 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-xs">
            <span class="font-bold text-slate-500">Ketentuan Format Pengumpulan:</span>
            @if($assignment->submission_type === 'text')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold bg-amber-500/15 text-amber-700 dark:text-amber-300">
                    <span>📝 Wajib Teks Langsung (Tidak Menerima Berkas)</span>
                </span>
            @elseif($assignment->submission_type === 'file')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold bg-indigo-500/15 text-indigo-700 dark:text-indigo-300">
                    <span>📁 Wajib Berkas: {{ $assignment->allowed_file_types_formatted }}</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300">
                    <span>📑 Teks & Berkas (Fleksibel)</span>
                </span>
                <span class="text-[11px] text-slate-500">
                    Format berkas yang diterima: <strong class="text-slate-700 dark:text-slate-300">{{ $assignment->allowed_file_types_formatted }}</strong>
                </span>
            @endif
        </div>

        @if($assignment->file_attachment)
            <div class="pt-2 flex items-center gap-3">
                <span class="text-xs font-bold text-slate-500">Berkas Soal dari Guru:</span>
                <a href="{{ asset('storage/' . $assignment->file_attachment) }}" target="_blank" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Unduh Soal / Lampiran</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Siswa Submission Section -->
    @if($isStudent)
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Status Pengumpulan Tugas Anda</h2>
                @if($mySubmission)
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $mySubmission->status === 'graded' ? 'bg-emerald-500/20 text-emerald-600' : 'bg-cyan-500/20 text-cyan-600' }}">
                        {{ $mySubmission->status === 'graded' ? 'Sudah Dinilai' : 'Terkumpul (Menunggu Koreksi)' }}
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-600">Belum Mengumpulkan</span>
                @endif
            </div>

            @if($mySubmission && $mySubmission->status === 'graded')
                <!-- Graded Result Card -->
                <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-transparent border border-emerald-500/30 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold uppercase text-emerald-600 dark:text-emerald-400">Hasil Penilaian Guru</span>
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                            {{ $mySubmission->score }} / {{ $assignment->max_score }}
                        </span>
                    </div>
                    @if($mySubmission->feedback)
                        <div class="text-xs text-slate-700 dark:text-slate-300 pt-2 border-t border-emerald-500/20">
                            <strong>Catatan Guru:</strong> "{{ $mySubmission->feedback }}"
                        </div>
                    @endif
                </div>
            @endif

            @if($mySubmission)
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Waktu Pengumpulan: <strong>{{ $mySubmission->submitted_at ? $mySubmission->submitted_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</strong></span>
                        @if($mySubmission->is_late)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-600">Terlambat</span>
                        @endif
                    </div>
                    @if($mySubmission->submission_text)
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                            <div class="flex items-center justify-between mb-1">
                                <strong>Jawaban Teks:</strong>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="previewSubmissionText('{{ addslashes(Auth::user()->name) }}', '{{ $mySubmission->submitted_at ? $mySubmission->submitted_at->translatedFormat('d F Y, H:i') : '-' }}', {{ json_encode($mySubmission->submission_text) }})" class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 hover:bg-amber-100 transition inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat</span>
                                    </button>
                                    <button type="button" onclick="downloadSubmissionText('{{ addslashes(Auth::user()->name) }}', {{ json_encode($mySubmission->submission_text) }})" class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-200 transition inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Download (.txt)</span>
                                    </button>
                                </div>
                            </div>
                            <p class="mt-1 whitespace-pre-line bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700/60">{{ $mySubmission->submission_text }}</p>
                        </div>
                    @endif
                    @if($mySubmission->file_path)
                        @php
                            $myExt = strtolower(pathinfo($mySubmission->file_path, PATHINFO_EXTENSION));
                            $myFileUrl = asset('storage/' . $mySubmission->file_path);
                            $myFileName = basename($mySubmission->file_path);
                        @endphp
                        <div class="pt-2 flex items-center justify-between gap-2 flex-wrap border-t border-slate-200 dark:border-slate-700">
                            <span class="text-slate-500 font-semibold">Berkas Unggahan: <strong class="text-slate-800 dark:text-slate-200">{{ $myFileName }}</strong></span>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="previewSubmissionFile('{{ $myFileUrl }}', '{{ addslashes($myFileName) }}', '{{ $myExt }}', '{{ addslashes(Auth::user()->name) }}')" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 transition inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Lihat</span>
                                </button>
                                <a href="{{ $myFileUrl }}" download="{{ $myFileName }}" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 transition inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Download</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                @if(isset($nextAssignment) && $nextAssignment)
                    <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200/60 dark:border-indigo-800/60 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0">🔓</span>
                            <div class="text-xs">
                                <span class="font-bold text-slate-900 dark:text-white">Tugas Berikutnya Telah Terbuka:</span>
                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ $nextAssignment->title }}</span>
                            </div>
                        </div>
                        <a href="{{ route('lms.assignments.show', ['courseClass' => $courseClass->id, 'assignment' => $nextAssignment->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition flex items-center gap-1.5 shrink-0">
                            <span>Buka Tugas Berikutnya &rarr;</span>
                        </a>
                    </div>
                @endif
            @endif

            <!-- Submission Form -->
            <form action="{{ route('lms.assignments.submit', ['courseClass' => $courseClass->id, 'assignment' => $assignment->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                @csrf

                @if($assignment->submission_type === 'text')
                    <!-- HANYA TEKS -->
                    <div class="p-3 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/60 text-xs text-amber-800 dark:text-amber-200 flex items-center gap-2">
                        <span>📝</span>
                        <span>Guru menetapkan bahwa pengumpulan tugas ini <strong>wajib ditulis langsung dalam bentuk teks</strong> (tanpa unggahan berkas).</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ $mySubmission ? 'Perbarui Jawaban Teks *' : 'Tuliskan Jawaban Tugas Anda (Wajib Teks) *' }}
                        </label>
                        <textarea name="submission_text" rows="5" required placeholder="Tuliskan jawaban tugas Anda secara lengkap di sini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-3 outline-none focus:border-indigo-500 text-slate-900 dark:text-white">{{ old('submission_text', $mySubmission?->submission_text) }}</textarea>
                    </div>
                @elseif($assignment->submission_type === 'file')
                    <!-- HANYA BERKAS -->
                    <div class="p-3 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200/60 dark:border-indigo-800/60 text-xs text-indigo-900 dark:text-indigo-200 flex items-center gap-2">
                        <span>📁</span>
                        <span>Guru mewajibkan pengumpulan berkas dalam format: <strong>{{ $assignment->allowed_file_types_formatted }}</strong> (Maksimal 20 MB).</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ ($mySubmission && $mySubmission->file_path) ? 'Unggah Ulang Berkas Tugas (Opsional jika tidak ingin mengubah)' : 'Unggah Berkas Tugas (Wajib) *' }}
                        </label>
                        <input type="file" name="submission_file" accept="{{ $assignment->accept_mimes }}" {{ (!$mySubmission || !$mySubmission->file_path) ? 'required' : '' }} class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">
                        <p class="text-[11px] text-slate-500 mt-1">Format yang diizinkan: {{ $assignment->allowed_file_types_formatted }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Catatan Tambahan untuk Guru (Opsional)
                        </label>
                        <textarea name="submission_text" rows="2" placeholder="Tuliskan catatan tambahan untuk guru jika ada..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-3 outline-none focus:border-indigo-500 text-slate-900 dark:text-white">{{ old('submission_text', $mySubmission?->submission_text) }}</textarea>
                    </div>
                @else
                    <!-- KEDUANYA (FLEKSIBEL) -->
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300 flex items-center gap-2">
                        <span>📑</span>
                        <span>Anda dapat mengumpulkan jawaban melalui teks langsung, mengunggah berkas (<strong>{{ $assignment->allowed_file_types_formatted }}</strong>), atau keduanya.</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ $mySubmission ? 'Perbarui Jawaban Teks' : 'Tuliskan Jawaban / Penjelasan Teks (Opsional jika melampirkan berkas)' }}
                        </label>
                        <textarea name="submission_text" rows="3" placeholder="Tuliskan jawaban Anda di sini..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-3 outline-none focus:border-indigo-500 text-slate-900 dark:text-white">{{ old('submission_text', $mySubmission?->submission_text) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ $mySubmission ? 'Unggah Ulang Berkas Tugas (Opsional)' : 'Unggah Berkas Tugas (' . $assignment->allowed_file_types_formatted . ' - Max 20MB)' }}
                        </label>
                        <input type="file" name="submission_file" accept="{{ $assignment->accept_mimes }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">
                        <p class="text-[11px] text-slate-500 mt-1">Format yang diizinkan: {{ $assignment->allowed_file_types_formatted }}</p>
                    </div>
                @endif

                <div class="flex items-center justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>{{ $mySubmission ? 'Kirim Pembaruan Tugas' : 'Kumpulkan Tugas Sekarang' }}</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Guru / Admin View: Submissions List & Grading Interface -->
    @if($canManage)
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Rekapitulasi Pengumpulan Tugas Siswa</h2>
                    <p class="text-xs text-slate-500">{{ $submissions->count() }} siswa telah mengumpulkan tugas</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('lms.assignments.export_excel', [$courseClass->id, $assignment->id]) }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh Excel</span>
                    </a>
                    <a href="{{ route('lms.assignments.print_pdf', [$courseClass->id, $assignment->id]) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak / PDF (KOP)</span>
                    </a>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400">
                        Total Siswa: {{ $courseClass->schoolClass->students->count() }}
                    </span>
                </div>
            </div>

            @if($submissions->isEmpty())
                <div class="py-8 text-center text-xs text-slate-400">
                    Belum ada siswa yang mengumpulkan tugas ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 uppercase font-extrabold text-slate-500">
                            <tr>
                                <th class="p-3">Nama Siswa</th>
                                <th class="p-3">Waktu Kirim</th>
                                <th class="p-3">Berkas / Jawaban</th>
                                <th class="p-3 text-center">Status</th>
                                <th class="p-3 text-center">Nilai (Maks {{ $assignment->max_score }})</th>
                                <th class="p-3 text-right">Aksi Penilaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($submissions as $sub)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">
                                        {{ $sub->student->name }}
                                        <div class="text-[10px] text-slate-400 font-normal">{{ $sub->student->nisn }}</div>
                                    </td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400">
                                        {{ $sub->submitted_at ? $sub->submitted_at->translatedFormat('d M Y, H:i') : '-' }}
                                        @if($sub->is_late)
                                            <span class="block text-[10px] text-rose-500 font-bold">Terlambat</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <div class="space-y-2">
                                            @if($sub->file_path)
                                                @php
                                                    $subExt = strtolower(pathinfo($sub->file_path, PATHINFO_EXTENSION));
                                                    $subFileUrl = asset('storage/' . $sub->file_path);
                                                    $subFileName = $sub->file_name ?? basename($sub->file_path);
                                                @endphp
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 uppercase">
                                                        .{{ $subExt }}
                                                    </span>
                                                    <!-- Tombol LIHAT BERKAS -->
                                                    <button type="button" 
                                                            onclick="previewSubmissionFile('{{ $subFileUrl }}', '{{ addslashes($subFileName) }}', '{{ $subExt }}', '{{ addslashes($sub->student->name) }}')" 
                                                            class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 border border-indigo-200 dark:border-indigo-800 inline-flex items-center gap-1 transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Lihat</span>
                                                    </button>
                                                    <!-- Tombol DOWNLOAD BERKAS -->
                                                    <a href="{{ $subFileUrl }}" download="{{ $subFileName }}" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900 border border-emerald-200 dark:border-emerald-800 inline-flex items-center gap-1 transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span>Download</span>
                                                    </a>
                                                </div>
                                            @endif
                                            @if($sub->submission_text)
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <!-- Tombol LIHAT JAWABAN TEKS -->
                                                    <button type="button" 
                                                            onclick="previewSubmissionText('{{ addslashes($sub->student->name) }}', '{{ $sub->submitted_at ? $sub->submitted_at->translatedFormat('d F Y, H:i') : '-' }}', {{ json_encode($sub->submission_text) }})"
                                                            class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900 border border-amber-200 dark:border-amber-800 inline-flex items-center gap-1 transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>Lihat Teks</span>
                                                    </button>
                                                    <!-- Tombol DOWNLOAD TEKS (.txt) -->
                                                    <button type="button" 
                                                            onclick="downloadSubmissionText('{{ addslashes($sub->student->name) }}', {{ json_encode($sub->submission_text) }})"
                                                            class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 inline-flex items-center gap-1 transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span>Download (.txt)</span>
                                                    </button>
                                                </div>
                                                <p class="text-[11px] text-slate-500 line-clamp-1 italic">
                                                    "{{ Str::limit(strip_tags($sub->submission_text), 70) }}"
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $sub->status === 'graded' ? 'bg-emerald-500/15 text-emerald-600' : 'bg-amber-500/15 text-amber-600' }}">
                                            {{ $sub->status === 'graded' ? 'Dinilai' : 'Menunggu' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center font-bold text-sm text-slate-800 dark:text-slate-100">
                                        {{ $sub->score !== null ? $sub->score : '-' }}
                                    </td>
                                    <td class="p-3 text-right">
                                        <!-- Inline Grade Form -->
                                        <form action="{{ route('lms.assignments.grade', ['courseClass' => $courseClass->id, 'assignment' => $assignment->id, 'submission' => $sub->id]) }}" method="POST" class="inline-flex items-center gap-1.5">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="score" value="{{ $sub->score }}" min="0" max="{{ $assignment->max_score }}" required placeholder="Skor" class="w-16 px-2 py-1 text-xs text-center bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                                            <input type="text" name="feedback" value="{{ $sub->feedback }}" placeholder="Catatan" class="w-28 px-2 py-1 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                                            <button type="submit" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition">
                                                Simpan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    @if($canManage)
        <!-- ======================================================== -->
        <!-- MODAL: EDIT TUGAS & PR -->
        <!-- ======================================================== -->
        <div id="modal-edit-assignment" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-xl w-full max-h-[92vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-indigo-500/15 text-indigo-600 dark:text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </span>
                            <span>Edit Tugas & PR</span>
                        </h3>
                        <p class="text-xs text-slate-500">Perbarui judul, instruksi, tenggat pengumpulan, dan berkas lampiran</p>
                    </div>
                    <button type="button" onclick="closeEditAssignmentModal()" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <form action="{{ route('lms.assignments.update', ['courseClass' => $courseClass->id, 'assignment' => $assignment->id]) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3.5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Tugas *</label>
                        <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Topik / Ruang Lingkup Materi *</label>
                        <input type="text" name="topic" value="{{ old('topic', $assignment->topic) }}" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Instruksi & Soal Tugas *</label>
                        <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden focus-within:border-indigo-500">
                            @include('lms.partials.rich_editor_toolbar', ['targetId' => 'edit_assignment_instructions'])
                            <textarea id="edit_assignment_instructions" name="instructions" rows="4" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white outline-none">{{ old('instructions', $assignment->instructions ?? $assignment->description) }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Waktu Dimulai (Dapat Dibuka Siswa)</label>
                            <input type="datetime-local" name="start_time" value="{{ old('start_time', $assignment->start_time ? $assignment->start_time->format('Y-m-d\TH:i') : '') }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                            <p class="text-[10px] text-slate-400 mt-0.5">Kosongkan jika langsung dapat dibuka.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tenggat Waktu Pengumpulan (Deadline) *</label>
                            <input type="datetime-local" name="due_date" value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                            <p class="text-[10px] text-slate-400 mt-0.5">Tugas tetap tampil & dapat diakses walau waktu lewat.</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nilai Maksimal (Bobot) *</label>
                        <input type="number" name="max_score" value="{{ old('max_score', $assignment->max_score) }}" min="10" max="100" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Metode Pengumpulan Jawaban</label>
                        <select name="submission_type" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                            <option value="both" {{ old('submission_type', $assignment->submission_type) === 'both' ? 'selected' : '' }}>Unggah Berkas atau Teks Bebas (Keduanya)</option>
                            <option value="file" {{ old('submission_type', $assignment->submission_type) === 'file' ? 'selected' : '' }}>Wajib Unggah Berkas (PDF, DOC, Gambar)</option>
                            <option value="text" {{ old('submission_type', $assignment->submission_type) === 'text' ? 'selected' : '' }}>Hanya Teks Online Langsung</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Berkas Soal / Rubrik Lampiran (Opsional)</label>
                        @if($assignment->attachment_name || $assignment->file_attachment)
                            <div class="mb-2 p-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                                <span class="text-slate-600 dark:text-slate-400 truncate">Berkas saat ini: <strong>{{ $assignment->attachment_name ?? basename($assignment->file_attachment) }}</strong></span>
                            </div>
                        @endif
                        <input type="file" name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png" class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 file:cursor-pointer border border-slate-300 dark:border-slate-700 rounded-xl p-2 bg-slate-50 dark:bg-slate-800">
                        <p class="mt-1 text-[11px] text-slate-500">Biarkan kosong jika tidak ingin mengubah berkas lampiran soal sebelumnya.</p>
                    </div>

                    <div class="space-y-2 pt-1">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="edit_assignment_allow_late" name="allow_late" value="1" {{ old('allow_late', $assignment->allow_late) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="edit_assignment_allow_late" class="text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Izinkan pengumpulan terlambat melewati tenggat</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="edit_assignment_is_published" name="is_published" value="1" {{ old('is_published', $assignment->is_published) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="edit_assignment_is_published" class="text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">Publikasikan tugas ke siswa kelas</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" onclick="closeEditAssignmentModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">Simpan Perubahan Tugas</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditAssignmentModal() {
                const modal = document.getElementById('modal-edit-assignment');
                if (modal) {
                    modal.classList.remove('hidden');
                    if (window.RichEditor && typeof window.RichEditor.initVisualCanvas === 'function') {
                        window.RichEditor.initVisualCanvas('edit_assignment_instructions');
                        const visual = document.getElementById('rich-visual-edit-assignment-instructions');
                        const ta = document.getElementById('edit_assignment_instructions');
                        if (visual && ta) {
                            visual.innerHTML = window.RichEditor.markdownToVisualHtml(ta.value) || '<p><br></p>';
                        }
                    }
                }
            }
            function closeEditAssignmentModal() {
                const modal = document.getElementById('modal-edit-assignment');
                if (modal) modal.classList.add('hidden');
            }
        </script>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL: PREVIEW BERKAS TUGAS (LIHAT BERKAS) -->
    <!-- ======================================================== -->
    <div id="modal-preview-submission-file" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-950/75 backdrop-blur-xs hidden">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800 shrink-0">
                <div>
                    <h3 id="preview_file_title" class="text-sm font-black text-slate-900 dark:text-white truncate max-w-md">Preview Berkas Tugas</h3>
                    <p id="preview_file_student" class="text-xs text-slate-500">Siswa: -</p>
                </div>
                <div class="flex items-center gap-2">
                    <a id="preview_file_open_new_tab" href="#" target="_blank" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 inline-flex items-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Tab Baru</span>
                    </a>
                    <a id="preview_file_download_link" href="#" download class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white inline-flex items-center gap-1 transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Download</span>
                    </a>
                    <button type="button" onclick="closePreviewSubmissionFile()" class="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">✕</button>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-3 flex items-center justify-center min-h-[350px]">
                <img id="preview_img_target" src="" class="hidden max-h-[70vh] max-w-full object-contain rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm" alt="Preview Gambar">
                <iframe id="preview_pdf_target" src="" class="hidden w-full h-[70vh] rounded-xl border border-slate-200 dark:border-slate-800"></iframe>
                <div id="preview_generic_target" class="hidden text-center p-8 space-y-3">
                    <span class="text-4xl">📁</span>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Format file ini tidak mendukung preview langsung peramban web.</p>
                    <p class="text-xs text-slate-500">Silakan klik tombol <strong>Download</strong> untuk membuka berkas dengan aplikasi di perangkat Anda.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: PREVIEW JAWABAN TEKS (LIHAT TEKS) -->
    <!-- ======================================================== -->
    <div id="modal-preview-submission-text" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-950/75 backdrop-blur-xs hidden">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 max-w-2xl w-full max-h-[92vh] flex flex-col shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800 shrink-0">
                <div>
                    <h3 id="preview_text_student" class="text-sm font-black text-slate-900 dark:text-white">Jawaban Siswa</h3>
                    <p id="preview_text_submitted_at" class="text-xs text-slate-500">Dikumpulkan: -</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="copyPreviewText()" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 inline-flex items-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span id="copy_btn_label">Salin Teks</span>
                    </button>
                    <button type="button" id="preview_text_download_btn" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white inline-flex items-center gap-1 transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Download (.txt)</span>
                    </button>
                    <button type="button" onclick="closePreviewSubmissionText()" class="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">✕</button>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/60 mt-3">
                <div id="preview_text_content" class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-wrap font-sans"></div>
            </div>
        </div>
    </div>

    <script>
        let currentPreviewRawText = '';
        let currentPreviewStudentName = '';

        function previewSubmissionFile(url, fileName, ext, studentName) {
            const modal = document.getElementById('modal-preview-submission-file');
            const titleEl = document.getElementById('preview_file_title');
            const studentEl = document.getElementById('preview_file_student');
            const openNewTab = document.getElementById('preview_file_open_new_tab');
            const downloadLink = document.getElementById('preview_file_download_link');
            const imgEl = document.getElementById('preview_img_target');
            const pdfEl = document.getElementById('preview_pdf_target');
            const genericEl = document.getElementById('preview_generic_target');

            if (!modal) return;

            titleEl.textContent = fileName || 'Berkas Tugas';
            studentEl.textContent = 'Siswa: ' + (studentName || '-');
            openNewTab.href = url;
            downloadLink.href = url;
            downloadLink.setAttribute('download', fileName || 'berkas_tugas');

            imgEl.classList.add('hidden');
            pdfEl.classList.add('hidden');
            genericEl.classList.add('hidden');
            imgEl.src = '';
            pdfEl.src = '';

            const lowerExt = (ext || '').toLowerCase();
            if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].includes(lowerExt)) {
                imgEl.src = url;
                imgEl.classList.remove('hidden');
            } else if (lowerExt === 'pdf') {
                pdfEl.src = url;
                pdfEl.classList.remove('hidden');
            } else {
                genericEl.classList.remove('hidden');
            }

            modal.classList.remove('hidden');
        }

        function closePreviewSubmissionFile() {
            const modal = document.getElementById('modal-preview-submission-file');
            if (modal) modal.classList.add('hidden');
            const pdfEl = document.getElementById('preview_pdf_target');
            if (pdfEl) pdfEl.src = '';
        }

        function previewSubmissionText(studentName, submittedAt, text) {
            const modal = document.getElementById('modal-preview-submission-text');
            const studentEl = document.getElementById('preview_text_student');
            const dateEl = document.getElementById('preview_text_submitted_at');
            const contentEl = document.getElementById('preview_text_content');
            const downloadBtn = document.getElementById('preview_text_download_btn');

            if (!modal) return;

            currentPreviewRawText = text || '';
            currentPreviewStudentName = studentName || 'Siswa';

            studentEl.textContent = 'Jawaban Siswa: ' + currentPreviewStudentName;
            dateEl.textContent = 'Waktu Pengumpulan: ' + (submittedAt || '-');
            contentEl.textContent = currentPreviewRawText;

            downloadBtn.onclick = function() {
                downloadSubmissionText(currentPreviewStudentName, currentPreviewRawText);
            };

            modal.classList.remove('hidden');
        }

        function closePreviewSubmissionText() {
            const modal = document.getElementById('modal-preview-submission-text');
            if (modal) modal.classList.add('hidden');
        }

        function copyPreviewText() {
            if (!currentPreviewRawText) return;
            navigator.clipboard.writeText(currentPreviewRawText).then(() => {
                const lbl = document.getElementById('copy_btn_label');
                if (lbl) {
                    const original = lbl.textContent;
                    lbl.textContent = 'Tersalin!';
                    setTimeout(() => { lbl.textContent = original; }, 2000);
                }
            });
        }

        function downloadSubmissionText(studentName, text) {
            if (!text) {
                alert('Tidak ada teks untuk diunduh.');
                return;
            }
            const cleanName = (studentName || 'siswa').replace(/[^a-zA-Z0-9_\-]/g, '_');
            const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `Jawaban_Tugas_${cleanName}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
</div>

@include('lms.partials.rich_editor_modals')
@include('lms.security.tracker_script', [
    'courseClass' => $courseClass,
    'blockableType' => 'assignment',
    'blockableId' => $assignment->id,
    'itemTitle' => $assignment->title,
    'mySubmission' => $mySubmission,
])
@endsection
