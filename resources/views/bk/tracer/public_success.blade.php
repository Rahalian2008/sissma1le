<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - Tracer Study Alumni {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    @php
        $compFavicon = $schoolFaviconUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
        $compLogo = $schoolLogoUrl ?? ($school?->logo_url ?? asset('images/logo.png'));
    @endphp
    <link rel="icon" type="image/png" href="{{ $compFavicon }}">
    <link rel="shortcut icon" href="{{ $compFavicon }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased">
    
    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200/80 dark:border-slate-800 text-center relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl"></div>
        <div class="absolute -left-10 -top-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl"></div>

        <div class="relative z-10">
            <!-- Icon Sukses -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 flex items-center justify-center mx-auto mb-5 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 mb-2">
                ✓ Data Berhasil Tersimpan
            </span>

            <h1 class="font-heading font-black text-2xl text-slate-900 dark:text-white tracking-tight mb-2">
                Terima Kasih, Alumni!
            </h1>

            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                Data penelusuran tamatan Anda telah berhasil direkam dalam sistem Tracer Study Bimbingan Konseling <strong>{{ $school->name ?? 'SMAN 1 Lengkong' }}</strong>. Semoga senantiasa sukses dalam studi, karier, maupun bisnis Anda!
            </p>

            @if($tracer)
            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 mb-6 text-left border border-slate-200/70 dark:border-slate-700/60 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-400">Nama:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->full_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Tahun Lulus:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tracer->graduation_year }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Status:</span>
                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $tracer->activity_status_label }}</span>
                </div>
                @if($tracer->campus_name)
                <div class="flex justify-between">
                    <span class="text-slate-400">Kampus:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[200px]">{{ $tracer->campus_name }}</span>
                </div>
                @endif
                @if($tracer->company_name)
                <div class="flex justify-between">
                    <span class="text-slate-400">Perusahaan:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[200px]">{{ $tracer->company_name }}</span>
                </div>
                @endif
                @if($tracer->business_name)
                <div class="flex justify-between">
                    <span class="text-slate-400">Usaha:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[200px]">{{ $tracer->business_name }}</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Bagikan Link ke Teman Seangkatan -->
            <div class="space-y-3">
                <button type="button" onclick="shareToWhatsApp()" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    <span>Bagikan Form ke Teman Seangkatan</span>
                </button>

                <a href="{{ route('website.home') }}" class="block w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Kembali ke Beranda Sekolah
                </a>
            </div>
        </div>
    </div>

    <script>
        function shareToWhatsApp() {
            const url = "{{ route('bk.tracer.public_form') }}";
            const text = "Halo rekan alumni {{ $school->name ?? 'SMAN 1 Lengkong' }}! Yuk luangkan waktu 2 menit untuk mengisi Kuesioner Tracer Study Alumni di tautan berikut: " + url;
            window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
        }
    </script>
</body>
</html>
