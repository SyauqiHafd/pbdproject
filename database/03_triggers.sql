-- =====================================================================
-- 03_triggers.sql  —  TRIGGER kalkulasi & pengisian tabel lain (Jaya Group)
-- Jalankan KETIGA (setelah 01_functions.sql dan 02_procedures.sql).
--
--  detail_pengadaan  BEFORE INSERT : validasi, isi harga_satuan & sub_total
--  detail_pengadaan  AFTER  INSERT : hitung ulang header pengadaan
--  detail_penerimaan AFTER  INSERT : (penerimaan diterima) tambah baris kartu_stok "masuk"
--  detail_penjualan  BEFORE INSERT : validasi stok, isi harga_satuan & subtotal
--  detail_penjualan  AFTER  INSERT : tambah baris kartu_stok "keluar", hitung ulang header penjualan
--
-- jenis_transaksi pada kartu_stok: T = penerimaan, J = penjualan, R = retur (cadangan)
-- =====================================================================
USE proyek_uts;

DELIMITER $$

-- ---------------------------------------------------------------------
-- PENGADAAN
-- ---------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_detail_pengadaan_bi$$
CREATE TRIGGER trg_detail_pengadaan_bi BEFORE INSERT ON detail_pengadaan
FOR EACH ROW
BEGIN
    DECLARE v_status_pengadaan CHAR(1);
    DECLARE v_nama             VARCHAR(45);
    DECLARE v_status_barang    TINYINT;
    DECLARE v_harga            INT;

    IF NEW.jumlah IS NULL OR NEW.jumlah <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah barang harus lebih dari 0.';
    END IF;

    SET v_status_pengadaan = (SELECT status FROM pengadaan WHERE idpengadaan = NEW.idpengadaan);
    IF v_status_pengadaan IS NULL OR v_status_pengadaan <> 'P' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Barang hanya bisa ditambahkan ke pengadaan yang masih menunggu penerimaan.';
    END IF;

    SET v_nama          = (SELECT nama   FROM barang WHERE idbarang = NEW.idbarang);
    SET v_status_barang = (SELECT status FROM barang WHERE idbarang = NEW.idbarang);
    SET v_harga         = (SELECT harga  FROM barang WHERE idbarang = NEW.idbarang);

    IF v_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Barang tidak ditemukan.';
    END IF;
    IF v_status_barang <> 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Barang tidak aktif, tidak dapat dipesan.';
    END IF;

    -- harga satuan kosong/0 -> pakai harga barang
    IF NEW.harga_satuan IS NULL OR NEW.harga_satuan <= 0 THEN
        SET NEW.harga_satuan = v_harga;
    END IF;
    SET NEW.sub_total = NEW.jumlah * NEW.harga_satuan;
END$$

DROP TRIGGER IF EXISTS trg_detail_pengadaan_ai$$
CREATE TRIGGER trg_detail_pengadaan_ai AFTER INSERT ON detail_pengadaan
FOR EACH ROW
BEGIN
    CALL sp_hitung_pengadaan(NEW.idpengadaan);
END$$

-- ---------------------------------------------------------------------
-- PENERIMAAN  ->  KARTU STOK (masuk)
-- Hanya penerimaan berstatus A (accept) yang menambah stok.
-- ---------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_detail_penerimaan_ai$$
CREATE TRIGGER trg_detail_penerimaan_ai AFTER INSERT ON detail_penerimaan
FOR EACH ROW
BEGIN
    DECLARE v_status  CHAR(1);
    DECLARE v_stok    INT;
    DECLARE v_idkartu BIGINT;

    SET v_status = (SELECT status FROM penerimaan WHERE idpenerimaan = NEW.idpenerimaan);

    IF v_status = 'A' THEN
        SET v_stok    = fn_stok_barang(NEW.barang_idbarang) + NEW.jumlah_terima;
        SET v_idkartu = (SELECT COALESCE(MAX(idkartu_stok), 0) + 1 FROM kartu_stok);

        INSERT INTO kartu_stok (idkartu_stok, jenis_transaksi, masuk, keluar, stock,
                                created_at, idtransaksi, idbarang)
        VALUES (v_idkartu, 'T', NEW.jumlah_terima, 0, v_stok,
                NOW(), NEW.idpenerimaan, NEW.barang_idbarang);
    END IF;
END$$

-- ---------------------------------------------------------------------
-- PENJUALAN
-- ---------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_detail_penjualan_bi$$
CREATE TRIGGER trg_detail_penjualan_bi BEFORE INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
    DECLARE v_idmargin      INT;
    DECLARE v_nama          VARCHAR(45);
    DECLARE v_status_barang TINYINT;
    DECLARE v_stok          INT;
    DECLARE v_pesan         VARCHAR(128);

    IF NEW.jumlah IS NULL OR NEW.jumlah <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah barang harus lebih dari 0.';
    END IF;

    SET v_idmargin = (SELECT idmargin_penjualan FROM penjualan WHERE idpenjualan = NEW.penjualan_idpenjualan);
    IF v_idmargin IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penjualan tidak ditemukan.';
    END IF;

    SET v_nama          = (SELECT nama   FROM barang WHERE idbarang = NEW.idbarang);
    SET v_status_barang = (SELECT status FROM barang WHERE idbarang = NEW.idbarang);
    IF v_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Barang tidak ditemukan.';
    END IF;
    IF v_status_barang <> 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Barang tidak aktif, tidak dapat dijual.';
    END IF;

    -- stok harus mencukupi (stok 0 otomatis ditolak)
    SET v_stok = fn_stok_barang(NEW.idbarang);
    IF v_stok < NEW.jumlah THEN
        SET v_pesan = CONCAT('Stok ', LEFT(v_nama, 30), ' tidak cukup. Tersedia ', v_stok,
                             ', diminta ', NEW.jumlah, '.');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_pesan;
    END IF;

    -- harga satuan kosong/0 -> harga jual = harga barang + margin penjualan ini
    IF NEW.harga_satuan IS NULL OR NEW.harga_satuan <= 0 THEN
        SET NEW.harga_satuan = fn_harga_jual(NEW.idbarang, v_idmargin);
    END IF;
    SET NEW.subtotal = NEW.jumlah * NEW.harga_satuan;
END$$

DROP TRIGGER IF EXISTS trg_detail_penjualan_ai$$
CREATE TRIGGER trg_detail_penjualan_ai AFTER INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
    DECLARE v_stok    INT;
    DECLARE v_idkartu BIGINT;

    -- kartu stok: barang keluar, saldo berkurang
    SET v_stok    = fn_stok_barang(NEW.idbarang) - NEW.jumlah;
    SET v_idkartu = (SELECT COALESCE(MAX(idkartu_stok), 0) + 1 FROM kartu_stok);

    INSERT INTO kartu_stok (idkartu_stok, jenis_transaksi, masuk, keluar, stock,
                            created_at, idtransaksi, idbarang)
    VALUES (v_idkartu, 'J', 0, NEW.jumlah, v_stok,
            NOW(), NEW.penjualan_idpenjualan, NEW.idbarang);

    CALL sp_hitung_penjualan(NEW.penjualan_idpenjualan);
END$$

DELIMITER ;
