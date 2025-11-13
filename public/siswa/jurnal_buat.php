<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['siswa']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$user_name = $_SESSION['nama_lengkap'] ?? 'Siswa';
$id_siswa = $_SESSION['user_id'];
$error = null;

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_kegiatan = sanitize_input($_POST['tanggal_kegiatan']);
    $deskripsi_kegiatan = sanitize_input($_POST['deskripsi_kegiatan']);
    $kendala = sanitize_input($_POST['kendala']);
    $solusi = sanitize_input($_POST['solusi']);

    // Basic Validation
    if (empty($tanggal_kegiatan) || empty($deskripsi_kegiatan)) {
        $error = "Tanggal dan Deskripsi Kegiatan wajib diisi.";
    } else {
        $pdo->beginTransaction();
        try {
            // Insert into jurnal_harian
            $stmt = $pdo->prepare("INSERT INTO jurnal_harian (id_siswa, tanggal_kegiatan, deskripsi_kegiatan, kendala, solusi, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$id_siswa, $tanggal_kegiatan, $deskripsi_kegiatan, $kendala, $solusi]);
            $id_jurnal = $pdo->lastInsertId();

            // Handle File Uploads
            $upload_dir = '../uploads/jurnal/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (isset($_FILES['foto_bukti']) && count($_FILES['foto_bukti']['name']) > 0) {
                for ($i = 0; $i < count($_FILES['foto_bukti']['name']); $i++) {
                    if ($_FILES['foto_bukti']['error'][$i] === UPLOAD_ERR_OK) {
                        $file_tmp = $_FILES['foto_bukti']['tmp_name'][$i];
                        $file_name = uniqid('jurnal_' . $id_jurnal . '_') . '-' . basename($_FILES['foto_bukti']['name'][$i]);
                        $file_path = $upload_dir . $file_name;

                        // Check file type and size (optional but recommended)
                        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                        if (in_array($_FILES['foto_bukti']['type'][$i], $allowed_types)) {
                            if (move_uploaded_file($file_tmp, $file_path)) {
                                $stmt_foto = $pdo->prepare("INSERT INTO jurnal_foto (id_jurnal, file_path) VALUES (?, ?)");
                                $stmt_foto->execute([$id_jurnal, $file_name]);
                            }
                        }
                    }
                }
            }

            $pdo->commit();
            redirect('index.php?success=jurnal_created');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Gagal menyimpan jurnal: " . $e->getMessage();
        }
    }
}

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
         <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-6">
            <h1 class="text-xl font-semibold">Buat Jurnal Baru</h1>
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
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Formulir Jurnal Harian</h2>

                <?php if ($error): ?>
                    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                        <p class="font-bold">Terjadi Kesalahan</p>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="jurnal_buat.php" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="tanggal_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kegiatan</label>
                        <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan" value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    
                    <div>
                        <label for="deskripsi_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kegiatan</label>
                        <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Jelaskan secara rinci kegiatan yang Anda lakukan hari ini." required></textarea>
                    </div>

                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="kendala" class="block text-sm font-medium text-gray-700 mb-1">Kendala yang Dihadapi (Opsional)</label>
                            <textarea id="kendala" name="kendala" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Apakah ada kendala? Jika ya, jelaskan di sini."></textarea>
                        </div>
                        <div>
                            <label for="solusi" class="block text-sm font-medium text-gray-700 mb-1">Solusi yang Dilakukan (Opsional)</label>
                            <textarea id="solusi" name="solusi" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Bagaimana Anda mengatasi kendala tersebut?"></textarea>
                        </div>
                    </div>

                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Bukti (Opsional)</label>
                         <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md" id="drop-area">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="foto_bukti" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload file</span>
                                        <input id="foto_bukti" name="foto_bukti[]" type="file" class="sr-only" multiple accept="image/*">
                                    </label>
                                    <p class="pl-1">atau tarik dan lepas</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 10MB</p>
                            </div>
                        </div>
                        <div id="preview-container" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4"></div>
                    </div>


                    <div class="flex items-center justify-end space-x-4 pt-4 border-t mt-8">
                        <a href="index.php" class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition-all">Batal</a>
                        <button type="submit" class="bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 transition-all flex items-center gap-2">
                           <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                            <span>Simpan Jurnal</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
const dropArea = document.getElementById('drop-area');
const fileInput = document.getElementById('foto_bukti');
const previewContainer = document.getElementById('preview-container');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
  dropArea.addEventListener(eventName, preventDefaults, false);
  document.body.addEventListener(eventName, preventDefaults, false);
});

['dragenter', 'dragover'].forEach(eventName => {
  dropArea.addEventListener(eventName, () => dropArea.classList.add('border-indigo-500', 'bg-gray-50'), false);
});

['dragleave', 'drop'].forEach(eventName => {
  dropArea.addEventListener(eventName, () => dropArea.classList.remove('border-indigo-500', 'bg-gray-50'), false);
});

dropArea.addEventListener('drop', handleDrop, false);
fileInput.addEventListener('change', handleFiles, false);

function preventDefaults(e) {
  e.preventDefault();
  e.stopPropagation();
}

function handleDrop(e) {
  const dt = e.dataTransfer;
  const files = dt.files;
  fileInput.files = files;
  handleFiles({ target: fileInput });
}

function handleFiles(event) {
    previewContainer.innerHTML = ''; // Clear existing previews
    const files = event.target.files;
    if (files.length > 0) {
        for (const file of files) {
            if (file.type.startsWith('image/')){
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg shadow-sm">
                        <p class="text-xs text-gray-700 mt-1 truncate">${file.name}</p>
                    `;
                    previewContainer.appendChild(div);
                }
                reader.readAsDataURL(file);
            }
        }
    }
}
</script>

<?php include '../../templates/footer.php'; ?>
