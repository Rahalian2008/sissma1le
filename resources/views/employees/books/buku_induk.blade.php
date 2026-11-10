@extends('layouts.app')

@section('title', 'Buku Induk Pegawai (PTK)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Buku Induk PTK</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Format Resmi Kemendikbud & Pemprov Jabar</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Induk Pegawai (Pendidik & Tenaga Kependidikan)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Dokumentasi portofolio induk, identitas kepegawaian (NIP/NI PPPK/Karpeg/Taspen), riwayat SK, kepangkatan, dan data keluarga.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.buku-induk.print', request()->query()) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Induk
            </a>
            <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pegawai
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Seluruh PTK</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $employees->total() }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Guru & Tenaga Kependidikan</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider">ASN PNS</div>
            <div class="text-2xl font-bold text-blue-700 mt-1">{{ $totalPns }}</div>
            <div class="text-[10px] text-blue-600/70 mt-0.5">Pegawai Negeri Sipil</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">ASN PPPK</div>
            <div class="text-2xl font-bold text-emerald-700 mt-1">{{ $totalPppk }}</div>
            <div class="text-[10px] text-emerald-600/70 mt-0.5">Perjanjian Kerja Jabar</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Non-ASN / Honorer</div>
            <div class="text-2xl font-bold text-amber-700 mt-1">{{ $totalHonorer }}</div>
            <div class="text-[10px] text-amber-600/70 mt-0.5">GTT, PTT & Tenaga Sekolah</div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200">
        <form action="{{ route('employees.buku-induk.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai, NIP, NI PPPK, NUPTK, atau jabatan..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs">
            </div>
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    <option value="">Semua Status Kepegawaian</option>
                    <option value="PNS" {{ request('status') === 'PNS' ? 'selected' : '' }}>PNS</option>
                    <option value="PPPK" {{ request('status') === 'PPPK' ? 'selected' : '' }}>PPPK</option>
                    <option value="GTT" {{ request('status') === 'GTT' ? 'selected' : '' }}>GTT</option>
                    <option value="PTT" {{ request('status') === 'PTT' ? 'selected' : '' }}>PTT</option>
                    <option value="Honorer" {{ request('status') === 'Honorer' ? 'selected' : '' }}>Honorer</option>
                </select>
            </div>
            <div>
                <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    <option value="">Semua Jenis Pegawai</option>
                    <option value="Guru" {{ request('type') === 'Guru' ? 'selected' : '' }}>Guru</option>
                    <option value="Tenaga Kependidikan" {{ request('type') === 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table Buku Induk -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-semibold border-b border-slate-200 text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Pegawai & Identitas Resmi</th>
                        <th class="px-5 py-3.5">Pangkat / Golongan & TMT</th>
                        <th class="px-5 py-3.5">Jabatan Dinas & Tugas</th>
                        <th class="px-5 py-3.5">Pendidikan Terakhir</th>
                        <th class="px-5 py-3.5">Status & Masa Kerja</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $e)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $e->photo_url }}" alt="{{ $e->name }}" class="w-9 h-9 rounded-full border border-slate-200 object-cover shrink-0" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'36\' height=\'36\' fill=\'%23cbd5e1\'><rect width=\'36\' height=\'36\' rx=\'18\'/></svg>'">
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm hover:text-blue-600">
                                            <a href="{{ route('employees.buku-induk.show', $e) }}">{{ $e->formatted_name }}</a>
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                            @if($e->nip)
                                                NIP. {{ $e->nip }}
                                            @elseif($e->ni_pppk)
                                                NI PPPK. {{ $e->ni_pppk }}
                                            @else
                                                NUPTK: {{ $e->nuptk ?? '-' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-900">{{ $e->rank_group ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">TMT: {{ $e->rank_tmt ? $e->rank_tmt->format('d/m/Y') : '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-slate-800">{{ $e->position ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $e->teaching_subject ?: ($e->additional_tasks ?: $e->employment_type) }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-900">{{ $e->education_level ?? '-' }} {{ $e->education_major }}</div>
                                <div class="text-[11px] text-slate-500 truncate max-w-[180px]">{{ $e->education_institution ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $e->isPns() ? 'bg-blue-100 text-blue-800 border border-blue-200' : ($e->isPppk() ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200') }}">
                                    {{ $e->employment_status }}
                                </span>
                                <div class="text-[11px] text-slate-500 mt-1">{{ $e->years_of_service_years }} thn {{ $e->years_of_service_months }} bln</div>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('employees.buku-induk.show', $e) }}" class="inline-flex items-center px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg text-xs transition border border-blue-200">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Folio Induk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">Tidak ada data pegawai yang sesuai dengan pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection
