<?php include APP_ROOT . '/app/views/dosen/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/dosen/includes/sidebar.php'; ?>

<div class="dosen-main-content">
    <div class="dosen-header">
        <h2>Lihat Logbook Harian Mahasiswa Bimbingan</h2>
    </div>

    <div class="dosen-container">
        <div class="filter-container">
            <h3>Filter Data Logbook</h3>
            <form action="<?php echo BASE_URL; ?>/dosen/logbook" method="POST">
                <div class="filter-group" style="flex-wrap: wrap;">
                    <div style="flex: 2;">
                        <label for="mahasiswa_id">Pilih Mahasiswa</label>
                        <select name="mahasiswa_id" id="mahasiswa_id" style="width: 100%;">
                            <option value="all">Semua Mahasiswa Bimbingan</option>
                            <?php foreach ($data['mahasiswa_bimbingan'] as $mhs) : ?>
                                <option value="<?php echo $mhs['id']; ?>" <?php echo ($data['selected_mahasiswa_id'] == $mhs['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($mhs['nama_lengkap'] . ' (' . $mhs['nim'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($data['start_date']); ?>" required>
                    </div>
                    <div>
                        <label for="end_date">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($data['end_date']); ?>" required>
                    </div>
                    <div>
                        <button type="submit" name="filter_logbook" class="btn btn-primary" style="padding: 9px 15px;">Lihat Data</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h3>Data Logbook Filtered</h3>
            <table>
                <thead>
                    <tr>
                        <th>NAMA MAHASISWA</th>
                        <th>TANGGAL</th>
                        <th>URAIAN KEGIATAN</th>
                        <th>DOKUMENTASI</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['logbook_data'])) : ?>
                        <?php foreach ($data['logbook_data'] as $logbook) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($logbook['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($logbook['tanggal']); ?></td>
                                <td><?php echo htmlspecialchars(substr($logbook['uraian_kegiatan'], 0, 70)) . (strlen($logbook['uraian_kegiatan']) > 70 ? '...' : ''); ?></td>
                                <td>
                                    <?php if ($logbook['dokumentasi']) : ?>
                                        <a href="<?php echo BASE_URL . htmlspecialchars($logbook['dokumentasi']); ?>" target="_blank">Lihat File</a>
                                    <?php else : ?>
                                        Tidak ada
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="showLogbookDetail(
                                        '<?php echo htmlspecialchars($logbook['tanggal'], ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($logbook['uraian_kegiatan'], ENT_QUOTES); ?>',
                                        '<?php echo $logbook['dokumentasi'] ? BASE_URL . $logbook['dokumentasi'] : ''; ?>'
                                    )">Lihat Detail</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="5">Tidak ada data tersedia.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="logbookDetailModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="hideLogbookDetailModal()">&times;</span>
        <h3>Detail Logbook Harian</h3>
        <p><strong>Tanggal:</strong> <span id="modalLogbookTanggal"></span></p>
        <p><strong>Uraian Kegiatan:</strong></p>
        <p id="modalLogbookUraian"></p>
        <div id="modalLogbookDokumentasiContainer">
            <strong>Dokumentasi:</strong><br>
            <img id="modalLogbookDokumentasi" src="" alt="Dokumentasi Kegiatan" style="display:none;">
            <p id="modalLogbookNoDokumentasi" style="display:none;">Tidak ada dokumentasi.</p>
        </div>
    </div>
</div>

<script>
    function showLogbookDetail(tanggal, uraian, dokumentasi_url) {
        document.getElementById('modalLogbookTanggal').textContent = tanggal;
        document.getElementById('modalLogbookUraian').textContent = uraian;

        const imgElement = document.getElementById('modalLogbookDokumentasi');
        const noDokumentasiText = document.getElementById('modalLogbookNoDokumentasi');
        const docContainer = document.getElementById('modalLogbookDokumentasiContainer');

        // Clear previous content
        imgElement.src = '';
        if (docContainer.querySelector('a')) {
            docContainer.querySelector('a').remove();
        }

        if (dokumentasi_url) {
            if (dokumentasi_url.toLowerCase().endsWith('.pdf')) {
                imgElement.style.display = 'none';
                const pdfLink = document.createElement('a');
                pdfLink.href = dokumentasi_url;
                pdfLink.target = '_blank';
                pdfLink.textContent = 'Lihat Dokumen PDF';
                docContainer.appendChild(pdfLink);
                noDokumentasiText.style.display = 'none';
            } else {
                imgElement.src = dokumentasi_url;
                imgElement.style.display = 'block';
                noDokumentasiText.style.display = 'none';
            }
        } else {
            imgElement.style.display = 'none';
            noDokumentasiText.style.display = 'block';
        }

        document.getElementById('logbookDetailModal').style.display = 'block';
    }

    function hideLogbookDetailModal() {
        document.getElementById('logbookDetailModal').style.display = 'none';
    }
</script>

<?php include APP_ROOT . '/app/views/dosen/includes/footer.php'; ?>
