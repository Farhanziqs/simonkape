<?php include APP_ROOT . '/app/views/dosen/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/dosen/includes/sidebar.php'; ?>

<div class="dosen-main-content">
    <div class="dosen-header">
        <h2>Lihat Laporan Mingguan Mahasiswa Bimbingan</h2>
    </div>

    <div class="dosen-container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <div class="filter-container">
            <h3>Filter Laporan</h3>
            <form action="<?php echo BASE_URL; ?>/dosen/laporan" method="POST">
                <div class="filter-group">
                    <div style="flex: 2;">
                        <label for="mahasiswa_id">Pilih Mahasiswa</label>
                        <select name="mahasiswa_id" id="mahasiswa_id" style="width: 100%;">
                            <option value="all">Semua Mahasiswa Bimbingan</option>
                            <?php foreach ($data['mahasiswa_bimbingan'] as $mhs) : ?>
                                <option value="<?php echo $mhs['id']; ?>" <?php echo ($data['selected_mahasiswa_id'] == $mhs['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($mhs['nama_lengkap'] . ' (' . $mhs['nim'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="periode_laporan">Pilih Periode Laporan</label>
                        <select name="periode" id="periode" style="width: 100%;">
                            <option value="all">Semua Periode</option>
                            <?php foreach ($data['periode_list'] as $periode) : ?>
                                <option value="<?php echo htmlspecialchars($periode); ?>" <?php echo ($data['selected_periode'] == $periode) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($periode); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" name="filter_laporan" class="btn btn-primary" style="padding: 9px 15px;">Lihat Data</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h3>Data Laporan Mingguan</h3>
            <table>
                <thead>
                    <tr>
                        <th>NAMA MAHASISWA</th>
                        <th>PERIODE LAPORAN</th>
                        <th>FILE LAPORAN</th>
                        <th>STATUS</th>
                        <th>FEEDBACK ANDA</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['laporan_data'])) : ?>
                        <?php foreach ($data['laporan_data'] as $laporan) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($laporan['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($laporan['periode_mingguan']); ?></td>
                                <td>
                                    <?php if ($laporan['file_laporan']) : ?>
                                        <a href="<?php echo BASE_URL . htmlspecialchars($laporan['file_laporan']); ?>" target="_blank">Lihat File</a>
                                    <?php else : ?>
                                        Tidak ada
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($laporan['status_laporan']); ?></td>
                                <td><?php echo htmlspecialchars($laporan['feedback_dosen'] ?? '-'); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="showFeedbackModal(<?php echo $laporan['id']; ?>, '<?php echo htmlspecialchars($laporan['periode_mingguan'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($laporan['status_laporan'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($laporan['feedback_dosen'] ?? '', ENT_QUOTES); ?>')">Tanggapan</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="6">Tidak ada data laporan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="hideFeedbackModal()">&times;</span>
        <h3>Tanggapan untuk Laporan <span id="modalLaporanNamaMahasiswa"></span></h3>
        <p>Periode: <span id="modalLaporanPeriode"></span></p>
        <p>Status Saat Ini: <strong id="modalLaporanStatus"></strong></p>
        <form id="feedbackForm" action="" method="POST">
            <div class="form-group">
                <label for="feedback_dosen">Feedback Anda:</label>
                <textarea id="feedback_dosen" name="feedback_dosen" rows="4" placeholder="Berikan tanggapan atau saran..."></textarea>
            </div>
            <div class="form-group">
                <label for="status_laporan">Ubah Status Laporan:</label>
                <select id="status_laporan" name="status_laporan" required>
                    <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                    <option value="Disetujui">Disetujui</option>
                    <option value="Revisi">Revisi</option>
                    <option value="Ditolak">Ditolak</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Simpan Tanggapan</button>
            <button type="button" class="btn btn-secondary" onclick="hideFeedbackModal()">Batal</button>
        </form>
    </div>
</div>

<script>
    function showFeedbackModal(laporanId, periode, status, feedback) {
        // Set form action URL
        const form = document.getElementById('feedbackForm');
        form.action = `<?php echo BASE_URL; ?>/dosen/tanggapan/${laporanId}`;

        // Set modal content
        document.getElementById('modalLaporanPeriode').textContent = periode;
        document.getElementById('modalLaporanStatus').textContent = status;
        document.getElementById('feedback_dosen').value = feedback;
        document.getElementById('status_laporan').value = status;

        // You might need to fetch the Mahasiswa name via AJAX if it's not available in the table
        // For now, let's just display the feedback part.
        document.getElementById('feedbackModal').style.display = 'block';
    }

    function hideFeedbackModal() {
        document.getElementById('feedbackModal').style.display = 'none';
    }

    // Reuse showLogbookDetail for the logbook page
    // It's already in the logbook/index.php file, so no need to repeat it here.
</script>

<?php include APP_ROOT . '/app/views/dosen/includes/footer.php'; ?>
