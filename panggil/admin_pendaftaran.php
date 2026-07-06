<?php
require_once '../config/database.php';
require_once '../config/admin_auth.php';
?>
<!doctype html>
<html lang="id">
<head>
  <link rel="stylesheet" href="../assets/css/global.css">
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin Panggil Pendaftaran</title>
  <style>
    body {margin:0; font-family:'Segoe UI',sans-serif; background:linear-gradient(135deg,#2b5876,#4e4376); color:#fff;}
    .wrap{padding:20px; display:flex; justify-content:center;}
    .card{background:rgba(255,255,255,0.1); backdrop-filter:blur(8px); border-radius:16px; padding:20px; max-width:900px; width:100%;}
    h2{text-align:center;margin:0 0 20px;font-size:24px}
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{padding:10px;text-align:left}
    .btn{padding:6px 12px;border:none;border-radius:6px;cursor:pointer;font-size:13px;color:#fff}
    .btn-call{background:#4caf50}.btn-call:hover{background:#43a047}
    .btn-finish{background:#f44336}.btn-finish:hover{background:#e53935}
    tr.mjkn-row{background:rgba(230,81,0,.15);}
    .badge-mjkn{font-size:11px;background:#e65100;color:#fff;border-radius:5px;padding:2px 6px;font-weight:700;}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h2>🗣️ Admin Antrian Pendaftaran</h2>
      <label>Loket:
        <select id="loket">
          <option value="1">1</option><option value="2">2</option>
          <option value="3">3</option><option value="4">4</option><option value="5">5</option>
        </select>
      </label>
      <div style="text-align:center; margin-top:20px;">
        <a href="../index.php" class="btn-home">🏠 Home</a>
      </div>
      <table>
        <thead><tr><th>No</th><th>Kode</th><th>MJKN</th><th>Daftar</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody id="tbody"></tbody>
      </table>
    </div>
  </div>

  <script>
    function esc(s) {
      const d = document.createElement('div');
      d.textContent = s ?? '';
      return d.innerHTML;
    }

    async function refreshList(){
      const r = await fetch('../api/list_queue.php?jenis=P');
      const d = await r.json();
      const tbody = document.getElementById('tbody');
      tbody.innerHTML = '';
      d.data.forEach((row, idx) => {
        const isMjkn = !!row.no_reg_mjkn;
        const tr = document.createElement('tr');
        if (isMjkn) tr.className = 'mjkn-row';

        const mjknCell = isMjkn
          ? `<span class="badge-mjkn">📱 ${esc(row.no_reg_mjkn)}</span>`
          : `<span style="color:#aaa">—</span>`;

        tr.innerHTML = `
          <td>${idx + 1}</td>
          <td><b>${esc(row.kode)}</b></td>
          <td>${mjknCell}</td>
          <td>${esc(row.created_at)}</td>
          <td>${esc(row.status)}</td>
          <td>
            <button class="btn btn-call">Panggil</button>
            <button class="btn btn-finish">Selesai</button>
          </td>`;

        tr.querySelector('.btn-call').addEventListener('click', () => call(row.id));
        tr.querySelector('.btn-finish').addEventListener('click', () => finish(row.id));
        tbody.appendChild(tr);
      });
    }

    async function call(id){
      const loket = +document.getElementById('loket').value;
      await fetch('../api/call.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id, loket})});
      refreshList();
    }

    async function finish(id){
      await fetch('../api/finish.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id})});
      refreshList();
    }

    refreshList(); setInterval(refreshList, 5000);
  </script>
</body>
</html>
