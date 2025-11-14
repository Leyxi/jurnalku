<?php
// CLI migration: add `tanggal_review` column to jurnal_harian if missing.
if (php_sapi_name() !== 'cli') {
    echo "Run this script from the command line: php app/_tools/add_tanggal_review.php\n";
    exit(1);
}

require_once __DIR__ . '/../_config/database.php';

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jurnal_harian' AND COLUMN_NAME = 'tanggal_review'");
    $stmt->execute();
    $exists = (bool) $stmt->fetchColumn();
    if ($exists) {
        echo "Column 'tanggal_review' already exists in jurnal_harian.\n";
        exit(0);
    }

    $pdo->exec("ALTER TABLE jurnal_harian ADD COLUMN tanggal_review TIMESTAMP NULL DEFAULT NULL AFTER komentar_pembimbing");
    echo "Added column 'tanggal_review' to jurnal_harian.\n";
} catch (PDOException $e) {
    echo "Error running migration: " . $e->getMessage() . "\n";
    exit(2);
}

?>