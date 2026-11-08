<?php
class DashboardController
{
    private $user;

    public function __construct(array $user)
    {
        $this->user = $user;
    }

    public function admin(): array
    {
        $barang   = new Barang();
        $satuan   = new Satuan();
        $vendor   = new Vendor();
        $margin   = new Margin();
        $pengguna = new Pengguna();
        $role     = new Role();

        $stats = [
            ['label' => 'Total Barang',    'value' => $barang->count(),        'icon' => 'box',     'tone' => 'pink'],
            ['label' => 'Total Vendor',    'value' => $vendor->count(),        'icon' => 'building', 'tone' => 'blend'],
            ['label' => 'Total Pengguna',  'value' => $pengguna->count(),      'icon' => 'users',   'tone' => 'lilac'],
            ['label' => 'Margin Aktif',    'value' => $margin->countActive(),  'icon' => 'percent', 'tone' => 'rose'],
        ];

        $bars = [
            ['Barang',   $barang->count()],
            ['Satuan',   $satuan->count()],
            ['Vendor',   $vendor->count()],
            ['Margin',   $margin->count()],
            ['Pengguna', $pengguna->count()],
            ['Role',     $role->count()],
        ];

        $roles = $role->options();
        $list  = [];
        foreach (array_slice($pengguna->all(), 0, 4) as $row) {
            $list[] = [
                'title' => $row['username'],
                'sub'   => $roles[$row['role']] ?? $row['role'],
            ];
        }

        return [
            'title'  => 'Dashboard',
            'stats'  => $stats,
            'chart'  => $this->chart('Ringkasan Data', 'Jumlah data per view', $bars),
            'panel'  => [
                'title'  => 'Pengguna',
                'list'   => $list,
                'action' => ['label' => '+ Tambah Pengguna', 'menu' => 'pengguna', 'add' => 'pengguna'],
                'more'   => 'pengguna',
            ],
        ];
    }

    public function staff(): array
    {
        $barang = new Barang();
        $margin = new Margin();
        $jual   = new Penjualan();

        $current = $margin->currentPercent();
        $stats = [
            ['label' => 'Jumlah Barang Aktif',      'value' => $barang->countActive(),                   'icon' => 'box',     'tone' => 'pink'],
            ['label' => 'Margin Berlaku Hari Ini',  'value' => $current === null ? '-' : number_format($current, 2) . '%', 'icon' => 'percent', 'tone' => 'blend'],
            ['label' => 'Transaksi Hari Ini',       'value' => $jual->countToday(),                      'icon' => 'receipt', 'tone' => 'lilac'],
        ];

        $bars = [
            ['Barang',       $barang->count()],
            ['Barang Aktif', $barang->countActive()],
            ['Margin',       $margin->count()],
            ['Margin Aktif', $margin->countActive()],
        ];

        $list = [];
        foreach (array_slice(array_reverse($margin->all()), 0, 4) as $row) {
            $list[] = [
                'title' => number_format($row['persen'], 2) . '%',
                'sub'   => ($row['status'] == 1 ? 'Aktif' : 'Nonaktif') . ' · ' . $row['tglbuat'],
            ];
        }

        return [
            'title' => 'Dashboard Kasir',
            'stats' => $stats,
            'chart' => $this->chart('Ringkasan Data', 'Jumlah data per view', $bars),
            'panel' => [
                'title'  => 'Margin Penjualan',
                'list'   => $list,
                'action' => ['label' => 'Lihat Margin', 'menu' => 'margin', 'add' => null],
                'more'   => 'margin',
            ],
        ];
    }

    private function chart(string $title, string $subtitle, array $bars): array
    {
        $values  = array_column($bars, 1);
        $maxVal  = $values ? max($values) : 0;
        $axisMax = max(4, (int) (ceil($maxVal / 4) * 4));

        $items = [];
        foreach ($bars as [$label, $value]) {
            $items[] = [
                'label'  => $label,
                'value'  => $value,
                'height' => $value > 0 ? round($value / $axisMax * 100, 2) : 0,
                'is_max' => $maxVal > 0 && $value === $maxVal,
            ];
        }

        $ticks = [];
        for ($i = 4; $i >= 0; $i--) {
            $ticks[] = (int) round($axisMax * $i / 4);
        }

        return [
            'title'    => $title,
            'subtitle' => $subtitle,
            'total'    => array_sum($values),
            'items'    => $items,
            'ticks'    => $ticks,
            'maxPct'   => $maxVal > 0 ? round($maxVal / $axisMax * 100, 2) : 0,
        ];
    }
}
