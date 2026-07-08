<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Sistem Antrian — RSU Handayani Kotabumi</title>
  <link rel="stylesheet" href="assets/css/global.css">
</head>
<body class="home-page">
  <div class="home-card card">
    <div class="home-icon">🏥</div>
    <h1>RSU Handayani Kotabumi</h1>
    <p class="subtitle">Sistem Antrian Pasien — pilih menu yang Anda butuhkan</p>

    <div class="nav-section">
      <div class="nav-section-label">Pasien</div>
      <div class="nav-grid">
        <a class="nav-btn nav-blue" href="ambil/" style="grid-column:1/-1">
          <span class="icon">🎫</span>
          Ambil Nomor Antrian
        </a>
      </div>
    </div>

    <div class="nav-section">
      <div class="nav-section-label">Layar Display</div>
      <div class="nav-grid-3">
        <a class="nav-btn nav-orange" href="panggil/index.php">
          <span class="icon">📺</span>
          Gabungan
        </a>
        <a class="nav-btn nav-orange" href="panggil/pendaftaran.php">
          <span class="icon">🧑‍⚕️</span>
          Pendaftaran
        </a>
        <a class="nav-btn nav-orange" href="panggil/fisioterapi.php">
          <span class="icon">💪</span>
          Fisioterapi
        </a>
      </div>
    </div>

    <div class="nav-section" style="margin-bottom:0">
      <div class="nav-section-label">Panel Admin & Pengaturan</div>
      <div class="nav-grid">
        <a class="nav-btn nav-green" href="panggil/admin.php">
          <span class="icon">🗣️</span>
          Admin Gabungan
        </a>
        <a class="nav-btn nav-green" href="panggil/admin_pendaftaran.php">
          <span class="icon">🗣️</span>
          Admin Pendaftaran
        </a>
        <a class="nav-btn nav-green" href="panggil/admin_fisioterapi.php">
          <span class="icon">🗣️</span>
          Admin Fisioterapi
        </a>
        <a class="nav-btn nav-blue" href="panggil/upload_video.php">
          <span class="icon">🎬</span>
          Upload Video Display
        </a>
      </div>
    </div>
  </div>

  <div class="cc">
    © <?=date('Y')?> Chandra Irawan, M.T.I — Sistem Antrian RSU Handayani Kotabumi
  </div>
</body>
</html>
