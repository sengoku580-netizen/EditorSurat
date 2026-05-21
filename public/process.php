<?php
// process.php - Handles form submission, calls Python PDF editor, serves download
session_start();

// ─── Config ─────────────────────────────────────────────
define('MAX_FILE_SIZE', 10 * 1024 * 1024);  // 10 MB
define('UPLOAD_DIR',  __DIR__ . '/uploads/');
define('OUTPUT_DIR',  __DIR__ . '/output/');
define('PYTHON_SCRIPT', __DIR__ . '/edit_surat.py');

// Detect Python binary (laragon ships python3 or python)
function findPython(): string {
    foreach (['python3', 'python'] as $bin) {
        $out = shell_exec("$bin --version 2>&1");
        if ($out && str_contains($out, 'Python')) return $bin;
    }
    return 'python3'; // fallback
}

// ─── Validation ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$judul  = trim($_POST['judul']  ?? '');
$matkul = trim($_POST['matkul'] ?? '');

if (empty($judul) || empty($matkul)) {
    $_SESSION['error'] = 'Judul dan mata kuliah wajib diisi.';
    header('Location: index.php');
    exit;
}

if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Gagal upload file. Pastikan file PDF dipilih dan ukurannya tidak melebihi 10 MB.';
    header('Location: index.php');
    exit;
}

$file = $_FILES['pdf_file'];

// Check size
if ($file['size'] > MAX_FILE_SIZE) {
    $_SESSION['error'] = 'Ukuran file melebihi batas 10 MB.';
    header('Location: index.php');
    exit;
}

// Check MIME
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
if ($mime !== 'application/pdf') {
    $_SESSION['error'] = 'File yang diupload bukan PDF yang valid.';
    header('Location: index.php');
    exit;
}

// ─── Ensure directories exist ────────────────────────────
foreach ([UPLOAD_DIR, OUTPUT_DIR] as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}

// ─── Save uploaded file ──────────────────────────────────
$uid         = uniqid('bsi_', true);
$input_path  = UPLOAD_DIR  . $uid . '_input.pdf';
$output_path = OUTPUT_DIR  . $uid . '_surat_sosialisasi.pdf';
$output_name = 'Surat_Sosialisasi_BSI.pdf';

if (!move_uploaded_file($file['tmp_name'], $input_path)) {
    $_SESSION['error'] = 'Gagal menyimpan file upload. Cek permission folder uploads/.';
    header('Location: index.php');
    exit;
}

// ─── Run Python edit script ──────────────────────────────
$python = findPython();

// Escape arguments safely
$args = implode(' ', array_map('escapeshellarg', [
    $input_path,
    $output_path,
    $judul,
    $matkul,
]));

$command     = "$python " . escapeshellarg(PYTHON_SCRIPT) . " $args 2>&1";
$py_output   = shell_exec($command);
$exit_ok     = file_exists($output_path) && filesize($output_path) > 0;

// Clean up input file
@unlink($input_path);

if (!$exit_ok) {
    $_SESSION['error'] = 'Gagal memproses PDF. Detail: ' . htmlspecialchars($py_output ?? 'unknown error');
    header('Location: index.php');
    exit;
}

// ─── Serve the file as download ──────────────────────────
$filesize = filesize($output_path);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $output_name . '"');
header('Content-Length: ' . $filesize);
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($output_path);

// Clean up output file after serving
@unlink($output_path);
exit;
