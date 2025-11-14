<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$user_name = $_SESSION['user_nama'] ?? 'Admin';
$error = null;
$success = null;

// Handle POST request for adding a new announcement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_pengumuman'])) {
    $judul = sanitize_input($_POST['judul']);
    $isi = sanitize_input($_POST['isi']);
    $target_audien = sanitize_input($_POST['target_audien']);

    if (empty($judul) || empty($isi) || empty($target_audien)) {
        $error = "Semua field wajib diisi.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO pengumuman (judul, isi, target_audien, created_by) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$judul, $isi, $target_audien, $_SESSION['user_id']])) {
            // Use a query parameter for success message
            redirect('pengumuman_manage.php?success=1');
            exit;
        } else {
            $error = "Gagal menambahkan pengumuman.";
        }
    }
}

// Fetch all announcements
$stmt = $pdo->query("SELECT * FROM pengumuman ORDER BY created_at DESC");
$pengumumans = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../templates/header.php';
?>

<div x-data="{ modalOpen: false }">
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-20 w-64 bg-white shadow-xl border-r border-gray-100 flex-col hidden sm:flex">
            <div class="flex h-16 items-center justify-center border-b border-gray-200">
                <a href="index.php" class="flex items-center gap-2 font-bold text-lg bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    <div class="p-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-white">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span>E-Jurnal</span>
                </a>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="index.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="users_manage.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 006-6v-1a6 6 0 00-9-5.197" /></svg>
                    Users
                </a>
                <a href="manage_relasi.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Relasi Bimbingan
                </a>
                <a href="pengumuman_manage.php" class="flex items-center gap-3 rounded-lg bg-blue-100 text-blue-700 px-3 py-2.5 font-semibold">
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
            <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-6">
                <h1 class="text-xl font-semibold">Manajemen Pengumuman</h1>
                <div class="flex items-center gap-4">
                    <div class="font-semibold text-right">
                        <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                        <div class="text-xs text-gray-500">Administrator</div>
                    </div>
                    <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar">
                </div>
            </header>

            <main class="flex-1 p-6 space-y-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-700">Daftar Pengumuman</h2>
                    <button @click="modalOpen = true" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        <span>Buat Baru</span>
                    </button>
                </div>

                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                        <p>Pengumuman berhasil ditambahkan!</p>
                    </div>
                <?php endif; ?>

                <div class="space-y-4">
                    <?php if (empty($pengumumans)): ?>
                        <div class="text-center py-12 px-6 bg-white rounded-xl shadow-md">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.85H7.923a3.375 3.375 0 00-3.285 2.85L2.37 16.02a4.5 4.5 0 00-.12 1.03v.228c0 .377.068.75.197 1.111l.758 2.273a.75.75 0 00.726.566h15.9c.323 0 .613-.209.726-.566l.758-2.273A2.25 2.25 0 0021.75 17.25zM9 12h6" /></svg>
                             <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum Ada Pengumuman</h3>
                             <p class="mt-1 text-sm text-gray-500">Buat pengumuman pertama Anda dengan mengklik tombol "Buat Baru".</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pengumumans as $p): ?>
                            <div class="bg-white rounded-xl shadow-md p-5">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <span class="font-semibold text-gray-800 text-lg"><?php echo htmlspecialchars($p['judul']); ?></span>
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                                if ($p['target_audien'] == 'siswa') echo 'bg-blue-100 text-blue-800';
                                                elseif ($p['target_audien'] == 'pembimbing') echo 'bg-green-100 text-green-800';
                                                else echo 'bg-gray-100 text-gray-800';
                                            ?>">
                                                <?php echo ucfirst(htmlspecialchars($p['target_audien'])); ?>
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Diterbitkan pada: <?php echo date('d F Y, H:i', strtotime($p['created_at'])); ?></p>
                                    </div>
                                    <div class="flex items-center space-x-1 flex-shrink-0">
                                         <a href="edit_pengumuman.php?id=<?php echo $p['id']; ?>" class="text-gray-500 hover:text-indigo-600 p-2 hover:bg-gray-100 rounded-full transition-all">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z" /></svg>
                                        </a>
                                        <a href="../../app/_logic/delete_pengumuman.php?id=<?php echo $p['id']; ?>" class="text-gray-500 hover:text-red-600 p-2 hover:bg-gray-100 rounded-full transition-all" onclick="return confirm('Anda yakin ingin menghapus pengumuman ini?')">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-4 text-gray-700">
                                    <?php echo nl2br(htmlspecialchars($p['isi'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal for Adding Announcement -->
    <div x-show="modalOpen" @keydown.escape.window="modalOpen = false" class="fixed inset-0 z-30 overflow-y-auto flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="modalOpen = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl p-8 m-4 max-w-lg w-full transform transition-all" @click.stop>
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Buat Pengumuman Baru</h3>
            
            <?php if ($error): // You might need a more sophisticated error handling for modals ?>
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="pengumuman_manage.php" class="space-y-6">
                <input type="hidden" name="add_pengumuman" value="1">
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                    <input type="text" id="judul" name="judul" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="isi" class="block text-sm font-medium text-gray-700 mb-1">Isi Pengumuman</label>
                    <textarea id="isi" name="isi" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required></textarea>
                </div>
                <div>
                    <label for="target_audien" class="block text-sm font-medium text-gray-700 mb-1">Target Audiens</label>
                    <select id="target_audien" name="target_audien" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="" disabled selected>Pilih Target</option>
                        <option value="all">Semua</option>
                        <option value="siswa">Siswa</option>
                        <option value="pembimbing">Pembimbing</option>
                    </select>
                </div>
                <div class="flex items-center justify-end space-x-4 pt-4">
                    <button type="button" @click="modalOpen = false" class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition-all">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
