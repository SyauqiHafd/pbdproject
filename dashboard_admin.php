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

$home = (new DashboardController($user))->admin();

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
    ['heading' => 'Pengaturan', 'items' => [
        ['key' => 'pengguna', 'label' => 'Pengguna', 'icon' => 'users'],
        ['key' => 'role',     'label' => 'Role',     'icon' => 'shield'],
    ]],
    ['heading' => 'Transaksi', 'items' => [
        ['key' => 'pengadaan', 'label' => 'Pengadaan',           'icon' => 'receipt'],
        ['key' => 'penerimaan', 'label' => 'Penerimaan',           'icon' => 'inbox'],
        ['key' => 'penjualaan', 'label' => 'Penjualaan',           'icon' => 'shop'],
        ['key' => 'retur', 'label' => 'Retur',           'icon' => 'rewind'],
        ['key' => 'kartustok', 'label' => 'Kartu stok',           'icon' => 'folder']
    ]]
];

$keys   = ['dashboard', 'barang', 'satuan', 'vendor', 'margin', 'pengguna', 'role'];
$active = in_array($_GET['menu'] ?? '', $keys, true) ? $_GET['menu'] : 'dashboard';

$pageTitle         = 'Dashboard Admin';
$roleChip          = 'Admin';
$searchPlaceholder = 'Cari...';

include __DIR__ . '/View/layout/header.php';
?>

      <!-- ---------- HALAMAN DASHBOARD (ringkasan) ---------- -->
      <section id="menu-dashboard" class="menu-page<?= $active === 'dashboard' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/partials/home.php'; ?>
      </section>

      <!-- ---------- HALAMAN BARANG ---------- -->
      <section id="menu-barang" class="menu-page<?= $active === 'barang' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/barang.php'; ?>
      </section>

      <!-- ---------- HALAMAN SATUAN ---------- -->
      <section id="menu-satuan" class="menu-page<?= $active === 'satuan' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/satuan.php'; ?>
      </section>

      <!-- ---------- HALAMAN VENDOR ---------- -->
      <section id="menu-vendor" class="menu-page<?= $active === 'vendor' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/vendor.php'; ?>
      </section>

      <!-- ---------- HALAMAN MARGIN PENJUALAN ---------- -->
      <section id="menu-margin" class="menu-page<?= $active === 'margin' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/margin.php'; ?>
      </section>

      <!-- ---------- HALAMAN PENGGUNA ---------- -->
      <section id="menu-pengguna" class="menu-page<?= $active === 'pengguna' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/pengguna.php'; ?>
      </section>

      <!-- ---------- HALAMAN ROLE ---------- -->
      <section id="menu-role" class="menu-page<?= $active === 'role' ? '' : ' hidden' ?>">
        <?php include __DIR__ . '/View/role.php'; ?>
      </section>

<?php include __DIR__ . '/View/layout/footer.php'; ?>
