<?php
/*
 * Salin file ini menjadi config/.env.php lalu isi nilainya.
 * File .env.php TIDAK boleh masuk git (sudah ada di .gitignore).
 */
return [
  // Database antrian (wajib)
  'DB_HOST' => 'localhost',
  'DB_USER' => 'root',
  'DB_PASS' => 'ganti_password_anda',
  'DB_NAME' => 'antrian_db',

  // Database SIK / SIMRS Khanza (untuk fitur MJKN)
  'SIK_HOST' => '192.168.x.x',   // IP server SIK
  'SIK_USER' => 'antrian_ro',
  'SIK_PASS' => 'ganti_password_sik',
  'SIK_NAME' => 'sik',
];
