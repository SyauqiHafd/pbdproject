-- =====================================================================
-- 01_functions.sql  —  FUNCTION untuk kalkulasi transaksi (Jaya Group)
-- Jalankan PERTAMA. Di phpMyAdmin: pilih database `proyek_uts` > tab Import
-- (atau tab SQL, tempel isi file ini). Aman dijalankan berulang kali.
-- =====================================================================
USE proyek_uts;

DELIMITER $$

-- Tarif PPN (persen). Ubah angka di sini bila tarif berubah; semua transaksi ikut.
DROP FUNCTION IF EXISTS fn_persen_ppn$$
CREATE FUNCTION fn_persen_ppn() RETURNS DECIMAL(5,2)
DETERMINISTIC
BEGIN
    RETURN 11.00;
END$$

-- PPN (dibulatkan ke rupiah) dari sebuah subtotal.
DROP FUNCTION IF EXISTS fn_hitung_ppn$$
CREATE FUNCTION fn_hitung_ppn(p_subtotal INT) RETURNS INT
DETERMINISTIC
BEGIN
    RETURN ROUND(IFNULL(p_subtotal, 0) * fn_persen_ppn() / 100);
END$$

-- Stok terkini sebuah barang = saldo (kolom stock) pada baris kartu_stok paling baru.
DROP FUNCTION IF EXISTS fn_stok_barang$$
CREATE FUNCTION fn_stok_barang(p_idbarang INT) RETURNS INT
READS SQL DATA
BEGIN
    RETURN COALESCE(
        (SELECT stock FROM kartu_stok
          WHERE idbarang = p_idbarang
          ORDER BY idkartu_stok DESC
          LIMIT 1),
        0);
END$$

-- Id margin penjualan yang berlaku: yang berstatus aktif dan paling baru dibuat (NULL bila tidak ada).
DROP FUNCTION IF EXISTS fn_id_margin_aktif$$
CREATE FUNCTION fn_id_margin_aktif() RETURNS INT
READS SQL DATA
BEGIN
    RETURN (SELECT idmargin_penjualan
              FROM margin_penjualan
             WHERE status = 1
             ORDER BY created_at DESC, idmargin_penjualan DESC
             LIMIT 1);
END$$

-- Harga jual satuan = barang.harga dinaikkan sebesar persen margin, dibulatkan ke rupiah.
DROP FUNCTION IF EXISTS fn_harga_jual$$
CREATE FUNCTION fn_harga_jual(p_idbarang INT, p_idmargin INT) RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_harga  INT;
    DECLARE v_persen DOUBLE;

    SET v_harga  = (SELECT harga FROM barang WHERE idbarang = p_idbarang);
    SET v_persen = IFNULL((SELECT persen FROM margin_penjualan WHERE idmargin_penjualan = p_idmargin), 0);

    RETURN ROUND(IFNULL(v_harga, 0) * (1 + v_persen / 100));
END$$

DELIMITER ;
