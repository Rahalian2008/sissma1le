@extends('layouts.app')

@section('title', 'Kokurikuler & Projek P5 - Rapor Kurnas')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Horizontal Rapor Kurnas -->
    @include('rapor_kurnas.partials.nav')

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>💡</span>
                <span>Kokurikuler & Projek Penguatan Profil Pelajar Pancasila (P5)</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola tema projek, target dimensi profil lulusan, serta input capaian penilaian kokurikuler (MB, SB, BSH, SAB).
            </p>
        </div>

        <button type="button" onclick="document.getElementById('modalAddProject').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition cursor-pointer shrink-0">
            <span>+ Tambah Tema / Projek P5</span>
        </button>
    </div>

    <!-- Selector Bar -->
    <form method="GET" action="{{ route('rapor_kurnas.cocurricular') }}" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Kelas / Rombongan Belajar</label>
                <select name="class_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-semibold">
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" {{ $selectedClassId == $cls->id ? 'selected' : '' }}>
                            {{ $cls->name }} (Tingkat {{ $cls->grade }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Projek Kokurikuler</label>
                <select name="project_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-2 font-semibold">
                    @forelse($projects as $p)
                        <option value="{{ $p->id }}" {{ $selectedProjectId == $p->id ? 'selected' : '' }}>
                            [{{ $p->theme }}] {{ $p->title }}
                        </option>
                    @empty
                        <option value="">Belum ada projek di tingkat ini</option>
                    @endforelse
                </select>
            </div>
        </div>
    </form>

    @if($selectedProject && $selectedClass)
        <!-- Project Detail Card -->
        <div class="bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 rounded-2xl p-5 text-xs text-indigo-900 dark:text-indigo-300 space-y-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="px-2.5 py-1 rounded-full bg-indigo-200 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 font-extrabold text-[11px] uppercase">
                    Tema: {{ $selectedProject->theme }}
                </span>
                <span class="text-slate-500 dark:text-slate-400">
                    Koordinator: <strong>{{ $selectedProject->coordinator->name ?? 'Tim Fasilitator P5' }}</strong>
                </span>
            </div>
            <h2 class="text-base font-bold text-indigo-950 dark:text-white">{{ $selectedProject->title }}</h2>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $selectedProject->description }}</p>
            
            <div class="pt-2 flex flex-wrap items-center gap-1.5">
                <span class="font-bold text-[11px] text-slate-500 mr-1">Dimensi yang Dinilai:</span>
                @foreach($selectedProject->target_dimensions ?? [] as $dim)
                    <span class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-indigo-300 dark:border-indigo-700 text-[10px] font-bold text-indigo-700 dark:text-indigo-300">
                        ✓ {{ $dim }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Form Penilaian Kokurikuler Siswa -->
        <form action="{{ route('rapor_kurnas.cocurricular.store_scores', $selectedProject->id) }}" method="POST">
            @csrf

            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">
                        Lembar Penilaian Siswa: {{ $selectedClass->name }} ({{ count($students) }} Siswa)
                    </h2>
                    <div class="text-[11px] text-slate-400 flex items-center gap-2">
                        <span><strong>MB</strong>: Mulai Berkembang</span>
                        <span>•</span>
                        <span><strong>SB</strong>: Sedang Berkembang</span>
                        <span>•</span>
                        <span><strong>BSH</strong>: Berkembang Sesuai Harapan</span>
                        <span>•</span>
                        <span><strong>SAB</strong>: Sangat Berkembang</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="p-3 w-12 text-center">No</th>
                                <th class="p-3 w-48">Nama Siswa</th>
                                @foreach($selectedProject->target_dimensions ?? [] as $dim)
                                    <th class="p-3 w-36 text-center text-[10px]">{{ $dim }}</th>
                                @endforeach
                                <th class="p-3 min-w-[240px]">Catatan Proses Projek</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($students as $index => $student)
                                @php
                                    $scoreData = $existingScores->get($student->id);
                                    $dimScores = $scoreData?->dimension_scores ?? [];
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                    <td class="p-3 text-center text-slate-400 font-mono">
                                        {{ $index + 1 }}
                                        <input type="hidden" name="scores[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td class="p-3 font-bold text-slate-800 dark:text-white">
                                        {{ $student->name }}
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $student->nisn ?? '-' }}</div>
                                    </td>
                                    @foreach($selectedProject->target_dimensions ?? [] as $dim)
                                        @php
                                            $val = $dimScores[$dim] ?? 'BSH';
                                        @endphp
                                        <td class="p-3 text-center">
                                            <select name="scores[{{ $index }}][dimensions][{{ $dim }}]" class="w-full text-center rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[11px] py-1.5 font-bold {{ $val == 'SAB' ? 'text-emerald-600' : ($val == 'BSH' ? 'text-indigo-600' : ($val == 'SB' ? 'text-amber-600' : 'text-slate-600')) }}">
                                                <option value="MB" {{ $val == 'MB' ? 'selected' : '' }}>MB</option>
                                                <option value="SB" {{ $val == 'SB' ? 'selected' : '' }}>SB</option>
                                                <option value="BSH" {{ $val == 'BSH' ? 'selected' : '' }}>BSH</option>
                                                <option value="SAB" {{ $val == 'SAB' ? 'selected' : '' }}>SAB</option>
                                            </select>
                                        </td>
                                    @endforeach
                                    <td class="p-3">
                                        <input type="text" 
                                               name="scores[{{ $index }}][process_notes]" 
                                               value="{{ $scoreData?->process_notes ?? 'Menunjukkan keaktifan, kepedulian sosial, serta kontribusi nyata dalam penyelesaian karya projek.' }}" 
                                               class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white text-xs px-3 py-1.5 focus:border-indigo-500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition cursor-pointer flex items-center gap-2">
                        <span>💾</span>
                        <span>Simpan Capaian Kokurikuler</span>
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="p-12 text-center bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl text-slate-400">
            <span class="text-4xl block mb-3">💡</span>
            <p>Silakan buat tema projek kokurikuler terlebih dahulu dengan menekan tombol <strong>+ Tambah Tema / Projek P5</strong> di atas.</p>
        </div>
    @endif
</div>

<!-- Modal Tambah Projek P5 -->
<div id="modalAddProject" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl text-slate-800 dark:text-white space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold flex items-center gap-2">
                <span>💡</span>
                <span>Tambah Projek Kokurikuler (P5)</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalAddProject').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <form action="{{ route('rapor_kurnas.cocurricular.store_project') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold mb-1">Tingkat Kelas</label>
                    <select name="grade" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2">
                        <option value="X">Kelas X (Fase E)</option>
                        <option value="XI">Kelas XI (Fase F)</option>
                        <option value="XII">Kelas XII (Fase F)</option>
                    </select>
                    <input type="hidden" name="fase" value="E">
                </div>

                <div>
                    <label class="block font-bold mb-1">Tema Projek Resmi</label>
                    <select name="theme" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2">
                        <option value="Gaya Hidup Berkelanjutan">Gaya Hidup Berkelanjutan</option>
                        <option value="Kearifan Lokal">Kearifan Lokal</option>
                        <option value="Bhinneka Tunggal Ika">Bhinneka Tunggal Ika</option>
                        <option value="Bangunlah Jiwa dan Raganya">Bangunlah Jiwa dan Raganya</option>
                        <option value="Suara Demokrasi">Suara Demokrasi</option>
                        <option value="Rekayasa dan Teknologi">Rekayasa dan Teknologi</option>
                        <option value="Kewirausahaan">Kewirausahaan</option>
                        <option value="Kebekerjaan">Kebekerjaan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold mb-1">Judul Kegiatan Projek</label>
                <input type="text" name="title" required placeholder="misal: Pengolahan Sampah Organik dan Biopori Lingkungan" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2">
            </div>

            <div>
                <label class="block font-bold mb-1">Deskripsi Singkat Projek</label>
                <textarea name="description" rows="2.5" required placeholder="Jelaskan tujuan dan aktivitas utama peserta didik dalam projek..." class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2"></textarea>
            </div>

            <div>
                <label class="block font-bold mb-1">Koordinator / Fasilitator Guru</label>
                <select name="coordinator_teacher_id" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs px-3 py-2">
                    <option value="">-- Pilih Guru Koordinator (Opsional) --</option>
                    @foreach($teachers as $tch)
                        <option value="{{ $tch->id }}">{{ $tch->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold mb-1.5">Pilih Target Dimensi Profil Lulusan (P5)</label>
                <div class="space-y-1.5 max-h-36 overflow-y-auto p-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    @php
                        $standardDims = [
                            'Beriman, Bertakwa kepada Tuhan YME, dan Berakhlak Mulia',
                            'Berkebinekaan Global',
                            'Gotong Royong',
                            'Mandiri',
                            'Bernalar Kritis',
                            'Kreatif'
                        ];
                    @endphp
                    @foreach($standardDims as $dim)
                        <label class="flex items-center gap-2 cursor-pointer hover:text-indigo-600">
                            <input type="checkbox" name="target_dimensions[]" value="{{ $dim }}" checked class="rounded text-indigo-600">
                            <span>{{ $dim }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modalAddProject').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 font-bold text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 font-bold text-xs text-white shadow-md">Simpan Projek</button>
            </div>
        </form>
    </div>
</div>
@endsection

