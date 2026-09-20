@extends('layouts.app')

@section('title', 'Proyektor Sesi Presensi QR Guru')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">
                PROYEKTOR SESI KELAS
            </span>
            <h2 class="text-xl sm:text-2xl font-black text-white mt-1">Sesi Presensi QR Code Dinamis</h2>
            <p class="text-xs text-slate-300">Tampilkan kode ini di layar proyektor atau smart TV kelas untuk di-scan oleh siswa.</p>
        </div>
        <button onclick="document.getElementById('modal-create-session').classList.remove('hidden')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center gap-2">
            <span>+</span> Buat Sesi Baru
        </button>
    </div>

    @if($selectedSession)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Big QR Projector Card (Left 2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-10 text-center flex flex-col items-center justify-center space-y-6">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-200">
                <span>Kelas {{ $selectedSession->schoolClass?->name ?? 'Semua Siswa' }}</span>
                <span>•</span>
                <span>{{ $selectedSession->subject_or_activity }}</span>
            </div>

            <!-- Dynamic QR Code Container -->
            <div class="p-6 bg-slate-900 rounded-3xl shadow-2xl border-4 border-slate-800 flex flex-col items-center">
                <!-- SVG Simulated QR Code Box with token inside -->
                <div id="qr-container" class="w-64 h-64 sm:w-72 sm:h-72 bg-white rounded-2xl p-4 flex flex-col items-center justify-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($selectedSession->qr_code_token) }}"
                         alt="QR Code Sesi" class="w-full h-full object-contain"
                         onerror="this.style.display='none'; document.getElementById('qr-fallback').style.display='flex';">
                    <div id="qr-fallback" class="hidden flex-col items-center justify-center p-4 text-center">
                        <div class="font-mono font-bold text-xs text-slate-800 break-all">{{ $selectedSession->qr_code_token }}</div>
                    </div>
                </div>
                <div class="text-[11px] text-slate-300 font-mono mt-3">
                    Token: <strong class="text-white">{{ $selectedSession->qr_code_token }}</strong>
                </div>
            </div>

            <div class="space-y-1">
                <div class="text-xs text-slate-500 font-medium">Batas Waktu Berlaku Sesi:</div>
                <div class="text-sm font-bold text-slate-800">
                    Sampai Pukul {{ substr($selectedSession->end_time, 0, 5) }} WIB
                    @if($selectedSession->isQrValid())
                        <span class="text-emerald-600 font-bold ml-1">(Aktif)</span>
                    @else
                        <span class="text-rose-600 font-bold ml-1">(Kedaluwarsa)</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Side: Scanned Attendance Realtime List -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 flex flex-col h-[500px]">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Siswa Telah Presensi</h3>
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-black text-xs rounded-full">
                    {{ $selectedSession->attendances->count() }} Hadir
                </span>
            </div>

            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 py-2 space-y-1">
                @forelse($selectedSession->attendances as $att)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-800">{{ $att->student_name }}</div>
                            <div class="text-[10px] text-slate-400">NIS: {{ $att->nis }}</div>
                        </div>
                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                            {{ substr($att->time, 0, 5) }} WIB
                        </span>
                    </div>
                @empty
                    <div class="text-center py-16 text-slate-400 text-xs">
                        Belum ada siswa yang melakukan scan QR sesi ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @else
        <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center text-slate-500">
            <h3 class="text-base font-bold text-slate-800 mb-1">Belum Ada Sesi Presensi Aktif</h3>
            <p class="text-xs mb-4">Silakan buat sesi presensi QR baru untuk kelas Anda.</p>
            <button onclick="document.getElementById('modal-create-session').classList.remove('hidden')" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-xl text-xs">
                Buat Sesi Sekarang
            </button>
        </div>
    @endif

    <!-- Modal Create Session -->
    <div id="modal-create-session" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100">
            <h3 class="text-base font-bold text-slate-900 mb-1">Buat Sesi Presensi QR Baru</h3>
            <p class="text-xs text-slate-500 mb-5">QR Code akan aktif secara dinamis sesuai durasi yang dipilih.</p>

            <form action="{{ route('attendance.qr.session.create') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Target Kelas</label>
                    <select name="class_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-indigo-500">
                        <option value="">-- Semua Siswa (Umum) --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mata Pelajaran / Aktivitas</label>
                    <input type="text" name="subject_or_activity" required placeholder="Contoh: Fisika Wajib (KBM 1)"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Durasi Aktif QR (Menit)</label>
                    <input type="number" name="duration_minutes" value="45" min="5" max="240" required
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-indigo-500">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modal-create-session').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-800">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-md">
                        Mulai Sesi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
