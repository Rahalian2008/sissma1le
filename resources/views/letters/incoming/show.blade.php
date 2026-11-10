@extends('layouts.app')

@section('title', 'Detail Surat Masuk - ' . $incomingLetter->letter_number)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.incoming.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Detail Surat Masuk</h1>
                <p class="text-xs text-slate-500 font-mono">No. Agenda: {{ $incomingLetter->agenda_number }} &bull; Status: {{ $incomingLetter->status }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($isArchived)
                <span class="inline-flex items-center px-3 py-2 bg-emerald-50 text-emerald-800 font-semibold text-xs rounded-xl border border-emerald-200">
                    <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Terarsip di Kearsipan Digital
                </span>
            @else
                <form action="{{ route('letters.incoming.archive', $incomingLetter) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        Simpan ke Kearsipan Digital
                    </button>
                </form>
            @endif
            <a href="{{ route('letters.incoming.edit', $incomingLetter) }}" class="inline-flex items-center px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs rounded-xl border border-amber-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Surat
            </a>
            <form action="{{ route('letters.incoming.destroy', $incomingLetter) }}" method="POST" onsubmit="return confirm('Hapus surat masuk ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl border border-rose-200 transition cursor-pointer">
                    Hapus
                </button>
            </form>
            <a href="{{ route('letters.incoming.dispositions.create', $incomingLetter) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Buat Lembar Disposisi
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Letter Info Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4 text-xs">
                <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Informasi Surat
                </h2>
                <div>
                    <span class="text-slate-400 block text-[11px]">Asal Pengirim:</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $incomingLetter->sender }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Nomor Surat:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $incomingLetter->letter_number }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Tanggal Surat:</span>
                        <span class="font-semibold text-slate-800">{{ $incomingLetter->letter_date?->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Tanggal Diterima:</span>
                        <span class="font-semibold text-slate-800">{{ $incomingLetter->received_date?->format('d/m/Y') }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Sifat Surat:</span>
                        <span class="font-bold text-blue-700">{{ $incomingLetter->character }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Lampiran:</span>
                        <span class="font-semibold text-slate-800">{{ $incomingLetter->attachment_count ?? '-' }}</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-400 block text-[11px]">Perihal:</span>
                    <p class="font-semibold text-slate-900 mt-1 leading-relaxed">{{ $incomingLetter->subject }}</p>
                </div>

                <!-- Berkas Bukti / File Surat -->
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-slate-400 block text-[11px] mb-1.5">Berkas Bukti / File Surat:</span>
                    @if($incomingLetter->file_path)
                        <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl space-y-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="font-bold text-slate-800 truncate">{{ basename($incomingLetter->file_path) }}</span>
                            </div>
                            <div class="flex items-center gap-2 pt-1">
                                <a href="{{ $incomingLetter->file_url }}" target="_blank" class="flex-1 text-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow-2xs transition">
                                    Buka / Unduh Berkas
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-slate-50 border border-dashed border-slate-300 rounded-xl text-center text-slate-400">
                            <span>Tidak ada berkas bukti yang diunggah.</span>
                            <div class="mt-1">
                                <a href="{{ route('letters.incoming.edit', $incomingLetter) }}" class="text-blue-600 font-semibold hover:underline">+ Unggah Berkas</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dispositions List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Instruksi & Disposisi Kepala Sekolah
                    </h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $incomingLetter->dispositions->count() }} Disposisi
                    </span>
                </div>

                @forelse ($incomingLetter->dispositions as $disp)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 mb-4 space-y-3 text-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-200 pb-2">
                            <div>
                                <span class="text-slate-500">Diteruskan Kepada:</span>
                                <span class="font-bold text-slate-900 ml-1">{{ $disp->target_role_or_user }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                    @if($disp->status === 'Selesai') bg-emerald-100 text-emerald-800
                                    @elseif($disp->status === 'Sedang Dikerjakan') bg-blue-100 text-blue-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $disp->status }}
                                </span>
                                <a href="{{ route('letters.sppd.create', [
                                    'task_number' => $incomingLetter->letter_number,
                                    'purpose' => 'Menindaklanjuti disposisi surat ' . $incomingLetter->letter_number . ': ' . $incomingLetter->subject . ' (' . $disp->instructions . ')'
                                ]) }}" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold rounded-lg border border-amber-200 shadow-2xs transition text-[11px] inline-flex items-center gap-1">
                                    <span>🚗 Buat SPPD</span>
                                </a>
                                <a href="{{ route('dispositions.print', $disp) }}" target="_blank" class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-lg border border-slate-200 shadow-2xs transition">
                                    Cetak Lembar
                                </a>
                            </div>
                        </div>

                        <div>
                            <span class="text-slate-500 block">Petunjuk / Isi Disposisi:</span>
                            <p class="font-medium text-slate-800 mt-0.5 bg-white p-2.5 rounded-lg border border-slate-200">{{ $disp->instructions }}</p>
                        </div>

                        <div class="flex flex-wrap items-center justify-between text-[11px] text-slate-500">
                            <div>Pemberi Disposisi: <strong class="text-slate-700">{{ $disp->giver?->name ?? 'Kepala Sekolah' }}</strong> &bull; Tgl: {{ $disp->disposition_date?->format('d/m/Y') }}</div>
                            @if($disp->due_date)
                                <div class="text-rose-600 font-semibold">Batas Waktu: {{ $disp->due_date?->format('d/m/Y') }}</div>
                            @endif
                        </div>

                        <!-- Follow Up Form / Notes -->
                        <div class="pt-3 border-t border-slate-200">
                            @if($disp->follow_up_notes)
                                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-2.5 text-[11px] text-emerald-900">
                                    <strong>Catatan Tindak Lanjut:</strong> {{ $disp->follow_up_notes }}
                                </div>
                            @endif

                            <form action="{{ route('dispositions.follow-up', $disp) }}" method="POST" class="mt-2 flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="follow_up_notes" placeholder="Tuliskan catatan tindak lanjut..." required class="flex-1 px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white">
                                <select name="status" class="px-2 py-1.5 border border-slate-300 rounded-lg text-xs bg-white">
                                    <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs cursor-pointer">
                                    Update
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        Surat ini belum memiliki lembar disposisi. Klik tombol Buat Lembar Disposisi di kanan atas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
