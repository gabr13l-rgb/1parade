<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../database/koneksi.php';

// Tambah user
if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role     = $_POST['role'];

    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");
    header("Location: users.php");
    exit;
}

// Hapus user
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    header("Location: users.php");
    exit;
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen User</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/closing.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">👥 Manajemen User</h2>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="col-md-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select" required>
                <option value="pegawai">Pegawai (Kasir)</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-dark w-100" name="tambah">Tambah</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($users)) { ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['role'] ?></td>
                <td>
                    <a href="users.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus user ini?')" class="btn btn-sm btn-danger">Hapus</a>
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
