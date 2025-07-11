<?php include APP_ROOT . '/app/views/admin/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/admin/includes/sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h2>Manajemen Data Mahasiswa KP</h2>
    </div>

    <div class="container">
        <?php if (isset($data['error'])) : ?>
            <div class="alert alert-danger"><?php echo $data['error']; ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <button class="btn btn-primary" onclick="showForm('addForm')">Tambah Mahasiswa Baru</button>
        <button class="btn btn-info" onclick="showForm('importForm')">Import CSV</button>

        <div id="addForm" class="form-container" style="display: none;">
            <h3>Tambah Mahasiswa Baru</h3>
            <form action="<?php echo BASE_URL; ?>/admin/tambahMahasiswa" method="POST">
                <div class="form-group">
                    <label for="nim">NIM</label>
                    <input type="text" id="nim" name="nim" required value="<?php echo htmlspecialchars($data['nim'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="nama_lengkap">Nama Mahasiswa</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($data['nama_lengkap'] ?? ''); ?>">
                </div>
                <input type="hidden" name="program_studi" value="Teknik Informatika">
                <div class="form-group">
                    <label>Program Studi</label>
                    <input type="text" value="Teknik Informatika" readonly>
                </div>
                <div class="form-group">
                    <label for="status_kp">Status Awal</label>
                    <select name="status_kp" required>
                        <option value="Belum Terdaftar">Belum Terdaftar</option>
                        <option value="Terdaftar">Terdaftar</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Simpan Data</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('addForm')">Batal</button>
            </form>
        </div>

        <div id="importForm" class="form-container" style="display: none;">
            <h3>Import Data Mahasiswa dari CSV</h3>
            <form action="<?php echo BASE_URL; ?>/admin/importMahasiswaCsv" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="csv_file">Pilih File CSV</label>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                    <small>Pastikan baris pertama CSV adalah header. Kolom yang akan diambil: `NIM`, `Nama Lengkap`, `Program Studi` (opsional, default 'Teknik Informatika'), `Instansi ID` (opsional, bisa nama instansi atau ID), `Dosen Pembimbing ID` (opsional, bisa NIDN atau ID dosen). **Status KP otomatis akan diatur menjadi 'Terdaftar'.** Kolom lain di CSV akan diabaikan.</small>
                </div>
                <button type="submit" class="btn btn-success">Import Data</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('importForm')">Batal</button>
            </form>
        </div>

        <div id="editForm" class="form-container" style="display: <?php echo isset($data['mahasiswa_edit']) ? 'block' : 'none'; ?>;">
            <h3>Edit Data Mahasiswa</h3>
            <?php if (isset($data['mahasiswa_edit'])) : ?>
            <form action="<?php echo BASE_URL; ?>/admin/editMahasiswa/<?php echo $data['mahasiswa_edit']['id']; ?>" method="POST">
                <div class="form-group">
                    <label for="edit_nim">NIM</label>
                    <input type="text" id="edit_nim" name="nim" required value="<?php echo htmlspecialchars($data['mahasiswa_edit']['nim']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_nama_lengkap">Nama Mahasiswa</label>
                    <input type="text" id="edit_nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($data['mahasiswa_edit']['nama_lengkap']); ?>">
                </div>
                <input type="hidden" name="program_studi" value="Teknik Informatika">
                <div class="form-group">
                    <label>Program Studi</label>
                    <input type="text" value="Teknik Informatika" readonly>
                </div>
                <div class="form-group">
                    <label for="edit_status_kp">Status KP</label>
                    <select id="edit_status_kp" name="status_kp" required>
                        <option value="Belum Terdaftar" <?php echo ($data['mahasiswa_edit']['status_kp'] == 'Belum Terdaftar') ? 'selected' : ''; ?>>Belum Terdaftar</option>
                        <option value="Terdaftar" <?php echo ($data['mahasiswa_edit']['status_kp'] == 'Terdaftar') ? 'selected' : ''; ?>>Terdaftar</option>
                        <option value="Sedang KP" <?php echo ($data['mahasiswa_edit']['status_kp'] == 'Sedang KP') ? 'selected' : ''; ?>>Sedang KP</option>
                        <option value="Selesai" <?php echo ($data['mahasiswa_edit']['status_kp'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>
                <input type="hidden" name="instansi_id" value="<?php echo htmlspecialchars($data['mahasiswa_edit']['instansi_id']); ?>">
                <input type="hidden" name="dosen_pembimbing_id" value="<?php echo htmlspecialchars($data['mahasiswa_edit']['dosen_pembimbing_id']); ?>">

                <button type="submit" class="btn btn-success">Simpan Data</button>
                <a href="<?php echo BASE_URL; ?>/admin/mahasiswa" class="btn btn-secondary">Batal</a>
            </form>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <h3>Data Mahasiswa KP</h3>
            <table>
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama Lengkap</th>
                        <th>Instansi KP</th>
                        <th>Dosen Pembimbing</th>
                        <th>Status KP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['mahasiswa'])) : ?>
                        <?php foreach ($data['mahasiswa'] as $mhs) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mhs['nim']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['nama_instansi'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($mhs['nama_dosen'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($mhs['status_kp']); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/admin/editMahasiswa/<?php echo $mhs['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="<?php echo BASE_URL; ?>/admin/hapusMahasiswa/<?php echo $mhs['id']; ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa ini? Tindakan ini juga akan menghapus akun user terkait.');">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="6">Tidak ada data mahasiswa.</td></tr>
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
        document.getElementById('importForm').style.display = 'none';
        document.getElementById(formId).style.display = 'block';
    }

    function hideForm(formId) {
        document.getElementById(formId).style.display = 'none';
    }

    // Tampilkan form yang sesuai jika ada error atau saat mode edit
    <?php if (isset($data['mahasiswa_edit'])) : ?>
        showForm('editForm');
    <?php elseif (isset($data['error'])) : ?>
        showForm('addForm');
    <?php endif; ?>
</script>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
