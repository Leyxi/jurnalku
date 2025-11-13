<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../app/_config/database.php';

$stmt = $pdo->query("SELECT * FROM users WHERE role IN ('siswa', 'pembimbing')");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Manage Users</h1>
    <a href="add_user.php" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Tambah User Baru</a>
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Nama Lengkap</th>
                <th class="py-2 px-4 border-b">Email</th>
                <th class="py-2 px-4 border-b">Role</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td class="py-2 px-4 border-b"><?php echo $user['nama_lengkap']; ?></td>
                <td class="py-2 px-4 border-b"><?php echo $user['email']; ?></td>
                <td class="py-2 px-4 border-b"><?php echo $user['role']; ?></td>
                <td class="py-2 px-4 border-b">
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="text-blue-500">Edit</a>
                    <a href="../../app/_logic/delete_user.php?id=<?php echo $user['id']; ?>" class="text-red-500 ml-4" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../../templates/footer.php'; ?>
