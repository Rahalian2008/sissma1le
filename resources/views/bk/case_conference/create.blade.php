@extends('layouts.app')

@section('title', 'Buat Berita Acara Konferensi Kasus')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Catat Sidang Konferensi Kasus Siswa</h1>
            <p class="text-xs text-slate-500">Forum resmi multi-pihak penanganan masalah siswa secara komprehensif.</p>
        </div>
        <a href="{{ route('bk.case-conference.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Batal / Kembali
        </a>
    </div>

    <form action="{{ route('bk.case-conference.store') }}" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-5 text-xs">
        @csrf

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Siswa yang Dibahas <span class="text-rose-500">*</span></label>
                    <select name="student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="">-- Cari Siswa --</option>
                        @foreach ($students as $stu)
                            <option value="{{ $stu->id }}">{{ $stu->name }} ({{ $stu->schoolClass?->name ?? 'No Class' }} - NISN: {{ $stu->nisn ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Judul / Topik Kasus <span class="text-rose-500">*</span></label>
                    <input type="text" name="case_title" placeholder="Contoh: Penanganan Tingkat Ketidakhadiran Tinggi dan Penurunan Drastis Nilai Belajar" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Sidang <span class="text-rose-500">*</span></label>
                    <input type="date" name="conference_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Waktu Pelaksanaan</label>
                    <input type="text" name="conference_time" value="09.00 - 11.30 WIB" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tempat Sidang</label>
                    <input type="text" name="place" value="Ruang Rapat BK SMAN 1 Lengkong" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pimpinan Sidang / Konferensi <span class="text-rose-500">*</span></label>
                    <input type="text" name="leader_name" value="Kepala Sekolah / Koordinator Guru BK" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Sidang Kasus <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="TERLAKSANA" selected>Terlaksana (Berita Acara Dibuat)</option>
                        <option value="TERJADWAL">Terjadwal (Akan Datang)</option>
                        <option value="DITINDAKLANJUTI">Sedang Ditindaklanjuti</option>
                        <option value="SELESAI">Selesai (Kasus Tuntas)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Daftar Pihak yang Hadir (Peserta Musyawarah)</label>
                <p class="text-[11px] text-slate-400 mb-1.5">Format per baris: <code>Nama | Jabatan / Peran | Catatan</code></p>
                <textarea name="attendees_input" rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-[11px]">Drs. H. Pimpinan, M.Pd. | Kepala Sekolah | Pimpinan Sidang
Ahmad Konselor, S.Pd., Kons. | Guru Bimbingan Konseling | Notulis & Penyaji Kasus
Siti Waliyah, S.Pd. | Wali Kelas Siswa | Peserta
Bapak / Ibu Orang Tua Siswa | Orang Tua / Wali Murid | Peserta</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Latar Belakang Masalah (Deskripsi Kasus) <span class="text-rose-500">*</span></label>
                <textarea name="case_background" rows="3" required placeholder="Kronologi kemunculan masalah, laporan awal, dan riwayat bimbingan sebelumnya..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Fakta & Temuan Data (Hasil Penggalian Multi-Pihak) <span class="text-rose-500">*</span></label>
                <textarea name="facts_and_findings" rows="3" required placeholder="Data kehadiran, hasil home visit, klarifikasi orang tua, pengamatan guru mapel..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Dinamika Diskusi & Pembahasan Musyawarah</label>
                <textarea name="discussion_dynamics" rows="2" placeholder="Tanggapan orang tua, masukan wali kelas, pertimbangan guru BK..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Keputusan Bersama Sidang Konferensi Kasus <span class="text-rose-500">*</span></label>
                <textarea name="agreed_decisions" rows="3" required placeholder="Poin-poin kesepakatan penanganan yang disetujui bersama..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Komitmen Tindak Lanjut & Tanggung Jawab Masing-masing Pihak <span class="text-rose-500">*</span></label>
                <textarea name="follow_up_commitments" rows="3" required placeholder="1. Orang tua berkomitmen...&#10;2. Siswa berkomitmen...&#10;3. Guru BK & Wali Kelas akan..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
            <a href="{{ route('bk.case-conference.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                Simpan Berita Acara Konferensi
            </button>
        </div>
    </form>
</div>
@endsection

