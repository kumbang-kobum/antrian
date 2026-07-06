<?php
date_default_timezone_set('Asia/Jakarta');
$kode  = $_GET['kode']  ?? '';
$jenis = $_GET['jenis'] ?? 'P';
$label = $jenis === 'F' ? 'Antrian Fisioterapi' : 'Antrian Pendaftaran';
$now   = date('d/m/Y H:i');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Nomor Antrian — <?= htmlspecialchars($kode) ?></title>
  <link rel="stylesheet" href="../assets/css/print.css">
</head>
<body>
  <div class="ticket">
    <div class="ticket-rs">RSU Handayani Kotabumi</div>
    <div class="ticket-unit"><?= htmlspecialchars($label) ?></div>
    <hr class="ticket-divider">
    <div class="ticket-label">Nomor Antrian Anda</div>
    <div class="ticket-kode"><?= htmlspecialchars($kode) ?></div>
    <hr class="ticket-divider">
    <div class="ticket-meta">Tgl/Jam: <?= $now ?></div>
    <div class="ticket-note">Harap menunggu panggilan petugas.</div>
    <div class="no-print">
      <a href="#" onclick="window.print()">Cetak</a>
      <a href="index.php">Kembali</a>
    </div>
  </div>
  <script>window.print();</script>
</body>
</html>
