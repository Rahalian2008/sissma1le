@extends('layouts.app')

@section('title', 'Edit Surat Izin Keluar Sekolah - ' . $permit->permit_number)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('attendance.school-pass.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Izin Keluar Sekolah
        </a>
        <span class="font-mono text-xs font-bold px-2.5 py-1 bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 rounded-lg border border-teal-200 dark:border-teal-800">
            {{ $permit->permit_number }}
        </span>
    </div>

    <!-- Main Edit Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-xl space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center font-bold text-2xl shadow-sm">
                🚶
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Edit Data Izin Keluar Lingkungan Sekolah</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui rincian siswa, jam keluar/kembali, tujuan keperluan, atau status verifikasi resmi.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 rounded-2xl text-xs text-rose-700 dark:text-rose-300 space-y-1">
                <div class="font-bold">Terdapat beberapa kesalahan:</div>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('attendance.school-pass.update', $permit->id) }}" method="POST" class="space-y-5 text-xs">
            @csrf
            @method('PUT')

            <!-- Pilih Siswa (Bisa Multiple Siswa) -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Siswa yang Diberi Izin <span class="text-rose-500">* (Gunakan Ctrl/Cmd + Klik untuk memilih lebih dari 1 siswa)</span></label>
                @php
                    $selectedStudentIds = old('students', $permit->students->pluck('id')->toArray());
                @endphp
                <select name="students[]" multiple required size="5" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-teal-500">
                    @foreach($allStudents as $st)
                        <option value="{{ $st->id }}" {{ in_array($st->id, $selectedStudentIds) ? 'selected' : '' }}>
                            {{ $st->name }} (NIS: {{ $st->nis }}) - {{ $st->schoolClass?->name ?? 'Tanpa Kelas' }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Siswa terpilih: <span class="font-bold text-teal-600 dark:text-teal-400">{{ count($selectedStudentIds) }} siswa</span></p>
            </div>

            <!-- Tanggal, Jam Keluar & Jam Kembali -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Izin <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', $permit->date ? \Carbon\Carbon::parse($permit->date)->format('Y-m-d') : date('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Keluar Sekolah <span class="text-rose-500">*</span></label>
                    <input type="time" name="out_time" value="{{ old('out_time', $permit->out_time ? substr($permit->out_time, 0, 5) : date('H:i')) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Estimasi Jam Kembali (WIB)</label>
                    <input type="time" name="expected_back_time" value="{{ old('expected_back_time', $permit->expected_back_time ? substr($permit->expected_back_time, 0, 5) : '') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <!-- Keperluan & Lokasi Tujuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Keperluan / Urusan <span class="text-rose-500">*</span></label>
                    <input type="text" name="purpose" value="{{ old('purpose', $permit->purpose) }}" required placeholder="Contoh: Cetak Tugas / Lomba / Keperluan Dinas"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Lokasi / Tempat Tujuan</label>
                    <input type="text" name="destination" value="{{ old('destination', $permit->destination) }}" placeholder="Contoh: Percetakan Lengkong / Kantor Camat"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <!-- Alasan & Pendamping -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Rincian Keterangan <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="2" required placeholder="Jelaskan secara jelas keperluan siswa di luar sekolah..."
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-teal-500">{{ old('reason', $permit->reason) }}</textarea>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Didampingi Oleh</label>
                    <input type="text" name="accompanied_by" value="{{ old('accompanied_by', $permit->accompanied_by) }}" placeholder="Guru Pembina / Mandiri"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <!-- Ketentuan Kembali & Status Kembalinya -->
            <div class="p-4 bg-teal-50/60 dark:bg-teal-950/30 rounded-2xl border border-teal-200 dark:border-teal-800/60 space-y-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_returning" id="is_returning" value="1" {{ old('is_returning', $permit->is_returning) ? 'checked' : '' }}
                        class="rounded text-teal-600 focus:ring-teal-500 w-4 h-4">
                    <label for="is_returning" class="font-bold text-teal-900 dark:text-teal-200">
                        Wajib Kembali ke Sekolah pada Hari yang Sama (Lapor ke Pos Satpam/Piket)
                    </label>
                </div>

                @if(auth()->user()->canManageSchoolPermits())
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-teal-200/80 dark:border-teal-800/80">
                        <div>
                            <label class="block font-bold text-teal-900 dark:text-teal-200 mb-1">Status Izin</label>
                            <select name="status" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-teal-300 dark:border-teal-700 text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-teal-500">
                                <option value="PENDING" {{ old('status', $permit->status) === 'PENDING' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
                                <option value="APPROVED" {{ old('status', $permit->status) === 'APPROVED' ? 'selected' : '' }}>✅ Disetujui (Sedang Keluar)</option>
                                <option value="RETURNED" {{ old('status', $permit->status) === 'RETURNED' ? 'selected' : '' }}>🔙 Sudah Kembali Masuk</option>
                                <option value="REJECTED" {{ old('status', $permit->status) === 'REJECTED' ? 'selected' : '' }}>❌ Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-teal-900 dark:text-teal-200 mb-1">Jam Real Masuk Kembali</label>
                            <input type="time" name="actual_back_time" value="{{ old('actual_back_time', $permit->actual_back_time ? substr($permit->actual_back_time, 0, 5) : '') }}"
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-teal-300 dark:border-teal-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block font-bold text-teal-900 dark:text-teal-200 mb-1">Catatan Verifikator</label>
                            <input type="text" name="approval_notes" value="{{ old('approval_notes', $permit->approval_notes) }}" placeholder="Catatan persetujuan..."
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-teal-300 dark:border-teal-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                @endif
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('attendance.school-pass.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-extrabold shadow-md shadow-teal-600/20 active:scale-95 transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

