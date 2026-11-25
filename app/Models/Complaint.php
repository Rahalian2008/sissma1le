<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints';

    protected $fillable = [
        'ticket_number',
        'is_anonymous',
        'reporter_name',
        'reporter_phone',
        'reporter_email',
        'reporter_type',
        'category',
        'title',
        'description',
        'incident_date',
        'incident_location',
        'attachment_path',
        'attachment_name',
        'status',
        'disposition_notes',
        'internal_notes',
        'handled_by',
        'handled_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'incident_date' => 'date',
        'handled_at' => 'datetime',
    ];

    /**
     * Relasi ke user / pimpinan penindaklanjut
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Generate Nomor Tiket Pengaduan Unik (ADU-YYYYMMDD-XXXX)
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'ADU-'.Carbon::now()->format('Ymd').'-';

        do {
            $randomCode = strtoupper(Str::random(4));
            $ticketNumber = $prefix.$randomCode;
        } while (static::where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }

    /**
     * Label Kategori
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'layanan_akademik' => 'KBM, Guru & Layanan Akademik',
            'kedisiplinan_tatib' => 'Kedisiplinan & Tata Tertib Siswa',
            'bullying_perundungan' => 'Dugaan Perundungan / Bullying / Kekerasan',
            'sarana_prasarana' => 'Fasilitas & Sarana Prasarana',
            'administrasi_tu' => 'Pelayanan Administrasi & Tata Usaha',
            'integritas_pungli' => 'Integritas Layanan & Dugaan Pungli',
            'aspirasi_kemajuan' => 'Saran & Aspirasi Kemajuan Sekolah',
            default => ucfirst(str_replace('_', ' ', (string) $this->category)),
        };
    }

    /**
     * Label Tipe Pelapor
     */
    public function getReporterTypeLabelAttribute(): string
    {
        return match ($this->reporter_type) {
            'orang_tua' => 'Orang Tua / Wali Siswa',
            'siswa' => 'Siswa / Siswi',
            'alumni' => 'Alumni',
            'masyarakat' => 'Masyarakat Umum',
            'mitra' => 'Mitra / Instansi Eksternal',
            'guru_staf' => 'Guru / Tenaga Kependidikan',
            default => ucfirst(str_replace('_', ' ', (string) $this->reporter_type)),
        };
    }

    /**
     * Badge status warna Tailwind
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'MENUNGGU' => [
                'label' => 'Menunggu Telaah',
                'bg' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border-amber-300 dark:border-amber-700',
                'dot' => 'bg-amber-500',
            ],
            'DITELAAH' => [
                'label' => 'Sedang Ditelaah',
                'bg' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border-blue-300 dark:border-blue-700',
                'dot' => 'bg-blue-500',
            ],
            'DIPROSES' => [
                'label' => 'Dalam Tindak Lanjut',
                'bg' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-300 dark:border-indigo-700',
                'dot' => 'bg-indigo-500',
            ],
            'SELESAI' => [
                'label' => 'Tuntas / Selesai',
                'bg' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-300 dark:border-emerald-700',
                'dot' => 'bg-emerald-500',
            ],
            'DITOLAK' => [
                'label' => 'Ditolak / Arsip',
                'bg' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 border-rose-300 dark:border-rose-700',
                'dot' => 'bg-rose-500',
            ],
            default => [
                'label' => $this->status,
                'bg' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-300 dark:border-slate-700',
                'dot' => 'bg-slate-400',
            ],
        };
    }

    /**
     * Nama pelapor yang ditampilkan publik / aman
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonim (Identitas Dirahasiakan)';
        }

        return $this->reporter_name;
    }

    /**
     * Hitung total aduan menunggu secara aman tanpa memicu QueryException 1146 jika tabel belum dimigrasi
     */
    public static function countPendingSafe(): int
    {
        try {
            static::ensureComplaintsTableExists();

            return static::where('status', 'MENUNGGU')->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * Hitung total aduan dalam proses secara aman
     */
    public static function countInProgressSafe(): int
    {
        try {
            static::ensureComplaintsTableExists();

            return static::whereIn('status', ['DITELAAH', 'DIPROSES'])->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * Self-healing: Otomatis buat tabel complaints jika belum ada di database Hostinger/Local
     */
    public static function ensureComplaintsTableExists(): void
    {
        try {
            if (! Schema::hasTable('complaints')) {
                Schema::create('complaints', function (Blueprint $table) {
                    $table->id();
                    $table->string('ticket_number', 30)->unique();
                    $table->boolean('is_anonymous')->default(false);
                    $table->string('reporter_name', 150);
                    $table->string('reporter_phone', 30);
                    $table->string('reporter_email', 150)->nullable();
                    $table->string('reporter_type', 50)->default('masyarakat');
                    $table->string('category', 50)->default('aspirasi_kemajuan');
                    $table->string('title', 255);
                    $table->text('description');
                    $table->date('incident_date')->nullable();
                    $table->string('incident_location', 150)->nullable();
                    $table->string('attachment_path', 255)->nullable();
                    $table->string('attachment_name', 255)->nullable();
                    $table->string('status', 30)->default('MENUNGGU');
                    $table->text('disposition_notes')->nullable();
                    $table->text('internal_notes')->nullable();
                    $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
                    $table->timestamp('handled_at')->nullable();
                    $table->timestamps();

                    $table->index('ticket_number');
                    $table->index('status');
                    $table->index('category');
                    $table->index('created_at');
                });
            }
        } catch (\Throwable) {
            // Silently ignore if direct DDL fails
        }
    }
}
