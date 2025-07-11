<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'pegawai') {
    header("Location: login.php");
    exit;
}

include 'database/koneksi.php';

$id = $_GET['id'];
$q = mysqli_query($conn, "SELECT * FROM transaksi WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

// Ambil detail item
$q_detail = mysqli_query($conn, "
    SELECT dt.*, m.nama_menu 
    FROM detail_transaksi dt 
    JOIN menu m ON m.id = dt.menu_id 
    WHERE dt.transaksi_id = '$id'
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Struk Pembayaran</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media print {
      .no-print { display: none; }
    }
    .struk-box {
      max-width: 400px;
      margin: auto;
      padding: 20px;
      background: white;
      border: 1px solid #ccc;
    }
  </style>
</head>
<body class="bg-light">

<div class="struk-box mt-5">
  <h4 class="text-center">1Parade Store</h4>
  <p class="text-center mb-2"><?= date('d-m-Y H:i', strtotime($data['waktu_transaksi'])) ?></p>
  <hr>
  <table class="w-100 mb-3">
    <?php while ($item = mysqli_fetch_assoc($q_detail)) { ?>
    <tr>
      <td><?= $item['nama_menu'] ?> x<?= $item['jumlah'] ?></td>
      <td class="text-end">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
    </tr>
    <?php } ?>
  </table>
  <hr>
  <table class="w-100">
    <tr>
      <td><strong>Total</strong></td>
      <td class="text-end">Rp <?= number_format($data['total'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <td>Bayar</td>
      <td class="text-end">Rp <?= number_format($data['bayar'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <td>Kembali</td>
      <td class="text-end">Rp <?= number_format($data['kembalian'], 0, ',', '.') ?></td>
    </tr>
  </table>

  <div class="text-center mt-4 no-print">
    <button onclick="window.print()" class="btn btn-dark w-100 mb-2">🖨️ Cetak Struk</button>
    <a href="kasir.php" class="btn btn-outline-secondary w-100">⬅️ Kembali ke Kasir</a>
  </div>
</div>

</body>
</html>
