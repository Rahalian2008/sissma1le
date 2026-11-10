@extends('layouts.app')

@section('title', 'Tambah Buku Baru - Perpustakaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
        <div>
            <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Entri Katalog Pustaka</div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">Tambah Koleksi Buku Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Standar metadata Nasional Perpustakaan (SNP) dan Klasifikasi DDC</p>
        </div>
        <a href="{{ route('library.books.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-200 transition">
            &larr; Kembali ke Katalog
        </a>
    </div>

    <form action="{{ route('library.books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Card 1: Informasi Utama Buku -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2 flex items-center gap-2">
                <span>📘 Informasi Bibliografis Buku</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Induk / Barcode Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="book_code" value="{{ old('book_code', 'LIB-' . date('Y') . '-' . strtoupper(substr(uniqid(), -4))) }}" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor ISBN / ISSN</label>
                    <input type="text" name="isbn" value="{{ old('isbn') }}" placeholder="Contoh: 978-602-000-000-0" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Buku Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Masukkan judul buku secara lengkap..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Penulis / Pengarang <span class="text-rose-500">*</span></label>
                    <input type="text" name="author" value="{{ old('author') }}" required placeholder="Nama penulis..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Penerbit</label>
                    <input type="text" name="publisher" value="{{ old('publisher') }}" placeholder="Nama penerbit..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Terbit</label>
                    <input type="number" name="publish_year" value="{{ old('publish_year', date('Y')) }}" min="1900" max="{{ date('Y') + 1 }}" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Klasifikasi DDC</label>
                    <select name="category_id" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                        <option value="">Pilih Klasifikasi DDC</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                DDC {{ $cat->ddc_code }} - {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Panggil (Call Number)</label>
                    <input type="text" name="call_number" value="{{ old('call_number') }}" placeholder="Contoh: 530 BAM f" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Lokasi Rak Fisik</label>
                    <input type="text" name="rack_location" value="{{ old('rack_location') }}" placeholder="Contoh: Rak MIPA 01 - Baris 2" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Koleksi <span class="text-rose-500">*</span></label>
                    <select name="book_type" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                        <option value="fisik" {{ old('book_type') == 'fisik' ? 'selected' : '' }}>Buku Fisik</option>
                        <option value="digital" {{ old('book_type') == 'digital' ? 'selected' : '' }}>Buku Digital (E-Book Flip HTML)</option>
                        <option value="hybrid" {{ old('book_type') == 'hybrid' ? 'selected' : '' }}>Hybrid (Fisik & Digital)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jumlah Eksemplar Stok <span class="text-rose-500">*</span></label>
                    <input type="number" name="total_copies" value="{{ old('total_copies', 1) }}" min="1" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sumber Pengadaan</label>
                    <input type="text" name="source" value="{{ old('source', 'Pengadaan Sekolah') }}" placeholder="BOS / Hibah / Kemendikbud" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sinopsis / Ringkasan Buku</label>
                <textarea name="description" rows="3" placeholder="Tuliskan sinopsis atau deskripsi isi buku..." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-slate-900 dark:text-white">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Card 2: Berkas & E-Book Digital Flip HTML -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2 flex items-center gap-2">
                <span>✨ Berkas Cover & E-Book Digital Flip HTML</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Foto Sampul (Cover Buku)</label>
                    <input type="file" name="cover_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WebP (Maksimal 5MB)</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">File PDF Asli Buku (Opsional)</label>
                    <input type="file" name="digital_file" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                    <p class="text-[11px] text-slate-400 mt-1">Format: PDF (Maksimal 50MB)</p>
                </div>
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_digital_flip" value="1" {{ old('is_digital_flip', true) ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Aktifkan Pembaca Interaktif 3D Flip HTML</span>
                </label>
                <p class="text-[11px] text-slate-500 ml-6">Jika diaktifkan, pembaca dapat menikmati buku dengan efek membalik lembaran kertas (Page Flip 3D) di web browser.</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('library.books.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-100 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition cursor-pointer">
                Simpan Buku ke Perpustakaan
            </button>
        </div>
    </form>
</div>
@endsection

