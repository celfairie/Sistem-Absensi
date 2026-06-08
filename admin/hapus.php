<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? '';

if ($id) {
    // 1. Cari nama file foto di database
    $stmt = $pdo->prepare("SELECT foto_path FROM absensi WHERE id_absen = ?");
    $stmt->execute([$id]);
    $absen = $stmt->fetch();

    if ($absen) {
        // 2. Hapus file foto dari folder
        $file_path = "uploads/" . $absen['foto_path'];
        if (file_exists($file_path)) {
            unlink($file_path); // Fungsi PHP untuk menghapus file
        }
        
        // 3. Hapus data dari database (DELETE)
        $del = $pdo->prepare("DELETE FROM absensi WHERE id_absen = ?");
        $del->execute([$id]);
    }
}

// Kembali ke halaman admin
header("Location:admin.php");
exit();
?>