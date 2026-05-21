<?php
// index.php - Generator Surat Sosialisasi BSI
session_start();
$error   = $_SESSION['error']   ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Generator Surat Sosialisasi — BSI</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

  <header class="topbar">
    <div class="topbar-inner">
      <div class="brand">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
          <circle cx="16" cy="16" r="15" fill="#1a3a6b"/>
          <text x="16" y="13" text-anchor="middle" font-family="Arial" font-weight="900" font-size="7" fill="#e8b000">BSI</text>
          <text x="16" y="20" text-anchor="middle" font-family="Arial" font-weight="700" font-size="3.5" fill="#fff">BINA SARANA</text>
          <text x="16" y="24" text-anchor="middle" font-family="Arial" font-weight="700" font-size="3.5" fill="#fff">INFORMATIKA</text>
        </svg>
        <span class="brand-name">Surat Sosialisasi <span class="brand-tag">Generator</span></span>
      </div>
      <span class="topbar-hint">Universitas Bina Sarana Informatika</span>
    </div>
  </header>

  <main class="container">

    <?php if ($error): ?>
    <div class="alert alert-error">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert alert-success">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <div class="card-grid">

      <!-- LEFT: Petunjuk -->
      <div class="panel panel-info">
        <h2 class="panel-title">Cara Penggunaan</h2>
        <ol class="steps">
          <li>
            <span class="step-num">1</span>
            <div>
              <strong>Upload surat asli (PDF)</strong>
              <p>Upload file PDF surat pengantar riset yang sudah dicetak dari sistem BSI.</p>
            </div>
          </li>
          <li>
            <span class="step-num">2</span>
            <div>
              <strong>Isi Judul & Mata Kuliah</strong>
              <p>Masukkan judul/tema materi sosialisasi dan nama mata kuliah terkait.</p>
            </div>
          </li>
          <li>
            <span class="step-num">3</span>
            <div>
              <strong>Generate & Unduh</strong>
              <p>Klik Generate. PDF baru akan otomatis terunduh — hanya paragraf utama yang berubah, format lainnya tetap sama persis.</p>
            </div>
          </li>
        </ol>
        <div class="info-box">
          <strong>Yang diubah:</strong><br>
          Paragraf <em>"Berkaitan dengan program pemerintah…"</em> diubah menjadi kalimat pengajuan sosialisasi.<br><br>
          <strong>Yang tidak berubah:</strong><br>
          Kop surat, nama mahasiswa, NIM, jurusan, tanda tangan, QR code, footer, security print — semuanya tetap asli.
        </div>
      </div>

      <!-- RIGHT: Form -->
      <div class="panel panel-form">
        <h2 class="panel-title">Form Generator</h2>
        <form action="process.php" method="POST" enctype="multipart/form-data" id="mainForm">

          <div class="form-group">
            <label for="pdf_file">
              Upload Surat Asli PDF
              <span class="required">*</span>
            </label>
            <div class="upload-area" id="uploadArea">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#1a3a6b" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              <p class="upload-hint">Klik atau drag & drop PDF di sini</p>
              <p class="upload-sub">Hanya file .pdf — maksimum 10 MB</p>
              <span class="upload-filename" id="uploadFilename"></span>
            </div>
            <input type="file" id="pdf_file" name="pdf_file" accept=".pdf" required style="display:none">
          </div>

          <div class="form-group">
            <label for="judul">
              Judul / Tema Materi Sosialisasi
              <span class="required">*</span>
            </label>
            <textarea
              id="judul"
              name="judul"
              rows="3"
              placeholder='Contoh: Pemanfaatan Business Intelligence untuk Efisiensi Data di Era Digital'
              required
            ></textarea>
            <span class="hint">Judul ini akan otomatis muncul di dalam paragraf surat.</span>
          </div>

          <div class="form-group">
            <label for="matkul">
              Mata Kuliah
              <span class="required">*</span>
            </label>
            <input
              type="text"
              id="matkul"
              name="matkul"
              placeholder="Contoh: BUSINESS INTELLIGENCE"
              required
            >
            <span class="hint">Tulis nama mata kuliah sesuai yang ada di surat asli.</span>
          </div>

          <div class="preview-text" id="previewText" style="display:none">
            <strong>Preview paragraf baru:</strong>
            <p id="previewParagraph"></p>
          </div>

          <button type="submit" class="btn-generate" id="btnGenerate">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="12" y2="12"/><line x1="15" y1="15" x2="12" y2="12"/></svg>
            Generate & Unduh PDF
          </button>

        </form>
      </div>

    </div>
  </main>

  <footer class="footer">
    <p>Generator Surat Sosialisasi BSI &nbsp;·&nbsp; Hanya mengubah paragraf isi surat, seluruh format asli tetap terjaga.</p>
  </footer>

  <script src="assets/app.js"></script>
</body>
</html>
