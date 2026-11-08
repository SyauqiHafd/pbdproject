<?php $data = $retur->all(); ?>
<div class="page-head">
  <div class="page-head-title"><h1>Data Retur</h1><span class="badge"><?= count($data) ?> data</span></div>
</div>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr><th class="px-4 py-3">No.</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Penerimaan</th><th class="px-4 py-3">User</th></tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= Html::e($row["idretur"]) ?></td>
        <td class="px-4 py-2"><?= Html::e($row["created_at"]) ?></td>
        <td class="px-4 py-2"><?= Html::e($row["idpenerimaan"]) ?></td>
        <td class="px-4 py-2"><?= Html::e($row["iduser"]) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="4">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
