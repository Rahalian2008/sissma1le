<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Collection;

class DukCalculationService
{
    /**
     * Map rank code to numeric weight for sorting (higher is higher priority).
     */
    protected static array $rankWeights = [
        'IV/e' => 170,
        'IV/d' => 160,
        'IV/c' => 150,
        'IV/b' => 140,
        'IV/a' => 130,
        'III/d' => 120,
        'III/c' => 110,
        'III/b' => 100,
        'III/a' => 90,
        'II/d' => 80,
        'II/c' => 70,
        'II/b' => 60,
        'II/a' => 50,
        'I/d' => 40,
        'I/c' => 30,
        'I/b' => 20,
        'I/a' => 10,
        'IX' => 95, // PPPK Ahli Pertama
        'X' => 105, // PPPK Ahli Muda
        'XI' => 115, // PPPK Ahli Madya
    ];

    /**
     * Map education level to numeric weight.
     */
    protected static array $educationWeights = [
        'S3' => 60,
        'S2' => 50,
        'S1' => 40,
        'D4' => 40,
        'D3' => 30,
        'D2' => 20,
        'D1' => 15,
        'SMA' => 10,
        'SMK' => 10,
    ];

    /**
     * Get sorted DUK collection of employees.
     *
     * @return Collection<int, Employee>
     */
    public static function getRankedEmployees(?string $filterType = null): Collection
    {
        $query = Employee::where('is_active', true);

        if ($filterType && $filterType !== 'all') {
            $query->where('employment_type', $filterType);
        }

        $employees = $query->get();

        return $employees->sort(function (Employee $a, Employee $b) {
            // 1. Pangkat / Golongan (descending weight)
            $rankWeightA = self::$rankWeights[$a->rank_group] ?? 0;
            $rankWeightB = self::$rankWeights[$b->rank_group] ?? 0;
            if ($rankWeightA !== $rankWeightB) {
                return $rankWeightB <=> $rankWeightA;
            }

            // 2. TMT Pangkat (ascending date: older TMT has seniority)
            if ($a->rank_tmt && $b->rank_tmt && $a->rank_tmt != $b->rank_tmt) {
                return $a->rank_tmt <=> $b->rank_tmt;
            }

            // 3. Masa kerja tahun + bulan (descending total months)
            $monthsA = ($a->years_of_service_years * 12) + $a->years_of_service_months;
            $monthsB = ($b->years_of_service_years * 12) + $b->years_of_service_months;
            if ($monthsA !== $monthsB) {
                return $monthsB <=> $monthsA;
            }

            // 4. Pendidikan (descending weight)
            $eduWeightA = self::$educationWeights[$a->education_level] ?? 0;
            $eduWeightB = self::$educationWeights[$b->education_level] ?? 0;
            if ($eduWeightA !== $eduWeightB) {
                return $eduWeightB <=> $eduWeightA;
            }

            // 5. Usia / Tanggal Lahir (older date of birth has seniority)
            if ($a->birth_date && $b->birth_date) {
                return $a->birth_date <=> $b->birth_date;
            }

            return $a->id <=> $b->id;
        })->values();
    }
}
