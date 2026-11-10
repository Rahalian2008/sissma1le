@extends('layouts.app')

@section('title', 'Tambah Peserta Didik Baru - Buku Induk & Demografi')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Buku Induk & Demografi</span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs text-slate-500 font-medium">Standar Kemendikbudristek & Disdik Jabar</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Tambah Peserta Didik Baru</h1>
            <p class="text-xs text-slate-500 mt-0.5">Input data siswa lengkap terintegrasi dengan Demografi, Buku Induk Siswa Digital, dan Buku Klaper.</p>
        </div>
        <a href="{{ route('students.index') }}" class="text-xs text-slate-600 hover:text-slate-900 font-semibold flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Siswa
        </a>
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

    <form action="{{ route('students.store') }}" method="POST" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf

        <div class="p-6 sm:p-8 space-y-8">
            <!-- 1. Identitas Pokok Siswa -->
            <div>
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100 mb-4">
                    <span class="w-7 h-7 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">1</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Identitas Pokok Peserta Didik</h2>
                        <p class="text-[11px] text-slate-400">Data identitas kependudukan resmi sesuai Akta Lahir & Kartu Keluarga (KK).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Induk Siswa (NIS) <span class="text-rose-500">*</span></label>
                        <input type="text" name="nis" value="{{ old('nis') }}" required placeholder="cth: 242510001" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">NISN (10 Digit)</label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" maxlength="10" placeholder="cth: 0071234567" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">NIK Siswa (16 Digit)</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="3202xxxxxxxxxxxx" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Kartu Keluarga (No. KK)</label>
                        <input type="text" name="family_card_number" value="{{ old('family_card_number') }}" maxlength="16" placeholder="3202xxxxxxxxxxxx" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap (Sesuai Ijazah/Akta) <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap peserta didik tanpa singkatan" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold text-slate-900 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                        <select name="gender" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-semibold bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place') }}" placeholder="Kota/Kabupaten kelahiran" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Agama</label>
                        <select name="religion" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500">
                            @if(isset($religions) && $religions->count() > 0)
                                @foreach($religions as $r)
                                    <option value="{{ $r->name }}" {{ old('religion') === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                                @endforeach
                            @else
                                <option value="Islam">Islam</option>
                                <option value="Kristen Protestan">Kristen Protestan</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Anak Ke-</label>
                        <input type="number" name="child_order" value="{{ old('child_order', 1) }}" min="1" max="20" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Dari Berapa Bersaudara</label>
                        <input type="number" name="siblings_count" value="{{ old('siblings_count', 1) }}" min="0" max="20" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Golongan Darah</label>
                        <select name="blood_type" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="Tidak Tahu" {{ old('blood_type') === 'Tidak Tahu' ? 'selected' : '' }}>Tidak Tahu</option>
                            <option value="A" {{ old('blood_type') === 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('blood_type') === 'B' ? 'selected' : '' }}>B</option>
                            <option value="AB" {{ old('blood_type') === 'AB' ? 'selected' : '' }}>AB</option>
                            <option value="O" {{ old('blood_type') === 'O' ? 'selected' : '' }}>O</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tinggi (cm)</label>
                            <input type="number" name="height" value="{{ old('height') }}" placeholder="165" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Berat (kg)</label>
                            <input type="number" name="weight" value="{{ old('weight') }}" placeholder="55" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Demografi Tempat Tinggal & Aksesibilitas -->
            <div>
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100 mb-4">
                    <span class="w-7 h-7 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">2</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Demografi Domisili & Aksesibilitas</h2>
                        <p class="text-[11px] text-slate-400">Data geografis, moda transportasi, dan jarak tempuh ke sekolah.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                    <div class="sm:col-span-3">
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal Lengkap (Jalan / Kampung / Dusun)</label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="Kp. Pasir Salam RT 02 RW 05" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">RT / RW</label>
                        <input type="text" name="rt_rw" value="{{ old('rt_rw') }}" placeholder="002 / 005" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kecamatan Domisili</label>
                        <input type="text" name="residence_district" value="{{ old('residence_district', 'Lengkong') }}" placeholder="Kecamatan" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kode Pos</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', '43174') }}" maxlength="10" placeholder="43174" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tinggal Bersama</label>
                        <select name="living_with" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="Bersama Orang Tua" {{ old('living_with') === 'Bersama Orang Tua' ? 'selected' : '' }}>Bersama Orang Tua</option>
                            <option value="Bersama Wali" {{ old('living_with') === 'Bersama Wali' ? 'selected' : '' }}>Bersama Wali</option>
                            <option value="Kost / Asrama" {{ old('living_with') === 'Kost / Asrama' ? 'selected' : '' }}>Kost / Asrama</option>
                            <option value="Panti Asuhan" {{ old('living_with') === 'Panti Asuhan' ? 'selected' : '' }}>Panti Asuhan</option>
                            <option value="Lainnya" {{ old('living_with') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Moda Transportasi</label>
                        <select name="transportation" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="Sepeda Motor" {{ old('transportation') === 'Sepeda Motor' ? 'selected' : '' }}>Sepeda Motor</option>
                            <option value="Jalan Kaki" {{ old('transportation') === 'Jalan Kaki' ? 'selected' : '' }}>Jalan Kaki</option>
                            <option value="Angkutan Umum" {{ old('transportation') === 'Angkutan Umum' ? 'selected' : '' }}>Angkutan Umum</option>
                            <option value="Sepeda" {{ old('transportation') === 'Sepeda' ? 'selected' : '' }}>Sepeda</option>
                            <option value="Antar Jemput" {{ old('transportation') === 'Antar Jemput' ? 'selected' : '' }}>Antar Jemput</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jarak ke Sekolah</label>
                        <select name="distance_to_school" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="Kurang dari 1 km" {{ old('distance_to_school') === 'Kurang dari 1 km' ? 'selected' : '' }}>Kurang dari 1 km</option>
                            <option value="1 - 3 km" {{ old('distance_to_school') === '1 - 3 km' ? 'selected' : '' }}>1 - 3 km</option>
                            <option value="3 - 5 km" {{ old('distance_to_school') === '3 - 5 km' ? 'selected' : '' }}>3 - 5 km</option>
                            <option value="5 - 10 km" {{ old('distance_to_school') === '5 - 10 km' ? 'selected' : '' }}>5 - 10 km</option>
                            <option value="Lebih dari 10 km" {{ old('distance_to_school') === 'Lebih dari 10 km' ? 'selected' : '' }}>Lebih dari 10 km</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kebutuhan Khusus</label>
                        <input type="text" name="special_needs" value="{{ old('special_needs', 'Tidak Ada') }}" placeholder="Tidak Ada" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">No. HP / WhatsApp Siswa</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email Siswa</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="siswa@sma1lengkong.sch.id" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- 3. Pendidikan Asal & Penempatan Kelas -->
            <div>
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100 mb-4">
                    <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">3</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Pendidikan Asal & Penempatan Kelas</h2>
                        <p class="text-[11px] text-slate-400">Riwayat kelulusan SMP/MTs dan jalur masuk penerimaan peserta didik baru (PPDB).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Sekolah Asal (SMP / MTs)</label>
                        <input type="text" name="school_origin" value="{{ old('school_origin') }}" placeholder="SMP Negeri 1 Lengkong" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">No. Seri Ijazah SMP</label>
                        <input type="text" name="junior_diploma_number" value="{{ old('junior_diploma_number') }}" placeholder="DN-02/D-SMP/..." class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">No. Peserta Ujian SMP</label>
                        <input type="text" name="junior_exam_number" value="{{ old('junior_exam_number') }}" placeholder="2-24-02-..." class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jalur Masuk PPDB</label>
                        <select name="entry_path" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white font-medium focus:ring-2 focus:ring-blue-500">
                            <option value="Zonasi" {{ old('entry_path') === 'Zonasi' ? 'selected' : '' }}>Zonasi</option>
                            <option value="Afirmasi / KETM" {{ old('entry_path') === 'Afirmasi / KETM' ? 'selected' : '' }}>Afirmasi / KETM</option>
                            <option value="Prestasi Nilai Rapor" {{ old('entry_path') === 'Prestasi Nilai Rapor' ? 'selected' : '' }}>Prestasi Nilai Rapor</option>
                            <option value="Prestasi Kejuaraan" {{ old('entry_path') === 'Prestasi Kejuaraan' ? 'selected' : '' }}>Prestasi Kejuaraan</option>
                            <option value="Perpindahan Tugas Orang Tua" {{ old('entry_path') === 'Perpindahan Tugas Orang Tua' ? 'selected' : '' }}>Perpindahan Tugas Orang Tua</option>
                            <option value="Mutasi / Pindahan Masuk" {{ old('entry_path') === 'Mutasi / Pindahan Masuk' ? 'selected' : '' }}>Mutasi / Pindahan Masuk</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Diterima</label>
                        <input type="date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tingkat Kelas <span class="text-rose-500">*</span></label>
                        <select name="current_class_level" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold bg-white focus:ring-2 focus:ring-blue-500">
                            @if(isset($classes) && $classes->count() > 0)
                                @foreach($classes as $c)
                                    <option value="{{ $c->name }}" {{ old('current_class_level') === $c->name ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                                @endforeach
                            @else
                                <option value="X">Kelas X</option>
                                <option value="XI">Kelas XI</option>
                                <option value="XII">Kelas XII</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Rombongan Belajar (Rombel)</label>
                        <select name="current_rombel" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white font-medium focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Rombel --</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->name }}" {{ old('current_rombel') === $rombel->name ? 'selected' : '' }}>
                                    {{ $rombel->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Diterima di Kelas / Rombel Pertama</label>
                        <input type="text" name="admission_class" value="{{ old('admission_class', 'X Merdeka 1') }}" placeholder="X Merdeka 1" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Status Keaktifan Siswa <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold bg-white focus:ring-2 focus:ring-blue-500">
                            @if(isset($statuses) && $statuses->count() > 0)
                                @foreach($statuses as $st)
                                    <option value="{{ $st->name }}" {{ old('status', 'Aktif') === $st->name ? 'selected' : '' }}>{{ $st->name }}</option>
                                @endforeach
                            @else
                                <option value="Aktif" selected>Aktif</option>
                                <option value="Mutasi Masuk">Mutasi Masuk</option>
                                <option value="Mutasi Keluar">Mutasi Keluar</option>
                                <option value="Lulus">Lulus</option>
                                <option value="Mengundurkan Diri">Mengundurkan Diri</option>
                                <option value="Dikeluarkan">Dikeluarkan</option>
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Program PIP / Bantuan Afirmasi -->
                <div class="mt-4 p-4 bg-amber-50/70 border border-amber-200 rounded-2xl">
                    <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-amber-900 text-xs">
                        <input type="checkbox" name="is_pip" value="1" {{ old('is_pip') ? 'checked' : '' }} onchange="document.getElementById('pip-container').classList.toggle('hidden', !this.checked)" class="w-4 h-4 text-amber-600 rounded">
                        <span>Peserta Didik Penerima Bantuan Program Indonesia Pintar (PIP / KIP)</span>
                    </label>
                    <div id="pip-container" class="{{ old('is_pip') ? '' : 'hidden' }} mt-3 pt-3 border-t border-amber-200 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor KIP / Kartu Indonesia Pintar</label>
                            <input type="text" name="pip_number" value="{{ old('pip_number') }}" placeholder="KIP-xxxxxxxx" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Data Orang Tua & Wali (Buku Induk Lengkap) -->
            <div>
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100 mb-4">
                    <span class="w-7 h-7 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">4</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Keterangan Orang Tua & Wali Siswa</h2>
                        <p class="text-[11px] text-slate-400">Data lengkap orang tua kandung dan wali murid sesuai standar Buku Induk Pendidikan.</p>
                    </div>
                </div>

                <!-- Data Ayah -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 mb-4">
                    <h3 class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        A. Data Ayah Kandung
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Ayah Kandung</label>
                            <input type="text" name="father_name" value="{{ old('father_name') }}" placeholder="Nama lengkap ayah" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">NIK Ayah</label>
                            <input type="text" name="father_nik" value="{{ old('father_nik') }}" maxlength="16" placeholder="3202xxxxxxxxxxxx" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pendidikan Terakhir</label>
                            <select name="father_education" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                                <option value="">-- Pilih Pendidikan --</option>
                                <option value="SD / Sederajat">SD / Sederajat</option>
                                <option value="SMP / Sederajat">SMP / Sederajat</option>
                                <option value="SMA / Sederajat" selected>SMA / Sederajat</option>
                                <option value="D1 / D2 / D3">D1 / D2 / D3</option>
                                <option value="D4 / S1">D4 / S1</option>
                                <option value="S2 / S3">S2 / S3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pekerjaan Ayah</label>
                            <input type="text" name="father_occupation" value="{{ old('father_occupation') }}" placeholder="Wiraswasta / PNS / Petani" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Penghasilan Bulanan</label>
                            <select name="father_income" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                                <option value="< Rp 1.000.000">&lt; Rp 1.000.000</option>
                                <option value="Rp 1.000.000 - Rp 2.000.000" selected>Rp 1.000.000 - Rp 2.000.000</option>
                                <option value="Rp 2.000.000 - Rp 5.000.000">Rp 2.000.000 - Rp 5.000.000</option>
                                <option value="> Rp 5.000.000">&gt; Rp 5.000.000</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon / HP Ayah</label>
                            <input type="text" name="father_phone" value="{{ old('father_phone') }}" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                    </div>
                </div>

                <!-- Data Ibu -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 mb-4">
                    <h3 class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        B. Data Ibu Kandung
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Ibu Kandung</label>
                            <input type="text" name="mother_name" value="{{ old('mother_name') }}" placeholder="Nama lengkap ibu" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">NIK Ibu</label>
                            <input type="text" name="mother_nik" value="{{ old('mother_nik') }}" maxlength="16" placeholder="3202xxxxxxxxxxxx" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pendidikan Terakhir</label>
                            <select name="mother_education" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                                <option value="">-- Pilih Pendidikan --</option>
                                <option value="SD / Sederajat">SD / Sederajat</option>
                                <option value="SMP / Sederajat">SMP / Sederajat</option>
                                <option value="SMA / Sederajat" selected>SMA / Sederajat</option>
                                <option value="D1 / D2 / D3">D1 / D2 / D3</option>
                                <option value="D4 / S1">D4 / S1</option>
                                <option value="S2 / S3">S2 / S3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pekerjaan Ibu</label>
                            <input type="text" name="mother_occupation" value="{{ old('mother_occupation', 'Ibu Rumah Tangga') }}" placeholder="Ibu Rumah Tangga / Karyawan" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Penghasilan Bulanan</label>
                            <select name="mother_income" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                                <option value="Tidak Berpenghasilan" selected>Tidak Berpenghasilan</option>
                                <option value="< Rp 1.000.000">&lt; Rp 1.000.000</option>
                                <option value="Rp 1.000.000 - Rp 2.000.000">Rp 1.000.000 - Rp 2.000.000</option>
                                <option value="Rp 2.000.000 - Rp 5.000.000">Rp 2.000.000 - Rp 5.000.000</option>
                                <option value="> Rp 5.000.000">&gt; Rp 5.000.000</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon / HP Ibu</label>
                            <input type="text" name="mother_phone" value="{{ old('mother_phone') }}" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                    </div>
                </div>

                <!-- Data Wali -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                    <h3 class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                        C. Data Wali Siswa (Jika Tidak Tinggal Bersama Orang Tua)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Wali</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Nama lengkap wali murid" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">NIK Wali</label>
                            <input type="text" name="guardian_nik" value="{{ old('guardian_nik') }}" maxlength="16" placeholder="3202xxxxxxxxxxxx" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pendidikan Wali</label>
                            <input type="text" name="guardian_education" value="{{ old('guardian_education') }}" placeholder="SMA / S1" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pekerjaan Wali</label>
                            <input type="text" name="guardian_occupation" value="{{ old('guardian_occupation') }}" placeholder="Pekerjaan wali" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Penghasilan Wali</label>
                            <input type="text" name="guardian_income" value="{{ old('guardian_income') }}" placeholder="Penghasilan" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon / HP Wali</label>
                            <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 sm:px-8 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">Data akan otomatis terdaftar pada Buku Induk Siswa Digital & Statistik Demografi.</span>
            <div class="flex items-center space-x-3">
                <a href="{{ route('students.index') }}" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Data Buku Induk Siswa
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
