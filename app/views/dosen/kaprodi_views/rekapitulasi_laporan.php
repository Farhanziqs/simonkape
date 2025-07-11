<?php
// simonkapedb/app/views/dosen/kaprodi_views/rekapitulasi_laporan.php

include APP_ROOT . '/app/views/dosen/includes/header.php';
include APP_ROOT . '/app/views/dosen/includes/sidebar.php';
?>

<div class="dosen-main-content">
    <div class="header">
        <h2>Rekapitulasi Laporan Mingguan Mahasiswa</h2>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Instansi</th>
                        <th>Dosen Pembimbing</th>
                        <th>NIM Mahasiswa</th>
                        <th>Nama Mahasiswa</th>
                        <th>Periode Laporan</th>
                        <th>Status Laporan</th>
                        <th>Link Dokumen</th>
                        <th>Komentar Dosen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['processed_reports'])) : ?>
                        <?php foreach ($data['processed_reports'] as $instansi_name => $inst_group) : ?>
                            <?php
                            $instansi_rowspan = 0;
                            foreach ($inst_group['dosen_groups'] as $dosen_group) {
                                $instansi_rowspan += count($dosen_group['reports']);
                            }
                            $instansi_cell_printed = false;
                            ?>
                            <?php foreach ($inst_group['dosen_groups'] as $dosen_name => $dosen_group) : ?>
                                <?php
                                $dosen_rowspan = count($dosen_group['reports']);
                                $dosen_cell_printed = false;
                                ?>
                                <?php foreach ($dosen_group['reports'] as $index => $report) : ?>
                                    <tr>
                                        <?php if (!$instansi_cell_printed) : ?>
                                            <td rowspan="<?php echo $instansi_rowspan; ?>">
                                                <?php echo htmlspecialchars($instansi_name); ?>
                                            </td>
                                            <?php $instansi_cell_printed = true; ?>
                                        <?php endif; ?>

                                        <?php if (!$dosen_cell_printed) : ?>
                                            <td rowspan="<?php echo $dosen_rowspan; ?>">
                                                <?php echo htmlspecialchars($dosen_name); ?>
                                            </td>
                                            <?php $dosen_cell_printed = true; ?>
                                        <?php endif; ?>

                                        <td><?php echo htmlspecialchars($report['nim']); ?></td>
                                        <td><?php echo htmlspecialchars($report['nama_mahasiswa']); ?></td>
                                        <td><?php echo htmlspecialchars($report['periode_mingguan']); ?></td>
                                        <td><?php echo htmlspecialchars($report['status_laporan']); ?></td>
                                        <td>
                                            <?php if (!empty($report['link_dokumen'])) : ?>
                                                <a href="<?php echo htmlspecialchars($report['link_dokumen']); ?>" target="_blank">Lihat Dokumen</a>
                                            <?php else : ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($report['komentar_dosen'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="8">Tidak ada data laporan mingguan yang ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/dosen/includes/footer.php'; ?>
