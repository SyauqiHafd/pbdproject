<?php
$data  = $penjualan->all();
$lines = $penjualan->detailMap();
?>
<?php include __DIR__ . '/partials/db_belum_siap.php'; ?>
<div class="page-head">
  <div class="page-head-title"><h1>Penjualan</h1><span class="badge"><?= count($data) ?> data</span></div>
  <button type="button" class="btn btn-dark" data-open-form="modal-penjualan"><?= Icon::svg('plus', 16) ?>Buat Penjualan</button>
</div>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr>
        <th>No.</th><th>Tanggal</th><th>Kasir</th><th class="num">Margin</th>
        <th class="num">Subtotal</th><th class="num">PPN</th><th class="num">Total</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <?php
        $detail = Html::detailAttr(
            'Penjualan #' . $row['idpenjualan'],
            $row['tanggal'] . ', kasir ' . $row['username'],
            $lines[$row['idpenjualan']] ?? [],
            [['Subtotal', Label::rupiah($row['subtotal_nilai'])], ['PPN', Label::rupiah($row['ppn'])], ['Total', Label::rupiah($row['total_nilai'])]]
        );
      ?>
      <tr>
        <td><?= Html::e($row['idpenjualan']) ?></td>
        <td><?= Html::e($row['tanggal']) ?></td>
        <td><?= Label::dash($row['username']) ?></td>
        <td class="num"><?= $row['persen'] === null ? '-' : number_format((float) $row['persen'], 2) . '%' ?></td>
        <td class="num"><?= Label::angka($row['subtotal_nilai']) ?></td>
        <td class="num"><?= Label::angka($row['ppn']) ?></td>
        <td class="num"><?= Label::angka($row['total_nilai']) ?></td>
        <td class="actions"><button type="button" class="link-btn" data-detail="<?= $detail ?>">Detail</button></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="8">Belum ada penjualan. Klik “Buat Penjualan” untuk mencatat penjualan pertama.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
