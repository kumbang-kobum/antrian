<?php
require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kd_bpjs = strtoupper(trim($_POST['kd_bpjs'] ?? ''));
    $no_urut  = trim($_POST['no_urut'] ?? '');

    if ($kd_bpjs === '' || $no_urut === '') {
        $error = 'Kode poli dan nomor urut tidak boleh kosong.';
    } elseif (!preg_match('/^\d+$/', $no_urut)) {
        $error = 'Nomor urut harus berupa angka.';
    } else {
        $conn_sik = get_conn_sik();
        if (!$conn_sik) {
            $error = 'Koneksi ke database SIK tidak tersedia. Hubungi petugas IT.';
        } else {
            /* Lookup ke SIK via maping_poli_bpjs */
            $stmt = $conn_sik->prepare(
                "SELECT rp.no_reg, rp.no_rawat, rp.jam_reg,
                        p.nm_pasien, pk.nm_poli, d.nm_dokter
                 FROM reg_periksa rp
                 INNER JOIN pasien           p   ON p.no_rkm_medis  = rp.no_rkm_medis
                 INNER JOIN poliklinik       pk  ON pk.kd_poli       = rp.kd_poli
                 INNER JOIN dokter           d   ON d.kd_dokter      = rp.kd_dokter
                 INNER JOIN maping_poli_bpjs mpb ON mpb.kd_poli_rs   = rp.kd_poli
                 INNER JOIN (
                   SELECT no_rawat, MAX(nobooking) AS nobooking
                   FROM referensi_mobilejkn_bpjs
                   GROUP BY no_rawat
                 ) rb ON rb.no_rawat = rp.no_rawat
                 WHERE mpb.kd_poli_bpjs  = ?
                   AND rp.no_reg         = ?
                   AND rp.tgl_registrasi = CURDATE()
                 LIMIT 1"
            );
            $stmt->bind_param('ss', $kd_bpjs, $no_urut);
            $stmt->execute();
            $pasien = $stmt->get_result()->fetch_assoc();

            if (!$pasien) {
                $error = 'Data tidak ditemukan untuk kode <strong>'
                       . htmlspecialchars($kd_bpjs) . '-' . htmlspecialchars($no_urut)
                       . '</strong>. Pastikan kode poli dan nomor urut sesuai aplikasi Mobile JKN.';
            } else {
                $tgl         = date('Y-m-d');
                $jenis       = 'M';
                $no_reg_mjkn = $kd_bpjs . '-' . $no_urut;
                $nomor       = 0;

                /* Transaksi agar nomor urut tidak dobel saat dua pasien masuk bersamaan */
                $conn->begin_transaction();
                try {
                    $cek = $conn->prepare(
                        "SELECT nomor FROM antrian WHERE tgl=? AND no_reg_mjkn=? LIMIT 1 FOR UPDATE"
                    );
                    $cek->bind_param('ss', $tgl, $no_reg_mjkn);
                    $cek->execute();
                    $ada = $cek->get_result()->fetch_assoc();

                    if ($ada) {
                        $nomor = (int)$ada['nomor'];
                    } else {
                        $max = $conn->prepare(
                            "SELECT COALESCE(MAX(nomor),0) AS last FROM antrian WHERE tgl=? AND jenis=? FOR UPDATE"
                        );
                        $max->bind_param('ss', $tgl, $jenis);
                        $max->execute();
                        $nomor = (int)$max->get_result()->fetch_assoc()['last'] + 1;

                        $ins = $conn->prepare(
                            "INSERT INTO antrian (tgl, jenis, no_reg_mjkn, nomor) VALUES (?,?,?,?)"
                        );
                        $ins->bind_param('sssi', $tgl, $jenis, $no_reg_mjkn, $nomor);
                        $ins->execute();
                    }
                    $conn->commit();
                } catch (Throwable $e) {
                    $conn->rollback();
                    $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
                }

                if (!$error) {
                    $kode   = $jenis . str_pad($nomor, 4, '0', STR_PAD_LEFT);
                    $params = http_build_query([
                        'kode'        => $kode,
                        'no_reg_mjkn' => $no_reg_mjkn,
                        'nm'          => $pasien['nm_pasien'],
                        'poli'        => $pasien['nm_poli'],
                    ]);
                    header('Location: cetak_mjkn.php?' . $params);
                    exit;
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Antrian MJKN</title>
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="../assets/css/global.css">
  <style>
    body {
      display: flex; min-height: 100vh;
      align-items: center; justify-content: center;
      background: linear-gradient(150deg, #0077b6 0%, #00b4d8 45%, #caf0f8 80%, #fff 100%) fixed;
      transition: align-items .3s, padding .3s;
      /* override tokens untuk konteks terang */
      --surface:   rgba(255,255,255,0.86);
      --surface-2: rgba(255,255,255,0.6);
      --border:    rgba(0, 100, 160, 0.15);
      --text:      #1a2e44;
      --muted:     #4e7090;
      color: var(--text);
    }
    body.kbd-open { align-items: flex-start; padding-top: 28px; }

    .wrap {
      max-width: 440px; width: 92%;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 20px;
      box-shadow: 0 4px 36px rgba(0,80,160,0.14);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      padding: 28px 24px; text-align: center;
    }
    h1  { font-size: 22px; margin: 0 0 4px; color: var(--text); }
    p   { color: var(--muted); margin: 0 0 18px; font-size: 14px; }

    .input-row {
      display: flex; align-items: center;
      justify-content: center; gap: 10px;
    }
    .input-wrap { display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .input-wrap label {
      font-size: 10px; font-weight: 700; color: var(--muted);
      letter-spacing: 1px; text-transform: uppercase;
    }
    .input-row input {
      padding: 14px 8px; font-size: 22px; font-weight: 800;
      border: 2px solid var(--border); border-radius: 10px;
      background: var(--surface-2); color: var(--text);
      outline: none; text-align: center; text-transform: uppercase;
      transition: border-color .2s; cursor: pointer; font-family: inherit;
    }
    .input-row input.active { border-color: #0077b6; box-shadow: 0 0 0 3px rgba(0,119,182,.12); }
    #kd_bpjs { width: 110px; letter-spacing: 4px; }
    #no_urut  { width: 80px;  letter-spacing: 3px; }
    .sep { font-size: 28px; font-weight: 900; color: var(--muted); margin-top: 18px; }

    .hint  { margin: 10px 0 0; font-size: 12px; color: var(--muted); }
    .btn-submit {
      margin-top: 18px; width: 100%; padding: 14px;
      background: linear-gradient(135deg, #0077b6, #00b4d8); color: #fff;
      border: none; border-radius: 10px;
      font-size: 16px; font-weight: 700; cursor: pointer;
      font-family: inherit; transition: filter .2s;
      box-shadow: 0 4px 14px rgba(0,119,182,.3);
    }
    .btn-submit:hover { filter: brightness(1.08); }
    .error {
      margin-top: 16px; padding: 12px;
      background: rgba(255,123,114,.08);
      border: 1px solid rgba(255,123,114,.25);
      border-radius: 10px; font-size: 14px; color: #c0392b;
    }
    .back {
      display: inline-block; margin-bottom: 16px;
      color: var(--muted); font-size: 14px;
    }
    .back:hover { color: var(--text); }

    /* Virtual keyboard */
    #kbd {
      position: fixed; bottom: 0; left: 0; right: 0;
      background: rgba(240,248,255,0.96);
      border-top: 1px solid rgba(0,100,160,0.15);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      padding: 12px 10px 20px;
      box-shadow: 0 -4px 20px rgba(0,80,160,0.12);
      display: none; z-index: 999;
    }
    #kbd.show { display: block; }
    .kbd-row { display: flex; justify-content: center; gap: 6px; margin-bottom: 6px; }
    .kbd-row .k {
      min-width: 44px; height: 50px;
      background: #fff; color: #1a2e44;
      border: 1px solid rgba(0,100,160,0.18); border-radius: 8px;
      font-size: 17px; font-weight: 700;
      cursor: pointer; flex: 1; max-width: 60px;
      display: flex; align-items: center; justify-content: center;
      transition: background .1s, border-color .1s;
      -webkit-tap-highlight-color: transparent;
      font-family: inherit;
      box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    .kbd-row .k:active { background: #0077b6; color: #fff; border-color: #0077b6; }
    .kbd-row .k.wide   { max-width: 120px; font-size: 14px; background: #f0f7ff; }
    .kbd-row .k.submit { max-width: 160px; background: linear-gradient(135deg,#0077b6,#00b4d8); color:#fff; border-color: #0077b6; }
    .kbd-row .k.del    { background: #fff0f0; color: #c0392b; border-color: rgba(192,57,43,.2); }
  </style>
</head>
<body>
<div class="wrap">
  <a class="back" href="index.php">← Kembali</a>
  <h1>📱 Antrian MJKN</h1>
  <p>Masukkan kode poli BPJS dan nomor urut dari aplikasi Mobile JKN</p>

  <form method="post" action="mjkn.php" id="form-mjkn">
    <div class="input-row">
      <div class="input-wrap">
        <label for="kd_bpjs">KODE POLI</label>
        <input type="text" id="kd_bpjs" name="kd_bpjs"
               placeholder="ANA" maxlength="10" autocomplete="off" readonly
               value="<?= htmlspecialchars(strtoupper($_POST['kd_bpjs'] ?? '')) ?>">
      </div>
      <span class="sep">-</span>
      <div class="input-wrap">
        <label for="no_urut">NO. URUT</label>
        <input type="text" id="no_urut" name="no_urut"
               placeholder="1" maxlength="4" autocomplete="off" readonly
               value="<?= htmlspecialchars($_POST['no_urut'] ?? '') ?>">
      </div>
    </div>
    <p class="hint">Ketuk kolom untuk membuka keyboard</p>
    <button type="submit" class="btn-submit">Ambil Nomor Antrian</button>
  </form>

  <?php if ($error): ?>
    <div class="error"><?= $error ?></div>
  <?php endif; ?>
</div>

<!-- Virtual Keyboard -->
<div id="kbd"></div>

<script>
(function () {
  const LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
  const NUMS    = ['1','2','3','4','5','6','7','8','9','0'];

  let activeInput = null;

  function buildKeyboard(type) {
    const kbd = document.getElementById('kbd');
    kbd.innerHTML = '';

    if (type === 'alpha') {
      /* Baris huruf: 9 – 9 – 8 + baris aksi */
      const rows = [LETTERS.slice(0,9), LETTERS.slice(9,18), LETTERS.slice(18)];
      rows.forEach(function(keys) {
        const row = document.createElement('div');
        row.className = 'kbd-row';
        keys.forEach(function(ch) {
          const btn = makeKey(ch, function() { append(ch); });
          row.appendChild(btn);
        });
        kbd.appendChild(row);
      });
    } else {
      /* Numpad 3×3 + baris bawah */
      for (let r = 0; r < 3; r++) {
        const row = document.createElement('div');
        row.className = 'kbd-row';
        for (let c = 0; c < 3; c++) {
          const ch = NUMS[r * 3 + c];
          row.appendChild(makeKey(ch, function(v){ return function(){ append(v); }; }(ch)));
        }
        kbd.appendChild(row);
      }
      const bot = document.createElement('div');
      bot.className = 'kbd-row';
      bot.appendChild(makeKey('⌫', del, 'k del'));
      bot.appendChild(makeKey('0', function() { append('0'); }));
      bot.appendChild(makeKey('✓ Lanjut', next, 'k submit'));
      kbd.appendChild(bot);
      return; // baris aksi sudah dibuat
    }

    /* Baris aksi untuk keyboard huruf */
    const act = document.createElement('div');
    act.className = 'kbd-row';
    act.appendChild(makeKey('⌫ Hapus', del, 'k wide del'));
    act.appendChild(makeKey('Bersih', clear, 'k wide'));
    act.appendChild(makeKey('✓ Lanjut', next, 'k wide submit'));
    kbd.appendChild(act);
  }

  function makeKey(label, handler, cls) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = cls || 'k';
    btn.textContent = label;
    btn.addEventListener('mousedown', function(e) { e.preventDefault(); });
    btn.addEventListener('click', function(e) { e.stopPropagation(); handler(); });
    return btn;
  }

  function append(ch) {
    if (!activeInput) return;
    const max = parseInt(activeInput.getAttribute('maxlength') || '999', 10);
    if (activeInput.value.length < max) activeInput.value += ch;
  }

  function del() {
    if (activeInput) activeInput.value = activeInput.value.slice(0, -1);
  }

  function clear() {
    if (activeInput) activeInput.value = '';
  }

  function next() {
    if (activeInput && activeInput.id === 'kd_bpjs') {
      document.getElementById('no_urut').focus();
    } else {
      closeKbd();
    }
  }

  function openKbd(input) {
    if (activeInput) activeInput.classList.remove('active');
    activeInput = input;
    input.classList.add('active');
    const type = input.id === 'kd_bpjs' ? 'alpha' : 'num';
    buildKeyboard(type);
    document.getElementById('kbd').classList.add('show');
    document.body.classList.add('kbd-open');
  }

  function closeKbd() {
    document.getElementById('kbd').classList.remove('show');
    document.body.classList.remove('kbd-open');
    if (activeInput) activeInput.classList.remove('active');
    activeInput = null;
  }

  /* focus: menangani saat tab-switch kembali ke halaman
     click + stopPropagation: menangani klik pertama kali yang terkadang
     tidak memicu focus pada input readonly di beberapa browser */
  ['kd_bpjs', 'no_urut'].forEach(function(id) {
    var el = document.getElementById(id);
    el.addEventListener('focus', function() { openKbd(this); });
    el.addEventListener('click', function(e) {
      e.stopPropagation(); // cegah document handler menutup keyboard
      openKbd(this);
    });
  });

  /* Tutup keyboard saat klik di luar form dan keyboard */
  document.addEventListener('click', function(e) {
    const wrap = document.querySelector('.wrap');
    const kbd  = document.getElementById('kbd');
    if (!wrap.contains(e.target) && !kbd.contains(e.target)) closeKbd();
  });

  /* Tutup keyboard saat form disubmit */
  document.getElementById('form-mjkn').addEventListener('submit', closeKbd);
})();
</script>
</body>
</html>
