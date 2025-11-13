<?php
include '../_config/database.php';
include '../_lib/helpers.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // Validasi
    if (empty($nama_lengkap) || empty($email) || empty($password)) {
        redirect('../../public/register.php?error=All fields are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect('../../public/register.php?error=Invalid email format');
    }

    // Cek email sudah ada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        redirect('../../public/register.php?error=Email already exists');
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, 'siswa')");
    $stmt->execute([$nama_lengkap, $email, $hashed_password]);

    redirect('../../public/login.php?success=Registration successful');
}
?>
