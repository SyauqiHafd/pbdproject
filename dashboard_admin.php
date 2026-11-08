<?php
require_once __DIR__ . '/bootstrap.php';

$auth = new AuthController();
$user = $auth->requireRole('admin');          
$barang   = new Barang();
$satuan   = new Satuan();
$vendor   = new Vendor();
$margin   = new Margin();
$pengguna = new Pengguna();
$role     = new Role();

$pengadaan  = new Pengadaan();
$penerimaan = new Penerimaan();
$retur      = new Retur();
$penjualan  = new Penjualan();
$kartustok  = new KartuStok();

$home = (new DashboardController($user))->admin();

$transaksiModals = true;   

$modals = ['barang' => $barang, 'satuan' => $satuan, 'vendor' => $vendor,
           'margin' => $margin, 'pengguna' => $pengguna, 'role' => $role];

$menus = [
    ['heading' => null, 'items' => [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
    ]],
    ['heading' => 'Menu Utama', 'items' => [
        ['key' => 'barang', 'label' => 'Barang',           'icon' => 'box'],
        ['key' => 'satuan', 'label' => 'Satuan',           'icon' => 'ruler'],
        ['key' => 'vendor', 'label' => 'Vendor',           'icon' => 'building'],
        ['key' => 'margin', 'label' => 'Margin Penjualan', 'icon' => 'percent'],
    ]],
    ['heading' => 'Transaksi', 'items' => [
        ['key' => 'transaksi', 'label' => 'Transaksi', 'icon' => 'clipboard', 'children' => [
            ['key' => 'pengadaan',  'label' => 'Pengadaan'],
            ['key' => 'penerimaan', 'label' => 'Penerimaan'],
            ['key' => 'retur',      'label' => 'Retur'],
            ['key' => 'penjualan',  'label' => 'Penjualan'],
            ['key' => 'kartustok',  'label' => 'Kartu Stok'],
        ]],
    ]],
    ['heading' => 'Pengaturan', 'items' => [
        ['key' => 'pengguna', 'label' => 'Pengguna', 'icon' => 'users'],
        ['key' => 'role',     'label' => 'Role',     'icon' => 'shield'],
    ]],
];

$keys   = ['dashboard', 'barang', 'satuan', 'vendor', 'margin', 'pengguna', 'role',
          'pengadaan', 'penerimaan', 'retur', 'penjualan', 'kartustok'];
$active = in_array($_GET['menu'] ?? '', $keys, true) ? $_GET['menu'] : 'dashboard';

$pageTitle         = 'Dashboard Admin';
$roleChip          = 'Admin';
$searchPlaceholder = 'Cari...';

include __DIR__ . '/View/layout/header.php';
?>

      <section id="menu-dashboard" class="menu-page<?= $active === 'dashboard' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/partials/home.php'; ?>
      </section>

      <section id="menu-barang" class="menu-page<?= $active === 'barang' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/barang.php'; ?>
      </section>

      <section id="menu-satuan" class="menu-page<?= $active === 'satuan' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/satuan.php'; ?>
      </section>

      <section id="menu-vendor" class="menu-page<?= $active === 'vendor' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/vendor.php'; ?>
      </section>

      <section id="menu-margin" class="menu-page<?= $active === 'margin' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/margin.php'; ?>
      </section>

      <section id="menu-pengadaan" class="menu-page<?= $active === 'pengadaan' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/pengadaan.php'; ?>
      </section>

      <section id="menu-penerimaan" class="menu-page<?= $active === 'penerimaan' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/penerimaan.php'; ?>
      </section>

      <section id="menu-retur" class="menu-page<?= $active === 'retur' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/retur.php'; ?>
      </section>

      <section id="menu-penjualan" class="menu-page<?= $active === 'penjualan' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/penjualan.php'; ?>
      </section>

      <section id="menu-kartustok" class="menu-page<?= $active === 'kartustok' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/kartustok.php'; ?>
      </section>

      <section id="menu-pengguna" class="menu-page<?= $active === 'pengguna' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/pengguna.php'; ?>
      </section>

      <section id="menu-role" class="menu-page<?= $active === 'role' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/role.php'; ?>
      </section>

<?php include __DIR__ . '/View/layout/footer.php'; ?>
