@extends('layouts.app')

@section('title', 'Buku Kontrol Diklat & Pelatihan (PKB PTK)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Buku Kontrol Diklat / PKB</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Pengembangan Keprofesian Berkelanjutan Guru & Tendik</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Catatan Pelatihan, Bimtek & Sertifikasi Pegawai</h1>
            <p class="text-xs text-slate-500 mt-0.5">Portofolio jam pelatihan (JP), workshop kurikulum, diklat fungsional, dan sertifikat kompetensi untuk E-Kinerja BKN & DUPAK Kenaikan Pangkat.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.kontrol-diklat.print', ['year' => request('year'), 'employee_id' => request('employee_id')]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Rekap Diklat
            </a>
            <button onclick="document.getElementById('modalAddDiklat').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Sertifikat Diklat
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Sertifikat Diklat</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalTrainings) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Sertifikat Terarsip</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-emerald-200 bg-emerald-50/20">
            <div class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Akumulasi Jam Pelatihan</div>
            <div class="text-2xl font-bold text-emerald-800 mt-1">{{ number_format($totalHours) }} <span class="text-sm font-semibold">JP</span></div>
            <div class="text-[10px] text-emerald-600/80 mt-0.5">Total Jam Pelatihan Diikuti</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-blue-200 bg-blue-50/20">
            <div class="text-[11px] font-semibold text-blue-700 uppercase tracking-wider">Diklat Tahun {{ date('Y') }}</div>
            <div class="text-2xl font-bold text-blue-800 mt-1">{{ number_format($thisYearCount) }}</div>
            <div class="text-[10px] text-blue-600/80 mt-0.5">Kegiatan Tahun Berjalan</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-indigo-200 bg-indigo-50/20">
            <div class="text-[11px] font-semibold text-indigo-700 uppercase tracking-wider">Pegawai Bersertifikat</div>
            <div class="text-2xl font-bold text-indigo-800 mt-1">{{ number_format($uniqueEmployeesCount) }}</div>
            <div class="text-[10px] text-indigo-600/80 mt-0.5">Guru & Tendik Aktif PKB</div>
        </div>
    </div>

    <!-- Table Kontrol Diklat -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-3">
            <form method="GET" action="{{ route('employees.kontrol-diklat.index') }}" class="w-full md:w-auto flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama diklat, nomor sertifikat, atau nama guru..." class="px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 w-64">
                
                <select name="employee_id" onchange="this.form.submit()" class="px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-white">
                    <option value="">Semua Pegawai</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->formatted_name }}</option>
                    @endforeach
                </select>

                <select name="year" onchange="this.form.submit()" class="px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 bg-white">
                    <option value="">Semua Tahun</option>
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>

                <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold">Filter</button>
            </form>
            <div class="text-xs text-slate-500">
                Total Entri: <span class="font-bold text-slate-800">{{ $trainings->total() }}</span> Kegiatan Diklat
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Nama Pegawai & NIP</th>
                        <th class="px-4 py-3">Nama Pelatihan / Diklat / Bimtek</th>
                        <th class="px-4 py-3">Penyelenggara</th>
                        <th class="px-4 py-3 text-center">Durasi (JP)</th>
                        <th class="px-4 py-3 text-center">Tanggal Pelaksanaan</th>
                        <th class="px-4 py-3 text-center">No. Sertifikat / Dokumen</th>
                        <th class="px-4 py-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($trainings as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 text-center text-slate-400">{{ $trainings->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $item->employee?->formatted_name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    NIP. {{ $item->employee?->nip ?? '-' }} &bull; {{ $item->employee?->rank_group ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-800">{{ $item->title }}</div>
                                @if($item->notes)
                                    <div class="text-[10px] text-slate-500 mt-0.5 italic">{{ $item->notes }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $item->institution_or_issuer ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-emerald-700">
                                {{ $item->training_hours ?? 32 }} JP
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-[11px]">
                                @if($item->start_date)
                                    {{ $item->start_date->format('d/m/Y') }}
                                    @if($item->end_date && $item->end_date->ne($item->start_date))
                                        <br><span class="text-slate-400">s.d. {{ $item->end_date->format('d/m/Y') }}</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="font-mono text-[11px] text-slate-700 font-medium">
                                    {{ $item->document_number ?: '-' }}
                                </div>
                                @if($item->document_path)
                                    <a href="{{ asset('storage/' . $item->document_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-blue-600 hover:text-blue-800 font-medium mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Sertifikat
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('employees.kontrol-diklat.destroy', $item) }}" onsubmit="return confirm('Hapus catatan pelatihan ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400">Belum ada riwayat diklat atau sertifikasi yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $trainings->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Sertifikat Diklat -->
<div id="modalAddDiklat" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
        <div class="px-5 py-4 bg-emerald-900 text-white flex items-center justify-between">
            <h3 class="font-bold text-sm">Tambah Catatan Sertifikat Diklat / Pelatihan</h3>
            <button onclick="document.getElementById('modalAddDiklat').classList.add('hidden')" class="text-emerald-200 hover:text-white">&times;</button>
        </div>
        <form method="POST" action="{{ route('employees.kontrol-diklat.store') }}" enctype="multipart/form-data" class="p-5 space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Pegawai (Guru / Tendik) *</label>
                <select name="employee_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->formatted_name }} (NIP. {{ $emp->nip ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Diklat / Pelatihan / Workshop *</label>
                <input type="text" name="title" required placeholder="Contoh: Bimtek Implementasi Kurikulum Merdeka Jenjang SMA" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Penyelenggara *</label>
                    <input type="text" name="institution_or_issuer" required placeholder="BBGP Jabar / Kemdikbud" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jumlah Jam Pelatihan (JP) *</label>
                    <input type="number" name="training_hours" value="32" min="1" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Sertifikat / STTPL</label>
                <input type="text" name="document_number" placeholder="Contoh: 1234/B1.2/GT.02.00/2026" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai *</label>
                    <input type="date" name="start_date" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Upload File Sertifikat (PDF/JPG, Max 5MB)</label>
                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Tingkat Nasional/Provinsi, materi pelatihan, dsb..." class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalAddDiklat').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold shadow-sm">Simpan Diklat</button>
            </div>
        </form>
    </div>
</div>
@endsection
