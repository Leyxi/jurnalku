<?php
include '../_config/database.php';
include '../_lib/helpers.php';
include '../_lib/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // Cari user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];

        // Redirect berdasarkan role
        if ($user['role'] == 'admin') {
            header('Location: ../../public/admin/');
            exit();
        } elseif ($user['role'] == 'pembimbing') {
            header('Location: ../../public/pembimbing/');
            exit();
        } elseif ($user['role'] == 'siswa') {
            header('Location: ../../public/siswa/');
            exit();
        }
    } else {
        redirect('../../public/login.php?error=Invalid credentials');
    }
}
?>
