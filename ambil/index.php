<?php /* Halaman ambil nomor antrian */ ?>
<!doctype html>
<html lang="id">
<head>
  <link rel="stylesheet" href="../assets/css/global.css">
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Ambil Nomor Antrian</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      min-height: 100vh;
      align-items: center;
      justify-content: center;
      margin: 0;
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .wrap {
      max-width: 560px;
      width: 90%;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,.25);
      padding: 32px 24px;
      text-align: center;
      color: #fff;
    }

    h1 {
      margin: 0 0 12px;
      font-size: 26px;
      font-weight: 800;
    }

    p {
      color: #f0f0f0;
      margin-top: 0;
      font-size: 15px;
      opacity: .9;
    }

    .grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
      margin-top: 20px;
    }

    a.btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 20px 14px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 700;
      font-size: 15px;
      transition: all .25s ease;
      box-shadow: 0 4px 12px rgba(0,0,0,.2);
    }

    .p {background:#2196f3;color:#fff;}
    .f {background:#4caf50;color:#fff;}

    a.btn span {
      display:block;
      font-size:30px;
      margin-bottom:8px;
    }

    a.btn:hover {
      transform: translateY(-4px) scale(1.03);
      box-shadow: 0 8px 20px rgba(0,0,0,.3);
      filter: brightness(1.05);
    }

    .top-bar {
      margin-bottom: 20px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="top-bar">
      <a href="../index.php" class="btn-home">🏠 Home</a>
    </div>

    <h1>Ambil Nomor Antrian</h1>
    <p>Silakan pilih layanan yang Anda butuhkan:</p>

    <div class="grid">
      <a class="btn p" href="simpan.php?jenis=P">
        <span>👥</span>
        Pendaftaran Pasien<br>(Loket 1–5)
      </a>
      <a class="btn f" href="simpan.php?jenis=F">
        <span>💪</span>
        Pendaftaran Pasien Fisioterapi<br>(Loket 6–8)
      </a>
    </div>

    <!-- Footer CC -->
    <div class="cc">
      © <?=date('Y')?> Chandra Irawan,M.T.I — Sistem Antrian RSU Handayani Kotabumi
    </div>
  </div>
</body>
</html>