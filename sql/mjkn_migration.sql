-- ============================================================
-- Migrasi fitur MJKN — jalankan sekali di database production
-- ============================================================

-- 1. Tambah kolom no_reg_mjkn (kode poli BPJS + nomor urut, contoh: ANA-3)
ALTER TABLE antrian
  ADD COLUMN no_reg_mjkn VARCHAR(20) NULL DEFAULT NULL AFTER jenis;

ALTER TABLE antrian
  ADD UNIQUE KEY uq_mjkn_per_hari (tgl, no_reg_mjkn);

-- 2. Tambah jenis 'M' (MJKN) ke enum agar punya nomor urut sendiri (M0001, M0002 ...)
ALTER TABLE antrian
  MODIFY COLUMN jenis ENUM('P','F','M') NOT NULL;

ALTER TABLE last_called
  MODIFY COLUMN jenis ENUM('P','F','M') NOT NULL;

-- 3. Seed baris M di last_called agar display tidak error saat belum ada panggilan MJKN
INSERT IGNORE INTO last_called (jenis, kode, loket) VALUES ('M', '-', NULL);
