<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['siswa']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$user_name = $_SESSION['user_nama'] ?? 'Siswa';
$id_siswa = $_SESSION['user_id'];
$error = null;

// --- Parameter Validation ---
$id_jurnal = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_jurnal) {
    redirect('jurnal_history.php');
}

// --- Fetch Jurnal and verify ownership & status ---
$stmt = $pdo->prepare("SELECT * FROM jurnal_harian WHERE id = ? AND id_siswa = ?");
$stmt->execute([$id_jurnal, $id_siswa]);
$jurnal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jurnal) {
    redirect('jurnal_history.php?error=not_found');
}

// SECURITY: Allow edit only if status is pending or rejected
if (!in_array($jurnal['status'], ['pending', 'rejected'])) {
    redirect("jurnal_detail.php?id={$id_jurnal}&error=edit_not_allowed");
}

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_kegiatan = sanitize_input($_POST['tanggal_kegiatan']);
    $deskripsi_kegiatan = sanitize_input($_POST['deskripsi_kegiatan']);
    $kendala = sanitize_input($_POST['kendala']);
    $solusi = sanitize_input($_POST['solusi']);

    if (empty($tanggal_kegiatan) || empty($deskripsi_kegiatan)) {
        $error = "Tanggal dan Deskripsi Kegiatan wajib diisi.";
    } else {
        $pdo->beginTransaction();
        try {
            // Update jurnal, reset status to pending
            $sql_update = "UPDATE jurnal_harian SET tanggal_kegiatan = ?, deskripsi_kegiatan = ?, kendala = ?, solusi = ?, status = 'pending', komentar_pembimbing = NULL, tanggal_review = NULL WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$tanggal_kegiatan, $deskripsi_kegiatan, $kendala, $solusi, $id_jurnal]);

            // --- Handle Photo Deletion ---
            if (isset($_POST['delete_foto'])) {
                $fotos_to_delete = $_POST['delete_foto']; // Array of photo IDs
                $stmt_delete_select = $pdo->prepare("SELECT file_path FROM jurnal_foto WHERE id = ? AND id_jurnal = ?");
                $stmt_delete_exec = $pdo->prepare("DELETE FROM jurnal_foto WHERE id = ?");

                foreach ($fotos_to_delete as $foto_id) {
                    $stmt_delete_select->execute([$foto_id, $id_jurnal]);
                    $file = $stmt_delete_select->fetch(PDO::FETCH_ASSOC);
                    if ($file) {
                        // Delete file from server
                        $file_path_on_disk = '../uploads/jurnal/' . $file['file_path'];
                        if (file_exists($file_path_on_disk)) {
                            unlink($file_path_on_disk);
                        }
                        // Delete record from DB
                        $stmt_delete_exec->execute([$foto_id]);
                    }
                }
            }

            // --- Handle New Photo Uploads ---
            $upload_dir = '../uploads/jurnal/';
             if (isset($_FILES['foto_bukti']) && count($_FILES['foto_bukti']['name']) > 0) {
                for ($i = 0; $i < count($_FILES['foto_bukti']['name']); $i++) {
                    if ($_FILES['foto_bukti']['error'][$i] === UPLOAD_ERR_OK) {
                        $file_tmp = $_FILES['foto_bukti']['tmp_name'][$i];
                        $file_name = uniqid('jurnal_' . $id_jurnal . '_') . '-' . basename($_FILES['foto_bukti']['name'][$i]);
                        $file_path = $upload_dir . $file_name;
                        if (move_uploaded_file($file_tmp, $file_path)) {
                            $stmt_foto = $pdo->prepare("INSERT INTO jurnal_foto (id_jurnal, file_path) VALUES (?, ?)");
                            $stmt_foto->execute([$id_jurnal, $file_name]);
                        }
                    }
                }
            }

            $pdo->commit();
            redirect("jurnal_detail.php?id={$id_jurnal}&success=updated");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Gagal memperbarui jurnal: " . $e->getMessage();
        }
    }
}

// --- Fetch Existing Photos ---
$stmt_fotos = $pdo->prepare("SELECT * FROM jurnal_foto WHERE id_jurnal = ?");
$stmt_fotos->execute([$id_jurnal]);
$fotos = $stmt_fotos->fetchAll(PDO::FETCH_ASSOC);

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
            <h1 class="text-xl font-semibold">Edit Jurnal</h1>
             <div class="flex items-center gap-4">
                 <div class="font-semibold text-right">
                    <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="text-xs text-gray-500">Siswa</div>
                </div>
                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar">
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-6">
            <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
                 <h2 class="text-2xl font-bold text-gray-800 mb-2">Formulir Edit Jurnal</h2>
                 <p class="mb-6 text-sm text-gray-600">Perbarui detail jurnal Anda. Setelah disimpan, status akan kembali menjadi 'Pending' untuk ditinjau ulang.</p>

                <?php if ($error): ?>
                    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                        <p class="font-bold">Terjadi Kesalahan</p>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="jurnal_edit.php?id=<?php echo $jurnal['id']; ?>" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="tanggal_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kegiatan</label>
                        <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan" value="<?php echo htmlspecialchars($jurnal['tanggal_kegiatan']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    
                    <div>
                        <label for="deskripsi_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kegiatan</label>
                        <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required><?php echo htmlspecialchars($jurnal['deskripsi_kegiatan']); ?></textarea>
                    </div>

                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="kendala" class="block text-sm font-medium text-gray-700 mb-1">Kendala (Opsional)</label>
                            <textarea id="kendala" name="kendala" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($jurnal['kendala']); ?></textarea>
                        </div>
                        <div>
                            <label for="solusi" class="block text-sm font-medium text-gray-700 mb-1">Solusi (Opsional)</label>
                            <textarea id="solusi" name="solusi" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($jurnal['solusi']); ?></textarea>
                        </div>
                    </div>

                    <!-- Existing Photos -->
                     <?php if (!empty($fotos)): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto yang Sudah Di-upload</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        <?php foreach ($fotos as $foto): ?>
                            <div class="relative group">
                                <img src="../uploads/jurnal/<?php echo htmlspecialchars($foto['file_path']); ?>" class="w-full h-32 object-cover rounded-lg shadow-sm">
                                <div class="absolute top-1 right-1">
                                     <input type="checkbox" name="delete_foto[]" value="<?php echo $foto['id']; ?>" class="h-5 w-5 text-red-600 border-gray-300 rounded focus:ring-red-500 cursor-pointer" title="Pilih untuk hapus">
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                         <p class="text-xs text-gray-500 mt-2">Pilih foto yang ingin Anda hapus.</p>
                    </div>
                    <?php endif; ?>

                    <!-- New Photo Upload -->
                    <div>
                         <label for="foto_bukti" class="block text-sm font-medium text-gray-700 mb-2">Tambah Foto Bukti Baru (Opsional)</label>
                         <input type="file" id="foto_bukti" name="foto_bukti[]" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-4 border-t mt-8">
                        <a href="jurnal_detail.php?id=<?php echo $jurnal['id']; ?>" class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition-all">Batal</a>
                        <button type="submit" class="bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 transition-all flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
