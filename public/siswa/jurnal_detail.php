<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['siswa']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

$id_siswa = $_SESSION['user_id'];
$user_name = $_SESSION['user_nama'] ?? 'Siswa';

// --- Parameter Validation ---
$id_jurnal = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_jurnal) {
    redirect('index.php');
}

// --- Fetch Jurnal Details (Security check: student can only see their own jurnal) ---
$stmt_jurnal = $pdo->prepare("SELECT * FROM jurnal_harian WHERE id = ? AND id_siswa = ?");
$stmt_jurnal->execute([$id_jurnal, $id_siswa]);
$jurnal = $stmt_jurnal->fetch(PDO::FETCH_ASSOC);

// Redirect if jurnal not found or doesn't belong to the student
if (!$jurnal) {
    redirect('jurnal_history.php?error=not_found');
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
            <h1 class="text-xl font-semibold">Detail Jurnal</h1>
            <div class="flex items-center gap-4">
                 <div class="font-semibold text-right">
                    <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="text-xs text-gray-500">Siswa</div>
                </div>
                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar">
            </div>
        </header>

        <main class="flex-1 p-6">
            <div class="max-w-4xl mx-auto">
                 <!-- Action Header -->
                <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Jurnal Tanggal: <?php echo date('d F Y', strtotime($jurnal['tanggal_kegiatan'])); ?></h2>
                        <p class="text-sm text-gray-500">Lihat detail, komentar, dan foto dari jurnal Anda.</p>
                    </div>
                    <a href="jurnal_history.php" class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition-all flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        <span>Kembali</span>
                    </a>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-md space-y-6">
                    <!-- Status and Date -->
                    <div class="flex justify-between items-center pb-4 border-b">
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <span class="px-3 py-1 inline-flex text-base leading-5 font-semibold rounded-full <?php 
                                if ($jurnal['status'] == 'approved') echo 'bg-green-100 text-green-800';
                                elseif ($jurnal['status'] == 'rejected') echo 'bg-red-100 text-red-800';
                                else echo 'bg-yellow-100 text-yellow-800'; 
                            ?>">
                                <?php echo ucfirst(htmlspecialchars($jurnal['status'])); ?>
                            </span>
                        </div>
                        <div class="text-right">
                             <p class="text-sm text-gray-500">Tanggal Kegiatan</p>
                             <p class="text-base font-semibold text-gray-800"><?php echo date('l, d F Y', strtotime($jurnal['tanggal_kegiatan'])); ?></p>
                        </div>
                    </div>

                     <!-- Mentor Comment -->
                    <?php if (!empty($jurnal['komentar_pembimbing'])): ?>
                    <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded-r-lg">
                        <p class="font-bold text-indigo-800">Komentar dari Pembimbing</p>
                        <p class="text-indigo-700 mt-2 italic">"<?php echo htmlspecialchars($jurnal['komentar_pembimbing']); ?>"</p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Jurnal Content -->
                     <div class="space-y-5">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase">Deskripsi Kegiatan</h3>
                            <p class="mt-1 text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($jurnal['deskripsi_kegiatan']); ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase">Kendala</h3>
                            <p class="mt-1 text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($jurnal['kendala'] ?: '-'); ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase">Solusi</h3>
                            <p class="mt-1 text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($jurnal['solusi'] ?: '-'); ?></p>
                        </div>
                    </div>

                    <!-- Photos -->
                    <?php if (!empty($fotos)): ?>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Foto Bukti</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <?php foreach ($fotos as $foto): ?>
                                    <a href="../../public/uploads/jurnal/<?php echo htmlspecialchars($foto['file_path']); ?>" target="_blank" class="block relative group">
                                        <img src="../../public/uploads/jurnal/<?php echo htmlspecialchars($foto['file_path']); ?>" alt="Foto Bukti" class="w-full h-32 object-cover rounded-lg shadow-sm group-hover:shadow-xl transition-shadow">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 flex items-center justify-center transition-all rounded-lg">
                                            <svg class="h-8 w-8 text-white opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5"></path></svg>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Edit Button -->
                    <?php if ($jurnal['status'] === 'pending' || $jurnal['status'] === 'rejected'): ?>
                    <div class="pt-6 border-t flex justify-end">
                         <a href="jurnal_edit.php?id=<?php echo $jurnal['id']; ?>" class="bg-indigo-600 text-white font-semibold py-2 px-5 rounded-lg hover:bg-indigo-700 transition-all flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z" /></svg>
                            <span>Edit Jurnal</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
