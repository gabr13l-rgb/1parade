<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'pegawai') {
    header("Location: login.php");
    exit;
}
include 'database/koneksi.php';

$kasir_id = $_SESSION['user_id'];
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Ambil data transaksi
$query = "SELECT * FROM transaksi 
          WHERE kasir_id='$kasir_id' 
          AND DATE(waktu_transaksi) = '$tanggal' 
          ORDER BY waktu_transaksi DESC";
$result = mysqli_query($conn, $query);

// Ambil ringkasan pembayaran
$q_summary = mysqli_query($conn, "
    SELECT 
      SUM(CASE WHEN metode = 'QRIS' THEN total ELSE 0 END) AS total_qris,
      SUM(CASE WHEN metode = 'Tunai' THEN total ELSE 0 END) AS total_tunai
    FROM transaksi 
    WHERE kasir_id = '$kasir_id' AND DATE(waktu_transaksi) = '$tanggal'
");
$summary = mysqli_fetch_assoc($q_summary);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <!-- Tambahkan link font dan CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/kasir_unified.css">
</head>
<body>
<div class="main-container">
  <?php include 'layout/sidebar.php'; ?>
  
  <div class="content riwayat-container">
    <div class="riwayat-header">
      <h1 class="riwayat-title"><i class="fas fa-history"></i> Riwayat Transaksi</h1>
      <span><?= date('d F Y', strtotime($tanggal)) ?></span>
    </div>

    <form method="GET" class="form-filter">
      <input type="date" name="tanggal" value="<?= $tanggal ?>">
      <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Filter</button>
    </form>
    <div class="summary-card">
      <h4>💳 Ringkasan Pembayaran</h4>
      <p>QRIS: <strong>Rp <?= number_format($summary['total_qris'], 0, ',', '.') ?></strong></p>
      <p>Tunai: <strong>Rp <?= number_format($summary['total_tunai'], 0, ',', '.') ?></strong></p>
    </div>

    <?php while ($trx = mysqli_fetch_assoc($result)) { ?>
      <div class="card-transaksi">
        <div class="header">
          <span class="trx-id">#<?= $trx['id'] ?></span>
          <span class="trx-time"><?= date('H:i', strtotime($trx['waktu_transaksi'])) ?></span>
        </div>
        <div class="total">Total: Rp <?= number_format($trx['total'], 0, ',', '.') ?></div>

        <ul class="list-menu">
          <?php
          $q_detail = mysqli_query($conn, "
            SELECT dt.*, m.nama_menu 
            FROM detail_transaksi dt 
            JOIN menu m ON m.id = dt.menu_id 
            WHERE dt.transaksi_id = '{$trx['id']}'
          ");
          while ($item = mysqli_fetch_assoc($q_detail)) {
            echo "<li>
              <span>{$item['nama_menu']} x{$item['jumlah']}</span>
              <span>Rp " . number_format($item['subtotal'], 0, ',', '.') . "</span>
            </li>";
          }
          ?>
        </ul>

        <div class="footer-info">
          Metode: <?= $trx['metode'] ?> |
          Bayar: Rp <?= number_format($trx['metode'] === 'QRIS' ? $trx['total'] : $trx['bayar'], 0, ',', '.') ?>
        </div>
      </div>
    <?php } ?>

    <div class="back-btn-container">
      <a href="kasir.php" class="btn-back">⬅️ Kembali ke Kasir</a>
    </div>
  </div>
</div>

  <script>
  function updateClock() {
    const now = new Date();
    const jam = now.getHours().toString().padStart(2, '0');
    const menit = now.getMinutes().toString().padStart(2, '0');
    document.getElementById('clock').textContent = `⏰ ${jam}:${menit}`;
  }

  setInterval(updateClock, 1000);
  updateClock();
</script>
<script>
  // Auto refresh tiap 60 detik
  setInterval(() => {
    // Refresh hanya jika tab aktif
    if (document.visibilityState === "visible") {
      location.reload();
    }
  }, 60000); // 60 detik
</script>
</body>
</html>
