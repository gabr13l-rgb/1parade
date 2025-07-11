<?php
if (session_status() == PHP_SESSION_NONE) session_start();
$role = $_SESSION['role'] ?? 'guest';
?>

<div class="sidebar">
  <a href="index.php" class="brand">🧾 ONE PARADE</a>
  <a href="kasir.php" class="<?= basename($_SERVER['PHP_SELF']) == 'kasir.php' ? 'active' : '' ?>">Transaksi Menu</a>
  <a href="riwayat_kasir.php" class="<?= basename($_SERVER['PHP_SELF']) == 'riwayat_kasir.php' ? 'active' : '' ?>">Riwayat</a>
  <a href="closing_kasir.php" class="<?= basename($_SERVER['PHP_SELF']) == 'closing_kasir.php' ? 'active' : '' ?>">Closing</a>

  <?php if ($role === 'admin'): ?>
    <hr>
    <a href="admin/dashboard.php">Dashboard</a>
    <a href="admin/menu.php">Kelola Menu</a>
    <a href="admin/laporan.php">Laporan</a>
    <a href="admin/user.php">Pengguna</a>
  <?php endif; ?>

  <hr>
  <a href="logout.php">Logout</a>
  <hr>
  <div class="kasir-info mt-auto">
    <small>👤 <?= $_SESSION['username'] ?? 'Kasir' ?></small>
    <small id="clock">⏰ --:--</small>
  </div>
</div>
