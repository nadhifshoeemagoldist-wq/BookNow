<?php
session_start();
require_once 'koneksi.php';

// Fetch all services from database
$query = "SELECT * FROM layanan";
$stmt = $koneksi->prepare($query);
$stmt->execute();
$layanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNow - Sistem Booking Online</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="navbar">
            <a href="index.php" class="logo">Book<span>Now</span></a>
            <div class="nav-links">
                <a href="index.php">Layanan</a>
                <a href="riwayat.php">Riwayat Booking</a>
                <a href="login.php">Admin Panel</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Selamat Datang di BookNow</h1>
        <p class="page-subtitle">Pesan berbagai layanan profesional kami dengan mudah dan cepat.</p>

        <?php if(isset($_SESSION['pesan'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['pesan']; ?>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="services-grid">
            <?php foreach($layanan as $row): ?>
            <div class="card">
                <!-- Using placeholder if image doesn't exist yet -->
                <img src="gambar/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_layanan']) ?>" class="card-img" onerror="this.src='https://placehold.co/600x400/e2e8f0/64748b?text=<?= urlencode($row['nama_layanan']) ?>'">
                <div class="card-content">
                    <h3 class="card-title"><?= htmlspecialchars($row['nama_layanan']) ?></h3>
                    <p class="card-desc"><?= htmlspecialchars($row['deskripsi']) ?></p>
                    <a href="booking.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-block">Booking Sekarang</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> BookNow. All rights reserved.</p>
    </footer>

</body>
</html>
