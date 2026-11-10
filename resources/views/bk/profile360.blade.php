@extends('layouts.app')

@section('title', 'Profil 360° Siswa - ' . $student->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-white font-bold">Profil 360° Siswa</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.counseling.create', ['student_id' => $student->id]) }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition">
                + Tambah Sesi Konseling
            </a>
            <a href="{{ route('bk.parent-call.create', ['student_id' => $student->id]) }}" class="px-3 py-1.5 rounded-xl bg-amber-600 text-white font-bold text-xs hover:bg-amber-700 transition">
                + Panggil Orang Tua
            </a>
        </div>
    </div>

    <!-- Student Header Card -->
    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-black text-xl flex items-center justify-center shadow-md shadow-emerald-500/20 shrink-0">
                {{ $student->initials ?: substr($student->name, 0, 2) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ $student->name }}</h1>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $student->gender === 'L' ? 'bg-sky-500/10 text-sky-600' : 'bg-pink-500/10 text-pink-600' }}">
                        {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Kelas: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $student->schoolClass?->name ?: '-' }}</span> &bull;
                    NISN: <span class="font-mono">{{ $student->nisn ?: '-' }}</span> &bull;
                    NIS: <span class="font-mono">{{ $student->nis ?: '-' }}</span>
                </p>
                <p class="text-xs text-slate-400 mt-0.5">
                    Wali Kelas: <span class="text-slate-600 dark:text-slate-300 font-medium">{{ $student->schoolClass?->homeroomTeacher?->name ?: 'Belum ditentukan' }}</span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full md:w-auto">
            <div class="p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-center">
                <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 block uppercase">Poin Pelanggaran</span>
                <span class="text-xl font-black text-rose-700 dark:text-rose-300">{{ $totalViolationPoints }}</span>
            </div>
            <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-center">
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 block uppercase">Poin Prestasi</span>
                <span class="text-xl font-black text-emerald-700 dark:text-emerald-300">{{ $totalAchievementPoints }}</span>
            </div>
            <div class="p-3 rounded-2xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/50 text-center">
                <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 block uppercase">Alpa Absensi</span>
                <span class="text-xl font-black text-blue-700 dark:text-blue-300">{{ $totalAttendances->alpa ?? 0 }} hari</span>
            </div>
            <div class="p-3 rounded-2xl bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-900/50 text-center">
                <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 block uppercase">Sesi Konseling</span>
                <span class="text-xl font-black text-purple-700 dark:text-purple-300">{{ $student->counselingSessions->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Hasil Asesmen Psikotes Siswa (VAK & Holland RIASEC) -->
    <div class="p-6 rounded-3xl bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-950 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-white/10">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-pink-500/20 text-pink-300 border border-pink-500/30">
                    Hasil Tes Psikotes Terverifikasi
                </span>
                <h2 class="text-lg font-black mt-1">Profil Karakteristik & Minat Bakat Siswa</h2>
            </div>
            <a href="{{ route('bk.psychological.index', ['search' => $student->name]) }}" class="text-xs font-bold text-sky-400 hover:underline">
                Buka Seluruh Hasil Tes &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Gaya Belajar VAK -->
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                <h3 class="text-xs font-bold text-sky-300 uppercase tracking-wider mb-2">1. Gaya Belajar Siswa (Modalitas Sensorik)</h3>
                @if($vakResult)
                    <div class="text-base font-black text-white">{{ $vakResult->profile_label }}</div>
                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">{{ $vakResult->analysis_summary }}</p>

                    <div class="mt-4 pt-3 border-t border-white/10">
                        <span class="text-[11px] font-bold text-sky-200 block mb-1">Rekomendasi Strategi Belajar:</span>
                        <div class="text-xs text-slate-300 whitespace-pre-line leading-relaxed">{{ $vakResult->study_recommendations }}</div>
                    </div>
                @else
                    <p class="text-xs text-slate-400 py-4">Siswa belum mengerjakan Tes Gaya Belajar VAK.</p>
                    <a href="{{ route('bk.psychological.take', 'vak') }}" class="text-xs font-bold text-sky-400 hover:underline">Kerjakan Tes Gaya Belajar &rarr;</a>
                @endif
            </div>

            <!-- Minat Karier RIASEC -->
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                <h3 class="text-xs font-bold text-emerald-300 uppercase tracking-wider mb-2">2. Minat Karier & Peminatan Jurusan (Holland RIASEC)</h3>
                @if($riasecResult)
                    <div class="text-base font-black text-white">{{ $riasecResult->profile_label }}</div>
                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">{{ $riasecResult->analysis_summary }}</p>

                    @if(!empty($riasecResult->career_recommendations))
                    <div class="mt-4 pt-3 border-t border-white/10">
                        <span class="text-[11px] font-bold text-emerald-200 block mb-2">Rekomendasi Program Studi Kuliah & Karier:</span>
                        <div class="space-y-1.5">
                            @foreach(array_slice($riasecResult->career_recommendations, 0, 3) as $c)
                            <div class="p-2 rounded-xl bg-white/5 text-xs">
                                <span class="font-bold text-white">{{ $c['major'] }}</span>
                                <span class="text-emerald-400 block text-[10px]">Prospek: {{ $c['prospect'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @else
                    <p class="text-xs text-slate-400 py-4">Siswa belum mengerjakan Tes Minat Bakat Karier Holland RIASEC.</p>
                    <a href="{{ route('bk.psychological.take', 'riasec') }}" class="text-xs font-bold text-emerald-400 hover:underline">Kerjakan Tes Minat Bakat &rarr;</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Riwayat Sesi Konseling & Kunjungan Rumah -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Riwayat Sesi Konseling -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white">Riwayat Sesi Konseling</h3>
                <a href="{{ route('bk.counseling.create', ['student_id' => $student->id]) }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">+ Catat Baru</a>
            </div>
            <div class="space-y-3">
                @forelse($student->counselingSessions as $cs)
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $cs->field_badge['class'] }}">
                            {{ $cs->field_badge['label'] }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ \Carbon\Carbon::parse($cs->date)->translatedFormat('d M Y') }}</span>
                    </div>
                    <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1.5">{{ $cs->topic }}</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ $cs->problem_description }}</p>
                    @if($cs->agreement_result)
                        <div class="mt-2 text-[11px] text-emerald-700 dark:text-emerald-400 font-medium bg-emerald-500/10 p-2 rounded-xl">
                            <strong>Hasil/Kesepakatan:</strong> {{ $cs->agreement_result }}
                        </div>
                    @endif
                    <div class="mt-2 text-right">
                        <a href="{{ route('bk.counseling.show', $cs) }}" class="text-[11px] font-bold text-teal-600 hover:underline">Detail Sesi &rarr;</a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-6">Belum ada riwayat sesi konseling untuk siswa ini.</p>
                @endforelse
            </div>
        </div>

        <!-- Riwayat Home Visit & Panggilan Ortu -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white">Kunjungan Rumah & Panggilan Ortu</h3>
                <div class="flex gap-2">
                    <a href="{{ route('bk.home-visit.create', ['student_id' => $student->id]) }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">+ Home Visit</a>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($student->homeVisits as $hv)
                <div class="p-3.5 rounded-2xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400">
                            Home Visit (Kunjungan Rumah)
                        </span>
                        <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($hv->visit_date)->translatedFormat('d M Y') }}</span>
                    </div>
                    <p class="text-xs font-bold text-slate-800 dark:text-white mt-1">Ditemui: {{ $hv->parent_name ?: 'Orang Tua' }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">{{ $hv->case_background }}</p>
                    <div class="mt-2 text-right">
                        <a href="{{ route('bk.home-visit.show', $hv) }}" class="text-[11px] font-bold text-blue-600 hover:underline">Detail & Cetak &rarr;</a>
                    </div>
                </div>
                @endforeach

                @foreach($student->parentCalls as $pc)
                <div class="p-3.5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/40">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400">
                            Surat Panggilan Orang Tua
                        </span>
                        <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($pc->call_date)->translatedFormat('d M Y') }}</span>
                    </div>
                    <p class="text-xs font-bold text-slate-800 dark:text-white mt-1">No: {{ $pc->letter_number ?: $pc->call_code }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">Alasan: {{ $pc->reason }}</p>
                    <div class="mt-2 text-right space-x-2">
                        <a href="{{ route('bk.parent-call.print-letter', $pc) }}" target="_blank" class="text-[11px] font-bold text-amber-600 hover:underline">Cetak Surat</a>
                        <a href="{{ route('bk.parent-call.show', $pc) }}" class="text-[11px] font-bold text-slate-600 hover:underline">Detail &rarr;</a>
                    </div>
                </div>
                @endforeach

                @if($student->homeVisits->isEmpty() && $student->parentCalls->isEmpty())
                <p class="text-xs text-slate-400 text-center py-6">Belum ada catatan home visit atau surat panggilan orang tua.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

