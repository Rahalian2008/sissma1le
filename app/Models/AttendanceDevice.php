<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_code',
        'device_name',
        'device_type',
        'location_name',
        'ip_address',
        'status',
        'last_ping_at',
        'secret_key',
    ];

    protected function casts(): array
    {
        return [
            'last_ping_at' => 'datetime',
        ];
    }

    public function rfidLogs(): HasMany
    {
        return $this->hasMany(AttendanceRfidLog::class, 'device_id');
    }

    public function scopeRfid($query)
    {
        return $query->where('device_type', 'rfid');
    }

    public function scopeBarcode($query)
    {
        return $query->whereIn('device_type', ['barcode', 'qr', 'qrcode', 'BARCODE', 'QR', 'QRCODE']);
    }

    public function scopeQr($query)
    {
        return $query->whereIn('device_type', ['qr', 'qrcode', 'barcode', 'QR', 'QRCODE', 'BARCODE']);
    }

    public function scopeFaceRecognition($query)
    {
        return $query->whereIn('device_type', ['face', 'face_recognition', 'FACE', 'FACE_RECOGNITION']);
    }

    public function scopeFace($query)
    {
        return $this->scopeFaceRecognition($query);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['ONLINE', '1', 'TRUE', 'AKTIF', 'active']);
    }
}
