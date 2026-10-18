<?php
require_once __DIR__ . '/../../models/Barang.php';
$barang = new Barang();
$data   = $barang->all(); // READ semua barang

function labelJenis($kode) {
    return ['S' => 'Solid', 'L' => 'Liquid', 'G' => 'Gas'][$kode] ?? $kode;
}
?>
<div class="flex justify-between items-center mb-4">
  <h1 class="text-2xl font-bold">Data Barang</h1>
  <a href="#" class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Barang</a>
</div>
<div class="bg-white rounded shadow overflow-x-auto">
  <table class="w-full text-sm text-left">
    <thead class="bg-gray-200 text-gray-700">
      <tr>
        <th class="px-4 py-3">ID</th><th class="px-4 py-3">Nama</th>
        <th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Satuan</th>
        <th class="px-4 py-3">Harga</th><th class="px-4 py-3">Status</th>
        <th class="px-4 py-3">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <?php foreach ($data as $row): ?>
      <tr>
        <td class="px-4 py-2"><?= $row['id'] ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
        <td class="px-4 py-2"><?= labelJenis($row['jenis']) ?></td>
        <td class="px-4 py-2"><?= $row['satuan'] ?></td>
        <!-- Format harga: 1.340.000 -->
        <td class="px-4 py-2"><?= number_format($row['harga'], 0, ',', '.') ?></td>
        <!-- Status tinyint: 1=Aktif, 0=Nonaktif -->
        <td class="px-4 py-2"><?= $row['status'] == 1 ? 'Aktif' : 'Nonaktif' ?></td>
        <td class="px-4 py-2"><a href="#" class="text-blue-600">Edit</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>