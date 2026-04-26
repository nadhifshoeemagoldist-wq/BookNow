<?php
session_start();
require_once 'koneksi.php';

// Jika sudah login, langsung ke dashboard admin
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE username = :username";
    $stmt = $koneksi->prepare($query);
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: admin.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - BookNow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: var(--bg-color);">

    <div class="form-container" style="width: 100%; max-width: 400px;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="index.php" class="logo" style="justify-content: center; font-size: 2rem;">Book<span>Now</span></a>
            <p class="page-subtitle" style="margin-bottom: 0; margin-top: 0.5rem;">Admin Panel Login</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required placeholder="Masukkan username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Masukkan password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="index.php" style="color: var(--text-light); text-decoration: none; font-size: 0.9rem;">&larr; Kembali ke Beranda</a>
        </div>
    </div>

</body>
</html>
