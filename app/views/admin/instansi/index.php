<?php include APP_ROOT . '/app/views/admin/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/admin/includes/sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h2>Manajemen Data Instansi Kerja Praktik</h2>
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

        <button class="btn btn-primary" onclick="showForm('addForm')">Tambah Instansi Baru</button>
        <button class="btn btn-info" onclick="showForm('importForm')">Import CSV</button>

        <div id="addForm" class="form-container" style="display: none;">
            <h3>Tambah Instansi Baru</h3>
            <form action="<?php echo BASE_URL; ?>/admin/tambahInstansi" method="POST">
                <div class="form-group">
                    <label for="nama_instansi">Nama Instansi</label>
                    <input type="text" id="nama_instansi" name="nama_instansi" required value="<?php echo htmlspecialchars($data['nama_instansi'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat"><?php echo htmlspecialchars($data['alamat'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="kota_kab">Kota/Kabupaten</label>
                    <input type="text" id="kota_kab" name="kota_kab" value="<?php echo htmlspecialchars($data['kota_kab'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="telepon">Nomor Telepon Instansi</label>
                    <input type="text" id="telepon" name="telepon" value="<?php echo htmlspecialchars($data['telepon'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email Instansi</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="pic">PIC Instansi (Opsional)</label>
                    <input type="text" id="pic" name="pic" value="<?php echo htmlspecialchars($data['pic'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-success">Simpan Data</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('addForm')">Batal</button>
            </form>
        </div>

        <div id="importForm" class="form-container" style="display: none;">
            <h3>Import Data Instansi dari CSV</h3>
            <form action="<?php echo BASE_URL; ?>/admin/importInstansiCsv" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="csv_file">Pilih File CSV</label>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                    <small>Pastikan baris pertama CSV adalah header. Kolom yang akan diambil: `Nama Instansi`, `Bidang Kerja` (opsional), `Alamat` (opsional), `Kota/Kabupaten` (opsional), `Telepon` (opsional), `Email` (opsional), `PIC` (opsional). Kolom lain di CSV akan diabaikan.</small>
                </div>
                <button type="submit" class="btn btn-success">Import Data</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('importForm')">Batal</button>
            </form>
        </div>

        <div id="editForm" class="form-container" style="display: <?php echo isset($data['instansi_edit']) ? 'block' : 'none'; ?>;">
            <h3>Edit Data Instansi</h3>
            <?php if (isset($data['instansi_edit'])) : ?>
            <form action="<?php echo BASE_URL; ?>/admin/editInstansi/<?php echo $data['instansi_edit']['id']; ?>" method="POST">
                <div class="form-group">
                    <label for="edit_nama_instansi">Nama Instansi</label>
                    <input type="text" id="edit_nama_instansi" name="nama_instansi" required value="<?php echo htmlspecialchars($data['instansi_edit']['nama_instansi']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_alamat">Alamat Lengkap</label>
                    <textarea id="edit_alamat" name="alamat"><?php echo htmlspecialchars($data['instansi_edit']['alamat'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_kota_kab">Kota/Kabupaten</label>
                    <input type="text" id="edit_kota_kab" name="kota_kab" value="<?php echo htmlspecialchars($data['instansi_edit']['kota_kab'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_telepon">Nomor Telepon Instansi</label>
                    <input type="text" id="edit_telepon" name="telepon" value="<?php echo htmlspecialchars($data['instansi_edit']['telepon'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_email">Email Instansi</label>
                    <input type="email" id="edit_email" name="email" value="<?php echo htmlspecialchars($data['instansi_edit']['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_pic">PIC Instansi (Opsional)</label>
                    <input type="text" id="edit_pic" name="pic" value="<?php echo htmlspecialchars($data['instansi_edit']['pic'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-success">Simpan Data</button>
                <a href="<?php echo BASE_URL; ?>/admin/instansi" class="btn btn-secondary">Batal</a>
            </form>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <h3>Data Instansi Kerja Praktik</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Instansi</th>
                        <th>Alamat</th>
                        <th>Kota/Kab.</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>PIC</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['instansi'])) : ?>
                        <?php foreach ($data['instansi'] as $inst) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($inst['nama_instansi']); ?></td>
                                <td><?php echo htmlspecialchars($inst['alamat']); ?></td>
                                <td><?php echo htmlspecialchars($inst['kota_kab']); ?></td>
                                <td><?php echo htmlspecialchars($inst['telepon']); ?></td>
                                <td><?php echo htmlspecialchars($inst['email']); ?></td>
                                <td><?php echo htmlspecialchars($inst['pic']); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/admin/editInstansi/<?php echo $inst['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="<?php echo BASE_URL; ?>/admin/hapusInstansi/<?php echo $inst['id']; ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data instansi ini?');">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                         <tr><td colspan="6">Tidak ada data instansi.</td></tr>
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

    // Tampilkan form yang sesuai jika ada error atau mode edit
    <?php if (isset($data['instansi_edit'])) : ?>
        showForm('editForm');
    <?php elseif (isset($data['error'])) : ?>
        showForm('addForm');
    <?php endif; ?>
</script>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
