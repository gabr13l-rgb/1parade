<?php
session_start();
include '../database/koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Ambil data user berdasarkan username
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $data = mysqli_fetch_assoc($result);

    // Verifikasi password dengan password_verify
    if (password_verify($password, $data['password'])) {
        // Password cocok, buat session
        
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        // Arahkan ke halaman sesuai role
       
            // Arahkan sesuai role
    if ($data['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../kasir.php");
    }
    exit;
        } else {
            echo "Role tidak dikenal.";
        }
    } else {
        // Password salah
        echo "<script>alert('Password salah!'); window.location='../login.php';</script>";
    }
 
?>