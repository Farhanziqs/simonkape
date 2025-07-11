<?php
// simonkapedb/app/views/dosen/kaprodi_views/data_instansi.php

include APP_ROOT . '/app/views/dosen/includes/header.php';
include APP_ROOT . '/app/views/dosen/includes/sidebar.php';
?>

<div class="dosen-main-content">
    <div class="header">
        <h2>Data Instansi Mitra KP</h2>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Instansi</th>
                        <th>Alamat</th>
                        <th>Kota/Kab.</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>PIC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['instansi_list'])) : ?>
                        <?php foreach ($data['instansi_list'] as $inst) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($inst['nama_instansi']); ?></td>
                                <td><?php echo htmlspecialchars($inst['alamat'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($inst['kota_kab'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($inst['telepon'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($inst['email'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($inst['pic'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="6">Tidak ada data instansi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/dosen/includes/footer.php'; ?>
