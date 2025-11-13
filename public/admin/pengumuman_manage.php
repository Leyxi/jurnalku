<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = sanitize($_POST['judul']);
    $isi = sanitize($_POST['isi']);
    $target_audien = $_POST['target_audien'];

    $stmt = $pdo->prepare("INSERT INTO pengumuman (judul, isi, target_audien, created_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$judul, $isi, $target_audien, $_SESSION['user_id']]);
    redirect('pengumuman_manage.php?success=Pengumuman added');
}

$stmt = $pdo->query("SELECT * FROM pengumuman ORDER BY created_at DESC");
$pengumumans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Manage Pengumuman</h1>
    <form action="" method="POST" class="bg-white p-6 rounded shadow mb-6">
        <div class="mb-4">
            <label for="judul" class="block">Judul</label>
            <input type="text" id="judul" name="judul" class="w-full border px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="isi" class="block">Isi</label>
            <textarea id="isi" name="isi" class="w-full border px-3 py-2" rows="4" required></textarea>
        </div>
        <div class="mb-4">
            <label for="target_audien" class="block">Target Audien</label>
            <select id="target_audien" name="target_audien" class="w-full border px-3 py-2" required>
                <option value="all">Semua</option>
                <option value="siswa">Siswa</option>
                <option value="pembimbing">Pembimbing</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah Pengumuman</button>
    </form>
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Judul</th>
                <th class="py-2 px-4 border-b">Target</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pengumumans as $p): ?>
            <tr>
                <td class="py-2 px-4 border-b"><?php echo $p['judul']; ?></td>
                <td class="py-2 px-4 border-b"><?php echo ucfirst($p['target_audien']); ?></td>
                <td class="py-2 px-4 border-b">
                    <a href="edit_pengumuman.php?id=<?php echo $p['id']; ?>" class="text-blue-500">Edit</a>
                    <a href="../../app/_logic/delete_pengumuman.php?id=<?php echo $p['id']; ?>" class="text-red-500 ml-4" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../../templates/footer.php'; ?>
