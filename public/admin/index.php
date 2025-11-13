<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../app/_config/database.php';
include '../../templates/header.php';

// --- PHP Logic for Stats & Recent Activities ---

// Main Stats
$siswa_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetchColumn();
$pembimbing_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'pembimbing'")->fetchColumn();
$jurnal_pending_count = $pdo->query("SELECT COUNT(*) FROM jurnal_harian WHERE status = 'pending'")->fetchColumn();
$jurnal_approved_count = $pdo->query("SELECT COUNT(*) FROM jurnal_harian WHERE status = 'approved'")->fetchColumn();

// Recent Users
$stmt_users = $pdo->query("SELECT id, nama_lengkap, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$recent_users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// Recent Journals
$stmt_journals = $pdo->query("SELECT j.id, j.tanggal_kegiatan, j.status, u.nama_lengkap as nama_siswa FROM jurnal_harian j JOIN users u ON j.id_siswa = u.id ORDER BY j.created_at DESC LIMIT 5");
$recent_journals = $stmt_journals->fetchAll(PDO::FETCH_ASSOC);

$user_name = $_SESSION['user_nama'] ?? 'Admin';

?>

<div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-20 w-64 bg-white shadow-md flex-col hidden sm:flex">
        <div class="flex h-16 items-center justify-center border-b">
            <a href="#" class="flex items-center gap-2 font-bold text-lg text-gray-800">
                <svg class="h-7 w-7 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>E-Jurnal</span>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 rounded-lg bg-blue-100 text-blue-700 px-3 py-2 font-semibold">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="users_manage.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-all">
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
        <!-- Header -->
        <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-6">
            <div class="flex items-center">
                <h1 class="text-xl font-semibold">Dashboard Admin</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="font-semibold text-right">
                    <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="text-xs text-gray-500">Administrator</div>
                </div>
                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar">
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-6 space-y-6">
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="bg-white p-5 rounded-xl shadow-md flex items-center gap-4">
                    <div class="p-3 bg-blue-100 rounded-full"><svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg></div>
                    <div>
                        <p class="text-sm text-gray-500">Total Siswa</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $siswa_count; ?></p>
                    </div>
                </div>
                 <div class="bg-white p-5 rounded-xl shadow-md flex items-center gap-4">
                    <div class="p-3 bg-green-100 rounded-full"><svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg></div>
                    <div>
                        <p class="text-sm text-gray-500">Total Pembimbing</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $pembimbing_count; ?></p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-md flex items-center gap-4">
                    <div class="p-3 bg-yellow-100 rounded-full"><svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg></div>
                    <div>
                        <p class="text-sm text-gray-500">Jurnal Pending</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $jurnal_pending_count; ?></p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-md flex items-center gap-4">
                    <div class="p-3 bg-indigo-100 rounded-full"><svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                    <div>
                        <p class="text-sm text-gray-500">Jurnal Disetujui</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $jurnal_approved_count; ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Users -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="font-semibold text-gray-800 text-lg mb-4">Pengguna Baru</h3>
                    <div class="space-y-4">
                        <?php foreach($recent_users as $user): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['nama_lengkap']); ?>&background=random&color=fff" alt="Avatar">
                                    <div>
                                        <p class="font-semibold text-gray-700"><?php echo htmlspecialchars($user['nama_lengkap']); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                </div>
                                <span class="text-xs font-medium px-2 py-1 rounded-full <?php echo $user['role'] === 'siswa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recent Journals -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="font-semibold text-gray-800 text-lg mb-4">Aktivitas Jurnal Terbaru</h3>
                    <div class="space-y-3">
                         <?php foreach($recent_journals as $jurnal): ?>
                            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center gap-3">
                                     <span class="p-2 rounded-full <?php 
                                        if ($jurnal['status'] == 'approved') echo 'bg-green-100';
                                        elseif ($jurnal['status'] == 'rejected') echo 'bg-red-100';
                                        else echo 'bg-yellow-100';
                                    ?>">
                                        <svg class="h-5 w-5 <?php 
                                            if ($jurnal['status'] == 'approved') echo 'text-green-600';
                                            elseif ($jurnal['status'] == 'rejected') echo 'text-red-600';
                                            else echo 'text-yellow-600';
                                        ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <?php if($jurnal['status'] == 'approved'): ?> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /> <?php endif; ?>
                                            <?php if($jurnal['status'] == 'rejected'): ?> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /> <?php endif; ?>
                                            <?php if($jurnal['status'] == 'pending'): ?> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /> <?php endif; ?>
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-gray-700 text-sm">Jurnal dari <span class="font-bold"><?php echo htmlspecialchars($jurnal['nama_siswa']); ?></span></p>
                                        <p class="text-xs text-gray-500">Status: <span class="font-medium"><?php echo ucfirst(htmlspecialchars($jurnal['status'])); ?></span> - <?php echo date('d M Y', strtotime($jurnal['tanggal_kegiatan'])); ?></p>
                                    </div>
                                </div>
                                <a href="#" class="text-sm text-blue-600 hover:underline">Lihat</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
