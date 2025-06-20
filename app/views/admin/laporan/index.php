<?php include APP_ROOT . '/app/views/admin/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/admin/includes/sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h2>Laporan & Rekapitulasi Data KP</h2>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Rekap Absensi</h3>
                <p>Lihat rekap absensi seluruh mahasiswa.</p>
                <button class="btn btn-primary" onclick="showReport('absen')">Buka Laporan</button>
            </div>
            <div class="stat-card">
                <h3>Rekap Progress Laporan</h3>
                <p>Status laporan mingguan mahasiswa.</p>
                <button class="btn btn-primary" onclick="showReport('progress')">Buka Laporan</button>
            </div>
            <div class="stat-card">
                <h3>Daftar Mahasiswa per Status</h3>
                <p>Lihat mahasiswa berdasarkan status KP.</p>
                <button class="btn btn-primary" onclick="showReport('status')">Buka Laporan</button>
            </div>
            <div class="stat-card">
                <h3>Pembagian Dosen</h3>
                <p>Daftar pembagian dosen pembimbing.</p>
                <button class="btn btn-primary" onclick="showReport('dosen')">Buka Laporan</button>
            </div>
        </div>

        <div class="report-detail">
            <h3>Detail Laporan</h3>
            <p>Pilih jenis laporan untuk ditampilkan.</p>

            <div id="reportAbsen" class="report-content" style="display: none;">
                <h4>Absensi</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['rekap_absen'])) : ?>
                            <?php foreach ($data['rekap_absen'] as $rekap) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rekap['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($rekap['tanggal']); ?></td>
                                    <td><?php echo htmlspecialchars($rekap['status_kehadiran']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="3">Tidak ada data absensi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="<?php echo BASE_URL; ?>/admin/cetakLaporan/absen" target="_blank" class="btn btn-success">Cetak Laporan</a>
            </div>

            <div id="reportProgress" class="report-content" style="display: none;">
                <h4>Progress Laporan</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Laporan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['rekap_progress'])) : ?>
                            <?php foreach ($data['rekap_progress'] as $rekap) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rekap['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($rekap['periode_mingguan']); ?></td>
                                    <td><?php echo htmlspecialchars($rekap['status_laporan']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="3">Tidak ada data progress laporan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                 <a href="<?php echo BASE_URL; ?>/admin/cetakLaporan/progress" target="_blank" class="btn btn-success">Cetak Laporan</a>
            </div>

            <div id="reportStatus" class="report-content" style="display: none;">
                <h4>Mahasiswa per Status KP</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Status KP</th>
                            <th>Jumlah Mahasiswa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['mahasiswa_per_status'])) : ?>
                            <?php foreach ($data['mahasiswa_per_status'] as $status) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($status['status_kp']); ?></td>
                                    <td><?php echo htmlspecialchars($status['total']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="2">Tidak ada data status KP.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                 <a href="<?php echo BASE_URL; ?>/admin/cetakLaporan/status" target="_blank" class="btn btn-success">Cetak Laporan</a>
            </div>

            <div id="reportDosen" class="report-content" style="display: none;">
                <h4>Pembagian Dosen</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Dosen Pembimbing</th>
                            <th>Mahasiswa Bimbingan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['pembagian_dosen'])) : ?>
                            <?php foreach ($data['pembagian_dosen'] as $pembagian) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($pembagian['dosen_pembimbing']); ?></td>
                                    <td><?php echo htmlspecialchars($pembagian['mahasiswa_bimbingan'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="2">Tidak ada data pembagian dosen.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                 <a href="<?php echo BASE_URL; ?>/admin/cetakLaporan/dosen" target="_blank" class="btn btn-success">Cetak Laporan</a>
            </div>
        </div>
    </div>
</div>

<script>
    function showReport(reportType) {
        document.querySelectorAll('.report-content').forEach(function(el) {
            el.style.display = 'none';
        });
        document.getElementById('report' + reportType.charAt(0).toUpperCase() + reportType.slice(1)).style.display = 'block';
    }
</script>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
