@extends('layouts.app')

@section('title', 'Persetujuan Izin, Sakit & Pulang Cepat Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Persetujuan Izin, Sakit & Pulang Cepat Siswa</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                @if(Auth::user()->role === 'wali_kelas')
                    Verifikasi ajuan izin, sakit, dan izin pulang cepat siswa di kelas binaan <strong>{{ $homeroomClass?->name ?? 'Wali Kelas' }}</strong>.
                @else
                    Verifikasi dan persetujuan seluruh ajuan izin, sakit, dan izin pulang cepat siswa dari semua kelas.
                @endif
            </p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ request()->fullUrlWithQuery(['status' => 'PERLU_VERIFIKASI']) }}" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-amber-400 transition {{ $statusFilter === 'PERLU_VERIFIKASI' ? 'ring-2 ring-amber-500/20 border-amber-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Menunggu Persetujuan</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
            </div>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $stats['pending'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Memerlukan tindakan segera</div>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status' => 'DIVERIFIKASI']) }}" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-emerald-400 transition {{ $statusFilter === 'DIVERIFIKASI' ? 'ring-2 ring-emerald-500/20 border-emerald-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Telah Disetujui</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            </div>
            <div class="text-2xl font-black text-emerald-600 mt-2">{{ $stats['approved'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Presensi diakui resmi</div>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status' => 'DITOLAK']) }}" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-rose-400 transition {{ $statusFilter === 'DITOLAK' ? 'ring-2 ring-rose-500/20 border-rose-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ditolak</span>
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
            </div>
            <div class="text-2xl font-black text-rose-600 mt-2">{{ $stats['rejected'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Tidak memenuhi kriteria</div>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-1 overflow-x-auto pb-2 md:pb-0">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'PERLU_VERIFIKASI']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $statusFilter === 'PERLU_VERIFIKASI' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Menunggu ({{ $stats['pending'] }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'DIVERIFIKASI']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $statusFilter === 'DIVERIFIKASI' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Disetujui ({{ $stats['approved'] }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'DITOLAK']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $statusFilter === 'DITOLAK' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Ditolak ({{ $stats['rejected'] }})
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'ALL']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $statusFilter === 'ALL' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Semua Riwayat
            </a>
        </div>

        <!-- Class Filter for Admin / Super Admin -->
        @if(in_array(Auth::user()->role, ['admin', 'super_admin']))
            <form method="GET" action="{{ route('attendance.leaves.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <select name="class_id" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-medium text-slate-700 outline-none focus:border-indigo-500">
                    <option value="">Semua Kelas / Rombel</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
                @if($classId)
                    <a href="{{ route('attendance.leaves.index', ['status' => $statusFilter]) }}" class="text-xs text-rose-500 hover:underline font-bold">Reset</a>
                @endif
            </form>
        @else
            <div class="text-xs font-bold text-slate-500">
                Kelas: <span class="text-indigo-600">{{ $homeroomClass?->name ?? 'Kelas Binaan' }}</span>
            </div>
        @endif
    </div>

    <!-- Leaves List -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @if($leaves->isEmpty())
            <div class="p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="font-bold text-sm text-slate-600">Tidak ada data ajuan pada filter ini</div>
                <p class="text-xs text-slate-400 mt-1">Seluruh pengajuan izin dan sakit siswa telah tertangani dengan baik.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($leaves as $leave)
                    <div class="p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-6 hover:bg-slate-50/50 transition">
                        <!-- Left: Student Info & Photo -->
                        <div class="flex items-start gap-4">
                            <!-- Selfie Image with zoom trigger -->
                            <div class="relative group cursor-pointer shrink-0" onclick="viewPhoto('{{ asset('storage/'.$leave->selfie_path) }}', '{{ $leave->student_name }}')">
                                @if($leave->selfie_path)
                                    <img src="{{ asset('storage/'.$leave->selfie_path) }}" alt="Selfie" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-2 border-indigo-100 shadow-sm group-hover:scale-105 transition">
                                    <div class="absolute inset-0 bg-black/30 rounded-2xl opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition">
                                        Perbesar
                                    </div>
                                @else
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xs font-bold">
                                        No Pic
                                    </div>
                                @endif
                            </div>

                            <!-- Student Info -->
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">{{ $leave->student_name }}</h3>
                                    @php
                                        $isPc = $leave->is_early_leave || $leave->status === 'PULANG_CEPAT';
                                        $badgeColor = $leave->status === 'SAKIT' ? 'bg-rose-100 text-rose-800' : ($isPc ? 'bg-orange-100 text-orange-800 border border-orange-200' : 'bg-amber-100 text-amber-800');
                                        $badgeLabel = $isPc ? 'PULANG CEPAT' : $leave->status;
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-extrabold {{ $badgeColor }}">
                                        {{ $badgeLabel }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $leave->schoolClass?->name ?? 'Kelas -' }}
                                    </span>
                                    <span class="text-xs text-slate-400">NIS: {{ $leave->nis }}</span>
                                </div>

                                @if($isPc)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-orange-50 border border-orange-200 text-orange-900 text-xs font-bold mt-1">
                                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Rencana Pulang: <strong>{{ substr($leave->early_leave_time ?? $leave->time_out ?? $leave->time, 0, 5) }} WIB</strong></span>
                                        @if($leave->time_out && $leave->verification_status === 'DIVERIFIKASI')
                                            <span class="text-emerald-700 font-semibold">• Check-out: {{ substr($leave->time_out, 0, 5) }} WIB</span>
                                        @endif
                                    </div>
                                @endif

                                <p class="text-xs text-slate-700 leading-relaxed font-medium bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    "{{ $leave->notes }}"
                                </p>

                                <!-- Meta: Tanggal, Waktu & GPS -->
                                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 pt-1">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $leave->date->translatedFormat('d F Y') }} • {{ substr($leave->time, 0, 5) }} WIB
                                    </span>

                                    @if($leave->latitude && $leave->longitude)
                                        <a href="https://www.google.com/maps?q={{ $leave->latitude }},{{ $leave->longitude }}" target="_blank"
                                            class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-bold bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100 hover:bg-indigo-100 transition">
                                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            GPS: {{ round($leave->latitude, 4) }}, {{ round($leave->longitude, 4) }} (Buka Peta)
                                        </a>
                                    @endif

                                    @if($leave->attachment_path)
                                        <a href="{{ asset('storage/'.$leave->attachment_path) }}" target="_blank"
                                            class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-900 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100 transition">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            Lihat Surat Lampiran
                                        </a>
                                    @endif
                                </div>

                                @if($leave->verification_status === 'DIVERIFIKASI')
                                    <div class="text-[11px] text-emerald-700 font-medium pt-1">
                                        Disetujui oleh: <strong>{{ $leave->approver?->name ?? 'Administrator' }}</strong> pada {{ $leave->approved_at?->format('d/m/Y H:i') }}
                                    </div>
                                @elseif($leave->verification_status === 'DITOLAK')
                                    <div class="text-[11px] text-rose-600 font-medium pt-1">
                                        Ditolak oleh: <strong>{{ $leave->approver?->name ?? 'Administrator' }}</strong>. Alasan: <em>{{ $leave->rejection_reason }}</em>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Right: Actions -->
                        <div class="flex items-center gap-2 self-end lg:self-center shrink-0">
                            @if($leave->verification_status === 'PERLU_VERIFIKASI')
                                <!-- Approve Button -->
                                <form action="{{ route('attendance.leaves.approve', $leave) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin menyetujui ajuan {{ $badgeLabel }} siswa {{ $leave->student_name }}?')"
                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Setujui
                                    </button>
                                </form>

                                <!-- Reject Button Trigger -->
                                <button type="button" onclick="openRejectModal('{{ $leave->id }}', '{{ $leave->student_name }}', '{{ $badgeLabel }}')"
                                    class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl text-xs transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Tolak
                                </button>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $leave->verification_status === 'DIVERIFIKASI' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $leave->verification_status === 'DIVERIFIKASI' ? '✓ Disetujui' : '✕ Ditolak' }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tolak Ajuan -->
<div id="reject-modal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-base">Tolak Ajuan Izin / Sakit</h3>
            <button onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <p class="text-xs text-slate-600" id="reject-modal-desc">
            Masukkan alasan penolakan untuk siswa yang bersangkutan.
        </p>

        <form id="reject-form" action="" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="reject-reason" class="block text-xs font-bold text-slate-700 mb-1">
                    Alasan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea name="reason" id="reject-reason" rows="3" required placeholder="Contoh: Foto selfie tidak menunjukkan wajah siswa yang jelas / tanpa konfirmasi wali siswa..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:border-rose-500 outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs shadow-md">
                    Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Zoom Foto Selfie -->
<div id="photo-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs hidden flex items-center justify-center p-4" onclick="closePhotoModal()">
    <div class="relative max-w-lg w-full bg-slate-900 rounded-3xl overflow-hidden shadow-2xl p-2 text-center" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between p-3 text-white">
            <span class="font-bold text-xs" id="photo-student-name">Foto Selfie Siswa</span>
            <button onclick="closePhotoModal()" class="text-slate-400 hover:text-white text-sm">✕ Tutup</button>
        </div>
        <img id="photo-modal-img" src="" alt="Foto Selfie" class="w-full max-h-[70vh] object-contain rounded-2xl">
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(id, name, status) {
        document.getElementById('reject-form').action = `/attendance/leaves/${id}/reject`;
        document.getElementById('reject-modal-desc').innerText = `Anda akan menolak pengajuan ${status} untuk siswa ${name}. Silakan berikan alasan penolakan:`;
        document.getElementById('reject-modal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('reject-modal').classList.add('hidden');
        document.getElementById('reject-reason').value = '';
    }

    function viewPhoto(url, name) {
        document.getElementById('photo-modal-img').src = url;
        document.getElementById('photo-student-name').innerText = `Foto Selfie: ${name}`;
        document.getElementById('photo-modal').classList.remove('hidden');
    }

    function closePhotoModal() {
        document.getElementById('photo-modal').classList.add('hidden');
    }
</script>
@endpush
@endsection
