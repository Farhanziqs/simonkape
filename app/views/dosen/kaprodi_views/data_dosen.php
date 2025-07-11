<?php
// simonkapedb/app/views/dosen/kaprodi_views/data_dosen.php

include APP_ROOT . '/app/views/dosen/includes/header.php';
include APP_ROOT . '/app/views/dosen/includes/sidebar.php';
?>


<div class="dosen-main-content">
    <div class="header">
        <h2>Data Dosen Pembimbing</h2>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>NIDN</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Nomor Telepon</th>
                        <th>Status Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['dosen_list'])) : ?>
                        <?php foreach ($data['dosen_list'] as $dosen) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($dosen['nidn']); ?></td>
                                <td><?php echo htmlspecialchars($dosen['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($dosen['email']); ?></td>
                                <td><?php echo htmlspecialchars($dosen['nomor_telepon'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($dosen['status_aktif']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="5">Tidak ada data dosen.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/dosen/includes/footer.php'; ?>
