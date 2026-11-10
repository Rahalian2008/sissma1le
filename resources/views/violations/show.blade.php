@extends('layouts.app')

@section('title', 'Detail Pelanggaran Tata Tertib')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $violation->status === 'DIVERIFIKASI' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300' }}">
                    {{ str_replace('_', ' ', $violation->status) }}
                </span>
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-2">{{ $violation->title }}</h2>
                <p class="text-xs text-slate-400 dark:text-slate-500 font-mono">{{ $violation->violation_code }}</p>
            </div>
            <a href="{{ route('violations.index') }}" class="text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200">← Kembali</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-6">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                <div class="text-slate-500 dark:text-slate-400 font-semibold">Nama Siswa:</div>
                <div class="font-bold text-sm text-slate-900 dark:text-white">{{ $violation->student->name }}</div>
                <div class="text-slate-400">NIS: {{ $violation->student->nis }} • Kelas {{ $violation->schoolClass?->name }}</div>
            </div>

            <div class="p-4 rounded-2xl bg-rose-50/60 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-900/50 space-y-1.5">
                <div class="text-rose-800 dark:text-rose-300 font-semibold">Poin Pelanggaran:</div>
                <div class="font-black text-2xl text-rose-600 dark:text-rose-400">{{ $violation->points }} Poin</div>
                <div class="text-rose-700 dark:text-rose-300 font-medium">Status Siswa: {{ $violation->student->guidance_status['level'] }}</div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                <div class="text-slate-500 dark:text-slate-400 font-semibold">Kategori Pelanggaran:</div>
                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $violation->category->code }} - {{ $violation->category->name }}</div>
                <div class="text-slate-400">Waktu: {{ $violation->date->format('d F Y') }} • {{ substr($violation->time ?? '00:00', 0, 5) }} WIB</div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                <div class="text-slate-500 dark:text-slate-400 font-semibold">Pelapor & Verifikator:</div>
                <div class="text-slate-700 dark:text-slate-300">Dicatat oleh: <strong>{{ $violation->reporter->name ?? 'Pendidik' }}</strong></div>
                <div class="text-slate-700 dark:text-slate-300">Verifikator: <strong>{{ $violation->verifier->name ?? 'Menunggu Verifikasi' }}</strong></div>
            </div>
        </div>

        @if($violation->chronology)
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 text-xs">
                <span class="font-bold text-slate-700 dark:text-slate-200 block mb-1">Kronologi Kejadian:</span>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $violation->chronology }}</p>
            </div>
        @endif

        @if($violation->guidance_notes)
            <div class="mb-6 p-4 rounded-2xl bg-yellow-50/60 dark:bg-yellow-950/40 border border-yellow-200 dark:border-yellow-900/50 text-xs">
                <span class="font-bold text-yellow-900 dark:text-yellow-300 block mb-1">Catatan Pembinaan Awal / Arahan:</span>
                <p class="text-yellow-800 dark:text-yellow-400 leading-relaxed">{{ $violation->guidance_notes }}</p>
            </div>
        @endif

        @if($violation->evidence_path)
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs">
                <span class="font-bold text-slate-700 dark:text-slate-200 block mb-2">Dokumen / Foto Bukti Pendukung:</span>
                <a href="{{ Storage::url($violation->evidence_path) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-800 text-white font-bold rounded-xl shadow-xs hover:bg-slate-800 dark:hover:bg-slate-700 transition">
                    Lihat Bukti Foto / Dokumen ↗
                </a>
            </div>
        @endif

        @if(Auth::user()->isKesiswaanAdmin() && $violation->status === 'MENUNGGU_VERIFIKASI')
            <div class="p-5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h4 class="font-bold text-xs text-amber-900 dark:text-amber-300 uppercase tracking-wider">Aksi Keputusan Verifikasi (Admin / Wakasek Kesiswaan)</h4>
                </div>
                <p class="text-xs text-amber-800 dark:text-amber-300">
                    Laporan ini berstatus <strong>Pending</strong>. Poin pelanggaran baru akan resmi masuk ke catatan final siswa setelah Anda menyetujui (Approve) laporan ini.
                </p>
                <div class="flex items-center gap-3 pt-1">
                    <form action="{{ route('violations.verify', $violation->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="DIVERIFIKASI">
                        <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs shadow-md transition">
                            ✓ Setujui (Approve) & Masukkan ke Catatan Final
                        </button>
                    </form>

                    <button type="button" onclick="document.getElementById('reject-viol-box').classList.toggle('hidden')" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-xl text-xs transition">
                        Tolak Laporan
                    </button>
                </div>

                <div id="reject-viol-box" class="hidden pt-3 border-t border-amber-200 dark:border-amber-900/50">
                    <form action="{{ route('violations.verify', $violation->id) }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="hidden" name="status" value="DITOLAK">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Alasan Penolakan / Keterangan Tidak Valid:</label>
                        <textarea name="rejection_reason" required rows="2" placeholder="Tuliskan alasan penolakan..." class="w-full p-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl text-xs"></textarea>
                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-bold rounded-xl text-xs">Kirim Penolakan</button>
                    </form>
                </div>
            </div>
        @elseif($violation->status === 'MENUNGGU_VERIFIKASI')
            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-300 text-xs flex items-start gap-3">
                <span class="text-base mt-0.5">⏳</span>
                <div>
                    <h5 class="font-bold">Menunggu Persetujuan Admin / Wakasek Kesiswaan</h5>
                    <p class="text-amber-800 dark:text-amber-300 mt-0.5">
                        Pencatatan pelanggaran ini berstatus <strong>Pending</strong> dan sedang menunggu keputusan verifikasi/approve dari Admin atau Wakasek Kesiswaan sebelum resmi masuk ke catatan final siswa.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
