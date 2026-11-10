@extends('layouts.app')

@section('title', 'Detail Prestasi Siswa')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $achievement->status === 'DIVERIFIKASI' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ str_replace('_', ' ', $achievement->status) }}
                </span>
                <h2 class="text-xl font-extrabold text-slate-900 mt-2">{{ $achievement->title }}</h2>
                <p class="text-xs text-slate-400 font-mono">{{ $achievement->achievement_code }}</p>
            </div>
            <a href="{{ route('achievements.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">← Kembali</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-6">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                <div class="text-slate-500 font-semibold">Nama Siswa:</div>
                <div class="font-bold text-sm text-slate-900">{{ $achievement->student->name }}</div>
                <div class="text-slate-400">NIS: {{ $achievement->student->nis }} • Kelas {{ $achievement->schoolClass?->name }}</div>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-1.5">
                <div class="text-amber-800 font-semibold">Poin Penghargaan:</div>
                <div class="font-black text-2xl text-amber-600">+{{ $achievement->points }} Poin</div>
                <div class="text-amber-700 font-medium">Tingkat: {{ $achievement->level }}</div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                <div class="text-slate-500 font-semibold">Kategori Prestasi:</div>
                <div class="font-bold text-slate-800">{{ $achievement->category->code }} - {{ $achievement->category->name }}</div>
                <div class="text-slate-400">Tanggal: {{ $achievement->date->format('d F Y') }}</div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                <div class="text-slate-500 font-semibold">Pelapor & Verifikator:</div>
                <div class="text-slate-700">Dicatat oleh: <strong>{{ $achievement->reporter->name ?? 'Sistem' }}</strong></div>
                <div class="text-slate-700">Verifikator: <strong>{{ $achievement->verifier->name ?? 'Menunggu Verifikasi' }}</strong></div>
            </div>
        </div>

        @if($achievement->description)
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                <span class="font-bold text-slate-700 block mb-1">Uraian / Catatan Kegiatan:</span>
                <p class="text-slate-600 leading-relaxed">{{ $achievement->description }}</p>
            </div>
        @endif

        @if($achievement->certificate_path)
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                <span class="font-bold text-slate-700 block mb-2">Dokumen Bukti Sertifikat / Piagam:</span>
                <a href="{{ Storage::url($achievement->certificate_path) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl shadow-xs hover:bg-indigo-500 transition">
                    Lihat Berkas Sertifikat (Unduh) ↗
                </a>
            </div>
        @endif

        @if(Auth::user()->isKesiswaanAdmin() && $achievement->status === 'MENUNGGU_VERIFIKASI')
            <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h4 class="font-bold text-xs text-amber-900 uppercase tracking-wider">Aksi Keputusan Verifikasi (Admin / Wakasek Kesiswaan)</h4>
                </div>
                <p class="text-xs text-amber-800">
                    Pencatatan prestasi ini berstatus <strong>Pending</strong>. Poin prestasi baru akan resmi masuk ke catatan final siswa setelah Anda menyetujui (Approve) laporan ini.
                </p>
                <div class="flex items-center gap-3 pt-1">
                    <form action="{{ route('achievements.verify', $achievement->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="DIVERIFIKASI">
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md transition">
                            ✓ Setujui (Approve) & Masukkan ke Catatan Final
                        </button>
                    </form>

                    <button type="button" onclick="document.getElementById('reject-box').classList.toggle('hidden')" class="px-5 py-2.5 bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold rounded-xl text-xs transition">
                        Tolak Prestasi
                    </button>
                </div>

                <div id="reject-box" class="hidden pt-3 border-t border-amber-200">
                    <form action="{{ route('achievements.verify', $achievement->id) }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="hidden" name="status" value="DITOLAK">
                        <label class="block text-xs font-semibold text-slate-700">Alasan Penolakan:</label>
                        <textarea name="rejection_reason" required rows="2" placeholder="Tuliskan alasan penolakan..." class="w-full p-2 bg-white border border-slate-200 rounded-xl text-xs"></textarea>
                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-bold rounded-xl text-xs">Kirim Penolakan</button>
                    </form>
                </div>
            </div>
        @elseif($achievement->status === 'MENUNGGU_VERIFIKASI')
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
                <span class="text-base mt-0.5">⏳</span>
                <div>
                    <h5 class="font-bold">Menunggu Persetujuan Admin / Wakasek Kesiswaan</h5>
                    <p class="text-amber-800 mt-0.5">
                        Pencatatan prestasi ini berstatus <strong>Pending</strong> dan sedang menunggu keputusan verifikasi/approve dari Admin atau Wakasek Kesiswaan sebelum resmi masuk ke catatan final siswa.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
