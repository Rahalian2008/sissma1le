@extends('layouts.app')

@section('title', 'Pemantauan Disposisi Surat')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Pemantauan Disposisi Surat</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pelacakan instruksi pimpinan sekolah, tenggat waktu penugasan, dan status penyelesaian tindak lanjut.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('dispositions.index') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                <option value="">Semua Status Disposisi</option>
                <option value="Belum Diproses" {{ request('status') === 'Belum Diproses' ? 'selected' : '' }}>Belum Diproses</option>
                <option value="Sedang Dikerjakan" {{ request('status') === 'Sedang Dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </form>
    </div>

    <!-- Dispositions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Surat Masuk Terkait</th>
                        <th class="px-5 py-3.5">Penerima Disposisi</th>
                        <th class="px-5 py-3.5">Instruksi Pimpinan</th>
                        <th class="px-5 py-3.5">Tenggat Waktu</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($dispositions as $disp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 text-slate-600 font-mono">
                                {{ $disp->disposition_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <a href="{{ route('letters.incoming.show', $disp->incomingLetter) }}" class="font-bold text-slate-900 hover:text-blue-600 line-clamp-1">
                                    {{ $disp->incomingLetter->subject }}
                                </a>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $disp->incomingLetter->letter_number }} &bull; Dari: {{ $disp->incomingLetter->sender }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">
                                {{ $disp->target_role_or_user }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 max-w-sm">
                                <span class="line-clamp-2">{{ $disp->instructions }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($disp->due_date)
                                    <span class="font-semibold {{ $disp->due_date->isPast() && $disp->status !== 'Selesai' ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                        {{ $disp->due_date->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($disp->status === 'Selesai') bg-emerald-100 text-emerald-800
                                    @elseif($disp->status === 'Sedang Dikerjakan') bg-blue-100 text-blue-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $disp->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('dispositions.print', $disp) }}" target="_blank" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition inline-flex items-center text-xs" title="Cetak Lembar Disposisi">
                                    Cetak
                                </a>
                                <button type="button" 
                                        onclick="openEditDisposition({{ json_encode([
                                            'id' => $disp->id,
                                            'target_role_or_user' => $disp->target_role_or_user,
                                            'disposition_date' => $disp->disposition_date?->format('Y-m-d'),
                                            'due_date' => $disp->due_date?->format('Y-m-d'),
                                            'instructions' => $disp->instructions,
                                            'status' => $disp->status,
                                            'letter_subject' => $disp->incomingLetter?->subject,
                                            'letter_number' => $disp->incomingLetter?->letter_number,
                                        ]) }})" 
                                        class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer" 
                                        title="Edit Disposisi">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('dispositions.destroy', $disp) }}" method="POST" onsubmit="return confirm('Hapus lembar disposisi ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Disposisi">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada riwayat lembar disposisi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($dispositions->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $dispositions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Edit Disposisi -->
<div id="modal-edit-disposition" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Lembar Disposisi</h2>
        <p id="edit-disp-subtitle" class="text-xs text-slate-500 mb-4"></p>

        <form id="form-edit-disposition" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Diteruskan Kepada <span class="text-rose-500">*</span></label>
                <select id="edit-disp-target" name="target_role_or_user" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="Kepala Urusan Tata Usaha">Kepala Urusan Tata Usaha</option>
                    <option value="Wakil Kepala Sekolah Bidang Kurikulum">Wakil Kepala Sekolah Bidang Kurikulum</option>
                    <option value="Wakil Kepala Sekolah Bidang Kesiswaan">Wakil Kepala Sekolah Bidang Kesiswaan</option>
                    <option value="Wakil Kepala Sekolah Bidang Sarpras">Wakil Kepala Sekolah Bidang Sarpras</option>
                    <option value="Bendahara Sekolah">Bendahara Sekolah</option>
                    <option value="Koordinator Bimbingan Konseling (BK)">Koordinator Bimbingan Konseling (BK)</option>
                    <option value="Staf Administrasi Kepegawaian">Staf Administrasi Kepegawaian</option>
                    <option value="Petugas Arsip">Petugas Arsip</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Disposisi <span class="text-rose-500">*</span></label>
                    <input type="date" id="edit-disp-date" name="disposition_date" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tenggat Waktu Selesai</label>
                    <input type="date" id="edit-disp-due-date" name="due_date" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Petunjuk / Instruksi Pimpinan <span class="text-rose-500">*</span></label>
                <textarea id="edit-disp-instructions" name="instructions" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Status Disposisi <span class="text-rose-500">*</span></label>
                <select id="edit-disp-status" name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="Belum Diproses">Belum Diproses</option>
                    <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-disposition').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold cursor-pointer">Perbarui Disposisi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditDisposition(data) {
        document.getElementById('form-edit-disposition').action = `/dispositions/${data.id}`;
        document.getElementById('edit-disp-subtitle').textContent = `Surat: ${data.letter_number} - ${data.letter_subject}`;
        document.getElementById('edit-disp-target').value = data.target_role_or_user;
        document.getElementById('edit-disp-date').value = data.disposition_date;
        document.getElementById('edit-disp-due-date').value = data.due_date || '';
        document.getElementById('edit-disp-instructions').value = data.instructions;
        document.getElementById('edit-disp-status').value = data.status;
        document.getElementById('modal-edit-disposition').classList.remove('hidden');
    }
</script>
@endsection
