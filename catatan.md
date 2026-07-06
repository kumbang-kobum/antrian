# Catatan Sistem Antrian — RSU Handayani Kotabumi

> Dikembangkan oleh: Chandra Irawan, M.T.I  
> Database utama: `antrian_db` | Database SIK (SIMRS Khanza): `sik`  
> Stack: PHP 8, MariaDB 10.4, JavaScript (Vanilla)

---

## Daftar Isi
1. [Gambaran Sistem](#1-gambaran-sistem)
2. [Struktur Direktori](#2-struktur-direktori)
3. [Instalasi](#3-instalasi)
4. [Konfigurasi](#4-konfigurasi)
5. [Database](#5-database)
6. [Cara Penggunaan](#6-cara-penggunaan)
7. [API Internal](#7-api-internal)
8. [Catatan Teknis](#8-catatan-teknis)

---

## 1. Gambaran Sistem

Sistem antrian digital untuk RSU Handayani Kotabumi. Mendukung tiga jalur pengambilan nomor antrian:

| Jalur | Kode Tiket | Loket | Keterangan |
|---|---|---|---|
| Pendaftaran Walk-in | `P0001` – `P9999` | 1 – 5 | Pasien datang langsung tanpa daftar online |
| Fisioterapi Walk-in | `F0001` – `F9999` | 6 – 8 | Pasien fisioterapi datang langsung |
| MJKN (Mobile JKN) | `P0001` – `P9999` | 1 – 5 | Pasien yang sudah daftar via aplikasi BPJS Mobile JKN |

Pasien MJKN masuk ke antrian loket yang sama dengan walk-in pendaftaran (jenis `P`), namun tiket cetaknya menampilkan **dua nomor**:
- **No. Urutan Panggil Loket** — kode antrian sistem (misal `P0015`), untuk dipanggil petugas
- **No. Antrian MJKN** — kode gabungan `kode_poli_bpjs-no_urut` (misal `ANA-3`), sebagai referensi dari aplikasi

---

## 2. Struktur Direktori

```
antrian/
├── index.php                    # Portal / menu utama
├── config/
│   └── database.php             # Koneksi antrian_db dan sik
├── ambil/
│   ├── index.php                # Pilih jenis antrian
│   ├── simpan.php               # Buat antrian walk-in (P/F), auto-redirect cetak
│   ├── cetak.php                # Cetak tiket walk-in
│   ├── mjkn.php                 # Form input + lookup MJKN ke SIK
│   └── cetak_mjkn.php           # Cetak tiket MJKN
├── panggil/
│   ├── index.php                # Layar display gabungan (P + F) untuk TV
│   ├── pendaftaran.php          # Layar display khusus antrian pendaftaran
│   ├── fisioterapi.php          # Layar display khusus antrian fisioterapi
│   ├── admin.php                # Panel panggil gabungan (admin)
│   ├── admin_pendaftaran.php    # Panel panggil khusus pendaftaran
│   ├── admin_fisioterapi.php    # Panel panggil khusus fisioterapi
│   └── update.php               # Helper update status dari panel admin
├── api/
│   ├── call.php                 # Panggil nomor antrian → update status + last_called
│   ├── finish.php               # Tandai antrian selesai
│   ├── list_queue.php           # Daftar antrian aktif (menunggu/dipanggil)
│   ├── get_display.php          # Data lengkap untuk layar display (polling)
│   └── stats.php                # Statistik harian
├── assets/
│   ├── css/global.css           # Style global
│   ├── css/print.css            # Style cetak tiket (75mm)
│   ├── js/tts.js                # Text-to-Speech pemanggilan
│   └── video/edukasi.mp4        # Video edukatif di layar display
└── sql/
    ├── init.sql                 # Skema lengkap untuk instalasi baru
    └── mjkn_migration.sql       # Migrasi untuk instalasi yang sudah berjalan
```

---

## 3. Instalasi

### Prasyarat
- PHP 8.0 atau lebih baru (dengan ekstensi `mysqli`)
- MariaDB 10.4 / MySQL 8 atau lebih baru
- Web server (Apache/Nginx) dengan `mod_rewrite` aktif
- Akses baca ke database `sik` (SIMRS Khanza) dari server yang sama

### A. Instalasi Baru (belum ada database `antrian_db`)

**Langkah 1 — Buat database**
```sql
CREATE DATABASE antrian_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Langkah 2 — Import skema**
```bash
mysql -u root -p antrian_db < sql/init.sql
```
Atau buka `sql/init.sql` di phpMyAdmin dan klik **Eksekusi**.

**Langkah 3 — Salin file ke web server**
```bash
cp -r antrian/ /var/www/html/antrian
```

**Langkah 4 — Sesuaikan konfigurasi** (lihat [Bagian 4](#4-konfigurasi))

---

### B. Upgrade dari Versi Sebelumnya (antrian_db sudah ada, belum ada kolom MJKN)

Jalankan migrasi satu kali:
```bash
mysql -u root -p antrian_db < sql/mjkn_migration.sql
```
Atau via phpMyAdmin → tab SQL, jalankan:
```sql
ALTER TABLE antrian
  ADD COLUMN no_reg_mjkn VARCHAR(20) NULL DEFAULT NULL AFTER jenis;

ALTER TABLE antrian
  ADD UNIQUE KEY uq_mjkn_per_hari (tgl, no_reg_mjkn);
```

> **Catatan:** Jika kolom `no_reg_mjkn` sudah ada (pernah dijalankan sebelumnya), lewati langkah ini.

---

### C. Verifikasi Instalasi

Buka browser dan akses:
```
http://localhost/antrian/
```
Pastikan menu utama tampil. Lalu uji tiap jalur:
- Klik **Ambil Nomor Antrian** → pilih Pendaftaran → tiket cetak muncul ✓
- Klik **Ambil Nomor Antrian** → pilih MJKN → masukkan kode poli dan nomor → tiket cetak muncul ✓
- Buka **Display Gabungan** → tampil layar TV ✓
- Buka **Menu Panggil (Admin)** → panggil nomor → suara TTS berbunyi ✓

---

## 4. Konfigurasi

File: `config/database.php`

```php
/* Database antrian (wajib) */
$DB_HOST = 'localhost';       // host database
$DB_USER = 'root';            // user MySQL
$DB_PASS = 'password_anda';   // password MySQL
$DB_NAME = 'antrian_db';      // nama database

/* Database SIK — untuk fitur MJKN (opsional) */
$DB_HOST_SIK = 'localhost';   // ganti jika SIK di server berbeda
$DB_USER_SIK = 'root';        // user MySQL untuk SIK
$DB_PASS_SIK = 'password_sik';// password SIK (ganti jika berbeda)
$DB_NAME_SIK = 'sik';         // nama database SIMRS Khanza
```

> Jika koneksi ke `sik` gagal, variabel `$conn_sik` menjadi `null`.  
> Semua halaman selain MJKN **tidak terpengaruh** — hanya halaman MJKN yang menampilkan pesan error ke pasien.

---

## 5. Database

### Database: `antrian_db`

#### Tabel `antrian`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | int, PK, AUTO_INCREMENT | |
| `tgl` | date | Tanggal antrian |
| `jenis` | enum('P','F') | P = Pendaftaran, F = Fisioterapi |
| `no_reg_mjkn` | varchar(20), NULL | Kosong jika walk-in. Berisi `KD_BPJS-NOURUT` jika MJKN, misal `ANA-3` |
| `nomor` | int | Nomor urut antrian hari itu per jenis |
| `status` | enum('menunggu','dipanggil','selesai') | Default: menunggu |
| `loket` | tinyint, NULL | Loket yang memanggil |
| `created_at` | timestamp | Waktu ambil antrian |
| `called_at` | datetime, NULL | Waktu pertama dipanggil |
| `finished_at` | datetime, NULL | Waktu selesai dilayani |

Index:
- `PRIMARY KEY (id)`
- `KEY (tgl, jenis, status)` — untuk query antrian aktif hari ini
- `UNIQUE KEY uq_mjkn_per_hari (tgl, no_reg_mjkn)` — cegah pasien MJKN dapat 2 nomor

#### Tabel `last_called`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `jenis` | enum('P','F'), PK | |
| `kode` | varchar(10) | Kode terakhir dipanggil, misal `P0015` |
| `loket` | tinyint | Loket yang memanggil |
| `updated_at` | timestamp, ON UPDATE | Layar display memantau kolom ini untuk deteksi panggilan baru |

---

### Hak Akses ke Database `sik`

Sistem ini **hanya membaca** dari `sik`. Sebaiknya buat user MySQL khusus dengan `SELECT` terbatas pada tabel yang diperlukan saja — jangan gunakan user `root` di production.

Jalankan perintah berikut di MySQL sebagai admin:

```sql
-- Buat user khusus read-only untuk sistem antrian
CREATE USER 'antrian_ro'@'localhost' IDENTIFIED BY 'password_aman';

-- Berikan SELECT hanya pada tabel yang dibutuhkan
GRANT SELECT ON sik.reg_periksa               TO 'antrian_ro'@'localhost';
GRANT SELECT ON sik.pasien                    TO 'antrian_ro'@'localhost';
GRANT SELECT ON sik.poliklinik                TO 'antrian_ro'@'localhost';
GRANT SELECT ON sik.dokter                    TO 'antrian_ro'@'localhost';
GRANT SELECT ON sik.maping_poli_bpjs          TO 'antrian_ro'@'localhost';
GRANT SELECT ON sik.referensi_mobilejkn_bpjs  TO 'antrian_ro'@'localhost';

FLUSH PRIVILEGES;
```

Kemudian sesuaikan `config/database.php`:
```php
$DB_USER_SIK = 'antrian_ro';
$DB_PASS_SIK = 'password_aman';
```

> Jika database `sik` berada di server berbeda, ganti `'localhost'` dengan IP server tersebut dan pastikan port MySQL (3306) dapat diakses dari server antrian.

---

### Database: `sik` (hanya baca — SIMRS Khanza)

Tabel yang diakses:

| Tabel | Kolom yang dipakai | Keterangan |
|---|---|---|
| `reg_periksa` | `no_reg`, `no_rawat`, `tgl_registrasi`, `jam_reg`, `kd_dokter`, `no_rkm_medis`, `kd_poli` | Data pendaftaran pasien |
| `pasien` | `no_rkm_medis`, `nm_pasien` | Nama pasien |
| `poliklinik` | `kd_poli`, `nm_poli` | Nama poli internal RS |
| `dokter` | `kd_dokter`, `nm_dokter` | Nama dokter |
| `maping_poli_bpjs` | `kd_poli_rs`, `kd_poli_bpjs`, `nm_poli_bpjs` | Pemetaan kode poli RS → kode poli BPJS |
| `referensi_mobilejkn_bpjs` | `no_rawat`, `nobooking` | Bukti pasien daftar via Mobile JKN |

Query lookup MJKN:
```sql
SELECT rp.no_reg, p.nm_pasien, pk.nm_poli
FROM reg_periksa rp
INNER JOIN pasien           p   ON p.no_rkm_medis  = rp.no_rkm_medis
INNER JOIN poliklinik       pk  ON pk.kd_poli       = rp.kd_poli
INNER JOIN dokter           d   ON d.kd_dokter      = rp.kd_dokter
INNER JOIN maping_poli_bpjs mpb ON mpb.kd_poli_rs   = rp.kd_poli
INNER JOIN (
  SELECT no_rawat, MAX(nobooking) AS nobooking
  FROM referensi_mobilejkn_bpjs GROUP BY no_rawat
) rb ON rb.no_rawat = rp.no_rawat
WHERE mpb.kd_poli_bpjs  = ?   -- kode poli BPJS dari input (misal 'ANA')
  AND rp.no_reg         = ?   -- nomor urut dari input (misal '3')
  AND rp.tgl_registrasi = CURDATE()
```
`INNER JOIN` ke `referensi_mobilejkn_bpjs` memastikan hanya pasien yang benar-benar daftar via MJKN (`nobooking` tidak null) yang bisa menggunakan jalur ini.

---

## 6. Cara Penggunaan

### 6.1 Pasien — Ambil Nomor Antrian Walk-in

1. Buka `http://[server]/antrian/` atau langsung `ambil/`
2. Pilih **Pendaftaran Pasien** (Loket 1–5) atau **Pendaftaran Pasien Fisioterapi** (Loket 6–8)
3. Tiket cetak otomatis tampil dan dicetak — berisi kode antrian, tanggal/jam
4. Tunggu panggilan di layar display

### 6.2 Pasien — Ambil Nomor Antrian MJKN

1. Buka aplikasi **Mobile JKN** (BPJS Kesehatan) di ponsel
2. Lihat **nomor urut** dan **kode poli BPJS** pada jadwal kunjungan hari ini  
   _(contoh: Poli Anak → kode `ANA`, nomor urut `3`)_
3. Di kiosk/komputer, buka `http://[server]/antrian/` → klik **Ambil Antrian MJKN**
4. Isi dua kolom:
   - **Kode Poli**: `ANA` _(huruf kapital, sesuai yang tertera di aplikasi)_
   - **No. Urut**: `3`
5. Klik **Ambil Nomor Antrian**
6. Tiket cetak tampil dan dicetak, berisi:
   - **No. Urutan Panggil Loket** (misal `P0015`) — gunakan ini untuk pantau display
   - **No. Antrian MJKN** (misal `ANA-3`) — referensi dari aplikasi
   - Nama pasien dan nama poliklinik
7. Jika kode dan nomor dimasukkan dua kali, sistem memberikan nomor yang **sama** (tidak dobel)

### 6.3 Petugas Loket — Memanggil Antrian

1. Buka `panggil/admin_pendaftaran.php` (untuk loket pendaftaran) atau  
   `panggil/admin_fisioterapi.php` (untuk loket fisioterapi) atau  
   `panggil/admin.php` (panel gabungan)
2. Pastikan nomor loket sudah terisi di kolom **Loket**
3. Klik tombol **Panggil** pada nomor antrian yang ingin dipanggil
4. Layar display otomatis memperbarui dan suara TTS berbunyi
5. Setelah pasien selesai dilayani, klik **Selesai** agar nomor hilang dari daftar

### 6.4 Admin — Layar Display TV

Buka salah satu URL berikut di browser layar TV (mode fullscreen / F11):

| URL | Tampilan |
|---|---|
| `panggil/index.php` | Display gabungan: video edukatif + sidebar antrian P dan F |
| `panggil/pendaftaran.php` | Display khusus antrian pendaftaran (P) |
| `panggil/fisioterapi.php` | Display khusus antrian fisioterapi (F) |

> Klik tombol **Aktifkan Suara** satu kali setelah halaman dibuka agar TTS berfungsi.  
> Layar display memperbarui data otomatis setiap **2 detik** tanpa perlu refresh manual.

---

## 7. API Internal

Semua endpoint bertipe JSON, di direktori `api/`.

| Endpoint | Method | Body / Params | Respons |
|---|---|---|---|
| `call.php` | POST | `{id, loket}` | `{ok, kode, loket}` |
| `finish.php` | POST | `{id}` | `{ok}` |
| `list_queue.php` | GET | `?jenis=P` atau `?jenis=F` | `{data: [{id, kode, status, created_at}]}` |
| `get_display.php` | GET | — | `{last: {P,F}, waiting: {P,F}, time}` |
| `stats.php` | GET | — | Statistik jumlah per status per jenis |

---

## 8. Catatan Teknis

- **TTS (Text-to-Speech):** Menggunakan Web Speech API (`window.speechSynthesis`). Wajib ada interaksi pengguna (klik tombol "Aktifkan Suara") sebelum TTS bisa berjalan — ini adalah kebijakan keamanan browser modern, bukan bug.
- **Polling display:** Interval 2 detik. Perubahan terdeteksi dari kolom `updated_at` pada tabel `last_called` (bukan dari query ulang seluruh antrian), sehingga ringan di database.
- **Duplikat MJKN:** Jika pasien memasukkan kode yang sama dua kali, sistem mendeteksi via `UNIQUE KEY (tgl, no_reg_mjkn)` dan mengembalikan nomor antrian yang sudah ada — bukan membuat baru.
- **Keunikan `no_reg_mjkn`:** Format `KD_BPJS-NOURUT` (contoh `ANA-3`) unik per poli per hari. Poli berbeda memiliki kode BPJS berbeda sehingga nomor urut yang sama di dua poli tidak bertabrakan.
- **Keamanan input:** Semua input diproses dengan `prepared statement` (cegah SQL injection) dan `htmlspecialchars` (cegah XSS).
- **Timezone:** Semua waktu WIB (`Asia/Jakarta` / `+07:00`), diset di `config/database.php` dan `date_default_timezone_set`.
- **Database SIK hanya dibaca:** Sistem ini tidak pernah menulis ke database `sik`. Seluruh data antrian disimpan di `antrian_db`.
- **Auto-cleanup:** Data antrian lebih dari 30 hari dihapus otomatis setiap kali ada pasien walk-in ambil nomor (`simpan.php`). Tidak perlu cron job.

### Riwayat Perubahan

| Commit | Perubahan |
|---|---|
| `5755350` | Instalasi awal |
| `25e4ff9` | Antrian poli dan fisioterapi |
| `1494ce0` | Penyesuaian konfigurasi database |
| _(update)_ | Tambah jalur antrian MJKN, integrasi DB `sik`, format input `KD_BPJS-NOURUT` |
