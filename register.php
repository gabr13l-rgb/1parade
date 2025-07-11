<?php
session_start();
include 'database/koneksi.php';

$pesan = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role     = $_POST["role"];

    if (empty($username) || empty($password) || empty($role)) {
        $pesan = "Semua field wajib diisi.";
    } else {
        // Cek apakah username sudah digunakan
        $cek = mysqli_prepare($kconn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $pesan = "Username sudah digunakan.";
        } else {
            // Simpan user baru
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $query = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($query, "sss", $username, $hash, $role);

            if (mysqli_stmt_execute($query)) {
                $pesan = "Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
            } else {
                $pesan = "Gagal registrasi: " . mysqli_error($conn);
            }
        }

        mysqli_stmt_close($cek);
        mysqli_stmt_close($query);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi User</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; padding: 50px; }
        form { background: white; padding: 20px; border-radius: 10px; width: 300px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 10px; margin-top: 10px; border-radius: 5px; border: 1px solid #ccc; }
        h2 { text-align: center; }
        .pesan { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Registrasi</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="pegawai">Kasir</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Daftar</button>
        <div class="pesan"><?= $pesan ?></div>
    </form>
</body>
</html>
