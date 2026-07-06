<?php /* Display khusus antrian Pendaftaran (Loket 1–5) + MJKN */ ?>
<!doctype html>
<html lang="id">
<head>
  <link rel="stylesheet" href="../assets/css/global.css">
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Display Antrian Pendaftaran</title>
  <style>
    body {margin:0; font-family:'Segoe UI',sans-serif; background:#0b1020; color:#fff;}
    .wrap {
      display:grid;
      grid-template-columns: 2fr 1fr;
      gap:16px;
      height:100vh;
      padding:16px;
      box-sizing:border-box;
    }
    .card {
      background:#111a33;
      border-radius:12px;
      padding:16px;
      display:flex;
      flex-direction:column;
      box-shadow:0 6px 18px rgba(0,0,0,.4);
    }
    .video video {width:100%;height:100%;border-radius:12px;object-fit:cover;}

    /* Sidebar kanan: dua kartu stacked */
    .sidebar {display:flex;flex-direction:column;gap:16px;}
    .sidebar .card {flex:1;}

    h2 {margin:0 0 6px;font-size:17px;text-align:center;letter-spacing:.5px;}
    .lbl {font-size:12px;color:#aaa;text-align:center;}
    .big {font-size:56px;font-weight:900;text-align:center;margin:6px 0;}
    .big.p-color {color:#4fc3f7;}
    .big.m-color {color:#ffb74d;}

    .list {flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:4px;margin-top:6px;}
    .item {
      background:#1b2447;border-radius:7px;padding:8px 10px;
      display:flex;justify-content:space-between;align-items:center;
      font-size:13px;
    }
    .item.mjkn {background:#2a1800;}
    .badge-mjkn {font-size:10px;background:#e65100;color:#fff;border-radius:4px;padding:1px 5px;font-weight:700;margin-left:5px;}

    .footer {margin-top:6px;font-size:12px;color:#aaa;display:flex;justify-content:space-between;}
  </style>
  <script src="../assets/js/tts.js"></script>
</head>
<body>
  <button id="btnSound" style="position:fixed;top:12px;right:12px;padding:8px 12px;border-radius:10px;border:none;background:#ff9800;color:#fff;font-weight:700;cursor:pointer;z-index:10;">🔊 Aktifkan Suara</button>

  <div class="wrap">
    <!-- Video utama -->
    <div class="card video">
      <video src="../assets/video/edukasi.mp4" autoplay loop muted playsinline></video>
    </div>

    <!-- Sidebar: dua kartu -->
    <div class="sidebar">
      <!-- Kartu Pendaftaran Walk-in (P) -->
      <div class="card">
        <h2>ANTRIAN PENDAFTARAN</h2>
        <div class="lbl">Terakhir dipanggil</div>
        <div id="lastP" class="big p-color">-</div>
        <div class="lbl">Menunggu</div>
        <div id="listP" class="list"></div>
        <div class="footer"><span>Loket 1–5</span><span id="timeP">--:--</span></div>
      </div>

      <!-- Kartu MJKN (M) -->
      <div class="card">
        <h2>ANTRIAN MJKN 📱</h2>
        <div class="lbl">Terakhir dipanggil</div>
        <div id="lastM" class="big m-color">-</div>
        <div class="lbl">Menunggu</div>
        <div id="listM" class="list"></div>
        <div class="footer"><span>Loket 1–5</span><span id="timeM">--:--</span></div>
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
        div.className = 'item' + (x.no_reg_mjkn ? ' mjkn' : '');
        const badge = x.no_reg_mjkn ? `<span class="badge-mjkn">${esc(x.no_reg_mjkn)}</span>` : '';
        div.innerHTML = `<span>${esc(x.kode)}${badge}</span><span>${esc(x.created_at)}</span>`;
        el.appendChild(div);
      });
    }

    async function loadDisplay(){
      const r = await fetch('../api/get_display.php');
      const d = await r.json();

      /* ---- Pendaftaran (P) ---- */
      const lastP  = d.last.P;
      const kodeP  = lastP.kode  || '-';
      const loketP = lastP.loket || '';
      const stampP = lastP.updated_at || '';

      document.getElementById('lastP').textContent = kodeP;
      document.getElementById('timeP').textContent = d.time;
      renderList('listP', d.waiting.P);

      if (kodeP !== '-' && (kodeP !== lastKodeP || stampP !== lastStampP)) {
        lastKodeP = kodeP; lastStampP = stampP;
        if (loketP) speakIndo(`Nomor antrian ${ucapkanKode(kodeP)}, silakan menuju loket ${loketP}.`);
      }

      /* ---- MJKN (M) ---- */
      const lastM  = d.last.M;
      const kodeM  = lastM.kode  || '-';
      const loketM = lastM.loket || '';
      const stampM = lastM.updated_at || '';

      document.getElementById('lastM').textContent = kodeM;
      document.getElementById('timeM').textContent = d.time;
      renderList('listM', d.waiting.M);

      if (kodeM !== '-' && (kodeM !== lastKodeM || stampM !== lastStampM)) {
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
