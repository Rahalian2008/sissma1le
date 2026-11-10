@extends('layouts.app')

@section('title', 'Tracer Study Alumni')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Data Penelusuran Alumni</h1>
            <p class="text-xs text-slate-500 mt-0.5">Penelusuran tamatan {{ $school->name ?? 'Sekolah' }} (Pendidikan Tinggi, Dunia Kerja, Wirausaha).</p>
        </div>
        <div>
            <button onclick="document.getElementById('modal-alumni').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Perbarui Tracer Alumni
            </button>
        </div>
    </div>

    <!-- Alumni Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nama Alumni</th>
                        <th class="px-5 py-3.5">Tahun Lulus</th>
                        <th class="px-5 py-3.5">Aktivitas Saat Ini</th>
                        <th class="px-5 py-3.5">Perguruan Tinggi / Instansi Kerja</th>
                        <th class="px-5 py-3.5">Program Studi / Jabatan</th>
                        <th class="px-5 py-3.5">Kontak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($alumni as $al)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $al->student?->name }}
                                <div class="text-[11px] text-slate-400 font-mono font-normal">NIS: {{ $al->student?->nis }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">
                                {{ $al->graduation_year }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($al->activity_status === 'Kuliah') bg-blue-100 text-blue-800
                                    @elseif($al->activity_status === 'Bekerja') bg-emerald-100 text-emerald-800
                                    @elseif($al->activity_status === 'Wirausaha') bg-amber-100 text-amber-800
                                    @else bg-slate-100 text-slate-800 @endif">
                                    {{ $al->activity_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                {{ $al->campus_name ?? $al->company_name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $al->major ?? $al->job_title ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-mono">
                                {{ $al->phone ?? $al->email ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Belum ada data tracer study alumni.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($alumni->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $alumni->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah / Update Tracer Alumni -->
<div id="modal-alumni" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Catat Tracer Study Alumni</h2>
        <p class="text-xs text-slate-500 mb-4">Input aktivitas terkini lulusan sekolah.</p>

        <form action="{{ route('alumni.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa Lulus <span class="text-rose-500">*</span></label>
                <select name="student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Pilih Siswa Lulus --</option>
                    @foreach ($graduatedStudents as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} (NIS: {{ $s->nis }} - Lulus {{ $s->graduation_year ?? date('Y') }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Kelulusan <span class="text-rose-500">*</span></label>
                    <input type="text" name="graduation_year" value="{{ date('Y') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Aktivitas Terkini <span class="text-rose-500">*</span></label>
                    <select name="activity_status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Kuliah">Melanjutkan Kuliah</option>
                        <option value="Bekerja">Bekerja / Profesional</option>
                        <option value="Wirausaha">Wirausaha / Bisnis</option>
                        <option value="Mencari Kerja">Persiapan Kerja</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Kampus (Jika Kuliah)</label>
                    <input type="text" name="campus_name" placeholder="Universitas Indonesia, dsb" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Program Studi / Jurusan</label>
                    <input type="text" name="major" placeholder="Teknik Informatika, Kedokteran" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Perusahaan (Jika Kerja)</label>
                    <input type="text" name="company_name" placeholder="PT. Telkom Indonesia" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jabatan / Posisi</label>
                    <input type="text" name="job_title" placeholder="Software Engineer" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">No. HP / WA</label>
                    <input type="text" name="phone" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-alumni').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Data Alumni</button>
            </div>
        </form>
    </div>
</div>
@endsection
