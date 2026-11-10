@extends('layouts.app')

@section('title', 'Buku Agenda Surat Keluar')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Surat Keluar Resmi</h1>
            <p class="text-xs text-slate-500 mt-0.5">Penomoran otomatis surat dinas resmi, klasifikasi tata naskah dinas, dan pengarsipan digital.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('letters.outgoing.agenda.print', ['year' => date('Y')]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Agenda
            </a>
            <a href="{{ route('letters.generator.index') }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Generator Template Surat
            </a>
            <x-import-modal type="outgoing_letters" title="Surat Keluar" />
            <a href="{{ route('letters.outgoing.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Surat Keluar
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('letters.outgoing.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor surat, tujuan penerima, atau perihal..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="classification" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Klasifikasi</option>
                    @foreach($classifications as $c)
                        <option value="{{ $c->code }}" {{ request('classification') === $c->code ? 'selected' : '' }}>{{ $c->code }} - {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Outgoing Letters Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nomor Surat Resmi</th>
                        <th class="px-5 py-3.5">Tujuan Surat (Penerima)</th>
                        <th class="px-5 py-3.5">Perihal</th>
                        <th class="px-5 py-3.5">Tanggal Surat</th>
                        <th class="px-5 py-3.5">Penandatangan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($letters as $l)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 font-mono">{{ $l->letter_number }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">No. Agenda: {{ $l->agenda_number }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">
                                {{ $l->recipient }}
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <div class="font-semibold text-slate-900 line-clamp-1">{{ $l->subject }}</div>
                                <span class="text-[11px] text-slate-400">Kode: {{ $l->classification_code ?? '421.3' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-mono">
                                {{ $l->letter_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $l->signer_name }}</div>
                                @if($l->signer_nip)<div class="text-[10px] text-slate-400 font-mono">NIP. {{ $l->signer_nip }}</div>@endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('letters.outgoing.show', $l) }}" class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg transition border border-blue-200">
                                    Lihat
                                </a>
                                <a href="{{ route('letters.outgoing.edit', $l) }}" class="inline-flex items-center px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-lg transition border border-amber-200">
                                    Edit
                                </a>
                                <a href="{{ route('letters.outgoing.print', $l) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition border border-slate-200">
                                    Cetak
                                </a>
                                <form action="{{ route('letters.outgoing.destroy', $l) }}" method="POST" onsubmit="return confirm('Hapus surat keluar ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Surat Keluar">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Belum ada surat keluar yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($letters->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $letters->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
