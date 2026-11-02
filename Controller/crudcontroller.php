<?php
/** Menangani tambah / ubah / hapus dari modal. Hanya role admin. */
class CrudController
{
    private $entities = [
        'barang'   => 'Barang',
        'satuan'   => 'Satuan',
        'vendor'   => 'Vendor',
        'margin'   => 'Margin',
        'pengguna' => 'Pengguna',
        'role'     => 'Role',
    ];

    public function handle(): void
    {
        $user = (new AuthController())->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->back('dashboard');
        }

        $entity = $_POST['entity'] ?? '';
        if (!isset($this->entities[$entity])) {
            Session::flash('error', 'Data tidak dikenali.');
            $this->back('dashboard');
        }
        if (!Session::checkCsrf($_POST['csrf'] ?? null)) {
            Session::flash('error', 'Sesi form kedaluwarsa, silakan ulangi.');
            $this->back($entity);
        }

        $action = $_POST['action'] ?? '';
        $id     = trim((string) ($_POST['id'] ?? ''));
        $class  = $this->entities[$entity];
        $model  = new $class();
        $ctx    = ['user_id' => $user['id']];

        try {
            if ($action === 'create') {
                $model->save(null, $_POST, $ctx);
                Session::flash('success', $model->label() . ' berhasil ditambahkan.');
            } elseif ($action === 'update' && ctype_digit($id)) {
                $model->save($id, $_POST, $ctx);
                Session::flash('success', $model->label() . ' berhasil diperbarui.');
            } elseif ($action === 'delete' && ctype_digit($id)) {
                $model->remove($id, $ctx);
                Session::flash('success', $model->label() . ' berhasil dihapus.');
            } else {
                Session::flash('error', 'Permintaan tidak valid.');
            }
        } catch (InvalidArgumentException $e) {
            Session::flash('error', $e->getMessage());
        } catch (PDOException $e) {
            Session::flash('error', $this->friendly($e, $action));
        }

        $this->back($entity);
    }

    private function back(string $menu): void
    {
        header('Location: dashboard_admin.php?menu=' . urlencode($menu));
        exit;
    }

    private function friendly(PDOException $e, string $action): string
    {
        switch ($e->errorInfo[1] ?? 0) {
            case 1451:
                return 'Data tidak dapat dihapus karena masih dipakai oleh data lain.';
            case 1452:
                return 'Data yang dipilih (referensi) tidak valid.';
            case 1062:
                return 'Data sudah ada (duplikat).';
            case 1288: case 1471: case 1347: case 1423: case 1393:
                return 'View ini tidak bisa ditulis langsung. Isi properti $writeTable pada class Model terkait dengan nama tabel aslinya.';
            case 1054:
                return 'Kolom tidak ditemukan: ' . $e->getMessage();
            case 1406:
                return 'Isi data terlalu panjang untuk kolom di database.';
        }
        return 'Gagal memproses data: ' . Str::cut($e->getMessage(), 200);
    }
}
