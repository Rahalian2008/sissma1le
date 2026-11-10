@extends('layouts.app')

@section('title', 'Edit Surat Perintah Perjalanan Dinas (SPPD)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.sppd.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Edit SPPD No. {{ $sppd->sppd_number }}</h1>
                <p class="text-xs text-slate-500">Perbarui rincian Surat Perintah Perjalanan Dinas resmi.</p>
            </div>
        </div>
    </div>

    <!-- SPPD Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <form action="{{ route('letters.sppd.update', $sppd) }}" method="POST" class="space-y-6 text-xs">
            @csrf
            @method('PUT')

            <!-- Section 1: Dasaran Surat Tugas & Pegawai -->
            <div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-4 bg-amber-500 rounded-sm"></span>
                    1. Pejabat Yang Ditugaskan & Dasar Surat Tugas
                </h2>
                
                <div class="space-y-4">
                    <!-- Link from Surat Tugas -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Rujukan Surat Tugas / Surat Keluar (Opsional)</label>
                        <select name="outgoing_letter_id" id="letter_select" onchange="applyLetterData()" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            <option value="">-- Tanpa Rujukan Surat Keluar --</option>
                            @foreach ($outgoingLetters as $ol)
                                <option value="{{ $ol->id }}" data-number="{{ $ol->letter_number }}" data-purpose="{{ $ol->subject }}" {{ old('outgoing_letter_id', $sppd->outgoing_letter_id) == $ol->id ? 'selected' : '' }}>
                                    {{ $ol->letter_number }} - {{ $ol->subject }} ({{ $ol->letter_date?->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Surat Tugas Sebagai Dasar <span class="text-rose-500">*</span></label>
                            <input type="text" name="letter_task_number" id="task_number_input" value="{{ old('letter_task_number', $sppd->letter_task_number) }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pilih Pegawai / Guru Yang Diperintahkan <span class="text-rose-500">*</span></label>
                            <select name="employee_id" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 font-medium">
                                <option value="">-- Pilih Pegawai / Guru --</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $sppd->employee_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->formatted_name }} (NIP. {{ $emp->nip ?? '-' }} &bull; {{ $emp->position ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Maksud & Rute Perjalanan -->
            <div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-4 bg-amber-500 rounded-sm"></span>
                    2. Maksud & Rute Perjalanan Dinas
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Maksud Perjalanan Dinas <span class="text-rose-500">*</span></label>
                        <textarea name="assignment_purpose" id="purpose_input" rows="2.5" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">{{ old('assignment_purpose', $sppd->assignment_purpose) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Alat Angkutan / Transportasi <span class="text-rose-500">*</span></label>
                            <input type="text" name="transportation_type" value="{{ old('transportation_type', $sppd->transportation_type) }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tempat Berangkat <span class="text-rose-500">*</span></label>
                            <input type="text" name="departure_location" value="{{ old('departure_location', $sppd->departure_location) }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tempat Tujuan <span class="text-rose-500">*</span></label>
                            <input type="text" name="destination_location" value="{{ old('destination_location', $sppd->destination_location) }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Waktu & Anggaran -->
            <div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-4 bg-amber-500 rounded-sm"></span>
                    3. Waktu & Pembebanan Anggaran
                </h2>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Lama Perjalanan (Hari) <span class="text-rose-500">*</span></label>
                            <input type="number" name="duration_days" id="duration_input" value="{{ old('duration_days', $sppd->duration_days) }}" min="1" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tanggal Berangkat <span class="text-rose-500">*</span></label>
                            <input type="date" name="departure_date" id="departure_date_input" value="{{ old('departure_date', $sppd->departure_date?->format('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tanggal Kembali / Tiba <span class="text-rose-500">*</span></label>
                            <input type="date" name="return_date" id="return_date_input" value="{{ old('return_date', $sppd->return_date?->format('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Sumber Pembebanan Anggaran <span class="text-rose-500">*</span></label>
                            <input type="text" name="budget_source" value="{{ old('budget_source', $sppd->budget_source) }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Mata Anggaran / Kode Rekening</label>
                            <input type="text" name="budget_account" value="{{ old('budget_account', $sppd->budget_account) }}" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Pengikut / Rombongan (Opsional)</label>
                        <input type="text" name="followers" value="{{ old('followers', $sppd->followers) }}" placeholder="Nama & Jabatan Pengikut..." class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Keterangan Khusus Lainnya</label>
                        <textarea name="special_notes" rows="2" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">{{ old('special_notes', $sppd->special_notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('letters.sppd.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-xl font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Simpan Perubahan SPPD
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function applyLetterData() {
        const select = document.getElementById('letter_select');
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const taskNum = selectedOption.getAttribute('data-number');
            const purpose = selectedOption.getAttribute('data-purpose');
            if (taskNum) document.getElementById('task_number_input').value = taskNum;
            if (purpose) document.getElementById('purpose_input').value = purpose;
        }
    }
</script>
@endsection
