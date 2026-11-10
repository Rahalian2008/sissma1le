<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingNote extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
            'attendees_count' => 'integer',
        ];
    }

    /**
     * Generate unique meeting notula number (e.g. NOT/2026/001)
     */
    public static function generateMeetingNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('meeting_date', $year)->count() + 1;

        return "NOT/{$year}/".str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }
}
