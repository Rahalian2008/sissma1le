@extends('layouts.app')

@section('title', 'Manajemen Buku Tamu Terpadu')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Buku Tamu Digital Terpadu</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan kunjungan tamu berbasis selfie, identitas lengkap, dan monitoring waktu kunjungan di lingkungan sekolah.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('guests.report.print') }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition shadow-xs">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Rekap Buku Tamu
            </a>
            <a href="{{ route('guests.kiosk') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-500/20 transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Buka Kiosk Layar Tamu
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium">Tamu Hari Ini</div>
                <div class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $stats['today_total'] }} Orang</div>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium">Sedang di Sekolah</div>
                <div class="text-xl font-extrabold text-amber-600 mt-0.5">{{ $stats['active_in_school'] }} Tamu</div>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium">Kunjungan Dinas Jabar</div>
                <div class="text-xl font-extrabold text-indigo-700 mt-0.5">{{ $stats['dinas_jabar'] }}</div>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium">Orang Tua / Wali Siswa</div>
                <div class="text-xl font-extrabold text-emerald-700 mt-0.5">{{ $stats['orang_tua'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('guests.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tamu, instansi, tiket, atau HP..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <input type="date" name="date" value="{{ request('date', (!request()->has('filter_all') ? date('Y-m-d') : '')) }}" 
                       onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
            </div>
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="bertemu" {{ request('status') === 'bertemu' ? 'selected' : '' }}>Sedang Bertemu</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div>
                <select name="department" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Tujuan Bagian</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-5 flex items-center justify-between text-[11px] text-slate-500 pt-1">
                <div>
                    @if(!request()->has('filter_all'))
                        <span>Menampilkan tamu per tanggal <strong>{{ request('date', date('d/m/Y')) }}</strong>.</span>
                        <a href="{{ route('guests.index', ['filter_all' => 1]) }}" class="text-indigo-600 font-semibold hover:underline ml-1">Tampilkan Seluruh Riwayat</a>
                    @else
                        <span>Menampilkan <strong>seluruh riwayat buku tamu</strong>.</span>
                        <a href="{{ route('guests.index') }}" class="text-indigo-600 font-semibold hover:underline ml-1">Kembali ke Hari Ini</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Guests Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5">Selfie</th>
                        <th class="px-4 py-3.5">No. Tiket</th>
                        <th class="px-4 py-3.5">Nama & Kontak Tamu</th>
                        <th class="px-4 py-3.5">Instansi / Asal</th>
                        <th class="px-4 py-3.5">Tujuan & Keperluan</th>
                        <th class="px-4 py-3.5">Waktu Kunjungan</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($guests as $g)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3">
                                <img src="{{ $g->selfie_url }}" alt="Foto {{ $g->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-2xs" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'><rect width=\'100\' height=\'100\' rx=\'20\' fill=\'%236366f1\'/><text x=\'50%\' y=\'55%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23ffffff\' font-family=\'sans-serif\' font-size=\'38\' font-weight=\'bold\'>{{ strtoupper(substr($g->name, 0, 2)) }}</text></svg>';">
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-indigo-700 whitespace-nowrap">
                                <a href="{{ route('guests.show', $g) }}" class="hover:underline">
                                    {{ $g->ticket_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $g->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $g->identity_type }}: {{ $g->identity_number }} &bull; WA: {{ $g->phone }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $g->institution }}</div>
                                <div class="text-[10px] text-slate-500">{{ $g->institution_type }} ({{ $g->number_of_guests }} org)</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-indigo-700">{{ $g->destination_department }}</div>
                                <div class="text-[11px] text-slate-600 line-clamp-1" title="{{ $g->purpose }} - {{ $g->purpose_detail }}">{{ $g->purpose }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">{{ $g->check_in_at?->format('H:i') }} WIB</div>
                                <div class="text-[10px] text-slate-400">
                                    @if($g->check_out_at)
                                        Pulang: {{ $g->check_out_at?->format('H:i') }} ({{ $g->duration_formatted }})
                                    @else
                                        <span class="text-amber-600 font-semibold">Sedang Aktif ({{ $g->duration_formatted }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    @if($g->status === 'selesai') bg-slate-100 text-slate-700
                                    @elseif($g->status === 'bertemu') bg-emerald-100 text-emerald-800
                                    @elseif($g->status === 'menunggu') bg-amber-100 text-amber-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ $g->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('letters.incoming.create', ['guest_id' => $g->id]) }}" class="inline-flex items-center px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-lg transition" title="Registrasikan sebagai Surat Masuk">
                                    + Surat Masuk
                                </a>
                                <a href="{{ route('guests.show', $g) }}" class="inline-flex items-center px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg transition" title="Lihat Profil Tamu">
                                    Detail
                                </a>
                                <a href="{{ route('guests.print-card', $g->ticket_number) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition" title="Cetak ID Card">
                                    Badge
                                </a>
                                <button type="button" onclick="openEditGuestModal({{ json_encode($g) }})" class="inline-flex items-center px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-lg transition cursor-pointer" title="Edit Data Tamu">
                                    Edit
                                </button>
                                @if(!$g->isCheckedOut())
                                    <form action="{{ route('guests.status', $g) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit" class="inline-flex items-center px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-lg transition cursor-pointer" title="Check-out Selesai">
                                            Selesai
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('guests.destroy', $g) }}" method="POST" onsubmit="return confirm('Hapus data kunjungan tamu ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg transition cursor-pointer" title="Hapus Data">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                Tidak ada data kunjungan tamu pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($guests->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $guests->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Edit Buku Tamu -->
    <div id="modal-edit-guest" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 my-8 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Edit Data Buku Tamu</h3>
                    <p class="text-xs text-slate-500 font-mono" id="edit-ticket-display"></p>
                </div>
                <button type="button" onclick="closeEditGuestModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="form-edit-guest" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Tamu <span class="text-rose-500">*</span></label>
                            <input type="text" id="edit-guest-name" name="name" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <select id="edit-guest-gender" name="gender" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Identitas (KTP/SIM/dll) <span class="text-rose-500">*</span></label>
                            <div class="flex gap-2">
                                <select id="edit-guest-identity-type" name="identity_type" required class="w-1/3 px-2 py-2 border border-slate-300 rounded-xl text-xs">
                                    <option value="KTP">KTP</option>
                                    <option value="SIM">SIM</option>
                                    <option value="NIP">NIP</option>
                                    <option value="Paspor">Paspor</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <input type="text" id="edit-guest-identity-number" name="identity_number" required class="w-2/3 px-3 py-2 border border-slate-300 rounded-xl">
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                            <input type="text" id="edit-guest-phone" name="phone" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Asal Instansi / Lembaga <span class="text-rose-500">*</span></label>
                            <input type="text" id="edit-guest-institution" name="institution" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kategori Instansi <span class="text-rose-500">*</span></label>
                            <select id="edit-guest-institution-type" name="institution_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                                @foreach($institutionTypes as $itype)
                                    <option value="{{ $itype }}">{{ $itype }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Ditujukan Kepada <span class="text-rose-500">*</span></label>
                            <select id="edit-guest-department" name="destination_department" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Pejabat/Guru Dituju</label>
                            <input type="text" id="edit-guest-person-name" name="destination_person_name" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kategori Keperluan <span class="text-rose-500">*</span></label>
                            <select id="edit-guest-purpose" name="purpose" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                                @foreach($purposes as $purp)
                                    <option value="{{ $purp }}">{{ $purp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jumlah Tamu (Orang) <span class="text-rose-500">*</span></label>
                            <input type="number" id="edit-guest-count" name="number_of_guests" min="1" max="100" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Rincian Keperluan <span class="text-rose-500">*</span></label>
                        <textarea id="edit-guest-purpose-detail" name="purpose_detail" rows="2" required class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Status Kunjungan <span class="text-rose-500">*</span></label>
                            <select id="edit-guest-status" name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                                <option value="menunggu">Menunggu</option>
                                <option value="bertemu">Bertemu</option>
                                <option value="selesai">Selesai</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kendaraan</label>
                            <input type="text" id="edit-guest-vehicle" name="vehicle_type" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Plat Nomor</label>
                            <input type="text" id="edit-guest-plate" name="vehicle_license_plate" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                        <textarea id="edit-guest-notes" name="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditGuestModal()" class="px-4 py-2 border border-slate-300 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-sm cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditGuestModal(g) {
            document.getElementById('edit-ticket-display').innerText = 'Tiket: ' + g.ticket_number;
            document.getElementById('form-edit-guest').action = "{{ url('guests') }}/" + g.id;
            document.getElementById('edit-guest-name').value = g.name || '';
            document.getElementById('edit-guest-gender').value = g.gender || 'Laki-laki';
            document.getElementById('edit-guest-identity-type').value = g.identity_type || 'KTP';
            document.getElementById('edit-guest-identity-number').value = g.identity_number || '';
            document.getElementById('edit-guest-phone').value = g.phone || '';
            document.getElementById('edit-guest-institution').value = g.institution || '';
            document.getElementById('edit-guest-institution-type').value = g.institution_type || '';
            document.getElementById('edit-guest-department').value = g.destination_department || '';
            document.getElementById('edit-guest-person-name').value = g.destination_person_name || '';
            document.getElementById('edit-guest-purpose').value = g.purpose || '';
            document.getElementById('edit-guest-purpose-detail').value = g.purpose_detail || '';
            document.getElementById('edit-guest-count').value = g.number_of_guests || 1;
            document.getElementById('edit-guest-status').value = g.status || 'menunggu';
            document.getElementById('edit-guest-vehicle').value = g.vehicle_type || '';
            document.getElementById('edit-guest-plate').value = g.vehicle_license_plate || '';
            document.getElementById('edit-guest-notes').value = g.notes || '';
            
            document.getElementById('modal-edit-guest').classList.remove('hidden');
        }

        function closeEditGuestModal() {
            document.getElementById('modal-edit-guest').classList.add('hidden');
        }
    </script>
    @endpush
</div>
@endsection
