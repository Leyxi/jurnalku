<?php
include '../_config/database.php';
include '../_lib/helpers.php';
include '../_lib/auth.php';
check_login();
check_role(['siswa']);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $id_siswa = $_SESSION['user_id'];

    // Hapus foto fisik
    $stmt = $pdo->prepare("SELECT file_path FROM jurnal_foto WHERE id_jurnal = ?");
    $stmt->execute([$id]);
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fotos as $foto) {
        unlink('../../public/uploads/jurnal/' . $foto['file_path']);
    }

    // Hapus dari DB
    $stmt = $pdo->prepare("DELETE FROM jurnal_foto WHERE id_jurnal = ?");
    $stmt->execute([$id]);

    $stmt = $pdo->prepare("DELETE FROM jurnal_harian WHERE id = ? AND id_siswa = ?");
    $stmt->execute([$id, $id_siswa]);

    redirect('../../public/siswa/?success=Jurnal deleted');
}
?>
