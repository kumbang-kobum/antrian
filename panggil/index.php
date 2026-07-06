<?php /* Display Gabungan Antrian (Pendaftaran & Fisioterapi) */ ?>
<!doctype html>
<html lang="id">
<head>
  <link rel="stylesheet" href="../assets/css/global.css">
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Display Antrian Gabungan</title>
  <style>
    body {
      margin:0;
      font-family: 'Segoe UI', Arial, sans-serif;
      background:#0b1020;
      color:#fff;
    }
    .wrap {
      display:grid;
      grid-template-columns: 70% 30%;
      height:100vh;
    }
    .video {
      background:#000;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .video video {
      width:100%;
      height:100%;
      object-fit:cover;
    }
    .sidebar {
      display:flex;
      flex-direction:column;
      gap:16px;
      padding:12px;
      overflow-y:auto;
      background:#121a33;
    }
    .card {
      flex:1;
      background:#1b2447;
      border-radius:12px;
      padding:14px;
      display:flex;
      flex-direction:column;
    }
    h2 {
      margin:0 0 8px;
      font-size:18px;
      text-align:center;
    }
    .big {
      font-size:46px;
      font-weight:900;
      text-align:center;
      margin:10px 0;
    }
    .list {
      flex:1;
      overflow-y:auto;
      display:flex;
      flex-direction:column;
      gap:6px;
      padding-right:4px;
    }
    .item {
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:6px 10px;
      border-radius:8px;
      background:#0f1730;
      border:1px solid #223065;
      font-size:14px;
    }
    .footer {
      margin-top:8px;
      font-size:12px;
      text-align:center;
      opacity:.8;
    }
    .p { color:#4fc3f7; }
    .f { color:#81c784; }
  </style>
  <script src="../assets/js/tts.js"></script>
</head>
<body>
  <button id="btnSound" style="position:fixed;top:16px;right:16px;padding:10px 14px;border-radius:10px;border:none;background:#ff9800;color:#fff;font-weight:700;cursor:pointer;">🔊 Aktifkan Suara</button>  

  <div class="wrap">
    <!-- Video utama -->
    <div class="video">
      <video src="../assets/video/edukasi.mp4" autoplay loop muted playsinline></video>
    </div>

    <!-- Sidebar antrian -->
    <div class="sidebar">
      <div class="card">
        <h2>ANTRIAN PENDAFTARAN</h2>
        <div>Terakhir dipanggil:</div>
        <div id="lastP" class="big p">-</div>
        <div>Daftar menunggu:</div>
        <div id="listP" class="list"></div>
        <div class="footer">Loket 1–5 | <span id="timeP">--:--</span></div>
      </div>

      <div class="card">
        <h2>ANTRIAN FISIOTERAPI</h2>
        <div>Terakhir dipanggil:</div>
        <div id="lastF" class="big f">-</div>
        <div>Daftar menunggu:</div>
        <div id="listF" class="list"></div>
        <div class="footer">Loket 6–8 | <span id="timeF">--:--</span></div>
      </div>
    </div>
  </div>

  <script>
    let lastKodeP = '', lastStampP = '';
    let lastKodeF = '', lastStampF = '';

    function enableSoundButton(){
      const btn = document.getElementById('btnSound');
      if(!btn) return;
      btn.addEventListener('click',()=>{ 
        speakIndo('Suara tampilan display gabungan diaktifkan.');
        btn.style.display='none';
      });
    }

    async function loadDisplay(){
      const r = await fetch('../api/get_display.php');
      const d = await r.json();

      /* ================= Pendaftaran ================= */
      const lastP = d.last.P;
      const kodeP = lastP.kode || '-';
      const loketP = lastP.loket || '';
      const stampP = lastP.updated_at || '';

      document.getElementById('lastP').textContent = kodeP;
      document.getElementById('timeP').textContent = d.time;

      const listP = document.getElementById('listP');
      listP.innerHTML = '';
      d.waiting.P.forEach(x=>{
        const div = document.createElement('div');
        div.className='item';
        div.innerHTML = `<span>${x.kode}</span><span>${x.created_at}</span>`;
        listP.appendChild(div);
      });

      if (kodeP !== '-' && (kodeP !== lastKodeP || stampP !== lastStampP)) {
        lastKodeP = kodeP;
        lastStampP = stampP;
        if(loketP){
          speakIndo(`Nomor antrian ${ucapkanKode(kodeP)}, silakan menuju loket ${loketP}.`);
        }
      }

      /* ================= Fisioterapi ================= */
      const lastF = d.last.F;
      const kodeF = lastF.kode || '-';
      const loketF = lastF.loket || '';
      const stampF = lastF.updated_at || '';

      document.getElementById('lastF').textContent = kodeF;
      document.getElementById('timeF').textContent = d.time;

      const listF = document.getElementById('listF');
      listF.innerHTML = '';
      d.waiting.F.forEach(x=>{
        const div = document.createElement('div');
        div.className='item';
        div.innerHTML = `<span>${x.kode}</span><span>${x.created_at}</span>`;
        listF.appendChild(div);
      });

      if (kodeF !== '-' && (kodeF !== lastKodeF || stampF !== lastStampF)) {
        lastKodeF = kodeF;
        lastStampF = stampF;
        if(loketF){
          speakIndo(`Nomor antrian ${ucapkanKode(kodeF)}, silakan menuju loket ${loketF}.`);
        }
      }
    }

    enableSoundButton();
    loadDisplay();
    setInterval(loadDisplay, 2000);
  </script>

  <!-- Credit -->
  <div class="cc">
    © <?=date('Y')?> Chandra Irawan,M.T.I — Sistem Antrian RSU Handayani Kotabumi
  </div>
</body>
</html>