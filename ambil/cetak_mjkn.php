<?php
date_default_timezone_set('Asia/Jakarta');

$kode        = htmlspecialchars($_GET['kode']        ?? '');
$no_reg_mjkn = htmlspecialchars($_GET['no_reg_mjkn'] ?? '');
$nm          = htmlspecialchars($_GET['nm']           ?? '');
$poli        = htmlspecialchars($_GET['poli']         ?? '');
$now         = date('d/m/Y H:i');

if (!$kode || !$nm) {
    header('Location: mjkn.php');
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Cetak Antrian MJKN - <?= $kode ?></title>
  <link rel="stylesheet" href="../assets/css/print.css">
  <style>
    .label-mjkn { font-size: 12px; font-weight: 700; color: #e65100; margin: 2px 0 6px; }
    .info-row   { font-size: 12px; color: #333; margin: 3px 0; text-align: left; }
    .info-label { font-weight: 700; display: inline-block; width: 70px; }
    .divider    { border: none; border-top: 1px dashed #aaa; margin: 7px 0; }
    .sub-num    { font-size: 12px; color: #555; margin: 2px 0 4px; }
  </style>
</head>
<body>
  <div class="ticket">
    <div class="title">RSU Handayani Kotabumi</div>
    <div class="label-mjkn">📱 Mobile JKN — Rawat Jalan</div>
    <hr class="divider">

    <div style="font-size:11px;color:#666;margin-bottom:2px">No. Urutan Panggil Loket</div>
    <div class="big"><?= $kode ?></div>

    <div class="sub-num">No. Antrian MJKN : <strong><?= $no_reg_mjkn ?></strong></div>

    <hr class="divider">
    <div class="info-row"><span class="info-label">Nama</span>: <?= $nm ?></div>
    <div class="info-row"><span class="info-label">Poliklinik</span>: <?= $poli ?></div>
    <hr class="divider">

    <div class="meta">Dicetak : <?= $now ?></div>
    <div class="meta" style="margin-top:5px">Harap menunggu panggilan petugas.</div>

    <div class="no-print" style="margin-top:14px">
      <a class="btn" href="#" onclick="window.print()">Cetak</a>
      <a class="btn" href="mjkn.php">Kembali</a>
    </div>
  </div>
  <script>window.print();</script>
</body>
</html>
