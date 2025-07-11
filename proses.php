<?php
session_start();
include 'database/koneksi.php';

header('Content-Type: application/json');

// Pastikan user pegawai login dengan user_id
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pegawai') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}


// Ambil data JSON dari frontend
$data = json_decode(file_get_contents("php://input"), true);

// Validasi input
if (
    !$data ||
    !isset($data['total'], $data['bayar'], $data['kembalian'], $data['metode'], $data['items']) ||
    !is_array($data['items']) || count($data['items']) === 0
) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap atau tidak valid']);
    exit;
}

$kasir_id  = $_SESSION['user_id'];
$total     = (int) $data['total'];
$bayar     = (int) $data['bayar'];
$kembalian = (int) $data['kembalian'];
$metode    = $data['metode'];
$items     = $data['items'];

// Simpan transaksi utama
$stmt = $conn->prepare("
    INSERT INTO transaksi (kasir_id, total, bayar, kembalian, metode, waktu_transaksi)
    VALUES (?, ?, ?, ?, ?, NOW())
");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare gagal: transaksi']);
    exit;
}

$stmt->bind_param("iiiis", $kasir_id, $total, $bayar, $kembalian, $metode);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan transaksi']);
    exit;
}

$transaksi_id = $stmt->insert_id;

// Simpan detail transaksi
$detail = $conn->prepare("
    INSERT INTO detail_transaksi (transaksi_id, menu_id, jumlah, subtotal)
    VALUES (?, ?, ?, ?)
");
if (!$detail) {
    echo json_encode([
        'success' => false,
        'message' => 'Prepare gagal: detail - ' . $conn->error  // tampilkan error MySQL
    ]);
    exit;
}


foreach ($items as $item) {
    if (!isset($item['menu_id'], $item['jumlah'], $item['subtotal'])) continue;

    $menu_id  = (int) $item['menu_id'];
    $jumlah   = (int) $item['jumlah'];
    $subtotal = (int) $item['subtotal'];

    $detail->bind_param("iiii", $transaksi_id, $menu_id, $jumlah, $subtotal);
    if (!$detail->execute()) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan detail transaksi']);
        exit;
    }
}

// Sukses
echo json_encode([
    'success' => true,
    'message' => 'Transaksi berhasil disimpan',
    'id_transaksi' => $transaksi_id
]);
?>
