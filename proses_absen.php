<?php
session_start();
require 'config/koneksi.php';

// Jika belum login, arahkan ke halaman login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Menangkap data dari form index.php
$foto_b64 = $_POST['foto_base64'] ?? '';
$nama_input = $_POST['username'] ?? $_POST['nama_lengkap'] ?? ''; 
$npm = trim($_POST['npm']);

// ==========================================
// PENGATURAN TAMPILAN UI (DEFAULT: BERHASIL)
// ==========================================
$judul_card = 'Absen Berhasil!';
$pesan = 'Foto Berhasil di Simpan.';
$icon_class = 'bi-check-circle-fill';
$icon_color = '#FFB6D9';
$header_color = '#9CAF88';

// ==========================================
// CEK LOGIKA ERROR
// ==========================================
if (empty(trim($nama_input))) {
    $judul_card = 'Absen Gagal!';
    $pesan = 'Nama atau Username belum diisi!';
    $icon_class = 'bi-x-circle-fill';
    $icon_color = '#d9534f';
} elseif (empty($foto_b64)) {
    $judul_card = 'Absen Gagal!';
    $pesan = 'Foto tidak boleh kosong!';
    $icon_class = 'bi-x-circle-fill';
    $icon_color = '#d9534f';
} else {
    // Memeriksa format base64
    $image_parts = explode(";base64,", $foto_b64);
    
    if (count($image_parts) != 2) {
        $judul_card = 'Absen Gagal!';
        $pesan = 'Format foto tidak valid!';
        $icon_class = 'bi-x-circle-fill';
        $icon_color = '#d9534f';
    } else {
        // JIKA SEMUA AMAN, SIMPAN KE DATABASE
        $image_base64 = base64_decode($image_parts[1]);
        $id_user = $_SESSION['id_user'];
        $nama_file = 'absen_' . $id_user . '_' . time() . '.jpg';
        $lokasi_simpan = 'uploads/' . $nama_file;
        
        try {
            file_put_contents($lokasi_simpan, $image_base64);
            $stmt = $pdo->prepare("INSERT INTO absensi (id_user, nama_lengkap, npm, waktu_absen, foto_path, keterangan) VALUES (?, ?, ?, NOW(), ?, 'Hadir')");
            $stmt->execute([$id_user, $nama_input, $npm, $nama_file]);
        } catch(PDOException $e) {
            $judul_card = 'Absen Gagal!';
            $pesan = 'Sistem gagal menyimpan data ke database!';
            $icon_class = 'bi-x-circle-fill';
            $icon_color = '#d9534f';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $judul_card; ?> - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --sidebar-color: #9CAF88; --brand-pink: #FFB6D9; }
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; min-height: 100vh; }
        
        .sidebar { background-color: var(--sidebar-color); min-height: 100vh; color: white; position: fixed; width: 250px; }
        .sidebar-logo { padding: 30px 20px; text-align: center; font-weight: 700; font-size: 20px; }
        .nav-link { color: rgba(255,255,255,0.7); padding: 15px 25px; display: flex; align-items: center; gap: 10px; font-weight: 500; text-decoration: none; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.1); border-left: 4px solid var(--brand-pink); }
        .logout-link { position: absolute; bottom: 30px; width: 100%; color: rgba(255,255,255,0.7); }

        .main-content { margin-left: 250px; padding: 40px; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        
        .success-card { background: white; border-radius: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); width: 100%; max-width: 800px; overflow: hidden; margin-bottom: 30px; }
        .card-header-custom { background-color: <?php echo $header_color; ?>; color: white; padding: 20px; text-align: center; font-size: 1.5rem; font-weight: 700; }
        .card-body-custom { padding: 60px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; }
        
        .check-icon i { font-size: 80px; color: <?php echo $icon_color; ?>; line-height: 1; }
        .text-box { border: 1px solid #ccc; border-radius: 30px; padding: 12px 40px; display: inline-block; color: #333; font-weight: 500; font-size: 1rem; margin-top: 25px; }
        
        .btn-kembali { background-color: #6c757d; color: white; padding: 12px 50px; border-radius: 10px; font-weight: 700; border: none; text-transform: uppercase; text-decoration: none; transition: 0.3s; letter-spacing: 1px; }
        .btn-kembali:hover { background-color: #5c636a; transform: scale(1.05); color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">
        <i class="bi bi-person-check-fill"></i> ABSENSI APP
    </div>
    <nav class="mt-4">
        <a href="index.php" class="nav-link active">
            <i class="bi bi-camera-fill"></i> Attendance Check In
        </a>
        <a href="records.php" class="nav-link">
            <i class="bi bi-journal-text"></i> Records
        </a>
    </nav>
    <div class="logout-link px-4">
        <a href="auth/logout.php" class="nav-link text-white"><i class="bi bi-box-arrow-left"></i> Log Out</a>
    </div>
</div>

<div class="main-content">
    <div class="success-card">
        <div class="card-header-custom">
            <?php echo $judul_card; ?>
        </div>
        <div class="card-body-custom">
            <div class="check-icon">
                <i class="bi <?php echo $icon_class; ?>"></i>
            </div>
            <div class="text-box">
                <?php echo $pesan; ?>
            </div>
        </div>
    </div>
    <a href="index.php" class="btn-kembali">KEMBALI</a>
</div>

</body>
</html>