<?php
date_default_timezone_set('Asia/Jakarta');
$kode = $_GET['kode'] ?? '';
$jenis = $_GET['jenis'] ?? 'P';
$label = $jenis === 'F' ? 'Antrian Fisioterapi' : 'Antrian Pendaftaran';
$now = date('d/m/Y H:i');
?>
<!doctype html>
<html lang="id">
<head>
  <link rel="stylesheet" href="assets/css/global.css">  
  <meta charset="utf-8"/>
  <title>Cetak Nomor - <?= htmlspecialchars($kode) ?></title>
  <link rel="stylesheet" href="../assets/css/print.css">
</head>
<body>
  <div class="ticket">
    <div class="title">RSU Handayani Kotabumi</div>
    <div><?= htmlspecialchars($label) ?></div>
    <div class="big"><?= htmlspecialchars($kode) ?></div>
    <div class="meta">Tgl/Jam: <?= $now ?></div>
    <div class="meta">Harap menunggu panggilan.</div>
    <div class="no-print">
      <a class="btn" href="#" onclick="window.print()">Cetak</a>
      <a class="btn" href="index.php">Kembali</a>
    </div>
  </div>
  <script>window.print();</script>
</body>
</html>