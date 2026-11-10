@extends('layouts.app')

@section('title', 'Edit Ajuan Pulang Cepat Siswa - ' . $earlyLeave->letter_number)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('attendance.early-leave.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Ajuan Pulang Cepat
        </a>
        <span class="font-mono text-xs font-bold px-2.5 py-1 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 rounded-lg border border-amber-200 dark:border-amber-800">
            {{ $earlyLeave->letter_number }}
        </span>
    </div>

    <!-- Main Edit Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-xl space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center font-bold text-2xl shadow-sm">
                ✏️
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Edit Data Ajuan Pulang Cepat</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui rincian kepulangan, penjemput, kategori alasan, atau status verifikasi resmi.</p>
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

        <form action="{{ route('attendance.early-leave.update', $earlyLeave->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5 text-xs">
            @csrf
            @method('PUT')

            <!-- Siswa -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Siswa Bersangkutan <span class="text-rose-500">*</span></label>
                <select name="student_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">
                    @foreach($allStudents as $st)
                        <option value="{{ $st->id }}" {{ old('student_id', $earlyLeave->student_id) == $st->id ? 'selected' : '' }}>
                            {{ $st->name }} (NIS: {{ $st->nis }}) - {{ $st->schoolClass?->name ?? 'Tanpa Kelas' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal & Jam Pulang -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Izin Pulang <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', $earlyLeave->date ? \Carbon\Carbon::parse($earlyLeave->date)->format('Y-m-d') : date('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Kepulangan (WIB) <span class="text-rose-500">*</span></label>
                    <input type="time" name="leave_time" value="{{ old('leave_time', $earlyLeave->leave_time ? substr($earlyLeave->leave_time, 0, 5) : date('H:i')) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <!-- Kategori & Keterangan Alasan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori Alasan <span class="text-rose-500">*</span></label>
                    <select name="reason_category" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-amber-500">
                        @foreach(['Sakit di Sekolah (Rujukan UKS)', 'Keperluan Keluarga Mendesak', 'Tugas / Dispensasi Sekolah', 'Kematian / Takziyah Keluarga', 'Lainnya'] as $cat)
                            <option value="{{ $cat }}" {{ old('reason_category', $earlyLeave->reason_category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Rincian Keterangan Alasan <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="2" required placeholder="Jelaskan detail alasan kepulangan siswa..."
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">{{ old('reason', $earlyLeave->reason) }}</textarea>
                </div>
            </div>

            <!-- Data Penjemput -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                <span class="font-black text-slate-800 dark:text-slate-200 block">Informasi Penjemput (Orang Tua / Wali Siswa)</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Penjemput</label>
                        <input type="text" name="pickup_name" value="{{ old('pickup_name', $earlyLeave->pickup_name) }}" placeholder="Contoh: Ayah / Ibu Siswa"
                            class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Hubungan Keluarga</label>
                        <input type="text" name="pickup_relationship" value="{{ old('pickup_relationship', $earlyLeave->pickup_relationship) }}" placeholder="Orang Tua, Kakak, dll."
                            class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">No. Kontak / WA</label>
                        <input type="text" name="pickup_phone" value="{{ old('pickup_phone', $earlyLeave->pickup_phone) }}" placeholder="08xxxxxxxxxx"
                            class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            <!-- Status Verifikasi (Khusus Petugas / Staf) -->
            @if(auth()->user()->canManageSchoolPermits())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-amber-50/50 dark:bg-amber-950/30 rounded-2xl border border-amber-200 dark:border-amber-800/60">
                    <div>
                        <label class="block font-bold text-amber-900 dark:text-amber-200 mb-1">Status Verifikasi Resmi</label>
                        <select name="status" class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-amber-500">
                            <option value="PENDING" {{ old('status', $earlyLeave->status) === 'PENDING' ? 'selected' : '' }}>⏳ Menunggu Verifikasi (PENDING)</option>
                            <option value="APPROVED" {{ old('status', $earlyLeave->status) === 'APPROVED' ? 'selected' : '' }}>✅ Disetujui & Diterbitkan (APPROVED)</option>
                            <option value="REJECTED" {{ old('status', $earlyLeave->status) === 'REJECTED' ? 'selected' : '' }}>❌ Ditolak (REJECTED)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-amber-900 dark:text-amber-200 mb-1">Catatan Verifikator</label>
                        <input type="text" name="approval_notes" value="{{ old('approval_notes', $earlyLeave->approval_notes) }}" placeholder="Catatan persetujuan / alasan penolakan..."
                            class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            @endif

            <!-- Lampiran / Bukti Baru -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Dokumen Bukti Pendukung (Opsional)</label>
                @if($earlyLeave->attachment_path)
                    <div class="mb-2 text-[11px] text-slate-500 flex items-center gap-2">
                        <span>File saat ini:</span>
                        <a href="{{ asset('storage/' . $earlyLeave->attachment_path) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 font-semibold underline">
                            {{ basename($earlyLeave->attachment_path) }}
                        </a>
                    </div>
                @endif
                <input type="file" name="attachment" accept="image/*,.pdf"
                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-slate-800 dark:file:text-amber-300">
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('attendance.early-leave.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold shadow-md shadow-amber-500/20 active:scale-95 transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

