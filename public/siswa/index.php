<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['siswa']);
include '../../app/_config/database.php';
include '../../templates/header.php';

$id_siswa = $_SESSION['user_id'];
$user_name = $_SESSION['user_nama'] ?? 'Siswa';

// --- PHP Logic for Stats & Data ---

// Jurnal Stats
$total_jurnal = $pdo->prepare("SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = ?");
$total_jurnal->execute([$id_siswa]);
$total_jurnal_count = $total_jurnal->fetchColumn();

$approved_jurnal = $pdo->prepare("SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = ? AND status = 'approved'");
$approved_jurnal->execute([$id_siswa]);
$approved_jurnal_count = $approved_jurnal->fetchColumn();

$pending_jurnal = $pdo->prepare("SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = ? AND status = 'pending'");
$pending_jurnal->execute([$id_siswa]);
$pending_jurnal_count = $pending_jurnal->fetchColumn();

$rejected_jurnal = $pdo->prepare("SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = ? AND status = 'rejected'");
$rejected_jurnal->execute([$id_siswa]);
$rejected_jurnal_count = $rejected_jurnal->fetchColumn();

// Fetch Jurnals for table
$stmt_jurnals = $pdo->prepare("SELECT * FROM jurnal_harian WHERE id_siswa = ? ORDER BY tanggal_kegiatan DESC, created_at DESC LIMIT 5");
$stmt_jurnals->execute([$id_siswa]);
$jurnals = $stmt_jurnals->fetchAll(PDO::FETCH_ASSOC);

// Fetch Announcements
$stmt_pengumuman = $pdo->prepare("SELECT * FROM pengumuman WHERE target_audien IN ('all', 'siswa') ORDER BY created_at DESC LIMIT 2");
$stmt_pengumuman->execute();
$pengumumans = $stmt_pengumuman->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-20 w-64 bg-white shadow-md flex-col hidden sm:flex">
        <div class="flex h-16 items-center justify-center border-b">
            <a href="#" class="flex items-center gap-2 font-bold text-lg text-gray-800">
                <svg class="h-7 w-7 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span>E-Jurnal</span>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 rounded-lg bg-indigo-100 text-indigo-700 px-3 py-2 font-semibold">
                 <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
             <a href="jurnal_history.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-all">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Jurnal
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
            <h1 class="text-xl font-semibold">Dashboard</h1>
            <div class="flex items-center gap-4">
                 <div class="font-semibold text-right">
                    <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="text-xs text-gray-500">Siswa</div>
                </div>
                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar">
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-6 space-y-6">
             <!-- Welcome & Announcements -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-gradient-to-br from-indigo-600 to-blue-500 text-white p-8 rounded-2xl shadow-lg flex flex-col justify-between">
                    <div>
                        <h2 class="text-3xl font-bold">Selamat Datang, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>!</h2>
                        <p class="mt-2 text-indigo-100 max-w-md">Siap untuk mencatat kegiatan PKL Anda hari ini? Jaga semangat dan terus catat progres Anda.</p>
                    </div>
                    <a href="jurnal_buat.php" class="mt-8 bg-white text-indigo-600 font-bold py-3 px-6 rounded-lg hover:bg-indigo-50 transition-all text-center self-start shadow-md hover:shadow-lg transform hover:-translate-y-1">
                        Buat Jurnal Baru Hari Ini
                    </a>
                </div>
                 <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <h3 class="font-bold text-gray-800 text-xl mb-4">Pengumuman Terbaru</h3>
                    <div class="space-y-4">
                        <?php if (empty($pengumumans)): ?>
                            <div class="text-center py-4">
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.144-6.363a1.76 1.76 0 01.592-2.145l6.364-2.144a1.76 1.76 0 012.144.592z"/></svg>
                                <p class="mt-2 text-gray-500 text-sm">Tidak ada pengumuman untuk Anda saat ini.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($pengumumans as $p): ?>
                                <div class="border-l-4 border-indigo-400 pl-4 py-2">
                                    <h4 class="font-semibold text-gray-700"><?php echo htmlspecialchars($p['judul']); ?></h4>
                                    <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($p['isi']); ?></p>
                                </div>
                            <?php endforeach; ?>
                         <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Jurnal Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                 <div class="bg-white p-5 rounded-2xl shadow-lg flex items-center gap-4">
                    <div class="bg-blue-100 p-3 rounded-full"><svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg></div>
                    <div>
                        <p class="text-sm text-gray-500">Total Jurnal</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $total_jurnal_count; ?></p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-lg flex items-center gap-4">
                    <div class="bg-green-100 p-3 rounded-full"><svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <div>
                        <p class="text-sm text-gray-500">Disetujui</p>
                        <p class="text-2xl font-bold text-green-600"><?php echo $approved_jurnal_count; ?></p>
                    </div>
                </div>
                 <div class="bg-white p-5 rounded-2xl shadow-lg flex items-center gap-4">
                    <div class="bg-yellow-100 p-3 rounded-full"><svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <div>
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600"><?php echo $pending_jurnal_count; ?></p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-lg flex items-center gap-4">
                    <div class="bg-red-100 p-3 rounded-full"><svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg></div>
                    <div>
                        <p class="text-sm text-gray-500">Ditolak</p>
                        <p class="text-2xl font-bold text-red-600"><?php echo $rejected_jurnal_count; ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Jurnals List -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="font-bold text-gray-800 text-xl mb-4">Aktivitas Jurnal Terbaru</h3>
                 <div class="space-y-4">
                    <?php if (empty($jurnals)): ?>
                        <div class="text-center py-8">
                            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m-7 12h-2m2-12a2 2 0 00-2-2H7a2 2 0 00-2 2v4l3 3 3-3V6z" /></svg>
                             <p class="mt-4 text-gray-600">Anda belum memiliki jurnal. Silakan buat jurnal pertama Anda.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($jurnals as $jurnal): ?>
                            <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gray-800"><?php echo date('l, d F Y', strtotime($jurnal['tanggal_kegiatan'])); ?></p>
                                        <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars(substr($jurnal['deskripsi_kegiatan'], 0, 80)); ?>...</p>
                                    </div>
                                    <div class="text-right flex-shrink-0 ml-4">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                            if ($jurnal['status'] == 'approved') echo 'bg-green-100 text-green-800';
                                            elseif ($jurnal['status'] == 'rejected') echo 'bg-red-100 text-red-800';
                                            else echo 'bg-yellow-100 text-yellow-800'; 
                                        ?>">
                                            <?php echo ucfirst(htmlspecialchars($jurnal['status'])); ?>
                                        </span>
                                        <a href="jurnal_detail.php?id=<?php echo $jurnal['id']; ?>" class="mt-2 block text-sm font-semibold text-indigo-600 hover:text-indigo-800">Lihat Detail</a>
                                    </div>
                                </div>
                                <?php if (!empty($jurnal['komentar_pembimbing'])): ?>
                                    <div class="mt-3 bg-gray-50 rounded-lg p-3">
                                        <p class="text-sm font-semibold text-gray-700">Komentar Pembimbing:</p>
                                        <p class="text-sm text-gray-600 mt-1 italic">"<?php echo htmlspecialchars($jurnal['komentar_pembimbing']); ?>"</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <!-- Floating Action Button -->
    <a href="jurnal_buat.php" class="fixed bottom-8 right-8 bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700 transition-transform transform hover:scale-110">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
    </a>
</div>

<?php include '../../templates/footer.php'; ?>
