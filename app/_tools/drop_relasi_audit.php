<?php
// CLI-only script to drop relasi_audit table if it exists.
if (php_sapi_name() !== 'cli') {
    echo "Run this script from the command line: php app/_tools/drop_relasi_audit.php\n";
    exit(1);
}

require_once __DIR__ . '/../_config/database.php';

try {
    $pdo->exec("DROP TABLE IF EXISTS relasi_audit");
    echo "relasi_audit table dropped (if it existed)\n";
} catch (PDOException $e) {
    echo "Error dropping table: " . $e->getMessage() . "\n";
    exit(2);
}

?>