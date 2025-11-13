<?php
include '../_config/database.php';
include '../_lib/helpers.php';
include '../_lib/auth.php';
check_login();
check_role(['siswa']);

if (isset($_GET['id']) && isset($_GET['jurnal_id'])) {
    $id = $_GET['id'];
    $jurnal_id = $_GET['jurnal_id'];
    $id_siswa = $_SESSION['user_id'];

    // Pastikan jurnal milik siswa
    $stmt = $pdo->prepare("SELECT id FROM jurnal_harian WHERE id = ? AND id_siswa = ?");
    $stmt->execute([$jurnal_id, $id_siswa]);
    if ($stmt->fetch()) {
        // Hapus file fisik
        $stmt = $pdo->prepare("SELECT file_path FROM jurnal_foto WHERE id = ?");
        $stmt->execute([$id]);
        $foto = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($foto) {
            unlink('../../public/uploads/jurnal/' . $foto['file_path']);
        }

        // Hapus dari DB
        $stmt = $pdo->prepare("DELETE FROM jurnal_foto WHERE id = ?");
        $stmt->execute([$id]);
    }

    redirect('../../public/siswa/jurnal_edit.php?id=' . $jurnal_id);
}
?>
