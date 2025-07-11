<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke sistem Kedai 1parade untuk akses ke menu kasir dan laporan penjualan">
    <title>Login Sistem - Kedai 1parade | Coffee Shop Management</title>
    <link rel="stylesheet" href="assets/css/login.css"> <!-- Link ke file CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Link Font Awesome -->
</head>
<body>
    <div class="login-container">
        <img src="assets/img/logo.png" alt="Logo Kedai 1parade" class="login-logo"> <!-- Ganti dengan logo kedai -->
        <h2>Login</h2>
        <form action="auth/process_login.php" method="POST"> <!-- Ganti ke process_login.php -->
            <div class="input-group">
                <label for="username" class="sr-only">Username</label>
                <div class="input-icon">
                    <i class="fas fa-user" aria-hidden="true"></i> <!-- Ikon pengguna -->
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required aria-required="true">
                </div>
            </div>
            <div class="input-group">
                <label for="password" class="sr-only">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock" aria-hidden="true"></i> <!-- Ikon kunci -->
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required aria-required="true">
                </div>
            </div>
            <button type="submit" class="btn-login" aria-label="Masuk ke sistem">
                <span class="btn-text">Login</span>
                <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
            </button>
            <p class="error-message"><?php if(isset($error)) echo $error; ?></p> <!-- Tempat untuk menampilkan pesan kesalahan -->
        </form>
    </div>
</body>
</html>
