@extends('layouts.app')

@section('title', 'Buku Induk Digital - ' . $student->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('bk.students.index') }}" class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-0.5">
                    <a href="{{ route('bk.dashboard') }}" class="hover:text-indigo-600 font-medium">Bimbingan Konseling</a>
                    <span>/</span>
                    <a href="{{ route('bk.students.index') }}" class="hover:text-indigo-600 font-medium">Data Siswa</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-white font-bold">Buku Induk Digital</span>
                </div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white">Lembar Buku Induk Siswa Digital</h1>
                <p class="text-xs text-slate-500">Nomor Registrasi: <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">REG-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}-{{ $student->nis }}</span></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.students.buku-induk.print', $student) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-md shadow-blue-600/20 transition transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Lembar Buku Induk (PDF)</span>
            </a>
            <a href="{{ route('bk.student.profile360', $student) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 font-bold text-xs transition">
                <span>👤 Profil 360°</span>
            </a>
        </div>
    </div>

    <!-- Official Ledger Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-8">
        <!-- School Header (KOP) -->
        <div class="border-b-2 border-slate-900 dark:border-slate-700 pb-4 flex items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-900 text-white flex items-center justify-center font-black text-xl tracking-wider shrink-0 shadow-md">
                    {{ strtoupper(substr($school->name, 0, 4)) }}
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase text-slate-900 dark:text-white tracking-wide">{{ strtoupper($school->name) }}</h2>
                    <p class="text-xs text-slate-600 dark:text-slate-300">NPSN: {{ $school->npsn }} &bull; NSS: {{ $school->nss ?? '-' }} &bull; Akreditasi: {{ $school->accreditation }}</p>
                    <p class="text-[11px] text-slate-500">{{ $school->address }} &bull; Telp: {{ $school->phone }} &bull; Email: {{ $school->email }}</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <div class="p-2 border border-slate-200 dark:border-slate-700 rounded-xl inline-block bg-slate-50 dark:bg-slate-800">
                    {!! $qrCodeSvg !!}
                </div>
                <div class="text-[9px] text-slate-400 font-mono mt-1">TOKEN VERIFIKASI DIGITAL</div>
            </div>
        </div>

        <div class="text-center">
            <h3 class="text-base sm:text-lg font-black uppercase tracking-widest text-slate-900 dark:text-white">LEMBAR BUKU INDUK SISWA</h3>
            <p class="text-xs text-slate-500 mt-0.5">Integrasi Master Kesiswaan &bull; Modul Bimbingan &amp; Konseling SIS-SMA1LE</p>
        </div>

        <!-- Bagian A: Keterangan Pribadi Siswa -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-900 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/50 px-3.5 py-2 rounded-xl border border-blue-200/50 dark:border-blue-900/50">
                A. KETERANGAN TENTANG DIRI PESERTA DIDIK
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-xs">
                <div class="md:col-span-3 space-y-2">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">1. Nama Lengkap</span>
                        <span class="col-span-2 font-bold text-slate-900 dark:text-white">: {{ $student->name }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">2. Nomor Induk Siswa (NIS)</span>
                        <span class="col-span-2 font-mono font-bold text-indigo-600 dark:text-indigo-400">: {{ $student->nis }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">3. NISN / NIK Siswa</span>
                        <span class="col-span-2 font-mono text-slate-800 dark:text-slate-200">: {{ $student->nisn ?? '-' }} / {{ $student->nik ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">4. Nomor Kartu Keluarga (No. KK)</span>
                        <span class="col-span-2 font-mono text-slate-800 dark:text-slate-200">: {{ $student->family_card_number ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">5. Jenis Kelamin</span>
                        <span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->gender === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">6. Tempat, Tanggal Lahir</span>
                        <span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->birth_place ?? '-' }}, {{ $student->birth_date?->translatedFormat('d F Y') ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">7. Agama &amp; Kepercayaan</span>
                        <span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->religion ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">8. Golongan Darah</span>
                        <span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->blood_type ?: 'Belum Terdata' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">9. Keadaan Jasmani (Tinggi / Berat)</span>
                        <span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->height ? $student->height.' cm' : '-' }} / {{ $student->weight ? $student->weight.' kg' : '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">10. Anak Ke- / Dari Bersaudara</span>
                        <span class="col-span-2 text-slate-800 dark:text-slate-200">: Ke-{{ $student->child_order ?? 1 }} dari {{ $student->siblings_count ?? 1 }} bersaudara</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">11. Alamat Tempat Tinggal</span>
                        <span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->address ?? '-' }} {{ $student->rt_rw ? '(RT/RW: '.$student->rt_rw.')' : '' }}, Kec. {{ $student->residence_district ?? 'Lengkong' }}, Kode Pos {{ $student->postal_code ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">12. Tinggal Bersama / Jarak</span>
                        <span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->living_with ?? 'Bersama Orang Tua' }} &bull; Jarak: {{ $student->distance_to_school ?? '-' }} &bull; Transportasi: {{ $student->transportation ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">13. Telepon / HP Siswa</span>
                        <span class="col-span-2 text-slate-800 dark:text-slate-200 font-mono">: {{ $student->phone ?? '-' }} {{ $student->email ? '&bull; Email: '.$student->email : '' }}</span>
                    </div>
                </div>

                <!-- Pas Foto Box -->
                <div class="md:col-span-1 flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl bg-slate-50 dark:bg-slate-800 text-center min-h-[180px]">
                    @if(!empty($student->photo) || !empty($student->face_photo_path))
                        <img src="{{ asset($student->photo ?: $student->face_photo_path) }}" alt="Foto Siswa" class="w-28 h-36 object-cover rounded-xl shadow-xs">
                    @else
                        <span class="text-3xl mb-2">📸</span>
                        <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">PAS FOTO</span>
                        <span class="text-[9px] text-slate-400">Ukuran 3 x 4 cm</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bagian B: Keterangan Masuk Sekolah -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-900 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/50 px-3.5 py-2 rounded-xl border border-blue-200/50 dark:border-blue-900/50">
                B. KETERANGAN PENERIMAAN &amp; RIWAYAT PENDIDIKAN
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">1. Asal Sekolah (SMP/MTs)</span>
                    <span class="col-span-2 font-semibold text-slate-900 dark:text-white">: {{ $student->school_origin ?: ($student->previous_school ?: '-') }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">2. No. Ijazah / SKHUN SMP</span>
                    <span class="col-span-2 font-mono text-slate-800 dark:text-slate-200">: {{ $student->junior_diploma_number ?? '-' }} / {{ $student->junior_exam_number ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">3. Tanggal Masuk</span>
                    <span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">4. Jalur Masuk PPDB</span>
                    <span class="col-span-2 font-bold text-indigo-600 dark:text-indigo-400">: {{ $student->entry_path ?? 'Zonasi' }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">5. Diterima di Kelas Awal</span>
                    <span class="col-span-2 text-slate-800 dark:text-slate-200">: Kelas {{ $student->admission_class ?? $student->current_class_level }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">6. Rombel Saat Ini</span>
                    <span class="col-span-2 font-bold text-slate-900 dark:text-white">: {{ $student->schoolClass?->name ?? ('Kelas ' . $student->current_class_level . ' ' . $student->current_rombel) }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100 dark:border-slate-800 md:col-span-2">
                    <span class="text-slate-500">7. Bantuan Pendidikan PIP</span>
                    <span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->is_pip ? 'Penerima PIP (No. KIP: '.($student->pip_number ?? '-').')' : 'Bukan Penerima Bantuan PIP' }}</span>
                </div>
            </div>
        </div>

        <!-- Bagian C: Keterangan Orang Tua / Wali -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-900 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/50 px-3.5 py-2 rounded-xl border border-blue-200/50 dark:border-blue-900/50">
                C. KETERANGAN ORANG TUA KANDUNG &amp; WALI SISWA
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                <!-- Ayah -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                    <div class="font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-1.5 flex items-center gap-1.5">
                        <span>👨</span> Data Ayah Kandung:
                    </div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Nama Lengkap</span><span class="col-span-2 font-bold text-slate-900 dark:text-white">: {{ $student->family?->father_name ?: ($student->father_name ?: '-') }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">NIK Ayah</span><span class="col-span-2 font-mono text-slate-800 dark:text-slate-200">: {{ $student->family?->father_nik ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Pendidikan</span><span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->family?->father_education ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Pekerjaan</span><span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->family?->father_occupation ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Penghasilan</span><span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->family?->father_income ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">No. HP / Telp</span><span class="col-span-2 font-mono text-slate-800 dark:text-slate-200">: {{ $student->family?->father_phone ?? '-' }}</span></div>
                </div>

                <!-- Ibu -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                    <div class="font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-1.5 flex items-center gap-1.5">
                        <span>👩</span> Data Ibu Kandung:
                    </div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Nama Lengkap</span><span class="col-span-2 font-bold text-slate-900 dark:text-white">: {{ $student->family?->mother_name ?: ($student->mother_name ?: '-') }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">NIK Ibu</span><span class="col-span-2 font-mono text-slate-800 dark:text-slate-200">: {{ $student->family?->mother_nik ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Pendidikan</span><span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->family?->mother_education ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Pekerjaan</span><span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->family?->mother_occupation ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Penghasilan</span><span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->family?->mother_income ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">No. HP / Telp</span><span class="col-span-2 font-mono text-slate-800 dark:text-slate-200">: {{ $student->family?->mother_phone ?? '-' }}</span></div>
                </div>

                @if($student->family?->guardian_name)
                    <!-- Wali -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2 md:col-span-2">
                        <div class="font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-1.5">
                            Data Wali Murid:
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Nama Lengkap</span><span class="col-span-2 font-bold text-slate-900 dark:text-white">: {{ $student->family->guardian_name }}</span></div>
                            <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Pekerjaan</span><span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->family->guardian_occupation ?? '-' }}</span></div>
                            <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">No. HP</span><span class="col-span-2 font-mono text-slate-800 dark:text-slate-200">: {{ $student->family->guardian_phone ?? '-' }}</span></div>
                            <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Alamat</span><span class="col-span-2 text-slate-800 dark:text-slate-200">: {{ $student->family->guardian_address ?? '-' }}</span></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bagian D: Rekam Bimbingan Konseling & Asesmen Psikotes -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-purple-900 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/50 px-3.5 py-2 rounded-xl border border-purple-200/50 dark:border-purple-900/50 flex items-center justify-between">
                <span>D. REKAM LAYANAN BIMBINGAN &amp; KONSELING (BK)</span>
                <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400">{{ $student->counselingSessions->count() }} Sesi Terdata</span>
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <!-- Gaya Belajar -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Gaya Belajar (VAK)</span>
                    @if($student->latestVakResult)
                        <div class="font-bold text-indigo-600 dark:text-indigo-400 text-sm">{{ $student->latestVakResult->profile_label }}</div>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-3">{{ $student->latestVakResult->analysis_summary }}</p>
                    @else
                        <span class="text-slate-400 italic">Belum melaksanakan tes VAK</span>
                    @endif
                </div>

                <!-- Minat Karir -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Minat Karir (RIASEC)</span>
                    @if($student->latestRiasecResult)
                        <div class="font-bold text-purple-600 dark:text-purple-400 text-sm">{{ $student->latestRiasecResult->profile_label }}</div>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-3">{{ $student->latestRiasecResult->analysis_summary }}</p>
                    @else
                        <span class="text-slate-400 italic">Belum pemetaan RIASEC</span>
                    @endif
                </div>

                <!-- Bimbingan Karir -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Peminatan Pasca Kelulusan</span>
                    @php
                        $latestCareer = $student->counselingSessions->whereNotNull('career_path')->sortByDesc('date')->first();
                    @endphp
                    @if($latestCareer)
                        <div class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                            {{ $latestCareer->career_path === 'KULIAH' ? 'Kuliah / Perguruan Tinggi' : 'Bekerja / Wirausaha' }}
                        </div>
                        @if($latestCareer->career_path === 'KULIAH')
                            <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-1">
                                {{ $latestCareer->career_target_university }} &bull; {{ $latestCareer->career_target_major }}
                            </p>
                        @endif
                    @else
                        <span class="text-slate-400 italic">Belum terdata peminatan karir</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Bawah -->
        <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <a href="{{ route('bk.students.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 transition">
                &larr; Kembali ke Daftar Siswa
            </a>
            <a href="{{ route('bk.students.buku-induk.print', $student) }}" target="_blank"
               class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Lembar Buku Induk Resmi</span>
            </a>
        </div>
    </div>
</div>
@endsection
