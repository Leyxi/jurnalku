<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['admin']);
include '../../app/_config/database.php';
include '../../app/_lib/helpers.php';

// Handle optional messages
$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

// Fetch all relations with names
$stmt = $pdo->query("SELECT r.id, r.id_pembimbing, r.id_siswa, p.nama_lengkap AS pembimbing_name, s.nama_lengkap AS siswa_name, r.created_at
FROM relasi_bimbingan r
JOIN users p ON r.id_pembimbing = p.id
JOIN users s ON r.id_siswa = s.id
ORDER BY p.nama_lengkap, s.nama_lengkap");
$rels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch pembimbing and siswa lists for form
$pbs = $pdo->query("SELECT id, nama_lengkap FROM users WHERE role = 'pembimbing' ORDER BY nama_lengkap")->fetchAll(PDO::FETCH_ASSOC);
$ss = $pdo->query("SELECT id, nama_lengkap FROM users WHERE role = 'siswa' ORDER BY nama_lengkap")->fetchAll(PDO::FETCH_ASSOC);

include '../../templates/header.php';
$user_name = $_SESSION['user_nama'] ?? 'Admin';
$csrf = generate_csrf_token();
?>

<div class="min-h-screen bg-gray-100">
    <aside class="fixed inset-y-0 left-0 z-20 w-64 bg-white shadow-xl border-r border-gray-100 flex-col hidden sm:flex">
        <div class="flex h-16 items-center justify-center border-b border-gray-200">
            <a href="index.php" class="flex items-center gap-2 font-bold text-lg bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                <div class="p-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-white">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span>E-Jurnal</span>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="users_manage.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                 <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 006-6v-1a6 6 0 00-9-5.197" /></svg>
                Users
            </a>
            <a href="manage_relasi.php" class="flex items-center gap-3 rounded-lg bg-blue-100 text-blue-700 px-3 py-2.5 font-semibold">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Relasi Bimbingan
            </a>
            <a href="pengumuman_manage.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.144-6.363a1.76 1.76 0 01.592-2.145l6.364-2.144a1.76 1.76 0 012.144.592z" /></svg>
                Pengumuman
            </a>
            <a href="../logout.php" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-red-600 hover:bg-red-50 mt-auto transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Logout
            </a>
        </nav>
    </aside>

    <div class="flex flex-col sm:ml-64">
        <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-6">
            <h1 class="text-xl font-semibold">Manajemen Relasi Bimbingan</h1>
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
            </div>
        </header>

        <main class="p-6">
            <div class="max-w-4xl mx-auto space-y-6">
                <?php if ($success): ?>
                    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">Operasi berhasil.</div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-md"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="font-bold mb-4">Tambah Relasi (Multi-assign)</h2>
                    <form action="../../app/_logic/relasi_action.php" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-3" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_batch">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pembimbing (pilih satu atau lebih)</label>
                            <select name="id_pembimbing[]" multiple size="4" class="mt-1 w-full px-3 py-2 border rounded-lg">
                                <?php foreach ($pbs as $p): ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nama_lengkap']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Tekan Ctrl/Cmd untuk memilih beberapa.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Siswa (pilih satu atau lebih)</label>
                            <select name="id_siswa[]" multiple size="6" class="mt-1 w-full px-3 py-2 border rounded-lg">
                                <?php foreach ($ss as $s): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nama_lengkap']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Pilih siswa untuk ditetapkan ke pembimbing yang dipilih.</p>
                        </div>
                        <div class="flex flex-col gap-3 items-stretch">
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg">Tambah Relasi</button>
                        </div>
                    </form>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <form action="../../app/_logic/relasi_action.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="import_csv">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                            <label class="block text-sm font-medium text-gray-700">Import CSV</label>
                            <input type="file" name="csv_file" accept="text/csv" class="mt-2">
                            <button type="submit" class="mt-2 w-full bg-amber-600 text-white py-2 rounded-lg">Upload CSV</button>
                        </form>

                        <form action="../../app/_logic/relasi_action.php" method="POST">
                            <input type="hidden" name="action" value="export_csv">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                            <label class="block text-sm font-medium text-gray-700">Export CSV</label>
                            <button type="submit" class="mt-2 w-full bg-gray-700 text-white py-2 rounded-lg">Export CSV</button>
                        </form>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="font-bold mb-4">Daftar Relasi</h2>
                    <div class="overflow-x-auto">
                        <form id="batchForm" action="../../app/_logic/relasi_action.php" method="POST" onsubmit="return confirm('Lanjutkan aksi batch?');">
                            <input type="hidden" name="action" value="delete_batch">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                            <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3"><input type="checkbox" id="checkAll" onclick="toggleAll(this)"></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembimbing</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($rels)): ?>
                                    <tr><td colspan="5" class="p-6 text-center text-gray-500">Belum ada relasi.</td></tr>
                                <?php else: foreach ($rels as $r): ?>
                                    <tr>
                                        <td class="px-6 py-4"><input type="checkbox" name="ids[]" value="<?php echo $r['id']; ?>"></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($r['pembimbing_name']); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($r['siswa_name']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                                        <td class="px-6 py-4 text-right">
                                            <button type="button" onclick="if(confirm('Hapus relasi ini?')){ submitDelete(<?php echo $r['id']; ?>); }" class="text-red-600">Hapus</button>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Hapus Terpilih</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
function toggleAll(box) {
    document.querySelectorAll('input[name="ids[]"]').forEach(function(ch){ ch.checked = box.checked; });
}

function submitDelete(id) {
    var f = document.createElement('form');
    f.method = 'POST';
    f.action = '../../app/_logic/relasi_action.php';
    var a = document.createElement('input'); a.type = 'hidden'; a.name = 'action'; a.value = 'delete'; f.appendChild(a);
    var t = document.createElement('input'); t.type = 'hidden'; t.name = 'csrf_token'; t.value = '<?php echo $csrf; ?>'; f.appendChild(t);
    var i = document.createElement('input'); i.type = 'hidden'; i.name = 'ids[]'; i.value = id; f.appendChild(i);
    document.body.appendChild(f);
    f.submit();
}
</script>

<?php include '../../templates/footer.php'; ?>
