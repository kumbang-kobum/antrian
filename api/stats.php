<?php
require_once '../config/database.php';
$tgl = date('Y-m-d');

function countStatus($conn,$tgl,$jenis,$status){
  $stmt = $conn->prepare("SELECT COUNT(*) c FROM antrian WHERE tgl=? AND jenis=? AND status=?");
  $stmt->bind_param('sss',$tgl,$jenis,$status);
  $stmt->execute();
  return (int)$stmt->get_result()->fetch_assoc()['c'];
}

json_response([
  'P'=>[
    'menunggu'=>countStatus($conn,$tgl,'P','menunggu'),
    'dipanggil'=>countStatus($conn,$tgl,'P','dipanggil'),
    'selesai'=>countStatus($conn,$tgl,'P','selesai'),
  ],
  'F'=>[
    'menunggu'=>countStatus($conn,$tgl,'F','menunggu'),
    'dipanggil'=>countStatus($conn,$tgl,'F','dipanggil'),
    'selesai'=>countStatus($conn,$tgl,'F','selesai'),
  ]
]);