@extends('layouts.app')

@section('title', 'Penerbitan Surat Perintah Perjalanan Dinas (SPPD)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('letters.sppd.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Penerbitan SPPD Baru</h1>
                <p class="text-xs text-slate-500">Formulir rincian Surat Perintah Perjalanan Dinas berbasis Surat Tugas resmi.</p>
            </div>
        </div>
    </div>

    <!-- SPPD Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <form action="{{ route('letters.sppd.store') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: Dasaran Surat Tugas & Pegawai -->
            <div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-4 bg-blue-600 rounded-sm"></span>
                    1. Pejabat Yang Ditugaskan & Dasar Surat Tugas
                </h2>
                
                <div class="space-y-4">
                    <!-- Link from Surat Tugas -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Rujukan Surat Tugas / Surat Keluar (Opsional)</label>
                        <select name="outgoing_letter_id" id="letter_select" onchange="applyLetterData()" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Buat Mandiri / Tanpa Rujukan Surat Keluar --</option>
                            @foreach ($outgoingLetters as $ol)
                                <option value="{{ $ol->id }}" data-number="{{ $ol->letter_number }}" data-purpose="{{ $ol->subject }}" {{ $prefilledLetterId == $ol->id ? 'selected' : '' }}>
                                    {{ $ol->letter_number }} - {{ $ol->subject }} ({{ $ol->letter_date?->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Memilih surat tugas akan otomatis mengisikan nomor dasar surat dan maksud perjalanan.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nomor Surat Tugas Sebagai Dasar <span class="text-rose-500">*</span></label>
                            <input type="text" name="letter_task_number" id="task_number_input" value="{{ old('letter_task_number', $prefilledTaskNumber ?? '421.5/ST-012/SMA1LE/'.date('Y')) }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pilih Pegawai / Guru Yang Diperintahkan <span class="text-rose-500">*</span></label>
                            <select name="employee_id" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-medium">
                                <option value="">-- Pilih Pegawai / Guru --</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $prefilledEmployeeId) == $emp->id ? 'selected' : '' }}>
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
                    <span class="w-2 h-4 bg-blue-600 rounded-sm"></span>
                    2. Maksud & Rute Perjalanan Dinas
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Maksud Perjalanan Dinas <span class="text-rose-500">*</span></label>
                        <textarea name="assignment_purpose" id="purpose_input" rows="2.5" required placeholder="Contoh: Mengikuti Bimbingan Teknis Implementasi Kurikulum Merdeka Jenjang SMA Tingkat Provinsi..." class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('assignment_purpose', $prefilledPurpose ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Alat Angkutan Yang Digunakan <span class="text-rose-500">*</span></label>
                            <select name="transportation_type" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <option value="Kendaraan Dinas">Kendaraan Dinas</option>
                                <option value="Kendaraan Umum / Darat" selected>Kendaraan Umum / Darat</option>
                                <option value="Kereta Api">Kereta Api</option>
                                <option value="Pesawat Terbang">Pesawat Terbang</option>
                                <option value="Kendaraan Pribadi">Kendaraan Pribadi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tempat Berangkat <span class="text-rose-500">*</span></label>
                            <input type="text" name="departure_location" value="{{ old('departure_location', $school->city ?? 'Bandung') }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tempat Tujuan <span class="text-rose-500">*</span></label>
                            <input type="text" name="destination_location" value="{{ old('destination_location', 'Dinas Pendidikan Provinsi') }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Lamanya Perjalanan (Hari) <span class="text-rose-500">*</span></label>
                            <input type="number" name="duration_days" id="duration_input" min="1" value="{{ old('duration_days', 1) }}" required oninput="calcReturnDate()" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tanggal Berangkat <span class="text-rose-500">*</span></label>
                            <input type="date" name="departure_date" id="dep_date" value="{{ old('departure_date', date('Y-m-d')) }}" required onchange="calcReturnDate()" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tanggal Harus Kembali <span class="text-rose-500">*</span></label>
                            <input type="date" name="return_date" id="ret_date" value="{{ old('return_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Pembebanan Anggaran & Pengikut -->
            <div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4 flex items-center gap-2">
                    <span class="w-2 h-4 bg-blue-600 rounded-sm"></span>
                    3. Pembebanan Anggaran & Keterangan Tambahan
                </h2>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Instansi / Sumber Pembebanan Anggaran <span class="text-rose-500">*</span></label>
                            <select name="budget_source" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <option value="BOSP Reguler" selected>BOSP Reguler Tahun {{ date('Y') }}</option>
                                <option value="BOSP Kinerja">BOSP Kinerja</option>
                                <option value="BOSDA Provinsi">BOSDA Provinsi</option>
                                <option value="Komite Sekolah">Komite Sekolah</option>
                                <option value="DIPA Satker / APBD">DIPA Satker / APBD</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Mata Anggaran / Kode Rekening Belanja</label>
                            <input type="text" name="budget_account" value="{{ old('budget_account', '5.1.02.04.01.0001 (Belanja Perjalanan Dinas Biasa)') }}" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pengikut / Rombongan (Jika Ada)</label>
                            <textarea name="followers" rows="2" placeholder="Nama pengikut dan NIP jika ada..." class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">{{ old('followers') }}</textarea>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Keterangan Lain-Lain</label>
                            <textarea name="notes" rows="2" placeholder="Keterangan penginapan, transportasi, atau dispensasi..." class="w-full px-3.5 py-2 border border-slate-300 rounded-xl">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('letters.sppd.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Terbitkan SPPD & Siapkan Cetak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function applyLetterData() {
        const select = document.getElementById('letter_select');
        const selected = select.options[select.selectedIndex];
        if (selected && selected.dataset.number) {
            document.getElementById('task_number_input').value = selected.dataset.number;
            if (selected.dataset.purpose && !document.getElementById('purpose_input').value) {
                document.getElementById('purpose_input').value = selected.dataset.purpose;
            }
        }
    }

    function calcReturnDate() {
        const dep = document.getElementById('dep_date').value;
        const dur = parseInt(document.getElementById('duration_input').value) || 1;
        if (dep) {
            const d = new Date(dep);
            d.setDate(d.getDate() + (dur - 1));
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            document.getElementById('ret_date').value = `${yyyy}-${mm}-${dd}`;
        }
    }
</script>
@endsection
