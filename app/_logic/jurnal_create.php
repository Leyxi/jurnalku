<?php
include '../_config/database.php';
include '../_lib/helpers.php';
include '../_lib/auth.php';
check_login();
check_role(['siswa']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_kegiatan = $_POST['tanggal_kegiatan'];
    $deskripsi_kegiatan = sanitize($_POST['deskripsi_kegiatan']);
    $kendala = sanitize($_POST['kendala']);
    $solusi = sanitize($_POST['solusi']);
    $id_siswa = $_SESSION['user_id'];

    // Insert jurnal
    $stmt = $pdo->prepare("INSERT INTO jurnal_harian (id_siswa, tanggal_kegiatan, deskripsi_kegiatan, kendala, solusi) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id_siswa, $tanggal_kegiatan, $deskripsi_kegiatan, $kendala, $solusi]);
    $id_jurnal = $pdo->lastInsertId();

    // Handle foto
    if (isset($_FILES['foto_bukti'])) {
        $upload_dir = '../../public/uploads/jurnal/';
        foreach ($_FILES['foto_bukti']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['foto_bukti']['name'][$key];
            $file_size = $_FILES['foto_bukti']['size'][$key];
            $file_type = $_FILES['foto_bukti']['type'][$key];

            // Validasi
            if ($file_size > 5000000) { // 5MB
                continue;
            }
            if (!in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
                continue;
            }

            $unique_name = uniqid() . '_' . $file_name;
            $file_path = $upload_dir . $unique_name;

            if (move_uploaded_file($tmp_name, $file_path)) {
                $stmt = $pdo->prepare("INSERT INTO jurnal_foto (id_jurnal, file_path) VALUES (?, ?)");
                $stmt->execute([$id_jurnal, $unique_name]);
            }
        }
    }

    redirect('../../public/siswa/?success=Jurnal created');
}
?>
