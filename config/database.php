<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';          // sesuaikan
$DB_NAME = 'antrian_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
  die('Koneksi gagal: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

/* Set timezone ke WIB */
date_default_timezone_set('Asia/Jakarta');
$conn->query("SET time_zone = '+07:00'");

function json_response($data, $code=200) {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}