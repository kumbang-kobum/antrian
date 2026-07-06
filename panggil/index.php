<?php /* Display Gabungan Antrian (Pendaftaran & Fisioterapi) */ ?>
<!doctype html>
<html lang="id">
<head>
  <link rel="stylesheet" href="../assets/css/global.css">
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Display Antrian Gabungan</title>
  <script src="../assets/js/tts.js"></script>
</head>
<body class="disp-page">
  <button id="btnSound" class="btn-sound">🔊 Aktifkan Suara</button>

  <div class="disp-wrap">
    <div class="disp-video">
      <video src="../assets/video/edukasi.mp4" autoplay loop muted playsinline></video>
    </div>

    <div class="disp-sidebar">
      <!-- Pendaftaran -->
      <div class="disp-card">
        <div class="disp-section-label">Antrian Pendaftaran</div>
        <div id="lastP" class="disp-big color-p">—</div>
        <hr class="disp-divider">
        <div class="disp-queue-label">Menunggu</div>
        <div id="listP" class="disp-list"></div>
        <div class="disp-footer">
          <span>Loket 1–5</span>
          <span id="timeP">--:--</span>
        </div>
      </div>

      <!-- Fisioterapi -->
      <div class="disp-card">
        <div class="disp-section-label">Antrian Fisioterapi</div>
        <div id="lastF" class="disp-big color-f">—</div>
        <hr class="disp-divider">
        <div class="disp-queue-label">Menunggu</div>
        <div id="listF" class="disp-list"></div>
        <div class="disp-footer">
          <span>Loket 6–8</span>
          <span id="timeF">--:--</span>
        </div>
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

      const lastP = d.last.P;
      const kodeP = lastP.kode || '—';
      const loketP = lastP.loket || '';
      const stampP = lastP.updated_at || '';

      document.getElementById('lastP').textContent = kodeP;
      document.getElementById('timeP').textContent = d.time;

      const listP = document.getElementById('listP');
      listP.innerHTML = '';
      d.waiting.P.forEach(x=>{
        const div = document.createElement('div');
        div.className = 'disp-item';
        const kode = document.createElement('span');
        kode.textContent = x.kode;
        const time = document.createElement('span');
        time.textContent = x.created_at;
        div.appendChild(kode);
        div.appendChild(time);
        listP.appendChild(div);
      });

      if (kodeP !== '—' && (kodeP !== lastKodeP || stampP !== lastStampP)) {
        lastKodeP = kodeP; lastStampP = stampP;
        if(loketP) speakIndo(`Nomor antrian ${ucapkanKode(kodeP)}, silakan menuju loket ${loketP}.`);
      }

      const lastF = d.last.F;
      const kodeF = lastF.kode || '—';
      const loketF = lastF.loket || '';
      const stampF = lastF.updated_at || '';

      document.getElementById('lastF').textContent = kodeF;
      document.getElementById('timeF').textContent = d.time;

      const listF = document.getElementById('listF');
      listF.innerHTML = '';
      d.waiting.F.forEach(x=>{
        const div = document.createElement('div');
        div.className = 'disp-item';
        const kode = document.createElement('span');
        kode.textContent = x.kode;
        const time = document.createElement('span');
        time.textContent = x.created_at;
        div.appendChild(kode);
        div.appendChild(time);
        listF.appendChild(div);
      });

      if (kodeF !== '—' && (kodeF !== lastKodeF || stampF !== lastStampF)) {
        lastKodeF = kodeF; lastStampF = stampF;
        if(loketF) speakIndo(`Nomor antrian ${ucapkanKode(kodeF)}, silakan menuju loket ${loketF}.`);
      }
    }

    enableSoundButton();
    loadDisplay();
    setInterval(loadDisplay, 2000);
  </script>

  <div class="cc">
    © <?=date('Y')?> Chandra Irawan, M.T.I — Sistem Antrian RSU Handayani Kotabumi
  </div>
</body>
</html>
