<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../database/koneksi.php';

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

$q = mysqli_query($conn, "
  SELECT transaksi.*, users.username 
  FROM transaksi 
  JOIN users ON transaksi.kasir_id = users.id
  WHERE DATE(transaksi.waktu_transaksi) = '$tanggal'
  ORDER BY transaksi.waktu_transaksi DESC
");

$q_rekap = mysqli_query($conn, "
  SELECT menu.nama_menu, 
         SUM(detail_transaksi.jumlah) AS total_terjual, 
         SUM(detail_transaksi.subtotal) AS total_penjualan
  FROM transaksi
  JOIN detail_transaksi ON transaksi.id = detail_transaksi.transaksi_id
  JOIN menu ON menu.id = detail_transaksi.menu_id
  WHERE DATE(transaksi.waktu_transaksi) = '$tanggal'
  GROUP BY detail_transaksi.menu_id
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penjualan</title>
    <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/admin_laporan.css">
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>📊 Laporan Penjualan</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary back-btn">⬅️ Dashboard</a>
  </div>

  <!-- Filter -->
  <form method="GET" class="row g-3 mb-4">
    <div class="col-md-4">
      <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>">
    </div>
    <div class="col-md-2">
      <button class="btn btn-dark w-100">Tampilkan</button>
    </div>
  </form>

  <!-- Rekap Menu -->
  <h4 class="mb-3">📦 Rekap Penjualan Menu (<?= $tanggal ?>)</h4>
  <div class="table-responsive">
    <table class="table table-bordered table-striped bg-white">
      <thead>
        <tr>
          <th>Nama Menu</th>
          <th>Jumlah Terjual</th>
          <th>Total Penjualan</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $grand_total = 0;
        while ($r = mysqli_fetch_assoc($q_rekap)) {
          $grand_total += $r['total_penjualan'];
          echo "<tr>
                  <td>{$r['nama_menu']}</td>
                  <td>{$r['total_terjual']}</td>
                  <td>Rp " . number_format($r['total_penjualan'], 0, ',', '.') . "</td>
                </tr>";
        }
        ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" class="fw-bold">Total Omzet Menu</td>
          <td class="fw-bold">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- Transaksi Detail -->
  <h4 class="mt-5 mb-3">🧾 Daftar Transaksi (<?= $tanggal ?>)</h4>
  <?php
  $total_hari_ini = 0;
  while ($trx = mysqli_fetch_assoc($q)) {
    $total_hari_ini += $trx['total'];
  ?>
  <div class="card mb-3 shadow-sm">
    <div class="card-header">
      #<?= $trx['id'] ?> - Rp <?= number_format($trx['total'], 0, ',', '.') ?>
      <span class="float-end"><?= $trx['username'] ?> | <?= $trx['metode'] ?> | <?= date('H:i', strtotime($trx['waktu_transaksi'])) ?></span>
    </div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        <?php
        $detail = mysqli_query($conn, "
          SELECT dt.*, m.nama_menu 
          FROM detail_transaksi dt 
          JOIN menu m ON m.id = dt.menu_id 
          WHERE dt.transaksi_id = {$trx['id']}
        ");
        while ($d = mysqli_fetch_assoc($detail)) {
          echo "<li class='list-group-item d-flex justify-content-between'>
                  {$d['nama_menu']} x{$d['jumlah']}
                  <span>Rp " . number_format($d['subtotal'], 0, ',', '.') . "</span>
                </li>";
        }
        ?>
      </ul>
      <div class="mt-3 text-end small text-muted">
        Bayar: Rp <?= number_format($trx['bayar'], 0, ',', '.') ?> |
        Kembali: Rp <?= number_format($trx['kembalian'], 0, ',', '.') ?>
      </div>
    </div>
  </div>
  <?php } ?>

  <div class="alert alert-success mt-4 text-center">
    <strong>Total Penjualan Hari Ini:</strong> <span class="transaksi-total">Rp <?= number_format($total_hari_ini, 0, ',', '.') ?></span>
  </div>
</div>
</body>
</html>
