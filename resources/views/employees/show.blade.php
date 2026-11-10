@extends('layouts.app')

@section('title', 'Detail Pegawai - ' . $employee->formatted_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900">{{ $employee->formatted_name }}</h1>
                <p class="text-xs text-slate-500">NIP: {{ $employee->nip ?? 'Non-PNS' }} | {{ $employee->employment_type }} &bull; {{ $employee->position }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Pegawai
            </a>
            <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition border border-rose-200" title="Hapus Data">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-linear-to-tr from-indigo-600 to-blue-700 text-white text-3xl font-bold flex items-center justify-center shadow-md">
                    {{ substr($employee->name, 0, 1) }}
                </div>
                <h2 class="mt-4 text-base font-bold text-slate-900">{{ $employee->formatted_name }}</h2>
                <p class="text-xs text-slate-500">{{ $employee->position }}</p>
                <div class="mt-2 flex items-center justify-center gap-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ $employee->employment_status }}</span>
                    @if($employee->is_certified)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Bersertifikasi Pendidik</span>
                    @endif
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100 text-left space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">NIP</span>
                        <span class="font-mono font-semibold text-slate-900">{{ $employee->nip ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">NUPTK</span>
                        <span class="font-mono font-semibold text-slate-900">{{ $employee->nuptk ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jenis Kelamin</span>
                        <span class="font-semibold text-slate-900">{{ $employee->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pangkat / Golongan</span>
                        <span class="font-semibold text-slate-900">{{ $employee->rank_group ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">TMT Pangkat</span>
                        <span class="font-semibold text-slate-900">{{ $employee->rank_tmt?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Masa Kerja</span>
                        <span class="font-semibold text-slate-900">{{ $employee->years_of_service_years ?? 0 }} Thn {{ $employee->years_of_service_months ?? 0 }} Bln</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pendidikan Terakhir</span>
                        <span class="font-semibold text-slate-900">{{ $employee->education_level ?? 'S1' }} {{ $employee->education_major }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Mapel yang Diampu</span>
                        <span class="font-semibold text-blue-700">{{ $employee->teaching_subject ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. HP / WA</span>
                        <span class="font-semibold text-slate-900">{{ $employee->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Email</span>
                        <span class="font-semibold text-slate-900">{{ $employee->email ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- History & Portfolios -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Riwayat Karir, Diklat, & Kenaikan Pangkat
                    </h3>
                    <button onclick="document.getElementById('modal-history').classList.remove('hidden')" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl border border-blue-200 transition cursor-pointer">
                        + Tambah Riwayat
                    </button>
                </div>

                @if($employee->histories->isEmpty())
                    <p class="text-xs text-slate-400 py-6 text-center">Belum ada riwayat kepegawaian yang dicatat.</p>
                @else
                    <div class="relative border-l-2 border-slate-200 ml-3 space-y-6 text-xs">
                        @foreach($employee->histories as $h)
                            <div class="relative pl-6">
                                <span class="absolute -left-2 top-1 w-4 h-4 rounded-full bg-blue-600 border-2 border-white"></span>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-sm">{{ $h->title }}</span>
                                    <span class="text-slate-400 text-[11px] font-mono">{{ $h->start_date?->format('d M Y') ?? '-' }}</span>
                                </div>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $h->history_type }}
                                </span>
                                <p class="text-slate-600 mt-1">Institusi / Penerbit: {{ $h->institution_or_issuer ?? '-' }}</p>
                                @if($h->document_number)
                                    <p class="text-slate-500 font-mono text-[11px]">No. Dokumen/SK: {{ $h->document_number }}</p>
                                @endif
                                @if($h->notes)
                                    <p class="text-slate-500 italic mt-1">{{ $h->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Riwayat Kepegawaian -->
<div id="modal-history" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Tambah Riwayat Kepegawaian</h2>
        <p class="text-xs text-slate-500 mb-4">Input data kenaikan pangkat, mutasi tugas, diklat, atau ijazah baru.</p>

        <form action="{{ route('employees.histories.store', $employee) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Jenis Riwayat <span class="text-rose-500">*</span></label>
                <select name="history_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="Kenaikan Pangkat">Kenaikan Pangkat</option>
                    <option value="Kenaikan Gaji Berkala">Kenaikan Gaji Berkala (KGB)</option>
                    <option value="Jabatan & Penugasan">Jabatan & Penugasan</option>
                    <option value="Pendidikan Formal">Pendidikan Formal</option>
                    <option value="Diklat & Pelatihan">Diklat & Pelatihan</option>
                    <option value="Penghargaan / Tanda Jasa">Penghargaan / Tanda Jasa</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Judul / Uraian Riwayat <span class="text-rose-500">*</span></label>
                <input type="text" name="title" placeholder="Contoh: Kenaikan Pangkat ke Pembina (IV/a)" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Instansi / Penyelenggara</label>
                    <input type="text" name="institution_or_issuer" placeholder="Badan Kepegawaian Negara / Kemendikbud" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor SK / Sertifikat</label>
                    <input type="text" name="document_number" placeholder="821/SK/2025" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai / TMT</label>
                <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-history').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Riwayat</button>
            </div>
        </form>
    </div>
</div>
@endsection
