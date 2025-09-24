<?php
require_once '../config/database.php';

/**
 * API get_display.php
 * Menyediakan data untuk display:
 * - last: nomor terakhir dipanggil per jenis (P, F) + loket + updated_at
 * - waiting: daftar menunggu per jenis (P, F)
 * - time: jam server HH:ii
 */

$tgl = date('Y-m-d');

$resp = [
  'last' => [
    'P' => ['kode' => '-', 'loket' => null, 'updated_at' => null],
    'F' => ['kode' => '-', 'loket' => null, 'updated_at' => null]
  ],
  'waiting' => ['P' => [], 'F' => []],
  'time' => date('H:i')
];

/* ========================
   Ambil data terakhir dari last_called
   ======================== */
$res = $conn->query("SELECT jenis,kode,loket, DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i:%s') AS updated_at FROM last_called");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $jenis = $row['jenis'];
    if (isset($resp['last'][$jenis])) {
      $resp['last'][$jenis] = [
        'kode' => $row['kode'],
        'loket' => $row['loket'],
        'updated_at' => $row['updated_at']
      ];
    }
  }
}

/* ========================
   Daftar menunggu dari tabel antrian
   ======================== */
$stmt = $conn->prepare("
  SELECT id, nomor, created_at 
  FROM antrian 
  WHERE tgl=? AND jenis=? AND status='menunggu' 
  ORDER BY id ASC LIMIT 20
");
if ($stmt) {
  foreach (['P','F'] as $J) {
    $stmt->bind_param('ss', $tgl, $J);
    $stmt->execute();
    $q = $stmt->get_result();
    $tmp = [];
    while ($d = $q->fetch_assoc()) {
      $tmp[] = [
        'id' => (int)$d['id'],
        'kode' => $J . str_pad($d['nomor'], 4, '0', STR_PAD_LEFT),
        'created_at' => date('H:i', strtotime($d['created_at']))
      ];
    }
    $resp['waiting'][$J] = $tmp;
  }
}

json_response($resp);