<?php
// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "kos_maddog");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
// Proses pengiriman rating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $kos_id = intval($_POST['kos_id']);
    $rating = intval($_POST['rating']);
    $review = isset($_POST['review']) ? trim($_POST['review']) : '';

    // Validasi rating
    if ($rating >= 1 && $rating <= 5) {
        // Simpan rating dan review ke tabel rating
        $stmt = $koneksi->prepare("INSERT INTO rating (kos_id, rating, review) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $kos_id, $rating, $review);

        if ($stmt->execute()) {
            // Redirect ke halaman yang sama dengan pesan sukses
            header('Location: index.php?success=1');
            exit;
        } else {
            // Redirect ke halaman yang sama dengan pesan error
            header('Location: index.php?error=1');
            exit;
        }

    } else {
        echo "<script>alert('Rating harus antara 1 hingga 5.');</script>";
    }
}


// Inisialisasi variabel pencarian
$kota = isset($_GET['lokasi']) ? $koneksi->real_escape_string($_GET['lokasi']) : '';
$tipe = isset($_GET['tipe']) ? $koneksi->real_escape_string($_GET['tipe']) : '';
$tipe_hunian = isset($_GET['tipe_hunian']) ? $koneksi->real_escape_string($_GET['tipe_hunian']) : '';

// Query pencarian dinamis
// Modify the SQL query to include average rating calculation
$sql = "SELECT 
            kost.*, 
            COALESCE(rating_summary.avg_rating, 0) AS rating, 
            rating_summary.review_count
        FROM kost 
        LEFT JOIN (
            SELECT 
                kos_id, 
                AVG(rating) AS avg_rating, 
                COUNT(*) AS review_count
            FROM rating 
            GROUP BY kos_id
        ) rating_summary ON kost.id = rating_summary.kos_id 
        WHERE 1=1";


// Filter kota
if (!empty($kota)) {
    $map_kota = [
        'jakarta_pusat' => 'Jakarta',
        'jakarta_barat' => 'Bandung',
        'jakarta_timur' => 'Salatiga',
        'jakarta_selatan' => 'Surabaya',
        'jakarta_utara' => 'Semarang'
    ];
    $lokasi_kota = $map_kota[$kota] ?? '';
    if ($lokasi_kota) {
        $sql .= " AND kota = '$lokasi_kota'";
    }
}

// Filter tipe
if (!empty($tipe) && $tipe != 'semua') {
    $sql .= " AND tipe = '$tipe'";
}

// Filter tipe hunian
if (!empty($tipe_hunian) && $tipe_hunian != 'semua_tipe') {
    switch ($tipe_hunian) {
        case 'kost_pusat':
            $sql .= " AND lokasi LIKE '%pusat kota%'";
            break;
        case 'kost_pinggiran':
            $sql .= " AND lokasi LIKE '%pinggiran kota%'";
            break;
        case 'kost_eksklusif':
            $sql .= " AND lokasi LIKE '%mewah%'";
            break;
    }
}

// Eksekusi query
$result = $koneksi->query($sql);

// Inisialisasi grup data berdasarkan lokasi
$kelompokKos = [
    'Nyaman Pusat Kota' => [],
    'Nyaman Pinggiran Kota' => [],
    'Mewah Pusat Kota' => [],
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Kelompokkan berdasarkan lokasi
        if (strpos(strtolower($row['lokasi']), 'pusat kota') !== false && strpos(strtolower($row['lokasi']), 'mewah') !== false) {
            $kelompokKos['Mewah Pusat Kota'][] = $row;
        } elseif (strpos(strtolower($row['lokasi']), 'pusat kota') !== false) {
            $kelompokKos['Nyaman Pusat Kota'][] = $row;
        } elseif (strpos(strtolower($row['lokasi']), 'pinggiran kota') !== false) {
            $kelompokKos['Nyaman Pinggiran Kota'][] = $row;
        }
    }
}
// Function to generate star rating display
function generateStarRating($rating)
{
    // If no rating, return empty stars
    if ($rating == 0) {
        return str_repeat('<i class="ri-star-line text-warning"></i>', 5);
    }

    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
    $empty_stars = 5 - $full_stars - $half_star;

    $star_html = '';

    // Full stars
    for ($i = 0; $i < $full_stars; $i++) {
        $star_html .= '<i class="ri-star-fill text-warning"></i>';
    }

    // Half star
    if ($half_star) {
        $star_html .= '<i class="ri-star-half-line text-warning"></i>';
    }

    // Empty stars
    for ($i = 0; $i < $empty_stars; $i++) {
        $star_html .= '<i class="ri-star-line text-warning"></i>';
    }

    return $star_html;
}


// Tutup koneksi
$koneksi->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kos Maddog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
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

    <div class="container search-section">
        <div class="row justify-content-center mb-4">
            <div class="col-12">
                <form method="GET" action="" class="search-form">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="lokasi" class="form-label">Lokasi</label>
                            <select id="lokasi" name="lokasi" class="form-select">
                                <option value="" selected disabled>Pilih Lokasi</option>
                                <option value="jakarta_pusat">Jakarta</option>
                                <option value="jakarta_barat">Bandung</option>
                                <option value="jakarta_timur">Salatiga</option>
                                <option value="jakarta_selatan">Surabaya</option>
                                <option value="jakarta_utara">Semarang</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tipe" class="form-label">Tipe Kos</label>
                            <select id="tipe" name="tipe" class="form-select">
                                <option value="" selected disabled>Pilih Tipe Kos</option>
                                <option value="semua">Semua Tipe</option>
                                <option value="CAMPUR">Kos Campuran</option>
                                <option value="PRIA">Kos Pria</option>
                                <option value="WANITA">Kos Wanita</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tipe_hunian" class="form-label">Tipe Hunian</label>
                            <select id="tipe_hunian" name="tipe_hunian" class="form-select">
                                <option value="" selected disabled>Pilih Tipe Hunian</option>
                                <option value="semua_tipe">Semua Tipe</option>
                                <option value="kost_pusat">Kost Area Pusat Kota</option>
                                <option value="kost_pinggiran">Kost Area Pinggiran Kota</option>
                                <option value="kost_eksklusif">Kost Area Eksklusif</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Cari Hunian</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <div class="card card-custom bg-gradient-blue text-white overflow-hidden">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-7 p-4">
                            <h2 class="card-title mb-3" style="color: var(--white);">Pemilik Kos, Daftarkan Kosmu
                                Sekarang!</h2>
                            <p class="card-text mb-4">Bergabunglah dengan Kos Maddog dan dapatkan kesempatan untuk
                                menjangkau lebih banyak calon penyewa. Proses pendaftaran mudah, cepat, dan gratis!</p>
                            <div class="d-flex align-items-center">
                                <a href="daftarkos.php" class="btn btn-light text-primary me-3">
                                    <i class="ri-add-line me-2"></i>Daftar Kos
                                </a>
                                <a href="#" class="text-white text-decoration-none" data-bs-toggle="modal"
                                    data-bs-target="#infoModal">
                                    <i class="ri-information-line me-2"></i>Pelajari Lebih Lanjut
                                </a>
                            </div>
                        </div>
                        <div class="col-md-5 text-end d-none d-md-block">
                            <img src="/api/placeholder/400/250" alt="Daftar Kos" class="img-fluid"
                                style="max-height: 250px; opacity: 0.8;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php foreach ($kelompokKos as $kategori => $dataKos): ?>
            <?php if (count($dataKos) > 0): ?>
                <h3 class="kos-section-title"><?= $kategori; ?></h3>
                <div class="row">
                    <?php foreach ($dataKos as $kos): ?>
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card card-custom">
                                <img src="<?= $kos['gambar']; ?>" class="card-img-top" alt="<?= $kos['nama']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title text-primary"><?= $kos['nama']; ?></h5>
                                    <div class="rating mb-2">
                                        <?php
                                        $rating = isset($kos['rating']) ? $kos['rating'] : 0;
                                        echo generateStarRating($rating);
                                        ?>
                                        <span class="ms-2 text-muted small">
                                            (<?= number_format($rating, 1) ?>
                                            <?= isset($kos['review_count']) ? '- ' . $kos['review_count'] . ' ulasan' : '' ?>)
                                        </span>
                                    </div>
                                    <p class="text-muted mb-2">
                                        <i class="ri-map-pin-line me-2"></i><?= $kos['lokasi']; ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="price-badge">Rp<?= number_format($kos['harga']); ?>/bulan</span>
                                    </div>
                                    <p class="card-text text-muted"><?= $kos['fasilitas']; ?></p>
                                    <a href="detail.php?id=<?= $kos['id']; ?>" class="cta-btn d-block text-center">
                                        <i class="ri-eye-line me-2"></i>Lihat Detail
                                    </a>
                                    <div class="mt-2">
                                        <a href="#" class="rating-link" data-bs-toggle="modal"
                                            data-bs-target="#ratingModal-<?= $kos['id']; ?>">
                                            <i class="ri-star-line me-2"></i>Beri Rating
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal unik untuk setiap kos -->
                        <div class="modal fade" id="ratingModal-<?= $kos['id']; ?>" tabindex="-1"
                            aria-labelledby="ratingModalLabel-<?= $kos['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="ratingModalLabel-<?= $kos['id']; ?>">Berikan Penilaian untuk
                                            <?= $kos['nama']; ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="index.php">
                                            <input type="hidden" name="kos_id" value="<?= $kos['id']; ?>">
                                            <div class="rating-input text-center mb-3">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <input type="radio" name="rating" value="<?= $i; ?>"
                                                        id="star<?= $i; ?>-<?= $kos['id']; ?>" class="d-none">
                                                    <label for="star<?= $i; ?>-<?= $kos['id']; ?>" class="star-label fs-2">
                                                        <i class="ri-star-line text-warning"></i>
                                                    </label>
                                                <?php endfor; ?>
                                            </div>
                                            <textarea name="review" class="form-control" rows="4"
                                                placeholder="Tulis ulasan Anda (opsional)"></textarea>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="submit_rating" class="btn btn-primary">Kirim
                                                    Penilaian</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Modal Success -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Penilaian Berhasil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="ri-check-line text-success fs-1 mb-3"></i>
                    <p>Terima kasih atas penilaian Anda!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
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
                        <li class="mb-2"><a href="kritiksaran.php" class="text-white text-decoration-none">Kritik &
                                Saran</a></li>
                        <li class="mb-2"><a href="syarat.php" class="text-white text-decoration-none">Syarat & Ketentuan</a></li>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                // Hapus parameter 'success' dari URL setelah modal ditampilkan
                const url = new URL(window.location.href);
                urlParams.delete('success');
                window.history.replaceState({}, document.title, url.pathname + '?' + urlParams.toString());
            }
        });


        document.addEventListener('DOMContentLoaded', function () {
            // Fungsi untuk memperbarui tampilan bintang berdasarkan nilai
            function updateStars(ratingInput, value) {
                const stars = ratingInput.querySelectorAll('.star-label i');
                stars.forEach((star, index) => {
                    if (index < value) {
                        star.classList.remove('ri-star-line');
                        star.classList.add('ri-star-fill');
                    } else {
                        star.classList.remove('ri-star-fill');
                        star.classList.add('ri-star-line');
                    }
                });
            }

            document.addEventListener('mouseover', function (e) {
                const label = e.target.closest('.star-label');
                if (!label) return;

                const ratingInput = label.closest('.rating-input');
                const stars = ratingInput.querySelectorAll('.star-label i');
                const index = Array.from(stars).indexOf(label.querySelector('i'));

                stars.forEach((star, i) => {
                    star.classList.toggle('ri-star-fill', i <= index);
                    star.classList.toggle('ri-star-line', i > index);
                });
            });

            document.addEventListener('mouseout', function (e) {
                const label = e.target.closest('.star-label');
                if (!label) return;

                const ratingInput = label.closest('.rating-input');
                const stars = ratingInput.querySelectorAll('.star-label i');
                const selectedRating = ratingInput.querySelector('input:checked')?.value || 0;

                stars.forEach((star, i) => {
                    star.classList.toggle('ri-star-fill', i < selectedRating);
                    star.classList.toggle('ri-star-line', i >= selectedRating);
                });
            });

            // Event delegation untuk klik pada bintang
            document.addEventListener('click', function (e) {
                const label = e.target.closest('.star-label');
                if (label) {
                    const radioInput = label.previousElementSibling;
                    if (radioInput) {
                        radioInput.checked = true;
                        const ratingInput = label.closest('.rating-input');
                        updateStars(ratingInput, parseInt(radioInput.value, 10));
                    }
                }
            });
        });

        function submitRating(kosId) {
            const form = document.getElementById(`ratingForm-${kosId}`);
            const selectedRating = form.querySelector('input[name="rating"]:checked');
            const reviewText = form.querySelector('textarea[name="review"]').value;

            if (!selectedRating) {
                alert('Mohon pilih rating terlebih dahulu');
                return;
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById(`ratingModal-${kosId}`));
            modal.hide();

            alert('Terima kasih atas penilaian Anda!');
        }
    </script>
</body>

</html>