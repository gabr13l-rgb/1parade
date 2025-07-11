<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'pegawai') {
    header("Location: login.php");
    exit;
}
include 'database/koneksi.php';

$kasir_id = $_SESSION['user_id'];
$tanggal = isset($_GET['tanggal']) && $_GET['tanggal'] != '' ? $_GET['tanggal'] : date('Y-m-d');

// Ringkasan transaksi
$q1 = mysqli_query($conn, "
  SELECT
    COUNT(DISTINCT t.id) AS jumlah_transaksi,
    SUM(dt.subtotal) AS total_penjualan
  FROM transaksi t
  JOIN detail_transaksi dt ON dt.transaksi_id = t.id
  WHERE t.kasir_id = '$kasir_id' AND DATE(t.waktu_transaksi) = '$tanggal'
");
$data1 = mysqli_fetch_assoc($q1);

// Total per metode pembayaran
$q_summary = mysqli_query($conn, "
  SELECT
    SUM(CASE WHEN metode = 'QRIS' THEN total ELSE 0 END) AS total_qris,
    SUM(CASE WHEN metode = 'Tunai' THEN total ELSE 0 END) AS total_tunai
  FROM transaksi
  WHERE kasir_id = '$kasir_id' AND DATE(waktu_transaksi) = '$tanggal'
");
$summary = mysqli_fetch_assoc($q_summary);

// Detail metode pembayaran
$q2 = mysqli_query($conn, "
  SELECT metode, COUNT(*) as jumlah, SUM(total) as total
  FROM transaksi
  WHERE kasir_id = '$kasir_id' AND DATE(waktu_transaksi) = '$tanggal'
  GROUP BY metode
");

// Semua menu dan jumlah terjual
$q_per_menu = mysqli_query($conn, "
  SELECT
    m.nama_menu,
    COALESCE(SUM(CASE WHEN DATE(t.waktu_transaksi) = '$tanggal' THEN dt.jumlah ELSE 0 END), 0) AS total_terjual,
    COALESCE(SUM(CASE WHEN DATE(t.waktu_transaksi) = '$tanggal' THEN dt.subtotal ELSE 0 END), 0) AS total_penjualan
  FROM menu m
  LEFT JOIN detail_transaksi dt ON m.id = dt.menu_id
  LEFT JOIN transaksi t ON t.id = dt.transaksi_id AND t.kasir_id = '$kasir_id'
  GROUP BY m.id
  ORDER BY total_penjualan DESC
");

// Total menu terjual
$q_total_menu = mysqli_query($conn, "
  SELECT SUM(dt.jumlah) AS total_menu_terjual
  FROM transaksi t
  JOIN detail_transaksi dt ON dt.transaksi_id = t.id
  WHERE t.kasir_id = '$kasir_id' AND DATE(t.waktu_transaksi) = '$tanggal'
");
$data_menu = mysqli_fetch_assoc($q_total_menu);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Closing Penjualan</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/kasir_unified.css"> <!-- Ganti ke unified CSS -->
</head>
<body>
<?php include 'layout/sidebar.php'; ?>
<div class="main-container">
  <div class="content">
    <h3 class="mb-4">📊 Closing Penjualan - <?= htmlspecialchars($_SESSION['username']) ?> (<?= $tanggal ?>)</h3>

    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-3">
        <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal) ?>">
      </div>
      <div class="col-md-2">
        <button class="btn btn-success w-100">Tampilkan</button>
      </div>
    </form>

    <!-- Ringkasan -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5 class="card-title">Ringkasan</h5>
        <p>Total Penjualan: <strong>Rp <?= number_format($data1['total_penjualan'] ?? 0, 0, ',', '.') ?></strong></p>
        <p>Jumlah Transaksi: <?= $data1['jumlah_transaksi'] ?? 0 ?></p>
        <p>Total QRIS: Rp <?= number_format($summary['total_qris'] ?? 0, 0, ',', '.') ?></p>
        <p>Total Tunai: Rp <?= number_format($summary['total_tunai'] ?? 0, 0, ',', '.') ?></p>
        <p>Jumlah Menu Terjual: <strong><?= $data_menu['total_menu_terjual'] ?? 0 ?></strong></p>
      </div>
    </div>

    <!-- Metode Pembayaran -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5 class="card-title">Detail Metode Pembayaran</h5>
        <ul class="list-group">
          <?php while ($row = mysqli_fetch_assoc($q2)) { ?>
            <li class="list-group-item d-flex justify-content-between">
              <?= htmlspecialchars($row['metode']) ?>
              <span><?= $row['jumlah'] ?>x - Rp <?= number_format($row['total'], 0, ',', '.') ?></span>
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>

    <!-- Daftar Menu -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5 class="card-title">Daftar Menu dan Jumlah Terjual</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <thead class="table-light">
              <tr>
                <th>Nama Menu</th>
                <th>Jumlah Terjual</th>
                <th>Total Penjualan</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($q_per_menu)) { ?>
                <tr>
                  <td><?= htmlspecialchars($row['nama_menu']) ?></td>
                  <td><?= $row['total_terjual'] ?>x</td>
                  <td>Rp <?= number_format($row['total_penjualan'], 0, ',', '.') ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="text-end">
      <a href="kasir.php" class="btn btn-outline-dark">⬅️ Kembali ke Kasir</a>
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
</body>
</html>
