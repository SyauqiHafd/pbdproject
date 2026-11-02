<?php
class Role extends Model
{
    protected $table      = 'roles';
    protected $primaryKey = 'idrole';
    protected $entity     = 'role';
    protected $label      = 'Role';

    public function fields(): array
    {
        return [
            ['name' => 'nama_role', 'label' => 'Nama Role', 'type' => 'text', 'required' => true, 'max' => 100],
        ];
    }

    /** [idrole => nama_role] */
    public function options(): array
    {
        $out = [];
        foreach ($this->all() as $row) {
            $out[$row['nama_role']] = $row['nama_role'];
        }
        return $out;
    }

    /** Ubah nama role menjadi jenis akses aplikasi: 'admin', 'staff', atau null (tidak boleh login). */
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
