<?php
require_once __DIR__ . '/../../models/Pengguna.php';
$pengguna = new Pengguna();
$data     = $pengguna->all();
?>
<div class="flex justify-between items-center mb-4">
  <h1 class="text-2xl font-bold">Data Pengguna</h1>
  <a href="#" class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Pengguna</a>
</div>
<div class="bg-white rounded shadow overflow-x-auto">
  <table class="w-full text-sm text-left">
    <thead class="bg-gray-200 text-gray-700">
      <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Username</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Aksi</th></tr>
    </thead>
    <tbody class="divide-y">
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= $row['id'] ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($row['username']) ?></td>
        <td class="px-4 py-2"><?= $row['role'] ?></td>
        <td class="px-4 py-2"><a href="#" class="text-blue-600">Edit</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>