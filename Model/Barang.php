<?php
class Barang extends Model
{
    protected $table  = 'products';
    protected $entity = 'barang';
    protected $label  = 'Barang';

    public function fields(): array
    {
        return [
            ['name' => 'name',   'label' => 'Nama Barang', 'type' => 'text', 'required' => true, 'max' => 100],
            ['name' => 'jenis',  'label' => 'Jenis', 'type' => 'select', 'required' => true,
             'options' => ['S' => 'Solid', 'L' => 'Liquid', 'G' => 'Gas']],
            ['name' => 'satuan', 'label' => 'Satuan', 'type' => 'select', 'required' => true,
             'options' => (new Satuan())->options()],
            ['name' => 'harga',  'label' => 'Harga', 'type' => 'number', 'required' => true,
             'min' => 0, 'step' => 1, 'cast' => 'int'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true,
             'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
        ];
    }

    public function countActive(): int
    {
        return $this->count('status = 1');
    }
}
