<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['siswa']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$id_siswa = $_SESSION['user_id'];
$user_name = $_SESSION['nama_lengkap'] ?? 'Siswa';

// --- Fetch Jurnals with Filtering and Pagination ---
$filter_status = sanitize_input($_GET['status'] ?? 'all');
$valid_statuses = ['all', 'pending', 'approved', 'rejected'];
if (!in_array($filter_status, $valid_statuses)) {
    $filter_status = 'all';
}

// Pagination
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$per_page = 10; 
$offset = ($page - 1) * $per_page;

// Base SQL and Params
$sql_count = "SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = ?";
$sql_data = "SELECT * FROM jurnal_harian WHERE id_siswa = ?";
$params = [$id_siswa];

// Apply filter
if ($filter_status !== 'all') {
    $sql_count .= " AND status = ?";
    $sql_data .= " AND status = ?";
    $params[] = $filter_status;
}

// Get total count for pagination
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_records = $stmt_count->fetchColumn();
$total_pages = ceil($total_records / $per_page);

// Get data for current page
$sql_data .= " ORDER BY tanggal_kegiatan DESC, created_at DESC LIMIT ? OFFSET ?";

$stmt_jurnals = $pdo->prepare($sql_data);
$stmt_jurnals->bindValue(1, $id_siswa, PDO::PARAM_INT);
if ($filter_status !== 'all') {
    $stmt_jurnals->bindValue(2, $filter_status, PDO::PARAM_STR);
    $stmt_jurnals->bindValue(3, $per_page, PDO::PARAM_INT);
    $stmt_jurnals->bindValue(4, $offset, PDO::PARAM_INT);
} else {
    $stmt_jurnals->bindValue(2, $per_page, PDO::PARAM_INT);
    $stmt_jurnals->bindValue(3, $offset, PDO::PARAM_INT);
}
$stmt_jurnals->execute();
$jurnals = $stmt_jurnals->fetchAll(PDO::FETCH_ASSOC);

include '../../templates/header.php';
?>

<div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-20 w-64 bg-white shadow-md flex-col hidden sm:flex">
         <div class="flex h-16 items-center justify-center border-b">
            <a href="index.php" class="flex items-center gap-2 font-bold text-lg text-gray-800">
                <svg class="h-7 w-7 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span>E-Jurnal</span>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-all">
                 <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="jurnal_history.php" class="flex items-center gap-3 rounded-lg bg-indigo-100 text-indigo-700 px-3 py-2 font-semibold">
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
         <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-6">
            <h1 class="text-xl font-semibold">Riwayat Jurnal</h1>
            <div class="flex items-center gap-4">
                <div class="font-semibold text-right">
                    <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="text-xs text-gray-500">Siswa</div>
                </div>
                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar">
            </div>
        </header>

        <main class="flex-1 p-6 space-y-6">
            <!-- Filter Tabs -->
            <div class="bg-white p-2 rounded-xl shadow-md flex items-center justify-start space-x-1 sm:space-x-2">
                 <?php foreach ($valid_statuses as $status): ?>
                    <a href="?status=<?php echo $status; ?>" 
                       class="px-3 sm:px-4 py-2 text-sm font-semibold rounded-lg transition-all text-center <?php echo $filter_status === $status ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        <?php echo ucfirst($status); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Jurnal List -->
            <div class="space-y-4">
                <?php if (empty($jurnals)): ?>
                    <div class="text-center py-12 px-6 bg-white rounded-xl shadow-md">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum Ada Jurnal</h3>
                        <p class="mt-1 text-sm text-gray-500">Tidak ada jurnal yang cocok dengan filter '<?php echo ucfirst($filter_status); ?>'.</p>
                         <a href="jurnal_buat.php" class="mt-4 inline-block bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 transition-all">
                            Buat Jurnal Baru
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($jurnals as $jurnal): ?>
                         <div class="bg-white rounded-xl shadow-md p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800"><?php echo date('l, d F Y', strtotime($jurnal['tanggal_kegiatan'])); ?></p>
                                    <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars(substr($jurnal['deskripsi_kegiatan'], 0, 150)); ?><?php echo strlen($jurnal['deskripsi_kegiatan']) > 150 ? '...' : ''; ?></p>
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

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="mt-6 flex justify-center items-center space-x-2">
                    <!-- Previous Page -->
                    <a href="?status=<?php echo $filter_status; ?>&page=<?php echo max(1, $page - 1); ?>" 
                       class="flex items-center justify-center h-10 w-10 rounded-full <?php echo $page > 1 ? 'bg-white shadow-md hover:bg-gray-100' : 'bg-gray-100 text-gray-400 cursor-not-allowed'; ?>">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </a>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?status=<?php echo $filter_status; ?>&page=<?php echo $i; ?>" 
                           class="flex items-center justify-center h-10 w-10 rounded-full <?php echo $i == $page ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white shadow-md hover:bg-gray-100'; ?>">
                           <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Next Page -->
                    <a href="?status=<?php echo $filter_status; ?>&page=<?php echo min($total_pages, $page + 1); ?>" 
                       class="flex items-center justify-center h-10 w-10 rounded-full <?php echo $page < $total_pages ? 'bg-white shadow-md hover:bg-gray-100' : 'bg-gray-100 text-gray-400 cursor-not-allowed'; ?>">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
