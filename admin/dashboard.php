<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../database/koneksi.php';

// Periode bulan ini
$bulan_ini = date('Y-m-01');
$hari_terakhir = date('Y-m-t'); // ganti dari date('Y-m-d')

// Total transaksi bulan ini (dihitung dari transaksi)
$q_transaksi = mysqli_query($conn, "
    SELECT COUNT(DISTINCT id) AS total_transaksi 
    FROM transaksi 
    WHERE DATE(waktu_transaksi) BETWEEN '$bulan_ini' AND '$hari_terakhir'
");
$trx = mysqli_fetch_assoc($q_transaksi);

// Total omzet bulan ini (dihitung dari detail_transaksi untuk akurasi)
$q_omzet = mysqli_query($conn, "
    SELECT SUM(dt.subtotal) AS total_omzet
    FROM transaksi t
    JOIN detail_transaksi dt ON t.id = dt.transaksi_id
    WHERE DATE(waktu_transaksi) BETWEEN '$bulan_ini' AND '$hari_terakhir'

");
$omzet = mysqli_fetch_assoc($q_omzet);

// Total kasir (role pegawai)
$q_user = mysqli_query($conn, "SELECT COUNT(*) AS total_user FROM users WHERE role = 'pegawai'");
$user = mysqli_fetch_assoc($q_user);

// Total menu tersedia
$q_menu = mysqli_query($conn, "SELECT COUNT(*) AS total_menu FROM menu");
$menu = mysqli_fetch_assoc($q_menu);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard Admin</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container">
  <h2>📊 Dashboard Admin</h2>
  <p>Selamat datang, <?= $_SESSION['username']; ?>!</p>

  <div class="stat-box">
    <div class="card-stat">
      <h5>Transaksi Bulan Ini</h5>
      <p><?= $trx['total_transaksi'] ?? 0 ?></p>
    </div>
    <div class="card-stat">
      <h5>Omzet Bulan Ini</h5>
      <p>Rp <?= number_format($omzet['total_omzet'], 0, ',', '.') ?></p>
    </div>
    <div class="card-stat">
      <h5>Kasir Terdaftar</h5>
      <p><?= $user['total_user'] ?? 0 ?></p>
    </div>
    <div class="card-stat">
      <h5>Menu Tersedia</h5>
      <p><?= $menu['total_menu'] ?? 0 ?></p>
    </div>
  </div>

  <div class="nav-box">
    <div class="card-nav">
      <h5>☕ Kelola Menu</h5>
      <a href="menu.php">Lihat Menu</a>
    </div>
    <div class="card-nav">
      <h5>📈 Laporan Penjualan</h5>
      <a href="laporan.php">Lihat Laporan</a>
    </div>
    <div class="card-nav">
      <h5>👥 Manajemen User</h5>
      <a href="users.php">Kelola User</a>
    </div>
  </div>

  <div class="logout-btn">
    <a href="../logout.php">Logout</a>
  </div>
</div>

</body>
</html>
