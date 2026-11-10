<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Kunjungan Tamu - {{ $guest->ticket_number }} | {{ $school->name ?? 'SIKATAS' }}</title>
    @if(!empty($school->logo_url))
        <link rel="icon" type="image/png" href="{{ $school->logo_url }}">
    @endif
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="h-full font-sans antialiased text-slate-100 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 flex flex-col justify-between p-4 sm:p-8">

    <div class="max-w-xl mx-auto w-full space-y-6 my-auto">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Digital Guest Pass Card -->
        <div class="bg-slate-900/90 rounded-3xl border border-slate-800 shadow-2xl overflow-hidden backdrop-blur-xl">
            <!-- Header Card -->
            <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-blue-600 p-6 text-white text-center relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-white/10 blur-xl"></div>
                <div class="relative z-10 space-y-1">
                    <div class="text-[11px] font-bold uppercase tracking-widest text-indigo-200">Kartu Kunjungan Tamu Resmi</div>
                    <h1 class="text-xl font-extrabold">{{ $school->name ?? 'SMA NEGERI 1 LENGKONG' }}</h1>
                    <div class="inline-block mt-2 px-3 py-1 bg-black/30 rounded-full font-mono text-xs font-bold tracking-wider text-amber-300 border border-amber-400/30">
                        {{ $guest->ticket_number }}
                    </div>
                </div>
            </div>

            <!-- Body Details -->
            <div class="p-6 sm:p-8 space-y-6 text-xs">
                
                <!-- Selfie & QR Code Header row -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pb-6 border-b border-slate-800">
                    <!-- Foto Selfie -->
                    <div class="flex items-center gap-4">
                        <img src="{{ $guest->selfie_url }}" alt="Foto Selfie {{ $guest->name }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-2 border-indigo-500/50 shadow-md" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'120\' height=\'120\' viewBox=\'0 0 100 100\'><rect width=\'100\' height=\'100\' rx=\'20\' fill=\'%236366f1\'/><text x=\'50%\' y=\'55%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23ffffff\' font-family=\'sans-serif\' font-size=\'38\' font-weight=\'bold\'>{{ strtoupper(substr($guest->name, 0, 2)) }}</text></svg>';">
                        <div>
                            <div class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Tamu Terdaftar</div>
                            <h2 class="text-base font-extrabold text-white mt-0.5">{{ $guest->name }}</h2>
                            <div class="text-xs text-indigo-400 font-semibold mt-0.5">{{ $guest->institution }}</div>
                            <div class="mt-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide
                                    @if($guest->status === 'selesai') bg-slate-800 text-slate-300 border border-slate-700
                                    @elseif($guest->status === 'bertemu') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                    @else bg-amber-500/20 text-amber-300 border border-amber-500/30 @endif">
                                    Status: {{ strtoupper($guest->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Verifikasi -->
                    <div class="flex flex-col items-center text-center shrink-0">
                        <div class="p-2 bg-white rounded-2xl shadow-md border border-slate-700">
                            {!! $qrCodeSvg !!}
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 font-mono">Scan Verifikasi</span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800">
                        <div class="text-slate-400 text-[10px] uppercase tracking-wider">Identitas & Kontak</div>
                        <div class="font-bold text-white mt-0.5">{{ $guest->identity_type }}: {{ $guest->identity_number }}</div>
                        <div class="font-mono text-slate-300 text-[11px] mt-0.5">WA: {{ $guest->phone }}</div>
                    </div>

                    <div class="bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800">
                        <div class="text-slate-400 text-[10px] uppercase tracking-wider">Kategori Instansi</div>
                        <div class="font-bold text-white mt-0.5">{{ $guest->institution_type }}</div>
                        <div class="text-slate-400 text-[11px] mt-0.5">Rombongan: {{ $guest->number_of_guests }} Orang</div>
                    </div>

                    <div class="bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800">
                        <div class="text-slate-400 text-[10px] uppercase tracking-wider">Tujuan Bertemu</div>
                        <div class="font-bold text-indigo-300 mt-0.5">{{ $guest->destination_department }}</div>
                        @if($guest->destination_person_name)
                            <div class="text-white text-[11px]">D.h.i: {{ $guest->destination_person_name }}</div>
                        @endif
                    </div>

                    <div class="bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800">
                        <div class="text-slate-400 text-[10px] uppercase tracking-wider">Kendaraan & Waktu Masuk</div>
                        <div class="font-bold text-white mt-0.5">
                            {{ $guest->vehicle_type ?? 'Jalan Kaki' }}
                            @if($guest->vehicle_license_plate)
                                <span class="font-mono text-amber-400">({{ $guest->vehicle_license_plate }})</span>
                            @endif
                        </div>
                        <div class="font-mono text-slate-300 text-[11px] mt-0.5">{{ $guest->check_in_at?->format('d/m/Y H:i') }} WIB</div>
                    </div>
                </div>

                <!-- Keperluan -->
                <div class="bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800">
                    <div class="text-slate-400 text-[10px] uppercase tracking-wider">Agenda & Maksud Kunjungan</div>
                    <div class="font-bold text-white mt-0.5">{{ $guest->purpose }}</div>
                    <p class="text-slate-300 text-[11px] mt-1 leading-relaxed">{{ $guest->purpose_detail }}</p>
                </div>

                <!-- Tanda Tangan Tamu & Check-out info -->
                @if($guest->signature_path || $guest->check_out_at)
                    <div class="flex items-center justify-between gap-4 pt-2">
                        @if($guest->signature_path)
                            <div>
                                <div class="text-[10px] text-slate-500 uppercase tracking-wider">Tanda Tangan Tamu</div>
                                <img src="{{ $guest->signature_path }}" alt="Tanda Tangan" class="h-12 w-auto mt-1 border-b border-slate-700 p-0.5">
                            </div>
                        @endif

                        @if($guest->check_out_at)
                            <div class="text-right">
                                <div class="text-[10px] text-slate-500 uppercase tracking-wider">Waktu Selesai (Check-Out)</div>
                                <div class="font-mono font-bold text-emerald-400 text-xs mt-0.5">{{ $guest->check_out_at?->format('d/m/Y H:i') }} WIB</div>
                                <div class="text-[10px] text-slate-400">Durasi: {{ $guest->duration_formatted }}</div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Footer Card Actions -->
            <div class="bg-slate-950/80 p-5 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                <a href="{{ route('guests.print-card', $guest->ticket_number) }}" target="_blank" class="w-full sm:w-auto px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl font-bold flex items-center justify-center gap-1.5 transition">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak ID Badge Tamu
                </a>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    @if(!$guest->isCheckedOut())
                        <form action="{{ route('guests.kiosk.checkout') }}" method="POST" class="inline w-full sm:w-auto">
                            @csrf
                            <input type="hidden" name="ticket_or_phone" value="{{ $guest->ticket_number }}">
                            <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl transition cursor-pointer">
                                Selesai (Check-Out)
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('guests.kiosk') }}" class="w-full sm:w-auto px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition text-center">
                        Input Tamu Baru
                    </a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
