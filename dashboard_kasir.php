<?php
require_once __DIR__ . '/bootstrap.php';

$auth = new AuthController();
$user = $auth->requireRole('staff');          

$barang = new Barang();
$margin = new Margin();

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
];

$keys   = ['dashboard', 'barang', 'margin'];
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

<?php include __DIR__ . '/View/layout/footer.php'; ?>
