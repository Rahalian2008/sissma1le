@extends('layouts.app')

@section('title', 'Penerima Beasiswa & Bantuan Pendidikan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Beasiswa & Bantuan Siswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pengelolaan program Program Indonesia Pintar (PIP), Beasiswa Prestasi, dan afirmasi daerah.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-import-modal type="scholarships" title="Beasiswa & PIP Siswa" />
            <button onclick="document.getElementById('modal-scholarship').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Beasiswa
            </button>
        </div>
    </div>

    <!-- Scholarships Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nama Siswa</th>
                        <th class="px-5 py-3.5">Program Beasiswa</th>
                        <th class="px-5 py-3.5">Sumber Dana</th>
                        <th class="px-5 py-3.5 text-right">Nominal Bantuan</th>
                        <th class="px-5 py-3.5">Periode / Semester</th>
                        <th class="px-5 py-3.5">Status Penyaluran</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($scholarships as $sch)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $sch->student?->name }}
                                <div class="text-[11px] text-slate-400 font-mono font-normal">NIS: {{ $sch->student?->nis }} &bull; Kelas {{ $sch->student?->current_class_level }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">
                                {{ $sch->scholarship_name }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $sch->source ?? 'APBN / Kemendikbud' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-emerald-700">
                                Rp {{ number_format($sch->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $sch->period }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($sch->status === 'Tersalurkan') bg-emerald-100 text-emerald-800
                                    @elseif($sch->status === 'Disetujui') bg-blue-100 text-blue-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $sch->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                <button type="button" onclick="openEditScholarship({{ json_encode([
                                    'id' => $sch->id,
                                    'student_id' => $sch->student_id,
                                    'scholarship_name' => $sch->scholarship_name,
                                    'source' => $sch->source,
                                    'amount' => $sch->amount,
                                    'period' => $sch->period,
                                    'status' => $sch->status,
                                ]) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer" title="Edit Beasiswa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('scholarships.destroy', $sch) }}" method="POST" onsubmit="return confirm('Hapus catatan beasiswa ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus Beasiswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada data beasiswa tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($scholarships->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $scholarships->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Beasiswa -->
<div id="modal-scholarship" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Catat Penerima Beasiswa</h2>
        <p class="text-xs text-slate-500 mb-4">Input penerima beasiswa dan bantuan pendidikan peserta didik.</p>

        <form action="{{ route('students.scholarships.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa <span class="text-rose-500">*</span></label>
                <select name="student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Pilih Peserta Didik --</option>
                    @foreach ($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} (NIS: {{ $s->nis }} - Kelas {{ $s->current_class_level }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Program Beasiswa <span class="text-rose-500">*</span></label>
                <input type="text" name="scholarship_name" placeholder="Contoh: Program Indonesia Pintar (PIP) Tahap 1" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sumber Dana</label>
                    <input type="text" name="source" placeholder="Kemendikbudristek / Pemda" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" value="1800000" min="0" step="10000" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Periode / Tahun <span class="text-rose-500">*</span></label>
                    <input type="text" name="period" value="{{ date('Y') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Penyaluran <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Tersalurkan">Tersalurkan</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-scholarship').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold cursor-pointer">Simpan Beasiswa</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Beasiswa -->
<div id="modal-edit-scholarship" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Catatan Beasiswa</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui data penerima beasiswa atau bantuan pendidikan.</p>

        <form id="form-edit-scholarship" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa <span class="text-rose-500">*</span></label>
                <select name="student_id" id="edit-sch-student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Pilih Peserta Didik --</option>
                    @foreach ($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} (NIS: {{ $s->nis }} - Kelas {{ $s->current_class_level }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Program Beasiswa <span class="text-rose-500">*</span></label>
                <input type="text" name="scholarship_name" id="edit-sch-name" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sumber Dana</label>
                    <input type="text" name="source" id="edit-sch-source" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" id="edit-sch-amount" min="0" step="10000" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Periode / Tahun <span class="text-rose-500">*</span></label>
                    <input type="text" name="period" id="edit-sch-period" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Penyaluran <span class="text-rose-500">*</span></label>
                    <select name="status" id="edit-sch-status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Tersalurkan">Tersalurkan</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-scholarship').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditScholarship(data) {
        document.getElementById('form-edit-scholarship').action = '/scholarships/' + data.id;
        document.getElementById('edit-sch-student_id').value = data.student_id;
        document.getElementById('edit-sch-name').value = data.scholarship_name;
        document.getElementById('edit-sch-source').value = data.source || '';
        document.getElementById('edit-sch-amount').value = data.amount;
        document.getElementById('edit-sch-period').value = data.period;
        document.getElementById('edit-sch-status').value = data.status;
        document.getElementById('modal-edit-scholarship').classList.remove('hidden');
    }
</script>
@endsection
