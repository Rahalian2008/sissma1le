@extends('layouts.app')

@section('title', 'Edit Alih Tangan Kasus - ' . $referral->referral_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit / Update Rujukan Alih Tangan Kasus</h1>
            <p class="text-xs text-slate-500">Nomor Registrasi: <span class="font-mono font-bold">{{ $referral->referral_code }}</span></p>
        </div>
        <a href="{{ route('bk.referral.show', $referral) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Batal / Kembali
        </a>
    </div>

    <form action="{{ route('bk.referral.update', $referral) }}" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-5 text-xs">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div class="p-3 bg-rose-50 rounded-xl">
                <span class="text-slate-500 font-bold">Siswa yang Dirujuk:</span>
                <span class="font-bold text-slate-900 ml-1">{{ $referral->student?->name }} ({{ $referral->student?->schoolClass?->name ?? '-' }})</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Surat Rujukan <span class="text-rose-500">*</span></label>
                    <input type="date" name="referral_date" value="{{ old('referral_date', $referral->referral_date?->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Tenaga Ahli <span class="text-rose-500">*</span></label>
                    <input type="text" name="target_expert_name" value="{{ old('target_expert_name', $referral->target_expert_name) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lembaga / Rumah Sakit <span class="text-rose-500">*</span></label>
                    <input type="text" name="target_institution" value="{{ old('target_institution', $referral->target_institution) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Keahlian <span class="text-rose-500">*</span></label>
                    <select name="expert_category" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="PSIKOLOG_KLINIS" {{ $referral->expert_category === 'PSIKOLOG_KLINIS' ? 'selected' : '' }}>Psikolog Klinis</option>
                        <option value="PSIKIATER" {{ $referral->expert_category === 'PSIKIATER' ? 'selected' : '' }}>Psikiater (Sp.KJ)</option>
                        <option value="DOKTER_SPESIALIS" {{ $referral->expert_category === 'DOKTER_SPESIALIS' ? 'selected' : '' }}>Dokter / Fasilitas Medis</option>
                        <option value="DINAS_SOSIAL_PPA" {{ $referral->expert_category === 'DINAS_SOSIAL_PPA' ? 'selected' : '' }}>Dinas Sosial / UPTD PPA</option>
                        <option value="KEPOLISIAN_HUKUM" {{ $referral->expert_category === 'KEPOLISIAN_HUKUM' ? 'selected' : '' }}>Bapas / Lembaga Hukum</option>
                        <option value="LAINNYA" {{ $referral->expert_category === 'LAINNYA' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Persetujuan Kepala Sekolah <span class="text-rose-500">*</span></label>
                    <select name="headmaster_approval" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="DISETUJUI" {{ $referral->headmaster_approval === 'DISETUJUI' ? 'selected' : '' }}>Disetujui</option>
                        <option value="MENUNGGU" {{ $referral->headmaster_approval === 'MENUNGGU' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="DITOLAK" {{ $referral->headmaster_approval === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Penanganan <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="SEDANG_DIRUJUK" {{ $referral->status === 'SEDANG_DIRUJUK' ? 'selected' : '' }}>Sedang Dirujuk</option>
                        <option value="SURAT_DIBUAT" {{ $referral->status === 'SURAT_DIBUAT' ? 'selected' : '' }}>Surat Dibuat</option>
                        <option value="KEMBALI_KE_SEKOLAH" {{ $referral->status === 'KEMBALI_KE_SEKOLAH' ? 'selected' : '' }}>Kembali Ke Sekolah (Dengan Catatan)</option>
                        <option value="SELESAI" {{ $referral->status === 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Deskripsi Permasalahan Siswa <span class="text-rose-500">*</span></label>
                <textarea name="problem_description" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('problem_description', $referral->problem_description) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Langkah Bimbingan Sekolah Sebelumnya <span class="text-rose-500">*</span></label>
                <textarea name="previous_interventions" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('previous_interventions', $referral->previous_interventions) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Alasan Alih Tangan Kasus <span class="text-rose-500">*</span></label>
                <textarea name="referral_reason" rows="2" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('referral_reason', $referral->referral_reason) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Bantuan yang Diharapkan dari Tenaga Ahli <span class="text-rose-500">*</span></label>
                <textarea name="expected_support" rows="2" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('expected_support', $referral->expected_support) }}</textarea>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <label class="block font-bold text-emerald-800 mb-1">Hasil & Catatan Balik (Feedback) dari Tenaga Ahli</label>
                <textarea name="expert_feedback_notes" rows="3" placeholder="Input hasil diagnosa psikologis, resep terapi, atau anjuran perlakuan belajar siswa di sekolah..." class="w-full px-3 py-2 border border-emerald-300 bg-emerald-50/30 rounded-xl">{{ old('expert_feedback_notes', $referral->expert_feedback_notes) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <button type="button" onclick="if(confirm('Hapus berkas rujukan ini?')) document.getElementById('delete-form').submit();" class="text-rose-600 hover:text-rose-700 font-bold">
                Hapus Berkas
            </button>
            <div class="flex items-center gap-2">
                <a href="{{ route('bk.referral.show', $referral) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Perbarui Rujukan
                </button>
            </div>
        </div>
    </form>

    <form id="delete-form" action="{{ route('bk.referral.destroy', $referral) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

