<?php $data = $role->all(); ?>
<div class="page-head">
  <div class="page-head-title"><h1>Data Role</h1><span class="badge"><?= count($data) ?> data</span></div>
  <button type="button" class="btn btn-dark" data-add="role">+ Tambah Role</button>
</div>
<div class="tbl-wrap">
  <table class="tbl">
    <thead>
      <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Nama Role</th><th class="px-4 py-3">Aksi</th></tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= Html::e($row['idrole']) ?></td>
        <td class="px-4 py-2"><?= Html::e($row['nama_role']) ?></td>
        <?= Html::rowActions('role', $role->rowForForm($row), $row['idrole'], $row['nama_role']) ?>
      </tr>
      <?php endforeach; ?>
      <?php if (!$data): ?><tr class="empty"><td colspan="3">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
