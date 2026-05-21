// assets/app.js

const uploadArea    = document.getElementById('uploadArea');
const pdfInput      = document.getElementById('pdf_file');
const uploadFilename= document.getElementById('uploadFilename');
const judulInput    = document.getElementById('judul');
const matkulInput   = document.getElementById('matkul');
const previewDiv    = document.getElementById('previewText');
const previewPara   = document.getElementById('previewParagraph');
const btnGenerate   = document.getElementById('btnGenerate');
const mainForm      = document.getElementById('mainForm');

// ── Upload area click & drag ──────────────────────────────
uploadArea.addEventListener('click', () => pdfInput.click());

uploadArea.addEventListener('dragover', e => {
  e.preventDefault();
  uploadArea.classList.add('drag-over');
});
uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
uploadArea.addEventListener('drop', e => {
  e.preventDefault();
  uploadArea.classList.remove('drag-over');
  const file = e.dataTransfer.files[0];
  if (file && file.type === 'application/pdf') {
    setFile(file);
  } else {
    alert('Hanya file PDF yang diterima.');
  }
});

pdfInput.addEventListener('change', () => {
  if (pdfInput.files[0]) setFile(pdfInput.files[0]);
});

function setFile(file) {
  // Inject into file input via DataTransfer
  const dt = new DataTransfer();
  dt.items.add(file);
  pdfInput.files = dt.files;

  uploadArea.classList.add('has-file');
  uploadFilename.textContent = '✓ ' + file.name;
  updatePreview();
}

// ── Live preview ──────────────────────────────────────────
judulInput.addEventListener('input', updatePreview);
matkulInput.addEventListener('input', updatePreview);

function updatePreview() {
  const judul  = judulInput.value.trim();
  const matkul = matkulInput.value.trim();

  if (!judul && !matkul) {
    previewDiv.style.display = 'none';
    return;
  }

  const judulText  = judul  || '[JUDUL/TEMA MATERI SOSIALISASI]';
  const matkulText = matkul || '[MATA KULIAH]';

  previewPara.textContent =
    `Berkaitan dengan program pemerintah di bidang pendidikan dalam mewujudkan keterkaitan ` +
    `dan kesepadanan (Link and Match) antara dunia pendidikan dan dunia kerja, maka kami ` +
    `Universitas Bina Sarana Informatika mengajukan permohonan izin untuk dapat melaksanakan ` +
    `kegiatan sosialisasi dan edukasi terkait program "${judulText}" untuk mata kuliah ${matkulText}. ` +
    `Kegiatan ini ditujukan kepada siswa/i maupun staf pengajar di sekolah/instansi yang ` +
    `Bapak/Ibu pimpin guna menyelaraskan perkembangan teknologi data di industri dengan ` +
    `kurikulum pendidikan.`;

  previewDiv.style.display = 'block';
}

// ── Form submission loading state ────────────────────────
mainForm.addEventListener('submit', () => {
  btnGenerate.disabled = true;
  btnGenerate.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite">
      <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
    </svg>
    Sedang memproses PDF…
  `;
});

// CSS spin animation
const style = document.createElement('style');
style.textContent = `@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }`;
document.head.appendChild(style);
