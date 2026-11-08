<?php

class Penjualan extends Model
{
    protected $table      = 'penjualan';
    protected $primaryKey = 'idpenjualan';
    protected $orderDir   = 'DESC';
    protected $entity     = 'penjualan';
    protected $label      = 'Penjualan';

    public function all()
    {
        $sql = "SELECT p.idpenjualan, p.created_at AS tanggal, p.subtotal_nilai, p.ppn, p.total_nilai,
                       u.username, m.persen
                  FROM penjualan p
                  LEFT JOIN `user` u            ON u.iduser = p.iduser
                  LEFT JOIN margin_penjualan m  ON m.idmargin_penjualan = p.idmargin_penjualan
                 ORDER BY p.idpenjualan DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detailMap(): array
    {
        $sql = "SELECT d.penjualan_idpenjualan AS idpenjualan, b.nama, d.jumlah,
                       d.harga_satuan AS harga, d.subtotal
                  FROM detail_penjualan d
                  JOIN barang b ON b.idbarang = d.idbarang
                 ORDER BY d.iddetail_penjualan";
        $map = [];
        foreach ($this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $map[$row['idpenjualan']][] = $row;
        }
        return $map;
    }

    public function barangTersedia(): array
    {
        $sql = "SELECT * FROM (
                    SELECT b.idbarang, b.nama, s.nama_satuan,
                           fn_stok_barang(b.idbarang) AS stok,
                           fn_harga_jual(b.idbarang, fn_id_margin_aktif()) AS harga_jual
                      FROM barang b
                      LEFT JOIN satuan s ON s.idsatuan = b.idsatuan
                     WHERE b.status = 1
                ) t
                WHERE t.stok > 0
                ORDER BY t.nama";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marginAktif()
    {
        $v = $this->conn->query('SELECT persen FROM margin_penjualan WHERE idmargin_penjualan = fn_id_margin_aktif()')->fetchColumn();
        return $v === false ? null : (float) $v;
    }

    public function countToday(): int
    {
        return (int) $this->conn->query('SELECT COUNT(*) FROM penjualan WHERE DATE(created_at) = CURDATE()')->fetchColumn();
    }

    public function buat(int $userId, array $items): int
    {
        if (!$items) {
            throw new InvalidArgumentException('Tambahkan minimal satu barang.');
        }

        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare('CALL sp_buat_penjualan(?, @id_penjualan)');
            $stmt->execute([$userId]);
            $stmt->closeCursor();
            $id = (int) $this->conn->query('SELECT @id_penjualan')->fetchColumn();

            $add = $this->conn->prepare('CALL sp_tambah_detail_penjualan(?, ?, ?)');
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

    public function nota(int $id): ?array
    {
        $h = $this->conn->prepare(
            "SELECT p.idpenjualan, p.created_at, p.subtotal_nilai, p.ppn, p.total_nilai, m.persen, u.username
               FROM penjualan p
               LEFT JOIN margin_penjualan m ON m.idmargin_penjualan = p.idmargin_penjualan
               LEFT JOIN `user` u           ON u.iduser = p.iduser
              WHERE p.idpenjualan = ?"
        );
        $h->execute([$id]);
        $header = $h->fetch(PDO::FETCH_ASSOC);
        if (!$header) {
            return null;
        }

        $l = $this->conn->prepare(
            "SELECT b.nama, d.jumlah, d.harga_satuan AS harga, d.subtotal
               FROM detail_penjualan d
               JOIN barang b ON b.idbarang = d.idbarang
              WHERE d.penjualan_idpenjualan = ?
              ORDER BY d.iddetail_penjualan"
        );
        $l->execute([$id]);

        $q = $this->conn->prepare('SELECT COALESCE(SUM(jumlah), 0) FROM detail_penjualan WHERE penjualan_idpenjualan = ?');
        $q->execute([$id]);

        return [
            'header'        => $header,
            'lines'         => $l->fetchAll(PDO::FETCH_ASSOC),
            'jumlah_barang' => (int) $q->fetchColumn(),
        ];
    }
}
