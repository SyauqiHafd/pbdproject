<?php
$menunggu = $penerimaan->menunggu();
$riwayat  = $penerimaan->all();
?>
<?php include __DIR__ . '/partials/db_belum_siap.php'; ?>
<div class="page-head">
  <div class="page-head-title"><h1>Penerimaan</h1><span class="badge"><?= count($menunggu) ?> menunggu</span></div>
</div>

<h2 class="section-title">Menunggu diterima</h2>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr><th>Pengadaan</th><th>Tanggal</th><th>Vendor</th><th>Dibuat oleh</th><th class="num">Total</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php foreach ($menunggu as $row): ?>
      <?php
        $detail = Html::detailAttr(
            'Pengadaan #' . $row['idpengadaan'],
            $row['nama_vendor'] . ', ' . $row['tanggal'],
            $row['lines'],
            [['Subtotal', Label::rupiah($row['subtotal_nilai'])], ['PPN', Label::rupiah($row['ppn'])], ['Total', Label::rupiah($row['total_nilai'])]]
        );
      ?>
      <tr>
        <td>#<?= Html::e($row['idpengadaan']) ?></td>
        <td><?= Html::e($row['tanggal']) ?></td>
        <td><?= Label::dash($row['nama_vendor']) ?></td>
        <td><?= Label::dash($row['username']) ?></td>
        <td class="num"><?= Label::angka($row['total_nilai']) ?></td>
        <td class="actions">
          <button type="button" class="link-btn" data-detail="<?= $detail ?>">Detail</button>
          <button type="button" class="link-btn" data-proses="terima" data-id="<?= Html::e($row['idpengadaan']) ?>">Terima</button>
          <button type="button" class="link-btn link-danger" data-proses="batal" data-id="<?= Html::e($row['idpengadaan']) ?>">Batalkan</button>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$menunggu): ?><tr class="empty"><td colspan="6">Tidak ada pengadaan yang menunggu diterima.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<h2 class="section-title">Riwayat penerimaan</h2>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr><th>ID</th><th>Tanggal</th><th>Pengadaan</th><th>Vendor</th><th>Diproses oleh</th><th class="num">Total</th><th>Status</th></tr>
    </thead>
    <tbody>
      <?php foreach ($riwayat as $row): ?>
      <tr>
        <td><?= Html::e($row['idpenerimaan']) ?></td>
        <td><?= Html::e($row['tanggal']) ?></td>
        <td>#<?= Html::e($row['idpengadaan']) ?></td>
        <td><?= Label::dash($row['nama_vendor']) ?></td>
        <td><?= Label::dash($row['username']) ?></td>
        <td class="num"><?= Label::angka($row['total_nilai']) ?></td>
        <td><?= Label::statusTransaksi($row['status']) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$riwayat): ?><tr class="empty"><td colspan="7">Belum ada penerimaan.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
