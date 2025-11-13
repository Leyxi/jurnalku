<?php
include '../_config/database.php';
include '../_lib/helpers.php';
include '../_lib/auth.php';
check_login();
check_role(['admin']);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM pengumuman WHERE id = ?");
    $stmt->execute([$id]);
    redirect('../../public/admin/pengumuman_manage.php?success=Pengumuman deleted');
}
?>
