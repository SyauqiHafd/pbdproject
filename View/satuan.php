<?php $data = $satuan->all(); ?>
<div class="page-head">
  <div class="page-head-title"><h1>Data Satuan</h1><span class="badge"><?= count($data) ?> data</span></div>
  <button type="button" class="btn btn-dark" data-add="satuan">+ Tambah Satuan</button>
</div>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr><th class="px-4 py-3">No.</th><th class="px-4 py-3">Nama Satuan</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= Html::e($row['id']) ?></td>
        <td class="px-4 py-2"><?= Html::e($row['name']) ?></td>
        <td class="px-4 py-2"><?= Label::status($row['status'] == 1) ?></td>
        <?= Html::rowActions('satuan', $satuan->rowForForm($row), $row['id'], $row['name']) ?>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="4">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
