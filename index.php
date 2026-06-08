<?php
session_start();
require 'config/koneksi.php';

// Proteksi halaman user
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
    header("Location: auth/login.php");
    exit();
}

// Menyiapkan data otomatis
$tanggal_hari_ini = date('Y/m/d');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check In - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-color: #9CAF88; 
            --brand-pink: #FFB6D9;
            --brand-green: #C8F28A;
        }
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; min-height: 100vh; }
        
        /* Sidebar Styling */
        .sidebar { background-color: var(--sidebar-color); min-height: 100vh; color: white; position: fixed; width: 250px; }
        .sidebar-logo { padding: 30px 20px; text-align: center; font-weight: 700; font-size: 20px; }
        .nav-link { color: rgba(255,255,255,0.7); padding: 15px 25px; display: flex; align-items: center; gap: 10px; font-weight: 500; text-decoration: none; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.1); border-left: 4px solid var(--brand-pink); }
        .logout-link { position: absolute; bottom: 30px; width: 100%; color: rgba(255,255,255,0.7); }

        /* Main Content Layout */
        .main-content { margin-left: 250px; padding: 40px; }
        
        /* Card */
        .info-card { background: white; border-radius: 40px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 900px; margin: 0 auto; }
        .info-card h3 { color: var(--sidebar-color); font-weight: 700; border-bottom: 5px solid var(--brand-pink); display: inline-block; margin-bottom: 15px; }
        
        .input-custom, .form-control-static {
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 20px;
            padding: 12px 25px;
            font-size: 1rem;
            color: #666;
            width: 100%;
            display: block;
        }
        .form-label { font-weight: 600; color: #555; margin-left: 10px; margin-bottom: 8px; }
        
        #kamera { width: 100%; height: 280px; background-color: #ddd; border-radius: 20px; object-fit: cover; }
        .btn-simpan { background-color: #d9534f; color: white; border: none; padding: 12px 40px; border-radius: 20px; font-weight: 600; letter-spacing: 1px; transition: 0.3s; }
        .btn-simpan:hover { background-color: #c9302c; transform: scale(1.03); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo"><i class="bi bi-person-check-fill"></i> SISTEM ABSENSI</div>
    <nav class="mt-4">
        <a href="index.php" class="nav-link active"><i class="bi bi-camera-fill"></i> Attendance Check In</a>
        <a href="records.php" class="nav-link"><i class="bi bi-journal-text"></i> Records</a>
    </nav>
    <div class="logout-link px-4">
        <a href="auth/logout.php" class="nav-link text-white"><i class="bi bi-box-arrow-left"></i> Log Out</a>
    </div>
</div>

<div class="main-content">
    <div class="info-card">
        <h3>Attendance Check In</h3>
        
        <div class="mb-4">
            <span style="font-size: 1.15rem; font-weight: 500; color: #555;">
                Welcome, <strong style="color: var(--sidebar-color);"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>!
            </span>
        </div>

        <form id="formAbsen" action="proses_absen.php" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="nama_lengkap" class="form-control" 
                        value="<?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">NPM</label>
                        <input type="text" name="npm" class="form-control" placeholder="Masukkan NPM Anda" 
                        maxlength="15" pattern="\d{1,15}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <div class="form-control-static"><?php echo $tanggal_hari_ini; ?></div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label d-block text-start">Camera</label>
                    <video id="kamera" autoplay playsinline class="img-fluid rounded"></video>
                    <canvas id="canvas" style="display:none;" class="img-fluid rounded"></canvas>
    
                    <input type="hidden" name="foto_base64" id="foto_base64">
                    
                    <div class="mt-3">
                        <button type="button" id="btnCapture" class="btn btn-primary w-100">Ambil Foto</button>
                        <div id="actionButtons" style="display:none;">
                            <button type="button" id="btnRetake" class="btn btn-warning w-100 mb-2">Retake</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-danger w-100 rounded-pill mt-3" style="position: relative; z-index: 9999;">Simpan Absen</button>
            </div>
        </form>
    </div>
</div>

<script>
    const video = document.getElementById('kamera');
    const canvas = document.getElementById('canvas');
    const inputFoto = document.getElementById('foto_base64');
    
    const btnCapture = document.getElementById('btnCapture');
    const btnRetake = document.getElementById('btnRetake');
    const btnSubmit = document.getElementById('btnSubmit');
    const actionButtons = document.getElementById('actionButtons');

    // 1. Inisialisasi Kamera
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => { video.srcObject = stream; })
        .catch(err => { alert("Kamera tidak aktif: " + err); });

    // 2. Fungsi Ambil Foto (Capture)
    btnCapture.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Simpan data ke input hidden
        inputFoto.value = canvas.toDataURL('image/png');

        // Toggle UI
        video.style.display = 'none';      // Sembunyi kamera
        canvas.style.display = 'block';    // Tampil hasil
        btnCapture.style.display = 'none'; // Sembunyi tombol ambil
        actionButtons.style.display = 'block'; // Tampil tombol retake/submit
    });

    // 3. Fungsi Retake
    btnRetake.addEventListener('click', function() {
        // Toggle UI kembali ke awal
        video.style.display = 'block';
        canvas.style.display = 'none';
        btnCapture.style.display = 'block';
        actionButtons.style.display = 'none';
        inputFoto.value = ''; // Reset data
    });
</script>

</body>
</html>