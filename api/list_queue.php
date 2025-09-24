<?php
require_once '../config/database.php';
$jenis = isset($_GET['jenis']) && in_array($_GET['jenis'], ['P','F']) ? $_GET['jenis'] : 'P';
$tgl = date('Y-m-d');

$stmt = $conn->prepare("SELECT id, nomor, status, created_at FROM antrian WHERE tgl=? AND jenis=? AND status IN ('menunggu','dipanggil') ORDER BY FIELD(status,'dipanggil','menunggu'), id ASC LIMIT 50");
$stmt->bind_param('ss',$tgl,$jenis);
$stmt->execute();
$q = $stmt->get_result();
$data = [];
while($d = $q->fetch_assoc()){
  $data[] = [
    'id'=>$d['id'],
    'kode'=>$jenis . str_pad($d['nomor'],4,'0',STR_PAD_LEFT),
    'status'=>$d['status'],
    'created_at'=>date('H:i', strtotime($d['created_at']))
  ];
}
json_response(['data'=>$data]);