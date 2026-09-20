@extends('layouts.app')

@section('title', 'Detail Pelanggaran Tata Tertib')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $violation->status === 'DIVERIFIKASI' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ str_replace('_', ' ', $violation->status) }}
                </span>
                <h2 class="text-xl font-extrabold text-slate-900 mt-2">{{ $violation->title }}</h2>
                <p class="text-xs text-slate-400 font-mono">{{ $violation->violation_code }}</p>
            </div>
            <a href="{{ route('violations.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">← Kembali</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-6">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                <div class="text-slate-500 font-semibold">Nama Siswa:</div>
                <div class="font-bold text-sm text-slate-900">{{ $violation->student->name }}</div>
                <div class="text-slate-400">NIS: {{ $violation->student->nis }} • Kelas {{ $violation->schoolClass?->name }}</div>
            </div>

            <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200/80 space-y-1.5">
                <div class="text-rose-800 font-semibold">Poin Pelanggaran:</div>
                <div class="font-black text-2xl text-rose-600">{{ $violation->points }} Poin</div>
                <div class="text-rose-700 font-medium">Status Siswa: {{ $violation->student->guidance_status['level'] }}</div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                <div class="text-slate-500 font-semibold">Kategori Pelanggaran:</div>
                <div class="font-bold text-slate-800">{{ $violation->category->code }} - {{ $violation->category->name }}</div>
                <div class="text-slate-400">Waktu: {{ $violation->date->format('d F Y') }} • {{ substr($violation->time ?? '00:00', 0, 5) }} WIB</div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                <div class="text-slate-500 font-semibold">Pelapor & Lokasi:</div>
                <div class="text-slate-700">Pelapor: <strong>{{ $violation->reporter->name ?? 'Pendidik' }}</strong></div>
                <div class="text-slate-700">Tempat: <strong>{{ $violation->location ?? '-' }}</strong></div>
            </div>
        </div>

        @if($violation->chronology)
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                <span class="font-bold text-slate-700 block mb-1">Kronologi Kejadian:</span>
                <p class="text-slate-600 leading-relaxed">{{ $violation->chronology }}</p>
            </div>
        @endif

        @if($violation->guidance_notes)
            <div class="mb-6 p-4 rounded-2xl bg-yellow-50/60 border border-yellow-200 text-xs">
                <span class="font-bold text-yellow-900 block mb-1">Catatan Pembinaan Awal / Arahan:</span>
                <p class="text-yellow-800 leading-relaxed">{{ $violation->guidance_notes }}</p>
            </div>
        @endif

        @if($violation->evidence_path)
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                <span class="font-bold text-slate-700 block mb-2">Dokumen / Foto Bukti Pendukung:</span>
                <a href="{{ Storage::url($violation->evidence_path) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white font-bold rounded-xl shadow-xs hover:bg-slate-800 transition">
                    Lihat Bukti Foto / Dokumen ↗
                </a>
            </div>
        @endif

        @if(in_array(Auth::user()->role, ['wali_kelas', 'bk', 'kesiswaan', 'admin', 'super_admin']) && $violation->status === 'MENUNGGU_VERIFIKASI')
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Aksi Verifikasi Pendidik</h4>
                <div class="flex items-center gap-3">
                    <form action="{{ route('violations.verify', $violation->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="DIVERIFIKASI">
                        <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs shadow-md transition">
                            ✓ Konfirmasi & Masukkan Poin
                        </button>
                    </form>

                    <button type="button" onclick="document.getElementById('reject-viol-box').classList.toggle('hidden')" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-xl text-xs transition">
                        Tolak Laporan
                    </button>
                </div>

                <div id="reject-viol-box" class="hidden pt-3 border-t border-slate-200">
                    <form action="{{ route('violations.verify', $violation->id) }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="hidden" name="status" value="DITOLAK">
                        <label class="block text-xs font-semibold text-slate-700">Alasan Penolakan / Keterangan Tidak Valid:</label>
                        <textarea name="rejection_reason" required rows="2" placeholder="Tuliskan alasan penolakan..." class="w-full p-2 bg-white border border-slate-200 rounded-xl text-xs"></textarea>
                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-bold rounded-xl text-xs">Kirim Penolakan</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
