<?php include APP_ROOT . '/app/views/admin/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/admin/includes/sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h2>Manajemen Data Dosen Pembimbing</h2>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['flash_message'])) : ?>
            <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?>">
                <?php echo $_SESSION['flash_message']['message']; unset($_SESSION['flash_message']); ?>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <button class="btn btn-primary" onclick="showForm('addForm')">Tambah Dosen Baru</button>
            <button class="btn btn-success" onclick="showForm('importForm')">Import dari CSV</button>
        </div>

        <div id="importForm" class="form-container" style="display: none; background-color: #e9f5ff;">
            <h3>Import Data Dosen dari File CSV</h3>
            <form action="<?php echo BASE_URL; ?>/admin/importDosen" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="csv_file">Pilih File CSV</label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                    <small>Sistem hanya akan membaca kolom yang sesuai (NIDN, Nama, Email, No. HP, Status).</small>
                </div>
                <button type="submit" class="btn btn-success">Unggah dan Import</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('importForm')">Batal</button>
            </form>
        </div>

        <div id="addForm" class="form-container" style="display: none;">
            <h3>Tambah Dosen Baru</h3>
            <form action="<?php echo BASE_URL; ?>/admin/tambahDosen" method="POST">
                <div class="form-group">
                    <label for="nidn">NIDN</label>
                    <input type="text" id="nidn" name="nidn" required value="<?php echo htmlspecialchars($data['nidn'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($data['nama_lengkap'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="nomor_telepon">Nomor Telepon</label>
                    <input type="text" id="nomor_telepon" name="nomor_telepon" value="<?php echo htmlspecialchars($data['nomor_telepon'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="status_aktif">Status Aktif</label>
                    <select id="status_aktif" name="status_aktif" required>
                        <option value="Aktif" <?php echo (isset($data['status_aktif']) && $data['status_aktif'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="Tidak Aktif" <?php echo (isset($data['status_aktif']) && $data['status_aktif'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Simpan Data</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('addForm')">Batal</button>
            </form>
        </div>

        <div id="editForm" class="form-container" style="display: <?php echo isset($data['dosen_edit']) ? 'block' : 'none'; ?>;">
            <h3>Edit Data Dosen</h3>
            <?php if (isset($data['dosen_edit'])) : ?>
            <form action="<?php echo BASE_URL; ?>/admin/editDosen/<?php echo $data['dosen_edit']['id']; ?>" method="POST">
                <div class="form-group">
                    <label for="edit_nidn">NIDN</label>
                    <input type="text" id="edit_nidn" name="nidn" required value="<?php echo htmlspecialchars($data['dosen_edit']['nidn']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_nama_lengkap">Nama Lengkap</label>
                    <input type="text" id="edit_nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($data['dosen_edit']['nama_lengkap']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required value="<?php echo htmlspecialchars($data['dosen_edit']['email']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_nomor_telepon">Nomor Telepon</label>
                    <input type="text" id="edit_nomor_telepon" name="nomor_telepon" value="<?php echo htmlspecialchars($data['dosen_edit']['nomor_telepon'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_status_aktif">Status Aktif</label>
                    <select id="edit_status_aktif" name="status_aktif" required>
                        <option value="Aktif" <?php echo ($data['dosen_edit']['status_aktif'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="Tidak Aktif" <?php echo ($data['dosen_edit']['status_aktif'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Simpan Data</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('editForm')">Batal</button>
            </form>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <h3>Data Dosen Pembimbing</h3>
            <table>
                <thead>
                    <tr>
                        <th>NIDN</th>
                        <th>Nama Lengkap</th>
                        <th>Status Aktif</th>
                        <th>Email</th>
                        <th>Nomor Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['dosen'])) : ?>
                        <?php foreach ($data['dosen'] as $dsn) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($dsn['nidn']); ?></td>
                                <td><?php echo htmlspecialchars($dsn['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($dsn['status_aktif']); ?></td>
                                <td><?php echo htmlspecialchars($dsn['email']); ?></td>
                                <td><?php echo htmlspecialchars($dsn['nomor_telepon'] ?? ''); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/admin/editDosen/<?php echo $dsn['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <!-- <button class="btn btn-warning btn-sm" onclick="editDosen(<?php echo $dsn['id']; ?>, '<?php echo htmlspecialchars($dsn['nidn'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($dsn['nama_lengkap'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($dsn['email'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($dsn['nomor_telepon'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($dsn['status_aktif'], ENT_QUOTES); ?>')">Edit</button> -->
                                    <form action="<?php echo BASE_URL; ?>/admin/hapusDosen/<?php echo $dsn['id']; ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data dosen ini? Tindakan ini juga akan menghapus akun user terkait.');">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="6">Tidak ada data dosen.</td></tr>
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

    function editDosen(id, nidn, nama_lengkap, email, nomor_telepon, status_aktif) {
        document.getElementById('edit_nidn').value = nidn;
        document.getElementById('edit_nama_lengkap').value = nama_lengkap;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_nomor_telepon').value = nomor_telepon;
        document.getElementById('edit_status_aktif').value = status_aktif;

        document.querySelector('#editForm form').action = `<?php echo BASE_URL; ?>/admin/editDosen/${id}`;

        showForm('editForm');
    }

    <?php if (isset($data['dosen_edit'])) : ?>
        showForm('editForm');
    <?php endif; ?>
    <?php if (isset($data['error']) && !isset($data['dosen_edit'])) : ?>
        showForm('addForm');
    <?php endif; ?>
</script>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
