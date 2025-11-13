<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['pembimbing']);
include '../../app/_config/database.php';
include '../../templates/header.php';

$id_pembimbing = $_SESSION['user_id'];
$user_name = $_SESSION['user_nama'] ?? 'Pembimbing';

// --- PHP Logic for Stats & Student List ---

// Total Siswa Bimbingan
$stmt_total_siswa = $pdo->prepare("SELECT COUNT(*) FROM relasi_bimbingan WHERE id_pembimbing = ?");
$stmt_total_siswa->execute([$id_pembimbing]);
$total_siswa_count = $stmt_total_siswa->fetchColumn();

// Jurnal Perlu Direview
$stmt_pending = $pdo->prepare("SELECT COUNT(*) FROM jurnal_harian j JOIN relasi_bimbingan r ON j.id_siswa = r.id_siswa WHERE r.id_pembimbing = ? AND j.status = 'pending'");
$stmt_pending->execute([$id_pembimbing]);
$pending_jurnal_count = $stmt_pending->fetchColumn();

// Ambil daftar siswa yang dibimbing beserta jumlah jurnal mereka
$stmt_siswas = $pdo->prepare("
    SELECT u.id, u.nama_lengkap, u.email,
           (SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = u.id) as total_jurnal,
           (SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = u.id AND status = 'pending') as pending_jurnal
    FROM users u 
    JOIN relasi_bimbingan r ON u.id = r.id_siswa 
    WHERE r.id_pembimbing = ?
    ORDER BY u.nama_lengkap
");
$stmt_siswas->execute([$id_pembimbing]);
$siswas = $stmt_siswas->fetchAll(PDO::FETCH_ASSOC);

// Ambil pengumuman terbaru
$stmt_pengumuman = $pdo->prepare("SELECT * FROM pengumuman WHERE target_audien IN ('all', 'pembimbing') ORDER BY created_at DESC LIMIT 2");
$stmt_pengumuman->execute();
$pengumumans = $stmt_pengumuman->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-20 w-64 bg-white shadow-md flex-col hidden sm:flex">
        <div class="flex h-16 items-center justify-center border-b">
            <a href="#" class="flex items-center gap-2 font-bold text-lg text-gray-800">
                <svg class="h-7 w-7 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747h18"/></svg>
                <span>E-Jurnal</span>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 rounded-lg bg-green-100 text-green-700 px-3 py-2 font-semibold">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="../logout.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-red-600 hover:bg-red-100 mt-auto transition-all">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col sm:ml-64">
        <!-- Header -->
        <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-6">
            <h1 class="text-xl font-semibold">Dashboard Pembimbing</h1>
            <div class="flex items-center gap-4">
                 <div class="font-semibold text-right">
                    <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="text-xs text-gray-500">Pembimbing</div>
                </div>
                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar">
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-6 space-y-6">
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-5 rounded-xl shadow-md flex items-center gap-4">
                    <div class="p-3 bg-blue-100 rounded-full"><svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg></div>
                    <div>
                        <p class="text-sm text-gray-500">Siswa Bimbingan</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $total_siswa_count; ?></p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-md flex items-center gap-4">
                    <div class="p-3 bg-yellow-100 rounded-full"><svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg></div>
                    <div>
                        <p class="text-sm text-gray-500">Jurnal Perlu Direview</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $pending_jurnal_count; ?></p>
                    </div>
                </div>
            </div>

            <!-- Announcements -->
            <div class="space-y-4">
                 <h3 class="font-semibold text-gray-800 text-lg">Pengumuman</h3>
                <?php if (empty($pengumumans)): ?>
                    <p class="text-gray-500 text-sm">Tidak ada pengumuman baru.</p>
                <?php else: ?>
                    <?php foreach ($pengumumans as $p): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                            <h4 class="font-bold text-blue-800"><?php echo htmlspecialchars($p['judul']); ?></h4>
                            <p class="text-sm text-blue-700 mt-1"><?php echo htmlspecialchars($p['isi']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Student List -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="font-semibold text-gray-800 text-lg mb-4">Daftar Siswa Bimbingan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jurnal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurnal Pending</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($siswas)): ?>
                                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Anda tidak memiliki siswa bimbingan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($siswas as $siswa): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($siswa['nama_lengkap']); ?>&background=random&color=fff" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($siswa['nama_lengkap']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($siswa['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500"><?php echo $siswa['total_jurnal']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $siswa['pending_jurnal'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'; ?>">
                                                <?php echo $siswa['pending_jurnal']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="siswa_detail.php?id_siswa=<?php echo $siswa['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
