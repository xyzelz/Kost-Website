<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Ambil data transaksi berdasarkan ID
$query = "SELECT t.*, k.nama AS nama_kos, k.harga 
          FROM transaksi t
          JOIN kost k ON t.id_kost = k.id
          WHERE t.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$transaksi = mysqli_fetch_assoc($result);

if (!$transaksi) {
    echo "Transaksi tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/nota.css">
</head>
<body>
    <div class="nota-container">
        <div class="nota-header">
            <h1>Nota Pembayaran</h1>
            <p>Kost: <?= htmlspecialchars($transaksi['nama_kos'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <div class="nota-body">
            <div class="nota-detail">
                <div class="detail-item">
                    <span>Nama Pemesan</span>
                    <span><?= htmlspecialchars($transaksi['nama_pemesan'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="detail-item">
                    <span>Nomor Telepon</span>
                    <span><?= htmlspecialchars($transaksi['no_telepon'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="detail-item">
                    <span>Email</span>
                    <span><?= htmlspecialchars($transaksi['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="detail-item">
                    <span>Metode Pembayaran</span>
                    <span><?= htmlspecialchars($transaksi['metode_bayar'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>
            
            <div class="nota-detail">
                <div class="detail-item">
                    <span>Total Pembayaran</span>
                    <span>Rp<?= number_format($transaksi['total_harga'], 0, ',', '.'); ?></span>
                </div>
                <div class="detail-item">
                    <span>Tanggal Pesan</span>
                    <span><?= date('d-m-Y H:i:s', strtotime($transaksi['tanggal_pesan'])); ?></span>
                </div>
            </div>
        </div>

        <div class="nota-footer">
            <button class="btn-print" onclick="window.print();">Cetak Nota</button>
            <a href="index.php" class="btn-secondary">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>