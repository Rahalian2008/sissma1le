@extends('layouts.app')

@section('title', 'Buat Jadwal Ujian Baru - CBT SIS-SMA1LE')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('cbt.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                ⬅
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    Buat Jadwal & Paket Ujian Baru
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Konfigurasi parameter ujian berbasis komputer (CBT), proteksi anti-cheat, dan lock lokasi.
                </p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form action="{{ route('cbt.exams.store') }}" method="POST" class="space-y-6" x-data="{ enableGeofence: true, enableSecurity: true }">
            @csrf

            <!-- SECTION 1: IDENTITAS UTAMA UJIAN -->
            <div class="space-y-4">
                <div class="text-xs font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <span>📌</span>
                    <span>1. Identitas & Mata Pelajaran Ujian</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Judul / Nama Ujian <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Penilaian Akhir Semester (PAS) Ganjil Matematika Kelas X" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('title') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Kode Ujian (Unik) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="exam_code" value="{{ old('exam_code', $suggestedCode) }}" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500 uppercase">
                        @error('exam_code') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Tipe Asesmen / Ujian <span class="text-rose-500">*</span>
                        </label>
                        <select name="exam_type" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="UH">Ulangan Harian (UH / Formatif)</option>
                            <option value="PTS">Penilaian Tengah Semester (PTS / Sumatif Tengah)</option>
                            <option value="PAS" selected>Penilaian Akhir Semester (PAS / Sumatif Akhir)</option>
                            <option value="PAT">Penilaian Akhir Tahun (PAT)</option>
                            <option value="US">Ujian Sekolah (US)</option>
                            <option value="ANBK">Simulasi ANBK / Asesmen Nasional</option>
                            <option value="DIAGNOSTIK">Asesmen Diagnostik Awal Pembelajaran</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Mata Pelajaran <span class="text-rose-500">*</span>
                        </label>
                        <select name="subject_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Mata Pelajaran...</option>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->id }}" {{ old('subject_id') == $subj->id ? 'selected' : '' }}>
                                    {{ $subj->name }} ({{ $subj->code ?? 'MAPEL' }})
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Guru Pengampu / Penyusun
                        </label>
                        <select name="teacher_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Guru...</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ (old('teacher_id') == $t->id || (Auth::user()->teacher?->id == $t->id)) ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ $t->nip ?? 'NIP -' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- PILIH KELAS PESERTA -->
                <div class="space-y-2 pt-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                        Target Rombel / Kelas Peserta Ujian <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 max-h-48 overflow-y-auto">
                        @foreach($classes as $c)
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-indigo-500 cursor-pointer transition select-none">
                                <input type="checkbox" name="class_ids[]" value="{{ $c->id }}" class="rounded text-indigo-600 focus:ring-0">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $c->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('class_ids') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- SECTION 2: WAKTU, TOKEN & ATURAN -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="text-xs font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <span>⏱️</span>
                    <span>2. Pengaturan Waktu, Durasi & Token Akses</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Durasi Pengerjaan (Menit) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 90) }}" required min="5" max="360" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Token Masuk Ujian
                        </label>
                        <input type="text" name="token" value="{{ old('token', $suggestedToken) }}" placeholder="Contoh: SMA1LE" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-mono font-black text-amber-600 dark:text-amber-400 outline-none focus:ring-2 focus:ring-indigo-500 uppercase tracking-widest">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            KKM / Nilai Minimal Lulus
                        </label>
                        <input type="number" step="0.5" name="pass_percentage" value="{{ old('pass_percentage', 75.0) }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Jadwal Mulai Ujian
                        </label>
                        <input type="datetime-local" name="start_time" value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Batas Akhir Ujian Ditutup
                        </label>
                        <input type="datetime-local" name="end_time" value="{{ old('end_time', now()->addDays(2)->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Checkboxes Acak & Hasil -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    <label class="flex items-center gap-2 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer select-none">
                        <input type="checkbox" name="randomize_questions" value="1" {{ old('randomize_questions') ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-0">
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            🔀 Acak Urutan Soal
                        </div>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer select-none">
                        <input type="checkbox" name="randomize_options" value="1" {{ old('randomize_options') ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-0">
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            🔀 Acak Opsi Pilihan Ganda
                        </div>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer select-none">
                        <input type="checkbox" name="show_result_to_student" value="1" {{ old('show_result_to_student') ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-0">
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            👁️ Tampilkan Nilai ke Siswa
                        </div>
                    </label>
                </div>
            </div>

            <!-- SECTION 3: KEAMANAN ANTI-CHEAT & LOCKOUT -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="text-xs font-black uppercase tracking-wider text-rose-600 dark:text-rose-400 flex items-center gap-1.5 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <span>🛡️</span>
                    <span>3. Proteksi Keamanan & Anti-Cheat (Auto-Block)</span>
                </div>

                <div class="p-4 rounded-2xl bg-rose-500/5 border border-rose-500/20 space-y-4">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="enable_security_block" value="1" x-model="enableSecurity" class="rounded text-rose-600 focus:ring-0 mt-0.5">
                        <div>
                            <div class="text-xs font-black text-rose-900 dark:text-rose-300">
                                Aktifkan Auto-Block (Deteksi Buka Tab Baru / Minimize / Keluar Layar Penuh)
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                Jika diaktifkan, sistem akan otomatis mencatat log pelanggaran saat siswa beralih tab, meminimalkan aplikasi, atau keluar dari fullscreen. Jika batas toleransi tercapai, lembar ujian siswa akan langsung diblokir dan hanya dapat dibuka kembali oleh pengawas.
                            </p>
                        </div>
                    </label>

                    <div x-show="enableSecurity" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-rose-500/20">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Batas Toleransi Pelanggaran (Sebelum Diblokir)
                            </label>
                            <input type="number" name="max_violations" value="{{ old('max_violations', 3) }}" min="1" max="10" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-rose-500">
                            <span class="text-[10px] text-slate-400">Contoh: 3x (pelanggaran ke-3 langsung auto-block).</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: LOCK LOKASI GEOFENCE (RADIUS SEKOLAH) -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="text-xs font-black uppercase tracking-wider text-teal-600 dark:text-teal-400 flex items-center gap-1.5 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <span>📍</span>
                    <span>4. Kunci Lokasi Geofence (Presensi Lokasi Sekolah)</span>
                </div>

                <div class="p-4 rounded-2xl bg-teal-500/5 border border-teal-500/20 space-y-4">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="enable_geofence" value="1" x-model="enableGeofence" class="rounded text-teal-600 focus:ring-0 mt-0.5">
                        <div>
                            <div class="text-xs font-black text-teal-900 dark:text-teal-300">
                                Aktifkan Lock Lokasi GPS (Sama Seperti Presensi Utama Sekolah)
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                Siswa hanya diizinkan memulai ujian jika berada di dalam koordinat dan radius sekolah (berdasarkan GPS HP / Laptop siswa).
                            </p>
                        </div>
                    </label>

                    <div x-show="enableGeofence" x-transition class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-teal-500/20">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Radius Maksimal (Meter)
                            </label>
                            <input type="number" name="geofence_radius" value="{{ old('geofence_radius', 100) }}" min="10" max="5000" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-teal-500">
                            <span class="text-[10px] text-slate-400">Default: 100 meter dari koordinat sekolah.</span>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Latitude Kustom (Opsional)
                            </label>
                            <input type="text" name="geofence_latitude" value="{{ old('geofence_latitude') }}" placeholder="Kosongkan untuk pakai lokasi sekolah" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-teal-500 font-mono">
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Longitude Kustom (Opsional)
                            </label>
                            <input type="text" name="geofence_longitude" value="{{ old('geofence_longitude') }}" placeholder="Kosongkan untuk pakai lokasi sekolah" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-teal-500 font-mono">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: PETUNJUK PENGERJAAN -->
            <div class="space-y-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                    Petunjuk Pengerjaan / Deskripsi Tambahan
                </label>
                <textarea name="description" rows="3" placeholder="Tuliskan petunjuk pengerjaan bagi siswa..." class="w-full p-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('cbt.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-md shadow-indigo-600/30 transition cursor-pointer">
                    Simpan & Lanjutkan Buat Soal ➡
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
