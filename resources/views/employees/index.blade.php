@extends('layouts.app')

@section('title', 'Data Pendidik & Tenaga Kependidikan (PTK)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Pendidik & Tenaga Kependidikan (PTK)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Database lengkap guru, staf tata usaha, dan pengelola {{ $school->name ?? 'Sekolah' }}.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('duk.index') }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Daftar Urut Kepangkatan (DUK)
            </a>
            <a href="{{ route('dso.index') }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs rounded-xl border border-emerald-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Struktur Organisasi (DSO)
            </a>
            <x-import-modal type="employees" title="Data Pegawai" />
            <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pegawai
            </a>
        </div>
    </div>

    @if($employees->total() === 0)
    <div class="p-6 bg-teal-50/80 border-2 border-dashed border-teal-200 rounded-2xl text-center space-y-3">
        <h3 class="text-base font-bold text-slate-800">Data Pegawai di Administrasi Kepegawaian Sedang Kosong</h3>
        <p class="text-xs text-slate-500 max-w-lg mx-auto">
            Sesuai alur tata kelola data sekolah, seluruh basis data PTK tersimpan di Master Data Guru & PTK. Anda dapat menarik datanya ke Administrasi Kepegawaian kapan saja.
        </p>
        <form action="{{ route('tu.kepegawaian.pull_from_master') }}" method="POST">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs inline-flex items-center gap-2 shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Tarik Data dari Master Guru & PTK</span>
            </button>
        </form>
    </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('employees.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai, NIP, atau jabatan..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Jenis Pegawai</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Status Kepegawaian</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nama Pegawai & Gelar</th>
                        <th class="px-5 py-3.5">NIP / NUPTK</th>
                        <th class="px-5 py-3.5">Jenis & Status</th>
                        <th class="px-5 py-3.5">Jabatan / Mapel</th>
                        <th class="px-5 py-3.5">Pangkat / Golongan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $emp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $emp->formatted_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $emp->gender === 'L' ? 'Laki-laki' : 'Perempuan' }} &bull; {{ $emp->education_level ?? 'S1' }} {{ $emp->education_major }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-mono">
                                <div class="font-semibold text-slate-800">{{ $emp->nip ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">NUPTK: {{ $emp->nuptk ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $emp->employment_type }}</div>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold
                                    @if($emp->employment_status === 'PNS') bg-blue-100 text-blue-800
                                    @elseif($emp->employment_status === 'PPPK') bg-emerald-100 text-emerald-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $emp->employment_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-900">{{ $emp->position ?? 'Guru Mata Pelajaran' }}</div>
                                @if($emp->teaching_subject)
                                    <div class="text-[11px] text-blue-600 font-medium">{{ $emp->teaching_subject }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-800 font-semibold rounded-md border border-slate-200">
                                    {{ $emp->rank_group ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('employees.show', $emp) }}" class="p-1.5 inline-flex text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('employees.edit', $emp) }}" class="p-1.5 inline-flex text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit Pegawai">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('employees.destroy', $emp) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 inline-flex text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Pegawai">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="max-w-md mx-auto space-y-2">
                                    <div class="text-sm font-bold text-slate-700">Data Pegawai Administrasi Kepegawaian Masih Kosong</div>
                                    <p class="text-xs text-slate-400">Data belum ditarik dari Master Data Guru & PTK ke Administrasi Kepegawaian.</p>
                                    <form action="{{ route('tu.kepegawaian.pull_from_master') }}" method="POST" class="pt-2">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            <span>Tarik Data dari Master Guru & PTK</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($employees->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
