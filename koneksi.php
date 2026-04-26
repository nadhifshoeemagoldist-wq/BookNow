<?php
$host = 'localhost';
$dbname = 'booknow';
$username = 'root'; // Adjust this if your MySQL uses a different username
$password = ''; // Adjust this if your MySQL uses a different password

try {
    $koneksi = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Koneksi database gagal: " . $e->getMessage();
    die();
}
?>
