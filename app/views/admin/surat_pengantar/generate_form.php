<?php
// simonkapedb/app/views/admin/surat_pengantar/generate_form.php

include APP_ROOT . '/app/views/admin/includes/header.php';
include APP_ROOT . '/app/views/admin/includes/sidebar.php';
?>

<div class="main-content">
    <div class="header">
        <h2>Generate Surat Pengantar KP</h2>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>

        <h3>Konfigurasi Surat Pengantar untuk Instansi: <?php echo htmlspecialchars($data['penempatan']['nama_instansi']); ?></h3>
        <p>Surat ini akan berisi daftar mahasiswa yang ditempatkan pada kelompok ini.</p>

        <form action="<?php echo BASE_URL; ?>/admin/generateSuratPengantarPdf/<?php echo $data['penempatan_id']; ?>" method="POST">
            <div class="form-group">
                <label for="nomor_surat">Nomor Surat:</label>
                <input type="text" id="nomor_surat" name="nomor_surat"
                       value="<?php echo htmlspecialchars($data['default_nomor_surat'] ?? 'XXX/Q.11/TI-UND/IV/2025'); ?>" required>
                <small>Format contoh: 037/Q.11/TI-UND/IV/2025</small>
            </div>

            <div class="form-group">
                <label for="tanggal_surat">Tanggal Surat:</label>
                <input type="date" id="tanggal_surat" name="tanggal_surat"
                       value="<?php echo htmlspecialchars(date('Y-m-d')); ?>" required>
                <small>Tanggal surat akan ditampilkan dalam format Bahasa Indonesia.</small>
            </div>

            <div class="form-group">
                <label for="kepada_yth_nama">Kepada Yth. (Nama/Jabatan Penerima):</label>
                <input type="text" id="kepada_yth_nama" name="kepada_yth_nama"
                       value="<?php echo htmlspecialchars($data['default_kepada_yth_nama'] ?? $data['penempatan']['nama_instansi']); ?>" required>
                <small>Misal: KEPALA DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU KOTA BAUBAU</small>
            </div>

            <div class="form-group">
                <label for="alamat_tujuan">Alamat Tujuan (Opsional):</label>
                <input type="text" id="alamat_tujuan" name="alamat_tujuan"
                       value="<?php echo htmlspecialchars($data['default_alamat_tujuan'] ?? 'Tempat'); ?>">
            </div>

            <button type="submit" class="btn btn-success">Generate PDF</button>
            <a href="<?php echo BASE_URL; ?>/admin/penempatan" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
