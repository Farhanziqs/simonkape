<?php
// simonkapedb/app/views/admin/kaprodi/index.php

include APP_ROOT . '/app/views/admin/includes/header.php';
include APP_ROOT . '/app/views/admin/includes/sidebar.php';
?>

<div class="main-content">
    <div class="header">
        <h2>Manajemen Data Kaprodi</h2>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <button class="btn btn-primary" onclick="showForm('addForm')">Tambah Data Kaprodi</button>

        <div id="addForm" class="form-container" style="display: none;">
            <h3>Tambah Data Kaprodi Baru</h3>
            <form action="<?php echo BASE_URL; ?>/admin/tambahKaprodi" method="POST">
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap:</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($data['nama_lengkap'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="nidn">NIDN:</label>
                    <input type="text" id="nidn" name="nidn" required value="<?php echo htmlspecialchars($data['nidn'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="nomor_telepon">Nomor Telepon (Opsional):</label>
                    <input type="text" id="nomor_telepon" name="nomor_telepon" value="<?php echo htmlspecialchars($data['nomor_telepon'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="program_studi">Program Studi:</label>
                    <input type="text" id="program_studi" name="program_studi" required value="<?php echo htmlspecialchars($data['program_studi'] ?? 'Teknik Informatika'); ?>">
                </div>
                <button type="submit" class="btn btn-success">Simpan Data</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('addForm')">Batal</button>
            </form>
        </div>

        <div id="editForm" class="form-container" style="display: <?php echo isset($data['kaprodi_edit']) ? 'block' : 'none'; ?>;">
            <h3>Edit Data Kaprodi</h3>
            <?php if (isset($data['kaprodi_edit'])) : ?>
            <form action="<?php echo BASE_URL; ?>/admin/editKaprodi/<?php echo $data['kaprodi_edit']['id']; ?>" method="POST">
                <div class="form-group">
                    <label for="edit_nama_lengkap">Nama Lengkap:</label>
                    <input type="text" id="edit_nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($data['kaprodi_edit']['nama_lengkap']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_nidn">NIDN:</label>
                    <input type="text" id="edit_nidn" name="nidn" required value="<?php echo htmlspecialchars($data['kaprodi_edit']['nidn']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" required value="<?php echo htmlspecialchars($data['kaprodi_edit']['email']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_nomor_telepon">Nomor Telepon (Opsional):</label>
                    <input type="text" id="edit_nomor_telepon" name="nomor_telepon" value="<?php echo htmlspecialchars($data['kaprodi_edit']['nomor_telepon']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_program_studi">Program Studi:</label>
                    <input type="text" id="edit_program_studi" name="program_studi" required value="<?php echo htmlspecialchars($data['kaprodi_edit']['program_studi']); ?>">
                </div>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="<?php echo BASE_URL; ?>/admin/kaprodi" class="btn btn-secondary">Batal</a>
            </form>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <h3>Daftar Kaprodi Terdaftar</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>NIDN</th>
                        <th>Email</th>
                        <th>Nomor Telepon</th>
                        <th>Program Studi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['kaprodi_list'])) : ?>
                        <?php foreach ($data['kaprodi_list'] as $kaprodi) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($kaprodi['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($kaprodi['nidn']); ?></td>
                                <td><?php echo htmlspecialchars($kaprodi['email']); ?></td>
                                <td><?php echo htmlspecialchars($kaprodi['nomor_telepon'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($kaprodi['program_studi']); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/admin/editKaprodi/<?php echo $kaprodi['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="<?php echo BASE_URL; ?>/admin/hapusKaprodi/<?php echo $kaprodi['id']; ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Kaprodi ini? Tindakan ini juga akan menghapus akun user terkait.');">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="6">Belum ada data Kaprodi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function showForm(formId) {
        document.getElementById('addForm').style.display = 'none';
        document.getElementById('editForm').style.display = 'none';
        document.getElementById(formId).style.display = 'block';
    }

    function hideForm(formId) {
        document.getElementById(formId).style.display = 'none';
    }

    // Tampilkan form edit jika ada data edit (dari controller)
    <?php if (isset($data['kaprodi_edit'])) : ?>
        showForm('editForm');
    <?php elseif (isset($data['error']) && !isset($data['kaprodi_edit'])) : ?>
        // Tampilkan form tambah jika ada error saat tambah
        showForm('addForm');
    <?php endif; ?>
</script>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
