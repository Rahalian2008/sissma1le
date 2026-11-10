@extends('layouts.app')

@section('title', 'Detail Kunjungan Tamu - ' . $guest->ticket_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('guests.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Detail Kunjungan Tamu</h1>
                <p class="text-xs text-slate-500 font-mono">{{ $guest->ticket_number }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('guests.print-card', $guest->ticket_number) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition shadow-xs">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Badge
            </a>
            <a href="{{ route('guests.card', ['ticket' => $guest->ticket_number]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200 transition">
                Kartu Digital
            </a>
            <form action="{{ route('guests.destroy', $guest) }}" method="POST" onsubmit="return confirm('Hapus data kunjungan tamu ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl border border-rose-200 transition cursor-pointer">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6 text-xs">
        
        <!-- Top Section: Photo Selfie + General Info -->
        <div class="flex flex-col sm:flex-row items-start gap-6 pb-6 border-b border-slate-100">
            <div class="shrink-0">
                <img src="{{ $guest->selfie_url }}" alt="Foto Selfie {{ $guest->name }}" class="w-32 h-32 sm:w-36 sm:h-36 rounded-2xl object-cover border-2 border-indigo-200 shadow-md" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'150\' viewBox=\'0 0 100 100\'><rect width=\'100\' height=\'100\' rx=\'20\' fill=\'%236366f1\'/><text x=\'50%\' y=\'55%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23ffffff\' font-family=\'sans-serif\' font-size=\'38\' font-weight=\'bold\'>{{ strtoupper(substr($guest->name, 0, 2)) }}</text></svg>';">
            </div>

            <div class="flex-1 space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $guest->ticket_number }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                        @if($guest->status === 'selesai') bg-slate-100 text-slate-700
                        @elseif($guest->status === 'bertemu') bg-emerald-100 text-emerald-800
                        @elseif($guest->status === 'menunggu') bg-amber-100 text-amber-800
                        @else bg-rose-100 text-rose-800 @endif">
                        Status: {{ $guest->status }}
                    </span>
                </div>

                <h2 class="text-xl font-extrabold text-slate-900">{{ $guest->name }}</h2>
                <div class="text-sm font-semibold text-indigo-700">{{ $guest->institution }} ({{ $guest->institution_type }})</div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 text-slate-600">
                    <div><strong>Identitas:</strong> {{ $guest->identity_type }} - {{ $guest->identity_number }}</div>
                    <div><strong>WhatsApp:</strong> <span class="font-mono">{{ $guest->phone }}</span></div>
                    <div><strong>Email:</strong> {{ $guest->email ?? '-' }}</div>
                    <div><strong>Rombongan:</strong> {{ $guest->number_of_guests }} Orang</div>
                </div>
            </div>

            <div class="shrink-0 flex flex-col items-center">
                <div class="p-2 bg-slate-50 border border-slate-200 rounded-2xl shadow-xs">
                    {!! $qrCodeSvg !!}
                </div>
                <span class="text-[10px] text-slate-400 font-mono mt-1">Token Verifikasi</span>
            </div>
        </div>

        <!-- Visit Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Tujuan Bertemu</div>
                <div class="font-bold text-slate-900 text-sm mt-1">{{ $guest->destination_department }}</div>
                @if($guest->destination_person_name)
                    <div class="text-indigo-700 font-medium mt-0.5">D.h.i: {{ $guest->destination_person_name }}</div>
                @endif
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Waktu & Kendaraan</div>
                <div class="font-bold text-slate-900 mt-1">Masuk: {{ $guest->check_in_at?->format('d/m/Y H:i') }} WIB</div>
                <div class="text-slate-600 mt-0.5">
                    @if($guest->check_out_at)
                        Keluar: {{ $guest->check_out_at?->format('d/m/Y H:i') }} WIB (Durasi: {{ $guest->duration_formatted }})
                    @else
                        <span class="text-amber-600 font-semibold">Saat ini masih di sekolah ({{ $guest->duration_formatted }})</span>
                    @endif
                </div>
                <div class="text-slate-500 mt-1 font-mono text-[11px]">
                    {{ $guest->vehicle_type ?? 'Jalan Kaki' }} @if($guest->vehicle_license_plate) &bull; {{ $guest->vehicle_license_plate }} @endif
                </div>
            </div>
        </div>

        <!-- Purpose -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
            <div class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Agenda / Keperluan Kunjungan</div>
            <div class="font-bold text-slate-900 text-sm mt-1">{{ $guest->purpose }}</div>
            <p class="text-slate-700 mt-1 leading-relaxed whitespace-pre-line">{{ $guest->purpose_detail }}</p>
        </div>

        <!-- Digital Signature -->
        @if($guest->signature_path)
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex items-center justify-between">
                <div>
                    <div class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Tanda Tangan Digital Tamu</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Direkam secara digital saat kedatangan di kiosk buku tamu.</div>
                </div>
                <img src="{{ $guest->signature_path }}" alt="Tanda Tangan" class="h-14 w-auto bg-slate-900 rounded-xl p-1 shadow-inner">
            </div>
        @endif

        <!-- Form Update Status (Security / Resepsionis Action) -->
        <div class="pt-4 border-t border-slate-100">
            <h3 class="font-bold text-slate-900 mb-3">Tindakan Petugas Resepsionis / Keamanan</h3>
            <form action="{{ route('guests.status', $guest) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Perbarui Status Kunjungan</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                        <option value="menunggu" {{ $guest->status === 'menunggu' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        <option value="bertemu" {{ $guest->status === 'bertemu' ? 'selected' : '' }}>Sedang Bertemu</option>
                        <option value="selesai" {{ $guest->status === 'selesai' ? 'selected' : '' }}>Selesai (Check-Out)</option>
                        <option value="dibatalkan" {{ $guest->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Catatan Resepsionis / Security</label>
                    <input type="text" name="notes" value="{{ old('notes', $guest->notes) }}" placeholder="Tamu diterima di ruang kepala sekolah..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                        Simpan Pembaruan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
