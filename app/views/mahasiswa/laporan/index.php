<?php include APP_ROOT . '/app/views/mahasiswa/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/mahasiswa/includes/sidebar.php'; ?>

<div class="mahasiswa-main-content">
    <div class="mahasiswa-header">
        <h2>Laporan Mingguan</h2>
    </div>

    <div class="mahasiswa-container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <div class="form-container" style="background-color: #fdfdff;">
            <h3>Pilih Periode Laporan</h3>
            <form action="<?php echo BASE_URL; ?>/mahasiswa/laporan" method="POST">
                <div style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
                    <div class="form-group" style="flex: 1;">
                        <label for="start_date">Tanggal Mulai:</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($data['start_date']); ?>" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="end_date">Tanggal Selesai:</label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($data['end_date']); ?>" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="filter_logbook" class="btn btn-primary">Tampilkan Logbook</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (!empty($data['logbooks_available_for_report'])) : ?>
            <div class="form-container">
                <form action="<?php echo BASE_URL; ?>/mahasiswa/buatLaporanMingguan" method="POST">
                    <input type="hidden" name="dosen_pembimbing_id" value="<?php echo htmlspecialchars($data['dosen_pembimbing_id']); ?>">
                    <input type="hidden" name="periode_mingguan" value="<?php echo htmlspecialchars($data['periode_mingguan']); ?>">

                    <h3>Pilih Logbook untuk Disertakan (Periode: <?php echo htmlspecialchars($data['periode_mingguan']); ?>)</h3>

                    <div class="table-container">
                        <table id="logbook-selection-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">
                                        <input type="checkbox" id="select-all-checkbox" title="Pilih Semua">
                                    </th>
                                    <th style="width: 25%;">Tanggal</th>
                                    <th>Uraian Kegiatan Singkat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['logbooks_available_for_report'] as $logbook) : ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="selected_logbook_ids[]" value="<?php echo $logbook['id']; ?>" class="logbook-checkbox">
                                        </td>
                                        <td><?php echo htmlspecialchars(date('l, d F Y', strtotime($logbook['tanggal']))); ?></td>
                                        <td><?php echo htmlspecialchars(substr($logbook['uraian_kegiatan'], 0, 100)) . (strlen($logbook['uraian_kegiatan']) > 100 ? '...' : ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-success" style="margin-top: 20px;">Buat dan Kirim Laporan</button>
                </form>
            </div>
        <?php elseif(isset($_POST['filter_logbook'])): ?>
            <p class="alert alert-info">Tidak ada data logbook yang ditemukan untuk periode yang Anda pilih.</p>
        <?php endif; ?>
        <div class="table-container" style="margin-top: 50px;">
            <h3>Riwayat Laporan Mingguan</h3>
            <table>
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Status Laporan</th>
                        <th>Dosen Pembimbing</th>
                        <th>Feedback</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['riwayat_laporan_mingguan'])) : ?>
                        <?php foreach ($data['riwayat_laporan_mingguan'] as $laporan) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($laporan['periode_mingguan']); ?></td>
                                <td><?php echo htmlspecialchars($laporan['status_laporan']); ?></td>
                                <td><?php echo htmlspecialchars($laporan['nama_dosen'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($laporan['feedback_dosen'] ?? '-'); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/mahasiswa/cetakLaporanMingguan/<?php echo $laporan['id']; ?>" target="_blank" class="btn btn-info btn-sm">Lihat Laporan</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="5">Belum ada laporan mingguan yang dibuat.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const logbookCheckboxes = document.querySelectorAll('.logbook-checkbox');

    if(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            logbookCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    }
});
</script>

<?php include APP_ROOT . '/app/views/mahasiswa/includes/footer.php'; ?>
