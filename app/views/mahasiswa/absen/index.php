<?php include APP_ROOT . '/app/views/mahasiswa/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/mahasiswa/includes/sidebar.php'; ?>

<div class="mahasiswa-main-content">
    <div class="mahasiswa-header">
        <h2>Absen Harian</h2>
    </div>

    <div class="mahasiswa-container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <div class="absen-card">
            <h4>Tanggal:</h4>
            <p><?php echo date('l, d F Y', strtotime($data['tanggal_sekarang'])); ?></p>
            <h4>Jam:</h4>
            <p class="time" id="current-time"><?php echo date('H:i:s'); ?></p>

            <?php if ($data['absensi_hari_ini']) : ?>
                <p>Anda sudah melakukan absensi hari ini. Status: <strong><?php echo htmlspecialchars($data['absensi_hari_ini']['status_kehadiran']); ?></strong></p>
                <?php if ($data['absensi_hari_ini']['status_kehadiran'] != 'Hadir') : ?>
                    <form action="<?php echo BASE_URL; ?>/mahasiswa/prosesAbsen" method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="status_kehadiran" value="Hadir">
                        <button type="submit" class="btn btn-success">Absen Sekarang</button>
                    </form>
                <?php endif; ?>
            <?php else : ?>
                <form action="<?php echo BASE_URL; ?>/mahasiswa/prosesAbsen" method="POST">
                    <input type="hidden" name="status_kehadiran" value="Hadir">
                    <button type="submit" class="btn btn-success">Absen Sekarang</button>
                </form>
                <form action="<?php echo BASE_URL; ?>/mahasiswa/prosesAbsen" method="POST" style="margin-top: 10px;">
                    <select name="status_kehadiran" class="form-group" style="width: auto; display: inline-block; margin-right: 10px;">
                        <option value="Izin">Izin</option>
                        <option value="Sakit">Sakit</option>
                    </select>
                    <button type="submit" class="btn btn-warning">Kirim Izin/Sakit</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="table-container" style="margin-top: 30px;">
            <h3>Riwayat Absensi Singkat</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['riwayat_absen'])) : ?>
                        <?php foreach ($data['riwayat_absen'] as $absen) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($absen['tanggal']))); ?></td>
                                <td><?php echo htmlspecialchars($absen['waktu_absen']); ?></td>
                                <td><?php echo htmlspecialchars($absen['status_kehadiran']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3">Tidak ada riwayat absensi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
    }

    setInterval(updateTime, 1000);
    updateTime(); // Initial call
</script>

<?php include APP_ROOT . '/app/views/mahasiswa/includes/footer.php'; ?>
