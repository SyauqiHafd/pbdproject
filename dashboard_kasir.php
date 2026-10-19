<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Staff</title>

  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

  <nav class="sticky top-0 z-30 text-white flex items-center px-4 h-12"
       style="background-color:#212529;">
    <a href="#" class="font-bold text-lg mr-6 whitespace-nowrap">Jaya Group Pengadaan</a>

    <form class="hidden md:flex flex-grow max-w-md">
      <input type="text" placeholder="Cari barang..."
             class="w-full rounded px-3 py-1 text-sm text-gray-900 focus:outline-none">
    </form>

    <div class="ml-auto flex items-center gap-4 text-sm">
      <span>Staff: Farhan</span>
    </div>
  </nav>

  <div class="flex">

    <aside class="w-56 text-white fixed top-12 bottom-0 left-0 overflow-y-auto"
           style="background-color:#212529;">

      <div class="px-4 pt-4">
        <a href="#" onclick="showMenu('dashboard')"
           class="menu-link block px-3 py-2 rounded text-sm font-semibold bg-primary">
           Dashboard
        </a>
      </div>

      <h6 class="px-4 mt-5 mb-2 text-xs uppercase text-gray-400">Menu Staff</h6>
      <ul class="px-2 space-y-1 text-sm">

        <li><a href="#" onclick="showMenu('barang')"
               class="menu-link block px-3 py-2 rounded hover:bg-gray-700">> Barang</a></li>

        <li><a href="#" onclick="showMenu('margin')"
               class="menu-link block px-3 py-2 rounded hover:bg-gray-700">> Margin Penjualan</a></li>
      </ul>
    </aside>

    <main class="flex-grow ml-56 p-6">

      <section id="menu-dashboard" class="menu-page">
        <h1 class="text-2xl font-bold mb-6">Dashboard Staff</h1>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="bg-white rounded shadow p-4">
            <h5 class="text-gray-500 text-sm">Jumlah Barang Aktif</h5>
            <p class="text-3xl font-bold mt-1">5</p>
          </div>
          <div class="bg-white rounded shadow p-4">
            <h5 class="text-gray-500 text-sm">Margin Berlaku Hari Ini</h5>
            <p class="text-3xl font-bold mt-1">10.00%</p>
          </div>
          <div class="bg-white rounded shadow p-4">
            <h5 class="text-gray-500 text-sm">Transaksi Hari Ini</h5>
            <p class="text-3xl font-bold mt-1">N/A</p>
          </div>
        </div>

        <div class="mt-6 flex gap-3">
          <a href="#" onclick="showMenu('barang')" class="bg-primary text-white px-4 py-2 rounded text-sm">Lihat Barang</a>
          <a href="#" onclick="showMenu('margin')" class="bg-gray-600 text-white px-4 py-2 rounded text-sm">Lihat Margin</a>
        </div>
      </section>

      <section id="menu-barang" class="menu-page hidden">
      <?php include 'Views/barang.php'; ?>
    </section>

      <section id="menu-margin" class="menu-page hidden">
      <?php include 'Views/margin.php'; ?>
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