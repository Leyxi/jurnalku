<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['siswa']);
?>
<?php include '../../templates/header.php'; ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Buat Jurnal Baru</h1>
    <form action="../../app/_logic/jurnal_create.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        <div class="mb-4">
            <label for="tanggal_kegiatan" class="block">Tanggal Kegiatan</label>
            <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan" class="w-full border px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="deskripsi_kegiatan" class="block">Deskripsi Kegiatan</label>
            <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" class="w-full border px-3 py-2" rows="4" required></textarea>
        </div>
        <div class="mb-4">
            <label for="kendala" class="block">Kendala (Opsional)</label>
            <textarea id="kendala" name="kendala" class="w-full border px-3 py-2" rows="3"></textarea>
        </div>
        <div class="mb-4">
            <label for="solusi" class="block">Solusi (Opsional)</label>
            <textarea id="solusi" name="solusi" class="w-full border px-3 py-2" rows="3"></textarea>
        </div>
        <div class="mb-4">
            <label for="foto_bukti" class="block">Foto Bukti (Multiple)</label>
            <input type="file" id="foto_bukti" name="foto_bukti[]" multiple accept="image/*" class="w-full border px-3 py-2">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Jurnal</button>
    </form>
</div>
<?php include '../../templates/footer.php'; ?>
