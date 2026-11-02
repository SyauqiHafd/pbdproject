<?php
/** Fungsi string multibyte yang tetap jalan walau ekstensi mbstring tidak aktif. */
class Str
{
    public static function len(string $s): int
    {
        return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : (int) preg_match_all('/./us', $s);
    }

    public static function initial(string $s): string
    {
        if ($s === '') {
            return '?';
        }
        $first = function_exists('mb_substr') ? mb_substr($s, 0, 1, 'UTF-8') : (preg_match('/./us', $s, $m) ? $m[0] : $s[0]);
        return function_exists('mb_strtoupper') ? mb_strtoupper($first, 'UTF-8') : strtoupper($first);
    }

    public static function cut(string $s, int $max): string
    {
        if (self::len($s) <= $max) {
            return $s;
        }
        return function_exists('mb_substr') ? mb_substr($s, 0, $max, 'UTF-8') : substr($s, 0, $max);
    }
}
