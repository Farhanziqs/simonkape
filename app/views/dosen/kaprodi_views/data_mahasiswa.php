<?php
// simonkapedb/app/views/dosen/kaprodi_views/data_mahasiswa.php

include APP_ROOT . '/app/views/dosen/includes/header.php';
include APP_ROOT . '/app/views/dosen/includes/sidebar.php';
?>

<div class="dosen-main-content">
    <div class="header">
        <h2>Data Mahasiswa</h2>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama Lengkap</th>
                        <th>Program Studi</th>
                        <th>Instansi KP</th>
                        <th>Dosen Pembimbing</th>
                        <th>Status KP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['mahasiswa_list'])) : ?>
                        <?php foreach ($data['mahasiswa_list'] as $mhs) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mhs['nim']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['program_studi']); ?></td>
                                <td><?php echo htmlspecialchars($mhs['nama_instansi'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($mhs['nama_dosen'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($mhs['status_kp']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="6">Tidak ada data mahasiswa.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/dosen/includes/footer.php'; ?>
