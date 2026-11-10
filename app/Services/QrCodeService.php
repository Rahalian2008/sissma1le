<?php

namespace App\Services;

class QrCodeService
{
    /**
     * Generate an inline SVG or Data URI for a given payload text.
     */
    public static function svg(string $payload, int $size = 180): string
    {
        // Encode URL/text safely
        $encoded = htmlspecialchars($payload, ENT_QUOTES, 'UTF-8');
        $hash = md5($payload);

        // Generate deterministic 21x21 pseudo-matrix based on hash & payload for visual barcode
        $dim = 25;
        $modules = array_fill(0, $dim, array_fill(0, $dim, false));

        // Draw 3 corner finder patterns (7x7)
        self::drawFinderPattern($modules, 0, 0);
        self::drawFinderPattern($modules, $dim - 7, 0);
        self::drawFinderPattern($modules, 0, $dim - 7);

        // Timing patterns
        for ($i = 8; $i < $dim - 8; $i++) {
            $modules[6][$i] = ($i % 2 === 0);
            $modules[$i][6] = ($i % 2 === 0);
        }

        // Deterministic bit fill based on payload hash
        $bytes = unpack('C*', md5($payload, true).sha1($payload, true));
        $byteIndex = 1;
        $totalBytes = count($bytes);

        for ($r = 0; $r < $dim; $r++) {
            for ($c = 0; $c < $dim; $c++) {
                // skip finder patterns
                if (($r < 8 && $c < 8) || ($r < 8 && $c >= $dim - 8) || ($r >= $dim - 8 && $c < 8)) {
                    continue;
                }
                if ($r === 6 || $c === 6) {
                    continue;
                }

                $byte = $bytes[($byteIndex % $totalBytes) + 1] ?? 0;
                $bit = ($byte >> ($c % 8)) & 1;
                $modules[$r][$c] = ($bit === 1);
                $byteIndex++;
            }
        }

        $rects = '';
        $cellSize = 8;
        $viewBoxSize = $dim * $cellSize;

        for ($r = 0; $r < $dim; $r++) {
            for ($c = 0; $c < $dim; $c++) {
                if ($modules[$r][$c]) {
                    $x = $c * $cellSize;
                    $y = $r * $cellSize;
                    $rects .= "<rect x='{$x}' y='{$y}' width='{$cellSize}' height='{$cellSize}' fill='#1e293b'/>";
                }
            }
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$viewBoxSize} {$viewBoxSize}" width="{$size}" height="{$size}" class="inline-block bg-white p-2 rounded shadow-sm border border-slate-200" title="QR: {$encoded}">
    <rect width="100%" height="100%" fill="#ffffff"/>
    {$rects}
</svg>
SVG;
    }

    private static function drawFinderPattern(array &$modules, int $row, int $col): void
    {
        for ($r = 0; $r < 7; $r++) {
            for ($c = 0; $c < 7; $c++) {
                $isBorder = ($r === 0 || $r === 6 || $c === 0 || $c === 6);
                $isCenter = ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4);
                $modules[$row + $r][$col + $c] = ($isBorder || $isCenter);
            }
        }
    }
}
