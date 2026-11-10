<?php

namespace App\Services;

class NumberHelper
{
    /**
     * Konversi angka numerik ke kata terbilang bahasa Indonesia.
     */
    public static function terbilang(float|int $angka): string
    {
        $angka = abs((float) $angka);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp = '';

        if ($angka < 12) {
            $temp = ' '.$huruf[(int) $angka];
        } elseif ($angka < 20) {
            $temp = self::terbilang($angka - 10).' Belas';
        } elseif ($angka < 100) {
            $temp = self::terbilang((int) ($angka / 10)).' Puluh'.self::terbilang($angka % 10);
        } elseif ($angka < 200) {
            $temp = ' Seratus'.self::terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $temp = self::terbilang((int) ($angka / 100)).' Ratus'.self::terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $temp = ' Seribu'.self::terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $temp = self::terbilang((int) ($angka / 1000)).' Ribu'.self::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $temp = self::terbilang((int) ($angka / 1000000)).' Juta'.self::terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $temp = self::terbilang((int) ($angka / 1000000000)).' Miliar'.self::terbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $temp = self::terbilang((int) ($angka / 1000000000000)).' Triliun'.self::terbilang(fmod($angka, 1000000000000));
        }

        return trim($temp);
    }
}
