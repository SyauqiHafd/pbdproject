<?php $data = $margin->all(); ?>
<div class="page-head">
  <div class="page-head-title"><h1>Margin Penjualan</h1><span class="badge"><?= count($data) ?> data</span></div>
  <button type="button" class="btn btn-dark" data-add="margin">+ Tambah Margin</button>
</div>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Margin</th><th class="px-4 py-3">Dibuat</th><th class="px-4 py-3">Diubah</th><th class="px-4 py-3">Pengguna</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= Html::e($row['id']) ?></td>
        <td class="px-4 py-2"><?= number_format($row['persen'], 2) ?>%</td>
        <td class="px-4 py-2"><?= Html::e($row['tglbuat']) ?></td>
        <td class="px-4 py-2"><?= Html::e($row['tglupdate']) ?></td>
        <td class="px-4 py-2"><?= Html::e($row['pengguna']) ?></td>
        <td class="px-4 py-2"><?= Label::status($row['status'] == 1) ?></td>
        <?= Html::rowActions('margin', $margin->rowForForm($row), $row['id'], number_format($row['persen'], 2) . '%') ?>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="7">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
