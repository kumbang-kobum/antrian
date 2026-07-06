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
  <title>Admin Panggil Fisioterapi</title>
</head>
<body>
  <div class="admin-page">
    <div class="admin-card card">
      <div class="admin-header">
        <h2>Panggil Antrian Fisioterapi</h2>
        <a href="../index.php" class="btn-home">← Beranda</a>
      </div>

      <div class="toolbar">
        <label>Loket:
          <select id="loket">
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
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
    function statusClass(s) {
      if (s === 'menunggu')  return 'badge-menunggu';
      if (s === 'dipanggil') return 'badge-dipanggil';
      if (s === 'selesai')   return 'badge-selesai';
      return '';
    }

    async function refreshList(){
      const r = await fetch('../api/list_queue.php?jenis=F');
      const d = await r.json();
      const tbody = document.getElementById('tbody');
      tbody.innerHTML = '';
      d.data.forEach((row, idx) => {
        const tr = document.createElement('tr');

        const noTd     = document.createElement('td');
        noTd.textContent = idx + 1;
        noTd.style.color = 'var(--muted)';

        const kodeTd   = document.createElement('td');
        const strong   = document.createElement('strong');
        strong.textContent = row.kode;
        kodeTd.appendChild(strong);

        const timeTd   = document.createElement('td');
        timeTd.textContent = row.created_at;
        timeTd.style.cssText = 'color:var(--muted);font-size:13px';

        const statTd   = document.createElement('td');
        const badge    = document.createElement('span');
        badge.className = 'badge ' + statusClass(row.status);
        badge.textContent = row.status;
        statTd.appendChild(badge);

        const actTd    = document.createElement('td');
        actTd.style.display = 'flex';
        actTd.style.gap = '6px';

        const btnCall  = document.createElement('button');
        btnCall.className = 'btn btn-success btn-sm';
        btnCall.textContent = 'Panggil';
        btnCall.addEventListener('click', () => call(row.id));

        const btnFinish = document.createElement('button');
        btnFinish.className = 'btn btn-danger btn-sm';
        btnFinish.textContent = 'Selesai';
        btnFinish.addEventListener('click', () => finish(row.id));

        actTd.appendChild(btnCall);
        actTd.appendChild(btnFinish);
        tr.append(noTd, kodeTd, timeTd, statTd, actTd);
        tbody.appendChild(tr);
      });
    }

    async function call(id){
      const loket = document.getElementById('loket').value;
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
