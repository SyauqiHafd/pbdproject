-- =====================================================================
-- 02_procedures.sql  —  STORED PROCEDURE alur transaksi (Jaya Group)
-- Jalankan KEDUA (setelah 01_functions.sql, sebelum 03_triggers.sql).
--
-- Alur:  Pengadaan (P) -> Penerimaan (A=terima / C=batal) -> Kartu Stok -> Penjualan
-- Status pengadaan : P = menunggu penerimaan, A = diterima, C = dibatalkan
-- Status penerimaan: A = diterima (accept),   C = dibatalkan (cancel)
--
-- Id baris baru dibuat dengan MAX(id)+1 di dalam transaksi (FOR UPDATE), sehingga
-- tetap jalan baik kolom kuncinya AUTO_INCREMENT maupun bukan.
-- =====================================================================
USE proyek_uts;

DELIMITER $$

-- ---------------------------------------------------------------------
-- Hitung ulang subtotal, PPN, dan total pada header pengadaan.
-- (dipanggil oleh trigger setiap ada detail baru)
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_hitung_pengadaan$$
CREATE PROCEDURE sp_hitung_pengadaan(IN p_idpengadaan BIGINT)
BEGIN
    DECLARE v_subtotal INT DEFAULT 0;
    DECLARE v_ppn      INT DEFAULT 0;

    SELECT COALESCE(SUM(sub_total), 0) INTO v_subtotal
      FROM detail_pengadaan
     WHERE idpengadaan = p_idpengadaan;

    SET v_ppn = fn_hitung_ppn(v_subtotal);

    UPDATE pengadaan
       SET subtotal_nilai = v_subtotal,
           ppn            = v_ppn,
           total_nilai    = v_subtotal + v_ppn
     WHERE idpengadaan = p_idpengadaan;
END$$

-- ---------------------------------------------------------------------
-- Hitung ulang subtotal, PPN, dan total pada header penjualan.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_hitung_penjualan$$
CREATE PROCEDURE sp_hitung_penjualan(IN p_idpenjualan INT)
BEGIN
    DECLARE v_subtotal INT DEFAULT 0;
    DECLARE v_ppn      INT DEFAULT 0;

    SELECT COALESCE(SUM(subtotal), 0) INTO v_subtotal
      FROM detail_penjualan
     WHERE penjualan_idpenjualan = p_idpenjualan;

    SET v_ppn = fn_hitung_ppn(v_subtotal);

    UPDATE penjualan
       SET subtotal_nilai = v_subtotal,
           ppn            = v_ppn,
           total_nilai    = v_subtotal + v_ppn
     WHERE idpenjualan = p_idpenjualan;
END$$

-- ---------------------------------------------------------------------
-- 1) PENGADAAN: buat header (status P). Detail ditambah dengan sp_tambah_detail_pengadaan.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_buat_pengadaan$$
CREATE PROCEDURE sp_buat_pengadaan(
    IN  p_iduser      INT,
    IN  p_idvendor    INT,
    OUT p_idpengadaan BIGINT
)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM `user` WHERE iduser = p_iduser) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak valid.';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM vendor WHERE idvendor = p_idvendor AND status = 'Y') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Vendor tidak ditemukan atau tidak aktif.';
    END IF;

    SELECT COALESCE(MAX(idpengadaan), 0) + 1 INTO p_idpengadaan FROM pengadaan FOR UPDATE;

    INSERT INTO pengadaan (idpengadaan, `timestamp`, user_iduser, status, vendor_idvendor,
                           subtotal_nilai, ppn, total_nilai)
    VALUES (p_idpengadaan, NOW(), p_iduser, 'P', p_idvendor, 0, 0, 0);
END$$

-- Tambah satu baris barang ke pengadaan. Harga satuan, sub total, validasi, dan total header
-- diisi oleh trigger (lihat 03_triggers.sql).
DROP PROCEDURE IF EXISTS sp_tambah_detail_pengadaan$$
CREATE PROCEDURE sp_tambah_detail_pengadaan(
    IN p_idpengadaan BIGINT,
    IN p_idbarang    INT,
    IN p_jumlah      INT
)
BEGIN
    DECLARE v_iddetail BIGINT;

    SELECT COALESCE(MAX(iddetail_pengadaan), 0) + 1 INTO v_iddetail FROM detail_pengadaan FOR UPDATE;

    INSERT INTO detail_pengadaan (iddetail_pengadaan, harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
    VALUES (v_iddetail, 0, p_jumlah, 0, p_idbarang, p_idpengadaan);
END$$

-- ---------------------------------------------------------------------
-- 2) PENERIMAAN: toko menerima (p_aksi = 'A') atau membatalkan (p_aksi = 'C') sebuah pengadaan.
--    'A' : buat penerimaan + salin detail pengadaan -> trigger menambah baris kartu_stok (masuk).
--    'C' : buat penerimaan berstatus C, tanpa mengubah stok. (Retur: belum dikerjakan.)
--    Pengadaan ikut berubah status menjadi A / C.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_proses_penerimaan$$
CREATE PROCEDURE sp_proses_penerimaan(
    IN  p_idpengadaan  BIGINT,
    IN  p_iduser       INT,
    IN  p_aksi         CHAR(1),
    OUT p_idpenerimaan BIGINT
)
BEGIN
    DECLARE v_status   CHAR(1);
    DECLARE v_selesai  INT DEFAULT 0;
    DECLARE v_idbarang INT;
    DECLARE v_jumlah   INT;
    DECLARE v_harga    INT;
    DECLARE v_subtotal INT;
    DECLARE v_iddetail BIGINT;

    DECLARE cur CURSOR FOR
        SELECT idbarang, jumlah, harga_satuan, sub_total
          FROM detail_pengadaan
         WHERE idpengadaan = p_idpengadaan
         ORDER BY iddetail_pengadaan;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_selesai = 1;

    IF p_aksi IS NULL OR p_aksi NOT IN ('A', 'C') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Aksi penerimaan tidak dikenal.';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM `user` WHERE iduser = p_iduser) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak valid.';
    END IF;

    SELECT status INTO v_status FROM pengadaan WHERE idpengadaan = p_idpengadaan FOR UPDATE;

    IF v_status IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengadaan tidak ditemukan.';
    END IF;
    IF v_status <> 'P' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengadaan ini sudah diproses sebelumnya.';
    END IF;

    SELECT COALESCE(MAX(idpenerimaan), 0) + 1 INTO p_idpenerimaan FROM penerimaan FOR UPDATE;

    INSERT INTO penerimaan (idpenerimaan, created_at, status, idpengadaan, iduser)
    VALUES (p_idpenerimaan, NOW(), p_aksi, p_idpengadaan, p_iduser);

    IF p_aksi = 'A' THEN
        SELECT COALESCE(MAX(iddetail_penerimaan), 0) INTO v_iddetail FROM detail_penerimaan FOR UPDATE;
        SET v_selesai = 0;

        OPEN cur;
        baca: LOOP
            FETCH cur INTO v_idbarang, v_jumlah, v_harga, v_subtotal;
            IF v_selesai = 1 THEN
                LEAVE baca;
            END IF;

            SET v_iddetail = v_iddetail + 1;
            INSERT INTO detail_penerimaan (iddetail_penerimaan, idpenerimaan, barang_idbarang,
                                           jumlah_terima, harga_satuan_terima, sub_total_terima)
            VALUES (v_iddetail, p_idpenerimaan, v_idbarang, v_jumlah, v_harga, v_subtotal);
        END LOOP;
        CLOSE cur;
    END IF;

    UPDATE pengadaan SET status = p_aksi WHERE idpengadaan = p_idpengadaan;
END$$

-- ---------------------------------------------------------------------
-- 3) PENJUALAN: buat header memakai margin aktif, lalu tambah baris barang.
--    Cek stok, harga jual, sub total, pengurangan stok (kartu_stok), dan total header
--    dikerjakan oleh trigger.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_buat_penjualan$$
CREATE PROCEDURE sp_buat_penjualan(
    IN  p_iduser      INT,
    OUT p_idpenjualan INT
)
BEGIN
    DECLARE v_idmargin INT;

    IF NOT EXISTS (SELECT 1 FROM `user` WHERE iduser = p_iduser) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak valid.';
    END IF;

    SET v_idmargin = fn_id_margin_aktif();
    IF v_idmargin IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Belum ada margin penjualan yang aktif. Aktifkan margin dulu.';
    END IF;

    SELECT COALESCE(MAX(idpenjualan), 0) + 1 INTO p_idpenjualan FROM penjualan FOR UPDATE;

    INSERT INTO penjualan (idpenjualan, created_at, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
    VALUES (p_idpenjualan, NOW(), 0, 0, 0, p_iduser, v_idmargin);
END$$

DROP PROCEDURE IF EXISTS sp_tambah_detail_penjualan$$
CREATE PROCEDURE sp_tambah_detail_penjualan(
    IN p_idpenjualan INT,
    IN p_idbarang    INT,
    IN p_jumlah      INT
)
BEGIN
    DECLARE v_iddetail BIGINT;
    DECLARE v_kunci    INT;

    IF NOT EXISTS (SELECT 1 FROM penjualan WHERE idpenjualan = p_idpenjualan) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penjualan tidak ditemukan.';
    END IF;

    -- kunci baris barang agar dua penjualan bersamaan tidak memakai stok yang sama
    SELECT COUNT(*) INTO v_kunci FROM barang WHERE idbarang = p_idbarang FOR UPDATE;

    SELECT COALESCE(MAX(iddetail_penjualan), 0) + 1 INTO v_iddetail FROM detail_penjualan FOR UPDATE;

    INSERT INTO detail_penjualan (iddetail_penjualan, harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
    VALUES (v_iddetail, 0, p_jumlah, 0, p_idpenjualan, p_idbarang);
END$$

DELIMITER ;
