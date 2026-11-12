<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Guest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Guest $guest) {
            if (empty($guest->ticket_number)) {
                $guest->ticket_number = static::generateTicketNumber();
            }
            if (empty($guest->identity_number)) {
                $guest->identity_number = '-';
            }
            if (empty($guest->phone)) {
                $guest->phone = '-';
            }
            if (empty($guest->institution)) {
                $guest->institution = 'Masyarakat Umum';
            }
            if (empty($guest->destination_department)) {
                $guest->destination_department = 'Tata Usaha';
            }
            if (empty($guest->purpose)) {
                $guest->purpose = 'Kunjungan';
            }
            if (empty($guest->check_in_at)) {
                $guest->check_in_at = Carbon::now('Asia/Jakarta');
            }
            if (empty($guest->number_of_guests)) {
                $guest->number_of_guests = 1;
            }
            if (empty($guest->status)) {
                $guest->status = 'menunggu';
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'number_of_guests' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_user_id');
    }

    /**
     * Generate Nomor Tiket Tamu Unik (contoh: TM-20260925-0001)
     */
    public static function generateTicketNumber(): string
    {
        $datePrefix = date('Ymd');
        $todayCount = self::whereDate('check_in_at', Carbon::today())->count() + 1;

        return 'TM-'.$datePrefix.'-'.str_pad((string) $todayCount, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung durasi kunjungan
     */
    public function getDurationFormattedAttribute(): string
    {
        if (! $this->check_in_at) {
            return '-';
        }

        $endTime = $this->check_out_at ?? now();
        $diffMinutes = $this->check_in_at->diffInMinutes($endTime);

        if ($diffMinutes < 60) {
            return "{$diffMinutes} menit";
        }

        $hours = floor($diffMinutes / 60);
        $minutes = $diffMinutes % 60;

        return "{$hours} jam {$minutes} mnt";
    }

    public function getPhoneNumberAttribute(): ?string
    {
        return $this->attributes['phone'] ?? null;
    }

    public function setPhoneNumberAttribute(?string $value): void
    {
        $this->attributes['phone'] = $value;
    }

    public function getDestinationPersonAttribute(): ?string
    {
        return $this->attributes['destination_person_name'] ?? null;
    }

    public function setDestinationPersonAttribute(?string $value): void
    {
        $this->attributes['destination_person_name'] = $value;
    }

    public function getSelfieUrlAttribute(): string
    {
        if ($this->selfie_path) {
            if (str_starts_with($this->selfie_path, 'data:image/') || str_starts_with($this->selfie_path, 'http://') || str_starts_with($this->selfie_path, 'https://')) {
                return $this->selfie_path;
            }
            if (Storage::disk('public')->exists($this->selfie_path)) {
                return asset('storage/'.$this->selfie_path);
            }
            if (file_exists(public_path($this->selfie_path))) {
                return asset($this->selfie_path);
            }
        }

        // SVG Avatar Fallback with initials
        $initials = strtoupper(substr($this->name, 0, 2));

        return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'><rect width='100' height='100' rx='50' fill='%236366f1'/><text x='50%' y='55%' dominant-baseline='middle' text-anchor='middle' fill='%23ffffff' font-family='sans-serif' font-size='38' font-weight='bold'>{$initials}</text></svg>";
    }

    public function getSignatureUrlAttribute(): ?string
    {
        if ($this->signature_path) {
            if (str_starts_with($this->signature_path, 'data:image/') || str_starts_with($this->signature_path, 'http://') || str_starts_with($this->signature_path, 'https://')) {
                return $this->signature_path;
            }
            if (Storage::disk('public')->exists($this->signature_path)) {
                return asset('storage/'.$this->signature_path);
            }
            if (file_exists(public_path($this->signature_path))) {
                return asset($this->signature_path);
            }
        }

        return null;
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in_at', Carbon::today());
    }

    public function scopeCheckedIn($query)
    {
        return $query->whereIn('status', ['menunggu', 'bertemu', 'checked_in'])->whereNull('check_out_at');
    }

    public function isCheckedOut(): bool
    {
        return $this->status === 'selesai' || $this->status === 'checked_out' || ! is_null($this->check_out_at);
    }
}
