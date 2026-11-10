@extends('layouts.app')

@section('title', 'Edit Konferensi Kasus - ' . $caseConference->case_title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Edit Konferensi Kasus Siswa</h1>
            <p class="text-xs text-slate-500">Nomor Registrasi: <span class="font-mono font-bold">{{ $caseConference->conference_code }}</span></p>
        </div>
        <a href="{{ route('bk.case-conference.show', $caseConference) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Batal / Kembali
        </a>
    </div>

    <form action="{{ route('bk.case-conference.update', $caseConference) }}" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-5 text-xs">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div class="p-3 bg-purple-50 rounded-xl">
                <span class="text-slate-500 font-bold">Siswa:</span>
                <span class="font-bold text-slate-900 ml-1">{{ $caseConference->student?->name }} ({{ $caseConference->student?->schoolClass?->name ?? '-' }})</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Judul / Topik Kasus <span class="text-rose-500">*</span></label>
                <input type="text" name="case_title" value="{{ old('case_title', $caseConference->case_title) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Sidang <span class="text-rose-500">*</span></label>
                    <input type="date" name="conference_date" value="{{ old('conference_date', $caseConference->conference_date?->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Waktu Pelaksanaan</label>
                    <input type="text" name="conference_time" value="{{ old('conference_time', $caseConference->conference_time) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tempat Sidang</label>
                    <input type="text" name="place" value="{{ old('place', $caseConference->place) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pimpinan Sidang / Konferensi <span class="text-rose-500">*</span></label>
                    <input type="text" name="leader_name" value="{{ old('leader_name', $caseConference->leader_name) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Sidang Kasus <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="TERLAKSANA" {{ $caseConference->status === 'TERLAKSANA' ? 'selected' : '' }}>Terlaksana (Berita Acara Dibuat)</option>
                        <option value="TERJADWAL" {{ $caseConference->status === 'TERJADWAL' ? 'selected' : '' }}>Terjadwal (Akan Datang)</option>
                        <option value="DITINDAKLANJUTI" {{ $caseConference->status === 'DITINDAKLANJUTI' ? 'selected' : '' }}>Sedang Ditindaklanjuti</option>
                        <option value="SELESAI" {{ $caseConference->status === 'SELESAI' ? 'selected' : '' }}>Selesai (Kasus Tuntas)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Latar Belakang Masalah (Deskripsi Kasus) <span class="text-rose-500">*</span></label>
                <textarea name="case_background" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('case_background', $caseConference->case_background) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Fakta & Temuan Data <span class="text-rose-500">*</span></label>
                <textarea name="facts_and_findings" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('facts_and_findings', $caseConference->facts_and_findings) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Dinamika Diskusi & Pembahasan Musyawarah</label>
                <textarea name="discussion_dynamics" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('discussion_dynamics', $caseConference->discussion_dynamics) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Keputusan Bersama Sidang Konferensi Kasus <span class="text-rose-500">*</span></label>
                <textarea name="agreed_decisions" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('agreed_decisions', $caseConference->agreed_decisions) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Komitmen Tindak Lanjut <span class="text-rose-500">*</span></label>
                <textarea name="follow_up_commitments" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">{{ old('follow_up_commitments', $caseConference->follow_up_commitments) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <button type="button" onclick="if(confirm('Hapus konferensi kasus ini?')) document.getElementById('delete-form').submit();" class="text-rose-600 hover:text-rose-700 font-bold">
                Hapus Kasus
            </button>
            <div class="flex items-center gap-2">
                <a href="{{ route('bk.case-conference.show', $caseConference) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                    Perbarui Berita Acara
                </button>
            </div>
        </div>
    </form>

    <form id="delete-form" action="{{ route('bk.case-conference.destroy', $caseConference) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

