<?php
require_once '../config/database.php';
$jenis = isset($_GET['jenis']) && in_array($_GET['jenis'], ['P','F','M']) ? $_GET['jenis'] : 'P';
$tgl   = date('Y-m-d');

/* Pendaftaran (P) dan MJKN (M) berbagi loket 1-5, tampilkan bersama */
$clause = ($jenis === 'P') ? "jenis IN ('P','M')" : "jenis = 'F'";

$stmt = $conn->prepare("SELECT id, nomor, jenis, status, no_reg_mjkn, created_at
  FROM antrian
  WHERE tgl=? AND $clause AND status IN ('menunggu','dipanggil')
  ORDER BY FIELD(status,'dipanggil','menunggu'), id ASC LIMIT 50");
$stmt->bind_param('s', $tgl);
$stmt->execute();
$q = $stmt->get_result();
$data = [];
while($d = $q->fetch_assoc()){
  $data[] = [
    'id'          => $d['id'],
    'kode'        => $d['jenis'] . str_pad($d['nomor'],4,'0',STR_PAD_LEFT),
    'status'      => $d['status'],
    'no_reg_mjkn' => $d['no_reg_mjkn'],
    'created_at'  => date('H:i', strtotime($d['created_at']))
  ];
}
json_response(['data'=>$data]);