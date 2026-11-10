@extends('layouts.app')

@section('title', 'Pola Klasifikasi Kode Surat Dinas & Kearsipan SMA')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Pergub Jabar No. 14 Th 2018</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">SIKD Jabar</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Permendagri No. 83 Th 2022</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-600 font-semibold">Dinas Pendidikan Provinsi Jawa Barat & Balai Cabang Dinas Wilayah V</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-2">Pola Klasifikasi Kode Surat & Kearsipan SMA Negeri 1 Lengkong</h1>
            <p class="text-xs text-slate-500 mt-0.5">Panduan resmi standar kode kearsipan, tata naskah dinas, penomoran surat verbal keluar, lembar disposisi, dan pemberkasan arsip aktif/inaktif.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('letters.generator.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Template Surat
            </a>
            <a href="{{ route('letters.outgoing.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Surat Keluar Baru
            </a>
        </div>
    </div>

    <!-- Dasar Regulasi Resmi Kearsipan Section -->
    <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Dasar Regulasi & Ketentuan Penggunaan Tata Naskah Kearsipan</h2>
                    <p class="text-[11px] text-slate-500">Dasar hukum resmi tata naskah dinas dan tata kearsipan untuk satuan pendidikan jenjang SMA di lingkungan Pemerintah Provinsi Jawa Barat.</p>
                </div>
            </div>
            <span class="hidden md:inline-flex px-2.5 py-1 text-[10px] font-semibold bg-slate-100 text-slate-700 rounded-lg border border-slate-200">Dokumen Resmi Provinsi Jawa Barat</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 pt-2">
            <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-200/80">
                <div class="text-[10px] font-bold uppercase text-blue-700">Regulasi Pokok Jabar</div>
                <div class="text-xs font-bold text-slate-900 mt-1">Pergub Jabar No. 14 Tahun 2018</div>
                <p class="text-[11px] text-slate-600 mt-1">Tentang Pedoman Tata Kearsipan di Lingkungan Pemerintah Daerah Provinsi Jawa Barat.</p>
            </div>
            <div class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-200/80">
                <div class="text-[10px] font-bold uppercase text-emerald-700">Aplikasi Persuratan Dinas</div>
                <div class="text-xs font-bold text-slate-900 mt-1">Tata Naskah Kearsipan SIKD Jabar</div>
                <p class="text-[11px] text-slate-600 mt-1">Sistem Informasi Kearsipan Dinamis untuk administrasi naskah dinas, klasifikasi dan pemberkasan berkas.</p>
            </div>
            <div class="p-3 bg-purple-50/60 rounded-xl border border-purple-200/80">
                <div class="text-[10px] font-bold uppercase text-purple-700">Standar Satuan Pendidikan</div>
                <div class="text-xs font-bold text-slate-900 mt-1">Disdik Jabar & Balai KCD Wil. V</div>
                <p class="text-[11px] text-slate-600 mt-1">Standar penomoran surat dan pemberkasan arsip aktif/inaktif jenjang SMA Negeri dan Swasta.</p>
            </div>
            <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-200/80">
                <div class="text-[10px] font-bold uppercase text-amber-700">Acuan Nasional</div>
                <div class="text-xs font-bold text-slate-900 mt-1">Permendagri No. 83 Tahun 2022</div>
                <p class="text-[11px] text-slate-600 mt-1">Pedoman Tata Naskah Dinas di Lingkungan Instansi Pemerintah Daerah dan Perangkat Daerah.</p>
            </div>
        </div>
    </div>

    <!-- Official Letter Numbering Standard Banner (Latest Document Guidelines) -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 text-white p-6 rounded-2xl shadow-sm border border-blue-900 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-white/10 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 bg-blue-500/30 text-blue-200 text-[10px] font-bold rounded uppercase tracking-wider border border-blue-400/30">Standar Penomoran Resmi Terbaru</span>
                    <span class="text-xs text-blue-200/60">• Pedoman Kode Kearsipan Sekolah SMA</span>
                </div>
                <h2 class="text-base font-bold text-white mt-1">Struktur & Standar Format Penomoran Surat Dinas SMA</h2>
                <p class="text-xs text-slate-300">Format resmi penomoran naskah dinas verbal sekolah mengacu pada rumus tata naskah SIKD dan Dinas Pendidikan Provinsi Jawa Barat.</p>
            </div>
            <div class="bg-black/30 border border-white/10 px-4 py-2.5 rounded-xl text-center md:text-right shrink-0">
                <div class="text-[10px] uppercase tracking-wider text-amber-300 font-bold">Rumus Resmi Penomoran</div>
                <div class="font-mono text-xs sm:text-sm font-bold text-white mt-0.5">
                    [Nomor Urut Verbal] / [Kode Klasifikasi] - [Nama Sekolah] / [Bulan Romawi] / [Tahun]
                </div>
            </div>
        </div>

        <div>
            <div class="text-xs font-bold text-blue-200 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                Contoh Standar Sesuai Dokumen Panduan Resmi:
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @php
                    $schoolName = $school->name ?? 'SMAN 1 Lengkong';
                    $year = date('Y');
                    $examples = [
                        [
                            'title' => '1. Surat Penugasan Guru (Bintek / MGMP / Pengawasan)',
                            'code' => 'KPG.03.01.01',
                            'format' => "800 / 084 / KPG.03.01.01 - {$schoolName} / IX / {$year}",
                            'ket' => 'Kode Pokok 800 (Kepegawaian) / No. Verbal 084 / Sub-kode SPT GTK',
                        ],
                        [
                            'title' => '2. Surat Keterangan Aktif Siswa / Mutasi Keluar',
                            'code' => 'PK.11.01.01',
                            'format' => "421.3 / 195 / PK.11.01.01 - {$schoolName} / X / {$year}",
                            'ket' => 'Kode Pokok 421.3 (SMA) / No. Verbal 195 / Sub-kode Dapodik Siswa',
                        ],
                        [
                            'title' => '3. Surat Pemberitahuan Asesmen Sumatif (SAS/SAT)',
                            'code' => 'PK.07.01',
                            'format' => "421.3 / 210 / PK.07.01 - {$schoolName} / XI / {$year}",
                            'ket' => 'Kode Pokok 421.3 (SMA) / No. Verbal 210 / Sub-kode Penilaian Akademik',
                        ],
                        [
                            'title' => '4. Surat Usulan Kenaikan Gaji Berkala (KGB)',
                            'code' => 'KPG.14',
                            'format' => "822.2 / 045 / KPG.14 - {$schoolName} / XII / {$year}",
                            'ket' => 'Kode Pokok 822.2 (Gaji/KGB) / No. Verbal 045 / Sub-kode KGB GTK',
                        ],
                        [
                            'title' => '5. Laporan SPJ & Pertanggungjawaban BOS Sekolah',
                            'code' => 'KU.06.09',
                            'format' => "900 / 312 / KU.06.09 - {$schoolName} / XII / {$year}",
                            'ket' => 'Kode Pokok 900 (Keuangan) / No. Verbal 312 / Sub-kode SPJ & LRA BOS',
                        ],
                    ];
                @endphp
                @foreach($examples as $ex)
                    <div class="bg-white/5 border border-white/10 p-3 rounded-xl flex flex-col justify-between space-y-2">
                        <div>
                            <div class="text-[11px] font-semibold text-slate-300">{{ $ex['title'] }}</div>
                            <div class="font-mono text-xs font-bold text-amber-300 bg-black/40 px-2 py-1.5 rounded-lg mt-1 select-all border border-white/5">
                                {{ $ex['format'] }}
                            </div>
                        </div>
                        <div class="text-[10px] text-slate-400 flex items-center justify-between border-t border-white/10 pt-1.5">
                            <span class="truncate">{{ $ex['ket'] }}</span>
                            <button type="button" onclick="copyToClipboard('{{ $ex['code'] }}', this)" class="text-[9px] px-1.5 py-0.5 rounded bg-blue-500/30 hover:bg-blue-500/50 text-blue-200 transition shrink-0 ml-1">
                                Salin {{ $ex['code'] }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Search & Classification Items -->
    <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="relative w-full lg:w-96">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="searchClassification" placeholder="Cari kode (contoh: PK.03.03, KPG, KU) atau nama urusan..." onkeyup="filterClassifications()" class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="flex flex-wrap items-center gap-1.5 text-xs">
                <button type="button" onclick="filterCategory('all', this)" class="cat-btn px-2.5 py-1.5 rounded-lg font-bold text-xs bg-slate-800 text-white transition cursor-pointer">
                    Semua ({{ count($classifications) }})
                </button>
                <button type="button" onclick="filterCategory('PK', this)" class="cat-btn px-2.5 py-1.5 rounded-lg font-semibold text-xs bg-sky-100 text-sky-800 hover:bg-sky-200 transition cursor-pointer">
                    PK (Pendidikan)
                </button>
                <button type="button" onclick="filterCategory('KPG', this)" class="cat-btn px-2.5 py-1.5 rounded-lg font-semibold text-xs bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition cursor-pointer">
                    KPG (Kepegawaian GTK)
                </button>
                <button type="button" onclick="filterCategory('PS', this)" class="cat-btn px-2.5 py-1.5 rounded-lg font-semibold text-xs bg-amber-100 text-amber-800 hover:bg-amber-200 transition cursor-pointer">
                    PS (Perpustakaan)
                </button>
                <button type="button" onclick="filterCategory('KO_KS', this)" class="cat-btn px-2.5 py-1.5 rounded-lg font-semibold text-xs bg-teal-100 text-teal-800 hover:bg-teal-200 transition cursor-pointer">
                    KO & KS (Kesiswaan & UKS)
                </button>
                <button type="button" onclick="filterCategory('TU_HM', this)" class="cat-btn px-2.5 py-1.5 rounded-lg font-semibold text-xs bg-violet-100 text-violet-800 hover:bg-violet-200 transition cursor-pointer">
                    TU & HM (Tata Usaha & Humas)
                </button>
                <button type="button" onclick="filterCategory('PL_RT', this)" class="cat-btn px-2.5 py-1.5 rounded-lg font-semibold text-xs bg-orange-100 text-orange-800 hover:bg-orange-200 transition cursor-pointer">
                    PL & RT (Sarpras & RT)
                </button>
                <button type="button" onclick="filterCategory('KU', this)" class="cat-btn px-2.5 py-1.5 rounded-lg font-semibold text-xs bg-rose-100 text-rose-800 hover:bg-rose-200 transition cursor-pointer">
                    KU (Keuangan & BOS)
                </button>
            </div>
        </div>

        <div id="classificationGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($classifications as $item)
                @php
                    $code = strtoupper(trim($item->code));
                    $name = $item->name;
                    $desc = $item->description;

                    if (str_starts_with($code, 'PK')) {
                        $cardStyle = 'border-sky-200 bg-sky-50/20';
                        $badgeStyle = 'bg-sky-100 text-sky-800 border-sky-300';
                        $catGroup = 'PK';
                        $groupLabel = 'Lembar I - Substantif Pendidikan';
                    } elseif (str_starts_with($code, 'KPG')) {
                        $cardStyle = 'border-emerald-200 bg-emerald-50/20';
                        $badgeStyle = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                        $catGroup = 'KPG';
                        $groupLabel = 'Lembar II - Kepegawaian GTK';
                    } elseif (str_starts_with($code, 'PS')) {
                        $cardStyle = 'border-amber-200 bg-amber-50/20';
                        $badgeStyle = 'bg-amber-100 text-amber-800 border-amber-300';
                        $catGroup = 'PS';
                        $groupLabel = 'Lembar III - Perpustakaan Sekolah';
                    } elseif (str_starts_with($code, 'KO') || str_starts_with($code, 'KS')) {
                        $cardStyle = 'border-teal-200 bg-teal-50/20';
                        $badgeStyle = 'bg-teal-100 text-teal-800 border-teal-300';
                        $catGroup = 'KO_KS';
                        $groupLabel = 'Lembar III - Kesiswaan, BK & UKS';
                    } elseif (str_starts_with($code, 'TU') || str_starts_with($code, 'HM')) {
                        $cardStyle = 'border-violet-200 bg-violet-50/20';
                        $badgeStyle = 'bg-violet-100 text-violet-800 border-violet-300';
                        $catGroup = 'TU_HM';
                        $groupLabel = 'Lembar III - Ketatausahaan & Humas';
                    } elseif (str_starts_with($code, 'PL') || str_starts_with($code, 'RT')) {
                        $cardStyle = 'border-orange-200 bg-orange-50/20';
                        $badgeStyle = 'bg-orange-100 text-orange-800 border-orange-300';
                        $catGroup = 'PL_RT';
                        $groupLabel = 'Lembar IV - Sarpras & Rumah Tangga';
                    } elseif (str_starts_with($code, 'KU')) {
                        $cardStyle = 'border-rose-200 bg-rose-50/20';
                        $badgeStyle = 'bg-rose-100 text-rose-800 border-rose-300';
                        $catGroup = 'KU';
                        $groupLabel = 'Lembar IV - Keuangan & BOS';
                    } else {
                        $cardStyle = 'border-slate-200 bg-slate-50/30';
                        $badgeStyle = 'bg-slate-100 text-slate-800 border-slate-300';
                        $catGroup = 'OTHER';
                        $groupLabel = 'Kode Klasifikasi Umum';
                    }
                @endphp
                <div class="class-card p-4 rounded-xl border {{ $cardStyle }} hover:shadow-md transition flex flex-col justify-between" data-category="{{ $catGroup }}">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono font-bold text-xs sm:text-sm px-2.5 py-0.5 rounded-lg border {{ $badgeStyle }} tracking-wide">
                                {{ $code }}
                            </span>
                            <div class="flex items-center gap-1">
                                <button type="button" 
                                        onclick="copyToClipboard('{{ $code }}', this)" 
                                        class="px-2 py-0.5 rounded text-[10px] font-semibold bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 transition cursor-pointer shadow-2xs">
                                    Salin
                                </button>
                                <a href="{{ route('letters.outgoing.create', ['classification_code' => $code]) }}" 
                                   title="Gunakan untuk buat surat keluar baru"
                                   class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 border border-blue-200 hover:bg-blue-100 text-blue-700 transition">
                                    Pakai
                                </a>
                            </div>
                        </div>

                        <div class="text-xs font-bold text-slate-900 mt-2.5 leading-snug">
                            {{ \Illuminate\Support\Str::after($name, ' - ') ?: $name }}
                        </div>

                        @if($desc)
                            <p class="text-[11px] text-slate-600 mt-1.5 leading-relaxed">
                                {{ $desc }}
                            </p>
                        @endif
                    </div>

                    <div class="text-[10px] text-slate-400 mt-3 flex items-center justify-between border-t border-slate-200/60 pt-2">
                        <span class="truncate font-medium text-slate-500">{{ $groupLabel }}</span>
                        <span class="font-mono text-[9px] text-slate-400 shrink-0 ml-1">SIKD Jabar</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
let currentCategory = 'all';

function filterCategory(cat, btn) {
    currentCategory = cat;
    document.querySelectorAll('.cat-btn').forEach(b => {
        b.classList.remove('bg-slate-800', 'text-white');
        b.classList.add('opacity-80');
    });
    btn.classList.add('bg-slate-800', 'text-white');
    btn.classList.remove('opacity-80');
    applyFilters();
}

function filterClassifications() {
    applyFilters();
}

function applyFilters() {
    const input = document.getElementById('searchClassification').value.toLowerCase();
    const cards = document.querySelectorAll('.class-card');

    cards.forEach(card => {
        const text = card.innerText.toLowerCase();
        const cat = card.getAttribute('data-category');

        const matchesSearch = text.includes(input);
        const matchesCat = (currentCategory === 'all' || cat === currentCategory);

        card.style.display = (matchesSearch && matchesCat) ? 'flex' : 'none';
    });
}

function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalText = btn.innerText;
        btn.innerText = 'Tersalin! ✓';
        btn.classList.add('bg-emerald-100', 'text-emerald-800', 'border-emerald-300');
        setTimeout(() => {
            btn.innerText = originalText;
            btn.classList.remove('bg-emerald-100', 'text-emerald-800', 'border-emerald-300');
        }, 1500);
    });
}
</script>
@endsection
