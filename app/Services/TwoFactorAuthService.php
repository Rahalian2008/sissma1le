<?php

namespace App\Services;

class TwoFactorAuthService
{
    private static string $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a cryptographically secure Base32 secret key for TOTP.
     */
    public static function generateSecretKey(int $length = 16): string
    {
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::$base32Chars[random_int(0, 31)];
        }

        return $secret;
    }

    /**
     * Decode a Base32 string into its raw binary representation.
     */
    public static function base32Decode(string $b32): string
    {
        $b32 = strtoupper(rtrim($b32, '='));
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $val = strpos(self::$base32Chars, $b32[$i]);
            if ($val === false) {
                continue;
            }
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }

    /**
     * Calculate 6-digit TOTP code conforming to RFC 6238 / RFC 4226.
     */
    public static function calculateCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp = $timestamp ?? time();
        $counter = (int) floor($timestamp / 30);

        // Counter as 8-byte big-endian binary string
        $binTime = pack('N*', 0).pack('N*', $counter);
        $binKey = self::base32Decode($secret);

        $hash = hash_hmac('sha1', $binTime, $binKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncatedHash = substr($hash, $offset, 4);

        $unpacked = unpack('N', $truncatedHash);
        $value = $unpacked[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a 6-digit TOTP code against a Base32 secret key with time drift tolerance.
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $cleanCode = preg_replace('/\s+/', '', trim($code));
        if (strlen($cleanCode) !== 6 || ! ctype_digit($cleanCode)) {
            return false;
        }

        $currentTime = time();
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculated = self::calculateCode($secret, $currentTime + ($i * 30));
            if (hash_equals($calculated, $cleanCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate an otpauth:// URI recognized by Google Authenticator and compatible apps.
     */
    public static function getOtpAuthUri(string $appName, string $accountName, string $secret): string
    {
        $encodedApp = rawurlencode($appName);
        $encodedAccount = rawurlencode($accountName);

        return "otpauth://totp/{$encodedApp}:{$encodedAccount}?secret={$secret}&issuer={$encodedApp}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Format a secret key into spaced 4-character chunks for human readability.
     */
    public static function formatSecretKey(string $secret): string
    {
        return trim(chunk_split(strtoupper($secret), 4, ' '));
    }

    /**
     * Generate human-friendly, high-entropy single-use recovery codes.
     */
    public static function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $part1 = strtolower(bin2hex(random_bytes(2)));
            $part2 = strtolower(bin2hex(random_bytes(2)));
            $codes[] = "{$part1}-{$part2}";
        }

        return $codes;
    }
}
