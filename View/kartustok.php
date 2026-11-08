<?php
$stok = Model::transaksiSiap() ? $kartustok->stokSaatIni() : [];
$data = $kartustok->all();
?>
<?php include __DIR__ . '/partials/db_belum_siap.php'; ?>
<div class="page-head">
  <div class="page-head-title"><h1>Kartu Stok</h1><span class="badge"><?= count($data) ?> mutasi</span></div>
</div>

<h2 class="section-title">Stok saat ini</h2>
<div class="tbl-wrap">
  <table class="tbl">
    <thead><tr><th>Barang</th><th>Satuan</th><th class="num">Stok</th></tr></thead>
    <tbody>
      <?php foreach ($stok as $row): ?>
      <tr>
        <td><?= Html::e($row['nama']) ?></td>
        <td><?= Label::dash($row['nama_satuan']) ?></td>
        <td class="num"><?= Label::angka($row['stok']) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$stok): ?><tr class="empty"><td colspan="3">Belum ada barang aktif.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<h2 class="section-title">Riwayat mutasi</h2>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr><th>No.</th><th>Tanggal</th><th>Barang</th><th>Jenis</th><th>Transaksi</th><th class="num">Masuk</th><th class="num">Keluar</th><th class="num">Stok</th></tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <tr>
        <td><?= Html::e($row['idkartu_stok']) ?></td>
        <td><?= Html::e($row['created_at']) ?></td>
        <td><?= Label::dash($row['nama']) ?></td>
        <td><?= Html::e(Label::jenisStok($row['jenis_transaksi'])) ?></td>
        <td>#<?= Html::e($row['idtransaksi']) ?></td>
        <td class="num"><?= Label::angka($row['masuk']) ?></td>
        <td class="num"><?= Label::angka($row['keluar']) ?></td>
        <td class="num"><?= Label::angka($row['stock']) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="8">Belum ada mutasi stok. Stok bertambah saat pengadaan diterima.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
