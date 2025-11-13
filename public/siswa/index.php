<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['siswa']);
include '../../app/_config/database.php';

$id_siswa = $_SESSION['user_id'];

// Fetch Jurnals
$stmt_jurnals = $pdo->prepare("SELECT * FROM jurnal_harian WHERE id_siswa = ? ORDER BY tanggal_kegiatan DESC");
$stmt_jurnals->execute([$id_siswa]);
$jurnals = $stmt_jurnals->fetchAll(PDO::FETCH_ASSOC);

// Fetch Announcements
$stmt_pengumuman = $pdo->prepare("SELECT * FROM pengumuman WHERE target_audien IN ('all', 'siswa') ORDER BY created_at DESC LIMIT 3");
$stmt_pengumuman->execute();
$pengumumans = $stmt_pengumuman->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include '../../templates/header.php'; ?>

<div class="min-h-screen bg-gray-50/50">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-10 hidden w-64 flex-col border-r bg-white sm:flex">
        <div class="flex h-16 items-center border-b px-6">
            <a href="#" class="flex items-center gap-2 font-semibold">
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                <span>E-Jurnal - Siswa</span>
            </a>
        </div>
        <nav class="flex flex-col gap-4 p-4 sm:gap-6 sm:p-6">
            <a href="index.php" class="flex items-center gap-3 rounded-lg bg-gray-100 px-3 py-2 text-gray-900 transition-all hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Dashboard
            </a>
            <a href="jurnal_buat.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-500 transition-all hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                Buat Jurnal
            </a>
            <a href="../logout.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-red-500 transition-all hover:text-red-600 mt-auto">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col sm:gap-4 sm:py-4 sm:pl-72">
        <header class="sticky top-0 z-30 flex h-14 items-center justify-between gap-4 border-b bg-white px-4 sm:static sm:h-auto sm:border-0 sm:bg-transparent sm:px-6">
            <h1 class="text-2xl font-bold">Dashboard Siswa</h1>
             <a href="jurnal_buat.php" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-10 px-4 py-2 bg-blue-600 text-white hover:bg-blue-700">
                Buat Jurnal Baru
            </a>
        </header>

        <main class="grid flex-1 items-start gap-4 p-4 sm:px-6 sm:py-0 md:gap-8">
            <!-- Announcements -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Pengumuman</h2>
                <?php if (empty($pengumumans)): ?>
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6 text-center text-gray-500">
                        <p>Tidak ada pengumuman.</p>
                    </div>
                <?php else: ?>
                    <div class="grid gap-4">
                        <?php foreach ($pengumumans as $p): ?>
                            <div class="rounded-lg border bg-white text-card-foreground shadow-sm p-4">
                                <h3 class="font-semibold text-blue-600"><?php echo htmlspecialchars($p['judul']); ?></h3>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($p['isi']); ?></p>
                                <p class="text-xs text-gray-400 mt-2"> <?php echo date('d M Y', strtotime($p['created_at'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Jurnal Table -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Jurnal Harian Anda</h2>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="relative w-full overflow-auto">
                        <?php if (empty($jurnals)): ?>
                             <div class="p-6 text-center text-gray-500">
                                <p>Anda belum membuat jurnal. Mulai dengan membuat jurnal baru.</p>
                            </div>
                        <?php else: ?>
                            <table class="w-full caption-bottom text-sm">
                                <thead class="[&_tr]:border-b">
                                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0 w-[150px]">Tanggal</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">Deskripsi</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0 w-[120px]">Status</th>
                                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">Komentar</th>
                                        <th class="h-12 px-4 align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0 w-[150px] text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="[&_tr:last-child]:border-0">
                                    <?php foreach ($jurnals as $jurnal): ?>
                                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                            <td class="p-4 align-middle"><?php echo date('d M Y', strtotime($jurnal['tanggal_kegiatan'])); ?></td>
                                            <td class="p-4 align-middle"><?php echo htmlspecialchars(substr($jurnal['deskripsi_kegiatan'], 0, 70)); ?>...</td>
                                            <td class="p-4 align-middle">
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 
                                                    <?php 
                                                        if ($jurnal['status'] == 'approved') echo 'bg-green-100 text-green-800';
                                                        elseif ($jurnal['status'] == 'rejected') echo 'bg-red-100 text-red-800';
                                                        else echo 'bg-yellow-100 text-yellow-800'; 
                                                    ?>">
                                                    <?php echo ucfirst(htmlspecialchars($jurnal['status'])); ?>
                                                </span>
                                            </td>
                                            <td class="p-4 align-middle"><?php echo htmlspecialchars($jurnal['komentar_pembimbing'] ?? '-'); ?></td>
                                            <td class="p-4 align-middle text-right">
                                                <a href="jurnal_edit.php?id=<?php echo $jurnal['id']; ?>" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10 text-blue-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                                </a>
                                                <a href="../../app/_logic/jurnal_delete.php?id=<?php echo $jurnal['id']; ?>" onclick="return confirm('Anda yakin ingin menghapus jurnal ini?')" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10 text-red-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
