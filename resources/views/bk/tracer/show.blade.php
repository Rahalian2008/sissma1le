@extends('layouts.app')

@section('title', 'Detail Tracer Alumni - ' . $tracer->full_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @include('bk.partials.nav')

    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.tracer.index') }}" class="hover:text-indigo-600 font-medium">Tracer Alumni</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Detail Instrumen Standar</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">{{ $tracer->full_name }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">Tamatan Tahun {{ $tracer->graduation_year }} &bull; Diinput pada {{ $tracer->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>
        <div class="flex items-center gap-2">
            @if($tracer->phone)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tracer->phone) }}?text={{ urlencode('Halo ' . $tracer->full_name . ', salam dari Bimbingan Konseling SMAN 1 Lengkong...') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                <span>WhatsApp</span>
            </a>
            @endif
            <a href="{{ route('bk.tracer.edit', $tracer) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit</span>
            </a>
            <a href="{{ route('bk.tracer.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Overview Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5 mb-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-xl shrink-0">
                    {{ strtoupper(substr($tracer->full_name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">{{ $tracer->full_name }}</h2>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $tracer->activity_badge_class }}">
                            {{ $tracer->activity_status_label }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Lulus Tahun {{ $tracer->graduation_year }} &bull; Rombel: {{ $tracer->last_class ?? '-' }} &bull; {{ $tracer->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        @if($tracer->high_school_major) &bull; <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $tracer->high_school_major }}</span> @endif
                    </p>
                </div>
            </div>
            @if($tracer->bk_service_rating)
            <div class="text-right">
                <span class="text-[11px] font-bold text-slate-400 block">Rating Layanan BK</span>
                <span class="text-lg font-black text-amber-500">{{ $tracer->bk_service_rating }} / 5 ⭐</span>
            </div>
            @endif
        </div>

        <!-- 2 Kolom: Identitas Latar Belakang & Aktivitas Utama -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 1. Identitas & Latar Belakang Alumni -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    1. Identitas &amp; Latar Belakang Alumni
                </h3>
                <div class="bg-slate-50 dark:bg-slate-800/40 rounded-xl p-4 text-xs space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Peminatan / Jurusan SMA:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->high_school_major ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Sumber Pembiayaan Sekolah:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->study_funding_source ?: 'Biaya sendiri / keluarga' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">NISN / NIS:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->nisn ?: '-' }} / {{ $tracer->nis ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">WhatsApp / HP:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Email:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->email ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Domisili Kota / Wilayah:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->current_city ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Status Kegiatan Saat Ini -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    2. Status Kegiatan Saat Ini
                </h3>
                <div class="bg-slate-50 dark:bg-slate-800/40 rounded-xl p-4 text-xs space-y-2.5">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Status Aktivitas:</span>
                        <span class="px-2 py-0.5 rounded-full font-bold text-[11px] {{ $tracer->activity_badge_class }}">
                            {{ $tracer->activity_status_label }}
                        </span>
                    </div>

                    @if($tracer->activity_status === 'KULIAH')
                        <div class="flex justify-between">
                            <span class="text-slate-500">Perguruan Tinggi:</span>
                            <span class="font-bold text-indigo-600 dark:text-indigo-400 text-right">{{ $tracer->campus_name ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Program Studi &amp; Jenjang:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $tracer->study_program ?: '-' }} ({{ $tracer->college_level ?? 'S1' }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Jalur Masuk Kuliah:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->admission_track ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Mulai Masuk &amp; Biaya:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->college_start_date ?: '-' }} &bull; {{ $tracer->college_funding_source ?: 'Mandiri' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Keselarasan Peminatan:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $tracer->major_alignment ?: '-' }}</span>
                        </div>
                    @elseif($tracer->activity_status === 'BEKERJA')
                        <div class="flex justify-between">
                            <span class="text-slate-500">Perusahaan / Instansi:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 text-right">{{ $tracer->company_name ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Jenis Instansi:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->company_type ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Jabatan &amp; Sifat Kerja:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->job_position ?: '-' }} ({{ $tracer->work_type ?: 'Full Time' }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Lokasi Kantor:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->company_location ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Kisaran Take Home Pay:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $tracer->monthly_income_range ?: '-' }}</span>
                        </div>
                    @elseif($tracer->activity_status === 'WIRAUSAHA')
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nama Usaha / Brand:</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400 text-right">{{ $tracer->business_name ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Bidang Usaha:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->business_field ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Estimasi Omzet Bulanan:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->business_revenue_range ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Jumlah Karyawan:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->employee_count ?? 0 }} Orang</span>
                        </div>
                    @elseif($tracer->activity_status === 'BELUM_MEMUNGKINKAN')
                        <div class="text-slate-600 dark:text-slate-300 italic py-2">
                            Belum memungkinkan bekerja saat ini (misal: fokus mengurus keluarga, mempersiapkan diri, atau pertimbangan kesehatan).
                        </div>
                    @else
                        <div class="text-slate-500 italic py-2">
                            Sedang dalam fase persiapan studi lanjut, kursus peningkatan kompetensi, atau proses seleksi pencarian pekerjaan.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Transisi ke Dunia Kerja (Khusus Bekerja & Wirausaha) -->
        @if(in_array($tracer->activity_status, ['BEKERJA', 'WIRAUSAHA']))
        <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                3. Transisi ke Dunia Kerja &amp; Relevansi Pendidikan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                    <span class="text-slate-400 block text-[11px]">Kapan Mulai Mencari:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->job_search_start ?: '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                    <span class="text-slate-400 block text-[11px]">Masa Tunggu Dapat Kerja:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->job_waiting_time ?: '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                    <span class="text-slate-400 block text-[11px]">Cara Mendapatkan Kerja:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->job_search_method ?: '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                    <span class="text-slate-400 block text-[11px]">Keeratan Hubungan Bidang:</span>
                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $tracer->job_major_relevance ?: '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                    <span class="text-slate-400 block text-[11px]">Kesesuaian Jenjang Pendidikan:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->job_education_level_match ?: '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                    <span class="text-slate-400 block text-[11px]">Alasan Ketidaksesuaian (Jika Ada):</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->job_mismatch_reason ?: '-' }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- 5. Penilaian 6 Kompetensi Lulusan (Skala 1 - 5) -->
        <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                5. Penilaian Kompetensi Lulusan (Standar Kemdikbudristek / DIKTI)
            </h3>
            @php
                $stdCompetencies = \App\Models\BkTracerAlumnus::standardCompetencies();
                $evalCompetencies = (array) ($tracer->competencies_evaluation ?? []);
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($stdCompetencies as $kKey => $kLabel)
                    @php $score = (int) ($evalCompetencies[$kKey] ?? 0); @endphp
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $kLabel }}</span>
                            <span class="text-xs font-black text-amber-500">{{ $score > 0 ? $score . ' / 5 ⭐' : '-' }}</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $score * 20 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 6. Evaluasi 5 Metode Pembelajaran (Skala 1 - 5) -->
        <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                6. Evaluasi Penekanan Metode Pembelajaran
            </h3>
            @php
                $stdMethods = \App\Models\BkTracerAlumnus::standardLearningMethods();
                $evalMethods = (array) ($tracer->learning_methods_evaluation ?? []);
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                @foreach($stdMethods as $mKey => $mLabel)
                    @php $mScore = (int) ($evalMethods[$mKey] ?? 0); @endphp
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60 text-center">
                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ $mLabel }}</span>
                        <div class="text-sm font-black text-teal-600 dark:text-teal-400">{{ $mScore > 0 ? $mScore . ' / 5' : '-' }}</div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-1.5 overflow-hidden">
                            <div class="bg-teal-500 h-1.5 rounded-full" style="width: {{ $mScore * 20 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Masukan, Saran Peningkatan Mutu & Dampak BK -->
        @if($tracer->improvement_suggestions || $tracer->bk_career_guidance_impact || $tracer->school_feedback)
        <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 space-y-4">
            @if($tracer->improvement_suggestions)
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Hal yang Paling Perlu Ditingkatkan di Sekolah</h4>
                <div class="p-3.5 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 text-xs text-indigo-950 dark:text-indigo-200 leading-relaxed font-medium">
                    "{{ $tracer->improvement_suggestions }}"
                </div>
            </div>
            @endif

            @if($tracer->bk_career_guidance_impact)
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Dampak Bimbingan Karier &amp; Konseling</h4>
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-xs text-slate-800 dark:text-slate-200 leading-relaxed italic">
                    "{{ $tracer->bk_career_guidance_impact }}"
                </div>
            </div>
            @endif

            @if($tracer->school_feedback)
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pesan &amp; Saran Tambahan untuk Almamater</h4>
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-xs text-slate-800 dark:text-slate-200 leading-relaxed italic">
                    "{{ $tracer->school_feedback }}"
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
