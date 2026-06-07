<?php
session_start(); 
require 'koneksi.php';

// Menangkap data dari form
$action = $_POST['action'] ?? 'login'; // Default ke login jika tombol tidak terdeteksi
$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (empty($username) || empty($password)) {
    die("Username dan Password tidak boleh kosong! <a href='login.php'>Kembali</a>");
}

try {
    // 1. LOGIKA DAFTAR
    // 1. LOGIKA DAFTAR
if ($action == 'register') {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
    $stmt->execute([$username, $hashed_password]);

    // TAMPILAN SUKSES
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        <style>
            body { background-color: #f0f4ff; height: 100vh; display: flex; align-items: center; justify-content: center; }
            .card { border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; padding: 2rem; }
        </style>
    </head>
    <body>
        <div class="card text-center">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            <h3 class="mt-3">Daftar Berhasil!</h3>
            <p class="text-muted">Akun kamu sudah dibuat. Silakan login untuk melanjutkan.</p>
            <a href="login.php" class="btn btn-success w-100 mt-3">Login Sekarang</a>
        </div>
    </body>
    </html>
    <?php
    exit(); // Pastikan script berhenti di sini setelah menampilkan UI
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Simpan data ke Session
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username']; // <--- BARIS INI YANG DITAMBAHKAN

            if ($user['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Sorry, your password/username was incorrect. Please double-check your password/username.";
            header("Location: login.php");
            exit();
            }
    }
    
    } catch(PDOException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage() . " <a href='login.php'>Kembali</a>";
}
?>