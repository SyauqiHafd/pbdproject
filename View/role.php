<?php
require_once __DIR__ . '/../../models/Role.php';
$role = new Role();
$data = $role->all();
?>
<div class="flex justify-between items-center mb-4">
  <h1 class="text-2xl font-bold">Data Role</h1>
  <a href="#" class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Role</a>
</div>
<div class="bg-white rounded shadow overflow-x-auto">
  <table class="w-full text-sm text-left">
    <thead class="bg-gray-200 text-gray-700">
      <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Nama Role</th><th class="px-4 py-3">Aksi</th></tr>
    </thead>
    <tbody class="divide-y">
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= $row['idrole'] ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($row['nama_role']) ?></td>
        <td class="px-4 py-2"><a href="#" class="text-blue-600">Edit</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>