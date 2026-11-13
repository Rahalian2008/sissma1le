<?php

namespace App\Services;

use App\Models\CbtExam;
use App\Models\SchoolSetting;

class CbtGeofenceService
{
    /**
     * Periksa apakah koordinat siswa berada di dalam radius sekolah yang diizinkan.
     *
     * @return array{
     *     is_allowed: bool,
     *     distance_meters: float,
     *     allowed_radius: int,
     *     target_lat: float,
     *     target_lng: float
     * }
     */
    public function verifyLocation(CbtExam $exam, ?float $studentLat, ?float $studentLng): array
    {
        // Jika fitur geofence dimatikan pada ujian ini, selalu lolos
        if (! $exam->enable_geofence) {
            return [
                'is_allowed' => true,
                'distance_meters' => 0.0,
                'allowed_radius' => 0,
                'target_lat' => 0.0,
                'target_lng' => 0.0,
            ];
        }

        // Ambil target koordinat sekolah (dari konfigurasi ujian atau master setting sekolah)
        $targetLat = (float) ($exam->geofence_latitude ?? SchoolSetting::get('school_lat', -7.13013342752238));
        $targetLng = (float) ($exam->geofence_longitude ?? SchoolSetting::get('school_lng', 106.7038263897621));
        $allowedRadius = (int) ($exam->geofence_radius ?? SchoolSetting::get('geofence_radius_meters', 100));

        if ($studentLat === null || $studentLng === null) {
            return [
                'is_allowed' => false,
                'distance_meters' => 999999.0,
                'allowed_radius' => $allowedRadius,
                'target_lat' => $targetLat,
                'target_lng' => $targetLng,
            ];
        }

        $distance = $this->calculateHaversineDistance($studentLat, $studentLng, $targetLat, $targetLng);

        return [
            'is_allowed' => $distance <= $allowedRadius,
            'distance_meters' => round($distance, 1),
            'allowed_radius' => $allowedRadius,
            'target_lat' => $targetLat,
            'target_lng' => $targetLng,
        ];
    }

    /**
     * Hitung jarak dua titik koordinat GPS dalam satuan meter menggunakan Haversine Formula.
     */
    public function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
