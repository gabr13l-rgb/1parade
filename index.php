<?php
session_start();

if (!isset($_SESSION['username'])) {
    // Belum login, arahkan ke login
    header("Location: login.php");
    exit;
}

// Sudah login, arahkan ke halaman sesuai role
if ($_SESSION['role'] == 'admin') {
    header("Location: admin/dashboard.php");
} else {
    header("Location: kasir.php");
}
exit;
?>
