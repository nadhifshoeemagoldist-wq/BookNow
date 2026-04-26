<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_layanan = $_POST['id_layanan'];
    $nama_user = trim($_POST['nama_user']);
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];

    // Validasi input kosong
    if (empty($id_layanan) || empty($nama_user) || empty($tanggal) || empty($jam)) {
        $_SESSION['error'] = "Semua field wajib diisi!";
        header("Location: booking.php?id=" . $id_layanan);
        exit;
    }

    // Validasi double booking (jam yang sama pada tanggal dan layanan yang sama)
    $query_cek = "SELECT id FROM booking WHERE id_layanan = :id_layanan AND tanggal = :tanggal AND jam = :jam AND status != 'Selesai'";
    $stmt_cek = $koneksi->prepare($query_cek);
    $stmt_cek->execute([
        ':id_layanan' => $id_layanan,
        ':tanggal' => $tanggal,
        ':jam' => $jam
    ]);

    if ($stmt_cek->rowCount() > 0) {
        $_SESSION['error'] = "Maaf, jadwal pada tanggal dan jam tersebut sudah terbooking. Silakan pilih waktu lain.";
        header("Location: booking.php?id=" . $id_layanan);
        exit;
    }

    // Jika tidak ada double booking, simpan ke database
    try {
        $query_insert = "INSERT INTO booking (id_layanan, nama_user, tanggal, jam, status) VALUES (:id_layanan, :nama_user, :tanggal, :jam, 'Pending')";
        $stmt_insert = $koneksi->prepare($query_insert);
        $stmt_insert->execute([
            ':id_layanan' => $id_layanan,
            ':nama_user' => $nama_user,
            ':tanggal' => $tanggal,
            ':jam' => $jam
        ]);

        // Simpan nama di session agar mudah dilihat di halaman riwayat
        $_SESSION['user_nama'] = $nama_user;
        $_SESSION['pesan'] = "Booking berhasil! Menunggu persetujuan admin.";
        
        header("Location: riwayat.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: booking.php?id=" . $id_layanan);
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>
