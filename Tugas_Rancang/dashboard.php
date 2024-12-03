<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
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
            <h1>Selamat Datang di Dashboard</h1>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </div>
        <p>Ini adalah area utama dari dashboard admin. Silakan pilih menu di sidebar untuk melanjutkan.</p>
    </div>
</body>

</html>