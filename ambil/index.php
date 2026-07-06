<?php /* Halaman ambil nomor antrian */ ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Ambil Nomor Antrian</title>
  <link rel="stylesheet" href="../assets/css/global.css">
</head>
<body class="home-page">
  <div class="home-card card">
    <div style="text-align:left;margin-bottom:20px">
      <a href="../index.php" class="btn-home">← Beranda</a>
    </div>

    <h1>Ambil Nomor Antrian</h1>
    <p class="subtitle">Silakan pilih layanan yang Anda butuhkan</p>

    <div class="nav-section">
      <div class="nav-section-label">Antrian Walk-in</div>
      <div class="nav-grid">
        <a class="nav-btn nav-blue" href="simpan.php?jenis=P">
          <span class="icon">👥</span>
          Pendaftaran Pasien
          <small style="font-weight:400;opacity:.8">Loket 1–5</small>
        </a>
        <a class="nav-btn nav-green" href="simpan.php?jenis=F">
          <span class="icon">💪</span>
          Fisioterapi
          <small style="font-weight:400;opacity:.8">Loket 6–8</small>
        </a>
      </div>
    </div>

    <div class="nav-section" style="margin-bottom:0">
      <div class="nav-section-label">Mobile JKN</div>
      <div class="nav-grid">
        <a class="nav-btn nav-orange" href="mjkn.php" style="grid-column:1/-1">
          <span class="icon">📱</span>
          Ambil Antrian MJKN
          <small style="font-weight:400;opacity:.8">Sudah daftar via aplikasi Mobile JKN</small>
        </a>
      </div>
    </div>
  </div>

  <div class="cc">
    © <?=date('Y')?> Chandra Irawan, M.T.I — Sistem Antrian RSU Handayani Kotabumi
  </div>
</body>
</html>
