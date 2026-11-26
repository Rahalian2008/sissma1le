@extends('layouts.app')

@section('title', 'Edit Data Tracer Alumni - ' . $tracer->full_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @include('bk.partials.nav')

    <!-- Header & Navigation -->
    <div class="flex items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.tracer.index') }}" class="hover:text-indigo-600 font-medium">Tracer Alumni</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Edit Data</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Edit Data Tracer Alumni</h1>
            <p class="text-xs text-slate-400 mt-0.5">Perbarui informasi status studi, pekerjaan, atau evaluasi tamatan</p>
        </div>
        <a href="{{ route('bk.tracer.show', $tracer) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            Batal
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs">
        <div class="font-bold mb-1">Terdapat kesalahan pengisian data:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('bk.tracer.update', $tracer) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Bagian 1: Identitas -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center text-xs">1</span>
                <span>Identitas Diri Alumni</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Nama Lengkap *</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $tracer->full_name) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $tracer->nisn) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">NIS Sekolah</label>
                    <input type="text" name="nis" value="{{ old('nis', $tracer->nis) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Jenis Kelamin *</label>
                    <select name="gender" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                        <option value="L" {{ old('gender', $tracer->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $tracer->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Tahun Lulus *</label>
                    <input type="text" name="graduation_year" value="{{ old('graduation_year', $tracer->graduation_year) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Rombel / Kelas Terakhir</label>
                    <input type="text" name="last_class" value="{{ old('last_class', $tracer->last_class) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">No. WhatsApp / HP *</label>
                    <input type="text" name="phone" value="{{ old('phone', $tracer->phone) }}" required
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $tracer->email) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Kota Domisili Saat Ini</label>
                    <input type="text" name="current_city" value="{{ old('current_city', $tracer->current_city) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Bagian 2: Status Aktivitas -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-600 flex items-center justify-center text-xs">2</span>
                <span>Status Aktivitas Pasca Kelulusan</span>
            </h2>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Pilih Status Aktivitas Utama *</label>
                <select name="activity_status" id="activity_status_select" required onchange="toggleEditSections()"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-bold">
                    <option value="KULIAH" {{ old('activity_status', $tracer->activity_status) === 'KULIAH' ? 'selected' : '' }}>🎓 Kuliah (Perguruan Tinggi)</option>
                    <option value="BEKERJA" {{ old('activity_status', $tracer->activity_status) === 'BEKERJA' ? 'selected' : '' }}>💼 Bekerja (Karyawan / Pegawai)</option>
                    <option value="WIRAUSAHA" {{ old('activity_status', $tracer->activity_status) === 'WIRAUSAHA' ? 'selected' : '' }}>🚀 Wirausaha (Usaha Mandiri / Bisnis)</option>
                    <option value="KURSUS_PELATIHAN" {{ old('activity_status', $tracer->activity_status) === 'KURSUS_PELATIHAN' ? 'selected' : '' }}>📚 Kursus / Pelatihan</option>
                    <option value="MENCARI_KERJA" {{ old('activity_status', $tracer->activity_status) === 'MENCARI_KERJA' ? 'selected' : '' }}>🔍 Sedang Mencari Kerja</option>
                </select>
            </div>

            <!-- Detail Kuliah -->
            <div id="edit-kuliah" class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400">Rincian Perguruan Tinggi</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Jenjang</label>
                        <select name="college_level" class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="S1" {{ old('college_level', $tracer->college_level) === 'S1' ? 'selected' : '' }}>S1 (Sarjana)</option>
                            <option value="D4" {{ old('college_level', $tracer->college_level) === 'D4' ? 'selected' : '' }}>D4 (Sarjana Terapan)</option>
                            <option value="D3" {{ old('college_level', $tracer->college_level) === 'D3' ? 'selected' : '' }}>D3 (Diploma)</option>
                            <option value="Kedinasan" {{ old('college_level', $tracer->college_level) === 'Kedinasan' ? 'selected' : '' }}>Kedinasan</option>
                            <option value="S2" {{ old('college_level', $tracer->college_level) === 'S2' ? 'selected' : '' }}>S2 (Magister)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Jalur Masuk</label>
                        <input type="text" name="admission_track" value="{{ old('admission_track', $tracer->admission_track) }}" placeholder="SNBP / SNBT / Mandiri"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama Kampus</label>
                        <input list="universities_list" type="text" name="campus_name" value="{{ old('campus_name', $tracer->campus_name) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                        <datalist id="universities_list">
                            @foreach($universities as $uni)
                                <option value="{{ $uni['name'] }}">{{ $uni['short_name'] }} - {{ $uni['province'] }} ({{ $uni['type'] }})</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Program Studi</label>
                        <input list="majors_list" type="text" name="study_program" value="{{ old('study_program', $tracer->study_program) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                        <datalist id="majors_list">
                            @foreach($standardMajors as $group => $majors)
                                @foreach($majors as $m)
                                    <option value="{{ $m }}">{{ $group }}</option>
                                @endforeach
                            @endforeach
                        </datalist>
                    </div>
                </div>
            </div>

            <!-- Detail Bekerja -->
            <div id="edit-bekerja" class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800 hidden">
                <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Rincian Pekerjaan</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama Perusahaan / Kantor</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $tracer->company_name) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Posisi / Jabatan</label>
                        <input type="text" name="job_position" value="{{ old('job_position', $tracer->job_position) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Bidang Sektor</label>
                        <input type="text" name="work_field" value="{{ old('work_field', $tracer->work_field) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Penghasilan Bulanan</label>
                        <input type="text" name="monthly_income_range" value="{{ old('monthly_income_range', $tracer->monthly_income_range) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Detail Wirausaha -->
            <div id="edit-wirausaha" class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800 hidden">
                <div class="text-xs font-bold text-amber-600 dark:text-amber-400">Rincian Usaha Mandiri</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama Usaha / Brand</label>
                        <input type="text" name="business_name" value="{{ old('business_name', $tracer->business_name) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Bidang Usaha</label>
                        <input type="text" name="business_field" value="{{ old('business_field', $tracer->business_field) }}"
                               class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian 3: Evaluasi BK -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center text-xs">3</span>
                <span>Evaluasi &amp; Catatan</span>
            </h2>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Rating Layanan BK (1 - 5 Bintang)</label>
                    <input type="number" min="1" max="5" name="bk_service_rating" value="{{ old('bk_service_rating', $tracer->bk_service_rating) }}"
                           class="w-24 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Dampak Bimbingan Karier BK</label>
                    <textarea name="bk_career_guidance_impact" rows="2"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">{{ old('bk_career_guidance_impact', $tracer->bk_career_guidance_impact) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Pesan &amp; Saran untuk Sekolah</label>
                    <textarea name="school_feedback" rows="2"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">{{ old('school_feedback', $tracer->school_feedback) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('bk.tracer.show', $tracer) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    function toggleEditSections() {
        const val = document.getElementById('activity_status_select').value;
        const k = document.getElementById('edit-kuliah');
        const b = document.getElementById('edit-bekerja');
        const w = document.getElementById('edit-wirausaha');

        if (k) k.classList.add('hidden');
        if (b) b.classList.add('hidden');
        if (w) w.classList.add('hidden');

        if (val === 'KULIAH' && k) k.classList.remove('hidden');
        else if (val === 'BEKERJA' && b) b.classList.remove('hidden');
        else if (val === 'WIRAUSAHA' && w) w.classList.remove('hidden');
    }
    document.addEventListener('DOMContentLoaded', toggleEditSections);
</script>
@endsection
