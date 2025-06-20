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

        <button class="btn btn-primary" onclick="showForm('addForm')">Tambah Instansi Baru</button>

        <div id="addForm" class="form-container" style="display: none;">
            <h3>Tambah Instansi Baru</h3>
            <form action="<?php echo BASE_URL; ?>/admin/tambahInstansi" method="POST">
                <div class="form-group">
                    <label for="nama_instansi">Nama Instansi</label>
                    <input type="text" id="nama_instansi" name="nama_instansi" required value="<?php echo htmlspecialchars($data['nama_instansi'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="bidang_kerja">Bidang Kerja</label>
                    <input type="text" id="bidang_kerja" name="bidang_kerja" value="<?php echo htmlspecialchars($data['bidang_kerja'] ?? ''); ?>">
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

        <div id="editForm" class="form-container" style="display: <?php echo isset($data['instansi_edit']) ? 'block' : 'none'; ?>;">
            <h3>Edit Data Instansi</h3>
            <?php if (isset($data['instansi_edit'])) : ?>
            <form action="<?php echo BASE_URL; ?>/admin/editInstansi/<?php echo $data['instansi_edit']['id']; ?>" method="POST">
                <div class="form-group">
                    <label for="edit_nama_instansi">Nama Instansi</label>
                    <input type="text" id="edit_nama_instansi" name="nama_instansi" required value="<?php echo htmlspecialchars($data['instansi_edit']['nama_instansi']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_bidang_kerja">Bidang Kerja</label>
                    <input type="text" id="edit_bidang_kerja" name="bidang_kerja" value="<?php echo htmlspecialchars($data['instansi_edit']['bidang_kerja'] ?? ''); ?>">
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
                <button type="button" class="btn btn-secondary" onclick="hideForm('editForm')">Batal</button>
            </form>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <h3>Data Instansi Kerja Praktik</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Instansi</th>
                        <th>Bidang Kerja</th>
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
                                <td><?php echo htmlspecialchars($inst['bidang_kerja']); ?></td>
                                <td><?php echo htmlspecialchars($inst['alamat']); ?></td>
                                <td><?php echo htmlspecialchars($inst['kota_kab']); ?></td>
                                <td><?php echo htmlspecialchars($inst['telepon']); ?></td>
                                <td><?php echo htmlspecialchars($inst['email']); ?></td>
                                <td><?php echo htmlspecialchars($inst['pic']); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="editInstansi(<?php echo $inst['id']; ?>, '<?php echo htmlspecialchars($inst['nama_instansi'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($inst['bidang_kerja'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($inst['alamat'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($inst['kota_kab'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($inst['telepon'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($inst['email'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($inst['pic'], ENT_QUOTES); ?>')">Edit</button>
                                    <form action="<?php echo BASE_URL; ?>/admin/hapusInstansi/<?php echo $inst['id']; ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data instansi ini?');">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="8">Tidak ada data instansi.</td></tr>
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

    function editInstansi(id, nama_instansi, bidang_kerja, alamat, kota_kab, telepon, email, pic) {
        document.getElementById('edit_nama_instansi').value = nama_instansi;
        document.getElementById('edit_bidang_kerja').value = bidang_kerja;
        document.getElementById('edit_alamat').value = alamat;
        document.getElementById('edit_kota_kab').value = kota_kab;
        document.getElementById('edit_telepon').value = telepon;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_pic').value = pic;

        document.querySelector('#editForm form').action = `<?php echo BASE_URL; ?>/admin/editInstansi/${id}`;

        showForm('editForm');
    }

    <?php if (isset($data['instansi_edit'])) : ?>
        showForm('editForm');
    <?php endif; ?>
    <?php if (isset($data['error']) && !isset($data['instansi_edit'])) : ?>
        showForm('addForm');
    <?php endif; ?>
</script>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
