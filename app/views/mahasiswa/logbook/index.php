<?php include APP_ROOT . '/app/views/mahasiswa/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/mahasiswa/includes/sidebar.php'; ?>

<div class="mahasiswa-main-content">
    <div class="mahasiswa-header">
        <h2>Logbook Harian</h2>
    </div>

    <div class="mahasiswa-container">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

            <button class="btn btn-primary" onclick="showForm('addLogbookForm')">Buat Laporan Harian</button>
            <div id="addLogbookForm" class="form-container" style="display: none;">
                <h3>Laporan Harian</h3>
                <form action="<?php echo BASE_URL; ?>/mahasiswa/prosesLogbook" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="tanggal_logbook">Tanggal:</label>
                        <input type="date" id="tanggal_logbook" name="tanggal_logbook" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="uraian_kegiatan">Bagaimana Kegiatanmu Hari Ini?</label>
                        <textarea id="uraian_kegiatan" name="uraian_kegiatan" required placeholder="Jelaskan kegiatan Anda secara detail..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="dokumentasi">Dokumentasi Kegiatan (Opsional)</label>
                        <input type="file" id="dokumentasi" name="dokumentasi" accept="image/*,application/pdf">
                        <small>PNG, JPG, PDF hingga 10MB.</small>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Logbook</button>
                    <button type="button" class="btn btn-secondary" onclick="hideForm('addLogbookForm')">Batal</button>
                </form>
            </div>

        <div class="table-container" style="margin-top: 30px;">
            <h3>Daftar Logbook Terisi</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kegiatan Singkat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['riwayat_logbook'])) : ?>
                        <?php foreach ($data['riwayat_logbook'] as $logbook) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($logbook['tanggal']))); ?></td>
                                <td><?php echo htmlspecialchars(substr($logbook['uraian_kegiatan'], 0, 50)) . (strlen($logbook['uraian_kegiatan']) > 50 ? '...' : ''); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="showLogbookDetail(
                                        '<?php echo htmlspecialchars(date('d/m/Y', strtotime($logbook['tanggal'])), ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($logbook['uraian_kegiatan'], ENT_QUOTES); ?>',
                                        '<?php echo $logbook['dokumentasi'] ? BASE_URL . $logbook['dokumentasi'] : ''; ?>'
                                    )">Lihat Detail</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3">Belum ada logbook yang diisi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="logbookDetailModal" class="logbook-detail-modal">
    <div class="logbook-detail-modal-content">
        <span class="close-button" onclick="hideLogbookDetailModal()">&times;</span>
        <h4>Detail Logbook Harian</h4>
        <p><strong>Tanggal:</strong> <span id="modalLogbookTanggal"></span></p>
        <p><strong>Uraian Kegiatan:</strong></p>
        <p id="modalLogbookUraian"></p>
        <div id="modalLogbookDokumentasiContainer">
            <strong>Dokumentasi:</strong><br>
            <img id="modalLogbookDokumentasi" src="" alt="Dokumentasi Kegiatan" style="max-width: 100%; margin-top: 10px; display:none;">
            <p id="modalLogbookNoDokumentasi" style="display:none;">Tidak ada dokumentasi.</p>
        </div>
    </div>
</div>

<script>
    function showForm(formId) {
        document.getElementById(formId).style.display = 'block';
    }

    function hideForm(formId) {
        document.getElementById(formId).style.display = 'none';
    }

    function showLogbookDetail(tanggal, uraian, dokumentasi_url) {
        // Mengisi data teks pada modal
        document.getElementById('modalLogbookTanggal').textContent = tanggal;
        document.getElementById('modalLogbookUraian').textContent = uraian;

        // Mendapatkan elemen-elemen penting
        const docContainer = document.getElementById('modalLogbookDokumentasiContainer');
        const imgElement = document.getElementById('modalLogbookDokumentasi');
        const noDokumentasiText = document.getElementById('modalLogbookNoDokumentasi');

        // --- AWAL PERBAIKAN ---

        // 1. Selalu hapus link PDF lama jika ada (dari panggilan sebelumnya)
        const oldPdfLink = docContainer.querySelector('a');
        if (oldPdfLink) {
            oldPdfLink.remove();
        }

        // 2. Cek apakah ada URL dokumentasi
        if (dokumentasi_url) {
            noDokumentasiText.style.display = 'none'; // Sembunyikan teks "tidak ada dokumentasi"

            // 3. Cek apakah dokumentasi adalah file PDF
            if (dokumentasi_url.toLowerCase().endsWith('.pdf')) {
                imgElement.style.display = 'none'; // Sembunyikan elemen gambar

                // Buat dan tampilkan link untuk PDF
                const pdfLink = document.createElement('a');
                pdfLink.href = dokumentasi_url;
                pdfLink.target = '_blank';
                pdfLink.textContent = 'Lihat Dokumen PDF';
                pdfLink.style.marginTop = '10px';
                pdfLink.style.display = 'inline-block';
                docContainer.appendChild(pdfLink);
            } else {
                // Jika bukan PDF (berarti gambar), tampilkan elemen gambar
                imgElement.src = dokumentasi_url;
                imgElement.style.display = 'block';
            }
        } else {
            // 4. Jika tidak ada URL, sembunyikan gambar dan tampilkan teks
            imgElement.style.display = 'none';
            noDokumentasiText.style.display = 'block';
        }

        // --- AKHIR PERBAIKAN ---

        // Tampilkan modal setelah semuanya siap
        document.getElementById('logbookDetailModal').style.display = 'block';
    }

    function hideLogbookDetailModal() {
        document.getElementById('logbookDetailModal').style.display = 'none';
    }
</script>

<?php include APP_ROOT . '/app/views/mahasiswa/includes/footer.php'; ?>
