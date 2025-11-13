<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['pembimbing']);
include '../../app/_config/database.php';

$id_jurnal = $_GET['id_jurnal'];
$id_pembimbing = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT j.*, u.nama_lengkap FROM jurnal_harian j JOIN users u ON j.id_siswa = u.id WHERE j.id = ?");
$stmt->execute([$id_jurnal]);
$jurnal = $stmt->fetch(PDO::FETCH_ASSOC);

// Pastikan siswa dibimbing oleh pembimbing ini
$stmt = $pdo->prepare("SELECT COUNT(*) FROM relasi_bimbingan WHERE id_siswa = ? AND id_pembimbing = ?");
$stmt->execute([$jurnal['id_siswa'], $id_pembimbing]);
if ($stmt->fetchColumn() == 0) {
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT * FROM jurnal_foto WHERE id_jurnal = ?");
$stmt->execute([$id_jurnal]);
$fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Review Jurnal - <?php echo $jurnal['nama_lengkap']; ?></h1>
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-xl font-semibold mb-4">Detail Jurnal</h2>
        <p><strong>Tanggal:</strong> <?php echo $jurnal['tanggal_kegiatan']; ?></p>
        <p><strong>Deskripsi:</strong> <?php echo $jurnal['deskripsi_kegiatan']; ?></p>
        <p><strong>Kendala:</strong> <?php echo $jurnal['kendala'] ?: '-'; ?></p>
        <p><strong>Solusi:</strong> <?php echo $jurnal['solusi'] ?: '-'; ?></p>
        <div class="mt-4">
            <strong>Foto Bukti:</strong>
            <div class="flex flex-wrap mt-2">
                <?php foreach ($fotos as $foto): ?>
                <img src="../../public/uploads/jurnal/<?php echo $foto['file_path']; ?>" alt="Foto" class="w-32 h-32 object-cover mr-2 mb-2">
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <form action="../../app/_logic/review_submit.php" method="POST" class="bg-white p-6 rounded shadow">
        <input type="hidden" name="id_jurnal" value="<?php echo $id_jurnal; ?>">
        <div class="mb-4">
            <label for="komentar" class="block">Komentar</label>
            <textarea id="komentar" name="komentar" class="w-full border px-3 py-2" rows="4" required></textarea>
        </div>
        <div class="mb-4">
            <label for="nilai_apresiasi" class="block">Nilai Apresiasi (1-5)</label>
            <select id="nilai_apresiasi" name="nilai_apresiasi" class="w-full border px-3 py-2" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block">Status</label>
            <div class="flex">
                <label class="mr-4">
                    <input type="radio" name="status" value="approved" required> Approve
                </label>
                <label>
                    <input type="radio" name="status" value="rejected" required> Reject
                </label>
            </div>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Submit Review</button>
    </form>
</div>
<?php include '../../templates/footer.php'; ?>
