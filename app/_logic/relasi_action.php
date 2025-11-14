<?php
include_once __DIR__ . '/../_lib/auth.php';
if (!isset($_SESSION)) {
    // auth.php will start session if needed
}
check_login();
// Only admin may add/delete via this endpoint
check_role(['admin']);

include __DIR__ . '/../_config/database.php';
include __DIR__ . '/../_lib/helpers.php';

$action = $_POST['action'] ?? null;
// All actions require CSRF token
$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    redirect('../../public/admin/manage_relasi.php?error=csrf');
}

if ($action === 'add' || $action === 'add_batch') {
    // Accept single or multiple pembimbing/siswa
    $pbs = $_POST['id_pembimbing'] ?? [];
    $sss = $_POST['id_siswa'] ?? [];
    if (!is_array($pbs)) $pbs = [$pbs];
    if (!is_array($sss)) $sss = [$sss];
    $inserted = 0;
    foreach ($pbs as $p) {
        $id_pembimbing = (int) $p;
        if ($id_pembimbing <= 0) continue;
        // verify pembimbing role
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id_pembimbing]);
        $role_p = $stmt->fetchColumn();
        if ($role_p !== 'pembimbing') continue;
        foreach ($sss as $s) {
            $id_siswa = (int) $s;
            if ($id_siswa <= 0) continue;
            $stmt->execute([$id_siswa]);
            $role_s = $stmt->fetchColumn();
            if ($role_s !== 'siswa') continue;
            $ins = $pdo->prepare("INSERT IGNORE INTO relasi_bimbingan (id_pembimbing, id_siswa) VALUES (?, ?)");
            $ins->execute([$id_pembimbing, $id_siswa]);
            if ($ins->rowCount()) {
                $inserted++;
            }
        }
    }
    redirect('../../public/admin/manage_relasi.php?success=added&count=' . $inserted);

} elseif ($action === 'delete' || $action === 'delete_batch') {
    $ids = $_POST['ids'] ?? [];
    if (!is_array($ids)) $ids = [$ids];
    $deleted = 0;
    $del = $pdo->prepare("DELETE FROM relasi_bimbingan WHERE id = ?");
    foreach ($ids as $id) {
        $id = (int) $id;
        if ($id <= 0) continue;
        // Get relation details for audit
        $stmt = $pdo->prepare("SELECT id_pembimbing, id_siswa FROM relasi_bimbingan WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) continue;
        $del->execute([$id]);
        if ($del->rowCount()) {
            $deleted++;
        }
    }
    redirect('../../public/admin/manage_relasi.php?success=deleted&count=' . $deleted);

} elseif ($action === 'import_csv') {
    // handle CSV import (multipart/form-data)
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        redirect('../../public/admin/manage_relasi.php?error=csv_upload');
    }
    $tmp = $_FILES['csv_file']['tmp_name'];
    $h = fopen($tmp, 'r');
    if (!$h) redirect('../../public/admin/manage_relasi.php?error=csv_read');
    $row = 0; $added = 0;
    while (($data = fgetcsv($h, 1000, ',')) !== FALSE) {
        $row++;
        if ($row === 1) continue; // skip header
        // Expected columns: id_pembimbing, id_siswa
        $id_pembimbing = isset($data[0]) ? (int) $data[0] : 0;
        $id_siswa = isset($data[1]) ? (int) $data[1] : 0;
        if ($id_pembimbing <= 0 || $id_siswa <= 0) continue;
        // verify roles
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id_pembimbing]);
        if ($stmt->fetchColumn() !== 'pembimbing') continue;
        $stmt->execute([$id_siswa]);
        if ($stmt->fetchColumn() !== 'siswa') continue;
        $ins = $pdo->prepare("INSERT IGNORE INTO relasi_bimbingan (id_pembimbing, id_siswa) VALUES (?, ?)");
        $ins->execute([$id_pembimbing, $id_siswa]);
        if ($ins->rowCount()) { $added++; }
    }
    fclose($h);
    redirect('../../public/admin/manage_relasi.php?success=import&added=' . $added);

} elseif ($action === 'export_csv') {
    // Stream CSV of current relations (no redirect)
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="relasi_export.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['id_pembimbing', 'id_siswa']);
    $stmt = $pdo->query('SELECT id_pembimbing, id_siswa FROM relasi_bimbingan');
    while ($r = $stmt->fetch(PDO::FETCH_NUM)) fputcsv($out, $r);
    fclose($out);
    exit;

} else {
    redirect('../../public/admin/manage_relasi.php');
}

?>