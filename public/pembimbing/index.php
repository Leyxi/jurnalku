<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['pembimbing']);
include '../../app/_config/database.php';

$id_pembimbing = $_SESSION['user_id'];

// Ambil siswa yang dibimbing
$stmt_siswa = $pdo->prepare("SELECT u.id, u.nama_lengkap FROM users u JOIN relasi_bimbingan r ON u.id = r.id_siswa WHERE r.id_pembimbing = ? ORDER BY u.nama_lengkap");
$stmt_siswa->execute([$id_pembimbing]);
$siswas = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

// Pengumuman untuk pembimbing
$stmt_pengumuman = $pdo->prepare("SELECT * FROM pengumuman WHERE target_audien IN ('all', 'pembimbing') ORDER BY created_at DESC LIMIT 5");
$stmt_pengumuman->execute();
$pengumumans = $stmt_pengumuman->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../../templates/header.php'; ?>

<div class="min-h-screen bg-gray-50/50">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-10 hidden w-64 flex-col border-r bg-white sm:flex">
        <div class="flex h-16 items-center border-b px-6">
            <a href="#" class="flex items-center gap-2 font-semibold">
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M15.2 3a2 2 0 0 1 2.8 2.8l-3.4 3.4L12 12l-4.6 4.6-3.4-3.4a2 2 0 0 1 2.8-2.8L9.8 12l4.4-4.4Z"/><path d="M10.6 10.6 3 18.2a2 2 0 0 0 2.8 2.8l7.6-7.6"/><path d="m18 6 2.8-2.8a2 2 0 0 0-2.8-2.8L16 3.2"/></svg>
                <span>E-Jurnal - Pembimbing</span>
            </a>
        </div>
        <nav class="flex flex-col gap-4 p-4 sm:gap-6 sm:p-6">
            <a href="index.php" class="flex items-center gap-3 rounded-lg bg-gray-100 px-3 py-2 text-gray-900 transition-all hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Dashboard
            </a>
            <a href="../logout.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-red-500 transition-all hover:text-red-600 mt-auto">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col sm:gap-4 sm:py-4 sm:pl-72">
        <header class="sticky top-0 z-30 flex h-14 items-center gap-4 border-b bg-white px-4 sm:static sm:h-auto sm:border-0 sm:bg-transparent sm:px-6">
            <h1 class="text-2xl font-bold">Dashboard Pembimbing</h1>
        </header>

        <main class="grid flex-1 items-start gap-4 p-4 sm:px-6 sm:py-0 md:gap-8">
            <!-- Pengumuman Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Pengumuman Terbaru</h2>
                <?php if (empty($pengumumans)): ?>
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6 text-center text-gray-500">
                        <p>Tidak ada pengumuman saat ini.</p>
                    </div>
                <?php else: ?>
                    <div class="grid gap-4">
                        <?php foreach ($pengumumans as $p): ?>
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-blue-600"><?php echo htmlspecialchars($p['judul']); ?></h3>
                                <p class="mt-2 text-gray-700"><?php echo htmlspecialchars($p['isi']); ?></p>
                                <p class="mt-4 text-xs text-gray-500">Diposting pada: <?php echo date('d M Y', strtotime($p['created_at'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Daftar Siswa Bimbingan -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Siswa Bimbingan</h2>
                 <?php if (empty($siswas)): ?>
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6 text-center text-gray-500">
                        <p>Anda belum memiliki siswa bimbingan.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($siswas as $siswa): ?>
                            <?php
                            // Get pending journal count for each student
                            $stmt_pending = $pdo->prepare("SELECT COUNT(*) as pending FROM jurnal_harian WHERE id_siswa = ? AND status = 'pending'");
                            $stmt_pending->execute([$siswa['id']]);
                            $pending_count = $stmt_pending->fetch(PDO::FETCH_ASSOC)['pending'];
                            ?>
                            <div class="rounded-xl border bg-white text-card-foreground shadow">
                                <div class="p-6 flex flex-col items-center">
                                    <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-center"><?php echo htmlspecialchars($siswa['nama_lengkap']); ?></h3>
                                    <?php if ($pending_count > 0): ?>
                                        <p class="text-red-500 font-bold mt-2"><?php echo $pending_count; ?> Jurnal Perlu Direview</p>
                                    <?php else: ?>
                                        <p class="text-green-500 mt-2">Tidak ada jurnal pending</p>
                                    <?php endif; ?>
                                    <a href="siswa_detail.php?id_siswa=<?php echo $siswa['id']; ?>" class="mt-4 inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 w-full bg-blue-600 text-white hover:bg-blue-700">
                                        Lihat Detail Siswa
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
