<?php
// simonkapedb/app/views/surat_pengantar/template.php
// Template HTML untuk Surat Pengantar KP (PDF)

// Pastikan variabel $data sudah tersedia dari controller
// $data akan berisi:
// - 'nomor_surat'
// - 'tanggal_surat' (format Bahasa Indonesia)
// - 'kepada_yth' (nama penerima surat)
// - 'alamat_tujuan' (alamat penerima surat, jika ada)
// - 'mahasiswa_list' (array daftar mahasiswa untuk lampiran)
// - 'kaprodi_nama'
// - 'kaprodi_nidn'

// Contoh data default jika diakses langsung (untuk debugging)
if (!isset($data)) {
    $data = [
        'nomor_surat' => '037/Q.11/TI-UND/IV/2025',
        'tanggal_surat' => '11 April 2025',
        'kepada_yth' => 'KEPALA DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU KOTA BAUBAU',
        'alamat_tujuan' => 'Tempat',
        'mahasiswa_list' => [
            ['no' => 1, 'nim' => '22650065', 'nama' => 'FATIN NAFISYA', 'prodi' => 'Teknik Informatika'],
            ['no' => 2, 'nim' => '22650067', 'nama' => 'IIN RESTI', 'prodi' => 'Teknik Informatika'],
            ['no' => 3, 'nim' => '22650068', 'nama' => 'RENDI NADIRMAN', 'prodi' => 'Teknik Informatika'],
            ['no' => 4, 'nim' => '122650198', 'nama' => 'MAGI AWUD DIVA AZ ZIKRA', 'prodi' => 'Teknik Informatika'],
            ['no' => 5, 'nim' => '21650213', 'nama' => 'SULISTINA (TAMBAHAN)', 'prodi' => 'Teknik Informatika'],
        ],
        'kaprodi_nama' => 'Ir. ERY MUCHYAR HASIRI, S.Kom, M.T.',
        'kaprodi_nidn' => '0913098203',
        'instansi_nama_tujuan' => 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU KOTA BAUBAU',
        'tanggal_mulai_kp' => 'tanggal xxx',
        'tanggal_selesai_kp' => 'tanggal yyy'
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Surat Pengantar KP</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            margin: 0; /* Hapus margin body */
            padding: 0;
        }
        /* Mengatur margin sesuai dokumen asli dan ukuran kertas A4 */
        @page {
            size: A4;
            margin-top: 2.54cm;
            margin-right: 2.54cm;
            margin-bottom: 2.54cm;
            margin-left: 2.54cm;
        }
        /* Hapus .page karena @page sudah mengatur ukuran dan margin halaman */
        /* .page {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            position: relative;
        } */

        .header-kop {
            display: flex;
            align-items: center;
            margin-bottom: 0;      /* Ubah dari 20px ke 0 */
            padding-bottom: 0;     /* Ubah dari 10px ke 0 */
            line-height: 1.2; /* Padding di bawah header kop */
        }
        .header-kop img {
            width: 80px; /* Sesuaikan ukuran logo */
            height: auto;
            margin-right: 15px; /* Jarak antara logo dan teks */
        }
        .header-kop .text-container {
            flex-grow: 1; /* Membiarkan teks mengisi sisa ruang */
            text-align: center; /* Pusatkan teks */
        }
        .header-kop h1, .header-kop h2, .header-kop h3, .header-kop p {
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .header-kop .title {
            font-size: 16pt;
            font-weight: bold;
        }
        .header-kop .subtitle {
            font-size: 14pt;
        }
        .header-kop .prodi {
            font-size: 12pt;
            font-weight: bold;
        }
        .header-kop .address {
            font-size: 10pt;
        }
        .line {
            border-bottom: 2px solid black;
            margin-top: 5px;
            margin-bottom: 20px;
        }
        .content {
            text-align: justify;
            line-height: 1.5;
        }
        .info-header {
            width: 100%;
            display: table;
            margin-bottom: 15px;
        }
        .info-header > div {
            display: table-row;
        }
        .info-header span {
            display: table-cell;
            padding-right: 10px;
            vertical-align: top;
        }
        .info-header .label {
            width: 70px;
        }
        .right-align {
            text-align: right;
            margin-bottom: 20px;
        }
        /* Menggunakan table untuk TTD agar sejajar dengan tabulasi Word */
        .ttd-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse; /* Pastikan tidak ada border internal */
        }
        .ttd-table td {
            vertical-align: top;
            padding: 0; /* Hapus padding default sel tabel */
            border: none; /* Hapus border default sel tabel */
        }
        .ttd-table .spacer {
            width: 32%; /* Sesuaikan lebar spacer agar teks di kanan masuk margin */
        }
        .ttd-table .signature-block {
            width: 40%; /* Lebar blok tanda tangan */
            text-align: left; /* TTD align kiri di bloknya */
            padding-left: 20px; /* Sedikit padding agar tidak terlalu mepet */
        }
        .ttd-table .signature-block p {
            margin: 0;
        }
        .ttd-table .jabatan {
            margin-top: 50px; /* Ruang untuk tanda tangan */
            font-weight: bold;
        }
        .lampiran-header {
            text-align: center;
            margin-bottom: 20px;
            margin-top: 50px;
        }
        .lampiran-info {
            width: 100%;
            display: table;
            margin-top: 10px;
        }
        .lampiran-info > div {
            display: table-row;
        }
        .lampiran-info span {
            display: table-cell;
            padding-right: 10px;
            vertical-align: top;
        }
        .lampiran-info .label {
            width: 70px;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .student-table th, .student-table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        .student-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .student-table td.center {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="page-content">

        <div class="header-kop">
            <table style="width:100%; border:none;">
                <tr>
                    <td style="text-align:center;">
                        <span style="font-size:12pt; font-weight:bold; letter-spacing:1px;">UNIVERSITAS DAYANU IKHSANUDDIN</span><br>
                        <span style="font-size:12pt; font-weight:bold;">FAKULTAS TEKNIK</span><br>
                        <span style="font-size:12pt;">PROGRAM STUDI TEKNIK INFORMATIKA</span><br>
                        <span style="font-size:12pt; font-style:italic;">
                            Terakreditasi (S-1) No.3084/SK/BAN-PT/Ak-PPJ/S/I/2020
                        </span><br>
                        <span style="font-size:11pt; font-style:italic; font-weight:bold;">
                            Jalan Sultan Dayanu Ikhsanuddin No. 100 Telp (0402) 2821327 Baubau
                        </span>
                    </td>
                </tr>
            </table>
            <div style="border-bottom:3px solid #000; margin-top:2px; margin-bottom:8px;"></div>
        </div>

        <div class="right-align">
            Baubau, <?php echo htmlspecialchars($data['tanggal_surat']); ?>
        </div>

        <div class="info-header">
            <div>
                <span class="label">Nomor</span><span>:</span><span><?php echo htmlspecialchars($data['nomor_surat']); ?></span>
            </div>
            <div>
                <span class="label">Perihal</span><span>:</span><span>Pengantar Kerja Praktek</span>
            </div>
        </div>
        <div style="margin-top:18px; margin-bottom:0;">
                <p style="margin:0;">Kepada Yth,</p>
                <p style="margin:0; font-weight:bold; white-space:nowrap;">
                    <?php echo htmlspecialchars($data['kepada_yth']); ?>
                </p>
                <p style="margin:0;">Di-</p>
                <p style="margin:0 0 10px 40px;">Tempat</p>
        </div>

        <div class="content">
            <p>Dengan Hormat,</p>
            <p>Dalam rangka memenuhi kebutuhan Akademik pada Program Studi Teknik Informatika Fakultas Teknik Universitas Dayanu Ikhsanuddin dan untuk meningkatkan kemampuan dan keterampilan mahasiswa serta memperluas pengetahuan dan pemahaman mahasiswa mengenai disiplin ilmu dan penerapannya, serta untuk memberikan gambaran umum mengenai Dunia Kerja. Sehingga untuk memenuhi persyaratan tersebut mahasiswa wajib melaksanakan Praktek Kerja Lapangan (PKL). Maka dengan ini kami memohon kesediaan Bapak/Ibu untuk dapat memberikan kesempatan kepada Mahasiswa/i kami dalam melaksanakan Kerja Praktek (KP) sebagaimana tersebut diatas.</p>
            <p>Bersama ini kami sampaikan bahwa, kami akan mengirimkan Mahasiswa/i yang akan melaksanakan Kerja Praktek (KP) selama &plusmn; 2 bulan. Adapun daftar nama Mahasiswa tersebut terdapat pada lampiran surat ini.</p>
            <p>Demikian disampaikan atas perhatian dan kerjasama yang baik diucapkan terima kasih dan besar harapan kami Mahasiswa/i kami dapat diterima untuk dapat melaksanakan Kerja Praktek.</p>
        </div>

        <table class="ttd-table">
        <tr>
            <td class="spacer"></td>
            <td class="signature-block">
                <p style="margin-bottom:0;">Hormat kami,</p>
                <p style="margin-bottom:82px;">Ketua Program Studi</p>
                <p style="font: size 12pt; font-weight:bold; margin-bottom:0;">
                    <?php echo htmlspecialchars($data['kaprodi_nama']); ?>
                </p>
                <p style="margin-top:0;">NIDN. <?php echo htmlspecialchars($data['kaprodi_nidn']); ?></p>
            </td>
        </tr>
        </table>
    </div>

    <div class="page-content" style="page-break-before: always;">
        <div class="lampiran-header">
            <h3>LAMPIRAN SURAT PERMOHONAN KERJA PRAKTEK (KP)</h3>
        </div>

        <div class="lampiran-info">
            <div>
                <span class="label">Nomor</span><span>:</span><span><?php echo htmlspecialchars($data['nomor_surat']); ?></span>
            </div>
            <div>
                <span class="label">Tanggal</span><span>:</span><span><?php echo htmlspecialchars($data['tanggal_surat']); ?></span>
            </div>
        </div>

        <table class="student-table">
            <thead>
                <tr>
                    <th>NO.</th>
                    <th>NIM</th>
                    <th>NAMA MAHASISWA</th>
                    <th>PRODI</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php if (!empty($data['mahasiswa_list'])) : ?>
                    <?php foreach ($data['mahasiswa_list'] as $mhs) : ?>
                        <tr>
                            <td class="center"><?php echo $no++; ?>.</td>
                            <td><?php echo htmlspecialchars($mhs['nim'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($mhs['nama'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($mhs['prodi'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada data mahasiswa untuk lampiran.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <table class="ttd-table">
        <tr>
            <td class="spacer"></td>
            <td class="signature-block">
                <p style="margin-bottom:0;">Hormat kami,</p>
                <p style="margin-bottom:82px;">Ketua Program Studi</p>
                <p style="font: size 12pt; font-weight:bold; margin-bottom:0;">
                    <?php echo htmlspecialchars($data['kaprodi_nama']); ?>
                </p>
                <p style="margin-top:0;">NIDN. <?php echo htmlspecialchars($data['kaprodi_nidn']); ?></p>
            </td>
        </tr>
        </table>
    </div>

</body>
</html>
