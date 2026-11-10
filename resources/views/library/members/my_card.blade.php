@extends('layouts.app')

@section('title', 'Kartu Anggota Perpustakaan Digital')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    <div class="print:hidden">
        @include('library.partials.nav')
    </div>

    <!-- Top Action Nav -->
    <div class="flex items-center justify-between gap-4 print:hidden">
        <a href="{{ route('library.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-50 transition">
            &larr; Dashboard Perpustakaan
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('library.loans.my') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                Peminjaman Saya
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Kartu Anggota</span>
            </button>
        </div>
    </div>

    <!-- TAMPILAN KARTU ANGGOTA PERPUSTAKAAN DIGITAL RESMI -->
    <div class="flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-md rounded-3xl overflow-hidden shadow-2xl border border-indigo-500/30 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white relative p-6 sm:p-7">
            <!-- Background Ornaments -->
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-amber-500/15 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-cyan-500/15 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Header Kartu -->
            <div class="relative z-10 flex items-center justify-between border-b border-indigo-700/50 pb-4 mb-5">
                <div class="flex items-center gap-3">
                    @if(!empty($schoolLogo))
                        <div class="w-10 h-10 rounded-xl bg-white/10 p-1 backdrop-blur-xs border border-white/20 flex items-center justify-center shrink-0 shadow-md">
                            <img src="{{ $schoolLogo }}" alt="Logo {{ $schoolName }}" class="w-full h-full object-contain">
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 font-black text-xl shadow-md shrink-0">
                            📖
                        </div>
                    @endif
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-amber-400">{{ strtoupper($schoolName ?? 'SMA NEGERI 1 LENGKONG') }}</div>
                        <h2 class="text-sm font-black text-white tracking-wide">KARTU ANGGOTA PERPUSTAKAAN</h2>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        {{ $member->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                    </span>
                </div>
            </div>

            <!-- Body Kartu: Foto & Biodata -->
            <div class="relative z-10 flex items-start gap-4 mb-5">
                <!-- Avatar / Foto Profil -->
                <div class="w-22 h-26 rounded-2xl overflow-hidden bg-slate-800 border-2 border-amber-400/50 shrink-0 shadow-lg flex items-center justify-center">
                    @if($user->avatar && (Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar) || file_exists(public_path('storage/' . $user->avatar))))
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-indigo-700 to-indigo-900 flex flex-col items-center justify-center text-white">
                            <span class="text-3xl font-black">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                            <span class="text-[9px] font-bold text-amber-300 uppercase mt-1">{{ strtoupper($member->member_type) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Info Biodata -->
                <div class="flex-1 min-w-0 space-y-1 text-xs">
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase font-medium">Nama Anggota</div>
                        <div class="font-extrabold text-sm text-white truncate">{{ $member->name }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase font-medium">{{ strtoupper($member->member_type) === 'SISWA' ? 'NISN / NIS' : 'NIP / Identitas' }}</div>
                        <div class="font-mono font-bold text-cyan-300 text-xs">{{ $member->identity_number ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase font-medium">Kelas / Status</div>
                        <div class="font-semibold text-slate-200 truncate">{{ $member->member_class_or_title }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase font-medium">Bergabung Sejak</div>
                        <div class="font-medium text-slate-300 text-[11px]">{{ $member->joined_at ? $member->joined_at->translatedFormat('d F Y') : '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Barcode & Nomor Anggota -->
            <div class="relative z-10 pt-4 border-t border-indigo-700/50 flex flex-col items-center justify-center">
                <!-- Barcode SVG Simulation -->
                <div class="bg-white p-2.5 rounded-xl flex flex-col items-center shadow-inner w-full">
                    <svg class="h-10 w-full max-w-[280px]" viewBox="0 0 200 40" preserveAspectRatio="none">
                        @php
                            $code = $member->member_code;
                            $bars = [];
                            $seed = crc32($code);
                            mt_srand($seed);
                            $x = 10;
                            while ($x < 190) {
                                $w = mt_rand(1, 3);
                                $space = mt_rand(1, 3);
                                $bars[] = ['x' => $x, 'w' => $w];
                                $x += $w + $space;
                            }
                        @endphp
                        @foreach($bars as $bar)
                            <rect x="{{ $bar['x'] }}" y="2" width="{{ $bar['w'] }}" height="36" fill="#0f172a" />
                        @endforeach
                    </svg>
                    <div class="font-mono font-black text-xs text-slate-900 tracking-widest mt-1">
                        {{ $member->member_code }}
                    </div>
                </div>
                <div class="text-[10px] text-indigo-300 text-center mt-2 font-medium">
                    Tunjukkan barcode ini ke meja sirkulasi atau presensi perpustakaan
                </div>
            </div>
        </div>
    </div>

    <!-- Tata Tertib & Ketentuan Pemustaka -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs print:hidden">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
            <span>📜 Tata Tertib Pemustaka {{ $schoolName ?? 'SMAN 1 Lengkong' }}</span>
        </h3>
        <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-2 list-disc list-inside leading-relaxed">
            <li>Kartu anggota digital ini berlaku sah selama pemilik terdaftar aktif sebagai Siswa atau Pendidik/Tenaga Kependidikan {{ $schoolName ?? 'SMAN 1 Lengkong' }}.</li>
            <li>Peminjaman buku fisik dapat diajukan secara online melalui akun aplikasi, kemudian diverifikasi di meja sirkulasi perpustakaan.</li>
            <li>Batas waktu peminjaman buku standar adalah <strong>7 (tujuh) hari</strong> dan dapat diperpanjang maksimal 2 kali jika tidak ada antrean reservasi dari pemustaka lain.</li>
            <li>Keterlambatan pengembalian buku dikenakan sanksi denda administrasi harian sesuai Standar Nasional Perpustakaan.</li>
            <li>Pemustaka wajib menjaga kebersihan, keutuhan lembaran buku, serta ketenangan ruang perpustakaan.</li>
        </ul>
    </div>
</div>

<style>
    @media print {
        body {
            background: white !important;
            color: black !important;
        }
        .sidebar, nav, header, #toast-container, .print\\:hidden {
            display: none !important;
        }
    }
</style>
@endsection

