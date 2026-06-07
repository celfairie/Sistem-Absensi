<?php
session_start();
require 'koneksi.php';

// Proteksi akses
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 1. LOGIKA PAGINATION
$limit = 50; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil total data
$stmt_count = $pdo->query("SELECT COUNT(*) FROM absensi");
$total_data = $stmt_count->fetchColumn();
$total_pages = ceil($total_data / $limit);

// Ambil data absen dengan limit & offset
$stmt = $pdo->prepare("SELECT * FROM absensi ORDER BY waktu_absen DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$data_absen = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --brand-pink: #9CAF88; }
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; margin: 0; }
        
        /* SIDEBAR */
        .sidebar { 
            background-color: var(--brand-pink); 
            height: 100vh; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 250px; 
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar-logo { padding: 30px 20px; text-align: center; font-weight: 700; font-size: 20px; color: white; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .sidebar nav { flex-grow: 1; padding-top: 20px; }
        
        /* Link Sidebar (Style bersih) */
        .sidebar a { color: white; text-decoration: none; font-weight: 400; display: block; padding: 15px 25px; transition: 0.3s; }
        .sidebar a:hover { background-color: rgba(255,255,255,0.1); }
        
        /* Link Log Out (Style sesuai gambar) */
        .logout-link { padding-bottom: 30px; }
        .logout-link a { color: white !important; opacity: 0.8; font-weight: 400; }
        .logout-link a:hover { opacity: 1; background: none; }

        /* KONTEN UTAMA */
        .main-content { margin-left: 250px; padding: 40px; }
        .card { border-radius: 20px; border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: var(--brand-pink); color: #fff; }
        .btn-action { border-radius: 8px; margin-right: 5px; }
        .table th, .table td { padding: 15px 25px !important; vertical-align: middle; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo">Admin Panel</div>
        <nav>
            <a href="admin.php"><i class="bi bi-journal-text me-2"></i> Data Absensi</a>
        </nav>
        <div class="logout-link">
            <a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <h3 class="mb-4 fw-bold">Data Absensi Mahasiswa</h3>

        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NPM</th>
                            <th>Waktu</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offset + 1; foreach ($data_absen as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['npm'] ?? '-'); ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($row['waktu_absen'])); ?></td>
                            <td>
                                <img src="uploads/<?php echo $row['foto_path']; ?>" width="60" class="rounded border">
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id_absen']; ?>" class="btn btn-sm btn-outline-success btn-action">Edit</a>
                                <a href="hapus.php?id=<?php echo $row['id_absen']; ?>" class="btn btn-sm btn-outline-danger btn-action" onclick="return confirm('Yakin hapus?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

</body>
</html>