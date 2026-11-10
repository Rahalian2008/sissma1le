@extends('layouts.app')

@section('title', 'Tahun Pelajaran & Semester')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Tahun Pelajaran & Semester</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola kalender periode akademik aktif satuan pendidikan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4">
                Tambah Tahun Pelajaran
            </h2>
            <form action="{{ route('master.academic-years.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Pelajaran (e.g. 2026/2027) *</label>
                    <input type="text" name="year" placeholder="2026/2027" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Semester *</label>
                    <select name="semester" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div class="pt-2">
                    <label class="flex items-center text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-blue-600 rounded">
                        <span class="ml-2 font-medium">Jadikan Semester Aktif</span>
                    </label>
                </div>
                <button type="submit" class="w-full mt-2 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm">
                    Simpan Tahun Pelajaran
                </button>
            </form>
        </div>

        <!-- Daftar Tabel -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 font-bold text-sm text-slate-800">
                Daftar Periode Akademik
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Tahun</th>
                            <th class="px-4 py-3">Semester</th>
                            <th class="px-4 py-3">Rentang Waktu</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($years as $yr)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $yr->year }}</td>
                                <td class="px-4 py-3 font-medium">{{ $yr->semester }}</td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ $yr->start_date ? $yr->start_date->format('d/m/Y') : '-' }} s/d {{ $yr->end_date ? $yr->end_date->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($yr->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">AKTIF</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">Arsip</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    @if(!$yr->is_active)
                                        <form action="{{ route('master.academic-years.set-active', $yr) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-blue-600 hover:underline font-semibold cursor-pointer">Aktifkan</button>
                                        </form>
                                    @endif
                                    <button type="button" onclick="openEditAcademicYear({{ json_encode([
                                        'id' => $yr->id,
                                        'year' => $yr->year,
                                        'semester' => $yr->semester,
                                        'start_date' => $yr->start_date?->format('Y-m-d'),
                                        'end_date' => $yr->end_date?->format('Y-m-d'),
                                    ]) }})" class="text-amber-600 hover:underline font-semibold cursor-pointer">Edit</button>
                                    <form action="{{ route('master.academic-years.destroy', $yr) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tahun pelajaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:underline cursor-pointer">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Tahun Pelajaran -->
<div id="modal-edit-academic-year" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Tahun Pelajaran</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui rentang kalender pendidikan dan semester.</p>

        <form id="form-edit-academic-year" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tahun Pelajaran <span class="text-rose-500">*</span></label>
                <input type="text" name="year" id="edit-ay-year" required class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Semester <span class="text-rose-500">*</span></label>
                <select name="semester" id="edit-ay-semester" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="edit-ay-start" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="edit-ay-end" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-academic-year').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditAcademicYear(data) {
        document.getElementById('form-edit-academic-year').action = '/master/academic-years/' + data.id;
        document.getElementById('edit-ay-year').value = data.year;
        document.getElementById('edit-ay-semester').value = data.semester;
        document.getElementById('edit-ay-start').value = data.start_date || '';
        document.getElementById('edit-ay-end').value = data.end_date || '';
        document.getElementById('modal-edit-academic-year').classList.remove('hidden');
    }
</script>
@endsection
