<?php
$koneksi = mysqli_connect('localhost', 'root', '', 'proyek_uts');

if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

function ambil_data($koneksi, $sql) {
    $hasil = mysqli_query($koneksi, $sql);          
    $data  = [];                                    
    while ($baris = mysqli_fetch_assoc($hasil)) {   
        $data[] = $baris;
    }
    return $data;
}

function label_jenis($kode) {
    if ($kode == 'S') return 'Solid';
    if ($kode == 'L') return 'Liquid';
    if ($kode == 'G') return 'Gas';
    return $kode;
}

function label_badan($kode) {
    if ($kode == 'P') return 'PT';
    if ($kode == 'C') return 'CV';
    if ($kode == 'U') return 'UD';
    if ($kode == 'D') return 'Toko';
    return $kode;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <!-- ============================================================
       DASHBOARD ADMIN - Sistem Penjualan & Persediaan Barang
       Navigasi: sidebar fixed (ala Bootstrap Dashboard) + top navbar
       ============================================================ -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

  <nav class="sticky top-0 z-30 bg-dark text-white flex items-center px-4 h-12"
       style="background-color:#212529;">
    <a href="#" class="font-bold text-lg mr-6 whitespace-nowrap">Jaya Group Pengadaan</a>

    <div class="ml-auto flex items-center gap-4 text-sm">
      <span class="hidden sm:inline text-gray-300">|</span>
      <span>Admin</span>
    </div>
  </nav>

  <div class="flex">

    <aside class="w-56 bg-dark text-white fixed top-12 bottom-0 left-0 overflow-y-auto"
           style="background-color:#212529;">

      <div class="px-4 pt-4">
        <a href="#" onclick="showMenu('dashboard')"
           class="menu-link block px-3 py-2 rounded text-sm font-semibold bg-primary">
            Dashboard
        </a>
      </div>

      <h6 class="px-4 mt-5 mb-2 text-xs uppercase text-gray-400">Menu Utama</h6>
      <ul class="px-2 space-y-1 text-sm">

        <li><a href="#" onclick="showMenu('barang')"
               class="menu-link block px-3 py-2 rounded hover:bg-gray-700">> Barang</a></li>

        <li><a href="#" onclick="showMenu('satuan')"
               class="menu-link block px-3 py-2 rounded hover:bg-gray-700">> Satuan</a></li>

        <li><a href="#" onclick="showMenu('vendor')"
               class="menu-link block px-3 py-2 rounded hover:bg-gray-700">> Vendor</a></li>

        <li><a href="#" onclick="showMenu('margin')"
               class="menu-link block px-3 py-2 rounded hover:bg-gray-700">> Margin Penjualan</a></li>

        <h6 class="px-2 pt-4 pb-1 text-xs uppercase text-gray-400">Pengaturan</h6>

        <li><a href="#" onclick="showMenu('pengguna')"
               class="menu-link block px-3 py-2 rounded hover:bg-gray-700">> Pengguna</a></li>

        <li><a href="#" onclick="showMenu('role')"
               class="menu-link block px-3 py-2 rounded hover:bg-gray-700">> Role</a></li>
      </ul>
    </aside>

    <main class="flex-grow ml-56 p-6">

      <!-- ---------- HALAMAN DASHBOARD (ringkasan) ---------- -->
      <section id="menu-dashboard" class="menu-page">
        <h1 class="text-2xl font-bold mb-6">Dashboard</h1>
        <!-- Kartu ringkasan statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white rounded shadow p-4">
            <h5 class="text-gray-500 text-sm">Total Barang</h5>
            <p class="text-3xl font-bold mt-1">5</p>
          </div>
          <div class="bg-white rounded shadow p-4">
            <h5 class="text-gray-500 text-sm">Total Vendor</h5>
            <p class="text-3xl font-bold mt-1">5</p>
          </div>
          <div class="bg-white rounded shadow p-4">
            <h5 class="text-gray-500 text-sm">Total Pengguna</h5>
            <p class="text-3xl font-bold mt-1">5</p>
          </div>
          <div class="bg-white rounded shadow p-4">
            <h5 class="text-gray-500 text-sm">Margin Aktif</h5>
            <p class="text-3xl font-bold mt-1">1</p>
          </div>
        </div>
      </section>

      <section id="menu-barang" class="menu-page hidden">

      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data Barang</h1>
        <button class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Barang</button>
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
            <?php
            $sql_barang = "SELECT * FROM products";
            $hasil_barang = mysqli_query($koneksi, $sql_barang);

            while ($row = mysqli_fetch_assoc($hasil_barang)) :
            ?>
            <tr>
              <td class="px-4 py-2"><?= $row['id'] ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
              <td class="px-4 py-2"><?= label_jenis($row['jenis']) ?></td>
              <td class="px-4 py-2"><?= $row['satuan'] ?></td>
              <td class="px-4 py-2"><?= number_format($row['harga'], 0, ',', '.') ?></td>
              <td class="px-4 py-2"><?= $row['status'] == 1 ? 'Aktif' : 'Nonaktif' ?></td>
              <td class="px-4 py-2"><a href="#" class="text-blue-600">Edit</a></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </section>

      <section id="menu-satuan" class="menu-page hidden">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data Satuan</h1>
        <button class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Satuan</button>
      </div>
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-200 text-gray-700">
            <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Nama Satuan</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
          </thead>
          <tbody class="divide-y">
            <?php
            $data_satuan = ambil_data($koneksi, "SELECT * FROM skala");
            foreach ($data_satuan as $row) :
            ?>
            <tr>
              <td class="px-4 py-2"><?= $row['id'] ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
              <td class="px-4 py-2"><?= $row['status'] == 1 ? 'Aktif' : 'Nonaktif' ?></td>
              <td class="px-4 py-2"><a href="#" class="text-blue-600">Edit</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

      <section id="menu-vendor" class="menu-page hidden">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data Vendor</h1>
        <button class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Vendor</button>
      </div>
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-200 text-gray-700">
            <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Nama Vendor</th><th class="px-4 py-3">Badan Hukum</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
          </thead>
          <tbody class="divide-y">
            <?php
            $data_vendor = ambil_data($koneksi, "SELECT * FROM companies");
            foreach ($data_vendor as $row) :
            ?>
            <tr>
              <td class="px-4 py-2"><?= $row['id'] ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['nama_corp']) ?></td>
              <td class="px-4 py-2"><?= label_badan($row['badan']) ?></td>
              <td class="px-4 py-2"><?= $row['status'] == 'Y' ? 'Aktif' : 'Tidak Aktif' ?></td>
              <td class="px-4 py-2"><a href="#" class="text-blue-600">Edit</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

      <section id="menu-margin" class="menu-page hidden">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Margin Penjualan</h1>
        <button class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Margin</button>
      </div>
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-200 text-gray-700">
            <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Margin</th><th class="px-4 py-3">Dibuat</th><th class="px-4 py-3">Diubah</th><th class="px-4 py-3">Pengguna</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
          </thead>
          <tbody class="divide-y">
            <?php
            $data_margin = ambil_data($koneksi, "SELECT * FROM margins");
            foreach ($data_margin as $row) :
            ?>
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
    </section>

      <section id="menu-pengguna" class="menu-page hidden">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data Pengguna</h1>
        <button class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Pengguna</button>
      </div>
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-200 text-gray-700">
            <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Username</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Aksi</th></tr>
          </thead>
          <tbody class="divide-y">
            <?php
            $data_pengguna = ambil_data($koneksi, "SELECT * FROM pengguna");
            foreach ($data_pengguna as $row) :
            ?>
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
    </section>

      <section id="menu-role" class="menu-page hidden">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data Role</h1>
        <button class="bg-primary text-white px-4 py-2 rounded text-sm">+ Tambah Role</button>
      </div>
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-200 text-gray-700">
            <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Nama Role</th><th class="px-4 py-3">Aksi</th></tr>
          </thead>
          <tbody class="divide-y">
            <?php
            $data_role = ambil_data($koneksi, "SELECT * FROM roles");
            foreach ($data_role as $row) :
            ?>
            <tr>
              <td class="px-4 py-2"><?= $row['idrole'] ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['nama_role']) ?></td>
              <td class="px-4 py-2"><a href="#" class="text-blue-600">Edit</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    </main>
  </div>

  <script>
    function showMenu(menu) {
      document.querySelectorAll('.menu-page').forEach(function(page) {
        page.classList.add('hidden');
      });
      document.getElementById('menu-' + menu).classList.remove('hidden');
      document.querySelectorAll('.menu-link').forEach(function(link) {
        link.classList.remove('bg-primary');
      });
      event.target.closest('.menu-link').classList.add('bg-primary');
    }
  </script>
</body>
</html>