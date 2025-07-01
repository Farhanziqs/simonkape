<?php include APP_ROOT . '/app/views/dosen/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/dosen/includes/sidebar.php'; ?>

<div class="dosen-main-content">
    <div class="dosen-header">
        <h2>Lihat Absen Harian Mahasiswa Bimbingan</h2>
    </div>

    <div class="dosen-container">
        <div class="filter-container">
            <h3>Filter Data Absensi</h3>
            <form action="<?php echo BASE_URL; ?>/dosen/absen" method="POST">
                <div class="filter-group" style="flex-wrap: wrap;">
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
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($data['start_date']); ?>" required>
                    </div>
                    <div>
                        <label for="end_date">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($data['end_date']); ?>" required>
                    </div>
                    <div>
                        <button type="submit" name="filter_absen" class="btn btn-primary" style="padding: 9px 15px;">Lihat Data</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h3>Data Absensi Filtered</h3>
            <table>
                <thead>
                    <tr>
                        <th>NAMA MAHASISWA</th>
                        <th>TANGGAL</th>
                        <th>WAKTU ABSEN</th>
                        <th>STATUS KEHADIRAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['absen_data'])) : ?>
                        <?php foreach ($data['absen_data'] as $absen) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($absen['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($absen['tanggal']); ?></td>
                                <td><?php echo htmlspecialchars($absen['waktu_absen']); ?></td>
                                <td><?php echo htmlspecialchars($absen['status_kehadiran']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="4">Tidak ada data tersedia.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (!empty($data['absen_data'])) : ?>
                <a href="#" onclick="window.print()" class="btn btn-success">Cetak Rekap Absen</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/dosen/includes/footer.php'; ?>
