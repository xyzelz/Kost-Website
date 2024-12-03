<?php
include 'koneksi.php';

// Ambil ID kos dari parameter GET
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Query untuk mengambil detail kos
$query = "SELECT * FROM kost WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $kos = mysqli_fetch_assoc($result);

    if (!$kos) {
        echo "Detail kos tidak ditemukan.";
        exit;
    }
} else {
    die("Query gagal: " . mysqli_error($conn));
}
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    unset($_GET['status']); // Hapus query string setelah ditangani
}

// Query untuk mengambil data rating
$queryRating = "SELECT AVG(rating) as avg_rating, COUNT(rating) as total_reviews FROM rating WHERE kos_id = ?";
$stmtRating = mysqli_prepare($conn, $queryRating);
if ($stmtRating) {
    mysqli_stmt_bind_param($stmtRating, 'i', $id);
    mysqli_stmt_execute($stmtRating);
    $resultRating = mysqli_stmt_get_result($stmtRating);

    $ratingData = mysqli_fetch_assoc($resultRating);
    $avgRating = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : 0;
    $totalReviews = $ratingData['total_reviews'];
} else {
    die("Query rating gagal: " . mysqli_error($conn));
}

// Query untuk mengambil ulasan
$queryReviews = "SELECT rating, review, created_at FROM rating WHERE kos_id = ? ORDER BY created_at DESC";
$stmtReviews = mysqli_prepare($conn, $queryReviews);
if ($stmtReviews) {
    mysqli_stmt_bind_param($stmtReviews, 'i', $id);
    mysqli_stmt_execute($stmtReviews);
    $resultReviews = mysqli_stmt_get_result($stmtReviews);
} else {
    die("Query ulasan gagal: " . mysqli_error($conn));
}

// Deklarasi dummy untuk fasilitas (karena tidak ada di database awal)
$fasilitas = [
    "WiFi Gratis",
    "AC",
    "Parkir Motor",
    "CCTV 24 Jam"
];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kos - <?= htmlspecialchars($kos['nama'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/detail.css">
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
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="page-wrapper">
        <div class="kos-detail-card">
            <div class="kos-image">
                <img src="<?= htmlspecialchars($kos['gambar'], ENT_QUOTES, 'UTF-8'); ?>"
                    alt="<?= htmlspecialchars($kos['nama'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="kos-info">
                <h1 class="kos-title"><?= htmlspecialchars($kos['nama'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <span class="price-badge">Rp<?= number_format($kos['harga']); ?>/bulan</span>
                <a href="checkin.php?id=<?= $id ?>" class="btn btn-success">
                    <i class="ri-check-double-line"></i> Pesan
                </a>
                <div class="kos-details mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="ri-map-pin-line"></i> Lokasi:</strong>
                            <p><?= htmlspecialchars($kos['lokasi'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="ri-door-line"></i> Sisa Kamar:</strong>
                            <p><?= (int) $kos['sisa_kamar']; ?> kamar</p>
                        </div>
                        <div class="col-12">
                            <strong><i class="ri-home-line"></i> Tipe Kos:</strong>
                            <p><?= htmlspecialchars($kos['tipe'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <strong><i class="ri-file-text-line"></i> Deskripsi:</strong>
                        <p><?= nl2br(htmlspecialchars($kos['deskripsi'], ENT_QUOTES, 'UTF-8')); ?></p>
                    </div>
                </div>
                <a href="index.php" class="btn-back mt-4">
                    <i class="ri-arrow-left-line"></i> Kembali ke Daftar Kos
                </a>
            </div>
        </div>

        <div class="facilities-section">
            <h3><i class="ri-pulse-line facility-icon"></i> Fasilitas Kos</h3>
            <div class="row">
                <?php foreach ($fasilitas as $fasilitasItem): ?>
                    <div class="col-md-6">
                        <div class="facility-item">
                            <i class="ri-check-line facility-icon"></i>
                            <?= htmlspecialchars($fasilitasItem, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="reviews-section mt-4">
            <h3><i class="ri-chat-3-line"></i> Ulasan</h3>
            <?php if (mysqli_num_rows($resultReviews) > 0): ?>
                <?php while ($review = mysqli_fetch_assoc($resultReviews)): ?>
                    <div class="review-item mb-3">
                        <div class="review-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="<?= $i <= $review['rating'] ? 'ri-star-fill' : 'ri-star-line'; ?>"
                                    style="color: #f4c150;"></i>
                            <?php endfor; ?>
                        </div>
                        <p><?= htmlspecialchars($review['review'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <small class="text-muted">
                            <?= date('d M Y', strtotime($review['created_at'])); ?>
                        </small>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">Belum ada ulasan.</p>
            <?php endif; ?>
        </div>

    </div>
    <!-- Footer Section -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3" style="color: var(--secondary-blue);">Kos Maddog</h5>
                    <p class="text-muted">Platform pencarian kos terbaik yang membantu mahasiswa dan pekerja menemukan
                        hunian nyaman dan terjangkau di berbagai kota.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="mb-3" style="color: var(--accent-blue);">Tautan Cepat</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-white text-decoration-none">Beranda</a></li>
                        <li class="mb-2"><a href="login.php" class="text-white text-decoration-none">Login</a></li>
                        <li class="mb-2"><a href="kritiksaran.php" class="text-white text-decoration-none">Kritik & Saran</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Syarat & Ketentuan</a></li>
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