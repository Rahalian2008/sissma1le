@extends('layouts.app')

@section('title', 'Alih Tangan Kasus (Referral) BK')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-700 mb-2">
                <span>Rujukan Tenaga Ahli Profesional Luar Sekolah</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Alih Tangan Kasus (Referral) BK</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pengelolaan rujukan resmi peserta didik yang memerlukan penanganan ahli di luar kewenangan guru BK (Psikolog Klinis, Psikiater, Dokter Spesialis, Dinas Sosial, atau Lembaga Bantuan Hukum).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('bk.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                &larr; Dashboard BK
            </a>
            <a href="{{ route('bk.referral.create') }}" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Surat Rujukan Kasus
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Total Rujukan</span>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $stats['total'] }} <span class="text-xs font-normal text-slate-400">Berkas</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Sedang Dirujuk</span>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['sedang_dirujuk'] }} <span class="text-xs font-normal text-slate-400">Kasus</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Psikolog Klinis</span>
            <div class="text-2xl font-black text-indigo-600 mt-1">{{ $stats['psikolog'] }} <span class="text-xs font-normal text-slate-400">Rujukan</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-bold text-slate-500 uppercase">Psikiater / Medis</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['psikiater'] }} <span class="text-xs font-normal text-slate-400">Rujukan</span></div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Berkas Alih Tangan Kasus Resmi</h3>
                <p class="text-xs text-slate-400">Total: {{ $referrals->total() }} Rujukan Terdaftar</p>
            </div>
            <form action="{{ route('bk.referral.index') }}" method="GET" class="flex items-center gap-2 text-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ahli, lembaga, atau siswa..." class="px-3 py-2 border border-slate-300 rounded-xl">
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-800 font-bold uppercase text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">No. Rujukan & Tanggal</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Tenaga Ahli / Lembaga Tujuan</th>
                        <th class="py-3 px-4">Kategori Ahli</th>
                        <th class="py-3 px-4 text-center">Persetujuan Kepsek</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($referrals as $index => $r)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">{{ $referrals->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="font-mono font-bold text-slate-900">{{ $r->referral_code }}</div>
                                <div class="text-[11px] text-slate-400">{{ $r->referral_date?->translatedFormat('d F Y') ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $r->student?->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $r->student?->schoolClass?->name ?? '-' }} - NISN: {{ $r->student?->nisn ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800">{{ $r->target_expert_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $r->target_institution }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ str_replace('_', ' ', $r->expert_category) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $r->headmaster_approval === 'DISETUJUI' ? 'bg-emerald-100 text-emerald-800' : ($r->headmaster_approval === 'MENUNGGU' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $r->headmaster_approval }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $r->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-800' : ($r->status === 'SEDANG_DIRUJUK' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ str_replace('_', ' ', $r->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('bk.referral.print', $r) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition" title="Cetak Surat Rujukan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    <a href="{{ route('bk.referral.show', $r) }}" class="p-1.5 text-rose-600 hover:text-rose-800 rounded-lg hover:bg-rose-50 transition" title="Detail Rujukan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                Belum ada berkas alih tangan kasus (referral) yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($referrals->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

