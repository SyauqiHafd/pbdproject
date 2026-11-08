<?php $data = $margin->all(); ?>
<div class="page-head">
  <div class="page-head-title"><h1>Margin Penjualan</h1><span class="badge"><?= count($data) ?> data</span></div>
</div>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr><th class="px-4 py-3">No.</th><th class="px-4 py-3">Margin</th><th class="px-4 py-3">Berlaku Sejak</th><th class="px-4 py-3">Status</th></tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= Html::e($row['id']) ?></td>
        <td class="px-4 py-2"><?= number_format($row['persen'], 2) ?>%</td>
        <td class="px-4 py-2"><?= Html::e($row['tglbuat']) ?></td>
        <td class="px-4 py-2"><?= Label::status($row['status'] == 1) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="4">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
