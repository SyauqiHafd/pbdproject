<?php
class Vendor extends Model
{
    protected $table  = 'companies';
    protected $entity = 'vendor';
    protected $label  = 'Vendor';

    protected $writeTable = 'vendor';
    protected $writePk    = ['idvendor', 'id'];
    protected $columnMap  = [
        'nama_corp' => ['nama_vendor', 'nama_corp', 'nama'],
        'badan'     => ['badan_hukum', 'badan'],
        'status'    => ['status'],
    ];

    public function fields(): array
    {
        return [
            ['name' => 'nama_corp', 'label' => 'Nama Vendor', 'type' => 'text', 'required' => true, 'max' => 100],
            ['name' => 'badan',     'label' => 'Badan Hukum', 'type' => 'select', 'required' => true,
             'options' => ['P' => 'PT', 'C' => 'CV', 'U' => 'UD', 'D' => 'Toko']],
            ['name' => 'status',    'label' => 'Status', 'type' => 'select', 'required' => true,
             'options' => ['Y' => 'Aktif', 'N' => 'Tidak Aktif']],
        ];
    }
}
