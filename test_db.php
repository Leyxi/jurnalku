<?php
include 'app/_config/database.php';

try {
    // Test koneksi
    echo "Database connection: SUCCESS\n";

    // Test query users
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Users in database: " . $result['count'] . "\n";

    // Test query jurnal
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM jurnal_harian');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Jurnal entries: " . $result['count'] . "\n";

    // Test query pengumuman
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM pengumuman');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Pengumuman entries: " . $result['count'] . "\n";

    // Test login query
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@pklhero.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "Admin user found: " . $user['nama_lengkap'] . " (" . $user['role'] . ")\n";
        echo "Password hash verification: " . (password_verify('password', $user['password']) ? 'SUCCESS' : 'FAILED') . "\n";
    } else {
        echo "Admin user NOT found\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
