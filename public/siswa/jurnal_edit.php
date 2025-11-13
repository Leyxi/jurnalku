<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['siswa']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$id = $_GET['id'];
$id_siswa = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM jurnal_harian WHERE id = ? AND id_siswa = ?");
$stmt->execute([$id, $id_siswa]);
$jurnal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jurnal) {
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT * FROM jurnal_foto WHERE id_jurnal = ?");
$stmt->execute([$id]);
$fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_kegiatan = $_POST['tanggal_kegiatan'];
    $deskripsi_kegiatan = sanitize($_POST['deskripsi_kegiatan']);
    $kendala = sanitize($_POST['kendala']);
    $solusi = sanitize($_POST['solusi']);

    $stmt = $pdo->prepare("UPDATE jurnal_harian SET tanggal_kegiatan = ?, deskripsi_kegiatan = ?, kendala = ?, solusi = ? WHERE id = ? AND id_siswa = ?");
    $stmt->execute([$tanggal_kegiatan, $deskripsi_kegiatan, $kendala, $solusi, $id, $id_siswa]);

    // Handle new fotos
    if (isset($_FILES['foto_bukti'])) {
        $upload_dir = '../../public/uploads/jurnal/';
        foreach ($_FILES['foto_bukti']['tmp_name'] as $key => $tmp_name) {
            if (empty($tmp_name)) continue;
            $file_name = $_FILES['foto_bukti']['name'][$key];
            $file_size = $_FILES['foto_bukti']['size'][$key];
            $file_type = $_FILES['foto_bukti']['type'][$key];

            if ($file_size > 5000000 || !in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
                continue;
            }

            $unique_name = uniqid() . '_' . $file_name;
            $file_path = $upload_dir . $unique_name;

            if (move_uploaded_file($tmp_name, $file_path)) {
                $stmt = $pdo->prepare("INSERT INTO jurnal_foto (id_jurnal, file_path) VALUES (?, ?)");
                $stmt->execute([$id, $unique_name]);
            }
        }
    }

    redirect('index.php?success=Jurnal updated');
}
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Edit Jurnal</h1>
    <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        <div class="mb-4">
            <label for="tanggal_kegiatan" class="block">Tanggal Kegiatan</label>
            <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan" value="<?php echo $jurnal['tanggal_kegiatan']; ?>" class="w-full border px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="deskripsi_kegiatan" class="block">Deskripsi Kegiatan</label>
            <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" class="w-full border px-3 py-2" rows="4" required><?php echo $jurnal['deskripsi_kegiatan']; ?></textarea>
        </div>
        <div class="mb-4">
            <label for="kendala" class="block">Kendala</label>
            <textarea id="kendala" name="kendala" class="w-full border px-3 py-2" rows="3"><?php echo $jurnal['kendala']; ?></textarea>
        </div>
        <div class="mb-4">
            <label for="solusi" class="block">Solusi</label>
            <textarea id="solusi" name="solusi" class="w-full border px-3 py-2" rows="3"><?php echo $jurnal['solusi']; ?></textarea>
        </div>
        <div class="mb-4">
            <label class="block">Foto Bukti Saat Ini</label>
            <?php foreach ($fotos as $foto): ?>
                <div class="inline-block mr-2 mb-2">
                    <img src="../../public/uploads/jurnal/<?php echo $foto['file_path']; ?>" alt="Foto" class="w-20 h-20 object-cover">
                    <a href="../../app/_logic/delete_foto.php?id=<?php echo $foto['id']; ?>&jurnal_id=<?php echo $id; ?>" class="text-red-500 text-xs">Hapus</a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mb-4">
            <label for="foto_bukti" class="block">Tambah Foto Baru (Opsional)</label>
            <input type="file" id="foto_bukti" name="foto_bukti[]" multiple accept="image/*" class="w-full border px-3 py-2">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Jurnal</button>
    </form>
</div>
<?php include '../../templates/footer.php'; ?>
