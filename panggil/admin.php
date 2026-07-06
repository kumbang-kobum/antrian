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
  <title>Admin Panggil Antrian</title>
</head>
<body>
  <div class="admin-page">
    <div class="admin-card card">
      <div class="admin-header">
        <h2>Panggil Antrian</h2>
        <a href="../index.php" class="btn-home">← Beranda</a>
      </div>

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
        <button class="btn btn-ghost btn-sm" onclick="refreshList()">↺ Refresh</button>
        <span id="info" class="pill">Ready</span>
      </div>

      <div class="tbl-wrap">
        <table class="tbl">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode</th>
              <th>Waktu Daftar</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    const tbody = document.getElementById('tbody');
    const info  = document.getElementById('info');

    function esc(s) {
      const d = document.createElement('div');
      d.textContent = s ?? '';
      return d.innerHTML;
    }

    function statusClass(s) {
      if (s === 'menunggu')  return 'badge-menunggu';
      if (s === 'dipanggil') return 'badge-dipanggil';
      if (s === 'selesai')   return 'badge-selesai';
      return '';
    }

    async function refreshList(){
      info.textContent = 'Memuat...';
      const jenis = document.getElementById('jenis').value;
      const r = await fetch(`../api/list_queue.php?jenis=${jenis}`);
      const d = await r.json();
      tbody.innerHTML = '';
      d.data.forEach((row, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td style="color:var(--muted)">${idx+1}</td>
          <td><strong>${esc(row.kode)}</strong></td>
          <td style="color:var(--muted);font-size:13px">${esc(row.created_at)}</td>
          <td><span class="badge ${statusClass(row.status)}">${esc(row.status)}</span></td>
          <td style="display:flex;gap:6px">
            <button class="btn btn-success btn-sm">Panggil</button>
            <button class="btn btn-danger btn-sm">Selesai</button>
          </td>`;
        tr.querySelector('.btn-success').addEventListener('click', () => call(row.id));
        tr.querySelector('.btn-danger').addEventListener('click',  () => finish(row.id));
        tbody.appendChild(tr);
      });
      info.textContent = 'Ready';
    }

    async function call(id){
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
      if(d.ok){ refreshList(); }
      else { alert('Gagal memanggil: '+d.msg); }
    }

    async function finish(id){
      if(!confirm('Tandai selesai?')) return;
      const r = await fetch('../api/finish.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id})
      });
      const d = await r.json();
      if(d.ok){ refreshList(); }
      else { alert('Gagal: '+d.msg); }
    }

    refreshList();
    setInterval(refreshList, 5000);
  </script>

  <div class="cc">
    © <?=date('Y')?> Chandra Irawan, M.T.I — Sistem Antrian RSU Handayani Kotabumi
  </div>
</body>
</html>
