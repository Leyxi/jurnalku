<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['pembimbing']);
include '../../app/_config/database.php';

$id_siswa = $_GET['id_siswa'];
$id_pembimbing = $_SESSION['user_id'];

// Pastikan siswa dibimbing oleh pembimbing ini
$stmt = $pdo->prepare("SELECT COUNT(*) FROM relasi_bimbingan WHERE id_siswa = ? AND id_pembimbing = ?");
$stmt->execute([$id_siswa, $id_pembimbing]);
if ($stmt->fetchColumn() == 0) {
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT u.nama_lengkap FROM users u WHERE u.id = ?");
$stmt->execute([$id_siswa]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM jurnal_harian WHERE id_siswa = ? ORDER BY tanggal_kegiatan DESC");
$stmt->execute([$id_siswa]);
$jurnals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Jurnal <?php echo $siswa['nama_lengkap']; ?></h1>
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Tanggal</th>
                <th class="py-2 px-4 border-b">Deskripsi</th>
                <th class="py-2 px-4 border-b">Status</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jurnals as $jurnal): ?>
            <tr>
                <td class="py-2 px-4 border-b"><?php echo $jurnal['tanggal_kegiatan']; ?></td>
                <td class="py-2 px-4 border-b"><?php echo substr($jurnal['deskripsi_kegiatan'], 0, 50) . '...'; ?></td>
                <td class="py-2 px-4 border-b">
                    <span class="px-2 py-1 rounded <?php echo $jurnal['status'] == 'approved' ? 'bg-green-200' : ($jurnal['status'] == 'rejected' ? 'bg-red-200' : 'bg-yellow-200'); ?>">
                        <?php echo ucfirst($jurnal['status']); ?>
                    </span>
                </td>
                <td class="py-2 px-4 border-b">
                    <a href="review_jurnal.php?id_jurnal=<?php echo $jurnal['id']; ?>" class="text-blue-500">Review</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../../templates/footer.php'; ?>
