<?php /* Display khusus antrian Pendaftaran (Loket 1–5) */ ?>
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
      gap:20px;
      height:100vh;
      padding:20px;
      box-sizing:border-box;
    }
    .card {
      background:#111a33;
      border-radius:12px;
      padding:20px;
      display:flex;
      flex-direction:column;
      justify-content:center;
      align-items:center;
      box-shadow:0 6px 18px rgba(0,0,0,.4);
    }
    .video video {width:100%;height:100%;border-radius:12px;object-fit:cover;}
    .big {font-size:72px;font-weight:900;margin:20px 0;color:#4fc3f7;}
    .list {margin-top:20px;width:100%;}
    .item {background:#1b2447;border-radius:8px;padding:10px;margin-bottom:8px;display:flex;justify-content:space-between;}
    h2 {margin:0 0 10px;font-size:26px}
    .footer {margin-top:auto;font-size:14px;color:#aaa;display:flex;justify-content:space-between;width:100%;}
  </style>
  <script src="../assets/js/tts.js"></script>
</head>
<body>
  <button id="btnSound" style="position:fixed;top:16px;right:16px;padding:10px 14px;border-radius:10px;border:none;background:#ff9800;color:#fff;font-weight:700;cursor:pointer;">🔊 Aktifkan Suara</button>

  <div class="wrap">
    <div class="card video">
      <video src="../assets/video/edukasi.mp4" autoplay loop muted playsinline></video>
    </div>
    <div class="card">
      <h2>ANTRIAN PENDAFTARAN</h2>
      <div>Terakhir dipanggil:</div>
      <div id="lastP" class="big">-</div>
      <div>Daftar menunggu:</div>
      <div id="listP" class="list"></div>
      <div class="footer">
        <div>Loket 1–5</div><div id="timeP">--:--</div>
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
        speakIndo('Suara tampilan diaktifkan.'); 
        btn.style.display='none';
      });
    }

    async function loadDisplay(){
      const r = await fetch('../api/get_display.php');
      const d = await r.json();

      const last = d.last.P;
      const kode = last.kode || '-';
      const loket = last.loket || '';
      const stamp = last.updated_at || '';

      document.getElementById('lastP').textContent = kode;
      document.getElementById('timeP').textContent = d.time;

      const list = document.getElementById('listP');
      list.innerHTML = '';
      d.waiting.P.forEach(x=>{
        const div = document.createElement('div');
        div.className='item';
        div.innerHTML = `<span>${x.kode}</span><span>${x.created_at}</span>`;
        list.appendChild(div);
      });

      // 🔊 bicara jika kode berubah atau panggil ulang (updated_at berubah)
      if (kode !== '-' && (kode !== lastKode || stamp !== lastStamp)) {
        lastKode = kode;
        lastStamp = stamp;
        if(loket){
          speakIndo(`Nomor antrian ${ucapkanKode(kode)}, silakan menuju loket ${loket}.`);
        }
      }
    }

    function ucapkanKode(kode){ return kode.split('').join(' '); }

    enableSoundButton();
    loadDisplay();
    setInterval(loadDisplay, 2000);
  </script>
</body>
</html>