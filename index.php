<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Menu Utama Antrian</title>
  <link rel="stylesheet" href="assets/css/global.css">
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
      max-width: 720px;
      width: 90%;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
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
      padding: 20px 14px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 700;
      font-size: 15px;
      transition: all .25s ease;
      box-shadow: 0 4px 12px rgba(0,0,0,.2);
      color: #fff;
    }
    a.btn span {
      font-size: 28px;
      margin-bottom: 6px;
    }
    .ambil {background:#2196f3;}
    .display {background:#ff9800;}
    .admin {background:#43a047;}
    a.btn:hover {
      transform: translateY(-4px) scale(1.03);
      box-shadow: 0 8px 20px rgba(0,0,0,.3);
      filter: brightness(1.05);
    }
    /* Responsif di layar kecil */
    @media(max-width:600px){
      .grid {grid-template-columns: 1fr;}
      a.btn {font-size: 16px;}
    }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>📋 Sistem Antrian Pasien</h1>
    <p>Pilih menu utama di bawah ini:</p>
    <div class="grid">
      <a class="btn ambil" href="ambil/">
        <span>🎫</span>
        Ambil Nomor Antrian
      </a>
      <a class="btn display" href="panggil/index.php">
        <span>📺</span>
        Display Gabungan
      </a>
      <a class="btn display" href="panggil/pendaftaran.php">
        <span>🧑‍⚕️</span>
        Display Pendaftaran
      </a>
      <a class="btn display" href="panggil/fisioterapi.php">
        <span>💪</span>
        Display Fisioterapi
      </a>
      <a class="btn admin" href="panggil/admin.php">
        <span>🗣️</span>
        Menu Panggil (Admin Gabung)
      </a>
      <a class="btn admin" href="panggil/admin_pendaftaran.php">
        <span>🗣️</span>
        Menu Panggil (Admin Pendaftaran)
      </a>
      <a class="btn admin" href="panggil/admin_fisioterapi.php">
        <span>🗣️</span>
        Menu Panggil (Admin Fisioterapi)
      </a>
    </div>
  </div>
  <div class="cc">
    © <?=date('Y')?> Chandra Irawan,M.T.I — Sistem Antrian RSU Handayani Kotabumi
  </div>
</body>
</html>