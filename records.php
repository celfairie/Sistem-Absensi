<?php
session_start();
require 'config/koneksi.php';

// Proteksi halaman user
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
    header("Location: auth/login.php");
    exit();
}

// Mengambil data riwayat absensi user yang sedang login
$id_user = $_SESSION['id_user'];
$stmt = $pdo->prepare("SELECT * FROM absensi WHERE id_user = ? ORDER BY waktu_absen DESC");
$stmt->execute([$id_user]);
$riwayat_absen = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Records - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-color: #9CAF88;
            --brand-pink: #FFB6D9;
        }
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; min-height: 100vh; }
        
        /* Sidebar (Sama dengan index.php) */
        .sidebar { background-color: var(--sidebar-color); min-height: 100vh; color: white; position: fixed; width: 250px; }
        .sidebar-logo { padding: 30px 20px; text-align: center; font-weight: 700; font-size: 20px; }
        .nav-link { color: rgba(255,255,255,0.7); padding: 15px 25px; display: flex; align-items: center; gap: 10px; font-weight: 500; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.1); border-left: 4px solid var(--brand-pink); }
        .logout-link { position: absolute; bottom: 30px; width: 100%; color: rgba(255,255,255,0.7); }

        .main-content { margin-left: 250px; padding: 40px; }
        .info-card { background: white; border-radius: 40px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        /* Table Styling */
        .table thead { color: #555; border-bottom: 2px solid #eee; }
        .table th { font-weight: 700; text-transform: uppercase; font-size: 0.9rem; padding: 20px; }
        .table td { padding: 20px; font-weight: 500; }
        .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo"><i class="bi bi-person-check-fill"></i> SISTEM ABSENSI</div>
    <nav class="mt-4">
        <a href="index.php" class="nav-link"><i class="bi bi-camera-fill"></i> Attendance Check In</a>
        <a href="records.php" class="nav-link active"><i class="bi bi-journal-text"></i> Records</a>
    </nav>
    <div class="logout-link px-4">
        <a href="auth/logout.php" class="nav-link text-white"><i class="bi bi-box-arrow-left"></i> Log Out</a>
    </div>
</div>

<div class="main-content">
    <div class="info-card">
        <h3>Attendance Records</h3>
        <table class="table table-hover align-middle mt-4">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>NPM</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($riwayat_absen) > 0): ?>
                    <?php foreach ($riwayat_absen as $row): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($row['waktu_absen'])); ?></td>
                        <td><?php echo date('H:i', strtotime($row['waktu_absen'])); ?></td>
                        <td><?php echo htmlspecialchars($row['npm'] ?? '-'); ?></td>
                        <td>
                            <?php
                            $status = $row['keterangan'] ?? 'Hadir'; 
                            if ($status == 'Terlambat') {
                                $warna = 'bg-warning text-dark'; // Kuning untuk terlambat
                                } elseif ($status == 'Sakit' || $status == 'Izin') {
                                    $warna = 'bg-info text-white';   // Biru untuk sakit/izin
                                    } else {
                                        $warna = 'bg-success';           // Hijau untuk hadir
                                        }
                                ?>
                                <span class="badge <?php echo $warna; ?> status-badge">
                                    <?php echo $status; ?>
                                </span>
                            </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">Belum ada riwayat absensi.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>