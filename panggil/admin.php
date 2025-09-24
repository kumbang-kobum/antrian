<!doctype html>
<html lang="id">
<head>
  <link rel="stylesheet" href="../assets/css/global.css">
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin Panggil Antrian</title>
  <link rel="stylesheet" href="style.css"/>
  <style>
    body {
      margin:0;
      font-family:'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, #2b5876, #4e4376);
      color:#f0f0f0;
    }
    .wrap {
      padding:20px;
      display:flex;
      justify-content:center;
    }
    .card {
      background:rgba(255,255,255,0.1);
      backdrop-filter:blur(8px);
      border-radius:16px;
      padding:20px;
      width:100%;
      max-width:900px;
      box-shadow:0 10px 30px rgba(0,0,0,0.3);
    }
    h2 {
      margin-top:0;
      font-size:24px;
      font-weight:700;
      text-align:center;
      margin-bottom:20px;
    }
    .toolbar {
      display:flex;
      gap:16px;
      align-items:center;
      flex-wrap:wrap;
      margin-bottom:16px;
      background:rgba(0,0,0,0.2);
      padding:12px 16px;
      border-radius:12px;
    }
    label { font-size:14px; font-weight:500; }
    select, button {
      padding:8px 12px;
      border-radius:8px;
      border:none;
      outline:none;
    }
    button {
      cursor:pointer;
      font-weight:600;
      background:#2196f3;
      color:#fff;
      transition:.2s;
    }
    button:hover { filter:brightness(1.1); }

    table {
      width:100%;
      border-collapse:collapse;
      margin-top:10px;
      background:rgba(0,0,0,0.25);
      border-radius:12px;
      overflow:hidden;
    }
    thead {
      background:rgba(0,0,0,0.4);
    }
    th, td {
      padding:12px;
      text-align:left;
      font-size:14px;
    }
    tbody tr:nth-child(even) {
      background:rgba(255,255,255,0.05);
    }
    .act { display:flex; gap:8px; }
    .btn-call {
      background:#4caf50;
      color:#fff;
      border:none;
      padding:6px 12px;
      border-radius:6px;
      cursor:pointer;
      font-size:13px;
    }
    .btn-call:hover { background:#45a047; }
    .btn-finish {
      background:#f44336;
      color:#fff;
      border:none;
      padding:6px 12px;
      border-radius:6px;
      cursor:pointer;
      font-size:13px;
    }
    .btn-finish:hover { background:#e53935; }

    .status-badge {
      padding:4px 8px;
      border-radius:12px;
      font-size:12px;
      font-weight:600;
      text-transform:capitalize;
    }
    .status-menunggu { background:#ff9800; color:#fff; }
    .status-dipanggil { background:#2196f3; color:#fff; }
    .status-selesai { background:#4caf50; color:#fff; }

    .pill {
      padding:6px 10px;
      border-radius:999px;
      background:#223065;
      color:#cfe2ff;
      font-size:12px;
      font-weight:600;
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h2>🗣️ Menu Panggil Antrian</h2>
      <div class="toolbar">
        <label>Jenis:
          <select id="jenis">
            <option value="P">Pendaftaran (P)</option>
            <option value="F">Fisioterapi (F)</option>
          </select>
        </label>
        <label>Loket:
          <select id="loket">
            <option value="1">1</option><option value="2">2</option><option value="3">3</option>
            <option value="4">4</option><option value="5">5</option>
            <option value="6">6</option><option value="7">7</option><option value="8">8</option>
          </select>
        </label>
        <button onclick="refreshList()">🔄 Refresh</button>
        <span id="info" class="pill">Ready</span>
        <div style="text-align:center; margin-top:20px;">
          <a href="../index.php" class="btn-home">🏠 Home</a>
        </div>
      </div>

      <table>
        <thead>
          <tr><th>No</th><th>Kode</th><th>Daftar</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
    </div>
  </div>

  <script>
    const tbody = document.getElementById('tbody');
    const info = document.getElementById('info');

    async function refreshList(){
      info.textContent='Loading...';
      const jenis = document.getElementById('jenis').value;
      const r = await fetch(`../api/list_queue.php?jenis=${jenis}`);
      const d = await r.json();
      tbody.innerHTML = '';
      d.data.forEach((row,idx)=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${idx+1}</td>
          <td><b>${row.kode}</b></td>
          <td>${row.created_at}</td>
          <td><span class="status-badge status-${row.status}">${row.status}</span></td>
          <td class="act">
            <button class="btn-call" onclick="call(${row.id}, '${row.kode}')">Panggil</button>
            <button class="btn-finish" onclick="finish(${row.id})">Selesai</button>
          </td>`;
        tbody.appendChild(tr);
      });
      info.textContent='Ready';
    }

    async function call(id, kode){
      const loket = document.getElementById('loket').value;
      const jenis = document.getElementById('jenis').value;

      if (jenis==='P' && !(loket>=1 && loket<=5)){
        alert('Antrian P hanya untuk loket 1–5.');
        return;
      }
      if (jenis==='F' && !(loket>=6 && loket<=8)){
        alert('Antrian F hanya untuk loket 6–8.');
        return;
      }

      const r = await fetch('../api/call.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ id, loket })
      });
      const d = await r.json();
      if(d.ok){
        // suara tidak lagi dipanggil di admin
        refreshList();
      } else {
        alert('Gagal memanggil: '+d.msg);
      }
    }

    async function finish(id){
      if(!confirm('Tandai selesai?')) return;
      const r = await fetch('../api/finish.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id})
      });
      const d = await r.json();
      if(d.ok){
        refreshList();
      } else alert('Gagal: '+d.msg);
    }

    refreshList();
    setInterval(refreshList, 5000);
  </script>

  <div class="cc">
    © <?=date('Y')?> Chandra Irawan,M.T.I — Sistem Antrian RSU Handayani Kotabumi
  </div>
</body>
</html>