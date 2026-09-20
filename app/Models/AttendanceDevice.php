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
}
