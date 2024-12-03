<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Koneksi ke database
include 'koneksi.php';

// Memproses form jika ada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Ambil ID pengguna dari session
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
        // Tambahkan prefix 'img/' ke nama file gambar
        $gambar = $target_dir . $gambar;
        $target_file = $target_dir . basename($gambar);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file);
    }

    $views = 0;

    $sql = "INSERT INTO kost (nama, lokasi, kota, harga, sisa_kamar, tipe, deskripsi, gambar, views, fasilitas, user_id) 
            VALUES ('$nama', '$lokasi', '$kota', '$harga', '$sisa_kamar', '$tipe', '$deskripsi', '$gambar', '$views', '$fasilitas', '$user_id')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Properti Berhasil Ditambahkan!');window.location='dataproperti.php';</script>";
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/tambah.css">
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            <h2>Property Admin</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a></li>
            <li><a href="dataproperti.php"><i class="ri-building-line"></i> Data Properti</a></li>
            <li><a href="tambah.php"><i class="ri-add-line"></i> Tambah Properti</a></li>
            <li><a href="pengajuankost.php"><i class="ri-file-list-line"></i> Pengajuan Kos</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="form-container">
            <div class="form-header">
                <h1>Tambah Properti Kost Baru</h1>
                <p>Lengkapi informasi kost dengan detail yang akurat</p>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama">Nama Kost</label>
                        <input type="text" id="nama" name="nama" placeholder="Contoh: Kost Putra" required>
                    </div>
                    <div class="form-group">
                        <label for="lokasi">Lokasi</label>
                        <select id="lokasi" name="lokasi" required>
                            <option value="" selected disabled>Pilih Lokasi</option>
                            <option value="Lokasi Strategis di Pusat Kota">Lokasi Strategis di Pusat Kota</option>
                            <option value="Lokasi Nyaman di Pinggiran Kota">Lokasi Nyaman di Pinggiran Kota</option>
                            <option value="Lokasi Mewah di Pusat Kota">Lokasi Mewah di Pusat Kota</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="kota">Kota</label>
                    <select id="kota" name="kota" required>
                        <option value="" selected disabled>Pilih Kota</option>
                        <option value="jakarta">Jakarta</option>
                        <option value="bandung">Bandung</option>
                        <option value="salatiga">Salatiga</option>
                        <option value="surabaya">Surabaya</option>
                        <option value="semarang">Semarang</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="harga">Harga</label>
                        <input type="number" id="harga" name="harga" placeholder="Masukkan harga kost per bulan"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="sisa_kamar">Sisa Kamar</label>
                        <input type="number" id="sisa_kamar" name="sisa_kamar" placeholder="Jumlah kamar tersedia"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tipe">Tipe Kost</label>
                    <select id="tipe" name="tipe" required>
                        <option value="" selected disabled>Pilih Tipe</option>
                        <option value="campur">Campur</option>
                        <option value="pria">Pria</option>
                        <option value="wanita">Wanita</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Kost</label>
                    <textarea id="deskripsi" name="deskripsi" placeholder="Tuliskan deskripsi lengkap kost"
                        required></textarea>
                </div>

                <div class="form-group">
                    <label for="fasilitas">Fasilitas</label>
                    <textarea id="fasilitas" name="fasilitas" placeholder="Contoh: AC, WiFi, Dapur Bersama"
                        required></textarea>
                </div>

                <div class="form-group upload-group">
                    <label for="gambar">Upload Gambar</label>
                    <input type="file" id="gambar" name="gambar" accept="image/*" required>
                </div>

                <button type="submit" class="btn-submit">Simpan Kost</button>
            </form>
        </div>
    </div>
</body>

</html>