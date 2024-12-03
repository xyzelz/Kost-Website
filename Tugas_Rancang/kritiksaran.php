<?php
// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "kos_maddog");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Proses form jika di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $koneksi->real_escape_string($_POST['nama']);
    $email = $koneksi->real_escape_string($_POST['email']);
    $kritik_saran = $koneksi->real_escape_string($_POST['kritik_saran']);

    $sql = "INSERT INTO kritik_saran (nama, email, kritik_saran, tanggal) VALUES ('$nama', '$email', '$kritik_saran', NOW())";
    
    $pesan = $koneksi->query($sql) ? "Terima kasih atas kritik dan saran Anda!" : "Gagal mengirim kritik dan saran.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kritik & Saran - Kos Maddog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/kritiksaran.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">Kos Maddog</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom">
                    <div class="card-body p-4">
                        <h3 class="kos-section-title mb-4">Kritik dan Saran</h3>
                        
                        <?php if (isset($pesan)): ?>
                            <div class="alert alert-<?= $koneksi->query($sql) ? 'success' : 'danger' ?> mb-4">
                                <?= $pesan ?>
                            </div>
                        <?php endif; ?>

                        <form id="kritikSaranForm" method="POST" action="">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="kritikSaran" class="form-label">Kritik dan Saran</label>
                                <textarea class="form-control" id="kritikSaran" name="kritik_saran" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-send-plane-line me-2"></i>Kirim Kritik dan Saran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3" style="color: var(--secondary-blue);">Kos Maddog</h5>
                    <p class="text-muted">Platform pencarian kos terbaik yang membantu mahasiswa dan pekerja menemukan hunian nyaman dan terjangkau di berbagai kota.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="mb-3" style="color: var(--accent-blue);">Tautan Cepat</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-white text-decoration-none">Beranda</a></li>
                        <li class="mb-2"><a href="login.php" class="text-white text-decoration-none">Login</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Syarat & Ketentuan</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="mb-3" style="color: var(--accent-blue);">Hubungi Kami</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="ri-phone-line me-2"></i>
                            +62 812-3456-7890
                        </li>
                        <li class="mb-2">
                            <i class="ri-mail-line me-2"></i>
                            support@kosmaddog.com
                        </li>
                        <li class="mb-2">
                            <i class="ri-map-pin-line me-2"></i>
                            Jakarta, Indonesia
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="text-center">
                <p class="mb-0 text-muted">
                    &copy; <?= date('Y') ?> Kos Maddog. All Rights Reserved.
                </p>
                <div class="social-icons mt-3">
                    <a href="#" class="text-white me-3"><i class="ri-facebook-circle-fill"></i></a>
                    <a href="#" class="text-white me-3"><i class="ri-instagram-line"></i></a>
                    <a href="#" class="text-white me-3"><i class="ri-twitter-x-line"></i></a>
                    <a href="#" class="text-white"><i class="ri-linkedin-box-fill"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Tutup koneksi
$koneksi->close();
?>