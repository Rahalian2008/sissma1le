@extends('layouts.app')

@section('title', 'Edit Data Siswa - ' . $student->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('students.show', $student) }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Perbarui Buku Induk & Demografi</span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">NIS: {{ $student->nis }}</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 mt-1">Edit Data Siswa: {{ $student->name }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi kependudukan, demografi domisili, sekolah asal, dan data keluarga.</p>
            </div>
        </div>
        <a href="{{ route('buku-induk.show', $student) }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            Lembar Buku Induk
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

    <form action="{{ route('students.update', $student) }}" method="POST" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')

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
                        <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">NISN (10 Digit)</label>
                        <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" maxlength="10" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">NIK Siswa (16 Digit)</label>
                        <input type="text" name="nik" value="{{ old('nik', $student->nik) }}" maxlength="16" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Kartu Keluarga (No. KK)</label>
                        <input type="text" name="family_card_number" value="{{ old('family_card_number', $student->family_card_number) }}" maxlength="16" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap (Sesuai Ijazah/Akta) <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $student->name) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold text-slate-900 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                        <select name="gender" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-semibold bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Agama</label>
                        <select name="religion" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500">
                            @php $currRel = old('religion', $student->religion); @endphp
                            @if(isset($religions) && $religions->count() > 0)
                                @foreach($religions as $r)
                                    <option value="{{ $r->name }}" {{ $currRel === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                                @endforeach
                            @else
                                <option value="Islam" {{ $currRel === 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen Protestan" {{ $currRel === 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                                <option value="Katolik" {{ $currRel === 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ $currRel === 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ $currRel === 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Konghucu" {{ $currRel === 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Anak Ke-</label>
                        <input type="number" name="child_order" value="{{ old('child_order', $student->child_order ?? 1) }}" min="1" max="20" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Dari Berapa Bersaudara</label>
                        <input type="number" name="siblings_count" value="{{ old('siblings_count', $student->siblings_count ?? 1) }}" min="0" max="20" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Golongan Darah</label>
                        @php $bType = old('blood_type', $student->blood_type ?? 'Tidak Tahu'); @endphp
                        <select name="blood_type" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="Tidak Tahu" {{ $bType === 'Tidak Tahu' ? 'selected' : '' }}>Tidak Tahu</option>
                            <option value="A" {{ $bType === 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ $bType === 'B' ? 'selected' : '' }}>B</option>
                            <option value="AB" {{ $bType === 'AB' ? 'selected' : '' }}>AB</option>
                            <option value="O" {{ $bType === 'O' ? 'selected' : '' }}>O</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tinggi (cm)</label>
                            <input type="number" name="height" value="{{ old('height', $student->height) }}" placeholder="165" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Berat (kg)</label>
                            <input type="number" name="weight" value="{{ old('weight', $student->weight) }}" placeholder="55" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
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
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal Lengkap</label>
                        <input type="text" name="address" value="{{ old('address', $student->address) }}" placeholder="Kp. Pasir Salam RT 02 RW 05" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">RT / RW</label>
                        <input type="text" name="rt_rw" value="{{ old('rt_rw', $student->rt_rw) }}" placeholder="002 / 005" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kecamatan Domisili</label>
                        <input type="text" name="residence_district" value="{{ old('residence_district', $student->residence_district ?? 'Lengkong') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kode Pos</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $student->postal_code ?? '43174') }}" maxlength="10" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tinggal Bersama</label>
                        @php $living = old('living_with', $student->living_with ?? 'Bersama Orang Tua'); @endphp
                        <select name="living_with" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="Bersama Orang Tua" {{ $living === 'Bersama Orang Tua' ? 'selected' : '' }}>Bersama Orang Tua</option>
                            <option value="Bersama Wali" {{ $living === 'Bersama Wali' ? 'selected' : '' }}>Bersama Wali</option>
                            <option value="Kost / Asrama" {{ $living === 'Kost / Asrama' ? 'selected' : '' }}>Kost / Asrama</option>
                            <option value="Panti Asuhan" {{ $living === 'Panti Asuhan' ? 'selected' : '' }}>Panti Asuhan</option>
                            <option value="Lainnya" {{ $living === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Moda Transportasi</label>
                        @php $trans = old('transportation', $student->transportation ?? 'Sepeda Motor'); @endphp
                        <select name="transportation" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="Sepeda Motor" {{ $trans === 'Sepeda Motor' ? 'selected' : '' }}>Sepeda Motor</option>
                            <option value="Jalan Kaki" {{ $trans === 'Jalan Kaki' ? 'selected' : '' }}>Jalan Kaki</option>
                            <option value="Angkutan Umum" {{ $trans === 'Angkutan Umum' ? 'selected' : '' }}>Angkutan Umum</option>
                            <option value="Sepeda" {{ $trans === 'Sepeda' ? 'selected' : '' }}>Sepeda</option>
                            <option value="Antar Jemput" {{ $trans === 'Antar Jemput' ? 'selected' : '' }}>Antar Jemput</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jarak ke Sekolah</label>
                        @php $dist = old('distance_to_school', $student->distance_to_school ?? '1 - 3 km'); @endphp
                        <select name="distance_to_school" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="Kurang dari 1 km" {{ $dist === 'Kurang dari 1 km' ? 'selected' : '' }}>Kurang dari 1 km</option>
                            <option value="1 - 3 km" {{ $dist === '1 - 3 km' ? 'selected' : '' }}>1 - 3 km</option>
                            <option value="3 - 5 km" {{ $dist === '3 - 5 km' ? 'selected' : '' }}>3 - 5 km</option>
                            <option value="5 - 10 km" {{ $dist === '5 - 10 km' ? 'selected' : '' }}>5 - 10 km</option>
                            <option value="Lebih dari 10 km" {{ $dist === 'Lebih dari 10 km' ? 'selected' : '' }}>Lebih dari 10 km</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kebutuhan Khusus</label>
                        <input type="text" name="special_needs" value="{{ old('special_needs', $student->special_needs ?? 'Tidak Ada') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">No. HP / WhatsApp Siswa</label>
                        <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email Siswa</label>
                        <input type="email" name="email" value="{{ old('email', $student->email) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
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
                        <input type="text" name="school_origin" value="{{ old('school_origin', $student->school_origin) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">No. Seri Ijazah SMP</label>
                        <input type="text" name="junior_diploma_number" value="{{ old('junior_diploma_number', $student->junior_diploma_number) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">No. Peserta Ujian SMP</label>
                        <input type="text" name="junior_exam_number" value="{{ old('junior_exam_number', $student->junior_exam_number) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jalur Masuk PPDB</label>
                        @php $entry = old('entry_path', $student->entry_path ?? 'Zonasi'); @endphp
                        <select name="entry_path" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white font-medium focus:ring-2 focus:ring-blue-500">
                            <option value="Zonasi" {{ $entry === 'Zonasi' ? 'selected' : '' }}>Zonasi</option>
                            <option value="Afirmasi / KETM" {{ $entry === 'Afirmasi / KETM' ? 'selected' : '' }}>Afirmasi / KETM</option>
                            <option value="Prestasi Nilai Rapor" {{ $entry === 'Prestasi Nilai Rapor' ? 'selected' : '' }}>Prestasi Nilai Rapor</option>
                            <option value="Prestasi Kejuaraan" {{ $entry === 'Prestasi Kejuaraan' ? 'selected' : '' }}>Prestasi Kejuaraan</option>
                            <option value="Perpindahan Tugas Orang Tua" {{ $entry === 'Perpindahan Tugas Orang Tua' ? 'selected' : '' }}>Perpindahan Tugas Orang Tua</option>
                            <option value="Mutasi / Pindahan Masuk" {{ $entry === 'Mutasi / Pindahan Masuk' ? 'selected' : '' }}>Mutasi / Pindahan Masuk</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Diterima</label>
                        <input type="date" name="admission_date" value="{{ old('admission_date', $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('Y-m-d') : null) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tingkat Kelas <span class="text-rose-500">*</span></label>
                        <select name="current_class_level" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold bg-white focus:ring-2 focus:ring-blue-500">
                            @if(isset($classes) && $classes->count() > 0)
                                @foreach($classes as $c)
                                    <option value="{{ $c->name }}" {{ old('current_class_level', $student->current_class_level) === $c->name ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                                @endforeach
                            @else
                                <option value="X" {{ old('current_class_level', $student->current_class_level) === 'X' ? 'selected' : '' }}>Kelas X</option>
                                <option value="XI" {{ old('current_class_level', $student->current_class_level) === 'XI' ? 'selected' : '' }}>Kelas XI</option>
                                <option value="XII" {{ old('current_class_level', $student->current_class_level) === 'XII' ? 'selected' : '' }}>Kelas XII</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Rombongan Belajar (Rombel)</label>
                        <select name="current_rombel" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white font-medium focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Rombel --</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->name }}" {{ old('current_rombel', $student->current_rombel) === $rombel->name ? 'selected' : '' }}>
                                    {{ $rombel->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Diterima di Kelas / Rombel Pertama</label>
                        <input type="text" name="admission_class" value="{{ old('admission_class', $student->admission_class) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Status Keaktifan Siswa <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-bold bg-white focus:ring-2 focus:ring-blue-500">
                            @if(isset($statuses) && $statuses->count() > 0)
                                @foreach($statuses as $st)
                                    <option value="{{ $st->name }}" {{ old('status', $student->status) === $st->name ? 'selected' : '' }}>{{ $st->name }}</option>
                                @endforeach
                            @else
                                <option value="Aktif" {{ old('status', $student->status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Mutasi Masuk" {{ old('status', $student->status) === 'Mutasi Masuk' ? 'selected' : '' }}>Mutasi Masuk</option>
                                <option value="Mutasi Keluar" {{ old('status', $student->status) === 'Mutasi Keluar' ? 'selected' : '' }}>Mutasi Keluar</option>
                                <option value="Lulus" {{ old('status', $student->status) === 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                <option value="Mengundurkan Diri" {{ old('status', $student->status) === 'Mengundurkan Diri' ? 'selected' : '' }}>Mengundurkan Diri</option>
                                <option value="Dikeluarkan" {{ old('status', $student->status) === 'Dikeluarkan' ? 'selected' : '' }}>Dikeluarkan</option>
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Program PIP / Bantuan Afirmasi -->
                <div class="mt-4 p-4 bg-amber-50/70 border border-amber-200 rounded-2xl">
                    @php $isPip = old('is_pip', $student->is_pip); @endphp
                    <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-amber-900 text-xs">
                        <input type="checkbox" name="is_pip" value="1" {{ $isPip ? 'checked' : '' }} onchange="document.getElementById('pip-container-edit').classList.toggle('hidden', !this.checked)" class="w-4 h-4 text-amber-600 rounded">
                        <span>Peserta Didik Penerima Bantuan Program Indonesia Pintar (PIP / KIP)</span>
                    </label>
                    <div id="pip-container-edit" class="{{ $isPip ? '' : 'hidden' }} mt-3 pt-3 border-t border-amber-200 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor KIP / Kartu Indonesia Pintar</label>
                            <input type="text" name="pip_number" value="{{ old('pip_number', $student->pip_number) }}" placeholder="KIP-xxxxxxxx" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-mono">
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
                            <input type="text" name="father_name" value="{{ old('father_name', $student->family?->father_name) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">NIK Ayah</label>
                            <input type="text" name="father_nik" value="{{ old('father_nik', $student->family?->father_nik) }}" maxlength="16" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pendidikan Terakhir</label>
                            @php $fEdu = old('father_education', $student->family?->father_education ?? 'SMA / Sederajat'); @endphp
                            <select name="father_education" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                                <option value="">-- Pilih Pendidikan --</option>
                                <option value="SD / Sederajat" {{ $fEdu === 'SD / Sederajat' ? 'selected' : '' }}>SD / Sederajat</option>
                                <option value="SMP / Sederajat" {{ $fEdu === 'SMP / Sederajat' ? 'selected' : '' }}>SMP / Sederajat</option>
                                <option value="SMA / Sederajat" {{ $fEdu === 'SMA / Sederajat' ? 'selected' : '' }}>SMA / Sederajat</option>
                                <option value="D1 / D2 / D3" {{ $fEdu === 'D1 / D2 / D3' ? 'selected' : '' }}>D1 / D2 / D3</option>
                                <option value="D4 / S1" {{ $fEdu === 'D4 / S1' ? 'selected' : '' }}>D4 / S1</option>
                                <option value="S2 / S3" {{ $fEdu === 'S2 / S3' ? 'selected' : '' }}>S2 / S3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pekerjaan Ayah</label>
                            <input type="text" name="father_occupation" value="{{ old('father_occupation', $student->family?->father_occupation) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Penghasilan Bulanan</label>
                            @php $fInc = old('father_income', $student->family?->father_income ?? 'Rp 1.000.000 - Rp 2.000.000'); @endphp
                            <select name="father_income" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                                <option value="< Rp 1.000.000" {{ $fInc === '< Rp 1.000.000' ? 'selected' : '' }}>&lt; Rp 1.000.000</option>
                                <option value="Rp 1.000.000 - Rp 2.000.000" {{ $fInc === 'Rp 1.000.000 - Rp 2.000.000' ? 'selected' : '' }}>Rp 1.000.000 - Rp 2.000.000</option>
                                <option value="Rp 2.000.000 - Rp 5.000.000" {{ $fInc === 'Rp 2.000.000 - Rp 5.000.000' ? 'selected' : '' }}>Rp 2.000.000 - Rp 5.000.000</option>
                                <option value="> Rp 5.000.000" {{ $fInc === '> Rp 5.000.000' ? 'selected' : '' }}>&gt; Rp 5.000.000</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon / HP Ayah</label>
                            <input type="text" name="father_phone" value="{{ old('father_phone', $student->family?->father_phone) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
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
                            <input type="text" name="mother_name" value="{{ old('mother_name', $student->family?->mother_name) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">NIK Ibu</label>
                            <input type="text" name="mother_nik" value="{{ old('mother_nik', $student->family?->mother_nik) }}" maxlength="16" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pendidikan Terakhir</label>
                            @php $mEdu = old('mother_education', $student->family?->mother_education ?? 'SMA / Sederajat'); @endphp
                            <select name="mother_education" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                                <option value="">-- Pilih Pendidikan --</option>
                                <option value="SD / Sederajat" {{ $mEdu === 'SD / Sederajat' ? 'selected' : '' }}>SD / Sederajat</option>
                                <option value="SMP / Sederajat" {{ $mEdu === 'SMP / Sederajat' ? 'selected' : '' }}>SMP / Sederajat</option>
                                <option value="SMA / Sederajat" {{ $mEdu === 'SMA / Sederajat' ? 'selected' : '' }}>SMA / Sederajat</option>
                                <option value="D1 / D2 / D3" {{ $mEdu === 'D1 / D2 / D3' ? 'selected' : '' }}>D1 / D2 / D3</option>
                                <option value="D4 / S1" {{ $mEdu === 'D4 / S1' ? 'selected' : '' }}>D4 / S1</option>
                                <option value="S2 / S3" {{ $mEdu === 'S2 / S3' ? 'selected' : '' }}>S2 / S3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pekerjaan Ibu</label>
                            <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $student->family?->mother_occupation ?? 'Ibu Rumah Tangga') }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Penghasilan Bulanan</label>
                            @php $mInc = old('mother_income', $student->family?->mother_income ?? 'Tidak Berpenghasilan'); @endphp
                            <select name="mother_income" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                                <option value="Tidak Berpenghasilan" {{ $mInc === 'Tidak Berpenghasilan' ? 'selected' : '' }}>Tidak Berpenghasilan</option>
                                <option value="< Rp 1.000.000" {{ $mInc === '< Rp 1.000.000' ? 'selected' : '' }}>&lt; Rp 1.000.000</option>
                                <option value="Rp 1.000.000 - Rp 2.000.000" {{ $mInc === 'Rp 1.000.000 - Rp 2.000.000' ? 'selected' : '' }}>Rp 1.000.000 - Rp 2.000.000</option>
                                <option value="Rp 2.000.000 - Rp 5.000.000" {{ $mInc === 'Rp 2.000.000 - Rp 5.000.000' ? 'selected' : '' }}>Rp 2.000.000 - Rp 5.000.000</option>
                                <option value="> Rp 5.000.000" {{ $mInc === '> Rp 5.000.000' ? 'selected' : '' }}>&gt; Rp 5.000.000</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon / HP Ibu</label>
                            <input type="text" name="mother_phone" value="{{ old('mother_phone', $student->family?->mother_phone) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
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
                            <input type="text" name="guardian_name" value="{{ old('guardian_name', $student->family?->guardian_name) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">NIK Wali</label>
                            <input type="text" name="guardian_nik" value="{{ old('guardian_nik', $student->family?->guardian_nik) }}" maxlength="16" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pendidikan Wali</label>
                            <input type="text" name="guardian_education" value="{{ old('guardian_education', $student->family?->guardian_education) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pekerjaan Wali</label>
                            <input type="text" name="guardian_occupation" value="{{ old('guardian_occupation', $student->family?->guardian_occupation) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Penghasilan Wali</label>
                            <input type="text" name="guardian_income" value="{{ old('guardian_income', $student->family?->guardian_income) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon / HP Wali</label>
                            <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $student->family?->guardian_phone) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 sm:px-8 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">Perubahan data akan otomatis diperbarui di seluruh dokumen cetak Buku Induk & Demografi.</span>
            <div class="flex items-center space-x-3">
                <a href="{{ route('students.show', $student) }}" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Perbarui Data Buku Induk Siswa
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
