@extends('layouts.app')

@section('title', 'Tambah Pegawai PTK Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Tambah Pegawai Baru</h1>
                <p class="text-xs text-slate-500">Formulir pendaftaran data pokok pegawai {{ $school->name ?? 'Sekolah' }}.</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800">
            <p class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('employees.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Bagian 1: Identitas Pribadi -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                Identitas Pribadi Pegawai
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Gelar Depan</label>
                    <input type="text" name="title_prefix" value="{{ old('title_prefix') }}" placeholder="Drs. / Prof. / Dr." class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap (Tanpa Gelar) <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Gelar Belakang</label>
                    <input type="text" name="title_suffix" value="{{ old('title_suffix') }}" placeholder="M.Pd. / S.Kom. / S.Pd." class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" placeholder="197801012005011001" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NUPTK</label>
                    <input type="text" name="nuptk" value="{{ old('nuptk') }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place') }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">No. HP / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
                <div class="sm:col-span-3">
                    <label class="block font-semibold text-slate-700 mb-1">Alamat Domisili</label>
                    <textarea name="address" rows="2" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">{{ old('address') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Bagian 2: Status & Penugasan -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold">2</span>
                Kepegawaian & Jabatan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Pegawai <span class="text-rose-500">*</span></label>
                    <select name="employment_type" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="Guru" {{ old('employment_type') === 'Guru' ? 'selected' : '' }}>Guru</option>
                        <option value="Tenaga Kependidikan" {{ old('employment_type') === 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan (Tendik / TU)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Kepegawaian <span class="text-rose-500">*</span></label>
                    <select name="employment_status" required class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        @foreach($employmentStatuses as $es)
                            <option value="{{ $es->name }}" {{ old('employment_status', 'PNS') === $es->name ? 'selected' : '' }}>
                                {{ $es->name }} ({{ $es->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Pilihan Jabatan Fungsional / Struktural (Bisa Pilih Lebih Dari 1) -->
                <div class="col-span-full bg-slate-50/80 p-4 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <label class="block font-bold text-slate-800 text-sm">Jabatan Fungsional / Struktural <span class="text-blue-600 text-xs font-normal">(Bisa memilih lebih dari 1)</span></label>
                            <p class="text-xs text-slate-500">Pilih penugasan dan tanggung jawab dinas pegawai di satuan pendidikan.</p>
                        </div>
                        <span class="text-[11px] font-semibold px-2 py-0.5 bg-blue-100 text-blue-700 rounded-md">Multi-Pilihan</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 pt-1">
                        @foreach($standardPositions as $sp)
                            <label class="relative flex items-center p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-blue-50/50 hover:border-blue-300 transition-all cursor-pointer shadow-xs select-none">
                                <input type="checkbox" name="positions[]" value="{{ $sp }}"
                                    id="pos_{{ Str::slug($sp) }}"
                                    class="position-checkbox w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500"
                                    {{ (is_array(old('positions')) && in_array($sp, old('positions'))) || old('position') === $sp ? 'checked' : '' }}
                                    @if($sp === 'Wali Kelas') onchange="toggleHomeroomRombel(this.checked)" @endif
                                >
                                <span class="ml-2.5 text-xs font-semibold text-slate-700 leading-tight">{{ $sp }}</span>
                            </label>
                        @endforeach
                    </div>

                    <!-- Menu Baru: Pemilihan Rombel saat Wali Kelas dipilih -->
                    <div id="homeroomRombelContainer" class="mt-3.5 p-3.5 bg-blue-50/70 border border-blue-200 rounded-xl {{ (is_array(old('positions')) && in_array('Wali Kelas', old('positions'))) || old('homeroom_rombel') ? '' : 'hidden' }}">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex-1">
                                <label for="homeroom_rombel_select" class="block text-xs font-bold text-blue-900 mb-1">
                                    🏫 Pilih Rombel Asuhan (Wali Kelas) <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-[11px] text-blue-700">Nama pegawai ini akan otomatis ditetapkan sebagai Wali Kelas dan mengisi seluruh dokumen cetak resmi (presensi, daftar rombel, surat peringatan, dan agenda pembelajaran).</p>
                            </div>
                            <div class="w-full sm:w-64">
                                <select name="homeroom_rombel" id="homeroom_rombel_select" class="w-full px-3 py-2 text-xs font-semibold bg-white border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800">
                                    <option value="">-- Pilih Rombel --</option>
                                    @foreach($rombels as $r)
                                        <option value="{{ $r }}" {{ old('homeroom_rombel') === $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center gap-2">
                        <label class="text-xs font-semibold text-slate-600 whitespace-nowrap">Jabatan Tambahan / Lainnya (Opsional):</label>
                        <input type="text" name="custom_position" value="{{ old('custom_position') }}" placeholder="Contoh: Guru Mata Pelajaran / Kepala Tata Usaha..." class="flex-1 px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pangkat & Golongan Ruang</label>
                    <select name="rank_group" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Pangkat Golongan --</option>
                        @foreach($rankGroups as $rg)
                            <option value="{{ $rg->name }}" {{ old('rank_group') === $rg->name ? 'selected' : '' }}>{{ $rg->name }} ({{ $rg->description }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">TMT Pangkat Terakhir</label>
                    <input type="date" name="rank_tmt" value="{{ old('rank_tmt') }}" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mata Pelajaran yang Diampu</label>
                    <input type="text" name="teaching_subject" value="{{ old('teaching_subject') }}" placeholder="Matematika, Fisika, dsb..." class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Masa Kerja (Tahun)</label>
                    <input type="number" name="years_of_service_years" value="{{ old('years_of_service_years', 5) }}" min="0" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Masa Kerja (Bulan)</label>
                    <input type="number" name="years_of_service_months" value="{{ old('years_of_service_months', 0) }}" min="0" max="11" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tugas Tambahan</label>
                    <input type="text" name="additional_tasks" value="{{ old('additional_tasks') }}" placeholder="Wakasek / Kepala Lab / Wali Kelas" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="is_certified" value="1" {{ old('is_certified') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <span>Sudah Memiliki Sertifikat Pendidik (Sertifikasi Guru)</span>
                </label>
            </div>
        </div>

        <!-- Bagian 3: Pendidikan Terakhir -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-bold">3</span>
                Pendidikan Terakhir
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenjang</label>
                    <select name="education_level" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="S1">S1 / D4</option>
                        <option value="S2">S2 (Magister)</option>
                        <option value="S3">S3 (Doktor)</option>
                        <option value="D3">D3</option>
                        <option value="SMA/SMK">SMA / SMK</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Program Studi / Jurusan</label>
                    <input type="text" name="education_major" value="{{ old('education_major') }}" placeholder="Pendidikan Matematika" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Perguruan Tinggi / Universitas</label>
                    <input type="text" name="education_institution" value="{{ old('education_institution') }}" placeholder="Universitas Negeri Jakarta" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Lulus</label>
                    <input type="text" name="education_grad_year" value="{{ old('education_grad_year') }}" placeholder="2015" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('employees.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                Simpan Pegawai PTK
            </button>
        </div>
    </form>
</div>

<script>
function toggleHomeroomRombel(isChecked) {
    const container = document.getElementById('homeroomRombelContainer');
    if (container) {
        if (isChecked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            const select = document.getElementById('homeroom_rombel_select');
            if (select) select.value = '';
        }
    }
}
</script>
@endsection
