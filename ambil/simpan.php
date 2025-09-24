<?php

require_once '../config/database.php';

$jenis = isset($_GET['jenis']) && in_array($_GET['jenis'], ['P','F']) ? $_GET['jenis'] : 'P';
$tgl = date('Y-m-d');

/* 🔥 AUTO-CLEANUP: hapus data lebih dari 30 hari */
$conn->query("DELETE FROM antrian WHERE tgl < DATE_SUB(CURDATE(), INTERVAL 30 DAY)");

$stmt = $conn->prepare("SELECT COALESCE(MAX(nomor),0) AS last FROM antrian WHERE tgl=? AND jenis=?");
$stmt->bind_param('ss',$tgl,$jenis);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$next = (int)$res['last'] + 1;

$stmt2 = $conn->prepare("INSERT INTO antrian (tgl, jenis, nomor) VALUES (?,?,?)");
$stmt2->bind_param('ssi',$tgl,$jenis,$next);
$stmt2->execute();
$id = $stmt2->insert_id;

$kode = $jenis . str_pad($next,4,'0',STR_PAD_LEFT);
header('Location: cetak.php?id='.$id.'&kode='.$kode.'&jenis='.$jenis);
exit;