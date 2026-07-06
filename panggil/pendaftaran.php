<?php /* Display khusus antrian Pendaftaran (Loket 1–5) + MJKN */ ?>
<!doctype html>
<html lang="id">
<head>
  <link rel="stylesheet" href="../assets/css/global.css">
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Display Antrian Pendaftaran</title>
  <script src="../assets/js/tts.js"></script>
</head>
<body class="disp-page">
  <button id="btnSound" class="btn-sound">🔊 Aktifkan Suara</button>

  <div class="disp-wrap">
    <div class="disp-video">
      <video src="../assets/video/edukasi.mp4" autoplay loop muted playsinline></video>
    </div>

    <div class="disp-sidebar">
      <!-- Pendaftaran Walk-in (P) -->
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

      <!-- MJKN (M) -->
      <div class="disp-card">
        <div class="disp-section-label">Antrian MJKN 📱</div>
        <div id="lastM" class="disp-big color-m">—</div>
        <hr class="disp-divider">
        <div class="disp-queue-label">Menunggu</div>
        <div id="listM" class="disp-list"></div>
        <div class="disp-footer">
          <span>Loket 1–5</span>
          <span id="timeM">--:--</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    let lastKodeP = '', lastStampP = '';
    let lastKodeM = '', lastStampM = '';

    function enableSoundButton(){
      const btn = document.getElementById('btnSound');
      if (!btn) return;
      btn.addEventListener('click', () => {
        speakIndo('Suara tampilan diaktifkan.');
        btn.style.display = 'none';
      });
    }

    function esc(s) {
      const d = document.createElement('div');
      d.textContent = s ?? '';
      return d.innerHTML;
    }

    function renderList(elId, items) {
      const el = document.getElementById(elId);
      el.innerHTML = '';
      items.forEach(x => {
        const div = document.createElement('div');
        div.className = 'disp-item' + (x.no_reg_mjkn ? ' mjkn' : '');
        const kode = document.createElement('span');
        kode.textContent = x.kode;
        if (x.no_reg_mjkn) {
          const badge = document.createElement('span');
          badge.className = 'badge badge-mjkn';
          badge.style.marginLeft = '6px';
          badge.textContent = x.no_reg_mjkn;
          kode.appendChild(badge);
        }
        const time = document.createElement('span');
        time.textContent = x.created_at;
        div.appendChild(kode);
        div.appendChild(time);
        el.appendChild(div);
      });
    }

    async function loadDisplay(){
      const r = await fetch('../api/get_display.php');
      const d = await r.json();

      const lastP  = d.last.P;
      const kodeP  = lastP.kode  || '—';
      const loketP = lastP.loket || '';
      const stampP = lastP.updated_at || '';

      document.getElementById('lastP').textContent = kodeP;
      document.getElementById('timeP').textContent = d.time;
      renderList('listP', d.waiting.P);

      if (kodeP !== '—' && (kodeP !== lastKodeP || stampP !== lastStampP)) {
        lastKodeP = kodeP; lastStampP = stampP;
        if (loketP) speakIndo(`Nomor antrian ${ucapkanKode(kodeP)}, silakan menuju loket ${loketP}.`);
      }

      const lastM  = d.last.M;
      const kodeM  = lastM.kode  || '—';
      const loketM = lastM.loket || '';
      const stampM = lastM.updated_at || '';

      document.getElementById('lastM').textContent = kodeM;
      document.getElementById('timeM').textContent = d.time;
      renderList('listM', d.waiting.M);

      if (kodeM !== '—' && (kodeM !== lastKodeM || stampM !== lastStampM)) {
        lastKodeM = kodeM; lastStampM = stampM;
        if (loketM) speakIndo(`Nomor antrian ${ucapkanKode(kodeM)}, silakan menuju loket ${loketM}.`);
      }
    }

    enableSoundButton();
    loadDisplay();
    setInterval(loadDisplay, 2000);
  </script>
</body>
</html>
