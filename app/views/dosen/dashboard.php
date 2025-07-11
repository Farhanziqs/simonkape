<?php include APP_ROOT . '/app/views/dosen/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/dosen/includes/sidebar.php'; ?>

<div class="dosen-main-content">
    <div class="dosen-header">
        <h2>Selamat Datang, Dosen Pembimbing</h2>
        <span>Sistem Monitoring Kerja Praktik Teknik Informatika</span>
    </div>

    <div class="dosen-container">
        <h3>Dashboard Dosen Pembimbing</h3>
        <p>Selamat Pagi, <strong><?php echo htmlspecialchars($data['nama_dosen']); ?></strong></p>

        <div class="dashboard-cards">
            <div class="card">
                <span class="icon">👥</span>
                <h3>Mahasiswa Bimbingan</h3>
                <p><?php echo $data['total_mahasiswa_bimbingan']; ?></p>
            </div>
            <div class="card">
                <span class="icon">📄</span>
                <h3>Laporan Menunggu Persetujuan</h3>
                <p><?php echo $data['total_laporan_menunggu']; ?></p>
            </div>
            <div class="card">
                <span class="icon">📅</span>
                <h3>Absen Harian Mahasiswa</h3>
                <a href="<?php echo BASE_URL; ?>/dosen/absen" class="btn btn-blue">Lihat Absen</a>
            </div>
            <div class="card">
                <span class="icon">📝</span>
                <h3>Logbook Harian Mahasiswa</h3>
                <a href="<?php echo BASE_URL; ?>/dosen/logbook" class="btn btn-green">Lihat Logbook</a>
            </div>
            <div class="card">
                <span class="icon">📤</span>
                <h3>Laporan Mingguan Mahasiswa</h3>
                <a href="<?php echo BASE_URL; ?>/dosen/laporan" class="btn btn-yellow">Lihat Laporan</a>
            </div>
        </div>

        <?php if ($data['is_kaprodi']) : // Konten khusus Kaprodi di Dashboard ?>
            <div class="kaprodi-dashboard-section" style="margin-top: 30px; padding: 20px; border: 1px solid #007bff; border-radius: 5px; background-color: #e0f2ff;">
                <h3>Akses Cepat Kaprodi</h3>
                <p>Sebagai Ketua Program Studi, Anda memiliki akses ke data dan laporan umum:</p>
                <div class="quick-access-kaprodi">
                    <a href="<?php echo BASE_URL; ?>/dosen/dataDosen" class="btn btn-secondary">Data Dosen</a>
                    <a href="<?php echo BASE_URL; ?>/dosen/dataMahasiswa" class="btn btn-secondary">Data Mahasiswa</a>
                    <a href="<?php echo BASE_URL; ?>/dosen/dataInstansi" class="btn btn-secondary">Data Instansi</a>
                    <a href="<?php echo BASE_URL; ?>/dosen/statusKp" class="btn btn-secondary">Status KP</a>
                    <a href="<?php echo BASE_URL; ?>/dosen/rekapitulasiLaporan" class="btn btn-secondary">Rekap Laporan</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <h3>Daftar Mahasiswa Kerja Praktik Bimbingan</h3>
            <table>
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Status KP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['mahasiswa_bimbingan'])) : ?>
                        <?php foreach ($data['mahasiswa_bimbingan'] as $mhs) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mhs['nim']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['program_studi']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['status_kp']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="4">Tidak ada mahasiswa bimbingan yang terdaftar.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/dosen/includes/footer.php'; ?>
