<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../templates/header.php';
?>

<div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Dashboard Admin</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- User Management -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Manajemen User</h2>
                <p class="text-gray-600 mb-4">Kelola siswa dan pembimbing</p>
                <a href="users_manage.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md inline-block">Kelola User</a>
            </div>

            <!-- Announcement Management -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Pengumuman</h2>
                <p class="text-gray-600 mb-4">Buat dan kelola pengumuman</p>
                <a href="pengumuman_manage.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md inline-block">Kelola Pengumuman</a>
            </div>

            <!-- Reports -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Laporan</h2>
                <p class="text-gray-600 mb-4">Lihat laporan aktivitas</p>
                <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md">Lihat Laporan</button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <?php
            include '../../app/_config/database.php';

            // Count users
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'siswa'");
            $siswa_count = $stmt->fetch()['count'];

            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'pembimbing'");
            $pembimbing_count = $stmt->fetch()['count'];

            $stmt = $pdo->query("SELECT COUNT(*) as count FROM jurnal_harian");
            $jurnal_count = $stmt->fetch()['count'];

            $stmt = $pdo->query("SELECT COUNT(*) as count FROM pengumuman");
            $pengumuman_count = $stmt->fetch()['count'];
            ?>

            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold text-gray-800">Total Siswa</h3>
                <p class="text-2xl font-bold text-blue-600"><?php echo $siswa_count; ?></p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold text-gray-800">Total Pembimbing</h3>
                <p class="text-2xl font-bold text-green-600"><?php echo $pembimbing_count; ?></p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold text-gray-800">Total Jurnal</h3>
                <p class="text-2xl font-bold text-purple-600"><?php echo $jurnal_count; ?></p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold text-gray-800">Total Pengumuman</h3>
                <p class="text-2xl font-bold text-red-600"><?php echo $pengumuman_count; ?></p>
            </div>
        </div>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
