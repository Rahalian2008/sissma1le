@extends('layouts.app')

@section('title', 'Presensi: ' . $attendance->title)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'presensi']) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Presensi {{ $courseClass->subject->name }}</span>
        </a>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold uppercase {{ $attendance->is_open ? 'bg-emerald-500/20 text-emerald-600 border border-emerald-500/30' : 'bg-slate-500/20 text-slate-500' }}">
                {{ $attendance->is_open ? 'Sesi Terbuka' : 'Sesi Ditutup' }}
            </span>
            <a href="{{ route('lms.attendances.print', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-xs" title="Cetak Rekap Presensi">
                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>🖨️ Cetak Rekap</span>
            </a>
            @if($canManage)
                <button type="button" onclick="document.getElementById('modal-edit-attendance').classList.remove('hidden')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-xs" title="Edit Sesi Presensi">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Sesi</span>
                </button>
                <form action="{{ route('lms.attendances.toggle', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-bold transition">
                        {{ $attendance->is_open ? 'Tutup Sesi Presensi' : 'Buka Kembali Sesi' }}
                    </button>
                </form>
                <form action="{{ route('lms.attendances.destroy', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi presensi {{ $attendance->title }}?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 rounded-lg text-xs font-bold transition" title="Hapus Sesi Presensi">
                        Hapus Sesi
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Attendance Header Card -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 uppercase">
                    {{ $courseClass->schoolClass->name }} • {{ $courseClass->subject->name }}
                </span>
                <span class="text-xs text-slate-500">📅 {{ Carbon\Carbon::parse($attendance->meeting_date)->translatedFormat('l, d F Y') }}</span>
                <span class="text-xs text-slate-500">• ⏰ {{ $attendance->start_time }} - {{ $attendance->end_time }} WIB</span>
                @if(($attendance->schedule_type ?? 'auto') === 'auto')
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 flex items-center gap-1" title="Jadwal presensi otomatis mengambil dari Tugaskan Guru ke Kelas Belajar">
                        <span>🤖</span>
                        <span>Jadwal Otomatis Kelas</span>
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30 flex items-center gap-1" title="Jadwal presensi diatur secara manual kustom">
                        <span>✍️</span>
                        <span>Jadwal Manual Kustom</span>
                    </span>
                @endif
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ $attendance->title }}</h1>
            <p class="text-xs text-slate-500">Metode Verifikasi: <strong class="uppercase">
                @if($attendance->attendance_method === 'both')
                    Selfie Siswa & Manual (Guru & Sekretaris Kelas)
                @elseif($attendance->attendance_method === 'selfie')
                    Hanya Selfie Siswa
                @elseif($attendance->attendance_method === 'manual')
                    Manual (Guru & Sekretaris Kelas)
                @else
                    {{ $attendance->attendance_method }}
                @endif
            </strong> • {{ $attendance->notes ?: 'Tatap muka / daring' }}</p>
        </div>

        @php
            $myLog = $student ? ($logsMap[$student->id] ?? null) : null;
        @endphp

        @if($student)
            <div class="shrink-0">
                @if($myLog)
                    <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-center">
                        <div class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400">✓ KEHADIRAN ANDA TERCATAT</div>
                        <div class="text-[11px] text-slate-600 dark:text-slate-300 mt-0.5">Status: <strong>{{ strtoupper($myLog->status) }}</strong> ({{ strtoupper($myLog->verified_method) }})</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $myLog->recorded_at ? $myLog->recorded_at->translatedFormat('H:i:s') . ' WIB' : '' }}</div>
                    </div>
                @else
                    @if($attendance->is_open)
                        <button type="button" onclick="openSelfieCameraModal()" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-sm rounded-2xl shadow-lg shadow-emerald-600/30 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                            <span>Presensi Selfie Hadir</span>
                        </button>
                    @else
                        <div class="p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-center text-xs font-bold text-rose-600">
                            Sesi presensi ini telah ditutup oleh guru pengampu.
                        </div>
                    @endif
                @endif
            </div>
        @endif
    </div>

    <!-- STUDENT SELFIE MODAL (Live Webcam or Photo Upload) -->
    @if($student && $attendance->is_open && ! $myLog)
        <div id="modal-selfie" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs hidden">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Ambil Foto Selfie Kehadiran</h3>
                    <button onclick="closeSelfieCameraModal()" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <!-- Video / Canvas Live Stream -->
                <div class="space-y-3">
                    <div class="relative w-full aspect-4/3 rounded-2xl overflow-hidden bg-slate-950 border border-slate-700 flex items-center justify-center">
                        <video id="webcam-video" autoplay playsinline class="w-full h-full object-cover"></video>
                        <canvas id="webcam-canvas" class="hidden"></canvas>
                        <div id="camera-loading" class="absolute inset-0 flex items-center justify-center text-xs text-slate-400 bg-slate-900">
                            Menghubungkan kamera webcam...
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-3">
                        <button type="button" id="snap-btn" onclick="captureSelfiePhoto()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                            <span>Ambil Jepretan</span>
                        </button>
                    </div>
                </div>

                <!-- Fallback File Upload & Submit Form -->
                <form id="selfie-form" action="{{ route('lms.attendances.selfie', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                    @csrf
                    <input type="hidden" name="selfie_image" id="selfie_base64">
                    <input type="hidden" name="device_info" value="{{ request()->userAgent() }}">

                    <div class="text-[11px] text-slate-500 text-center">
                        Atau pilih foto selfie dari galeri jika kamera tidak aktif:
                    </div>
                    <input type="file" name="selfie_file" accept="image/*" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-slate-900 dark:text-white">

                    <button type="submit" id="submit-selfie-btn" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Kirim Presensi Kehadiran
                    </button>
                </form>
            </div>
        </div>

        <script>
            let videoStream = null;

            function openSelfieCameraModal() {
                document.getElementById('modal-selfie').classList.remove('hidden');
                startWebcam();
            }

            function closeSelfieCameraModal() {
                document.getElementById('modal-selfie').classList.add('hidden');
                stopWebcam();
            }

            async function startWebcam() {
                const video = document.getElementById('webcam-video');
                const loading = document.getElementById('camera-loading');
                try {
                    videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                    video.srcObject = videoStream;
                    loading.classList.add('hidden');
                } catch (err) {
                    loading.innerText = "Kamera tidak diizinkan atau tidak tersedia. Silakan gunakan unggah foto.";
                }
            }

            function stopWebcam() {
                if (videoStream) {
                    videoStream.getTracks().forEach(track => track.stop());
                    videoStream = null;
                }
            }

            function captureSelfiePhoto() {
                const video = document.getElementById('webcam-video');
                const canvas = document.getElementById('webcam-canvas');
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 480;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                document.getElementById('selfie_base64').value = dataUrl;
                document.getElementById('snap-btn').innerText = "✓ Jepretan Berhasil Tersimpan";
                document.getElementById('snap-btn').classList.remove('bg-indigo-600');
                document.getElementById('snap-btn').classList.add('bg-emerald-600');
            }
        </script>
    @endif

    <!-- GURU, ADMIN & SEKRETARIS KELAS: REKAPITULASI PRESENSI KELAS & BATCH MANUAL CHECKLIST -->
    @if($canRecordManual)
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Daftar Kehadiran Siswa Kelas ({{ $students->count() }} Siswa)</h2>
                        @if($isSekretarisKelas)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                Akses Sekretaris Kelas
                            </span>
                        @elseif($canManage)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30">
                                Akses Guru Pengampu / Admin
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500">Pencatatan presensi manual (Hadir, Izin, Sakit, Alpa) oleh Guru & Sekretaris Kelas</p>
                </div>
                <button type="button" onclick="markAllPresent()" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                    Tandai Semua Hadir (H)
                </button>
            </div>

            <!-- Toolbar Aksi Massal (Multi-Pilih Siswa) -->
            <div id="bulk-action-bar" class="p-3.5 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-2xl flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span id="selected-count-badge" class="px-2.5 py-1 rounded-xl bg-indigo-600 text-white font-black text-xs shadow-xs">0 Siswa Dipilih</span>
                    <span class="text-xs text-slate-600 dark:text-slate-300 font-semibold hidden sm:inline">Pilih beberapa siswa lalu klik status kehadiran untuk mengubah sekaligus:</span>
                </div>
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" onclick="applyBulkStatusToSelected('hadir')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1">
                        <span>✓ Set Hadir</span>
                    </button>
                    <button type="button" onclick="applyBulkStatusToSelected('izin')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1">
                        <span>ℹ Set Izin</span>
                    </button>
                    <button type="button" onclick="applyBulkStatusToSelected('sakit')" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1">
                        <span>🏥 Set Sakit</span>
                    </button>
                    <button type="button" onclick="applyBulkStatusToSelected('alpa')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1">
                        <span>✕ Set Alpa</span>
                    </button>
                </div>
            </div>

            <!-- Manual Attendance Batch Form -->
            <form action="{{ route('lms.attendances.manual', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]) }}" method="POST">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 uppercase font-extrabold text-slate-500">
                            <tr>
                                <th class="p-3 w-10 text-center">
                                    <input type="checkbox" id="check-all-students" onchange="toggleSelectAllStudents(this)" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" title="Pilih Semua Siswa">
                                </th>
                                <th class="p-3">Nama Siswa</th>
                                <th class="p-3 text-center">Status Kehadiran (H / I / S / A)</th>
                                <th class="p-3 text-center">Foto Selfie</th>
                                <th class="p-3">Waktu & Verifikasi</th>
                                <th class="p-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($students as $st)
                                @php
                                    $log = $logsMap[$st->id] ?? null;
                                    $currentStatus = $log ? $log->status : 'alpa';
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 student-row" id="student-row-{{ $st->id }}">
                                    <td class="p-3 text-center">
                                        <input type="checkbox" value="{{ $st->id }}" onchange="updateSelectedCount()" class="student-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </td>
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">
                                        {{ $st->name }}
                                        <div class="text-[10px] text-slate-400 font-normal">{{ $st->nisn }}</div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="inline-flex items-center gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-slate-800" title="H: Hadir | I: Izin | S: Sakit | A: Alpa">
                                            <label class="px-2 py-1 rounded-lg text-xs font-bold cursor-pointer transition has-checked:bg-emerald-600 has-checked:text-white text-slate-700 dark:text-slate-300" title="Hadir">
                                                <input type="radio" name="statuses[{{ $st->id }}]" value="hadir" class="hidden status-radio-{{ $st->id }}" {{ $currentStatus === 'hadir' ? 'checked' : '' }}>
                                                H
                                            </label>
                                            <label class="px-2 py-1 rounded-lg text-xs font-bold cursor-pointer transition has-checked:bg-blue-600 has-checked:text-white text-slate-700 dark:text-slate-300" title="Izin">
                                                <input type="radio" name="statuses[{{ $st->id }}]" value="izin" class="hidden status-radio-{{ $st->id }}" {{ $currentStatus === 'izin' ? 'checked' : '' }}>
                                                I
                                            </label>
                                            <label class="px-2 py-1 rounded-lg text-xs font-bold cursor-pointer transition has-checked:bg-amber-600 has-checked:text-white text-slate-700 dark:text-slate-300" title="Sakit">
                                                <input type="radio" name="statuses[{{ $st->id }}]" value="sakit" class="hidden status-radio-{{ $st->id }}" {{ $currentStatus === 'sakit' ? 'checked' : '' }}>
                                                S
                                            </label>
                                            <label class="px-2 py-1 rounded-lg text-xs font-bold cursor-pointer transition has-checked:bg-rose-600 has-checked:text-white text-slate-700 dark:text-slate-300" title="Alpa">
                                                <input type="radio" name="statuses[{{ $st->id }}]" value="alpa" class="hidden status-radio-{{ $st->id }}" {{ $currentStatus === 'alpa' ? 'checked' : '' }}>
                                                A
                                            </label>
                                        </div>
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($log && $log->selfie_path)
                                            <a href="{{ asset('storage/' . $log->selfie_path) }}" target="_blank" class="inline-block w-8 h-8 rounded-lg overflow-hidden border border-slate-300 dark:border-slate-700 shadow-xs hover:scale-125 transition-transform">
                                                <img src="{{ asset('storage/' . $log->selfie_path) }}" alt="Selfie" class="w-full h-full object-cover">
                                            </a>
                                        @else
                                            <span class="text-[10px] text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400">
                                        @if($log)
                                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $log->recorded_at ? $log->recorded_at->format('H:i') . ' WIB' : '-' }}</span>
                                            <span class="text-[10px] text-slate-400 block uppercase">{{ $log->verified_method }}</span>
                                        @else
                                            <span class="text-[10px] text-slate-400">Belum Ada Presensi</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <input type="text" name="notes[{{ $st->id }}]" value="{{ $log?->notes }}" placeholder="Catatan kehadiran" class="w-full px-2.5 py-1 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white student-note-{{ $st->id }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Simpan Rekapitulasi Presensi Manual Kelas
                    </button>
                </div>
            </form>
        </div>

        <script>
            function markAllPresent() {
                const radios = document.querySelectorAll('input[type="radio"][value="hadir"]');
                radios.forEach(r => {
                    r.checked = true;
                    r.dispatchEvent(new Event('change'));
                });
            }

            function toggleSelectAllStudents(master) {
                const checkboxes = document.querySelectorAll('.student-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = master.checked;
                });
                updateSelectedCount();
            }

            function updateSelectedCount() {
                const checked = document.querySelectorAll('.student-checkbox:checked');
                const badge = document.getElementById('selected-count-badge');
                if (badge) {
                    badge.innerText = checked.length + ' Siswa Dipilih';
                }
            }

            function applyBulkStatusToSelected(status) {
                const checked = document.querySelectorAll('.student-checkbox:checked');
                if (checked.length === 0) {
                    alert('Silakan centang satu atau beberapa siswa terlebih dahulu.');
                    return;
                }

                checked.forEach(cb => {
                    const studentId = cb.value;
                    const radio = document.querySelector(`input[name="statuses[${studentId}]"][value="${status}"]`);
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                });
            }
        </script>
    @endif
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT SESI PRESENSI (DI HALAMAN SHOW)-->
<!-- ========================================== -->
@if($canManage)
<div id="modal-edit-attendance" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-lg w-full max-h-[92vh] overflow-y-auto shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Edit Sesi Presensi Pertemuan</h3>
                <p class="text-xs text-slate-500">Ubah judul, waktu, metode presensi, atau status sesi</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-edit-attendance').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form action="{{ route('lms.attendances.update', ['courseClass' => $courseClass->id, 'attendance' => $attendance->id]) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Sesi / Pertemuan Ke- *</label>
                <input type="text" name="title" value="{{ $attendance->title }}" required class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white font-bold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Sesi</label>
                    <input type="date" name="meeting_date" value="{{ $attendance->meeting_date ? Carbon\Carbon::parse($attendance->meeting_date)->format('Y-m-d') : '' }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-2 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Mulai</label>
                    <input type="time" name="start_time" value="{{ $attendance->start_time }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-2 text-slate-900 dark:text-white font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Selesai</label>
                    <input type="time" name="end_time" value="{{ $attendance->end_time }}" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-2.5 py-2 text-slate-900 dark:text-white font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Metode Presensi</label>
                    <select name="attendance_method" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="both" {{ $attendance->attendance_method === 'both' ? 'selected' : '' }}>Selfie Siswa & Manual Guru/Sekretaris</option>
                        <option value="manual" {{ $attendance->attendance_method === 'manual' ? 'selected' : '' }}>Hanya Manual Guru & Sekretaris</option>
                        <option value="selfie" {{ $attendance->attendance_method === 'selfie' ? 'selected' : '' }}>Hanya Selfie Siswa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Keterbukaan Sesi</label>
                    <select name="is_open" class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white">
                        <option value="1" {{ $attendance->is_open ? 'selected' : '' }}>Sesi Terbuka (Dapat Absen)</option>
                        <option value="0" {{ ! $attendance->is_open ? 'selected' : '' }}>Sesi Ditutup</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Pertemuan / Agenda Belajar</label>
                <textarea name="notes" rows="2" placeholder="Catatan pertemuan tatap muka atau topik pembelajaran..." class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-slate-900 dark:text-white">{{ $attendance->notes }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-edit-attendance').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition">Simpan Perubahan Sesi</button>
            </div>
        </form>
    </div>
</div>
@endif
    @endif
</div>
@endsection
