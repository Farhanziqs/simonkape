<?php include APP_ROOT . '/app/views/admin/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/admin/includes/sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h2>Manajemen Penempatan KP</h2>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <button class="btn btn-primary" onclick="showForm('addForm')">Tambah Penempatan KP Baru</button>

        <div id="addForm" class="form-container" style="display: none;">
            <h3>Tambah Penempatan KP Baru</h3>
            <form action="<?php echo BASE_URL; ?>/admin/tambahPenempatan" method="POST">
                <div class="form-group">
                    <label for="instansi_id">Instansi KP</label>
                    <select id="instansi_id" name="instansi_id" required>
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
                    <select id="dosen_pembimbing_id" name="dosen_pembimbing_id" required>
                        <option value="">Pilih Dosen</option>
                        <?php foreach ($data['dosen_list'] as $dosen) : ?>
                            <option value="<?php echo $dosen['id']; ?>" <?php echo (isset($data['dosen_pembimbing_id']) && $data['dosen_pembimbing_id'] == $dosen['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dosen['nama_lengkap']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="form-group">
                    <label for="tanggal_mulai">Tanggal Mulai KP</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo htmlspecialchars($data['tanggal_mulai'] ?? ''); ?>">
                </div>
                 <div class="form-group">
                    <label for="tanggal_selesai">Tanggal Selesai KP</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="<?php echo htmlspecialchars($data['tanggal_selesai'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Pilih Mahasiswa</label>
                    <div class="table-container" style="max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; border-radius: 4px;">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 5%;"><input type="checkbox" id="select-all-add"></th>
                                    <th style="width: 30%;">NIM</th>
                                    <th>Nama Lengkap</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['mahasiswa_belum_ditempatkan'] as $mhs) : ?>
                                    <tr>
                                        <td><input type="checkbox" class="mhs-checkbox-add" name="mahasiswa_ids[]" value="<?php echo $mhs['id']; ?>"></td>
                                        <td><?php echo htmlspecialchars($mhs['nim']); ?></td>
                                        <td><?php echo htmlspecialchars($mhs['nama_lengkap']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <small>Mahasiswa yang dipilih akan otomatis ditetapkan Dosen Pembimbing dan Instansi KP ini.</small>
                </div>
                <button type="submit" class="btn btn-success">Simpan Data</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('addForm')">Batal</button>
            </form>
        </div>

        <div id="editForm" class="form-container" style="display: <?php echo isset($data['penempatan_edit']) ? 'block' : 'none'; ?>;">
            <h3>Edit Penempatan KP</h3>
            <?php if (isset($data['penempatan_edit'])) : ?>
            <form action="<?php echo BASE_URL; ?>/admin/editPenempatan/<?php echo $data['penempatan_edit']['id']; ?>" method="POST">
                <div class="form-group">
                    <label for="edit_instansi_id">Instansi KP</label>
                    <select id="edit_instansi_id" name="instansi_id" required>
                        <?php foreach ($data['instansi_list'] as $instansi) : ?>
                            <option value="<?php echo $instansi['id']; ?>" <?php echo ($data['penempatan_edit']['instansi_id'] == $instansi['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($instansi['nama_instansi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_dosen_pembimbing_id">Dosen Pembimbing</label>
                    <select id="edit_dosen_pembimbing_id" name="dosen_pembimbing_id" required>
                        <?php foreach ($data['dosen_list'] as $dosen) : ?>
                            <option value="<?php echo $dosen['id']; ?>" <?php echo ($data['penempatan_edit']['dosen_pembimbing_id'] == $dosen['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dosen['nama_lengkap']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="form-group">
                    <label for="edit_tanggal_mulai">Tanggal Mulai KP</label>
                    <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai" value="<?php echo htmlspecialchars($data['penempatan_edit']['tanggal_mulai'] ?? ''); ?>">
                </div>
                 <div class="form-group">
                    <label for="edit_tanggal_selesai">Tanggal Selesai KP</label>
                    <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai" value="<?php echo htmlspecialchars($data['penempatan_edit']['tanggal_selesai'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Pilih Mahasiswa</label>
                    <div class="mahasiswa-checkbox-list" style="max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                        <?php
                        $all_available_mahasiswa = $data['mahasiswa_belum_ditempatkan'];
                        $current_mahasiswa_ids = array_column($data['mahasiswa_saat_ini'], 'id');

                        // Gabungkan mahasiswa yang belum ditempatkan dengan mahasiswa yang sudah ada di kelompok ini
                        $display_mahasiswa_list = [];
                        $displayed_ids = [];

                        foreach($all_available_mahasiswa as $mhs) {
                            $display_mahasiswa_list[$mhs['id']] = $mhs;
                            $displayed_ids[] = $mhs['id'];
                        }

                        foreach($data['mahasiswa_saat_ini'] as $mhs) {
                            if (!in_array($mhs['id'], $displayed_ids)) {
                                $display_mahasiswa_list[$mhs['id']] = $mhs;
                            }
                        }

                        // Urutkan berdasarkan nama atau NIM
                        usort($display_mahasiswa_list, function($a, $b) {
                            return strcmp($a['nim'], $b['nim']);
                        });

                        if (!empty($display_mahasiswa_list)) : ?>
                            <?php foreach ($display_mahasiswa_list as $mhs) : ?>
                                <label>
                                    <input type="checkbox" name="mahasiswa_ids[]" value="<?php echo $mhs['id']; ?>"
                                        <?php echo in_array($mhs['id'], $current_mahasiswa_ids) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($mhs['nim']); ?> - <?php echo htmlspecialchars($mhs['nama_lengkap']); ?>
                                </label><br>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p>Tidak ada mahasiswa yang tersedia.</p>
                        <?php endif; ?>
                    </div>
                    <small>Mahasiswa yang dipilih akan otomatis ditetapkan Dosen Pembimbing dan Instansi KP ini.</small>
                </div>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="<?php echo BASE_URL; ?>/admin/penempatan" class="btn btn-secondary">Batal</a>
            </form>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <h3>Data Penempatan KP</h3>
            <table>
                <thead>
                    <tr>
                        <th>Instansi</th>
                        <th>Dosen Pembimbing</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['processed_penempatan_details'])) : ?>
                        <?php
                        // Variabel untuk melacak apakah sel Instansi dan Dosen sudah dicetak
                        $instansi_printed = [];
                        $dosen_printed = [];
                        ?>
                        <?php foreach ($data['processed_penempatan_details'] as $inst_group) : ?>
                            <?php
                            $instansi_rowspan_set_for_group = false; // Flag untuk Instansi di level group Instansi
                            ?>
                            <?php foreach ($inst_group['dosen_groups'] as $dosen_group) : ?>
                                <?php
                                $dosen_rowspan_set_for_group = false; // Flag untuk Dosen di level group Dosen
                                ?>
                                <?php foreach ($dosen_group['students'] as $index => $detail) : ?>
                                    <tr>
                                        <?php if (!$instansi_rowspan_set_for_group) : ?>
                                            <td rowspan="<?php echo $inst_group['instansi_rowspan']; ?>">
                                                <?php echo htmlspecialchars($inst_group['nama_instansi']); ?>
                                            </td>
                                            <?php $instansi_rowspan_set_for_group = true; ?>
                                        <?php endif; ?>

                                        <?php if (!$dosen_rowspan_set_for_group) : ?>
                                            <td rowspan="<?php echo $dosen_group['dosen_rowspan']; ?>">
                                                <?php echo htmlspecialchars($dosen_group['nama_dosen_pembimbing']); ?>
                                            </td>
                                            <?php $dosen_rowspan_set_for_group = true; ?>
                                        <?php endif; ?>

                                        <td><?php echo htmlspecialchars($detail['nim']); ?></td>
                                        <td><?php echo htmlspecialchars($detail['nama_mahasiswa']); ?></td>
                                        <td><?php echo htmlspecialchars($detail['tanggal_mulai']); ?></td>
                                        <td><?php echo htmlspecialchars($detail['tanggal_selesai']); ?></td>

                                        <?php if ($index === 0) : // Aksi hanya ditampilkan di baris pertama setiap kelompok dosen ?>
                                            <td rowspan="<?php echo $dosen_group['dosen_rowspan']; ?>">
                                                <a href="<?php echo BASE_URL; ?>/admin/showGenerateSuratForm/<?php echo $detail['penempatan_id']; ?>" class="btn btn-primary btn-sm">Surat Pengantar</a>
                                                <a href="<?php echo BASE_URL; ?>/admin/showGenerateSuratPenarikanForm/<?php echo $detail['penempatan_id']; ?>" class="btn btn-success btn-sm">Surat Penarikan</a>
                                                <a href="<?php echo BASE_URL; ?>/admin/editPenempatan/<?php echo $detail['penempatan_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="<?php echo BASE_URL; ?>/admin/hapusPenempatan/<?php echo $detail['penempatan_id']; ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penempatan kelompok ini? Ini akan menghapus semua mahasiswa dalam kelompok ini dari penempatan.');">
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="7">Tidak ada data penempatan mahasiswa yang ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function showForm(formId) {
        // Sembunyikan semua form dulu
        document.getElementById('addForm').style.display = 'none';
        document.getElementById('editForm').style.display = 'none';
        // Tampilkan form yang diminta
        document.getElementById(formId).style.display = 'block';
    }

    function hideForm(formId) {
        document.getElementById(formId).style.display = 'none';
    }

    // Tampilkan form yang sesuai jika ada error atau saat mode edit
    <?php if (isset($data['penempatan_edit'])) : ?>
        showForm('editForm');
    <?php elseif (isset($data['error']) && empty($data['penempatan_edit'])) : ?>
        showForm('addForm'); // Jika ada error saat tambah, tampilkan form tambah
    <?php endif; ?>

    // --- Fungsionalitas Checklist "Pilih Semua" ---
    document.addEventListener('DOMContentLoaded', function() {
        // Untuk form "Tambah"
        const selectAllAdd = document.getElementById('select-all-add');
        if (selectAllAdd) {
            selectAllAdd.addEventListener('change', function() {
                document.querySelectorAll('.mhs-checkbox-add').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }

        // Untuk form "Edit"
        const selectAllEdit = document.getElementById('select-all-edit');
        if (selectAllEdit) {
            selectAllEdit.addEventListener('change', function() {
                document.querySelectorAll('.mhs-checkbox-edit').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
    });
</script>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
