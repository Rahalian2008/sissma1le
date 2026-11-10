<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Kategori DDC (Dewey Decimal Classification) & Kelompok Koleksi
        Schema::create('library_categories', function (Blueprint $table) {
            $table->id();
            $table->string('ddc_code', 20)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('color', 50)->default('indigo');
            $table->string('icon', 50)->default('book');
            $table->timestamps();
        });

        // 2. Koleksi Buku Fisik & Digital (E-Book)
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->string('book_code', 50)->unique(); // Nomor Induk / Barcode
            $table->string('title', 255);
            $table->string('author', 255);
            $table->string('publisher', 255)->nullable();
            $table->integer('publish_year')->nullable();
            $table->string('isbn', 50)->nullable();
            $table->foreignId('category_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->string('ddc_code', 20)->nullable();
            $table->string('call_number', 50)->nullable(); // Nomor Panggil: 530 BAM f
            $table->string('rack_location', 100)->nullable(); // Lokasi Rak: Rak A-02
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->string('book_type', 20)->default('fisik'); // fisik, digital, hybrid
            $table->string('cover_image', 255)->nullable();
            $table->text('description')->nullable();
            $table->integer('pages_count')->nullable();
            $table->string('language', 50)->default('Indonesia');
            $table->string('source', 100)->default('Pengadaan Sekolah');
            $table->string('digital_file_path', 255)->nullable(); // PDF atau berkas e-book
            $table->boolean('is_digital_flip')->default(false); // Mode pembaca Flip HTML
            $table->json('digital_content')->nullable(); // Halaman-halaman buku digital interaktif Flip HTML
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('loans_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Data Anggota Perpustakaan (Terhubung ke Siswa, Guru, atau Umum)
        Schema::create('library_members', function (Blueprint $table) {
            $table->id();
            $table->string('member_code', 50)->unique(); // Nomor Anggota Perpustakaan
            $table->string('member_type', 20)->default('siswa'); // siswa, guru, umum
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('name', 255);
            $table->string('identity_number', 50)->nullable(); // NISN, NIP, atau NIK/KTP
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->date('joined_at');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Sirkulasi Peminjaman & Pengembalian Buku
        Schema::create('library_loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code', 50)->unique(); // Kode Transaksi Peminjaman
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Petugas
            $table->date('loan_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->string('status', 20)->default('DIPINJAM'); // DIPINJAM, KEMBALI, TERLAMBAT, HILANG, RUSAK
            $table->decimal('fine_amount', 10, 2)->default(0); // Denda keterlambatan
            $table->boolean('is_fine_paid')->default(false);
            $table->integer('extension_count')->default(0); // Jumlah perpanjangan
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Buku Tamu / Presensi Pengunjung Perpustakaan
        Schema::create('library_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_type', 20)->default('siswa'); // siswa, guru, umum
            $table->foreignId('member_id')->nullable()->constrained('library_members')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('name', 255);
            $table->string('identity_number', 50)->nullable();
            $table->string('class_or_institution', 100)->nullable();
            $table->string('purpose', 100)->default('Membaca Buku');
            $table->timestamp('visited_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_visitors');
        Schema::dropIfExists('library_loans');
        Schema::dropIfExists('library_members');
        Schema::dropIfExists('library_books');
        Schema::dropIfExists('library_categories');
    }
};
