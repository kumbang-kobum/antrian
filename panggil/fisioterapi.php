<?php /* Display khusus antrian Fisioterapi (Loket 6–8) */ ?>
<!doctype html>
<html lang="id">
<head>
  <link rel="stylesheet" href="../assets/css/global.css">
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Display Antrian Fisioterapi</title>
  <style>
    /* Sidebar single-card: big number lebih besar karena hanya satu layanan */
    .disp-big { font-size: 72px; }
  </style>
  <script src="../assets/js/tts.js"></script>
</head>
<body class="disp-page">
  <button id="btnSound" class="btn-sound">🔊 Aktifkan Suara</button>

  <div class="disp-wrap">
    <div class="disp-video">
      <video src="../assets/video/edukasi.mp4" autoplay loop muted playsinline></video>
    </div>

    <div class="disp-sidebar">
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
    let lastKode = '';
    let lastStamp = '';

    function enableSoundButton(){
      const btn = document.getElementById('btnSound');
      if(!btn) return;
      btn.addEventListener('click',()=>{
        speakIndo('Suara tampilan fisioterapi diaktifkan.');
        btn.style.display='none';
      });
    }

    async function loadDisplay(){
      const r = await fetch('../api/get_display.php');
      const d = await r.json();

      const last  = d.last.F;
      const kode  = last.kode  || '—';
      const loket = last.loket || '';
      const stamp = last.updated_at || '';

      document.getElementById('lastF').textContent = kode;
      document.getElementById('timeF').textContent = d.time;

      const listF = document.getElementById('listF');
      listF.innerHTML = '';
      d.waiting.F.forEach(x=>{
        const div = document.createElement('div');
        div.className = 'disp-item';
        const kodeEl = document.createElement('span');
        kodeEl.textContent = x.kode;
        const timeEl = document.createElement('span');
        timeEl.textContent = x.created_at;
        div.appendChild(kodeEl);
        div.appendChild(timeEl);
        listF.appendChild(div);
      });

      if (kode !== '—' && (kode !== lastKode || stamp !== lastStamp)) {
        lastKode = kode;
        lastStamp = stamp;
        if(loket) speakIndo(`Nomor antrian ${ucapkanKode(kode)}, silakan menuju loket ${loket}.`);
      }
    }

    enableSoundButton();
    loadDisplay();
    setInterval(loadDisplay, 2000);
  </script>
</body>
</html>
