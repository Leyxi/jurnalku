<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['pembimbing']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$id_pembimbing = $_SESSION['user_id'];
$user_name = $_SESSION['nama_lengkap'] ?? 'Pembimbing';
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

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-20 w-64 bg-white shadow-xl border-r border-gray-100 flex-col hidden sm:flex">
         <div class="flex h-16 items-center justify-center border-b border-gray-200">
            <a href="#" class="flex items-center gap-2 font-bold text-lg bg-gradient-to-r from-green-600 to-teal-600 bg-clip-text text-transparent">
                <div class="p-2 rounded-lg bg-gradient-to-br from-green-600 to-teal-600 text-white">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747h18"/></svg>
                </div>
                <span>E-Jurnal</span>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-2">
             <a href="index.php" class="flex items-center gap-3 rounded-lg bg-gradient-to-r from-green-600 to-teal-600 text-white px-3 py-2.5 font-semibold transition duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="../logout.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-red-600 hover:bg-red-50 mt-auto transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col sm:ml-64">
         <header class="sticky top-0 z-10 flex h-16 items-center justify-between bg-white border-b border-gray-200 px-6 shadow-sm">
            <h1 class="text-xl font-bold text-gray-800">Review Jurnal</h1>
            <div class="flex items-center gap-4">
                <div class="font-semibold text-right">
                    <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="text-xs text-gray-500">Pembimbing</div>
                </div>
                <img class="h-10 w-10 rounded-full object-cover border-2 border-green-600" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=16a34a&color=fff" alt="User Avatar">
            </div>
        </header>

        <main class="flex-1 p-6">
            <form method="POST" action="review_jurnal.php?id_jurnal=<?php echo $id_jurnal; ?>" class="space-y-6">
            <input type="hidden" name="id_jurnal" value="<?php echo $id_jurnal; ?>">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Jurnal Details -->
                <div class="lg:col-span-2 bg-white p-8 rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 space-y-6">
                    <div class="pb-6 border-b border-gray-200">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">👤 Siswa</h3>
                        <p class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($jurnal['nama_lengkap']); ?></p>
                        <p class="text-sm text-gray-600 mt-1 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <?php echo htmlspecialchars($jurnal['email']); ?>
                        </p>
                    </div>
                    
                    <div class="pb-6 border-b border-gray-200">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">📅 Tanggal Kegiatan</h3>
                        <p class="text-lg text-gray-800 font-semibold"><?php echo date('l, d F Y', strtotime($jurnal['tanggal_kegiatan'])); ?></p>
                    </div>
                    
                    <div class="pb-6 border-b border-gray-200">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">📝 Deskripsi Kegiatan</h3>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($jurnal['deskripsi_kegiatan']); ?></p>
                    </div>
                    
                    <div class="pb-6 border-b border-gray-200">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">⚠️ Kendala</h3>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($jurnal['kendala'] ?: 'Tidak ada kendala'); ?></p>
                    </div>
                    
                    <div class="pb-6 border-b border-gray-200">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">✅ Solusi</h3>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($jurnal['solusi'] ?: 'Tidak ada solusi'); ?></p>
                    </div>

                    <?php if (!empty($fotos)): ?>
                        <div>
                             <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">📸 Foto Bukti</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <?php foreach ($fotos as $foto): ?>
                                    <a href="../../public/uploads/jurnal/<?php echo htmlspecialchars($foto['file_path']); ?>" target="_blank" class="group relative overflow-hidden rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                                        <img src="../../public/uploads/jurnal/<?php echo htmlspecialchars($foto['file_path']); ?>" alt="Foto Bukti" class="w-full h-32 object-cover group-hover:scale-110 transition-transform duration-200">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors duration-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Review Form -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <div class="bg-white p-8 rounded-2xl shadow-lg shadow-gray-200/50 border border-gray-100 space-y-6">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Form Ulasan
                            </h3>

                            <?php if ($error): ?>
                                <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-sm text-red-700 font-medium"><?php echo $error; ?></p>
                                </div>
                            <?php endif; ?>

                            <div>
                                <label for="komentar" class="block text-sm font-bold text-gray-700 mb-3">Komentar atau Catatan</label>
                                <textarea id="komentar" name="komentar" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 resize-none" required><?php echo htmlspecialchars($jurnal['komentar_pembimbing'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <span class="block text-sm font-bold text-gray-700 mb-4">Status Persetujuan</span>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="status" value="approved" class="sr-only peer" <?php echo ($jurnal['status'] == 'approved') ? 'checked' : ''; ?> required>
                                        <div class="w-full p-4 text-center rounded-lg border-2 border-gray-300 peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-700 font-bold transition-all duration-200 hover:border-gray-400">
                                            <svg class="w-6 h-6 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Setuju
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="status" value="rejected" class="sr-only peer" <?php echo ($jurnal['status'] == 'rejected') ? 'checked' : ''; ?> required>
                                        <div class="w-full p-4 text-center rounded-lg border-2 border-gray-300 peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-700 font-bold transition-all duration-200 hover:border-gray-400">
                                            <svg class="w-6 h-6 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Tolak
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-3 pt-6 border-t border-gray-200">
                                <a href="siswa_detail.php?id_siswa=<?php echo $jurnal['id_siswa']; ?>" class="flex-1 bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg hover:bg-gray-300 transition-all duration-200 text-center">
                                    Kembali
                                </a>
                                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-teal-600 text-white font-bold py-3 px-4 rounded-lg hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
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
