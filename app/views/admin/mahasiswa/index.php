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

        <button class="btn btn-primary" onclick="showForm('addForm')">Tambah Mahasiswa Baru</button>

        <div id="addForm" class="form-container" style="display: none;">
            <h3>Tambah Mahasiswa Baru</h3>
            <form action="<?php echo BASE_URL; ?>/admin/tambahMahasiswa" method="POST">
                <div class="form-group">
                    <label for="nim">NIM</label>
                    <input type="text" id="nim" name="nim" required value="<?php echo htmlspecialchars($data['nim'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($data['nama_lengkap'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="program_studi">Program Studi</label>
                    <input type="text" id="program_studi" name="program_studi" required value="<?php echo htmlspecialchars($data['program_studi'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="instansi_id">Instansi KP</label>
                    <select id="instansi_id" name="instansi_id">
                        <option value="">Pilih Instansi</option>
                        <?php foreach ($data['instansi_list'] as $instansi) : ?>
                            <option value="<?php echo $instansi['id']; ?>" <?php echo (isset($data['instansi_id']) && $data['instansi_id'] == $instansi['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($instansi['nama_instansi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="form-group">
                    <label for="dosen_pembimbing_id">Dosen Pembimbing</label>
                    <select id="dosen_pembimbing_id" name="dosen_pembimbing_id">
                        <option value="">Pilih Dosen</option>
                        <?php foreach ($data['dosen_list'] as $dosen) : ?>
                            <option value="<?php echo $dosen['id']; ?>" <?php echo (isset($data['dosen_pembimbing_id']) && $data['dosen_pembimbing_id'] == $dosen['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dosen['nama_lengkap']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="form-group">
                    <label for="status_kp">Status KP</label>
                    <select id="status_kp" name="status_kp" required>
                        <option value="Belum Terdaftar" <?php echo (isset($data['status_kp']) && $data['status_kp'] == 'Belum Terdaftar') ? 'selected' : ''; ?>>Belum Terdaftar</option>
                        <option value="Terdaftar" <?php echo (isset($data['status_kp']) && $data['status_kp'] == 'Terdaftar') ? 'selected' : ''; ?>>Terdaftar</option>
                        <option value="Sedang KP" <?php echo (isset($data['status_kp']) && $data['status_kp'] == 'Sedang KP') ? 'selected' : ''; ?>>Sedang KP</option>
                        <option value="Selesai KP" <?php echo (isset($data['status_kp']) && $data['status_kp'] == 'Selesai KP') ? 'selected' : ''; ?>>Selesai KP</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Simpan Data</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('addForm')">Batal</button>
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
                    <label for="edit_nama_lengkap">Nama Lengkap</label>
                    <input type="text" id="edit_nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($data['mahasiswa_edit']['nama_lengkap']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_program_studi">Program Studi</label>
                    <input type="text" id="edit_program_studi" name="program_studi" required value="<?php echo htmlspecialchars($data['mahasiswa_edit']['program_studi']); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_instansi_id">Instansi KP</label>
                    <select id="edit_instansi_id" name="instansi_id">
                        <option value="">Pilih Instansi</option>
                        <?php foreach ($data['instansi_list'] as $instansi) : ?>
                            <option value="<?php echo $instansi['id']; ?>" <?php echo ($data['mahasiswa_edit']['instansi_id'] == $instansi['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($instansi['nama_instansi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_dosen_pembimbing_id">Dosen Pembimbing</label>
                    <select id="edit_dosen_pembimbing_id" name="dosen_pembimbing_id">
                        <option value="">Pilih Dosen</option>
                        <?php foreach ($data['dosen_list'] as $dosen) : ?>
                            <option value="<?php echo $dosen['id']; ?>" <?php echo ($data['mahasiswa_edit']['dosen_pembimbing_id'] == $dosen['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dosen['nama_lengkap']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_status_kp">Status KP</label>
                    <select id="edit_status_kp" name="status_kp" required>
                        <option value="Belum Terdaftar" <?php echo ($data['mahasiswa_edit']['status_kp'] == 'Belum Terdaftar') ? 'selected' : ''; ?>>Belum Terdaftar</option>
                        <option value="Terdaftar" <?php echo ($data['mahasiswa_edit']['status_kp'] == 'Terdaftar') ? 'selected' : ''; ?>>Terdaftar</option>
                        <option value="Sedang KP" <?php echo ($data['mahasiswa_edit']['status_kp'] == 'Sedang KP') ? 'selected' : ''; ?>>Sedang KP</option>
                        <option value="Selesai KP" <?php echo ($data['mahasiswa_edit']['status_kp'] == 'Selesai KP') ? 'selected' : ''; ?>>Selesai KP</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Simpan Data</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('editForm')">Batal</button>
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
                        <th>Program Studi</th>
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
                                <td><?php echo htmlspecialchars($mhs['program_studi']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['nama_instansi'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($mhs['nama_dosen'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($mhs['status_kp']); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="editMahasiswa(<?php echo $mhs['id']; ?>, '<?php echo htmlspecialchars($mhs['nim'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($mhs['nama_lengkap'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($mhs['program_studi'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($mhs['instansi_id'] ?? ''); ?>', '<?php echo htmlspecialchars($mhs['dosen_pembimbing_id'] ?? ''); ?>', '<?php echo htmlspecialchars($mhs['status_kp'], ENT_QUOTES); ?>')">Edit</button>
                                    <form action="<?php echo BASE_URL; ?>/admin/hapusMahasiswa/<?php echo $mhs['id']; ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa ini? Tindakan ini juga akan menghapus akun user terkait.');">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="7">Tidak ada data mahasiswa.</td></tr>
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

    function editMahasiswa(id, nim, nama_lengkap, program_studi, instansi_id, dosen_pembimbing_id, status_kp) {
        document.getElementById('edit_nim').value = nim;
        document.getElementById('edit_nama_lengkap').value = nama_lengkap;
        document.getElementById('edit_program_studi').value = program_studi;
        document.getElementById('edit_instansi_id').value = instansi_id;
        document.getElementById('edit_dosen_pembimbing_id').value = dosen_pembimbing_id;
        document.getElementById('edit_status_kp').value = status_kp;

        // Update form action for edit
        document.querySelector('#editForm form').action = `<?php echo BASE_URL; ?>/admin/editMahasiswa/${id}`;

        showForm('editForm');
    }

    // Tampilkan form edit jika ada data error dari proses edit sebelumnya
    <?php if (isset($data['mahasiswa_edit'])) : ?>
        showForm('editForm');
    <?php endif; ?>
     // Tampilkan form add jika ada data error dari proses add sebelumnya
    <?php if (isset($data['error']) && !isset($data['mahasiswa_edit'])) : ?>
        showForm('addForm');
    <?php endif; ?>
</script>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
