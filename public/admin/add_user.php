<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($nama_lengkap) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email already exists";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama_lengkap, $email, $hashed_password, $role]);
            redirect('users_manage.php?success=User added');
        }
    }
}
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Add New User</h1>
    <?php if (isset($error)): ?>
        <p class="text-red-500"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="" method="POST" class="bg-white p-6 rounded shadow">
        <div class="mb-4">
            <label for="nama_lengkap" class="block">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" class="w-full border px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block">Email</label>
            <input type="email" id="email" name="email" class="w-full border px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="password" class="block">Password</label>
            <input type="password" id="password" name="password" class="w-full border px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="role" class="block">Role</label>
            <select id="role" name="role" class="w-full border px-3 py-2" required>
                <option value="siswa">Siswa</option>
                <option value="pembimbing">Pembimbing</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add User</button>
    </form>
</div>
<?php include '../../templates/footer.php'; ?>
