@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $student->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('students.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">{{ $student->name }}</h1>
                <p class="text-xs text-slate-500">NIS: {{ $student->nis }} | NISN: {{ $student->nisn ?? '-' }} | Kelas {{ $student->current_class_level }} {{ $student->current_rombel }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('buku-induk.show', $student) }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Buku Induk Digital
            </a>
            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Profil
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile summary card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-linear-to-tr from-blue-600 to-indigo-600 text-white text-3xl font-bold flex items-center justify-center shadow-md">
                    {{ substr($student->name, 0, 1) }}
                </div>
                <h2 class="mt-4 text-base font-bold text-slate-900">{{ $student->name }}</h2>
                <span class="inline-block mt-1 px-3 py-0.5 rounded-full text-xs font-semibold
                    @if($student->status === 'Aktif') bg-emerald-100 text-emerald-800
                    @elseif($student->status === 'Lulus') bg-blue-100 text-blue-800
                    @else bg-amber-100 text-amber-800 @endif">
                    {{ $student->status }}
                </span>

                <div class="mt-6 pt-6 border-t border-slate-100 text-left space-y-2.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">NIS / NISN</span>
                        <span class="font-semibold text-slate-900 font-mono">{{ $student->nis }} / {{ $student->nisn ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">NIK Siswa</span>
                        <span class="font-semibold text-slate-900 font-mono">{{ $student->nik ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. Kartu Keluarga</span>
                        <span class="font-semibold text-slate-900 font-mono">{{ $student->family_card_number ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jenis Kelamin</span>
                        <span class="font-semibold text-slate-900">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tempat, Tgl Lahir</span>
                        <span class="font-semibold text-slate-900">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Agama</span>
                        <span class="font-semibold text-slate-900">{{ $student->religion ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Golongan Darah</span>
                        <span class="font-semibold text-slate-900">{{ $student->blood_type ?? 'Tidak Tahu' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tinggi / Berat Badan</span>
                        <span class="font-semibold text-slate-900">{{ $student->height ? $student->height.' cm' : '-' }} / {{ $student->weight ? $student->weight.' kg' : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Anak Ke- / Bersaudara</span>
                        <span class="font-semibold text-slate-900">Ke-{{ $student->child_order ?? 1 }} dari {{ $student->siblings_count ?? 1 }} bersaudara</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. Telepon / HP</span>
                        <span class="font-semibold text-slate-900">{{ $student->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Email</span>
                        <span class="font-semibold text-slate-900">{{ $student->email ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Family Information Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Data Orang Tua / Wali
                </h3>
                <div class="space-y-4 text-xs">
                    <div>
                        <div class="text-slate-500 font-semibold">Data Ayah Kandung:</div>
                        <div class="font-bold text-slate-900">{{ $student->family?->father_name ?? '-' }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            NIK: {{ $student->family?->father_nik ?? '-' }} &bull; Pendidikan: {{ $student->family?->father_education ?? '-' }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Pekerjaan: {{ $student->family?->father_occupation ?? '-' }} &bull; Penghasilan: {{ $student->family?->father_income ?? '-' }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Telp/HP: {{ $student->family?->father_phone ?? '-' }}
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-100">
                        <div class="text-slate-500 font-semibold">Data Ibu Kandung:</div>
                        <div class="font-bold text-slate-900">{{ $student->family?->mother_name ?? '-' }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            NIK: {{ $student->family?->mother_nik ?? '-' }} &bull; Pendidikan: {{ $student->family?->mother_education ?? '-' }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Pekerjaan: {{ $student->family?->mother_occupation ?? '-' }} &bull; Penghasilan: {{ $student->family?->mother_income ?? '-' }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Telp/HP: {{ $student->family?->mother_phone ?? '-' }}
                        </div>
                    </div>
                    @if($student->family?->guardian_name)
                        <div class="pt-3 border-t border-slate-100">
                            <div class="text-slate-500 font-semibold">Data Wali Siswa:</div>
                            <div class="font-bold text-slate-900">{{ $student->family->guardian_name }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                Pekerjaan: {{ $student->family->guardian_occupation ?? '-' }} &bull; Telp: {{ $student->family->guardian_phone ?? '-' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detail tabs/sections -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Academic and Admission Details -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Informasi Akademik & Penerimaan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-slate-500 block">Asal Sekolah:</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $student->school_origin ?? '-' }}</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">No. Ijazah SMP: {{ $student->junior_diploma_number ?? '-' }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-slate-500 block">Jalur Masuk PPDB:</span>
                        <span class="font-bold text-blue-700 text-sm">{{ $student->entry_path ?? 'Zonasi' }}</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Tgl Masuk: {{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-slate-500 block">Tingkat & Rombel:</span>
                        <span class="font-bold text-slate-800 text-sm">Kelas {{ $student->current_class_level }} {{ $student->current_rombel ?? '' }}</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Diterima Awal: Kelas {{ $student->admission_class ?? '-' }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-slate-500 block">Bantuan PIP / KIP:</span>
                        @if($student->is_pip)
                            <span class="font-bold text-emerald-700 text-sm">Penerima PIP (Aktif)</span>
                            <span class="text-[11px] text-slate-500 block mt-0.5 font-mono">No. KIP: {{ $student->pip_number ?? '-' }}</span>
                        @else
                            <span class="font-bold text-slate-500 text-sm">Bukan Penerima PIP</span>
                        @endif
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl sm:col-span-2">
                        <span class="text-slate-500 block">Domisili Tempat Tinggal:</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $student->address ?? '-' }} {{ $student->rt_rw ? '(RT/RW: '.$student->rt_rw.')' : '' }}</span>
                        <span class="text-[11px] text-slate-500 block mt-0.5">
                            Kecamatan {{ $student->residence_district ?? 'Lengkong' }}, Kode Pos {{ $student->postal_code ?? '-' }} &bull; Tinggal: {{ $student->living_with ?? 'Bersama Orang Tua' }}
                        </span>
                        <span class="text-[11px] text-slate-400 block">
                            Jarak ke Sekolah: {{ $student->distance_to_school ?? '-' }} &bull; Transportasi: {{ $student->transportation ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Prestasi & Penghargaan -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="text-amber-500">🏆</span>
                        Prestasi & Penghargaan
                    </h3>
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 font-bold text-xs rounded-full border border-amber-200">{{ $student->achievements->count() }} Prestasi</span>
                </div>
                @if($student->achievements->isEmpty())
                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada catatan prestasi.</p>
                @else
                    <div class="divide-y divide-slate-100 text-xs">
                        @foreach($student->achievements as $ach)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800">{{ $ach->name }}</span>
                                    <div class="text-[11px] text-slate-500">{{ $ach->category }} &bull; Juara {{ $ach->rank }} (Tingkat {{ $ach->level }}) - {{ $ach->organizer }}</div>
                                </div>
                                <span class="text-slate-400 text-[11px]">{{ $ach->date?->format('d/m/Y') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Kedisiplinan & Pelanggaran -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="text-rose-500">⚠️</span>
                        Kedisiplinan & Pelanggaran
                    </h3>
                    @php $totalPoints = $student->violations->sum('points'); @endphp
                    <span class="px-2.5 py-0.5 {{ $totalPoints > 50 ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }} font-bold text-xs rounded-full">
                        Total Poin: {{ $totalPoints }}
                    </span>
                </div>
                @if($student->violations->isEmpty())
                    <p class="text-xs text-slate-400 py-3 text-center">Tidak ada catatan pelanggaran tata tertib (Siswa Disiplin).</p>
                @else
                    <div class="divide-y divide-slate-100 text-xs">
                        @foreach($student->violations as $vio)
                            <div class="py-2.5 flex items-start justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800">{{ $vio->violation_name }}</span>
                                        <span class="px-1.5 py-0.2 bg-rose-50 text-rose-700 font-bold text-[10px] rounded border border-rose-200">+{{ $vio->points }} poin</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Tindakan: {{ $vio->action_taken ?? '-' }} | Status: {{ $vio->resolution_status }}</p>
                                </div>
                                <span class="text-slate-400 text-[11px]">{{ $vio->violation_date?->format('d/m/Y') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Riwayat Mutasi Siswa -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Riwayat Mutasi & Perubahan Status
                    </h3>
                </div>
                @if($student->mutations->isEmpty())
                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada riwayat mutasi.</p>
                @else
                    <div class="divide-y divide-slate-100 text-xs">
                        @foreach($student->mutations as $mut)
                            <div class="py-2.5 flex items-start justify-between">
                                <div>
                                    <span class="font-bold text-slate-800">{{ $mut->mutation_type }}</span>
                                    <div class="text-[11px] text-slate-500">Sekolah Asal/Tujuan: {{ $mut->school_target_or_origin ?? '-' }} | Alasan: {{ $mut->reason ?? '-' }}</div>
                                </div>
                                <span class="text-slate-400 text-[11px]">{{ $mut->mutation_date?->format('d/m/Y') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
