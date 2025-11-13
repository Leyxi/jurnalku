<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $email = sanitize($_POST['email']);
    $role = $_POST['role'];

    if (empty($nama_lengkap) || empty($email) || empty($role)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$nama_lengkap, $email, $role, $id]);
        redirect('users_manage.php?success=User updated');
    }
}
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Edit User</h1>
    <?php if (isset($error)): ?>
        <p class="text-red-500"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="" method="POST" class="bg-white p-6 rounded shadow">
        <div class="mb-4">
            <label for="nama_lengkap" class="block">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo $user['nama_lengkap']; ?>" class="w-full border px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" class="w-full border px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="role" class="block">Role</label>
            <select id="role" name="role" class="w-full border px-3 py-2" required>
                <option value="siswa" <?php if ($user['role'] == 'siswa') echo 'selected'; ?>>Siswa</option>
                <option value="pembimbing" <?php if ($user['role'] == 'pembimbing') echo 'selected'; ?>>Pembimbing</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update User</button>
    </form>
</div>
<?php include '../../templates/footer.php'; ?>
