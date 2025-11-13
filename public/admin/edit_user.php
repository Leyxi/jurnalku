<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$error = null;
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    redirect('users_manage.php');
}

// Fetch user data
$stmt_user = $pdo->prepare("SELECT id, nama_lengkap, email, role FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    redirect('users_manage.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = sanitize_input($_POST['nama_lengkap']);
    $email = sanitize_input($_POST['email']);
    $role = sanitize_input($_POST['role']);
    $password = $_POST['password'];

    if (empty($nama_lengkap) || empty($email) || empty($role)) {
        $error = "Nama lengkap, email, dan role wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        // Check if email already exists for another user
        $stmt_email_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
        $stmt_email_check->execute([$email, $user_id]);
        if ($stmt_email_check->fetchColumn() > 0) {
            $error = "Email sudah terdaftar untuk pengguna lain.";
        } else {
            // Prepare the update query
            if (!empty($password)) {
                // If password is being changed
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET nama_lengkap = ?, email = ?, role = ?, password = ? WHERE id = ?";
                $params = [$nama_lengkap, $email, $role, $hashed_password, $user_id];
            } else {
                // If password is not being changed
                $sql = "UPDATE users SET nama_lengkap = ?, email = ?, role = ? WHERE id = ?";
                $params = [$nama_lengkap, $email, $role, $user_id];
            }
            
            $stmt_update = $pdo->prepare($sql);
            if ($stmt_update->execute($params)) {
                redirect('users_manage.php?success=2');
                exit;
            } else {
                $error = "Terjadi kesalahan saat memperbarui pengguna.";
            }
        }
    }
    // Re-fetch user data to show posted values on error
    $user['nama_lengkap'] = $nama_lengkap;
    $user['email'] = $email;
    $user['role'] = $role;
}

include '../../templates/header.php';
$user_name = $_SESSION['user_nama'] ?? 'Admin';
?>

<div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-20 w-64 bg-white shadow-md flex-col hidden sm:flex">
         <div class="flex h-16 items-center justify-center border-b">
            <a href="index.php" class="flex items-center gap-2 font-bold text-lg text-gray-800">
                <svg class="h-7 w-7 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>E-Jurnal</span>
            </a>
        </div>
       <nav class="flex-1 p-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-all">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="users_manage.php" class="flex items-center gap-3 rounded-lg bg-blue-100 text-blue-700 px-3 py-2 font-semibold">
                 <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 006-6v-1a6 6 0 00-9-5.197" /></svg>
                Users
            </a>
            <a href="pengumuman_manage.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-all">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.144-6.363a1.76 1.76 0 01.592-2.145l6.364-2.144a1.76 1.76 0 012.144.592z" /></svg>
                Pengumuman
            </a>
            <a href="../logout.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-red-600 hover:bg-red-100 mt-auto transition-all">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col sm:ml-64">
         <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-6">
            <h1 class="text-xl font-semibold">Edit Pengguna</h1>
            <div class="flex items-center gap-4">
                <div class="font-semibold text-right">
                     <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="text-xs text-gray-500">Administrator</div>
                </div>
                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar">
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-6">
            <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-md">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Data Pengguna</h2>

                <?php if ($error): ?>
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                        <p class="font-bold">Error</p>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="edit_user.php?id=<?php echo $user['id']; ?>" class="space-y-6">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                     <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="siswa" <?php echo ($user['role'] === 'siswa') ? 'selected' : ''; ?>>Siswa</option>
                            <option value="pembimbing" <?php echo ($user['role'] === 'pembimbing') ? 'selected' : ''; ?>>Pembimbing</option>
                        </select>
                    </div>

                    <div class="border-t pt-6 mt-6">
                         <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Ubah Password (Opsional)</label>
                         <input type="password" id="password" name="password" placeholder="Masukkan password baru jika ingin diubah" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <a href="users_manage.php" class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition-all">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
