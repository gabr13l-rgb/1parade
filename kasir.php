<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'pegawai') {
    header("Location: login.php");
    exit;
}
include 'database/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kasir 1parade Coffee</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/kasir_unified.css"> <!-- Ganti ke unified CSS -->
</head>
<body>

<button class="btn-toggle-sidebar" onclick="toggleSidebar()">☰ Menu</button>
<?php include 'layout/sidebar.php'; ?>

<div class="main-container">
  <div class="content">
    <div style="margin-bottom: 20px;">
      <input type="text" id="searchMenu" placeholder="Cari menu...">
    </div>

    <h2>Order Coffee</h2>
    <div class="grid">
      <?php
      $q = mysqli_query($conn, "SELECT * FROM menu");
      while ($m = mysqli_fetch_assoc($q)) {
          echo "
  <div class='menu-card'>
    " . (!empty($m['gambar']) ?
      "<img src='uploads/{$m['gambar']}' alt='{$m['nama_menu']}'>" :
      "<img src='https://via.placeholder.com/150x100?text=" . urlencode($m['nama_menu']) . "' alt='{$m['nama_menu']}'>") . "
    <h4>{$m['nama_menu']}</h4>
    <p>Rp " . number_format($m['harga'], 0, ',', '.') . "</p>
    <button onclick=\"tambahPesanan({$m['id']}, '{$m['nama_menu']}', {$m['harga']})\">Tambah</button>
  </div>";
      }
      ?>
    </div>
  </div>

  <div class="order-sidebar">
    <h3 class="section-title">🦾 Pesanan</h3>
    <div id="orderList" class="order-list"></div>

    <div class="total-section">
      <p>Total</p>
      <h4 class="total-amount">Rp <span id="total">0</span></h4>
    </div>

    <div class="input-group">
      <label for="uang">Uang Pembayaran</label>
      <input type="number" id="uang" placeholder="Masukkan nominal..." oninput="hitungKembalian()">
    </div>

    <div id="kembalian" class="kembalian-display"></div>

    <div class="input-group">
      <label for="metode_pembayaran">Metode Pembayaran</label>
      <select id="metode_pembayaran">
        <option value="Tunai">Tunai</option>
        <option value="QRIS">QRIS</option>
      </select>
    </div>

    <button class="bayar-btn" onclick="bayar()">💸 Bayar Sekarang</button>
  </div>
</div>

<script>
  function toggleSidebar() {
    document.querySelector(".sidebar").classList.toggle("active");
  }
</script>
<script src="assets/js/kasir.js"></script>
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
