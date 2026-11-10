@extends('layouts.app')

@section('title', 'Buku Agenda Surat Masuk')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Surat Masuk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan buku agenda surat masuk, klasifikasi arsip, dan lembar disposisi Kepala Sekolah.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('letters.incoming.agenda.print', ['year' => date('Y')]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Agenda
            </a>
            <a href="{{ route('dispositions.index') }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Semua Disposisi
            </a>
            <x-import-modal type="incoming_letters" title="Surat Masuk" />
            <a href="{{ route('letters.incoming.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Surat Masuk
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('letters.incoming.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor surat, pengirim, perihal, atau nomor agenda..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Status</option>
                    <option value="Menunggu Disposisi" {{ request('status') === 'Menunggu Disposisi' ? 'selected' : '' }}>Menunggu Disposisi</option>
                    <option value="Didisposisikan" {{ request('status') === 'Didisposisikan' ? 'selected' : '' }}>Didisposisikan</option>
                    <option value="Ditindaklanjuti" {{ request('status') === 'Ditindaklanjuti' ? 'selected' : '' }}>Ditindaklanjuti</option>
                    <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Incoming Letters Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">No. Agenda & Tgl Terima</th>
                        <th class="px-5 py-3.5">Asal Surat (Pengirim)</th>
                        <th class="px-5 py-3.5">Nomor & Tanggal Surat</th>
                        <th class="px-5 py-3.5">Perihal & Sifat</th>
                        <th class="px-5 py-3.5">Status Disposisi</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($letters as $l)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-blue-700 font-mono">{{ $l->agenda_number }}</div>
                                <div class="text-[11px] text-slate-500">{{ $l->received_date?->format('d M Y') }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $l->sender }}</div>
                                <div class="text-[11px] text-slate-400">Klasifikasi: {{ $l->classification_code ?? 'Umum' }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-mono">
                                <div class="font-semibold text-slate-800">{{ $l->letter_number }}</div>
                                <div class="text-[11px] text-slate-500">Tgl: {{ $l->letter_date?->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <div class="font-bold text-slate-900 line-clamp-1">{{ $l->subject }}</div>
                                <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[10px] font-bold 
                                    @if($l->character === 'Sangat Segera' || $l->character === 'Penting') bg-rose-50 text-rose-700 border border-rose-200
                                    @elseif($l->character === 'Segera') bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-slate-100 text-slate-700 @endif">
                                    {{ $l->character }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($l->status === 'Selesai') bg-emerald-100 text-emerald-800
                                    @elseif($l->status === 'Didisposisikan' || $l->status === 'Ditindaklanjuti') bg-blue-100 text-blue-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $l->status }}
                                </span>
                                @if($l->dispositions->isNotEmpty())
                                    <div class="text-[10px] text-slate-500 mt-0.5 font-semibold">
                                        {{ $l->dispositions->count() }} Disposisi Diberikan
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                @if($l->file_path)
                                    <a href="{{ $l->file_url }}" target="_blank" class="inline-flex items-center px-2 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-lg transition border border-emerald-200" title="Unduh / Lihat Berkas Bukti">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        Berkas
                                    </a>
                                @endif
                                <a href="{{ route('letters.incoming.show', $l) }}" class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg transition border border-blue-200">
                                    Detail / Disposisi
                                </a>
                                <a href="{{ route('letters.incoming.edit', $l) }}" class="inline-flex items-center px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-lg transition border border-amber-200" title="Edit Data Surat">
                                    Edit
                                </a>
                                <form action="{{ route('letters.incoming.destroy', $l) }}" method="POST" onsubmit="return confirm('Hapus surat masuk ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Surat Masuk">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Belum ada surat masuk yang tercatat.
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
