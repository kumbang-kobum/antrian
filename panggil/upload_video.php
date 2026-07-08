<?php
require_once '../config/admin_auth.php';

$success_msg = '';
$error_msg = '';

$target_dir = '../assets/video/';
$target_file = $target_dir . 'edukasi.mp4';

// Pastikan folder target ada
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['video_file'])) {
        $file = $_FILES['video_file'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $error_msg = 'Ukuran file melebihi batas "upload_max_filesize" di php.ini (' . ini_get('upload_max_filesize') . ').';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $error_msg = 'Ukuran file melebihi batas MAX_FILE_SIZE dalam form.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_msg = 'File hanya ter-upload sebagian.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_msg = 'Tidak ada file yang di-upload.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error_msg = 'Folder temp PHP tidak ditemukan.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error_msg = 'Gagal menulis file ke disk.';
                    break;
                default:
                    $error_msg = 'Terjadi kesalahan saat upload file (Kode: ' . $file['error'] . ').';
            }
        } else {
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $mime_type = '';
            if (function_exists('mime_content_type')) {
                $mime_type = @mime_content_type($file['tmp_name']);
            }
            
            if ($file_ext !== 'mp4') {
                $error_msg = 'Hanya file video dengan ekstensi .mp4 yang diperbolehkan.';
            } elseif ($mime_type && strpos($mime_type, 'video/') !== 0) {
                $error_msg = 'File yang di-upload bukan merupakan file video yang valid.';
            } else {
                if (!is_writable($target_dir)) {
                    $error_msg = 'Folder penyimpanan video tidak writable. Silakan periksa izin folder (permissions) "assets/video/".';
                } else {
                    // Backup file lama jika ada
                    $backup_file = $target_file . '.bak';
                    if (file_exists($target_file)) {
                        @rename($target_file, $backup_file);
                    }
                    
                    if (move_uploaded_file($file['tmp_name'], $target_file)) {
                        $success_msg = 'Video display berhasil diperbarui!';
                        if (file_exists($backup_file)) {
                            @unlink($backup_file);
                        }
                    } else {
                        $error_msg = 'Gagal menyimpan file video ke folder assets/video/.';
                        if (file_exists($backup_file)) {
                            @rename($backup_file, $target_file);
                        }
                    }
                }
            }
        }
    } else {
        $error_msg = 'Form data tidak lengkap.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <link rel="stylesheet" href="../assets/css/global.css">
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Upload Video Display</title>
  <style>
    .upload-area {
      border: 2px dashed var(--border);
      border-radius: var(--r);
      padding: 30px;
      text-align: center;
      background: var(--surface-2);
      cursor: pointer;
      transition: border-color .15s, background-color .15s;
      margin-bottom: 20px;
    }
    .upload-area:hover, .upload-area.dragover {
      border-color: var(--blue);
      background: rgba(88,166,255,0.05);
    }
    .upload-area input[type="file"] {
      display: none;
    }
    .upload-icon {
      font-size: 40px;
      margin-bottom: 10px;
    }
    .upload-text {
      font-size: 14px;
      color: var(--text);
      font-weight: 500;
    }
    .upload-hint {
      font-size: 12px;
      color: var(--muted);
      margin-top: 6px;
    }
    .video-preview-container {
      margin-top: 20px;
      padding: 16px;
      background: var(--surface-2);
      border: 1px solid var(--border);
      border-radius: var(--r);
    }
    .video-preview-container h3 {
      font-size: 14px;
      margin-bottom: 12px;
      color: var(--text);
    }
    .video-preview {
      width: 100%;
      max-height: 240px;
      border-radius: var(--r-sm);
      background: #000;
    }
    .alert {
      padding: 12px 16px;
      border-radius: var(--r);
      font-size: 14px;
      margin-bottom: 20px;
      font-weight: 500;
    }
    .alert-success {
      background: rgba(63, 185, 80, 0.15);
      border: 1px solid rgba(63, 185, 80, 0.3);
      color: var(--green);
    }
    .alert-error {
      background: rgba(255, 123, 114, 0.15);
      border: 1px solid rgba(255, 123, 114, 0.3);
      color: var(--red);
    }
    .php-info {
      font-size: 12px;
      color: var(--muted);
      background: var(--surface-2);
      padding: 12px;
      border-radius: var(--r-sm);
      border-left: 3px solid var(--border);
      margin-top: 20px;
    }
    .php-info ul {
      margin: 6px 0 0;
      padding-left: 20px;
    }
    .file-selected-name {
      margin-top: 10px;
      font-size: 13px;
      color: var(--blue);
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="admin-page">
    <div class="admin-card card">
      <div class="admin-header">
        <h2>Upload Video Display</h2>
        <a href="../index.php" class="btn-home">← Beranda</a>
      </div>

      <!-- Alerts -->
      <?php if ($success_msg): ?>
        <div class="alert alert-success">
          🎉 <?= htmlspecialchars($success_msg) ?>
        </div>
      <?php endif; ?>

      <?php if ($error_msg): ?>
        <div class="alert alert-error">
          ⚠️ <?= htmlspecialchars($error_msg) ?>
        </div>
      <?php endif; ?>

      <!-- Upload Form -->
      <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
        <div class="upload-area" id="dropArea">
          <div class="upload-icon">📁</div>
          <div class="upload-text">Klik atau seret file video MP4 ke sini</div>
          <div class="upload-hint">Format file harus .mp4</div>
          <div class="file-selected-name" id="fileSelectedName"></div>
          <input type="file" name="video_file" id="videoFileInput" accept="video/mp4" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;">
          🚀 Upload dan Perbarui Video Display
        </button>
      </form>

      <!-- Video Preview -->
      <div class="video-preview-container">
        <h3>Video Display Aktif Saat Ini</h3>
        <?php if (file_exists($target_file)): ?>
          <video class="video-preview" src="<?= $target_file ?>?v=<?= filemtime($target_file) ?>" controls></video>
          <div class="upload-hint" style="margin-top: 8px; text-align: center;">
            Terakhir diperbarui: <?= date('d F Y, H:i:s', filemtime($target_file)) ?> (<?= round(filesize($target_file) / 1024 / 1024, 2) ?> MB)
          </div>
        <?php else: ?>
          <div class="upload-hint" style="text-align: center; padding: 20px;">
            Belum ada video display yang terpasang di <code>assets/video/edukasi.mp4</code>.
          </div>
        <?php endif; ?>
      </div>

      <!-- Limit Info -->
      <div class="php-info">
        <strong>ℹ️ Informasi Kapasitas Upload Server:</strong>
        <ul>
          <li>Maksimum ukuran file upload (<code>upload_max_filesize</code>): <strong><?= ini_get('upload_max_filesize') ?></strong></li>
          <li>Maksimum ukuran data POST (<code>post_max_size</code>): <strong><?= ini_get('post_max_size') ?></strong></li>
        </ul>
        <p style="margin: 8px 0 0 0; font-size: 11px; line-height: 1.4;">
          Jika video Anda berukuran lebih besar dari batas di atas, silakan sesuaikan konfigurasi <code>php.ini</code> server Anda (misal: meningkatkan <code>upload_max_filesize = 100M</code> dan <code>post_max_size = 105M</code>).
        </p>
      </div>
    </div>
  </div>

  <script>
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('videoFileInput');
    const fileNameDisplay = document.getElementById('fileSelectedName');
    const form = document.getElementById('uploadForm');

    // Click to select
    dropArea.addEventListener('click', () => {
      fileInput.click();
    });

    // File selected
    fileInput.addEventListener('change', () => {
      showFileName();
    });

    // Drag and drop events
    ['dragenter', 'dragover'].forEach(eventName => {
      dropArea.addEventListener(eventName, (e) => {
        e.preventDefault();
        dropArea.classList.add('dragover');
      }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, (e) => {
        e.preventDefault();
        dropArea.classList.remove('dragover');
      }, false);
    });

    dropArea.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      const files = dt.files;
      if (files.length) {
        fileInput.files = files;
        showFileName();
      }
    }, false);

    function showFileName() {
      if (fileInput.files.length) {
        const file = fileInput.files[0];
        fileNameDisplay.textContent = `File terpilih: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
      } else {
        fileNameDisplay.textContent = '';
      }
    }

    form.addEventListener('submit', (e) => {
      if (fileInput.files.length) {
        const file = fileInput.files[0];
        const ext = file.name.split('.').pop().toLowerCase();
        if (ext !== 'mp4') {
          alert('Hanya file video MP4 (.mp4) yang diperbolehkan.');
          e.preventDefault();
          return;
        }
      }
    });
  </script>

  <div class="cc">
    © <?=date('Y')?> Chandra Irawan, M.T.I — Sistem Antrian RSU Handayani Kotabumi
  </div>
</body>
</html>
