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
</head>
<body>
  <div class="admin-page">
    <div class="admin-card card">
      <div class="admin-header">
        <h2>Panggil Antrian Pendaftaran</h2>
        <a href="../index.php" class="btn-home">← Beranda</a>
      </div>

      <div class="toolbar">
        <label>Loket:
          <select id="loket">
            <option value="1">1</option><option value="2">2</option>
            <option value="3">3</option><option value="4">4</option><option value="5">5</option>
          </select>
        </label>
        <button class="btn btn-ghost btn-sm" onclick="refreshList()">↺ Refresh</button>
      </div>

      <div class="tbl-wrap">
        <table class="tbl">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode</th>
              <th>MJKN</th>
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
      const r = await fetch('../api/list_queue.php?jenis=P');
      const d = await r.json();
      const tbody = document.getElementById('tbody');
      tbody.innerHTML = '';
      d.data.forEach((row, idx) => {
        const tr = document.createElement('tr');
        if (row.no_reg_mjkn) tr.className = 'row-mjkn';

        const mjknCell = row.no_reg_mjkn
          ? `<span class="badge badge-mjkn">📱 ${esc(row.no_reg_mjkn)}</span>`
          : `<span style="color:var(--muted)">—</span>`;

        tr.innerHTML = `
          <td style="color:var(--muted)">${idx+1}</td>
          <td><strong>${esc(row.kode)}</strong></td>
          <td>${mjknCell}</td>
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
    }

    async function call(id){
      const loket = +document.getElementById('loket').value;
      await fetch('../api/call.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id, loket})
      });
      refreshList();
    }

    async function finish(id){
      await fetch('../api/finish.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id})
      });
      refreshList();
    }

    refreshList();
    setInterval(refreshList, 5000);
  </script>

  <div class="cc">
    © <?=date('Y')?> Chandra Irawan, M.T.I — Sistem Antrian RSU Handayani Kotabumi
  </div>
</body>
</html>
