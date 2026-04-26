<?php
session_start();
require_once 'koneksi.php';

// Cek session login admin
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Proses Logout
if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Proses Tambah Layanan
if(isset($_POST['tambah_layanan'])) {
    $nama_layanan = $_POST['nama_layanan'];
    $deskripsi = $_POST['deskripsi'];
    
    // Upload Gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $ext = pathinfo($gambar, PATHINFO_EXTENSION);
    $nama_baru = time() . '_' . rand(100,999) . '.' . $ext;
    $path = "gambar/" . $nama_baru;
    
    if(move_uploaded_file($tmp, $path)) {
        $query_layanan = "INSERT INTO layanan (nama_layanan, deskripsi, gambar) VALUES (:nama, :desk, :gbr)";
        $stmt_layanan = $koneksi->prepare($query_layanan);
        $stmt_layanan->execute([':nama' => $nama_layanan, ':desk' => $deskripsi, ':gbr' => $nama_baru]);
        $_SESSION['pesan'] = "Layanan baru berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal mengupload gambar layanan.";
    }
    header("Location: admin.php");
    exit;
}

// Ambil semua data booking
$query = "SELECT b.*, l.nama_layanan 
          FROM booking b 
          JOIN layanan l ON b.id_layanan = l.id 
          ORDER BY b.tanggal DESC, b.jam DESC";
$stmt = $koneksi->prepare($query);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookNow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .action-form { display: inline-block; }
        .action-select { padding: 0.25rem; border-radius: 0.25rem; border: 1px solid var(--border-color); }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.85rem; }
    </style>
</head>
<body>

    <header>
        <div class="navbar">
            <a href="admin.php" class="logo">Book<span>Now</span> <span style="font-size: 1rem; color: var(--text-light);">| Admin</span></a>
            <div class="nav-links">
                <a href="index.php" target="_blank">Lihat Website</a>
                <a href="admin.php?logout=1" style="color: #ef4444;">Logout</a>
            </div>
        </div>
    </header>

    <div class="container" style="max-width: 100%; padding: 0 2rem;">
        <h1 class="page-title" style="text-align: left; margin-top: 2rem;">Manajemen Booking</h1>
        
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

        <div class="table-responsive" style="margin-top: 2rem;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama User</th>
                        <th>Layanan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($bookings) > 0): ?>
                        <?php foreach($bookings as $row): ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['nama_user']) ?></td>
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
                            <td>
                                <!-- Form Update Status -->
                                <form action="update_status.php" method="POST" class="action-form">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="action" value="update">
                                    <select name="status" class="action-select">
                                        <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Disetujui" <?= $row['status'] == 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                        <option value="Selesai" <?= $row['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                </form>

                                <!-- Form Hapus -->
                                <form action="update_status.php" method="POST" class="action-form" onsubmit="return confirm('Yakin ingin menghapus data booking ini?');">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Belum ada data booking.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Bagian Tambah Layanan Baru -->
        <h2 class="page-title" style="text-align: left; margin-top: 4rem; font-size: 1.8rem;">Tambah Layanan Baru</h2>
        <div class="form-container" style="max-width: 600px; margin: 2rem 0; padding: 2rem; background: var(--card-bg); box-shadow: var(--shadow); border-radius: var(--radius);">
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama_layanan">Nama Layanan</label>
                    <input type="text" id="nama_layanan" name="nama_layanan" class="form-control" required placeholder="Contoh: Studio Foto">
                </div>
                
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" required placeholder="Jelaskan tentang layanan ini..." rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="gambar">Upload Gambar Layanan</label>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*" required>
                    <small style="color: var(--text-light); margin-top: 0.25rem; display: block;">Format: JPG, PNG, GIF</small>
                </div>
                
                <button type="submit" name="tambah_layanan" class="btn btn-secondary">Simpan Layanan</button>
            </form>
        </div>
        
    </div>

</body>
</html>
