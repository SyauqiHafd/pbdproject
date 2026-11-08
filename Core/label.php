<?php
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

    public static function status(bool $aktif, string $on = 'Aktif', string $off = 'Nonaktif'): string
    {
        return $aktif
            ? '<span class="pill pill-on">' . Icon::svg('check', 15) . Html::e($on) . '</span>'
            : '<span class="pill pill-off">' . Icon::svg('slash', 15) . Html::e($off) . '</span>';
    }

    public static function angka($v): string
    {
        return ($v === null || $v === '') ? '-' : number_format((float) $v, 0, ',', '.');
    }

    public static function dash($v): string
    {
        return ($v === null || $v === '') ? '-' : Html::e($v);
    }

    public static function statusTransaksi($kode): string
    {
        switch ($kode) {
            case 'P':
                return '<span class="pill pill-warn">' . Icon::svg('clock', 15) . 'Menunggu</span>';
            case 'A':
                return self::status(true, 'Diterima');
            case 'C':
                return self::status(false, 'Dibatalkan');
        }
        return Html::e($kode);
    }

    public static function jenisStok($kode): string
    {
        return ['T' => 'Penerimaan', 'J' => 'Penjualan', 'R' => 'Retur'][$kode] ?? (string) $kode;
    }

    public static function rupiah($v): string
    {
        return 'Rp ' . number_format((float) $v, 0, ',', '.');
    }
}
