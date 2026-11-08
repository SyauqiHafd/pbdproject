<?php
class Role extends Model
{
    protected $table      = 'roles';
    protected $primaryKey = 'idrole';
    protected $entity     = 'role';
    protected $label      = 'Role';

    protected $writeTable = 'role';
    protected $writePk    = ['idrole', 'id'];
    protected $columnMap  = [
        'nama_role' => ['nama_role', 'nama'],
    ];

    public function fields(): array
    {
        return [
            ['name' => 'nama_role', 'label' => 'Nama Role', 'type' => 'text', 'required' => true, 'max' => 100],
        ];
    }

    public function options(): array
    {
        $out = [];
        foreach ($this->all() as $row) {
            $out[$row['idrole']] = $row['nama_role'];
        }
        return $out;
    }

    public function names(): array
    {
        $out = [];
        foreach ($this->all() as $row) {
            $out[$row['nama_role']] = $row['nama_role'];
        }
        return $out;
    }

    public static function kind($namaRole): ?string
    {
        $n = strtolower((string) $namaRole);
        if (strpos($n, 'admin') !== false) {
            return 'admin';
        }
        if (strpos($n, 'staff') !== false || strpos($n, 'staf') !== false || strpos($n, 'kasir') !== false) {
            return 'staff';
        }
        return null;
    }
}
