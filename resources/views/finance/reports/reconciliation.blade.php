@extends('layouts.app')

@section('title', 'Rekonsiliasi & Pemeriksaan Kas Bulanan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">Pengawasan Keuangan Internal</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Permendagri No. 77/2020</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Berita Acara Rekonsiliasi & Penutupan Kas</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemeriksaan fisik saldo kas tunai di brankas dan saldo rekening koran bank oleh Kepala Sekolah secara berkala setiap akhir bulan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.reports.index') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z"></path></svg>
                Laporan Realisasi
            </a>
            <a href="{{ route('finance.reconciliation.print', request()->all()) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Berita Acara
            </a>
            <form action="{{ route('finance.reconciliation.archive') }}" method="POST" class="inline" onsubmit="return confirm('Simpan Berita Acara Rekonsiliasi Kas ini ke Kearsipan Digital?')">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    Arsipkan BA Kas
                </button>
            </form>
        </div>
    </div>

    <!-- Filter Month / Year -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('finance.reconciliation.index') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs">
            <select name="month" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-bold">
                @foreach(range(1, 12) as $m)
                    @php $mStr = str_pad((string)$m, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $mStr }}" {{ (int)$month === $m ? 'selected' : '' }}>
                        Periode: Bulan {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endforeach
            </select>
            <select name="year" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-bold">
                @foreach(range(date('Y')-2, date('Y')+1) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endforeach
            </select>
            <select name="source" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                <option value="all">Semua Sumber Dana</option>
                <option value="BOSP Reguler" {{ $source === 'BOSP Reguler' ? 'selected' : '' }}>BOSP Reguler</option>
                <option value="BOSP Kinerja" {{ $source === 'BOSP Kinerja' ? 'selected' : '' }}>BOSP Kinerja</option>
                <option value="BOPD Pemprov" {{ $source === 'BOPD Pemprov' ? 'selected' : '' }}>BOPD Pemprov</option>
                <option value="Komite Sekolah" {{ $source === 'Komite Sekolah' ? 'selected' : '' }}>Komite Sekolah</option>
            </select>
        </form>
    </div>

    <!-- Reconciliation Verification Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Menurut BKU</div>
            <div class="text-xl font-black text-slate-900 font-mono mt-1">Rp {{ number_format($bkuBalance, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Buku Kas Umum</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Saldo Kas Tunai (Brankas)</div>
            <div class="text-xl font-black text-emerald-700 font-mono mt-1">Rp {{ number_format($cashBalance, 0, ',', '.') }}</div>
            <div class="text-[11px] text-emerald-600 mt-1">Buku Pembantu Kas</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Saldo Bank (Rekening)</div>
            <div class="text-xl font-black text-blue-700 font-mono mt-1">Rp {{ number_format($bankBalance, 0, ',', '.') }}</div>
            <div class="text-[11px] text-blue-600 mt-1">Buku Pembantu Bank</div>
        </div>
        <div class="p-5 rounded-2xl border shadow-xs {{ $difference === 0.0 ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200' }}">
            <div class="text-xs font-semibold {{ $difference === 0.0 ? 'text-emerald-800' : 'text-rose-800' }} uppercase tracking-wider">Status Rekonsiliasi</div>
            <div class="text-xl font-black {{ $difference === 0.0 ? 'text-emerald-700' : 'text-rose-700' }} font-mono mt-1">
                {{ $difference === 0.0 ? 'SEIMBANG (MATCH)' : 'SELISIH: Rp ' . number_format(abs($difference), 0, ',', '.') }}
            </div>
            <div class="text-[11px] {{ $difference === 0.0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1">
                {{ $difference === 0.0 ? 'Kas Fisik & Bank = BKU' : 'Terdapat selisih pencatatan' }}
            </div>
        </div>
    </div>

    <!-- Official Format Preview -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
        <h3 class="font-bold text-slate-900 text-sm mb-3">Ringkasan Instrumen Berita Acara Pemeriksaan Kas Bulanan:</h3>
        <div class="space-y-3 text-xs text-slate-700 leading-relaxed border-l-2 border-indigo-400 pl-4 py-1">
            <p>1. <strong>Buku Kas Umum (BKU)</strong> ditutup pada tanggal terakhir bulan <strong>{{ DateTime::createFromFormat('!m', (int)$month)->format('F') }} {{ $year }}</strong> dengan saldo akhir sebesar <strong>Rp {{ number_format($bkuBalance, 0, ',', '.') }}</strong>.</p>
            <p>2. Saldo tersebut berada pada:</p>
            <ul class="list-disc list-inside ml-2 space-y-1 font-mono">
                <li>Uang Kas Tunai di Brankas Bendahara: <strong>Rp {{ number_format($cashBalance, 0, ',', '.') }}</strong></li>
                <li>Saldo Simpanan di Rekening Bank Sekolah: <strong>Rp {{ number_format($bankBalance, 0, ',', '.') }}</strong></li>
                <li>Total Fisik Kas & Bank: <strong>Rp {{ number_format($calculatedTotal, 0, ',', '.') }}</strong></li>
            </ul>
            <p>3. Hasil pemeriksaan fisik dan dokumen membuktikan bahwa keadaan kas dan saldo bank <strong>SESUAI DAN TIDAK TERDAPAT SELISIH LEBIH/KURANG</strong>.</p>
        </div>
    </div>
</div>
@endsection
