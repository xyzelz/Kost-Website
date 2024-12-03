<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Koneksi ke database
include 'koneksi.php';

// Query data kost dari database
$result = mysqli_query($conn, "SELECT * FROM kost");
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $sisa_kamar = $_POST['sisa_kamar'];
    $tipe = $_POST['tipe'];
    $deskripsi = $_POST['deskripsi'];
    $fasilitas = $_POST['fasilitas'];

    $query = "UPDATE kost SET 
              nama = '$nama', 
              harga = '$harga', 
              sisa_kamar = '$sisa_kamar', 
              tipe = '$tipe', 
              deskripsi = '$deskripsi',
              fasilitas = '$fasilitas' 
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil diperbarui!');window.location='dataproperti.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM kost WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil dihapus!');window.location='dataproperti.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!');</script>";
    }
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
    <link rel="stylesheet" href="css/dataproperti.css">
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
            <h1>Data Properti</h1>
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
                    <th>Gambar</th>
                    <th>Fasilitas</th>
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
                <td><img src='{$row['gambar']}' alt='Gambar Properti' class='thumbnail'></td>
                <td>{$row['fasilitas']}</td>
                <td>
                    <button class='btn btn-edit' onclick=\"openModal(" . htmlspecialchars(json_encode($row)) . ")\">Edit</button>
                    <a href='dataproperti.php?hapus={$row['id']}' class='btn btn-hapus' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?');\">Hapus</a>
                </td>
            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>Tidak ada data</td></tr>";
                }
                ?>
            </tbody>

        </table>
    </div>

    <!-- Modal Popup -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <div class="modal-header">Edit Properti</div>
            <form method="POST" action="dataproperti.php">
                <input type="hidden" name="id" id="edit-id">
                <div class="form-group">
                    <label for="edit-nama">Nama Lokasi</label>
                    <input type="text" id="edit-nama" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="edit-harga">Harga</label>
                    <input type="number" id="edit-harga" name="harga" required>
                </div>
                <div class="form-group">
                    <label for="edit-sisa-kamar">Sisa Kamar</label>
                    <input type="number" id="edit-sisa-kamar" name="sisa_kamar" required>
                </div>
                <div class="form-group">
                    <label for="edit-tipe">Tipe</label>
                    <input type="text" id="edit-tipe" name="tipe" required>
                </div>
                <div class="form-group">
                    <label for="edit-deskripsi">Deskripsi</label>
                    <textarea id="edit-deskripsi" name="deskripsi" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit-fasilitas">Fasilitas</label>
                    <input type="text" id="edit-fasilitas" name="fasilitas" required>
                </div>
                <button type="submit" name="update" class="btn">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script>
    // Membuka modal dan memuat data ke form
    function openModal(data) {
        document.getElementById('edit-id').value = data.id;
        document.getElementById('edit-nama').value = data.nama;
        document.getElementById('edit-harga').value = data.harga;
        document.getElementById('edit-sisa-kamar').value = data.sisa_kamar;
        document.getElementById('edit-tipe').value = data.tipe;
        document.getElementById('edit-deskripsi').value = data.deskripsi;
        document.getElementById('edit-fasilitas').value = data.fasilitas;
        document.getElementById('editModal').style.display = 'flex';
    }

    // Menutup modal
    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
    </script>
</body>

</html>