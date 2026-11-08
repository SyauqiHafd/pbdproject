<?php
class KartuStok extends Model
{
    protected $table      = 'kartu_stok';
    protected $primaryKey = 'idkartu_stok';
    protected $orderDir   = 'DESC';
    protected $entity     = 'kartustok';
    protected $label      = 'Kartu Stok';

    public function all()
    {
        $sql = "SELECT k.idkartu_stok, k.jenis_transaksi, k.masuk, k.keluar, k.stock,
                       k.created_at, k.idtransaksi, k.idbarang, b.nama
                  FROM kartu_stok k
                  LEFT JOIN barang b ON b.idbarang = k.idbarang
                 ORDER BY k.idkartu_stok DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function stokSaatIni(): array
    {
        $sql = "SELECT b.idbarang, b.nama, s.nama_satuan, fn_stok_barang(b.idbarang) AS stok
                  FROM barang b
                  LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
                 WHERE b.status = 1
                 ORDER BY b.nama";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
