<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM pengumuman WHERE id = ?");
$stmt->execute([$id]);
$pengumuman = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = sanitize($_POST['judul']);
    $isi = sanitize($_POST['isi']);
    $target_audien = $_POST['target_audien'];

    $stmt = $pdo->prepare("UPDATE pengumuman SET judul = ?, isi = ?, target_audien = ? WHERE id = ?");
    $stmt->execute([$judul, $isi, $target_audien, $id]);
    redirect('pengumuman_manage.php?success=Pengumuman updated');
}
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Edit Pengumuman</h1>
    <form action="" method="POST" class="bg-white p-6 rounded shadow">
        <div class="mb-4">
            <label for="judul" class="block">Judul</label>
            <input type="text" id="judul" name="judul" value="<?php echo $pengumuman['judul']; ?>" class="w-full border px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="isi" class="block">Isi</label>
            <textarea id="isi" name="isi" class="w-full border px-3 py-2" rows="4" required><?php echo $pengumuman['isi']; ?></textarea>
        </div>
        <div class="mb-4">
            <label for="target_audien" class="block">Target Audien</label>
            <select id="target_audien" name="target_audien" class="w-full border px-3 py-2" required>
                <option value="all" <?php if ($pengumuman['target_audien'] == 'all') echo 'selected'; ?>>Semua</option>
                <option value="siswa" <?php if ($pengumuman['target_audien'] == 'siswa') echo 'selected'; ?>>Siswa</option>
                <option value="pembimbing" <?php if ($pengumuman['target_audien'] == 'pembimbing') echo 'selected'; ?>>Pembimbing</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Pengumuman</button>
    </form>
</div>
<?php include '../../templates/footer.php'; ?>
