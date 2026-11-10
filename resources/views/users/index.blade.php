@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Multi-Role')

@section('content')
<div class="space-y-6">
    @include('master.partials.nav')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Pengguna & Multi-Role</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data login petugas, staf tata usaha, guru, dan atur penugasan multi-peran (1 petugas dapat memiliki lebih dari 1 role).</p>
        </div>
        <div>
            <button onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pengguna Baru
            </button>
        </div>
    </div>

    <!-- Table Users -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-3">
            <form method="GET" action="{{ route('users.index') }}" class="w-full md:w-80">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, NIP, atau email..." class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>
            <div class="text-xs text-slate-500">
                Total: <strong class="text-slate-800">{{ $users->total() }}</strong> Akun Pengguna Terdaftar
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-semibold text-[11px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Nama & Kontak</th>
                        <th class="px-5 py-3.5">Akun Login</th>
                        <th class="px-5 py-3.5">Peran / Hak Akses (Multi-Role)</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-full border border-slate-200 object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'32\' height=\'32\' fill=\'%23cbd5e1\'><rect width=\'32\' height=\'32\' rx=\'16\'/></svg>'">
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $u->name }}</div>
                                        <div class="text-slate-500 text-[11px]">{{ $u->phone ?? 'Tidak ada nomor telepon' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-900 font-bold font-mono text-xs">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $u->username ?: '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1 space-y-0.5">
                                    @if($u->nip || $u->employee?->nip)
                                        <div><span class="text-slate-400">NIP:</span> <span class="font-mono text-slate-700 font-medium">{{ $u->nip ?: $u->employee?->nip }}</span></div>
                                    @endif
                                    @if($u->email)
                                        <div><span class="text-slate-400">Email:</span> <span class="text-slate-600">{{ $u->email }}</span></div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $userRoles = $u->relationLoaded('roles') ? $u->getRelation('roles') : null;
                                        if (!$userRoles || !($userRoles instanceof \Illuminate\Support\Collection) || $userRoles->isEmpty()) {
                                            $raw = $u->getRawOriginal('roles');
                                            $roleArr = is_array($raw) ? $raw : (json_decode($raw ?? '[]', true) ?: []);
                                            if (!empty($roleArr)) {
                                                $userRoles = collect($roleArr)->map(fn($r) => (object)['name' => $r, 'display_name' => ucwords(str_replace('_', ' ', $r))]);
                                            }
                                        }
                                    @endphp
                                    @if($userRoles && $userRoles->isNotEmpty())
                                        @foreach($userRoles as $r)
                                            @php
                                                $rName = is_object($r) ? ($r->name ?? '') : (is_string($r) ? $r : '');
                                                $rDisplay = is_object($r) ? ($r->display_name ?? $rName) : ucwords(str_replace('_', ' ', (string)$r));
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ in_array($rName, ['admin', 'super_admin']) ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                                {{ $rDisplay }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $u->activeRole?->display_name ?? ucwords(str_replace('_', ' ', $u->role ?? 'Staf')) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($u->is_active)
                                    <span class="inline-flex items-center text-emerald-700 font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-rose-700 font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Dinonaktifkan
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <button type="button" 
                                        onclick="openEditUserModal({{ json_encode([
                                            'id' => $u->id,
                                            'name' => $u->name,
                                            'username' => $u->username,
                                            'nip' => $u->nip ?: $u->employee?->nip,
                                            'email' => $u->email,
                                            'phone' => $u->phone,
                                            'role' => $u->role,
                                            'roles' => $u->all_roles,
                                            'role_ids' => $roles->whereIn('name', $u->all_roles)->pluck('id')->values()->toArray(),
                                            'is_active' => (bool)$u->is_active,
                                            'action_url' => route('users.update', $u),
                                        ]) }})"
                                        class="text-blue-600 hover:text-blue-800 font-semibold hover:underline inline-flex items-center text-xs">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit Peran
                                </button>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun pengguna {{ $u->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold hover:underline inline-flex items-center text-xs">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Pengguna -->
<div id="addUserModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm">Tambah Pengguna Baru</h3>
                <p class="text-[11px] text-slate-300">Buat akun login (NIP, nama depan, atau username custom) dan tentukan role</p>
            </div>
            <button onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="{{ route('users.store') }}" method="POST" class="p-5 space-y-3.5 text-xs overflow-y-auto">
            @csrf
            <div class="bg-blue-50/80 p-3 rounded-xl border border-blue-200">
                <label class="block font-bold text-blue-900 mb-1">Ambil dari Data Guru & Tendik (Otomatis)</label>
                <select id="selectEmployee" name="employee_id" class="w-full px-3 py-2 bg-white border border-blue-300 rounded-lg text-xs" onchange="fillFromEmployee(this)">
                    <option value="">-- Pilih Guru / Tendik (Otomatis Mengisi Nama & Akun) --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" 
                                data-name="{{ $emp->formatted_name ?: $emp->name }}" 
                                data-nip="{{ $emp->nip }}"
                                data-email="{{ $emp->email }}" 
                                data-phone="{{ $emp->phone }}">
                            {{ $emp->formatted_name ?: $emp->name }} (NIP: {{ $emp->nip ?: '-' }} | {{ $emp->employment_type ?? 'PTK' }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-blue-700 mt-1">Pilih PTK untuk otomatis mengisi nama lengkap, akun login (NIP/Nama Depan), dan nomor telepon.</p>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" id="addUserName" name="name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Nama lengkap petugas beserta gelar jika ada">
            </div>

            <div>
                <label class="block font-bold text-slate-800 mb-1">Akun Login / Username *</label>
                <input type="text" id="addUserUsername" name="username" required class="w-full px-3 py-2 border border-blue-400 bg-blue-50/30 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-xs font-semibold" placeholder="Contoh: 198303242022211010 atau rahmat atau admin_tu">
                <p class="text-[10px] text-slate-500 mt-1">
                    <span class="text-blue-600 font-semibold">&bull; Bebas & Fleksibel:</span> Cukup gunakan <strong>NIP</strong>, <strong>nama depan</strong>, atau <strong>username custom</strong> (tidak wajib email).
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NIP (Opsional)</label>
                    <input type="text" id="addUserNip" name="nip" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-xs" placeholder="18 digit angka NIP">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email Dinas (Opsional)</label>
                    <input type="email" id="addUserEmail" name="email" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-xs" placeholder="opsional@sekolah.sch.id">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kata Sandi Awal *</label>
                <input type="password" name="password" required minlength="8" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Minimal 8 karakter">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" id="addUserPhone" name="phone" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="08xxxxxxxxxx">
            </div>

            <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-xl space-y-3">
                <span class="font-bold text-blue-900 text-xs block">Pengaturan Akun Login & Multi-Role:</span>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Peran Utama (Primary Role) <span class="text-rose-500">*</span></label>
                    <select name="role" id="addUserPrimaryRole" onchange="handleUserPrimaryRoleChange('add')" required class="w-full px-3 py-2 bg-white border border-blue-300 rounded-lg text-xs font-bold text-blue-900 focus:ring-2 focus:ring-blue-500">
                        <option value="guru">Tenaga Pendidik (Guru Mapel)</option>
                        <option value="staff">Tenaga Kependidikan (Staf TU)</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- Peran Tambahan Tenaga Pendidik (14 Role) -->
                <div id="add_user_pendidik_roles_box" class="space-y-2">
                    <label class="block font-bold text-slate-700 mb-1 text-[11px]">Pilih Peran Tambahan Tenaga Pendidik (Bisa lebih dari 1 role):</label>
                    <div class="grid grid-cols-2 gap-1.5 bg-white p-2.5 rounded-lg border border-slate-200 text-[11px] max-h-48 overflow-y-auto">
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-blue-700">
                            <input type="checkbox" name="roles[]" value="kepala_sekolah" class="w-3.5 h-3.5 rounded text-blue-600 focus:ring-0">
                            <span>Kepala Sekolah</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-sky-700">
                            <input type="checkbox" name="roles[]" value="wakasek_kurikulum" class="w-3.5 h-3.5 rounded text-sky-600 focus:ring-0">
                            <span class="font-bold">Wakasek Kurikulum</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700">
                            <input type="checkbox" name="roles[]" value="wakasek_kesiswaan" class="w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                            <span class="font-bold">Wakasek Kesiswaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-pink-700">
                            <input type="checkbox" name="roles[]" value="wakasek_humas" class="w-3.5 h-3.5 rounded text-pink-600 focus:ring-0">
                            <span class="font-bold">Wakasek Humas</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-teal-700">
                            <input type="checkbox" name="roles[]" value="wakasek_sarpras" class="w-3.5 h-3.5 rounded text-teal-600 focus:ring-0">
                            <span class="font-bold">Wakasek Sarpras</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-purple-700">
                            <input type="checkbox" name="roles[]" value="kepala_laboratorium" class="w-3.5 h-3.5 rounded text-purple-600 focus:ring-0">
                            <span class="font-bold">Kepala Laboratorium</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700">
                            <input type="checkbox" name="roles[]" value="kepala_perpustakaan" class="w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                            <span class="font-bold">Kepala Perpustakaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-indigo-700">
                            <input type="checkbox" name="roles[]" value="koordinator_bk" class="w-3.5 h-3.5 rounded text-indigo-600 focus:ring-0">
                            <span class="font-bold">Koordinator BP/BK</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="koordinator_keagamaan" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span>Koordinator Keagamaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-indigo-700">
                            <input type="checkbox" name="roles[]" value="wali_kelas" class="w-3.5 h-3.5 rounded text-indigo-600 focus:ring-0">
                            <span>Wali Kelas</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-orange-700">
                            <input type="checkbox" name="roles[]" value="bk" class="w-3.5 h-3.5 rounded text-orange-600 focus:ring-0">
                            <span>Guru BK</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-rose-700">
                            <input type="checkbox" name="roles[]" value="guru_piket" class="w-3.5 h-3.5 rounded text-rose-600 focus:ring-0">
                            <span class="font-bold">Petugas Piket</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="pengelola_perpustakaan" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span>Pengelola Perpustakaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-cyan-700">
                            <input type="checkbox" name="roles[]" value="pembina_ekstra" class="w-3.5 h-3.5 rounded text-cyan-600 focus:ring-0">
                            <span class="font-bold">Pembina Ekstrakurikuler</span>
                        </label>
                    </div>

                    @if(isset($extracurriculars) && $extracurriculars->isNotEmpty())
                    <div class="mt-2">
                        <label class="block font-bold text-slate-700 mb-1 text-[10px]">Penugasan Ekstrakurikuler (Bina Ekstra):</label>
                        <div class="grid grid-cols-2 gap-1.5 bg-white p-2 rounded-lg border border-slate-200 text-[10px] max-h-24 overflow-y-auto">
                            @foreach($extracurriculars as $ekstra)
                            <label class="flex items-center gap-1.5 cursor-pointer text-slate-700 hover:text-cyan-700">
                                <input type="checkbox" name="extracurricular_ids[]" value="{{ $ekstra->id }}" class="w-3 h-3 rounded text-cyan-600 focus:ring-0">
                                <span class="truncate">{{ $ekstra->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Peran Tambahan Tenaga Kependidikan (12 Role) -->
                <div id="add_user_tendik_roles_box" class="hidden space-y-2">
                    <label class="block font-bold text-slate-700 mb-1 text-[11px]">Pilih Peran Tambahan Tenaga Kependidikan (Bisa lebih dari 1 role):</label>
                    <div class="grid grid-cols-2 gap-1.5 bg-white p-2.5 rounded-lg border border-slate-200 text-[11px] max-h-48 overflow-y-auto">
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-800">
                            <input type="checkbox" name="roles[]" value="kepala_administrasi" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span class="font-bold">Kepala Urusan Tata Usaha</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="bendahara_bosp" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span class="font-bold">Bendahara BOSP</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="bendahara_bopd" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span class="font-bold">Bendahara BOPD</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="bendahara_gaji" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span class="font-bold">Bendahara Gaji</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-purple-700">
                            <input type="checkbox" name="roles[]" value="operator_dapodik" class="w-3.5 h-3.5 rounded text-purple-600 focus:ring-0">
                            <span class="font-bold">Operator Dapodik</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-blue-700">
                            <input type="checkbox" name="roles[]" value="ta_kesiswaan" class="w-3.5 h-3.5 rounded text-blue-600 focus:ring-0">
                            <span>Adm. Urusan Kesiswaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-violet-700">
                            <input type="checkbox" name="roles[]" value="ta_kepegawaian" class="w-3.5 h-3.5 rounded text-violet-600 focus:ring-0">
                            <span>Adm. Urusan Kepegawaian</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700">
                            <input type="checkbox" name="roles[]" value="ta_sarpras" class="w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                            <span>Adm. Urusan Sarpras</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-sky-700">
                            <input type="checkbox" name="roles[]" value="ta_persuratan" class="w-3.5 h-3.5 rounded text-sky-600 focus:ring-0">
                            <span>Adm. Urusan Persuratan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700">
                            <input type="checkbox" name="roles[]" value="kepala_perpustakaan" class="w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                            <span class="font-bold">Kepala Perpustakaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="pengelola_perpustakaan" class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span class="font-bold">Pengelola Perpustakaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-rose-700">
                            <input type="checkbox" name="roles[]" value="tu_piket" class="w-3.5 h-3.5 rounded text-rose-600 focus:ring-0">
                            <span class="font-bold">Petugas Piket</span>
                        </label>
                    </div>
                </div>

                <!-- Info Box Admin -->
                <div id="add_user_admin_roles_box" class="hidden p-2.5 rounded-lg bg-red-50 border border-red-200 text-[11px] text-red-800 font-medium">
                    🛡️ <strong>Peran Utama Admin:</strong> Memiliki wewenang Administrator Sekolah penuh untuk mengakses seluruh modul dan konfigurasi.
                </div>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-sm">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div id="editUserModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm">Edit Data Pengguna & Multi-Role</h3>
                <p class="text-[11px] text-slate-300">Sesuaikan data akun login dan hak akses multi-peran petugas</p>
            </div>
            <button onclick="closeEditUserModal()" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form id="formEditUser" method="POST" class="p-5 space-y-3.5 text-xs overflow-y-auto">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" id="editUserName" name="name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div>
                <label class="block font-bold text-slate-800 mb-1">Akun Login / Username *</label>
                <input type="text" id="editUserUsername" name="username" required class="w-full px-3 py-2 border border-blue-400 bg-blue-50/30 rounded-lg font-mono text-xs font-semibold focus:ring-2 focus:ring-blue-500" placeholder="NIP / nama depan / username kustom">
                <p class="text-[10px] text-slate-500 mt-1">Akun login untuk masuk ke aplikasi (bisa NIP, nama depan, atau custom tanpa email).</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NIP (Opsional)</label>
                    <input type="text" id="editUserNip" name="nip" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email Dinas (Opsional)</label>
                    <input type="email" id="editUserEmail" name="email" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs" placeholder="opsional@sekolah.sch.id">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Ganti Kata Sandi (Opsional)</label>
                <input type="password" name="password" minlength="8" class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Kosongkan jika tidak ingin diubah">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" id="editUserPhone" name="phone" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-xl space-y-3">
                <span class="font-bold text-blue-900 text-xs block">Pengaturan Akun Login & Multi-Role:</span>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Peran Utama (Primary Role) <span class="text-rose-500">*</span></label>
                    <select name="role" id="editUserPrimaryRole" onchange="handleUserPrimaryRoleChange('edit')" required class="w-full px-3 py-2 bg-white border border-blue-300 rounded-lg text-xs font-bold text-blue-900 focus:ring-2 focus:ring-blue-500">
                        <option value="guru">Tenaga Pendidik (Guru Mapel)</option>
                        <option value="staff">Tenaga Kependidikan (Staf TU)</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- Peran Tambahan Tenaga Pendidik (14 Role) -->
                <div id="edit_user_pendidik_roles_box" class="space-y-2">
                    <label class="block font-bold text-slate-700 mb-1 text-[11px]">Pilih Peran Tambahan Tenaga Pendidik (Bisa lebih dari 1 role):</label>
                    <div class="grid grid-cols-2 gap-1.5 bg-white p-2.5 rounded-lg border border-slate-200 text-[11px] max-h-48 overflow-y-auto">
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-blue-700">
                            <input type="checkbox" name="roles[]" value="kepala_sekolah" id="edit_u_role_kepala_sekolah" class="edit-u-role-cb w-3.5 h-3.5 rounded text-blue-600 focus:ring-0">
                            <span>Kepala Sekolah</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-sky-700">
                            <input type="checkbox" name="roles[]" value="wakasek_kurikulum" id="edit_u_role_wakasek_kurikulum" class="edit-u-role-cb w-3.5 h-3.5 rounded text-sky-600 focus:ring-0">
                            <span class="font-bold">Wakasek Kurikulum</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700">
                            <input type="checkbox" name="roles[]" value="wakasek_kesiswaan" id="edit_u_role_wakasek_kesiswaan" class="edit-u-role-cb w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                            <span class="font-bold">Wakasek Kesiswaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-pink-700">
                            <input type="checkbox" name="roles[]" value="wakasek_humas" id="edit_u_role_wakasek_humas" class="edit-u-role-cb w-3.5 h-3.5 rounded text-pink-600 focus:ring-0">
                            <span class="font-bold">Wakasek Humas</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-teal-700">
                            <input type="checkbox" name="roles[]" value="wakasek_sarpras" id="edit_u_role_wakasek_sarpras" class="edit-u-role-cb w-3.5 h-3.5 rounded text-teal-600 focus:ring-0">
                            <span class="font-bold">Wakasek Sarpras</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-purple-700">
                            <input type="checkbox" name="roles[]" value="kepala_laboratorium" id="edit_u_role_kepala_laboratorium" class="edit-u-role-cb w-3.5 h-3.5 rounded text-purple-600 focus:ring-0">
                            <span class="font-bold">Kepala Laboratorium</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700">
                            <input type="checkbox" name="roles[]" value="kepala_perpustakaan" id="edit_u_role_kepala_perpustakaan" class="edit-u-role-cb w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                            <span class="font-bold">Kepala Perpustakaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-indigo-700">
                            <input type="checkbox" name="roles[]" value="koordinator_bk" id="edit_u_role_koordinator_bk" class="edit-u-role-cb w-3.5 h-3.5 rounded text-indigo-600 focus:ring-0">
                            <span class="font-bold">Koordinator BP/BK</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="koordinator_keagamaan" id="edit_u_role_koordinator_keagamaan" class="edit-u-role-cb w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span>Koordinator Keagamaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-indigo-700">
                            <input type="checkbox" name="roles[]" value="wali_kelas" id="edit_u_role_wali_kelas" class="edit-u-role-cb w-3.5 h-3.5 rounded text-indigo-600 focus:ring-0">
                            <span>Wali Kelas</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-orange-700">
                            <input type="checkbox" name="roles[]" value="bk" id="edit_u_role_bk" class="edit-u-role-cb w-3.5 h-3.5 rounded text-orange-600 focus:ring-0">
                            <span>Guru BK</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-rose-700">
                            <input type="checkbox" name="roles[]" value="guru_piket" id="edit_u_role_guru_piket" class="edit-u-role-cb w-3.5 h-3.5 rounded text-rose-600 focus:ring-0">
                            <span class="font-bold">Petugas Piket</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="pengelola_perpustakaan" id="edit_u_role_pengelola_perpustakaan" class="edit-u-role-cb w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span>Pengelola Perpustakaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-cyan-700">
                            <input type="checkbox" name="roles[]" value="pembina_ekstra" id="edit_u_role_pembina_ekstra" class="edit-u-role-cb w-3.5 h-3.5 rounded text-cyan-600 focus:ring-0">
                            <span class="font-bold">Pembina Ekstrakurikuler</span>
                        </label>
                    </div>

                    @if(isset($extracurriculars) && $extracurriculars->isNotEmpty())
                    <div class="mt-2">
                        <label class="block font-bold text-slate-700 mb-1 text-[10px]">Penugasan Ekstrakurikuler (Bina Ekstra):</label>
                        <div class="grid grid-cols-2 gap-1.5 bg-white p-2 rounded-lg border border-slate-200 text-[10px] max-h-24 overflow-y-auto">
                            @foreach($extracurriculars as $ekstra)
                            <label class="flex items-center gap-1.5 cursor-pointer text-slate-700 hover:text-cyan-700">
                                <input type="checkbox" name="extracurricular_ids[]" value="{{ $ekstra->id }}" id="edit_u_ekstra_{{ $ekstra->id }}" class="edit-u-ekstra-cb w-3 h-3 rounded text-cyan-600 focus:ring-0">
                                <span class="truncate">{{ $ekstra->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Peran Tambahan Tenaga Kependidikan (12 Role) -->
                <div id="edit_user_tendik_roles_box" class="hidden space-y-2">
                    <label class="block font-bold text-slate-700 mb-1 text-[11px]">Pilih Peran Tambahan Tenaga Kependidikan (Bisa lebih dari 1 role):</label>
                    <div class="grid grid-cols-2 gap-1.5 bg-white p-2.5 rounded-lg border border-slate-200 text-[11px] max-h-48 overflow-y-auto">
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-800">
                            <input type="checkbox" name="roles[]" value="kepala_administrasi" id="edit_u_role_kepala_administrasi" class="edit-u-role-cb w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span class="font-bold">Kepala Urusan Tata Usaha</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="bendahara_bosp" id="edit_u_role_bendahara_bosp" class="edit-u-role-cb w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span class="font-bold">Bendahara BOSP</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="bendahara_bopd" id="edit_u_role_bendahara_bopd" class="edit-u-role-cb w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span class="font-bold">Bendahara BOPD</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="bendahara_gaji" id="edit_u_role_bendahara_gaji" class="edit-u-role-cb w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span class="font-bold">Bendahara Gaji</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-purple-700">
                            <input type="checkbox" name="roles[]" value="operator_dapodik" id="edit_u_role_operator_dapodik" class="edit-u-role-cb w-3.5 h-3.5 rounded text-purple-600 focus:ring-0">
                            <span class="font-bold">Operator Dapodik</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-blue-700">
                            <input type="checkbox" name="roles[]" value="ta_kesiswaan" id="edit_u_role_ta_kesiswaan" class="edit-u-role-cb w-3.5 h-3.5 rounded text-blue-600 focus:ring-0">
                            <span>Adm. Urusan Kesiswaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-violet-700">
                            <input type="checkbox" name="roles[]" value="ta_kepegawaian" id="edit_u_role_ta_kepegawaian" class="edit-u-role-cb w-3.5 h-3.5 rounded text-violet-600 focus:ring-0">
                            <span>Adm. Urusan Kepegawaian</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700">
                            <input type="checkbox" name="roles[]" value="ta_sarpras" id="edit_u_role_ta_sarpras" class="edit-u-role-cb w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                            <span>Adm. Urusan Sarpras</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-sky-700">
                            <input type="checkbox" name="roles[]" value="ta_persuratan" id="edit_u_role_ta_persuratan" class="edit-u-role-cb w-3.5 h-3.5 rounded text-sky-600 focus:ring-0">
                            <span>Adm. Urusan Persuratan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-amber-700">
                            <input type="checkbox" name="roles[]" value="kepala_perpustakaan" id="edit_u_role_tendik_kepala_perpustakaan" class="edit-u-role-cb w-3.5 h-3.5 rounded text-amber-600 focus:ring-0">
                            <span class="font-bold">Kepala Perpustakaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-emerald-700">
                            <input type="checkbox" name="roles[]" value="pengelola_perpustakaan" id="edit_u_role_tendik_pengelola_perpustakaan" class="edit-u-role-cb w-3.5 h-3.5 rounded text-emerald-600 focus:ring-0">
                            <span class="font-bold">Pengelola Perpustakaan</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-rose-700">
                            <input type="checkbox" name="roles[]" value="tu_piket" id="edit_u_role_tu_piket" class="edit-u-role-cb w-3.5 h-3.5 rounded text-rose-600 focus:ring-0">
                            <span class="font-bold">Petugas Piket</span>
                        </label>
                    </div>
                </div>

                <!-- Info Box Admin -->
                <div id="edit_user_admin_roles_box" class="hidden p-2.5 rounded-lg bg-red-50 border border-red-200 text-[11px] text-red-800 font-medium">
                    🛡️ <strong>Peran Utama Admin:</strong> Memiliki wewenang Administrator Sekolah penuh untuk mengakses seluruh modul dan konfigurasi.
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Status Akun *</label>
                <select id="editUserStatus" name="is_active" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    <option value="1">Aktif (Dapat Login)</option>
                    <option value="0">Nonaktif (Diblokir)</option>
                </select>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function handleUserPrimaryRoleChange(prefix) {
    const roleSelect = document.getElementById(prefix === 'edit' ? 'editUserPrimaryRole' : 'addUserPrimaryRole');
    const role = roleSelect ? roleSelect.value : 'guru';
    const pendidikBox = document.getElementById(prefix + '_user_pendidik_roles_box');
    const tendikBox = document.getElementById(prefix + '_user_tendik_roles_box');
    const adminBox = document.getElementById(prefix + '_user_admin_roles_box');

    if (pendidikBox) pendidikBox.classList.add('hidden');
    if (tendikBox) tendikBox.classList.add('hidden');
    if (adminBox) adminBox.classList.add('hidden');

    if (role === 'guru') {
        if (pendidikBox) pendidikBox.classList.remove('hidden');
    } else if (role === 'staff') {
        if (tendikBox) tendikBox.classList.remove('hidden');
    } else if (role === 'admin') {
        if (adminBox) adminBox.classList.remove('hidden');
    }
}

function fillFromEmployee(selectEl) {
    const selected = selectEl.options[selectEl.selectedIndex];
    if (!selected || !selected.value) return;
    const name = selected.getAttribute('data-name') || '';
    const nip = selected.getAttribute('data-nip') || '';
    const email = selected.getAttribute('data-email') || '';
    const phone = selected.getAttribute('data-phone') || '';
    
    if (name) document.getElementById('addUserName').value = name;

    // Utamakan NIP jika ada, jika tidak ada NIP gunakan nama depan yang bersih
    if (nip && nip !== '' && nip !== 'null' && nip !== '-') {
        document.getElementById('addUserUsername').value = nip;
        document.getElementById('addUserNip').value = nip;
    } else if (name) {
        // Ambil nama depan (hilangkan gelar)
        const cleanFirst = name.trim().split(/[\s,.]+/)[0].toLowerCase().replace(/[^a-z0-9]/g, '');
        document.getElementById('addUserUsername').value = cleanFirst;
        document.getElementById('addUserNip').value = '';
    }

    if (email) document.getElementById('addUserEmail').value = email;
    if (phone) document.getElementById('addUserPhone').value = phone;
}

function openEditUserModal(data) {
    document.getElementById('formEditUser').action = data.action_url;
    document.getElementById('editUserName').value = data.name || '';
    document.getElementById('editUserUsername').value = data.username || '';
    document.getElementById('editUserNip').value = data.nip || '';
    document.getElementById('editUserEmail').value = data.email || '';
    document.getElementById('editUserPhone').value = data.phone || '';
    document.getElementById('editUserStatus').value = data.is_active ? '1' : '0';
    
    const rawRole = data.role || 'guru';
    const userRoles = Array.isArray(data.roles) ? data.roles : (data.roles ? JSON.parse(data.roles) : [rawRole]);

    let mappedPrimaryRole = 'guru';
    if (rawRole === 'admin' || userRoles.includes('admin')) {
        mappedPrimaryRole = 'admin';
    } else if (['staff', 'tenaga_administrasi', 'kepala_administrasi', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'operator_dapodik', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_sarpras', 'ta_persuratan', 'tu_piket', 'bendahara', 'operator'].includes(rawRole)) {
        mappedPrimaryRole = 'staff';
    } else {
        mappedPrimaryRole = 'guru';
    }

    document.getElementById('editUserPrimaryRole').value = mappedPrimaryRole;
    handleUserPrimaryRoleChange('edit');

    // Clear and check active role checkboxes
    document.querySelectorAll('.edit-u-role-cb').forEach(cb => cb.checked = false);
    userRoles.forEach(r => {
        const el = document.getElementById('edit_u_role_' + r);
        if (el) el.checked = true;
        if (r === 'kepala_perpustakaan') {
            const el2 = document.getElementById('edit_u_role_tendik_kepala_perpustakaan');
            if (el2) el2.checked = true;
        }
        if (r === 'pengelola_perpustakaan') {
            const el3 = document.getElementById('edit_u_role_tendik_pengelola_perpustakaan');
            if (el3) el3.checked = true;
        }
        if (r === 'bendahara') {
            const bosp = document.getElementById('edit_u_role_bendahara_bosp');
            if (bosp) bosp.checked = true;
        }
        if (r === 'operator') {
            const op = document.getElementById('edit_u_role_operator_dapodik');
            if (op) op.checked = true;
        }
        if (r === 'petugas_piket') {
            const gp = document.getElementById('edit_u_role_guru_piket');
            if (gp) gp.checked = true;
            const tp = document.getElementById('edit_u_role_tu_piket');
            if (tp) tp.checked = true;
        }
    });

    document.getElementById('editUserModal').classList.remove('hidden');
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}
</script>
@endsection
