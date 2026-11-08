<?php
$data  = $pengadaan->all();
$lines = $pengadaan->detailMap();
?>
<?php include __DIR__ . '/partials/db_belum_siap.php'; ?>
<div class="page-head">
  <div class="page-head-title"><h1>Pengadaan</h1><span class="badge"><?= count($data) ?> data</span></div>
  <button type="button" class="btn btn-dark" data-open-form="modal-pengadaan"><?= Icon::svg('plus', 16) ?>Buat Pengadaan</button>
</div>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr>
        <th>No.</th><th>Tanggal</th><th>Vendor</th><th>Dibuat oleh</th>
        <th class="num">Subtotal</th><th class="num">PPN</th><th class="num">Total</th>
        <th>Status</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <?php
        $detail = Html::detailAttr(
            'Pengadaan #' . $row['idpengadaan'],
            Label::badan($row['badan_hukum']) . ' ' . $row['nama_vendor'] . ', ' . $row['tanggal'],
            $lines[$row['idpengadaan']] ?? [],
            [['Subtotal', Label::rupiah($row['subtotal_nilai'])], ['PPN', Label::rupiah($row['ppn'])], ['Total', Label::rupiah($row['total_nilai'])]]
        );
      ?>
      <tr>
        <td><?= Html::e($row['idpengadaan']) ?></td>
        <td><?= Html::e($row['tanggal']) ?></td>
        <td><?= Html::e(Label::badan($row['badan_hukum']) . ' ' . $row['nama_vendor']) ?></td>
        <td><?= Label::dash($row['username']) ?></td>
        <td class="num"><?= Label::angka($row['subtotal_nilai']) ?></td>
        <td class="num"><?= Label::angka($row['ppn']) ?></td>
        <td class="num"><?= Label::angka($row['total_nilai']) ?></td>
        <td><?= Label::statusTransaksi($row['status']) ?></td>
        <td class="actions"><button type="button" class="link-btn" data-detail="<?= $detail ?>">Detail</button></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="9">Belum ada pengadaan. Klik “Buat Pengadaan” untuk memesan barang ke vendor.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
