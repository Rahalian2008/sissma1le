@extends('layouts.app')

@section('title', 'Penataan Rombel & Kenaikan Kelas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span class="p-1.5 rounded-xl bg-gradient-to-tr from-sky-500 to-blue-600 text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </span>
                Rombongan Belajar & Kenaikan Kelas
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Penataan anggota rombel kelas, mutasi rombel internal, dan kenaikan tingkat/kelulusan massal.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('students.rombel.print', ['rombel' => $selectedRombel]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Daftar Rombel
            </a>
            <button onclick="document.getElementById('modal-promote-mass').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Kenaikan / Kelulusan Kelas Massal
            </button>
        </div>
    </div>

    <!-- Rombel Selector Pills -->
    <div class="bg-white rounded-2xl p-3 shadow-2xs border border-slate-200 overflow-x-auto">
        <div class="flex items-center gap-2 min-w-max">
            <span class="text-xs font-bold text-slate-500 px-2">Pilih Rombel:</span>
            @foreach($rombels as $r)
                <a href="{{ route('students.rombel.index', ['rombel' => $r]) }}" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition {{ $selectedRombel === $r ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    {{ $r }}
                    <span class="ml-1 text-[10px] px-1.5 py-0.2 rounded-full {{ $selectedRombel === $r ? 'bg-blue-500/50 text-white' : 'bg-slate-200 text-slate-600' }}">
                        {{ $rombelStats[$r]['total'] ?? 0 }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Active Rombel Info Banner -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl p-5 text-white shadow-xs border border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold mb-2">
                Rombongan Belajar Aktif
            </div>
            <h2 class="text-xl font-black">{{ $selectedRombel }}</h2>
            <p class="text-xs text-slate-300 mt-0.5">
                Wali Kelas: <strong class="text-white">{{ $waliKelas?->name ?? 'Belum ditentukan (Silakan atur di Kepegawaian)' }}</strong>
            </p>
        </div>
        <div class="flex items-center gap-4 bg-white/5 border border-white/10 rounded-xl p-3 text-xs">
            <div class="text-center px-2">
                <span class="text-[10px] text-slate-400 block">Total Siswa</span>
                <span class="text-lg font-black text-white">{{ $students->count() }}</span>
            </div>
            <div class="h-8 w-px bg-white/10"></div>
            <div class="text-center px-2">
                <span class="text-[10px] text-sky-400 block">Laki-laki</span>
                <span class="text-lg font-black text-sky-300">{{ $students->where('gender', 'L')->count() }}</span>
            </div>
            <div class="h-8 w-px bg-white/10"></div>
            <div class="text-center px-2">
                <span class="text-[10px] text-pink-400 block">Perempuan</span>
                <span class="text-lg font-black text-pink-300">{{ $students->where('gender', 'P')->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Students Table in Selected Rombel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Daftar Anggota Siswa ({{ $selectedRombel }})</h3>
            <span class="text-xs text-slate-500">Urut Abjad Nama</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5" style="width: 50px;">No</th>
                        <th class="px-5 py-3.5">Nama Peserta Didik</th>
                        <th class="px-5 py-3.5">NIS / NISN</th>
                        <th class="px-5 py-3.5 text-center">L/P</th>
                        <th class="px-5 py-3.5">Poin Tatib</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $idx => $st)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 text-slate-500 font-mono">{{ $idx + 1 }}</td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $st->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $st->birth_place }}, {{ $st->birth_date?->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-mono">
                                <div>{{ $st->nis }}</div>
                                <div class="text-[10px] text-slate-400">NISN: {{ $st->nisn ?: '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold {{ $st->gender === 'L' ? 'text-sky-600' : 'text-pink-600' }}">
                                {{ $st->gender }}
                            </td>
                            <td class="px-5 py-3.5">
                                @php $pts = $st->total_violation_points; @endphp
                                @if($pts > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $pts >= 50 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $pts }} Poin
                                    </span>
                                @else
                                    <span class="text-emerald-600 font-semibold text-[11px]">✓ Bersih (0)</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    {{ $st->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                <a href="{{ route('buku-induk.show', $st) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition cursor-pointer inline-block" title="Lihat Buku Induk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <button type="button" onclick="openMoveRombel({{ $st->id }}, '{{ $st->name }}', '{{ $st->current_rombel }}')" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer" title="Pindah Rombel">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada siswa yang ditempatkan pada rombel {{ $selectedRombel }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pindah Rombel Perorangan -->
<div id="modal-move-rombel" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pindah Rombel Siswa</h2>
                <p id="move-student-name" class="text-xs text-blue-700 font-semibold"></p>
            </div>
            <button type="button" onclick="document.getElementById('modal-move-rombel').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form action="{{ route('students.rombel.move') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <input type="hidden" name="student_id" id="move-student-id">

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Rombel Tujuan Baru <span class="text-rose-500">*</span></label>
                <select name="target_rombel" id="move-target-rombel" required class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white">
                    @foreach($rombels as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-move-rombel').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan Pemindahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kenaikan / Kelulusan Kelas Massal -->
<div id="modal-promote-mass" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">Kenaikan / Kelulusan Massal</h2>
                <p class="text-xs text-slate-500">Proses seluruh siswa di rombel terpilih secara serentak.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-promote-mass').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form action="{{ route('students.rombel.promote') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="p-3 bg-blue-50 border border-blue-200 rounded-2xl text-blue-900">
                <div class="font-bold">Rombel Sumber yang Diproses:</div>
                <div class="text-sm font-black">{{ $selectedRombel }} ({{ $students->count() }} Siswa Aktif)</div>
                <input type="hidden" name="source_rombel" value="{{ $selectedRombel }}">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Jenis Tindakan <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-blue-500 has-checked:border-blue-600 has-checked:bg-blue-50/50">
                        <input type="radio" name="action_type" value="promote" checked onchange="toggleActionType(this.value)" class="text-blue-600">
                        <div>
                            <span class="font-bold text-slate-900 block">Kenaikan Kelas</span>
                            <span class="text-[10px] text-slate-500">Naik ke tingkat berikutnya</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-blue-500 has-checked:border-blue-600 has-checked:bg-blue-50/50">
                        <input type="radio" name="action_type" value="graduate" onchange="toggleActionType(this.value)" class="text-blue-600">
                        <div>
                            <span class="font-bold text-slate-900 block">Luluskan Siswa</span>
                            <span class="text-[10px] text-slate-500">Untuk kelas XII yang tamat</span>
                        </div>
                    </label>
                </div>
            </div>

            <div id="target-rombel-container">
                <label class="block font-semibold text-slate-700 mb-1">Rombel Tujuan Kenaikan Kelas <span class="text-rose-500">*</span></label>
                <select name="target_rombel" id="promote-target-rombel" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white">
                    @foreach($rombels as $r)
                        @if($r !== $selectedRombel)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-[11px] text-amber-800">
                ⚠️ Pastikan data rapor dan keputusan rapat pleno dewan guru sudah tuntas sebelum memproses kenaikan atau kelulusan massal.
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-promote-mass').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold cursor-pointer">Batal</button>
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memproses rombel ini?')" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Jalankan Proses</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openMoveRombel(id, name, currentRombel) {
    document.getElementById('move-student-id').value = id;
    document.getElementById('move-student-name').innerText = name + ' (Rombel Sekarang: ' + currentRombel + ')';
    document.getElementById('move-target-rombel').value = currentRombel;
    document.getElementById('modal-move-rombel').classList.remove('hidden');
}

function toggleActionType(type) {
    const container = document.getElementById('target-rombel-container');
    if (type === 'graduate') {
        container.classList.add('hidden');
    } else {
        container.classList.remove('hidden');
    }
}
</script>
@endpush
@endsection
