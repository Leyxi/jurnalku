<?php
include_once __DIR__ . '/../_lib/auth.php';
check_login();
check_role(['pembimbing']);
include __DIR__ . '/../_config/database.php';
include __DIR__ . '/../_lib/helpers.php';

// Remove relation by id, but ensure current pembimbing owns it
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$id_pembimbing = $_SESSION['user_id'];
if ($id <= 0) redirect('../../public/pembimbing/index.php');

// verify csrf
$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    redirect('../../public/pembimbing/index.php?error=csrf');
}

// Verify relation belongs to this pembimbing
$stmt = $pdo->prepare("SELECT id FROM relasi_bimbingan WHERE id = ? AND id_pembimbing = ?");
$stmt->execute([$id, $id_pembimbing]);
if ($stmt->fetchColumn()) {
    $del = $pdo->prepare("DELETE FROM relasi_bimbingan WHERE id = ?");
    $del->execute([$id]);
}
redirect('../../public/pembimbing/index.php');
?>