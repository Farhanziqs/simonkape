<?php include APP_ROOT . '/app/views/mahasiswa/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/mahasiswa/includes/sidebar.php'; ?>

<div class="mahasiswa-main-content">
    <div class="mahasiswa-header">
        <h2>Selamat Datang, <?php echo htmlspecialchars($data['mahasiswa']['nama_lengkap']); ?>!</h2>
        <span>Sistem Monitoring Kerja Praktik Teknik Informatika</span>
    </div>
    <?php if (isset($_SESSION['error_message'])) : ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success_message'])) : ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <div class="mahasiswa-container">
        <h3 style="text-align: center; margin-bottom: 20px;">Menu Aktivitas Harian</h3>
        <div class="dashboard-cards">
            <div class="card">
                <span class="icon">📅</span>
                <h3>Absen Harian</h3>
                <p>Catat kehadiran harian Anda.</p>
                <a href="<?php echo BASE_URL; ?>/mahasiswa/absen" class="btn btn-blue">Absen Sekarang</a>
            </div>
            <div class="card">
                <span class="icon">📝</span>
                <h3>Logbook Harian</h3>
                <p>Isi catatan kegiatan harian Anda.</p>
                <a href="<?php echo BASE_URL; ?>/mahasiswa/logbook" class="btn btn-green">Isi Logbook</a>
            </div>
            <div class="card">
                <span class="icon">📄</span>
                <h3>Laporan Mingguan</h3>
                <p>Kirim laporan mingguan Anda.</p>
                <a href="<?php echo BASE_URL; ?>/mahasiswa/laporan" class="btn btn-yellow">Buat Laporan</a>
            </div>
        </div>

        <hr style="margin: 40px 0;">

        <?php if ($data['penempatan']) : ?>
            <div style="margin-bottom: 30px;">
                <h3>Informasi Kerja Praktik</h3>
                <p>
                    <strong>Instansi (Kantor):</strong><br>
                    <span style="font-size: 1.2em;"><?php echo htmlspecialchars($data['mahasiswa']['nama_instansi']); ?></span>
                </p>
                <p>
                    <strong>Periode Kerja Praktik:</strong><br>
                    <?php
                        $mulai = date('d F Y', strtotime($data['penempatan']['tanggal_mulai']));
                        $selesai = date('d F Y', strtotime($data['penempatan']['tanggal_selesai']));
                        echo "{$mulai} - {$selesai}";
                    ?>
                </p>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 30px; margin-bottom: 40px;">
                <div style="flex: 1; min-width: 300px;">
                    <h4>Dosen Pembimbing</h4>
                    <div class="table-container">
                        <table>
                            <tbody>
                                <tr>
                                    <td style="width: 30%;"><strong>Nama</strong></td>
                                    <td><?php echo htmlspecialchars($data['mahasiswa']['nama_dosen']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>NIDN</strong></td>
                                    <td><?php echo htmlspecialchars($data['mahasiswa']['nidn']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div style="flex: 2; min-width: 400px;">
                    <h4>Anggota Kelompok</h4>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['anggota_kelompok'] as $anggota): ?>
                                <tr>
                                    <td style="width: 30%;"><?php echo htmlspecialchars($anggota['nim']); ?></td>
                                    <td><?php echo htmlspecialchars($anggota['nama_lengkap']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="download-section" style="margin-top: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                <h3>Unduh Surat Penting</h3>
                <p>
                    Unduh Surat Pengantar KP Anda di sini.
                    <br>
                    <a href="<?php echo BASE_URL; ?>/mahasiswa/downloadSuratPengantar" class="btn btn-primary" style="margin-top: 10px;">
                        Unduh Surat Pengantar KP mu disini
                    </a>
                </p>
                <p style="margin-top: 15px;">
                Unduh Surat Penarikan KP Anda di sini jika KP Anda sudah berakhir.
                <br>
                <a href="<?php echo BASE_URL; ?>/mahasiswa/downloadSuratPenarikan" class="btn btn-primary" style="margin-top: 10px;">
                    Unduh Surat Penarikan KP
                </a>
            </p>
            </div>

        <?php else : ?>
            <div class="alert alert-info" style="text-align: center;">
                <h3>Anda Belum Ditempatkan</h3>
                <p>Informasi mengenai instansi, dosen pembimbing, dan periode KP akan muncul di sini setelah Anda ditempatkan oleh Admin Prodi.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include APP_ROOT . '/app/views/mahasiswa/includes/footer.php'; ?>
