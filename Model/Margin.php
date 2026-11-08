<?php
class Margin extends Model
{
    protected $table  = 'margins';
    protected $entity = 'margin';
    protected $label  = 'Margin';

    protected $writeTable = 'margin_penjualan';
    protected $writePk    = ['idmargin_penjualan', 'id'];
    protected $columnMap  = [
        'persen'    => ['persen'],
        'status'    => ['status'],
        'pengguna'  => ['iduser', 'pengguna'],
        'tglbuat'   => ['created_at', 'tglbuat'],
        'tglupdate' => ['updated_at', 'tglupdate'],
    ];

    public function fields(): array
    {
        return [
            ['name' => 'persen', 'label' => 'Margin (%)', 'type' => 'number', 'required' => true,
             'min' => 0, 'max' => 100, 'step' => '0.01', 'cast' => 'float'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true,
             'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
        ];
    }

    protected function prepare(string $mode, array $clean, array $ctx, $id): array
    {
        $clean['pengguna'] = (int) ($ctx['user_id'] ?? 0);
        $raw = $mode === 'create' ? ['tglbuat' => 'NOW()'] : ['tglupdate' => 'NOW()'];
        return [$clean, $raw];
    }

    public function countActive(): int
    {
        return $this->count('status = 1');
    }

    public function currentPercent()
    {
        $stmt = $this->conn->query("SELECT persen FROM {$this->table} WHERE status = 1 ORDER BY tglbuat DESC, id DESC LIMIT 1");
        $v = $stmt->fetchColumn();
        return $v === false ? null : (float) $v;
    }
}
