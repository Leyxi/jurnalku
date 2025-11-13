<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['pembimbing']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$id_pembimbing = $_SESSION['user_id'];
$user_name = $_SESSION['user_nama'] ?? 'Pembimbing';
$error = null;

// --- Parameter and Security Validation ---
$id_jurnal = filter_input(INPUT_GET, 'id_jurnal', FILTER_VALIDATE_INT);
if (!$id_jurnal) {
    redirect('index.php');
}

// --- Fetch Jurnal and Student Info ---
$stmt_jurnal = $pdo->prepare("SELECT j.*, u.nama_lengkap, u.email FROM jurnal_harian j JOIN users u ON j.id_siswa = u.id WHERE j.id = ?");
$stmt_jurnal->execute([$id_jurnal]);
$jurnal = $stmt_jurnal->fetch(PDO::FETCH_ASSOC);

if (!$jurnal) {
    redirect('index.php?error=jurnal_not_found');
}

// Security Check: Ensure the student is guided by this mentor
$stmt_check = $pdo->prepare("SELECT COUNT(*) FROM relasi_bimbingan WHERE id_siswa = ? AND id_pembimbing = ?");
$stmt_check->execute([$jurnal['id_siswa'], $id_pembimbing]);
if ($stmt_check->fetchColumn() == 0) {
    redirect('index.php?error=access_denied');
}

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $komentar = sanitize_input($_POST['komentar']);
    $status = sanitize_input($_POST['status']);
    $id_jurnal_post = filter_input(INPUT_POST, 'id_jurnal', FILTER_VALIDATE_INT);

    if (empty($komentar) || empty($status) || !$id_jurnal_post) {
        $error = "Komentar dan status wajib diisi.";
    } elseif ($id_jurnal_post != $id_jurnal) {
        $error = "Invalid request.";
    } elseif (!in_array($status, ['approved', 'rejected'])) {
        $error = "Status tidak valid.";
    } else {
        $sql = "UPDATE jurnal_harian SET status = ?, komentar_pembimbing = ?, tanggal_review = NOW() WHERE id = ?";
        $stmt_update = $pdo->prepare($sql);
        if ($stmt_update->execute([$status, $komentar, $id_jurnal])) {
            redirect("siswa_detail.php?id_siswa={$jurnal['id_siswa']}&success=review_submitted");
            exit;
        } else {
            $error = "Gagal menyimpan ulasan.";
        }
    }
}

// --- Fetch Photos ---
$stmt_fotos = $pdo->prepare("SELECT * FROM jurnal_foto WHERE id_jurnal = ?");
$stmt_fotos->execute([$id_jurnal]);
$fotos = $stmt_fotos->fetchAll(PDO::FETCH_ASSOC);

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
         <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-6">
            <h1 class="text-xl font-semibold">Review Jurnal</h1>
            <div class="flex items-center gap-4">
                <div class="font-semibold text-right">
                    <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="text-xs text-gray-500">Pembimbing</div>
                </div>
                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar">
            </div>
        </header>

        <main class="flex-1 p-6">
            <form method="POST" action="review_jurnal.php?id_jurnal=<?php echo $id_jurnal; ?>">
            <input type="hidden" name="id_jurnal" value="<?php echo $id_jurnal; ?>">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Jurnal Details -->
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md space-y-5">
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase">Siswa</h3>
                        <p class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($jurnal['nama_lengkap']); ?></p>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($jurnal['email']); ?></p>
                    </div>
                    <hr>
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase">Tanggal Kegiatan</h3>
                        <p class="text-lg text-gray-800"><?php echo date('l, d F Y', strtotime($jurnal['tanggal_kegiatan'])); ?></p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase">Deskripsi Kegiatan</h3>
                        <p class="text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($jurnal['deskripsi_kegiatan']); ?></p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase">Kendala</h3>
                        <p class="text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($jurnal['kendala'] ?: '-'); ?></p>
                    </div>
                     <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase">Solusi</h3>
                        <p class="text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($jurnal['solusi'] ?: '-'); ?></p>
                    </div>

                    <?php if (!empty($fotos)): ?>
                        <div>
                             <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">Foto Bukti</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <?php foreach ($fotos as $foto): ?>
                                    <a href="../../public/uploads/jurnal/<?php echo htmlspecialchars($foto['file_path']); ?>" target="_blank">
                                        <img src="../../public/uploads/jurnal/<?php echo htmlspecialchars($foto['file_path']); ?>" alt="Foto Bukti" class="w-full h-32 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Review Form -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <div class="bg-white p-6 rounded-xl shadow-md space-y-6">
                            <h3 class="text-lg font-bold text-gray-800">Form Ulasan</h3>

                            <?php if ($error): ?>
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md text-sm">
                                    <p><?php echo $error; ?></p>
                                </div>
                            <?php endif; ?>

                            <div>
                                <label for="komentar" class="block text-sm font-medium text-gray-700 mb-1">Komentar atau Catatan</label>
                                <textarea id="komentar" name="komentar" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required><?php echo htmlspecialchars($jurnal['komentar_pembimbing'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-700 mb-2">Persetujuan Jurnal</span>
                                <div class="flex space-x-2">
                                    <label class="flex-1">
                                        <input type="radio" name="status" value="approved" class="sr-only peer" <?php echo ($jurnal['status'] == 'approved') ? 'checked' : ''; ?>>
                                        <div class="w-full p-3 text-center rounded-lg border-2 border-gray-200 cursor-pointer peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-700 font-semibold hover:bg-gray-50">
                                            Setuju
                                        </div>
                                    </label>
                                    <label class="flex-1">
                                        <input type="radio" name="status" value="rejected" class="sr-only peer" <?php echo ($jurnal['status'] == 'rejected') ? 'checked' : ''; ?>>
                                        <div class="w-full p-3 text-center rounded-lg border-2 border-gray-200 cursor-pointer peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-700 font-semibold hover:bg-gray-50">
                                            Tolak
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                                <a href="siswa_detail.php?id_siswa=<?php echo $jurnal['id_siswa']; ?>" class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition-all">Kembali</a>
                                <button type="submit" class="bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition-all flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span>Kirim Ulasan</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
