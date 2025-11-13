<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['pembimbing']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$id_pembimbing = $_SESSION['user_id'];
$user_name = $_SESSION['user_nama'] ?? 'Pembimbing';

// Validate id_siswa from GET parameter
$id_siswa = filter_input(INPUT_GET, 'id_siswa', FILTER_VALIDATE_INT);
if (!$id_siswa) {
    redirect('index.php');
}

// --- Security Check: Verify this student is under this mentor's guidance ---
$stmt_check = $pdo->prepare("SELECT COUNT(*) FROM relasi_bimbingan WHERE id_siswa = ? AND id_pembimbing = ?");
$stmt_check->execute([$id_siswa, $id_pembimbing]);
if ($stmt_check->fetchColumn() == 0) {
    redirect('index.php?error=access_denied');
}

// --- Fetch Student Info ---
$stmt_siswa = $pdo->prepare("SELECT nama_lengkap, email FROM users WHERE id = ?");
$stmt_siswa->execute([$id_siswa]);
$siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    redirect('index.php?error=student_not_found');
}

// --- Fetch Jurnals with Filtering ---
$filter_status = sanitize_input($_GET['status'] ?? 'all');
$valid_statuses = ['all', 'pending', 'approved', 'rejected'];
if (!in_array($filter_status, $valid_statuses)) {
    $filter_status = 'all';
}

$sql = "SELECT * FROM jurnal_harian WHERE id_siswa = ?";
$params = [$id_siswa];

if ($filter_status !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $filter_status;
}

$sql .= " ORDER BY tanggal_kegiatan DESC, created_at DESC";

$stmt_jurnals = $pdo->prepare($sql);
$stmt_jurnals->execute($params);
$jurnals = $stmt_jurnals->fetchAll(PDO::FETCH_ASSOC);

include '../../templates/header.php';
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
            <a href="index.php" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-all">
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
        <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-6">
            <h1 class="text-xl font-semibold">Detail Jurnal Siswa</h1>
            <div class="flex items-center gap-4">
                 <div class="font-semibold text-right">
                    <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="text-xs text-gray-500">Pembimbing</div>
                </div>
                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar">
            </div>
        </header>

        <main class="flex-1 p-6 space-y-6">
            <!-- Student Info Header -->
            <div class="bg-white p-5 rounded-xl shadow-md flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800"> <?php echo htmlspecialchars($siswa['nama_lengkap']); ?></h2>
                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($siswa['email']); ?></p>
                </div>
                <a href="index.php" class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition-all flex items-center gap-2">
                     <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>Kembali</span>
                </a>
            </div>

            <!-- Filter Tabs -->
            <div class="bg-white p-2 rounded-xl shadow-md flex items-center justify-start space-x-2">
                 <?php foreach ($valid_statuses as $status): ?>
                    <a href="?id_siswa=<?php echo $id_siswa; ?>&status=<?php echo $status; ?>" 
                       class="px-4 py-2 text-sm font-semibold rounded-lg transition-all <?php echo $filter_status === $status ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'; ?>">
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
                        <p class="mt-1 text-sm text-gray-500">Siswa ini belum mengirimkan jurnal apapun<?php echo ($filter_status !== 'all') ? ' dengan status \'' . ucfirst($filter_status) . '\'' : ''; ?>.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($jurnals as $jurnal): ?>
                        <div class="bg-white rounded-xl shadow-md p-5">
                             <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium"><?php echo date('l, d F Y', strtotime($jurnal['tanggal_kegiatan'])); ?></p>
                                    <p class="mt-2 text-gray-700 text-base"><?php echo nl2br(htmlspecialchars($jurnal['deskripsi_kegiatan'])); ?></p>
                                </div>
                                <div class="flex flex-col items-end flex-shrink-0 ml-4">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                            if ($jurnal['status'] == 'approved') echo 'bg-green-100 text-green-800';
                                            elseif ($jurnal['status'] == 'rejected') echo 'bg-red-100 text-red-800';
                                            else echo 'bg-yellow-100 text-yellow-800'; 
                                        ?>">
                                        <?php echo ucfirst(htmlspecialchars($jurnal['status'])); ?>
                                    </span>
                                     <a href="review_jurnal.php?id_jurnal=<?php echo $jurnal['id']; ?>" class="mt-4 text-sm font-semibold text-indigo-600 hover:text-indigo-800">Review Jurnal</a>
                                </div>
                            </div>
                             <?php if (!empty($jurnal['komentar_pembimbing'])): ?>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <p class="text-sm font-semibold text-gray-700">Komentar Anda:</p>
                                    <p class="text-sm text-gray-600 mt-1 italic">"<?php echo htmlspecialchars($jurnal['komentar_pembimbing']); ?>"</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
