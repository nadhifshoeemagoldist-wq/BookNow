<?php
session_start();
require_once 'koneksi.php';

$nama_pencarian = "";
$bookings = [];

// Jika disubmit lewat form pencarian atau sudah ada di session
if (isset($_POST['cari_nama'])) {
    $nama_pencarian = trim($_POST['nama_user']);
    $_SESSION['user_nama'] = $nama_pencarian; // Update session
} elseif (isset($_SESSION['user_nama'])) {
    $nama_pencarian = $_SESSION['user_nama'];
}

if (!empty($nama_pencarian)) {
    // Cari riwayat booking berdasarkan nama user
    $query = "SELECT b.*, l.nama_layanan 
              FROM booking b 
              JOIN layanan l ON b.id_layanan = l.id 
              WHERE b.nama_user LIKE :nama_user 
              ORDER BY b.tanggal DESC, b.jam DESC";
    $stmt = $koneksi->prepare($query);
    $stmt->execute([':nama_user' => '%' . $nama_pencarian . '%']);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - BookNow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="navbar">
            <a href="index.php" class="logo">Book<span>Now</span></a>
            <div class="nav-links">
                <a href="index.php">Layanan</a>
                <a href="riwayat.php" style="color: var(--primary-color);">Riwayat Booking</a>
                <a href="login.php">Admin Panel</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Riwayat Booking</h1>
        <p class="page-subtitle">Cek status pesanan layanan Anda di sini.</p>

        <?php if(isset($_SESSION['pesan'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['pesan']; ?>
            </div>
            <?php unset($_SESSION['pesan']); ?>
        <?php endif; ?>

        <div class="form-container" style="max-width: 600px; margin-bottom: 2rem;">
            <form action="riwayat.php" method="POST" style="display: flex; gap: 1rem; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0; flex-grow: 1;">
                    <label for="nama_user">Cari Berdasarkan Nama Anda</label>
                    <input type="text" id="nama_user" name="nama_user" class="form-control" required placeholder="Masukkan nama..." value="<?= htmlspecialchars($nama_pencarian) ?>">
                </div>
                <button type="submit" name="cari_nama" class="btn btn-primary">Cari</button>
            </form>
        </div>

        <?php if (!empty($nama_pencarian)): ?>
            <?php if (count($bookings) > 0): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Layanan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($bookings as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_layanan']) ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= date('H:i', strtotime($row['jam'])) ?></td>
                                <td>
                                    <?php 
                                        $badgeClass = '';
                                        if($row['status'] == 'Pending') $badgeClass = 'badge-pending';
                                        elseif($row['status'] == 'Disetujui') $badgeClass = 'badge-disetujui';
                                        elseif($row['status'] == 'Selesai') $badgeClass = 'badge-selesai';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $row['status'] ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    Belum ada riwayat booking untuk nama "<?= htmlspecialchars($nama_pencarian) ?>".
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p style="text-align: center; color: var(--text-light);">Silakan masukkan nama Anda untuk melihat riwayat booking.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> BookNow. All rights reserved.</p>
    </footer>

</body>
</html>
