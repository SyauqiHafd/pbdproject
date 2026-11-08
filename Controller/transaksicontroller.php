<?php
class TransaksiController
{
    private const AKSI = [
        'pengadaan_buat'    => ['menu' => 'pengadaan',  'role' => ['admin']],
        'penerimaan_terima' => ['menu' => 'penerimaan', 'role' => ['admin', 'staff']],
        'penerimaan_batal'  => ['menu' => 'penerimaan', 'role' => ['admin', 'staff']],
        'penjualan_buat'    => ['menu' => 'penjualan',  'role' => ['admin', 'staff']],
    ];

    public function handle(): void
    {
        $user = (new AuthController())->user();
        if (!$user) {
            header('Location: login.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->kembali($user, 'dashboard');
        }

        $aksi = (string) ($_POST['aksi'] ?? '');
        if (!isset(self::AKSI[$aksi])) {
            Session::flash('error', 'Aksi transaksi tidak dikenali.');
            $this->kembali($user, 'dashboard');
        }

        $menu = self::AKSI[$aksi]['menu'];
        if (!in_array($user['role'], self::AKSI[$aksi]['role'], true)) {
            Session::flash('error', 'Anda tidak memiliki akses untuk aksi ini.');
            $this->kembali($user, 'dashboard');
        }
        if (!Session::checkCsrf($_POST['csrf'] ?? null)) {
            Session::flash('error', 'Sesi form kedaluwarsa, silakan ulangi.');
            $this->kembali($user, $menu);
        }

        try {
            switch ($aksi) {
                case 'pengadaan_buat':
                    $this->pengadaanBuat($user);
                    break;
                case 'penerimaan_terima':
                case 'penerimaan_batal':
                    $this->penerimaanProses($user, $aksi === 'penerimaan_terima');
                    break;
                case 'penjualan_buat':
                    $this->penjualanBuat($user);
                    break;
            }
        } catch (InvalidArgumentException $e) {
            Session::flash('error', $e->getMessage());
        } catch (PDOException $e) {
            Session::flash('error', $this->pesanDatabase($e));
        }

        $this->kembali($user, $menu);
    }


    private function pengadaanBuat(array $user): void
    {
        $vendor = trim((string) ($_POST['vendor'] ?? ''));
        if (!ctype_digit($vendor)) {
            throw new InvalidArgumentException('Pilih vendor terlebih dahulu.');
        }
        $id = (new Pengadaan())->buat((int) $user['id'], (int) $vendor, $this->items());
        Session::flash('success', "Pengadaan #{$id} dibuat dan menunggu penerimaan.");
    }

    private function penerimaanProses(array $user, bool $terima): void
    {
        $id = trim((string) ($_POST['id'] ?? ''));
        if (!ctype_digit($id)) {
            throw new InvalidArgumentException('Pengadaan tidak valid.');
        }
        (new Penerimaan())->proses((int) $id, (int) $user['id'], $terima ? 'A' : 'C');

        Session::flash('success', $terima
            ? "Pengadaan #{$id} diterima. Stok barang sudah masuk ke kartu stok."
            : "Pengadaan #{$id} dibatalkan.");
    }

    private function penjualanBuat(array $user): void
    {
        $model = new Penjualan();
        $id    = $model->buat((int) $user['id'], $this->items());
        $nota  = $model->nota($id);

        Session::set('nota', $id);   
        if ($nota) {
            Session::flash('success', sprintf(
                'Penjualan #%d berhasil: %s barang, total %s.',
                $id,
                Label::angka($nota['jumlah_barang']),
                Label::rupiah($nota['header']['total_nilai'])
            ));
        } else {
            Session::flash('success', "Penjualan #{$id} berhasil.");
        }
    }


    private function items(): array
    {
        $barang = $_POST['barang'] ?? [];
        $jumlah = $_POST['jumlah'] ?? [];
        if (!is_array($barang) || !is_array($jumlah)) {
            return [];
        }

        $items = [];
        foreach ($barang as $i => $idb) {
            $idb = trim((string) $idb);
            $qty = trim((string) ($jumlah[$i] ?? ''));
            if ($idb === '' && $qty === '') {
                continue;
            }
            if (!ctype_digit($idb) || !ctype_digit($qty) || (int) $qty < 1) {
                throw new InvalidArgumentException('Setiap baris wajib berisi barang dan jumlah (bilangan bulat, minimal 1).');
            }
            $items[] = [(int) $idb, (int) $qty];
        }
        return $items;
    }

    private function kembali(array $user, string $menu): void
    {
        $tujuan = $menu === 'dashboard' ? '' : '?menu=' . urlencode($menu);
        header('Location: ' . AuthController::homeFor($user) . $tujuan);
        exit;
    }

    private function pesanDatabase(PDOException $e): string
    {
        $info = $e->errorInfo ?? [];
        if (($info[0] ?? '') === '45000') {
            return (string) ($info[2] ?? 'Transaksi ditolak.');
        }
        switch ($info[1] ?? 0) {
            case 1305:
                return 'Function/procedure/trigger belum terpasang.';
            case 1451:
                return 'Data tidak dapat diubah karena masih dipakai oleh data lain.';
            case 1452:
                return 'Data yang dipilih (referensi) tidak valid.';
            case 1062:
                return 'Data sudah ada (duplikat). Coba ulangi.';
        }
        return 'Gagal memproses transaksi: ' . Str::cut($e->getMessage(), 200);
    }
}
