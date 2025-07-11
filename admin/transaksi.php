<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../database/koneksi.php';

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

$query = "SELECT t.*, u.username 
          FROM transaksi t 
          JOIN users u ON t.kasir_id = u.id 
          WHERE DATE(t.waktu_transaksi) = '$tanggal' 
          ORDER BY t.waktu_transaksi DESC";
         

// $result = mysqli_query($conn, $query);
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));

?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">📊 Riwayat Transaksi (<?= $tanggal ?>)</h2>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-dark w-100">Tampilkan</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Total</th>
                <th>Bayar</th>
                <th>Kembalian</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['waktu_transaksi'] ?></td>
                <td><?= $row['username'] ?></td>
                <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($row['bayar'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($row['kembalian'], 0, ',', '.') ?></td>
                <td>
                    <a href="detail_transaksi.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Lihat</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="text-end mt-4">
        <a href="dashboard.php" class="btn btn-outline-secondary">⬅️ Kembali ke Dashboard</a>
    </div>
</div>
</body>
</html>