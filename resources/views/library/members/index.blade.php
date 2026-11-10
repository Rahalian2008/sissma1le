@extends('layouts.app')

@section('title', 'Anggota Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
        <div>
            <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Database Keanggotaan</div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>👥 Anggota Perpustakaan Sekolah</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Data keanggotaan terintegrasi otomatis dengan Master Data Siswa & Guru, serta mendukung entri tamu manual.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Tombol Sinkronisasi dari Master Data -->
            <form action="{{ route('library.members.sync') }}" method="POST" onsubmit="return confirm('Sinkronkan seluruh data siswa & guru aktif dari Master Data ke Anggota Perpustakaan?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 font-bold text-xs shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Sinkronkan Master Data</span>
                </button>
            </form>

            <button type="button" onclick="openNewMemberModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-md transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Entri Anggota Manual</span>
            </button>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs">
        <form action="{{ route('library.members.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama anggota, nomor kartu, NISN/NIP, atau no telepon..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="member_type" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-200">
                <option value="">Semua Tipe Anggota</option>
                <option value="siswa" {{ request('member_type') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                <option value="guru" {{ request('member_type') === 'guru' ? 'selected' : '' }}>Guru / Pegawai</option>
                <option value="umum" {{ request('member_type') === 'umum' ? 'selected' : '' }}>Pengunjung Umum / Luar</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'member_type']))
                <a href="{{ route('library.members.index') }}" class="px-3 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Data Anggota -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
        @if($members->isEmpty())
            <div class="py-16 text-center text-slate-400 text-xs">
                Belum ada anggota perpustakaan yang terdaftar. Klik tombol <strong>Sinkronkan Master Data</strong> untuk mendaftarkan otomatis seluruh siswa dan guru.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="text-[11px] text-slate-400 uppercase bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3.5">Nomor Anggota</th>
                            <th class="px-4 py-3.5">Nama Lengkap</th>
                            <th class="px-4 py-3.5">Tipe & Identitas</th>
                            <th class="px-4 py-3.5">Kelas / Jabatan</th>
                            <th class="px-4 py-3.5">Tgl Bergabung</th>
                            <th class="px-4 py-3.5 text-center">Pinjaman Aktif</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($members as $m)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $m->member_code }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white">
                                    {{ $m->name }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $m->member_type === 'siswa' ? 'bg-cyan-500/15 text-cyan-600' : ($m->member_type === 'guru' ? 'bg-emerald-500/15 text-emerald-600' : 'bg-purple-500/15 text-purple-600') }}">
                                        {{ $m->member_type }}
                                    </span>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $m->identity_number ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3.5 text-slate-700 dark:text-slate-300">
                                    {{ $m->member_class_or_title }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-500">
                                    {{ $m->joined_at ? $m->joined_at->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[11px] {{ $m->activeLoans->count() > 0 ? 'bg-indigo-500/20 text-indigo-600' : 'text-slate-400' }}">
                                        {{ $m->activeLoans->count() }} buku
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $m->is_active ? 'bg-emerald-500/15 text-emerald-600' : 'bg-slate-500/15 text-slate-500' }}">
                                        {{ $m->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('library.members.print_card', $m) }}" target="_blank"
                                           class="px-2 py-1 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 font-bold text-xs border border-indigo-500/20 transition cursor-pointer"
                                           title="Cetak Kartu Anggota">
                                            🪪 Kartu
                                        </a>
                                        <a href="{{ route('library.members.clearance', $m) }}" target="_blank"
                                           class="px-2 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold text-xs border border-emerald-500/20 transition cursor-pointer"
                                           title="Cetak Surat Bebas Pustaka">
                                            📜 Bebas Pustaka
                                        </a>
                                        <button type="button" 
                                                onclick="openEditMemberModal({{ json_encode([
                                                    'id' => $m->id,
                                                    'member_code' => $m->member_code,
                                                    'name' => $m->name,
                                                    'member_type' => $m->member_type,
                                                    'identity_number' => $m->identity_number,
                                                    'phone' => $m->phone,
                                                    'email' => $m->email,
                                                    'address' => $m->address,
                                                    'is_active' => (bool)$m->is_active,
                                                    'notes' => $m->notes,
                                                ]) }})"
                                                title="Edit Data Anggota"
                                                class="px-2.5 py-1 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold text-xs border border-amber-500/20 transition cursor-pointer">
                                            ✏️ Edit
                                        </button>
                                        <form action="{{ route('library.members.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data anggota {{ addslashes($m->name) }} ({{ $m->member_code }})? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Hapus Anggota"
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-500/20 transition cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Anggota Manual -->
<div id="new-member-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>👤 Entri Anggota Perpustakaan Manual</span>
            </h3>
            <button type="button" onclick="closeNewMemberModal()" class="text-slate-400 hover:text-white text-lg p-1">✕</button>
        </div>

        <form action="{{ route('library.members.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Anggota <span class="text-rose-500">*</span></label>
                <select name="member_type" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                    <option value="umum">Pengunjung Umum / Luar</option>
                    <option value="guru">Guru / Staf Khusus</option>
                    <option value="siswa">Siswa Khusus</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Nama lengkap anggota..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Identitas (NIK KTP / NIP / NISN)</label>
                <input type="text" name="identity_number" placeholder="Nomor KTP atau identitas resmi..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Telepon/WA</label>
                    <input type="text" name="phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Instansi / Asal</label>
                    <input type="text" name="address" placeholder="Kota / Instansi asal..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan</label>
                <input type="text" name="notes" placeholder="Catatan keanggotaan..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeNewMemberModal()" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition cursor-pointer">
                    Simpan Anggota
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Anggota Perpustakaan -->
<div id="edit-member-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>✏️ Edit Data Anggota Perpustakaan</span>
            </h3>
            <button type="button" onclick="closeEditMemberModal()" class="text-slate-400 hover:text-white text-lg p-1">✕</button>
        </div>

        <form id="edit-member-form" action="" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Anggota</label>
                <input type="text" id="edit-member-code" disabled class="w-full px-3 py-2 text-xs bg-slate-100 dark:bg-slate-800/50 border border-slate-300 dark:border-slate-700 rounded-xl outline-none font-mono font-bold text-slate-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Anggota <span class="text-rose-500">*</span></label>
                <select name="member_type" id="edit-member-type" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                    <option value="siswa">Siswa</option>
                    <option value="guru">Guru / Pendidik</option>
                    <option value="umum">Pengunjung Umum / Luar</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit-member-name" required placeholder="Nama lengkap..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Identitas (NISN / NIP / NIK KTP)</label>
                <input type="text" name="identity_number" id="edit-member-identity" placeholder="NISN, NIP, atau NIK KTP..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. HP / WA</label>
                    <input type="text" name="phone" id="edit-member-phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Keanggotaan</label>
                    <select name="is_active" id="edit-member-status" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email</label>
                <input type="email" name="email" id="edit-member-email" placeholder="email@domain.com" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat / Asal Instansi</label>
                <input type="text" name="address" id="edit-member-address" placeholder="Alamat atau asal instansi..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan</label>
                <input type="text" name="notes" id="edit-member-notes" placeholder="Catatan khusus..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeEditMemberModal()" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-md transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openNewMemberModal() {
        document.getElementById('new-member-modal').classList.remove('hidden');
    }

    function closeNewMemberModal() {
        document.getElementById('new-member-modal').classList.add('hidden');
    }

    function openEditMemberModal(data) {
        const form = document.getElementById('edit-member-form');
        form.action = `/library/members/${data.id}`;
        document.getElementById('edit-member-code').value = data.member_code || '';
        document.getElementById('edit-member-type').value = data.member_type || 'siswa';
        document.getElementById('edit-member-name').value = data.name || '';
        document.getElementById('edit-member-identity').value = data.identity_number || '';
        document.getElementById('edit-member-phone').value = data.phone || '';
        document.getElementById('edit-member-email').value = data.email || '';
        document.getElementById('edit-member-status').value = data.is_active ? '1' : '0';
        document.getElementById('edit-member-address').value = data.address || '';
        document.getElementById('edit-member-notes').value = data.notes || '';
        document.getElementById('edit-member-modal').classList.remove('hidden');
    }

    function closeEditMemberModal() {
        document.getElementById('edit-member-modal').classList.add('hidden');
    }
</script>
@endsection

