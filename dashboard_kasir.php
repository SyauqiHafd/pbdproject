<?php
require_once __DIR__ . '/bootstrap.php';

$auth = new AuthController();
$user = $auth->requireRole('staff');         

$barang = new Barang();
$margin = new Margin();

$penerimaan = new Penerimaan();
$penjualan  = new Penjualan();
$kartustok  = new KartuStok();
$transaksiModals = true;

$home = (new DashboardController($user))->staff();

$modals = [];   

$menus = [
    ['heading' => null, 'items' => [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
    ]],
    ['heading' => 'Menu Kasir', 'items' => [
        ['key' => 'barang', 'label' => 'Barang',           'icon' => 'box'],
        ['key' => 'margin', 'label' => 'Margin Penjualan', 'icon' => 'percent'],
    ]],
    ['heading' => 'Transaksi', 'items' => [
        ['key' => 'penerimaan', 'label' => 'Penerimaan', 'icon' => 'inbox'],
        ['key' => 'penjualan',  'label' => 'Penjualan',  'icon' => 'cart'],
        ['key' => 'kartustok',  'label' => 'Kartu Stok', 'icon' => 'layers'],
    ]],
];

$keys   = ['dashboard', 'barang', 'margin', 'penerimaan', 'penjualan', 'kartustok'];
$active = in_array($_GET['menu'] ?? '', $keys, true) ? $_GET['menu'] : 'dashboard';

$pageTitle         = 'Dashboard Staff';
$roleChip          = 'Staff: ' . ucfirst($user['username']);
$searchPlaceholder = 'Cari barang...';

include __DIR__ . '/View/layout/header.php';
?>

      <section id="menu-dashboard" class="menu-page<?= $active === 'dashboard' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/partials/home.php'; ?>
      </section>

      <section id="menu-barang" class="menu-page<?= $active === 'barang' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/barangk.php'; ?>
      </section>

      <section id="menu-margin" class="menu-page<?= $active === 'margin' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/margink.php'; ?>
      </section>

      <section id="menu-penerimaan" class="menu-page<?= $active === 'penerimaan' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/penerimaan.php'; ?>
      </section>

      <section id="menu-penjualan" class="menu-page<?= $active === 'penjualan' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/penjualan.php'; ?>
      </section>

      <section id="menu-kartustok" class="menu-page<?= $active === 'kartustok' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/kartustok.php'; ?>
      </section>

<?php include __DIR__ . '/View/layout/footer.php'; ?>
