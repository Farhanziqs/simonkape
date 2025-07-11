<?php
// simonkapedb/app/views/dosen/kaprodi_views/status_kp.php

include APP_ROOT . '/app/views/dosen/includes/header.php';
include APP_ROOT . '/app/views/dosen/includes/sidebar.php';
?>

<div class="dosen-main-content">
    <div class="header">
        <h2>Status KP Mahasiswa per Instansi</h2>
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
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Status KP</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['processed_penempatan_details'])) : ?>
                        <?php
                        $instansi_printed = [];
                        ?>
                        <?php foreach ($data['processed_penempatan_details'] as $inst_group) : ?>
                            <?php
                            $instansi_rowspan_set_for_group = false;
                            ?>
                            <?php foreach ($inst_group['dosen_groups'] as $dosen_group) : ?>
                                <?php
                                $dosen_rowspan_set_for_group = false;
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
                                        <td><?php echo htmlspecialchars($detail['program_studi']); ?></td>
                                        <td><?php echo htmlspecialchars($detail['status_kp'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($detail['tanggal_mulai']); ?></td>
                                        <td><?php echo htmlspecialchars($detail['tanggal_selesai']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="8">Tidak ada data penempatan mahasiswa yang ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/dosen/includes/footer.php'; ?>
