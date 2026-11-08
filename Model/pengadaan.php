<?php

class Pengadaan extends Model
{
    protected $table      = 'pengadaan';
    protected $primaryKey = 'idpengadaan';
    protected $orderDir   = 'DESC';
    protected $entity     = 'pengadaan';
    protected $label      = 'Pengadaan';

    public function all()
    {
        $sql = "SELECT p.idpengadaan, p.`timestamp` AS tanggal, p.status,
                       p.subtotal_nilai, p.ppn, p.total_nilai,
                       v.nama_vendor, v.badan_hukum, u.username
                  FROM pengadaan p
                  LEFT JOIN vendor v ON v.idvendor = p.vendor_idvendor
                  LEFT JOIN `user` u ON u.iduser   = p.user_iduser
                 ORDER BY p.idpengadaan DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detailMap(): array
    {
        $sql = "SELECT d.idpengadaan, b.nama, d.jumlah, d.harga_satuan AS harga, d.sub_total AS subtotal
                  FROM detail_pengadaan d
                  JOIN barang b ON b.idbarang = d.idbarang
                 ORDER BY d.iddetail_pengadaan";
        $map = [];
        foreach ($this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $map[$row['idpengadaan']][] = $row;
        }
        return $map;
    }

    public function vendorOptions(): array
    {
        $sql = "SELECT idvendor, nama_vendor, badan_hukum FROM vendor WHERE status = 'Y' ORDER BY nama_vendor";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function barangOptions(): array
    {
        $sql = "SELECT b.idbarang, b.nama, b.harga, s.nama_satuan
                  FROM barang b
                  LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
                 WHERE b.status = 1
                 ORDER BY b.nama";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buat(int $userId, int $vendorId, array $items): int
    {
        if (!$items) {
            throw new InvalidArgumentException('Tambahkan minimal satu barang.');
        }

        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare('CALL sp_buat_pengadaan(?, ?, @id_pengadaan)');
            $stmt->execute([$userId, $vendorId]);
            $stmt->closeCursor();
            $id = (int) $this->conn->query('SELECT @id_pengadaan')->fetchColumn();

            $add = $this->conn->prepare('CALL sp_tambah_detail_pengadaan(?, ?, ?)');
            foreach ($items as [$idbarang, $jumlah]) {
                $add->execute([$id, $idbarang, $jumlah]);
                $add->closeCursor();
            }

            $this->conn->commit();
            return $id;
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }
}
