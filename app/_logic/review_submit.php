<?php
include '../_config/database.php';
include '../_lib/helpers.php';
include '../_lib/auth.php';
check_login();
check_role(['pembimbing']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_jurnal = $_POST['id_jurnal'];
    $komentar = sanitize($_POST['komentar']);
    $nilai_apresiasi = $_POST['nilai_apresiasi'];
    $status = $_POST['status'];
    $id_pembimbing = $_SESSION['user_id'];

    // Pastikan jurnal milik siswa yang dibimbing
    $stmt = $pdo->prepare("SELECT j.id_siswa FROM jurnal_harian j JOIN relasi_bimbingan r ON j.id_siswa = r.id_siswa WHERE j.id = ? AND r.id_pembimbing = ?");
    $stmt->execute([$id_jurnal, $id_pembimbing]);
    if (!$stmt->fetch()) {
        redirect('../../public/pembimbing/index.php');
    }

    $stmt = $pdo->prepare("UPDATE jurnal_harian SET status = ?, komentar_pembimbing = ?, nilai_apresiasi = ? WHERE id = ?");
    $stmt->execute([$status, $komentar, $nilai_apresiasi, $id_jurnal]);

    redirect('../../public/pembimbing/siswa_detail.php?id_siswa=' . $_POST['id_siswa'] . '&success=Review submitted');
}
?>
