<?php
/** Pengubah kode -> teks (sama seperti fungsi label_jenis & label_badan di kode awal). */
class Label
{
    public static function jenis($kode): string
    {
        return ['S' => 'Solid', 'L' => 'Liquid', 'G' => 'Gas'][$kode] ?? (string) $kode;
    }

    public static function badan($kode): string
    {
        return ['P' => 'PT', 'C' => 'CV', 'U' => 'UD', 'D' => 'Toko'][$kode] ?? (string) $kode;
    }

    /** Pil status bergaya dashboard (teks tetap sama: Aktif / Nonaktif / Tidak Aktif). */
    public static function status(bool $aktif, string $on = 'Aktif', string $off = 'Nonaktif'): string
    {
        return $aktif
            ? '<span class="pill pill-on">' . Icon::svg('check', 15) . Html::e($on) . '</span>'
            : '<span class="pill pill-off">' . Icon::svg('slash', 15) . Html::e($off) . '</span>';
    }
}
