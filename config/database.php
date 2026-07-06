<?php
/*
 * Konfigurasi koneksi database.
 * Nilai sensitif dibaca dari config/.env.php yang TIDAK masuk git (.gitignore).
 * Salin config/.env.example.php → config/.env.php lalu isi nilainya.
 */
$env = require __DIR__ . '/.env.php';

$conn = new mysqli($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);
if ($conn->connect_error) {
  error_log('antrian DB connect error: ' . $conn->connect_error);
  die('Layanan sementara tidak tersedia. Hubungi petugas IT.');
}
$conn->set_charset('utf8mb4');

/*
 * Koneksi SIK dibuat HANYA saat dipanggil get_conn_sik() — tidak di setiap halaman.
 * Timeout 3 detik agar jika server SIK down, halaman tidak hang lama.
 */
$conn_sik = null;
function get_conn_sik() {
  global $conn_sik, $env;
  if ($conn_sik !== null) return $conn_sik;
  $m = mysqli_init();
  $m->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);
  $ok = @$m->real_connect($env['SIK_HOST'], $env['SIK_USER'], $env['SIK_PASS'], $env['SIK_NAME']);
  if (!$ok || $m->connect_error) return null;
  $m->set_charset('utf8mb4');
  $conn_sik = $m;
  return $conn_sik;
}

/* Set timezone ke WIB */
date_default_timezone_set('Asia/Jakarta');
$conn->query("SET time_zone = '+07:00'");

function json_response($data, $code=200) {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}
