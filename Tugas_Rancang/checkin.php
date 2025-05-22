<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Ambil detail kos berdasarkan ID
$query = "SELECT * FROM kost WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$kos = mysqli_fetch_assoc($result);

if (!$kos) {
    echo "Kos tidak ditemukan.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pemesan = $_POST['nama_pemesan'];
    $no_telepon = $_POST['no_telepon'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    $metode_bayar = $_POST['metode_bayar'];
    $e_wallet = $_POST['e_wallet'] ?? '';

    // Jika E-Wallet dipilih, gabungkan dengan nama e-wallet
    if ($metode_bayar === 'E-Wallet') {
        $metode_bayar = "E-Wallet - $e_wallet";
    }

    $total_harga = $kos['harga'];

    $queryInsert = "INSERT INTO transaksi (id_kost, nama_pemesan, no_telepon, email, alamat, total_harga, metode_bayar, tanggal_pesan) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmtInsert = mysqli_prepare($conn, $queryInsert);
    mysqli_stmt_bind_param($stmtInsert, 'issssis', $id, $nama_pemesan, $no_telepon, $email, $alamat, $total_harga, $metode_bayar);
    $success = mysqli_stmt_execute($stmtInsert);

    if ($success) {
        $last_id = mysqli_insert_id($conn); // Ambil ID terakhir yang dimasukkan
        echo "<script>
                alert('Pemesanan berhasil!');
                window.location.href = 'nota.php?id=$last_id';
              </script>";
    }
}

// Deklarasi virtual account dummy dan pilihan e-Wallet
$virtualAccounts = [
    "Bank BCA" => "12345678901234",
    "Bank Mandiri" => "98765432101234",
    "Bank BNI" => "11223344556677"
];

$eWallets = ["GoPay", "OVO", "ShopeePay"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In Kos - <?= htmlspecialchars($kos['nama'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/checkin.css">
</head>

<body>
    <div class="form-container">
        <div class="form-header">
            <h1><i class="ri-home-heart-line"></i> Pemesanan Kos</h1>
            <p class="subtitle"><?= htmlspecialchars($kos['nama'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nama_pemesan" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_pemesan" name="nama_pemesan" required>
                </div>
                <div class="col-md-6">
                    <label for="no_telepon" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" id="no_telepon" name="no_telepon" required>
                </div>
                <div class="col-12">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                </div>
                <div class="col-12">
                    <label for="metode_bayar" class="form-label">Metode Pembayaran</label>
                    <select class="form-select" id="metode_bayar" name="metode_bayar" required>
                        <option value="" selected disabled>Pilih Metode Pembayaran</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="Cash">Cash</option>
                        <option value="E-Wallet">E-Wallet</option>
                    </select>
                </div>
            </div>

            <div id="bank-options" class="mt-4">
                <h5 class="mb-3">Virtual Account Bank</h5>
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    <?php foreach ($virtualAccounts as $bank => $va): ?>
                        <div class="col">
                            <div class="bg-white p-3 rounded shadow-sm">
                                <strong><?= htmlspecialchars($bank); ?>:</strong>
                                <?= htmlspecialchars($va); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="ewallet-options" class="mt-4">
                <h5 class="mb-3">Pilih E-Wallet</h5>
                <select name="e_wallet" id="e_wallet" class="form-select">
                    <?php foreach ($eWallets as $ewallet): ?>
                        <option value="<?= htmlspecialchars($ewallet); ?>"><?= htmlspecialchars($ewallet); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mt-4 text-center">
                <h5>Total Pembayaran: <span class="total-payment">Rp<?= number_format($kos['harga']); ?></span></h5>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn-submit mx-auto">
                    <i class="ri-check-double-line"></i> Konfirmasi Pembayaran
                </button>
            </div>
        </form>

        <a href="index.php" class="btn-back mt-3">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar Kos
        </a>
    </div>

    <script>
        document.getElementById('metode_bayar').addEventListener('change', function () {
            const bankOptions = document.getElementById('bank-options');
            const ewalletOptions = document.getElementById('ewallet-options');
            const selected = this.value;

            bankOptions.style.display = selected === 'Transfer Bank' ? 'block' : 'none';
            ewalletOptions.style.display = selected === 'E-Wallet' ? 'block' : 'none';
        });
    </script>
</body>

</html>
