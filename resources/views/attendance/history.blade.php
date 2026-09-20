@extends('layouts.app')

@section('title', 'Riwayat Presensi Siswa')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header & Filter -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-200 mb-1.5">
                    DATA PRESENSI SISWA TERPADU
                </div>
                <h2 class="text-xl font-bold text-slate-900">Riwayat Presensi Per Kelas & Siswa</h2>
                <p class="text-xs text-slate-500">Merekam presensi masuk & pulang dengan foto selfie langsung, titik geolokasi GPS, dan validasi otomatis</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export.csv', ['type' => 'attendance']) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ekspor CSV
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('attendance.history') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
            <!-- Filter Pencarian Siswa -->
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Cari Siswa / NIS</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama siswa atau NIS..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 text-xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Filter Kelas (Sesuai Permintaan Audio) -->
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Pilih Kelas</label>
                <select name="class_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                    <option value="">-- Semua Kelas --</option>
                    @if(isset($classes))
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string) request('class_id') === (string) $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Filter Tanggal -->
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Status Kehadiran</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                    <option value="">-- Semua Status --</option>
                    <option value="HADIR" {{ request('status') === 'HADIR' ? 'selected' : '' }}>HADIR</option>
                    <option value="TERLAMBAT" {{ request('status') === 'TERLAMBAT' ? 'selected' : '' }}>TERLAMBAT</option>
                    <option value="IZIN" {{ request('status') === 'IZIN' ? 'selected' : '' }}>IZIN</option>
                    <option value="SAKIT" {{ request('status') === 'SAKIT' ? 'selected' : '' }}>SAKIT</option>
                    <option value="ALPA" {{ request('status') === 'ALPA' ? 'selected' : '' }}>ALPA</option>
                </select>
            </div>

            <!-- Filter Metode -->
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Metode</label>
                <select name="method" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500">
                    <option value="">-- Semua Metode --</option>
                    <option value="SELFIE" {{ request('method') === 'SELFIE' ? 'selected' : '' }}>Selfie Kamera</option>
                    <option value="QR_CODE" {{ request('method') === 'QR_CODE' ? 'selected' : '' }}>QR Code Sesi</option>
                    <option value="RFID" {{ request('method') === 'RFID' ? 'selected' : '' }}>Kartu RFID</option>
                    <option value="MANUAL" {{ request('method') === 'MANUAL' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>

            <!-- Tombol Aksi Filter -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition shadow-xs">
                    Filter
                </button>
                <a href="{{ route('attendance.history') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal & Sesi</th>
                        <th class="py-3.5 px-4">Siswa & Kelas</th>
                        <th class="py-3.5 px-4">Foto Selfie</th>
                        <th class="py-3.5 px-4">Lokasi & GPS</th>
                        <th class="py-3.5 px-4">Metode</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Validasi</th>
                        <th class="py-3.5 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $att)
                        @php
                            $className = $att->schoolClass?->name ?? $att->student?->schoolClass?->name ?? '-';
                            $selfieIn = $att->selfie_path ? asset('storage/' . $att->selfie_path) : null;
                            $selfieOut = $att->out_selfie_path ? asset('storage/' . $att->out_selfie_path) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Tanggal & Jam Masuk / Pulang -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $att->date->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-emerald-600 font-semibold mt-0.5 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Masuk: {{ $att->time ? substr($att->time, 0, 5) . ' WIB' : '-' }}
                                </div>
                                <div class="text-[11px] text-sky-600 font-semibold mt-0.5 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                    Pulang: {{ $att->time_out ? substr($att->time_out, 0, 5) . ' WIB' : '-' }}
                                </div>
                            </td>

                            <!-- Siswa & Kelas (Jelas Per Kelas & Per Siswa) -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $att->student_name }}</div>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-extrabold text-[10px] border border-indigo-200">
                                        {{ $className }}
                                    </span>
                                    <span class="text-slate-400 text-[11px]">NIS: {{ $att->nis }}</span>
                                </div>
                            </td>

                            <!-- Foto Selfie (Masuk & Pulang) -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    @if($selfieIn)
                                        <div class="text-center">
                                            <button type="button" onclick="previewImage('{{ $selfieIn }}', '{{ $att->student_name }}', 'Foto Masuk ({{ substr($att->time, 0, 5) }} WIB)')" class="group relative block focus:outline-none">
                                                <img src="{{ $selfieIn }}" alt="Selfie Masuk" class="w-10 h-10 object-cover rounded-xl border border-slate-200 group-hover:ring-2 group-hover:ring-indigo-500 shadow-xs transition">
                                                <span class="block text-[9px] font-bold text-emerald-700 mt-0.5">Masuk</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if($selfieOut)
                                        <div class="text-center">
                                            <button type="button" onclick="previewImage('{{ $selfieOut }}', '{{ $att->student_name }}', 'Foto Pulang ({{ substr($att->time_out, 0, 5) }} WIB)')" class="group relative block focus:outline-none">
                                                <img src="{{ $selfieOut }}" alt="Selfie Pulang" class="w-10 h-10 object-cover rounded-xl border border-slate-200 group-hover:ring-2 group-hover:ring-sky-500 shadow-xs transition">
                                                <span class="block text-[9px] font-bold text-sky-700 mt-0.5">Pulang</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if(!$selfieIn && !$selfieOut)
                                        <span class="text-slate-400 text-[11px] italic">Tanpa Foto</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Lokasi & Peta GPS -->
                            <td class="py-3.5 px-4 text-slate-500">
                                @if($att->latitude && $att->longitude)
                                    <div class="font-mono text-[10px] text-slate-700">
                                        {{ number_format($att->latitude, 5) }}, {{ number_format($att->longitude, 5) }}
                                    </div>
                                    <div class="mt-1">
                                        <a href="https://www.google.com/maps?q={{ $att->latitude }},{{ $att->longitude }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[10px] transition border border-indigo-200">
                                            <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Buka Peta
                                        </a>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[10px] flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Area Sekolah
                                    </span>
                                @endif
                            </td>

                            <!-- Metode Presensi -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $att->method === 'SELFIE' ? 'bg-emerald-100 text-emerald-800' : ($att->method === 'QR_CODE' ? 'bg-sky-100 text-sky-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $att->method }}
                                </span>
                                @if($att->out_method && $att->out_method !== $att->method)
                                    <span class="block text-[9px] text-slate-400 mt-0.5">Pulang: {{ $att->out_method }}</span>
                                @endif
                            </td>

                            <!-- Status Kehadiran -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $att->status === 'HADIR' ? 'bg-emerald-100 text-emerald-800' : ($att->status === 'TERLAMBAT' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $att->status }}
                                </span>
                            </td>

                            <!-- Status Validasi (Otomatis VALID untuk Hadir) -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($att->verification_status === 'VALID' || in_array($att->status, ['HADIR', 'TERLAMBAT']))
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1 w-fit">
                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        VALID
                                    </span>
                                @elseif($att->verification_status === 'PERLU_VERIFIKASI')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                                        Menunggu
                                    </span>
                                @elseif($att->verification_status === 'DIVERIFIKASI')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ $att->verification_status }}
                                    </span>
                                @endif
                            </td>

                            <!-- Tindakan -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if(in_array(Auth::user()->role, ['guru', 'wali_kelas', 'bk', 'kesiswaan', 'admin', 'super_admin']))
                                    <button type="button" onclick="openVerifyModal('{{ $att->id }}', '{{ $att->student_name }}', '{{ $att->status }}')"
                                        class="px-2.5 py-1 bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 font-bold rounded-lg transition text-[11px]">
                                        Detail
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[10px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <div class="max-w-xs mx-auto text-center">
                                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-bold text-slate-600">Tidak ada riwayat presensi yang sesuai</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Coba ubah filter kelas, tanggal, atau kata kunci pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

    <!-- Photo Lightbox Modal -->
    <div id="photo-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" onclick="closePhotoModal(event)">
        <div class="bg-white rounded-3xl p-5 max-w-md w-full shadow-2xl border border-slate-100 text-center" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                <div class="text-left">
                    <h3 class="font-bold text-sm text-slate-900" id="photo-modal-student">Nama Siswa</h3>
                    <p class="text-xs text-slate-500" id="photo-modal-caption">Foto Presensi Selfie</p>
                </div>
                <button type="button" onclick="closePhotoModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center font-bold">
                    ✕
                </button>
            </div>
            <div class="relative aspect-4/3 bg-slate-900 rounded-2xl overflow-hidden shadow-inner flex items-center justify-center mb-4">
                <img id="photo-modal-img" src="" alt="Bukti Selfie Siswa" class="w-full h-full object-contain">
            </div>
            <button type="button" onclick="closePhotoModal()" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition">
                Tutup Tampilan Foto
            </button>
        </div>
    </div>

    <!-- Verification / Detail Modal -->
    <div id="verify-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-slate-100">
            <h3 class="text-base font-bold text-slate-900 mb-1">Verifikasi / Penyesuaian Presensi</h3>
            <p class="text-xs text-slate-500 mb-4" id="modal-student-name">Nama Siswa</p>

            <form id="verify-form" method="POST" action="" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Kehadiran:</label>
                    <select name="status" id="modal-status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        <option value="HADIR">HADIR</option>
                        <option value="TERLAMBAT">TERLAMBAT</option>
                        <option value="IZIN">IZIN</option>
                        <option value="SAKIT">SAKIT</option>
                        <option value="ALPA">ALPA</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Validasi:</label>
                    <select name="verification_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        <option value="VALID">VALID (Tervalidasi)</option>
                        <option value="DIVERIFIKASI">DIVERIFIKASI (Disetujui Manual)</option>
                        <option value="DITOLAK">DITOLAK (Tidak Sah)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Catatan:</label>
                    <textarea name="notes" rows="2" placeholder="Catatan tindak lanjut..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('verify-modal').classList.add('hidden')" class="px-4 py-2 font-bold text-slate-500 hover:text-slate-800">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl shadow-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openVerifyModal(id, studentName, currentStatus) {
        document.getElementById('modal-student-name').innerText = "Siswa: " + studentName;
        document.getElementById('modal-status').value = currentStatus;
        document.getElementById('verify-form').action = "/attendance/" + id + "/verify";
        document.getElementById('verify-modal').classList.remove('hidden');
    }

    function previewImage(src, studentName, caption) {
        document.getElementById('photo-modal-img').src = src;
        document.getElementById('photo-modal-student').innerText = studentName;
        document.getElementById('photo-modal-caption').innerText = caption;
        document.getElementById('photo-modal').classList.remove('hidden');
    }

    function closePhotoModal() {
        document.getElementById('photo-modal').classList.add('hidden');
        document.getElementById('photo-modal-img').src = "";
    }
</script>
@endpush
