<?php $data = $barang->all(); ?>
<div class="page-head">
  <div class="page-head-title"><h1>Data Barang</h1><span class="badge"><?= count($data) ?> data</span></div>
  <button type="button" class="btn btn-dark" data-add="barang">+ Tambah Barang</button>
</div>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr>
        <th class="px-4 py-3">No.</th><th class="px-4 py-3">Nama</th>
        <th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Satuan</th>
        <th class="px-4 py-3">Harga</th><th class="px-4 py-3">Status</th>
        <th class="px-4 py-3">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= Html::e($row['id']) ?></td>
        <td class="px-4 py-2"><?= Html::e($row['name']) ?></td>
        <td class="px-4 py-2"><?= Html::e(Label::jenis($row['jenis'])) ?></td>
        <td class="px-4 py-2"><?= Html::e($row['satuan']) ?></td>
        <td class="px-4 py-2"><?= number_format($row['harga'], 0, ',', '.') ?></td>
        <td class="px-4 py-2"><?= Label::status($row['status'] == 1) ?></td>
        <?= Html::rowActions('barang', $barang->rowForForm($row), $row['id'], $row['name']) ?>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="7">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
