@extends('layouts.app')

@section('title', 'Buat Surat Pengantar Alih Tangan Kasus (Referral)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Buat Surat Pengantar Alih Tangan Kasus</h1>
            <p class="text-xs text-slate-500">Rujukan resmi ke psikolog klinis, psikiater, rumah sakit, atau instansi pendukung.</p>
        </div>
        <a href="{{ route('bk.referral.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Batal / Kembali
        </a>
    </div>

    <form action="{{ route('bk.referral.store') }}" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-5 text-xs">
        @csrf

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Siswa yang Dirujuk <span class="text-rose-500">*</span></label>
                    <select name="student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="">-- Cari Siswa --</option>
                        @foreach ($students as $stu)
                            <option value="{{ $stu->id }}">{{ $stu->name }} ({{ $stu->schoolClass?->name ?? 'No Class' }} - NISN: {{ $stu->nisn ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Surat Rujukan <span class="text-rose-500">*</span></label>
                    <input type="date" name="referral_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Tenaga Ahli Tujuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="target_expert_name" placeholder="dr. / Psikolog / Spesialis..." required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lembaga / Rumah Sakit Tujuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="target_institution" placeholder="Contoh: Poli Jiwa / RSUD Sekarwangi" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Keahlian <span class="text-rose-500">*</span></label>
                    <select name="expert_category" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="PSIKOLOG_KLINIS">Psikolog Klinis</option>
                        <option value="PSIKIATER">Psikiater (Sp.KJ)</option>
                        <option value="DOKTER_SPESIALIS">Dokter / Fasilitas Medis</option>
                        <option value="DINAS_SOSIAL_PPA">Dinas Sosial / UPTD PPA</option>
                        <option value="KEPOLISIAN_HUKUM">Bapas / Lembaga Hukum</option>
                        <option value="LAINNYA">Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Persetujuan Kepala Sekolah <span class="text-rose-500">*</span></label>
                    <select name="headmaster_approval" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="DISETUJUI" selected>Disetujui (Siap Diberikan)</option>
                        <option value="MENUNGGU">Menunggu Verifikasi</option>
                        <option value="DITOLAK">Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Penanganan Rujukan <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="SEDANG_DIRUJUK" selected>Sedang Dirujuk (Proses Luar)</option>
                        <option value="SURAT_DIBUAT">Surat Dibuat</option>
                        <option value="KEMBALI_KE_SEKOLAH">Kembali Ke Sekolah (Dengan Catatan Ahli)</option>
                        <option value="SELESAI">Selesai (Kasus Teratasi)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Deskripsi & Gejala Masalah Siswa <span class="text-rose-500">*</span></label>
                <textarea name="problem_description" rows="3" required placeholder="Keluhan utama, gejala perilaku, atau gangguan emosi yang tampak..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Langkah Bimbingan yang Telah Dilakukan Sekolah <span class="text-rose-500">*</span></label>
                <textarea name="previous_interventions" rows="3" required placeholder="Riwayat konseling individual, home visit, pemanggilan orang tua, konferensi kasus..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Alasan Alih Tangan Kasus (Indikasi Rujukan) <span class="text-rose-500">*</span></label>
                <textarea name="referral_reason" rows="2" required placeholder="Contoh: Gejala depresi klinis / trauma yang memerlukan intervensi farmakologi atau psikoterapi intensif di luar kewenangan guru BK..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Bantuan / Penanganan yang Diharapkan dari Tenaga Ahli <span class="text-rose-500">*</span></label>
                <textarea name="expected_support" rows="2" required placeholder="Asesmen diagnosis psikologis/psikiatris, pendampingan medis, serta rekomendasi perlakuan belajar di sekolah..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
            <a href="{{ route('bk.referral.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                Terbitkan Surat Rujukan Kasus
            </button>
        </div>
    </form>
</div>
@endsection

