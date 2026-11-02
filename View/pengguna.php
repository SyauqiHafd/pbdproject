<?php $data = $pengguna->all(); ?>
<div class="page-head">
  <div class="page-head-title"><h1>Data Pengguna</h1><span class="badge"><?= count($data) ?> data</span></div>
  <button type="button" class="btn btn-dark" data-add="pengguna">+ Tambah Pengguna</button>
</div>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Username</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Aksi</th></tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= Html::e($row['id']) ?></td>
        <td class="px-4 py-2"><?= Html::e($row['username']) ?></td>
        <td class="px-4 py-2"><?= Html::e($row['role']) ?></td>
        <?= Html::rowActions('pengguna', $pengguna->rowForForm($row), $row['id'], $row['username']) ?>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="4">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
