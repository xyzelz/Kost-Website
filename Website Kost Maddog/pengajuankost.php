<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
include 'koneksi.php';

// Proses tambah ke tabel kost
if (isset($_GET['tambah'])) {
    $id = $_GET['tambah'];

    // Ambil data dari tabel pengajuan_kos
    $query = "SELECT * FROM daftarkos WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        // Tambahkan prefix 'img/' ke kolom gambar
        $gambar = $row['gambar'];
        $gambar_with_prefix = "img/" . $gambar;

        // Masukkan ke tabel kost
        $insert_query = "INSERT INTO kost 
                     (nama, lokasi, kota, harga, sisa_kamar, tipe, deskripsi, fasilitas, gambar) 
                     VALUES 
                     ('{$row['nama']}', '{$row['lokasi']}', '{$row['kota']}', 
                      '{$row['harga']}', '{$row['sisa_kamar']}', '{$row['tipe']}', 
                      '{$row['deskripsi']}', '{$row['fasilitas']}', '$gambar_with_prefix')";

        if (mysqli_query($conn, $insert_query)) {
            // Hapus data dari pengajuan_kos setelah berhasil ditambahkan
            $delete_query = "DELETE FROM daftarkos WHERE id = $id";
            mysqli_query($conn, $delete_query);

            echo "<script>alert('Kos berhasil ditambahkan!');window.location='pengajuankost.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan kos!');</script>";
        }
    } else {
        echo "<script>alert('Data tidak ditemukan!');</script>";
    }
}

// Query data pengajuan kos dari database
$result = mysqli_query($conn, "SELECT * FROM daftarkos");
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Properti</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/pengajuankost.css">
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
        <div class="dashboard-header">
            <h1>Pengajuan Daftar Kos</h1>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Lokasi</th>
                    <th>Kota</th>
                    <th>Harga</th>
                    <th>Sisa Kamar</th>
                    <th>Tipe</th>
                    <th>Deskripsi</th>
                    <th>Fasilitas</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['nama']}</td>
                <td>{$row['lokasi']}</td>
                <td>{$row['kota']}</td>
                <td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                <td>{$row['sisa_kamar']}</td>
                <td>{$row['tipe']}</td>
                <td>" . substr($row['deskripsi'], 0, 50) . "...</td>
                <td>{$row['fasilitas']}</td>
                <td><img src='img/{$row['gambar']}' alt='{$row['nama']}' style='max-width:50px; height:auto;'></td>
                <td>
                    <a href='pengajuankost.php?tambah={$row['id']}' class='btn btn-edit' onclick=\"return confirm('Apakah Anda yakin ingin menambahkan kos ini?');\">Tambah</a>
                    <a href='pengajuankost.php?hapus={$row['id']}' class='btn btn-hapus' onclick=\"return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');\">Hapus</a>
                </td>
            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>Tidak ada pengajuan kos</td></tr>";
                }
                ?>
            </tbody>

        </table>
    </div>
</body>

</html>