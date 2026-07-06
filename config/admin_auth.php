<?php
/*
 * Sertakan file ini di awal setiap halaman admin.
 * Membatasi akses berdasarkan daftar IP yang diizinkan di .env.php.
 */
if (!isset($env)) $env = require __DIR__ . '/.env.php';

$allowed = $env['ADMIN_IPS'] ?? [];

// Jika daftar kosong, akses terbuka (mode development)
if (!empty($allowed)) {
  $client = $_SERVER['HTTP_X_FORWARDED_FOR']
    ? trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0])
    : ($_SERVER['REMOTE_ADDR'] ?? '');

  if (!in_array($client, $allowed, true)) {
    http_response_code(403);
    die('403 Forbidden — Akses tidak diizinkan dari IP ini.');
  }
}
