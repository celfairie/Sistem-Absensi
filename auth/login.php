<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f0f4ff; font-family: 'Segoe UI', sans-serif; }
        .login-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 450px; padding: 2rem; }
        .btn-custom { background-color: #9CAF88; color: #fff; padding: 10px; width: 100%; border-radius: 8px; border: none; }
        .btn-custom:hover { background-color: #333; color: white; }
        .btn-success {
            border-radius: 8px; /* Agar sudutnya melengkung sama dengan btn-custom */
            padding: 10px;      /* Agar tingginya sama */
            border: none;
            width: 100%;       /* Agar lebarnya sama */}
        .form-control { background-color: #f8f9fa; border: 1px solid #e0e0e0; padding: 12px; border-radius: 8px; }
        .demo-text { font-size: 0.85rem; color: #666; margin-top: 20px; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card">
        <div class="text-center mb-4">
            <h3 class="fw-bold">Sistem Absensi</h3>
            <p class="text-muted">Attendance Management System</p>
        </div>

        <form action="proses_login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            <?php if (isset($_SESSION['error'])): ?>
                <p style="color: red; font-size: 0.85rem; margin-top: 5px;">
                    <?php echo $_SESSION['error']; ?>
                </p>
                <?php unset($_SESSION['error']); // Menghapus pesan error agar tidak muncul terus saat di-refresh ?>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn-custom">Sign in</button>
            <button type="submit" name="action" value="register" class="btn-success">Daftar</button>
        </form>
    </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function (e) {
        // Toggle tipe input
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle ikon mata
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });
</script>

</body>
</html>