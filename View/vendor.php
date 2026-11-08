<?php $data = $vendor->all(); ?>
<div class="page-head">
  <div class="page-head-title"><h1>Data Vendor</h1><span class="badge"><?= count($data) ?> data</span></div>
  <button type="button" class="btn btn-dark" data-add="vendor">+ Tambah Vendor</button>
</div>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr><th class="px-4 py-3">No.</th><th class="px-4 py-3">Nama Vendor</th><th class="px-4 py-3">Badan Hukum</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= Html::e($row['id']) ?></td>
        <td class="px-4 py-2"><?= Html::e($row['nama_corp']) ?></td>
        <td class="px-4 py-2"><?= Html::e(Label::badan($row['badan'])) ?></td>
        <td class="px-4 py-2"><?= Label::status($row['status'] == 'Y', 'Aktif', 'Tidak Aktif') ?></td>
        <?= Html::rowActions('vendor', $vendor->rowForForm($row), $row['id'], $row['nama_corp']) ?>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="5">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
