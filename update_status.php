<?php
session_start();
require_once 'koneksi.php';

// Cek session login admin
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    try {
        if($action == 'update') {
            $status = $_POST['status'];
            $query = "UPDATE booking SET status = :status WHERE id = :id";
            $stmt = $koneksi->prepare($query);
            $stmt->execute([':status' => $status, ':id' => $id]);
            $_SESSION['pesan'] = "Status booking #$id berhasil diupdate!";
        } 
        elseif($action == 'delete') {
            $query = "DELETE FROM booking WHERE id = :id";
            $stmt = $koneksi->prepare($query);
            $stmt->execute([':id' => $id]);
            $_SESSION['pesan'] = "Data booking #$id berhasil dihapus!";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    }
}

header("Location: admin.php");
exit;
?>
