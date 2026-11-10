@extends('layouts.app')

@section('title', 'Lembar Buku Induk Pegawai - ' . $employee->formatted_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div class="flex items-center space-x-4">
            <img src="{{ $employee->photo_url }}" alt="{{ $employee->name }}" class="w-16 h-16 rounded-2xl border-2 border-slate-200 object-cover shadow-xs" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\' fill=\'%23cbd5e1\'><rect width=\'64\' height=\'64\' rx=\'16\'/></svg>'">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $employee->isPns() ? 'bg-blue-100 text-blue-800 border border-blue-200' : ($employee->isPppk() ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200') }}">
                        {{ $employee->employment_status }} - {{ $employee->employment_type }}
                    </span>
                    <span class="text-xs text-slate-400">•</span>
                    <span class="text-xs font-semibold text-slate-600">{{ $employee->rank_group ?? '-' }}</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 mt-1">{{ $employee->formatted_name }}</h1>
                <p class="text-xs text-slate-500 font-mono">
                    {{ $employee->nip ? 'NIP. '.$employee->nip : ($employee->ni_pppk ? 'NI PPPK. '.$employee->ni_pppk : 'NUPTK: '.($employee->nuptk ?? '-')) }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.buku-induk.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Folio Pegawai
            </button>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- 1. Identitas Resmi & Kepegawaian -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                I. Identitas Resmi Pegawai
            </h2>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <div class="text-[11px] text-slate-400">Nomor Induk Pegawai (NIP)</div>
                    <div class="font-semibold text-slate-800">{{ $employee->nip ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Nomor Induk PPPK (NI PPPK)</div>
                    <div class="font-semibold text-slate-800">{{ $employee->ni_pppk ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">NUPTK Kemendikbud</div>
                    <div class="font-semibold text-slate-800">{{ $employee->nuptk ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Nomor Induk Kependudukan (NIK)</div>
                    <div class="font-semibold text-slate-800">{{ $employee->nik ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Nomor Karpeg / KPE</div>
                    <div class="font-semibold text-slate-800">{{ $employee->karpeg_number ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Nomor Taspen</div>
                    <div class="font-semibold text-slate-800">{{ $employee->taspen_number ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Nomor BPJS Kesehatan / TK</div>
                    <div class="font-semibold text-slate-800">{{ $employee->bpjs_number ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">NPWP</div>
                    <div class="font-semibold text-slate-800">{{ $employee->npwp ?? '-' }}</div>
                </div>
            </div>
        </div>

        <!-- 2. Data Pribadi & Kontak -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                II. Data Pribadi & Kontak
            </h2>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <div class="text-[11px] text-slate-400">Tempat, Tanggal Lahir</div>
                    <div class="font-semibold text-slate-800">{{ $employee->birth_place ?? '-' }}, {{ $employee->birth_date ? $employee->birth_date->format('d F Y') : '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Jenis Kelamin</div>
                    <div class="font-semibold text-slate-800">{{ $employee->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Nomor Telepon / WhatsApp</div>
                    <div class="font-semibold text-slate-800">{{ $employee->phone ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Email Dinas / Akun</div>
                    <div class="font-semibold text-slate-800">{{ $employee->email ?? '-' }}</div>
                </div>
                <div class="col-span-2">
                    <div class="text-[11px] text-slate-400">Alamat Tempat Tinggal</div>
                    <div class="font-semibold text-slate-800">{{ $employee->address ?? '-' }}</div>
                </div>
            </div>
        </div>

        <!-- 3. Pangkat, Jabatan & Kontrak Kerja -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                III. Riwayat Jabatan, Kepangkatan & Gaji
            </h2>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <div class="text-[11px] text-slate-400">Pangkat / Golongan Saat Ini</div>
                    <div class="font-bold text-slate-800">{{ $employee->rank_group ?? '-' }} (TMT: {{ $employee->rank_tmt ? $employee->rank_tmt->format('d/m/Y') : '-' }})</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Estimasi Kenaikan Pangkat (KP)</div>
                    <div class="font-bold text-blue-700">{{ $employee->next_kp_tmt ? $employee->next_kp_tmt->format('d F Y') : '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Jabatan Dinas</div>
                    <div class="font-semibold text-slate-800">{{ $employee->position ?? '-' }} (TMT: {{ $employee->position_tmt ? $employee->position_tmt->format('d/m/Y') : '-' }})</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Masa Kerja Golongan (MKG)</div>
                    <div class="font-semibold text-slate-800">{{ $employee->years_of_service_years }} tahun {{ $employee->years_of_service_months }} bulan</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">KGB Terakhir & Gaji Pokok</div>
                    <div class="font-semibold text-slate-800">{{ $employee->last_kgb_tmt ? $employee->last_kgb_tmt->format('d/m/Y') : '-' }} (Rp {{ number_format($employee->last_kgb_salary ?? 0, 0, ',', '.') }})</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Jadwal KGB Berikutnya</div>
                    <div class="font-bold text-emerald-700">{{ $employee->next_kgb_tmt ? $employee->next_kgb_tmt->format('d F Y') : '-' }}</div>
                </div>
                @if($employee->isPppk())
                <div class="col-span-2 bg-emerald-50 p-3 rounded-xl border border-emerald-200">
                    <div class="text-[11px] font-bold text-emerald-900 mb-1">Masa Hubungan Perjanjian Kerja (MHPK) PPPK</div>
                    <div class="text-xs text-emerald-800">No. SK/Kontrak: <strong>{{ $employee->pppk_contract_number ?? '-' }}</strong></div>
                    <div class="text-xs text-emerald-800 mt-0.5">Periode Kontrak: <strong>{{ $employee->pppk_contract_start ? $employee->pppk_contract_start->format('d/m/Y') : '-' }}</strong> s.d. <strong>{{ $employee->pppk_contract_end ? $employee->pppk_contract_end->format('d/m/Y') : '-' }}</strong></div>
                </div>
                @endif
            </div>
        </div>

        <!-- 4. Pendidikan Formal & Sertifikasi -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                IV. Pendidikan Formal & Sertifikasi Pendidik
            </h2>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <div class="text-[11px] text-slate-400">Jenjang & Jurusan</div>
                    <div class="font-bold text-slate-800">{{ $employee->education_level ?? '-' }} {{ $employee->education_major }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Tahun Lulus</div>
                    <div class="font-semibold text-slate-800">{{ $employee->education_grad_year ?? '-' }}</div>
                </div>
                <div class="col-span-2">
                    <div class="text-[11px] text-slate-400">Perguruan Tinggi / Lembaga</div>
                    <div class="font-semibold text-slate-800">{{ $employee->education_institution ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Status Sertifikasi Guru</div>
                    <div class="font-semibold {{ $employee->is_certified ? 'text-emerald-700' : 'text-slate-500' }}">
                        {{ $employee->is_certified ? 'Sudah Sertifikasi (Serdik Aktif)' : 'Belum Sertifikasi' }}
                    </div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-400">Mata Pelajaran Diampu</div>
                    <div class="font-semibold text-slate-800">{{ $employee->teaching_subject ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Dokumen SK & Cuti -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Riwayat SK & Karir -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Riwayat SK & Kepangkatan / Jabatan</h3>
            <div class="space-y-2">
                @forelse($employee->histories as $h)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                        <div class="font-bold text-slate-900">{{ $h->title }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">No. SK: {{ $h->document_number ?? '-' }} | TMT: {{ $h->start_date ? $h->start_date->format('d/m/Y') : '-' }}</div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada riwayat dokumen yang dicatat.</p>
                @endforelse
            </div>
        </div>

        <!-- Riwayat Cuti Terakhir -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Riwayat Cuti Terakhir</h3>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                    Sisa Kuota: {{ $employee->remainingLeaveBalance() }} Hari
                </span>
            </div>
            <div class="space-y-2">
                @forelse($employee->leaves as $l)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">{{ $l->leave_type }}</span>
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">{{ $l->days_count }} hari</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            {{ $l->start_date->format('d/m/Y') }} s.d. {{ $l->end_date->format('d/m/Y') }} | Alasan: {{ $l->reason }}
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada riwayat cuti pada tahun berjalan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
