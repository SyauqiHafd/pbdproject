<?php
class Satuan extends Model
{
    protected $table  = 'skala';
    protected $entity = 'satuan';
    protected $label  = 'Satuan';

    public function fields(): array
    {
        return [
            ['name' => 'name',   'label' => 'Nama Satuan', 'type' => 'text', 'required' => true, 'max' => 45],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true,
             'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
        ];
    }

    /** [id => nama] untuk pilihan satuan pada form Barang */
    public function options(): array
    {
        $out = [];
        foreach ($this->all() as $row) {
            $out[$row['id']] = $row['name'];
        }
        return $out;
    }
}
