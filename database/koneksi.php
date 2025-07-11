<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "coxlqmnp_dbs_1parade";

// Ganti variabel $koneksi menjadi $conn di sini saja
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
