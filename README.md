# Jaya Group Pengadaan — Dashboard Admin & Staff (PHP native, OOP)

## Cara menjalankan (XAMPP)
1. Salin folder ini ke `C:\xampp\htdocs\pbdproject`, nyalakan Apache + MySQL.
2. Buka `http://localhost/pbdproject/` → otomatis ke halaman login.
3. Koneksi database ada di `Model/database.php` (default: localhost, root, tanpa password, db `proyek_uts`).

## Struktur
```
index.php  login.php  logout.php  action.php  bootstrap.php
dashboard_admin.php   dashboard_kasir.php   (kasir = role Staff)
Core/        Session, Html, Icon, Label, Str          (alat bantu)
Model/       Database, Model (CRUD dasar), Barang, Satuan, Vendor, Margin, Pengguna, Role
Controller/  AuthController, CrudController, DashboardController
View/        layout/ (header, footer), partials/ (home, modals), barang.php, satuan.php, ...
assets/      css/app.css, js/app.js
```

## Login & role
- Login memakai kolom `username` dan `password` pada view `pengguna`, lalu role dicocokkan lewat view `roles`:
  nama role yang mengandung **admin** → `dashboard_admin.php`; mengandung **staff/staf/kasir** → `dashboard_kasir.php`.
  Role lain ditolak.
- Password bcrypt (`password_hash`) didukung penuh. Hash lama md5/sha1/sha256 atau teks biasa tetap bisa dipakai login.
- Pengguna baru dari modal otomatis disimpan sebagai bcrypt (60 karakter) → kolom password harus **VARCHAR(255)**:
  `ALTER TABLE <tabel_user_asli> MODIFY password VARCHAR(255);`
- Bila belum tahu password akun admin, set ulang lewat phpMyAdmin (contoh password `admin123`):
  `UPDATE <tabel_user_asli> SET password = '$2y$10$g4dQcA7eVlw0.63y0JqMh.MAhC.VTp2H6hi.jL7L4DEJNgwKXfRdC' WHERE username = 'admin';`
- Bila muncul pesan "Kolom 'password' tidak ditemukan", view `pengguna` belum menyertakan kolom password.
  Tambahkan kolom itu ke view, atau ubah nama sumber di `Pengguna::findForLogin()`.

## Tambah / Ubah / Hapus
- Hanya **admin** (halaman staff hanya melihat data). Semua lewat `action.php` -> `CrudController` (POST + token CSRF + prepared statement).
- **Membaca** dari view (`products`, `skala`, `companies`, `margins`, `pengguna`, `roles`), **menulis langsung ke tabel asli**:
  `barang`, `satuan`, `vendor`, `margin_penjualan`, `user`, `role`. Nama kolom tabel asli dicari otomatis dari daftar kandidat
  (`$columnMap` / `$writePk` di tiap class Model); bila tidak cocok, pesan error menyebut kolom yang ada di tabel.
- Bila kunci utama tabel asli **bukan AUTO_INCREMENT**, id baru dibuat otomatis = `MAX(id) + 1` (lanjut dari data yang sudah ada).
- Tabel `user`: bila kolom role bernama `idrole`, nama role dari form diubah menjadi id lewat view `roles`.
- Kolom `pengguna` pada margin diisi otomatis dengan pengguna yang login; `created_at` / `updated_at` diisi `NOW()`.
- Data yang masih dipakai tabel lain tidak bisa dihapus (muncul pesan di pojok kanan atas).

## Menu Transaksi — alur Pengadaan → Penerimaan → Kartu Stok → Penjualan

Semua hitungan (harga, subtotal, PPN, total, stok) dikerjakan **database**; PHP hanya memanggil prosedur dan menampilkan hasil.

### Pasang objek database (sekali saja)
Impor berurutan ke database `proyek_uts` (phpMyAdmin: pilih database > Import), aman diulang:
1. `database/01_functions.sql`   — `fn_persen_ppn`, `fn_hitung_ppn`, `fn_stok_barang`, `fn_id_margin_aktif`, `fn_harga_jual`
2. `database/02_procedures.sql`  — `sp_buat_pengadaan`, `sp_tambah_detail_pengadaan`, `sp_proses_penerimaan`, `sp_buat_penjualan`, `sp_tambah_detail_penjualan`, `sp_hitung_pengadaan`, `sp_hitung_penjualan`
3. `database/03_triggers.sql`    — validasi + pengisian harga/sub total, header total, dan baris `kartu_stok`

### Alur
| Langkah | Siapa | Yang terjadi |
|---|---|---|
| Buat pengadaan | admin | pilih vendor + barang + jumlah. Status **P** (menunggu). Harga = `barang.harga`; trigger mengisi sub total, PPN, total |
| Terima (accept) | admin / staff | `sp_proses_penerimaan(...,'A')` membuat penerimaan + detail; trigger menambah baris `kartu_stok` (masuk, saldo naik). Pengadaan → **A** |
| Batalkan (cancel) | admin / staff | penerimaan berstatus **C**, stok tidak berubah, pengadaan → **C** (alur retur belum dibuat) |
| Penjualan | admin / staff | hanya barang berstok > 0; trigger menolak bila stok < jumlah, mengisi harga jual = `harga × (1 + margin aktif)`, menulis `kartu_stok` (keluar), menghitung PPN & total. Respon berupa nota: **jumlah barang, daftar barang, total harga** |

Penjualan gagal (mis. stok kurang) → seluruh penjualan di-rollback dan pesan dari database ditampilkan.

### Asumsi yang bisa diubah di satu tempat
- **PPN 11%** → `fn_persen_ppn()`.
- **Harga jual = `barang.harga` + margin aktif** (margin = baris `margin_penjualan` aktif terbaru) → `fn_harga_jual()`. Penjualan ditolak bila belum ada margin aktif.
- Kode status: pengadaan P/A/C, penerimaan A/C; `kartu_stok.jenis_transaksi`: T penerimaan, J penjualan, R retur (cadangan).
- Id baris baru = `MAX(id)+1` di dalam transaksi, jadi jalan walau kolom kunci bukan AUTO_INCREMENT.

File baru: `transaksi.php`, `Controller/transaksicontroller.php`, `View/partials/transaksi_modals.php`, folder `database/`.
Tabel `retur` / `detail_retur` belum dipakai.
