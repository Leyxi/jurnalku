<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['pembimbing']);
include '../../app/_config/database.php';

$id_pembimbing = $_SESSION['user_id'];

// Ambil siswa yang dibimbing
$stmt = $pdo->prepare("SELECT u.id, u.nama_lengkap FROM users u JOIN relasi_bimbingan r ON u.id = r.id_siswa WHERE r.id_pembimbing = ?");
$stmt->execute([$id_pembimbing]);
$siswas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pengumuman untuk pembimbing
$stmt = $pdo->prepare("SELECT * FROM pengumuman WHERE target_audien IN ('all', 'pembimbing') ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$pengumumans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Dashboard Pembimbing</h1>
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($siswas as $siswa): ?>
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-2"><?php echo $siswa['nama_lengkap']; ?></h2>
            <?php
            $stmt = $pdo->prepare("SELECT COUNT(*) as pending FROM jurnal_harian WHERE id_siswa = ? AND status = 'pending'");
            $stmt->execute([$siswa['id']]);
            $pending = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
            ?>
            <p class="text-gray-600"><?php echo $pending; ?> Jurnal Perlu Direview</p>
            <a href="siswa_detail.php?id_siswa=<?php echo $siswa['id']; ?>" class="text-blue-500 mt-2 inline-block">Lihat Detail</a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include '../../templates/footer.php'; ?>
