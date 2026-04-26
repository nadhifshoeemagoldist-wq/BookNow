<?php
session_start();
require_once 'koneksi.php';

// Cek apakah ada id layanan yang dipilih
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_layanan = $_GET['id'];

// Ambil detail layanan
$query = "SELECT * FROM layanan WHERE id = :id";
$stmt = $koneksi->prepare($query);
$stmt->bindParam(':id', $id_layanan);
$stmt->execute();
$layanan = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$layanan) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - <?= htmlspecialchars($layanan['nama_layanan']) ?></title>
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
        <div class="form-container">
            <h1 class="page-title" style="font-size: 1.8rem; margin-bottom: 1rem;">Form Booking</h1>
            <p class="page-subtitle" style="margin-bottom: 2rem;">Layanan: <strong><?= htmlspecialchars($layanan['nama_layanan']) ?></strong></p>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="proses_booking.php" method="POST">
                <input type="hidden" name="id_layanan" value="<?= $layanan['id'] ?>">

                <div class="form-group">
                    <label for="nama_user">Nama Lengkap</label>
                    <input type="text" id="nama_user" name="nama_user" class="form-control" required placeholder="Masukkan nama Anda" value="<?= isset($_SESSION['user_nama']) ? htmlspecialchars($_SESSION['user_nama']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal Booking</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="jam">Jam Booking</label>
                    <input type="time" id="jam" name="jam" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Konfirmasi Booking</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> BookNow. All rights reserved.</p>
    </footer>

</body>
</html>
