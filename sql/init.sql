CREATE DATABASE IF NOT EXISTS antrian_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE antrian_db;

CREATE TABLE IF NOT EXISTS antrian (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tgl DATE NOT NULL DEFAULT (CURRENT_DATE),
  jenis ENUM('P','F') NOT NULL,               -- P = Pendaftaran Umum, F = Fisioterapi
  nomor INT NOT NULL,                         -- nomor urut (reset harian)
  status ENUM('menunggu','dipanggil','selesai') NOT NULL DEFAULT 'menunggu',
  loket TINYINT NULL,                         -- loket yang memanggil (1-8)
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  called_at DATETIME NULL,
  finished_at DATETIME NULL,
  INDEX (tgl, jenis, status)
);

-- Menyimpan "terakhir dipanggil" per jenis agar display mudah ambil
CREATE TABLE IF NOT EXISTS last_called (
  jenis ENUM('P','F') PRIMARY KEY,
  kode VARCHAR(10) NOT NULL,                  -- misal P0001 / F0003
  loket TINYINT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT IGNORE INTO last_called (jenis,kode,loket) VALUES ('P','-',NULL),('F','-',NULL);