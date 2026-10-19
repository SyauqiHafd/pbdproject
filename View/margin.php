<?php

require_once('bootstrap.php');
$margin = new Margin();
$data   = $margin->all();
?>
<div class="flex justify-between items-center mb-4">
  <h1 class="text-2xl font-bold">Margin Penjualan</h1>
  <a href="#" class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Margin</a>
</div>
<div class="bg-white rounded shadow overflow-x-auto">
  <table class="w-full text-sm text-left">
    <thead class="bg-gray-200 text-gray-700">
      <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Margin</th><th class="px-4 py-3">Dibuat</th><th class="px-4 py-3">Diubah</th><th class="px-4 py-3">Pengguna</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
    </thead>
    <tbody class="divide-y">
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= $row['id'] ?></td>
        <td class="px-4 py-2"><?= number_format($row['persen'], 2) ?>%</td>
        <td class="px-4 py-2"><?= $row['tglbuat'] ?></td>
        <td class="px-4 py-2"><?= $row['tglupdate'] ?></td>
        <td class="px-4 py-2"><?= $row['pengguna'] ?></td>
        <td class="px-4 py-2"><?= $row['status'] == 1 ? 'Aktif' : 'Nonaktif' ?></td>
        <td class="px-4 py-2"><a href="#" class="text-blue-600">Edit</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>