<?php
// Koneksi ke database
include 'koneksi.php';

// Memproses form jika ada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $harga = $_POST['harga'];
    $sisa_kamar = $_POST['sisa_kamar'];
    $tipe = $_POST['tipe'];
    $deskripsi = $_POST['deskripsi'];
    $fasilitas = $_POST['fasilitas'];
    $gambar = $_FILES['gambar']['name'];
    $kota = $_POST['kota'];

    // Memindahkan gambar ke folder "uploads"
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "img/";
        $target_file = $target_dir . basename($gambar);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file);
    }

    $views = 0;

    $sql = "INSERT INTO daftarkos (nama, lokasi, kota, harga, sisa_kamar, tipe, deskripsi, gambar, views, fasilitas) 
            VALUES ('$nama', '$lokasi', '$kota', '$harga', '$sisa_kamar', '$tipe', '$deskripsi', '$gambar', '$views', '$fasilitas')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Properti Berhasil Ditambahkan!');window.location='index.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Properti Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/daftarkos.css">
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

    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1>Daftarkan Kost Anda</h1>
                <p>Lengkapi informasi kost dengan detail yang akurat</p>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Kost</label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Contoh: Kost Putra"
                        required>
                </div>
                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi</label>
                    <select class="form-control" id="lokasi" name="lokasi" required>
                        <option value="" selected disabled>Pilih Lokasi</option>
                        <option value="Lokasi Strategis di Pusat Kota">Lokasi Strategis di Pusat Kota</option>
                        <option value="Lokasi Nyaman di Pinggiran Kota">Lokasi Nyaman di Pinggiran Kota</option>
                        <option value="Lokasi Mewah di Pusat Kota">Lokasi Mewah di Pusat Kota</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="kota" class="form-label">Kota</label>
                    <select class="form-control" id="kota" name="kota" required>
                        <option value="" selected disabled>Pilih Kota</option>
                        <option value="jakarta">Jakarta</option>
                        <option value="bandung">Bandung</option>
                        <option value="salatiga">Salatiga</option>
                        <option value="surabaya">Surabaya</option>
                        <option value="semarang">Semarang</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga"
                            placeholder="Masukkan harga kost per bulan" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sisa_kamar" class="form-label">Sisa Kamar</label>
                        <input type="number" class="form-control" id="sisa_kamar" name="sisa_kamar"
                            placeholder="Jumlah kamar tersedia" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="tipe" class="form-label">Tipe Kost</label>
                    <select class="form-control" id="tipe" name="tipe" required>
                        <option value="" selected disabled>Pilih Tipe</option>
                        <option value="campur">Campur</option>
                        <option value="pria">Pria</option>
                        <option value="wanita">Wanita</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Kost</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi"
                        placeholder="Tuliskan deskripsi lengkap kost" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="fasilitas" class="form-label">Fasilitas</label>
                    <textarea class="form-control" id="fasilitas" name="fasilitas"
                        placeholder="Contoh: AC, WiFi, Dapur Bersama" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="gambar" class="form-label">Upload Gambar</label>
                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" required>
                </div>
                <button type="submit" class="btn-submit"><i class="ri-save-line me-2"></i>Simpan Kost</button>
            </form>
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

</body>

</html>
