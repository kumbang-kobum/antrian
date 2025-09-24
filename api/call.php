<?php
require_once '../config/database.php';

/**
 * API call.php
 * Body JSON: { id: int, loket: int }
 * Efek:
 *  - Jika status=menunggu → ubah jadi dipanggil
 *  - Jika sudah dipanggil → update ulang (untuk panggil ulang)
 *  - Selalu update tabel last_called (updated_at berubah setiap panggilan)
 */

$payload = json_decode(file_get_contents('php://input'), true);
$id = (int)($payload['id'] ?? 0);
$loket = (int)($payload['loket'] ?? 0);

if ($id <= 0 || $loket <= 0) {
  json_response(['ok'=>false,'msg'=>'Param tidak valid'], 400);
}

$conn->begin_transaction();

try {
  $stmt = $conn->prepare("SELECT id, tgl, jenis, nomor, status FROM antrian WHERE id=? FOR UPDATE");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $res = $stmt->get_result();
  if (!$res || $res->num_rows === 0) throw new Exception('Data antrian tidak ditemukan');
  $row = $res->fetch_assoc();

  if ($row['status'] === 'selesai') throw new Exception('Antrian sudah selesai');

  if ($row['status'] === 'menunggu') {
    $stmt = $conn->prepare("UPDATE antrian SET status='dipanggil', loket=?, called_at=NOW() WHERE id=?");
    $stmt->bind_param('ii', $loket, $id);
    $stmt->execute();
  } else {
    $stmt = $conn->prepare("UPDATE antrian SET loket=? WHERE id=?");
    $stmt->bind_param('ii', $loket, $id);
    $stmt->execute();
  }

  $jenis = $row['jenis'];
  $kode = $jenis . str_pad($row['nomor'], 4, '0', STR_PAD_LEFT);

  $stmt2 = $conn->prepare("
    INSERT INTO last_called (jenis,kode,loket) VALUES (?,?,?)
    ON DUPLICATE KEY UPDATE kode=VALUES(kode), loket=VALUES(loket), updated_at=CURRENT_TIMESTAMP
  ");
  $stmt2->bind_param('ssi', $jenis, $kode, $loket);
  $stmt2->execute();

  $conn->commit();
  json_response(['ok'=>true, 'kode'=>$kode, 'loket'=>$loket]);
} catch (Exception $e) {
  $conn->rollback();
  json_response(['ok'=>false, 'msg'=>$e->getMessage()], 400);
}