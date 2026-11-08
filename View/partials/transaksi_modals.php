<?php
/**
 * Modal transaksi: form pengadaan (admin), form penjualan, konfirmasi terima/batal penerimaan,
 * detail transaksi, dan nota "penjualan berhasil".
 * Variabel: $user, $penjualan
 */
$csrf    = Session::csrfToken();
$isAdmin = $user['role'] === 'admin';

// nota penjualan yang baru saja dibuat (diset TransaksiController, tampil sekali)
$notaId = Session::pull('nota');
$nota   = $notaId ? $penjualan->nota((int) $notaId) : null;

$siap         = Model::transaksiSiap();
$dijual      = $siap ? $penjualan->barangTersedia() : [];
$marginAktif = $siap ? $penjualan->marginAktif() : null;
?>

<?php if ($isAdmin): ?>
<?php
  $mp         = new Pengadaan();
  $vendors    = $mp->vendorOptions();
  $barangBeli = $mp->barangOptions();
?>
<!-- ---------------------------- Buat pengadaan ---------------------------- -->
<dialog class="modal modal-lg" id="modal-pengadaan">
  <form method="post" action="transaksi.php" class="modal-card" autocomplete="off" data-lines-form>
    <input type="hidden" name="csrf" value="<?= Html::e($csrf) ?>">
    <input type="hidden" name="aksi" value="pengadaan_buat">

    <header class="modal-head">
      <h3 class="modal-title">Buat Pengadaan</h3>
      <button type="button" class="icon-btn" data-close aria-label="Tutup"><?= Icon::svg('x', 18) ?></button>
    </header>

    <div class="modal-body">
      <?php if (!$vendors || !$barangBeli): ?>
        <p class="alert alert-info">Pengadaan membutuhkan minimal satu vendor aktif dan satu barang aktif. Lengkapi dulu di menu Vendor dan Barang.</p>
      <?php endif; ?>

      <div class="field">
        <label for="pengadaan-vendor">Vendor</label>
        <select id="pengadaan-vendor" name="vendor" required>
          <option value="">Pilih vendor</option>
          <?php foreach ($vendors as $v): ?>
            <option value="<?= Html::e($v['idvendor']) ?>"><?= Html::e(Label::badan($v['badan_hukum']) . ' ' . $v['nama_vendor']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label>Barang yang dipesan</label>
        <div class="lines" data-lines></div>
        <div><button type="button" class="btn btn-outline" data-add-line><?= Icon::svg('plus', 15) ?>Tambah barang</button></div>
      </div>

      <p class="lines-total"><span>Perkiraan subtotal</span><strong data-lines-total>Rp 0</strong></p>
      <p class="muted">Harga mengikuti harga barang. PPN dan total akhir dihitung database saat pengadaan disimpan.</p>
    </div>

    <footer class="modal-foot">
      <button type="button" class="btn btn-outline" data-close>Batal</button>
      <button type="submit" class="btn btn-dark" <?= (!$vendors || !$barangBeli) ? 'disabled' : '' ?>>Simpan pengadaan</button>
    </footer>

    <template data-line-template>
      <div class="line">
        <select name="barang[]" required aria-label="Barang">
          <option value="">Pilih barang</option>
          <?php foreach ($barangBeli as $b): ?>
            <option value="<?= Html::e($b['idbarang']) ?>" data-harga="<?= Html::e($b['harga']) ?>"><?= Html::e($b['nama']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="number" name="jumlah[]" min="1" step="1" value="1" required aria-label="Jumlah">
        <span class="line-info"></span>
        <button type="button" class="icon-btn" data-remove-line aria-label="Hapus baris"><?= Icon::svg('x', 16) ?></button>
      </div>
    </template>
  </form>
</dialog>
<?php endif; ?>

<!-- ---------------------------- Buat penjualan ---------------------------- -->
<dialog class="modal modal-lg" id="modal-penjualan">
  <form method="post" action="transaksi.php" class="modal-card" autocomplete="off" data-lines-form>
    <input type="hidden" name="csrf" value="<?= Html::e($csrf) ?>">
    <input type="hidden" name="aksi" value="penjualan_buat">

    <header class="modal-head">
      <h3 class="modal-title">Buat Penjualan</h3>
      <button type="button" class="icon-btn" data-close aria-label="Tutup"><?= Icon::svg('x', 18) ?></button>
    </header>

    <div class="modal-body">
      <?php if (!$siap): ?>
        <p class="alert alert-error">Function, procedure, dan trigger transaksi belum terpasang. Impor file di folder database/ dulu.</p>
      <?php elseif ($marginAktif === null): ?>
        <p class="alert alert-error">Belum ada margin penjualan yang aktif, jadi harga jual belum bisa ditentukan. Aktifkan satu margin lebih dulu.</p>
      <?php elseif (!$dijual): ?>
        <p class="alert alert-info">Belum ada barang dengan stok tersedia. Terima pengadaan lebih dulu agar stok masuk.</p>
      <?php else: ?>
        <p class="muted margin-note">Harga jual = harga barang + margin <?= number_format($marginAktif, 2) ?>%. Hanya barang dengan stok tersedia yang bisa dipilih.</p>
      <?php endif; ?>

      <div class="field">
        <label>Barang yang dijual</label>
        <div class="lines" data-lines></div>
        <div><button type="button" class="btn btn-outline" data-add-line><?= Icon::svg('plus', 15) ?>Tambah barang</button></div>
      </div>

      <p class="lines-total"><span>Perkiraan subtotal</span><strong data-lines-total>Rp 0</strong></p>
      <p class="muted">PPN dan total akhir dihitung database saat penjualan disimpan.</p>
    </div>

    <footer class="modal-foot">
      <button type="button" class="btn btn-outline" data-close>Batal</button>
      <button type="submit" class="btn btn-dark" <?= ($marginAktif === null || !$dijual) ? 'disabled' : '' ?>>Simpan penjualan</button>
    </footer>

    <template data-line-template>
      <div class="line">
        <select name="barang[]" required aria-label="Barang">
          <option value="">Pilih barang</option>
          <?php foreach ($dijual as $b): ?>
            <option value="<?= Html::e($b['idbarang']) ?>" data-harga="<?= Html::e($b['harga_jual']) ?>" data-stok="<?= Html::e($b['stok']) ?>"><?= Html::e($b['nama']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="number" name="jumlah[]" min="1" step="1" value="1" required aria-label="Jumlah">
        <span class="line-info"></span>
        <button type="button" class="icon-btn" data-remove-line aria-label="Hapus baris"><?= Icon::svg('x', 16) ?></button>
      </div>
    </template>
  </form>
</dialog>

<!-- ------------------- Konfirmasi terima / batalkan pengadaan ------------------- -->
<dialog class="modal modal-sm" id="modal-proses">
  <form method="post" action="transaksi.php" class="modal-card">
    <input type="hidden" name="csrf" value="<?= Html::e($csrf) ?>">
    <input type="hidden" name="aksi" value="penerimaan_terima">
    <input type="hidden" name="id" value="">

    <header class="modal-head">
      <h3 class="modal-title" id="proses-title">Terima pengadaan</h3>
      <button type="button" class="icon-btn" data-close aria-label="Tutup"><?= Icon::svg('x', 18) ?></button>
    </header>
    <div class="modal-body">
      <p class="confirm-text" id="proses-text"></p>
    </div>
    <footer class="modal-foot">
      <button type="button" class="btn btn-outline" data-close>Kembali</button>
      <button type="submit" class="btn btn-dark" id="proses-submit">Terima</button>
    </footer>
  </form>
</dialog>

<!-- ------------------------------ Detail transaksi ------------------------------ -->
<dialog class="modal modal-lg" id="modal-detail">
  <div class="modal-card">
    <header class="modal-head">
      <h3 class="modal-title" id="detail-title">Detail</h3>
      <button type="button" class="icon-btn" data-close aria-label="Tutup"><?= Icon::svg('x', 18) ?></button>
    </header>
    <div class="modal-body">
      <p class="detail-sub" id="detail-sub"></p>
      <div class="tbl-wrap">
        <table class="tbl tbl-compact">
          <thead><tr><th>Barang</th><th class="num">Jumlah</th><th class="num">Harga</th><th class="num">Subtotal</th></tr></thead>
          <tbody id="detail-lines"></tbody>
        </table>
      </div>
      <dl class="totals" id="detail-totals"></dl>
    </div>
    <footer class="modal-foot"><button type="button" class="btn btn-outline" data-close>Tutup</button></footer>
  </div>
</dialog>

<?php if ($nota): $h = $nota['header']; ?>
<!-- ------------------------- Nota: penjualan berhasil ------------------------- -->
<dialog class="modal modal-lg" id="modal-nota" data-autoopen>
  <div class="modal-card">
    <header class="modal-head">
      <h3 class="modal-title">Penjualan berhasil</h3>
      <button type="button" class="icon-btn" data-close aria-label="Tutup"><?= Icon::svg('x', 18) ?></button>
    </header>
    <div class="modal-body">
      <p class="detail-sub">Nota #<?= Html::e($h['idpenjualan']) ?>, <?= Html::e($h['created_at']) ?>, kasir <?= Html::e($h['username']) ?></p>
      <div class="tbl-wrap">
        <table class="tbl tbl-compact">
          <thead><tr><th>Barang</th><th class="num">Jumlah</th><th class="num">Harga</th><th class="num">Subtotal</th></tr></thead>
          <tbody>
            <?php foreach ($nota['lines'] as $l): ?>
            <tr>
              <td><?= Html::e($l['nama']) ?></td>
              <td class="num"><?= Label::angka($l['jumlah']) ?></td>
              <td class="num"><?= Label::rupiah($l['harga']) ?></td>
              <td class="num"><?= Label::rupiah($l['subtotal']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <dl class="totals">
        <div><dt>Jumlah barang</dt><dd><?= Label::angka($nota['jumlah_barang']) ?></dd></div>
        <div><dt>Subtotal</dt><dd><?= Label::rupiah($h['subtotal_nilai']) ?></dd></div>
        <div><dt>PPN</dt><dd><?= Label::rupiah($h['ppn']) ?></dd></div>
        <div class="grand"><dt>Total harga</dt><dd><?= Label::rupiah($h['total_nilai']) ?></dd></div>
      </dl>
    </div>
    <footer class="modal-foot"><button type="button" class="btn btn-dark" data-close>Selesai</button></footer>
  </div>
</dialog>
<?php endif; ?>
