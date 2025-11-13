<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../templates/header.php';

// --- PHP Logic for Stats ---
include '../../app/_config/database.php';

// Count users
$stmt_siswa = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'siswa'");
$siswa_count = $stmt_siswa->fetch()['count'];

$stmt_pembimbing = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'pembimbing'");
$pembimbing_count = $stmt_pembimbing->fetch()['count'];

$stmt_jurnal = $pdo->query("SELECT COUNT(*) as count FROM jurnal_harian");
$jurnal_count = $stmt_jurnal->fetch()['count'];

$stmt_pengumuman = $pdo->query("SELECT COUNT(*) as count FROM pengumuman");
$pengumuman_count = $stmt_pengumuman->fetch()['count'];
?>

<div class="min-h-screen bg-gray-50/50">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-10 hidden w-64 flex-col border-r bg-white sm:flex">
        <div class="flex h-16 items-center border-b px-6">
            <a href="#" class="flex items-center gap-2 font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9-1.306-3.046a2 2 0 0 1 1.042-2.654l7-3.333a2 2 0 0 1 1.928 0l7 3.333a2 2 0 0 1 1.042 2.654L21 9"/><path d="M12 19v-4"/></svg>
                <span>E-Jurnal - Admin</span>
            </a>
        </div>
        <nav class="flex flex-col gap-4 p-4 sm:gap-6 sm:p-6">
            <a href="index.php" class="flex items-center gap-3 rounded-lg bg-gray-100 px-3 py-2 text-gray-900 transition-all hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Dashboard
            </a>
            <a href="users_manage.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-500 transition-all hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Manajemen User
            </a>
            <a href="pengumuman_manage.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-500 transition-all hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M7 7h10v10"/><path d="M17 17 7 7"/></svg>
                Pengumuman
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
            <h1 class="text-2xl font-bold">Dashboard Admin</h1>
        </header>

        <main class="grid flex-1 items-start gap-4 p-4 sm:px-6 sm:py-0 md:gap-8">
            <!-- Quick Stats -->
            <div class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                    <div class="p-6 flex flex-row items-center justify-between pb-2">
                        <h3 class="text-sm font-medium tracking-tight">Total Siswa</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-gray-500"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold"><?php echo $siswa_count; ?></div>
                    </div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                    <div class="p-6 flex flex-row items-center justify-between pb-2">
                        <h3 class="text-sm font-medium tracking-tight">Total Pembimbing</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-gray-500"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold"><?php echo $pembimbing_count; ?></div>
                    </div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                    <div class="p-6 flex flex-row items-center justify-between pb-2">
                        <h3 class="text-sm font-medium tracking-tight">Total Jurnal</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-gray-500"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold"><?php echo $jurnal_count; ?></div>
                    </div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                    <div class="p-6 flex flex-row items-center justify-between pb-2">
                        <h3 class="text-sm font-medium tracking-tight">Total Pengumuman</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-gray-500"><path d="M7 7h10v10"/><path d="M17 17 7 7"/></svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold"><?php echo $pengumuman_count; ?></div>
                    </div>
                </div>
            </div>

            <!-- Main Actions -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Manajemen User</h3>
                        <p class="text-gray-600 mb-4">Kelola data siswa dan pembimbing, tambah user baru, atau hapus user.</p>
                        <a href="users_manage.php" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full bg-blue-600 text-white hover:bg-blue-700">
                            Kelola User
                        </a>
                    </div>
                </div>
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Manajemen Pengumuman</h3>
                        <p class="text-gray-600 mb-4">Buat pengumuman baru untuk semua pengguna, edit atau hapus pengumuman.</p>
                         <a href="pengumuman_manage.php" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full bg-green-600 text-white hover:bg-green-700">
                            Kelola Pengumuman
                        </a>
                    </div>
                </div>
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Laporan</h3>
                        <p class="text-gray-600 mb-4">Lihat laporan aktivitas, jurnal siswa, dan data statistik lainnya.</p>
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full bg-yellow-500 text-white hover:bg-yellow-600">
                            Lihat Laporan
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
