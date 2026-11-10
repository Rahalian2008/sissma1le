@extends('layouts.app')

@section('title', 'Buku Induk Siswa - ' . $student->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('buku-induk.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Lembar Buku Induk Siswa Digital</h1>
                <p class="text-xs text-slate-500">Nomor Induk: {{ $student->nis }} &bull; Kode Verifikasi Digital Terdaftar</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('buku-induk.print', $student) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar Induk (PDF)
            </a>
        </div>
    </div>

    <!-- Official Ledger Card -->
    <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-200 space-y-8">
        <!-- School Header (KOP) -->
        <div class="border-b-2 border-slate-900 pb-4 flex items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-900 text-white flex items-center justify-center font-black text-xl tracking-wider shrink-0">
                    {{ strtoupper(substr($school->name, 0, 4)) }}
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase text-slate-900 tracking-wide">{{ strtoupper($school->name) }}</h2>
                    <p class="text-xs text-slate-600">NPSN: {{ $school->npsn }} &bull; NSS: {{ $school->nss ?? '-' }} &bull; Akreditasi: {{ $school->accreditation }}</p>
                    <p class="text-[11px] text-slate-500">{{ $school->address }} &bull; Telp: {{ $school->phone }} &bull; Email: {{ $school->email }}</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <div class="p-2 border border-slate-200 rounded-xl inline-block bg-slate-50">
                    {!! $qrCodeSvg !!}
                </div>
                <div class="text-[9px] text-slate-400 font-mono mt-1">TOKEN VERIFIKASI ASLI</div>
            </div>
        </div>

        <div class="text-center">
            <h3 class="text-base font-extrabold uppercase tracking-widest text-slate-900">LEMBAR BUKU INDUK SISWA</h3>
            <p class="text-xs text-slate-500">Nomor Registrasi Buku Induk: <span class="font-mono font-bold text-slate-800">REG-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}-{{ $student->nis }}</span></p>
        </div>

        <!-- Bagian A: Keterangan Pribadi Siswa -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-900 bg-blue-50 px-3 py-1.5 rounded-lg">A. KETERANGAN TENTANG DIRI PESERTA DIDIK</h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-xs">
                <div class="md:col-span-3 space-y-2.5">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">1. Nama Lengkap</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $student->name }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">2. Nomor Induk Siswa (NIS)</span>
                        <span class="col-span-2 font-mono font-semibold text-slate-900">: {{ $student->nis }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">3. NISN / NIK Siswa</span>
                        <span class="col-span-2 font-mono text-slate-900">: {{ $student->nisn ?? '-' }} / {{ $student->nik ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">4. Nomor Kartu Keluarga (No. KK)</span>
                        <span class="col-span-2 font-mono text-slate-900">: {{ $student->family_card_number ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">5. Jenis Kelamin</span>
                        <span class="col-span-2 text-slate-900">: {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">6. Tempat, Tanggal Lahir</span>
                        <span class="col-span-2 text-slate-900">: {{ $student->birth_place ?? '-' }}, {{ $student->birth_date?->translatedFormat('d F Y') ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">7. Agama & Kepercayaan</span>
                        <span class="col-span-2 text-slate-900">: {{ $student->religion ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">8. Golongan Darah</span>
                        <span class="col-span-2 text-slate-900">: {{ $student->blood_type ?? 'Tidak Tahu' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">9. Keadaan Jasmani (Tinggi / Berat)</span>
                        <span class="col-span-2 text-slate-900">: {{ $student->height ? $student->height.' cm' : '-' }} / {{ $student->weight ? $student->weight.' kg' : '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">10. Anak Ke- / Dari Bersaudara</span>
                        <span class="col-span-2 text-slate-900">: Ke-{{ $student->child_order ?? 1 }} dari {{ $student->siblings_count ?? 1 }} bersaudara</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">11. Alamat Tempat Tinggal</span>
                        <span class="col-span-2 text-slate-900">: {{ $student->address ?? '-' }} {{ $student->rt_rw ? '(RT/RW: '.$student->rt_rw.')' : '' }}, Kec. {{ $student->residence_district ?? 'Lengkong' }}, Kode Pos {{ $student->postal_code ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">12. Tinggal Bersama</span>
                        <span class="col-span-2 text-slate-900">: {{ $student->living_with ?? 'Bersama Orang Tua' }} (Jarak ke sekolah: {{ $student->distance_to_school ?? '-' }}, Moda: {{ $student->transportation ?? '-' }})</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">13. Nomor Telepon / HP Siswa</span>
                        <span class="col-span-2 text-slate-900">: {{ $student->phone ?? '-' }} (Email: {{ $student->email ?? '-' }})</span>
                    </div>
                </div>

                <!-- Pas Foto Box -->
                <div class="md:col-span-1 flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 text-center min-h-[160px]">
                    <span class="text-3xl mb-2">📸</span>
                    <span class="text-[11px] font-bold text-slate-600">PAS FOTO</span>
                    <span class="text-[9px] text-slate-400">Ukuran 3 x 4 cm</span>
                </div>
            </div>
        </div>

        <!-- Bagian B: Keterangan Masuk Sekolah -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-900 bg-blue-50 px-3 py-1.5 rounded-lg">B. KETERANGAN PENERIMAAN</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                    <span class="text-slate-500">1. Asal Sekolah (SMP/MTs)</span>
                    <span class="col-span-2 font-semibold text-slate-900">: {{ $student->school_origin ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                    <span class="text-slate-500">2. No. Ijazah & Ujian SMP</span>
                    <span class="col-span-2 text-slate-900">: {{ $student->junior_diploma_number ?? '-' }} / {{ $student->junior_exam_number ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                    <span class="text-slate-500">3. Tanggal Masuk</span>
                    <span class="col-span-2 text-slate-900">: {{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                    <span class="text-slate-500">4. Jalur Masuk PPDB</span>
                    <span class="col-span-2 font-semibold text-blue-800">: {{ $student->entry_path ?? 'Zonasi' }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                    <span class="text-slate-500">5. Diterima di Kelas</span>
                    <span class="col-span-2 text-slate-900">: Kelas {{ $student->admission_class ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                    <span class="text-slate-500">6. Tingkat & Rombel Kini</span>
                    <span class="col-span-2 font-semibold text-blue-700">: Kelas {{ $student->current_class_level }} {{ $student->current_rombel }}</span>
                </div>
                <div class="grid grid-cols-3 py-1 border-b border-slate-100 md:col-span-2">
                    <span class="text-slate-500">7. Program Bantuan PIP</span>
                    <span class="col-span-2 text-slate-900">: {{ $student->is_pip ? 'Penerima PIP (No. KIP: '.($student->pip_number ?? '-').')' : 'Bukan Penerima PIP' }}</span>
                </div>
            </div>
        </div>

        <!-- Bagian C: Keterangan Orang Tua / Wali -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-900 bg-blue-50 px-3 py-1.5 rounded-lg">C. KETERANGAN ORANG TUA / WALI SISWA</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <div class="space-y-2 p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="font-bold text-slate-900 border-b border-slate-200 pb-1">Data Ayah Kandung:</div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Nama Lengkap</span><span class="col-span-2 font-semibold text-slate-900">: {{ $student->family?->father_name ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">NIK Ayah</span><span class="col-span-2 font-mono text-slate-900">: {{ $student->family?->father_nik ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Pendidikan</span><span class="col-span-2 text-slate-900">: {{ $student->family?->father_education ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Pekerjaan</span><span class="col-span-2 text-slate-900">: {{ $student->family?->father_occupation ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Penghasilan</span><span class="col-span-2 text-slate-900">: {{ $student->family?->father_income ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">No. Telepon/HP</span><span class="col-span-2 text-slate-900">: {{ $student->family?->father_phone ?? '-' }}</span></div>
                </div>
                <div class="space-y-2 p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="font-bold text-slate-900 border-b border-slate-200 pb-1">Data Ibu Kandung:</div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Nama Lengkap</span><span class="col-span-2 font-semibold text-slate-900">: {{ $student->family?->mother_name ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">NIK Ibu</span><span class="col-span-2 font-mono text-slate-900">: {{ $student->family?->mother_nik ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Pendidikan</span><span class="col-span-2 text-slate-900">: {{ $student->family?->mother_education ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Pekerjaan</span><span class="col-span-2 text-slate-900">: {{ $student->family?->mother_occupation ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Penghasilan</span><span class="col-span-2 text-slate-900">: {{ $student->family?->mother_income ?? '-' }}</span></div>
                    <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">No. Telepon/HP</span><span class="col-span-2 text-slate-900">: {{ $student->family?->mother_phone ?? '-' }}</span></div>
                </div>
                @if($student->family?->guardian_name)
                    <div class="space-y-2 p-3.5 bg-slate-50 rounded-xl border border-slate-100 md:col-span-2">
                        <div class="font-bold text-slate-900 border-b border-slate-200 pb-1">Data Wali Siswa:</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Nama Wali</span><span class="col-span-2 font-semibold text-slate-900">: {{ $student->family->guardian_name }}</span></div>
                            <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">Pekerjaan</span><span class="col-span-2 text-slate-900">: {{ $student->family->guardian_occupation ?? '-' }}</span></div>
                            <div class="grid grid-cols-3 py-0.5"><span class="text-slate-500">No. Telepon/HP</span><span class="col-span-2 text-slate-900">: {{ $student->family->guardian_phone ?? '-' }}</span></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bagian D: Riwayat Prestasi & Kedisiplinan -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-900 bg-blue-50 px-3 py-1.5 rounded-lg">D. CATATAN PRESTASI DAN KEDISIPLINAN</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="font-bold text-slate-800 block mb-1">Catatan Prestasi:</span>
                    @if($student->achievements->isEmpty())
                        <span class="text-slate-400 italic">Belum ada catatan prestasi terdata.</span>
                    @else
                        <ul class="list-disc list-inside space-y-1 text-slate-700">
                            @foreach($student->achievements as $a)
                                <li>Juara {{ $a->rank }} {{ $a->name }} ({{ $a->level }}) - {{ $a->date?->format('d/m/Y') }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="font-bold text-slate-800 block mb-1">Catatan Kedisiplinan:</span>
                    @if($student->violations->isEmpty())
                        <span class="text-emerald-700 font-medium">Siswa disiplin, tidak ada catatan pelanggaran tata tertib.</span>
                    @else
                        <ul class="list-disc list-inside space-y-1 text-slate-700">
                            @foreach($student->violations as $v)
                                <li>{{ $v->violation_name }} (+{{ $v->points }} poin) - {{ $v->violation_date?->format('d/m/Y') }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tanda Tangan & Pengesahan -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 gap-8 text-xs text-center">
            <div>
                <p class="text-slate-500">Mengetahui,</p>
                <p class="font-bold text-slate-900">Orang Tua / Wali Siswa</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">{{ $student->family?->father_name ?? $student->family?->mother_name ?? '( ........................................ )' }}</p>
            </div>
            <div>
                <p class="text-slate-500">{{ $school->city ?? 'Kota' }}, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-900">Kepala Sekolah,</p>
                <p class="font-bold text-slate-900 underline">{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</p>
                <p class="text-[11px] text-slate-500">NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
