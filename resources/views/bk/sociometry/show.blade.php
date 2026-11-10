@extends('layouts.app')

@section('title', 'Matriks Sosiogram - ' . $sociometry->title)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
            <span>/</span>
            <a href="{{ route('bk.sociometry.index') }}" class="hover:text-emerald-600 font-medium">Sosiometri</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-white font-bold">{{ $sociometry->schoolClass?->name }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.sociometry.print', $sociometry) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5 border border-slate-300 dark:border-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                🖨️ Cetak Dokumen
            </a>
            <button onclick="document.getElementById('form-input-respon').classList.toggle('hidden')" class="px-3 py-1.5 rounded-xl bg-purple-600 text-white font-bold text-xs hover:bg-purple-700 transition">
                + Input Respon Siswa
            </button>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-purple-700 to-indigo-800 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-purple-100 border border-white/20">
                    Sosiogram Kelas
                </span>
                <h1 class="text-xl font-black mt-1">{{ $sociometry->title }} - Kelas {{ $sociometry->schoolClass?->name }}</h1>
                <p class="text-xs text-purple-200 mt-1">Kriteria: <strong class="text-white">{{ $sociometry->theme }}</strong> &bull; Total Respon: {{ $sociometry->responses->count() }} siswa</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-black bg-white text-purple-800 shadow-xs">
                    {{ $sociometry->status }}
                </span>
            </div>
        </div>
    </div>

    <!-- Form Input Respon Siswa (Toggleable) -->
    <div id="form-input-respon" class="hidden p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-md">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3">Catat Pilihan Sosiometri Peserta Didik</h3>
        <form action="{{ route('bk.sociometry.response', $sociometry) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Siswa Responden (Yang Memilih) *</label>
                    <select name="student_id" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($sociometry->schoolClass?->students ?? [] as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan 1 (Prioritas Utama - Skor 3) *</label>
                    <select name="first_choice_id" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">-- Pilih Teman --</option>
                        @foreach($sociometry->schoolClass?->students ?? [] as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan 2 (Skor 2)</label>
                    <select name="second_choice_id" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">-- Pilih Teman (Opsional) --</option>
                        @foreach($sociometry->schoolClass?->students ?? [] as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan 3 (Skor 1)</label>
                    <select name="third_choice_id" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">-- Pilih Teman (Opsional) --</option>
                        @foreach($sociometry->schoolClass?->students ?? [] as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Pilihan</label>
                <input type="text" name="reasons" placeholder="Alasan memilih teman tersebut..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
            </div>

            <div class="text-right">
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 text-white font-bold text-xs hover:bg-purple-700 transition">
                    Simpan Pilihan
                </button>
            </div>
        </form>
    </div>

    <!-- Analisis Sosiometrik: Ranking Popularitas & Siswa Terisolir -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Siswa Paling Populer (Bintang Kelas / Star) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5">
            <h3 class="font-bold text-slate-800 dark:text-white mb-1 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                Matriks Skor Penerimaan Sosial Siswa
            </h3>
            <p class="text-xs text-slate-400 mb-4">Bobot skor: Pilihan 1 (3 poin), Pilihan 2 (2 poin), Pilihan 3 (1 poin)</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3">Peringkat</th>
                            <th class="py-2.5 px-3">Nama Siswa</th>
                            <th class="py-2.5 px-3 text-center">Pilihan 1</th>
                            <th class="py-2.5 px-3 text-center">Pilihan 2</th>
                            <th class="py-2.5 px-3 text-center">Pilihan 3</th>
                            <th class="py-2.5 px-3 text-right">Total Skor</th>
                            <th class="py-2.5 px-3 text-center">Status Sosial</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @php $rank = 1; @endphp
                        @foreach($popularityScores as $id => $data)
                        @php
                            $score = $data['total_score'];
                            $statusBadge = $score >= 6
                                ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20 font-black'
                                : ($score === 0 ? 'bg-rose-500/10 text-rose-600 border border-rose-500/20 font-bold' : 'bg-slate-100 text-slate-600 font-medium');
                            $statusLabel = $score >= 6 ? 'Bintang Kelas (Star)' : ($score === 0 ? 'Terisolir (Perlu BK)' : 'Diterima Baik');
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="py-2.5 px-3 font-mono font-bold">{{ $rank++ }}</td>
                            <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-white">{{ $data['student']->name }}</td>
                            <td class="py-2.5 px-3 text-center font-mono">{{ $data['first_count'] }}</td>
                            <td class="py-2.5 px-3 text-center font-mono">{{ $data['second_count'] }}</td>
                            <td class="py-2.5 px-3 text-center font-mono">{{ $data['third_count'] }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-black text-purple-600 dark:text-purple-400">{{ $score }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $statusBadge }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rekomendasi Guru BK Berdasarkan Sosiogram -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs p-5 space-y-4">
            <h3 class="font-bold text-slate-800 dark:text-white">Rekomendasi Tindakan BK</h3>
            
            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40">
                <h4 class="font-bold text-xs text-amber-800 dark:text-amber-300">1. Pemberdayaan Siswa Star</h4>
                <p class="text-xs text-amber-700 dark:text-amber-400 mt-1 leading-relaxed">
                    Siswa dengan skor penerimaan sosial tertinggi dapat diberdayakan sebagai <em>Peer Counselor</em> (Konselor Sebaya) atau ketua kelompok belajar untuk membantu merangkul teman-temannya.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/40">
                <h4 class="font-bold text-xs text-rose-800 dark:text-rose-300">2. Pendampingan Siswa Terisolir</h4>
                <p class="text-xs text-rose-700 dark:text-rose-400 mt-1 leading-relaxed">
                    Siswa dengan skor 0 (tidak terpilih oleh siapapun) berpotensi mengalami isolasi sosial atau penolakan kelompok. Guru BK dianjurkan menjadwalkan konseling individual bidang sosial untuk menelaah kendala relasinya.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40">
                <h4 class="font-bold text-xs text-blue-800 dark:text-blue-300">3. Formasi Kelompok Dinamis</h4>
                <p class="text-xs text-blue-700 dark:text-blue-400 mt-1 leading-relaxed">
                    Wali kelas dan guru mata pelajaran disarankan memadukan siswa bintang dengan siswa terisolir dalam penugasan proyek kelompok agar interaksi kelas merata tanpa klik (geng) tertutup.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

