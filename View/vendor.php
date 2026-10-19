<?php
require_once('bootstrap.php');
$vendor = new Vendor();
$data   = $vendor->all();

function labelBadan($kode) {
    return ['P' => 'PT', 'C' => 'CV', 'U' => 'UD', 'D' => 'Toko'][$kode] ?? $kode;
}
?>
<div class="flex justify-between items-center mb-4">
  <h1 class="text-2xl font-bold">Data Vendor</h1>
  <a href="#" class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Vendor</a>
</div>
<div class="bg-white rounded shadow overflow-x-auto">
  <table class="w-full text-sm text-left">
    <thead class="bg-gray-200 text-gray-700">
      <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Nama Vendor</th><th class="px-4 py-3">Badan Hukum</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
    </thead>
    <tbody class="divide-y">
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= $row['id'] ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($row['nama_corp']) ?></td>
        <td class="px-4 py-2"><?= labelBadan($row['badan']) ?></td>
        <td class="px-4 py-2"><?= $row['status'] == 'Y' ? 'Aktif' : 'Tidak Aktif' ?></td>
        <td class="px-4 py-2"><a href="#" class="text-blue-600">Edit</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>