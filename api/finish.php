<?php
require_once '../config/database.php';
$payload = json_decode(file_get_contents('php://input'), true);
$id = (int)($payload['id'] ?? 0);
if($id<=0){ json_response(['ok'=>false,'msg'=>'Param tidak valid'],400); }

$stmt = $conn->prepare("UPDATE antrian SET status='selesai', finished_at=NOW() WHERE id=?");
$stmt->bind_param('i',$id);
$stmt->execute();

json_response(['ok'=> ($stmt->affected_rows>0) ]);