@extends('layouts.app')

@section('title', 'Identitas Sekolah')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Identitas Sekolah</h1>
            <p class="text-xs text-slate-500 mt-0.5">Informasi profil resmi, NPSN, akreditasi, dan legalitas {{ $school->name }}.</p>
        </div>
    </div>

    <form action="{{ route('master.school.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-6 space-y-6">
            <!-- Logo & Kop Surat Resmi -->
            <div class="p-5 bg-gradient-to-r from-blue-50/70 to-indigo-50/70 rounded-2xl border border-blue-100/80">
                <div class="flex items-center justify-between pb-3 border-b border-blue-200/60 mb-4">
                    <h2 class="text-sm font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Logo & Kop Surat Resmi Satuan Pendidikan</span>
                    </h2>
                    <span class="text-[11px] font-medium text-blue-700 bg-blue-100/60 px-2.5 py-0.5 rounded-full">Otomatis sinkron ke seluruh cetak dokumen</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <!-- Logo Upload Card -->
                    <div class="p-4 bg-white rounded-xl border border-blue-200/80 space-y-3 shadow-2xs flex flex-col justify-between" id="logo-card">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-blue-600 text-white inline-flex items-center justify-center text-[10px] font-bold">1</span>
                                    <span>Logo Sekolah (Ikon Aplikasi)</span>
                                </label>
                                <span class="text-[10px] text-blue-700 bg-blue-50 font-semibold px-2 py-0.5 rounded-md uppercase">Favicon & Navbar</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div id="logo-preview-box" class="w-20 h-20 rounded-xl border-2 border-dashed border-blue-300 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0 relative transition">
                                    @if ($school->has_logo)
                                        <img id="logo-preview-img" src="{{ $school->logo_url }}?v={{ time() }}" alt="Logo Sekolah" class="w-full h-full object-contain p-1.5" onerror="this.style.display='none'; document.getElementById('logo-fallback').classList.remove('hidden');">
                                        <div id="logo-fallback" class="hidden w-12 h-12 rounded-lg bg-blue-600 text-white font-bold flex items-center justify-center text-xl shadow-sm">
                                            {{ substr($school->name, 0, 1) }}
                                        </div>
                                    @else
                                        <div id="logo-fallback" class="w-12 h-12 rounded-lg bg-blue-600 text-white font-bold flex items-center justify-center text-xl shadow-sm">
                                            {{ substr($school->name, 0, 1) }}
                                        </div>
                                        <img id="logo-preview-img" src="" alt="Logo Sekolah" class="hidden w-full h-full object-contain p-1.5">
                                    @endif
                                </div>
                                <div class="flex-1 space-y-2">
                                    <input type="file" id="input-logo" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                    <div id="logo-info" class="text-[11px] text-slate-500 leading-tight">
                                        Format PNG/JPG/SVG/WebP (Maks. 2MB). Ditampilkan pada navbar, login, dan tab browser.
                                    </div>
                                    <div id="logo-new-badge" class="hidden text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded inline-block border border-emerald-200">
                                        ✓ File baru terpilih (Siap disimpan)
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($school->has_logo)
                            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between">
                                <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 hover:text-rose-700 cursor-pointer font-medium">
                                    <input type="checkbox" id="check-remove-logo" name="remove_logo" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                    <span>Hapus logo saat ini (kembali ke inisial)</span>
                                </label>
                                <a href="{{ $school->logo_url }}" target="_blank" class="text-[10px] text-blue-600 hover:underline">Lihat Gambar Asli ↗</a>
                            </div>
                        @endif
                    </div>

                    <!-- Kop Surat Upload Card -->
                    <div class="p-4 bg-white rounded-xl border border-blue-200/80 space-y-3 shadow-2xs flex flex-col justify-between" id="header-card">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-600 text-white inline-flex items-center justify-center text-[10px] font-bold">2</span>
                                    <span>Banner Kop Surat Resmi</span>
                                </label>
                                <span class="text-[10px] text-emerald-700 bg-emerald-50 font-semibold px-2 py-0.5 rounded-md uppercase">Seluruh Cetak Dokumen</span>
                            </div>
                            <div class="space-y-2">
                                <div id="header-preview-box" class="p-2 border-2 border-dashed border-emerald-300 rounded-xl bg-slate-50 overflow-hidden min-h-[58px] flex items-center justify-center relative transition">
                                    @if ($school->has_header_image)
                                        <img id="header-preview-img" src="{{ $school->header_image_url }}?v={{ time() }}" alt="Kop Surat Resmi" class="max-h-16 w-auto object-contain" onerror="this.style.display='none'; document.getElementById('header-fallback').classList.remove('hidden');">
                                        <div id="header-fallback" class="hidden text-center py-2 text-slate-400 text-xs">
                                            <span class="font-medium text-slate-600">Kop Teks Otomatis Aktif</span> (Kop gambar tidak ditemukan)
                                        </div>
                                    @else
                                        <div id="header-fallback" class="text-center py-2 text-slate-400 text-xs">
                                            <span class="font-medium text-slate-600">Belum Ada Banner Kop</span> (Menggunakan format teks dinamis)
                                        </div>
                                        <img id="header-preview-img" src="" alt="Kop Surat Resmi" class="hidden max-h-16 w-auto object-contain">
                                    @endif
                                </div>
                                <input type="file" id="input-header" name="header_image" accept="image/png,image/jpeg,image/webp" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                                <div id="header-info" class="text-[11px] text-slate-500 leading-tight">
                                    Format PNG/JPG/WebP banner persegi panjang (Maks. 4MB). Digunakan pada SPPD, Presensi, Buku Induk, BKU, dll.
                                </div>
                                <div id="header-new-badge" class="hidden text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded inline-block border border-emerald-200">
                                    ✓ Banner baru terpilih (Siap disimpan)
                                </div>
                            </div>
                        </div>
                        @if ($school->has_header_image)
                            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between">
                                <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 hover:text-rose-700 cursor-pointer font-medium">
                                    <input type="checkbox" id="check-remove-header" name="remove_header_image" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                    <span>Hapus banner kop (kembali ke kop teks resmi)</span>
                                </label>
                                <a href="{{ $school->header_image_url }}" target="_blank" class="text-[10px] text-emerald-600 hover:underline">Lihat Gambar Asli ↗</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Data Pokok Sekolah -->
            <div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4">
                    Data Pokok Sekolah
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Satuan Pendidikan *</label>
                        <input type="text" name="name" value="{{ old('name', $school->name) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Pokok Sekolah Nasional (NPSN)</label>
                        <input type="text" name="npsn" value="{{ old('npsn', $school->npsn) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Satuan Pendidikan</label>
                        <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                            <option value="Negeri" {{ $school->status === 'Negeri' ? 'selected' : '' }}>Negeri</option>
                            <option value="Swasta" {{ $school->status === 'Swasta' ? 'selected' : '' }}>Swasta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Peringkat Akreditasi</label>
                        <input type="text" name="accreditation" value="{{ old('accreditation', $school->accreditation) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Lokasi & Kontak -->
            <div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4">
                    Alamat & Kontak Resmi
                </h2>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea name="address" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">{{ old('address', $school->address) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Cabang Dinas Pendidikan (Cadisdik)</label>
                            <input type="text" name="branch_office" value="{{ old('branch_office', $school->branch_office ?? 'Cabang Dinas Pendidikan Wilayah V') }}" placeholder="Contoh: Cabang Dinas Pendidikan Wilayah V" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kabupaten/Kota (Digunakan di setiap cetak surat & dokumen)</label>
                            <input type="text" name="city" value="{{ old('city', $school->city ?? 'Kabupaten Sukabumi') }}" placeholder="Contoh: Kabupaten Sukabumi" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kecamatan</label>
                            <input type="text" name="district" value="{{ old('district', $school->district) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Provinsi</label>
                            <input type="text" name="province" value="{{ old('province', $school->province) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Pos</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $school->postal_code) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $school->phone) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $school->email) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Website</label>
                            <input type="text" name="website" value="{{ old('website', $school->website) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pejabat Sekolah -->
            <div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4">
                    Pimpinan & Pengelola
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Kepala Sekolah</label>
                        <input type="text" name="headmaster_name" value="{{ old('headmaster_name', $school->headmaster_name) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">NIP Kepala Sekolah</label>
                        <input type="text" name="headmaster_nip" value="{{ old('headmaster_nip', $school->headmaster_nip) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Ketua Komite Sekolah</label>
                        <input type="text" name="committee_head" value="{{ old('committee_head', $school->committee_head) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Bendahara Sekolah</label>
                        <input type="text" name="treasurer_name" value="{{ old('treasurer_name', $school->treasurer_name) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs">
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                Simpan Perubahan Identitas
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputLogo = document.getElementById('input-logo');
    const logoImg = document.getElementById('logo-preview-img');
    const logoFallback = document.getElementById('logo-fallback');
    const logoBox = document.getElementById('logo-preview-box');
    const logoNewBadge = document.getElementById('logo-new-badge');
    const checkRemoveLogo = document.getElementById('check-remove-logo');

    const inputHeader = document.getElementById('input-header');
    const headerImg = document.getElementById('header-preview-img');
    const headerFallback = document.getElementById('header-fallback');
    const headerBox = document.getElementById('header-preview-box');
    const headerNewBadge = document.getElementById('header-new-badge');
    const checkRemoveHeader = document.getElementById('check-remove-header');

    // Live preview Logo
    if (inputLogo) {
        inputLogo.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                alert('Peringatan: Ukuran file logo melebihi 2MB. Silakan pilih gambar yang lebih kecil.');
                inputLogo.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                logoImg.src = event.target.result;
                logoImg.classList.remove('hidden');
                logoImg.style.display = 'block';
                if (logoFallback) logoFallback.classList.add('hidden');
                if (logoNewBadge) logoNewBadge.classList.remove('hidden');
                if (logoBox) logoBox.classList.remove('opacity-40');
                if (checkRemoveLogo) checkRemoveLogo.checked = false;
            };
            reader.readAsDataURL(file);
        });
    }

    if (checkRemoveLogo) {
        checkRemoveLogo.addEventListener('change', function () {
            if (this.checked) {
                if (logoBox) logoBox.classList.add('opacity-40');
                if (logoNewBadge) logoNewBadge.classList.add('hidden');
                if (inputLogo) inputLogo.value = '';
            } else {
                if (logoBox) logoBox.classList.remove('opacity-40');
            }
        });
    }

    // Live preview Header / Kop Surat
    if (inputHeader) {
        inputHeader.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 4 * 1024 * 1024) {
                alert('Peringatan: Ukuran file banner kop surat melebihi 4MB. Silakan pilih gambar yang lebih kecil.');
                inputHeader.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                headerImg.src = event.target.result;
                headerImg.classList.remove('hidden');
                headerImg.style.display = 'block';
                if (headerFallback) headerFallback.classList.add('hidden');
                if (headerNewBadge) headerNewBadge.classList.remove('hidden');
                if (headerBox) headerBox.classList.remove('opacity-40');
                if (checkRemoveHeader) checkRemoveHeader.checked = false;
            };
            reader.readAsDataURL(file);
        });
    }

    if (checkRemoveHeader) {
        checkRemoveHeader.addEventListener('change', function () {
            if (this.checked) {
                if (headerBox) headerBox.classList.add('opacity-40');
                if (headerNewBadge) headerNewBadge.classList.add('hidden');
                if (inputHeader) inputHeader.value = '';
            } else {
                if (headerBox) headerBox.classList.remove('opacity-40');
            }
        });
    }
});
</script>
@endpush
@endsection
