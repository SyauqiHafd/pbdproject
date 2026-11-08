<?php
class Penerimaan extends Model
{
    protected $table      = 'penerimaan';
    protected $primaryKey = 'idpenerimaan';
    protected $orderDir   = 'DESC';
    protected $entity     = 'penerimaan';
    protected $label      = 'Penerimaan';

    public function all()
    {
        $sql = "SELECT n.idpenerimaan, n.created_at AS tanggal, n.status, n.idpengadaan,
                       g.total_nilai, v.nama_vendor, u.username
                  FROM penerimaan n
                  LEFT JOIN pengadaan g ON g.idpengadaan = n.idpengadaan
                  LEFT JOIN vendor v    ON v.idvendor    = g.vendor_idvendor
                  LEFT JOIN `user` u    ON u.iduser      = n.iduser
                 ORDER BY n.idpenerimaan DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function menunggu(): array
    {
        $sql = "SELECT g.idpengadaan, g.`timestamp` AS tanggal, g.subtotal_nilai, g.ppn, g.total_nilai,
                       v.nama_vendor, u.username
                  FROM pengadaan g
                  LEFT JOIN vendor v ON v.idvendor = g.vendor_idvendor
                  LEFT JOIN `user` u ON u.iduser   = g.user_iduser
                 WHERE g.status = 'P'
                 ORDER BY g.idpengadaan DESC";
        $rows  = $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $lines = (new Pengadaan())->detailMap();
        foreach ($rows as &$row) {
            $row['lines'] = $lines[$row['idpengadaan']] ?? [];
        }
        unset($row);
        return $rows;
    }

    public function proses(int $idpengadaan, int $userId, string $aksi): int
    {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare('CALL sp_proses_penerimaan(?, ?, ?, @id_penerimaan)');
            $stmt->execute([$idpengadaan, $userId, $aksi]);
            $stmt->closeCursor();
            $id = (int) $this->conn->query('SELECT @id_penerimaan')->fetchColumn();

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
