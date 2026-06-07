<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Tangkap ID dari URL
$id = $_GET['id'] ?? '';

// Ambil data absen yang mau diedit
$stmt = $pdo->prepare("SELECT * FROM absensi WHERE id_absen = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die("Data tidak ditemukan!");
}

// Jika tombol simpan ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keterangan_baru = $_POST['keterangan'];
    
    $update = $pdo->prepare("UPDATE absensi SET keterangan = ? WHERE id_absen = ?");
    $update->execute([$keterangan_baru, $id]);
    
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Absen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* Tombol Utama (Solid) */
    .btn-pink { 
        background-color: #FFB6D9; 
        color: white; 
        border: none; 
        padding: 10px 20px;
        border-radius: 8px;
    }
    .btn-pink:hover { 
        background-color: #ff9acc; 
        color: white; 
    }

    /* Tombol Batal (Garis Tepi) */
    .btn-outline-pink {
        background-color: transparent;
        color: #FFB6D9;
        border: 2px solid #FFB6D9;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: block;
        text-align: center;
    }
    .btn-outline-pink:hover {
        background-color: #FFB6D9;
        color: white;
    }
</style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card mx-auto shadow" style="max-width: 400px;">
            <div class="card-body">
                <h4 class="mb-3">Edit Status</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label>Keterangan:</label>
                        <select name="keterangan" class="form-select">
                            <option value="Hadir" <?php if($data['keterangan'] == 'Hadir') echo 'selected'; ?>>Hadir</option>
                            <option value="Sakit" <?php if($data['keterangan'] == 'Sakit') echo 'selected'; ?>>Sakit</option>
                            <option value="Izin" <?php if($data['keterangan'] == 'Izin') echo 'selected'; ?>>Izin</option>
                            <option value="Terlambat" <?php if($data['keterangan'] == 'Terlambat') echo 'selected'; ?>>Terlambat</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-pink w-100">Simpan Perubahan</button>
                    
                    <a href="admin.php" class="btn btn-outline-pink w-100 mt-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>