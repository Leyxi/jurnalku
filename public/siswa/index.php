<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['siswa']);
include '../../app/_config/database.php';

$id_siswa = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM jurnal_harian WHERE id_siswa = ? ORDER BY tanggal_kegiatan DESC");
$stmt->execute([$id_siswa]);
$jurnals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pengumuman untuk siswa
$stmt = $pdo->prepare("SELECT * FROM pengumuman WHERE target_audien IN ('all', 'siswa') ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$pengumumans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Dashboard Siswa</h1>
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-4">Pengumuman</h2>
        <?php if (empty($pengumumans)): ?>
            <p class="text-gray-500">Tidak ada pengumuman.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($pengumumans as $p): ?>
                <div class="bg-blue-50 p-4 rounded">
                    <h3 class="font-semibold"><?php echo $p['judul']; ?></h3>
                    <p><?php echo $p['isi']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <a href="jurnal_buat.php" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Buat Jurnal Baru</a>
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Tanggal</th>
                <th class="py-2 px-4 border-b">Deskripsi</th>
                <th class="py-2 px-4 border-b">Status</th>
                <th class="py-2 px-4 border-b">Komentar</th>
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
                <td class="py-2 px-4 border-b"><?php echo $jurnal['komentar_pembimbing'] ?? '-'; ?></td>
                <td class="py-2 px-4 border-b">
                    <a href="jurnal_edit.php?id=<?php echo $jurnal['id']; ?>" class="text-blue-500">Edit</a>
                    <a href="../../app/_logic/jurnal_delete.php?id=<?php echo $jurnal['id']; ?>" class="text-red-500 ml-4" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../../templates/footer.php'; ?>
