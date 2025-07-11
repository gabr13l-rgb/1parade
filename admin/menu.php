<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../database/koneksi.php';

$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// Handle tambah menu
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_menu'];
    $harga = $_POST['harga'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $type = $_FILES['gambar']['type'];
    $size = $_FILES['gambar']['size'];

    $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    $gambar_final = "default.png"; // Default jika tidak upload gambar

    if (!empty($gambar)) {
        if (!in_array($type, $allowed)) {
            echo "<script>alert('Format gambar tidak didukung.'); window.history.back();</script>";
            exit;
        }
        if ($size > 2 * 1024 * 1024) {
            echo "<script>alert('Ukuran gambar maksimal 2MB'); window.history.back();</script>";
            exit;
        }

        $gambar_final = uniqid() . "-" . basename($gambar);
        move_uploaded_file($tmp, $upload_dir . $gambar_final);
    }

    mysqli_query($conn, "INSERT INTO menu (nama_menu, harga, gambar) VALUES ('$nama', '$harga', '$gambar_final')") or die(mysqli_error($conn));
    header("Location: menu.php");
    exit;
}

// Handle hapus menu
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $cek = mysqli_query($conn, "SELECT * FROM detail_transaksi WHERE menu_id='$id'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Menu tidak bisa dihapus karena sudah digunakan di transaksi!');window.location='menu.php';</script>";
        exit;
    }
    mysqli_query($conn, "DELETE FROM menu WHERE id='$id'") or die(mysqli_error($conn));
    header("Location: menu.php");
    exit;
}

// Handle edit menu
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama_menu'];
    $harga = $_POST['harga'];

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $type = $_FILES['gambar']['type'];
        $size = $_FILES['gambar']['size'];

        if (!in_array($type, $allowed)) {
            echo "<script>alert('Format gambar tidak didukung.'); window.history.back();</script>";
            exit;
        }
        if ($size > 2 * 1024 * 1024) {
            echo "<script>alert('Ukuran gambar maksimal 2MB'); window.history.back();</script>";
            exit;
        }

        $gambar_final = uniqid() . "-" . basename($gambar);
        move_uploaded_file($tmp, $upload_dir . $gambar_final);
        mysqli_query($conn, "UPDATE menu SET nama_menu='$nama', harga='$harga', gambar='$gambar_final' WHERE id='$id'") or die(mysqli_error($conn));
    } else {
        mysqli_query($conn, "UPDATE menu SET nama_menu='$nama', harga='$harga' WHERE id='$id'") or die(mysqli_error($conn));
    }
    header("Location: menu.php");
    exit;
}

$menu = mysqli_query($conn, "SELECT * FROM menu ORDER BY id DESC");
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM menu WHERE id='$id'"));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Menu</title>
      <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/closing.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">🧾 Manajemen Menu</h2>

    <form method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
        <input type="hidden" name="id" value="<?= $edit ? $edit['id'] : '' ?>">
        <div class="col-md-4">
            <input type="text" name="nama_menu" class="form-control" placeholder="Nama menu" required value="<?= $edit ? $edit['nama_menu'] : '' ?>">
        </div>
        <div class="col-md-3">
            <input type="number" name="harga" class="form-control" placeholder="Harga" required value="<?= $edit ? $edit['harga'] : '' ?>">
        </div>
        <div class="col-md-3">
            <input type="file" name="gambar" class="form-control" accept="image/*">
            <?php if ($edit && $edit['gambar']) { ?>
                <img src="../uploads/<?= $edit['gambar'] ?>" width="60" class="mt-2">
            <?php } ?>
        </div>
        <div class="col-md-2">
            <button class="btn btn-<?= $edit ? 'warning' : 'dark' ?> w-100" name="<?= $edit ? 'edit' : 'tambah' ?>">
                <?= $edit ? 'Update' : 'Tambah' ?>
            </button>
        </div>
    </form>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Gambar</th>
                <th>Nama Menu</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($menu)) { ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <?php if (!empty($row['gambar'])) { ?>
                        <img src="../uploads/<?= $row['gambar'] ?>" width="60">
                    <?php } else { echo '-'; } ?>
                </td>
                <td><?= htmlspecialchars($row['nama_menu']) ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                <td>
                    <a href="./menu.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="./menu.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin mau hapus?')" class="btn btn-sm btn-danger">Hapus</a>
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
