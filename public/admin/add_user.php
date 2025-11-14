<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

// Fetch pembimbing list for assignment
$stmt_pemb = $pdo->query("SELECT id, nama_lengkap FROM users WHERE role = 'pembimbing' ORDER BY nama_lengkap ASC");
$pembimbings = $stmt_pemb->fetchAll(PDO::FETCH_ASSOC);

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // verify csrf
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = "Invalid CSRF token.";
    } else {
        $nama_lengkap = sanitize_input($_POST['nama_lengkap']);
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize_input($_POST['role']);

        if (empty($nama_lengkap) || empty($email) || empty($password) || empty($role)) {
            $error = "Semua field wajib diisi.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Format email tidak valid.";
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Email sudah terdaftar. Silakan gunakan email lain.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$nama_lengkap, $email, $hashed_password, $role])) {
                    $new_user_id = $pdo->lastInsertId();
                    // If role is siswa and pembimbing was selected, insert relasi(s)
                    if ($role === 'siswa' && isset($_POST['pembimbing_id']) && !empty($_POST['pembimbing_id'])) {
                        $pemb_ids = $_POST['pembimbing_id'];
                        if (!is_array($pemb_ids)) $pemb_ids = [$pemb_ids];
                        // Remove any existing just in case (defensive)
                        $del = $pdo->prepare("DELETE FROM relasi_bimbingan WHERE id_siswa = ?");
                        $del->execute([$new_user_id]);
                        $ins = $pdo->prepare("INSERT IGNORE INTO relasi_bimbingan (id_pembimbing, id_siswa) VALUES (?, ?)");
                        foreach ($pemb_ids as $pemb_id) {
                            $p = (int) $pemb_id;
                            if ($p <= 0) continue;
                            $ins->execute([$p, $new_user_id]);
                            if ($ins->rowCount()) audit_relasi($pdo, 'add', $_SESSION['user_id'], "p:$p,s:$new_user_id");
                        }
                    }
                    // Redirect with success message
                    redirect('users_manage.php?success=1');
                    exit;
                } else {
                    $error = "Terjadi kesalahan saat menambahkan pengguna.";
                }
            }
        }
    }
}

include '../../templates/header.php';
$user_name = $_SESSION['user_nama'] ?? 'Admin';
$csrf = generate_csrf_token();
?>

<div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-20 w-64 bg-white shadow-xl border-r border-gray-100 flex-col hidden sm:flex">
        <div class="flex h-16 items-center justify-center border-b border-gray-200">
            <a href="index.php" class="flex items-center gap-2 font-bold text-lg bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                <div class="p-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-white">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span>E-Jurnal</span>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="users_manage.php" class="flex items-center gap-3 rounded-lg bg-blue-100 text-blue-700 px-3 py-2.5 font-semibold">
                 <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 006-6v-1a6 6 0 00-9-5.197" /></svg>
                Users
            </a>
            <a href="manage_relasi.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Relasi Bimbingan
            </a>
            <a href="pengumuman_manage.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.144-6.363a1.76 1.76 0 01.592-2.145l6.364-2.144a1.76 1.76 0 012.144.592z" /></svg>
                Pengumuman
            </a>
            <a href="../logout.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-red-600 hover:bg-red-50 mt-auto transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col sm:ml-64">
        <!-- Header -->
        <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-6">
            <h1 class="text-xl font-semibold">Tambah Pengguna Baru</h1>
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
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Formulir Pengguna</h2>

                <?php if ($error): ?>
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                        <p class="font-bold">Error</p>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>

                <form action="add_user.php" method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="" disabled selected>Pilih Role</option>
                            <option value="siswa">Siswa</option>
                            <option value="pembimbing">Pembimbing</option>
                        </select>
                    </div>

                    <!-- Pembimbing assignment (visible when role == siswa) - Multi-select -->
                    <div id="pembimbing-select" class="mt-4 hidden">
                        <label for="pembimbing_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Pembimbing (pilih satu atau lebih)</label>
                        <select id="pembimbing_id" name="pembimbing_id[]" multiple size="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <?php foreach ($pembimbings as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nama_lengkap']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Tekan Ctrl/Cmd untuk memilih beberapa pembimbing.</p>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <a href="users_manage.php" class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition-all">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Simpan Pengguna</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
    // Show pembimbing select only when role == siswa
    document.addEventListener('DOMContentLoaded', function(){
        var roleEl = document.getElementById('role');
        var pembWrap = document.getElementById('pembimbing-select');
        function togglePemb() {
            if (!roleEl) return;
            if (roleEl.value === 'siswa') pembWrap.classList.remove('hidden');
            else pembWrap.classList.add('hidden');
        }
        if (roleEl) {
            roleEl.addEventListener('change', togglePemb);
            togglePemb();
        }
    });
</script>

<?php include '../../templates/footer.php'; ?>
