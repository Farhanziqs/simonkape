<?php
// simonkapedb/app/views/surat_penarikan/template.php
// Template HTML untuk Surat Penarikan KP (PDF)

// Pastikan variabel $data sudah tersedia dari controller
// $data akan berisi:
// - 'nomor_surat'
// - 'tanggal_surat' (format Bahasa Indonesia)
// - 'kepada_yth' (nama penerima surat)
// - 'alamat_tujuan' (alamat penerima surat, jika ada)
// - 'mahasiswa_list' (array daftar mahasiswa untuk lampiran)
// - 'kaprodi_nama'
// - 'kaprodi_nidn'
// - 'instansi_nama' (nama instansi tempat KP)
// - 'tanggal_mulai_kp'
// - 'tanggal_selesai_kp' (tanggal berakhir KP)
// - 'tanggal_surat_balasan' (tanggal masuk surat balasan, dari form admin)

// Contoh data default jika diakses langsung (untuk debugging)
if (!isset($data)) {
    $data = [
        'nomor_surat' => '152.I/Q.12/TI-UND/V/2025',
        'tanggal_surat' => '10 Juli 2025', // Tanggal generate surat penarikan
        'kepada_yth' => 'KEPALA PERPUSTAKAAN PROGRAM STUDI TEKNIK INFORMATIKA',
        'alamat_tujuan' => 'Tempat',
        'instansi_nama' => 'PERPUSTAKAAN PROGRAM STUDI TEKNIK INFORMATIKA',
        'tanggal_mulai_kp' => 'YYYY-MM-DD', // Contoh tanggal mulai KP
        'tanggal_selesai_kp' => 'YYYY-MM-DD', // Contoh tanggal selesai KP
        // 'tanggal_surat_balasan' DIHAPUS dari default data
        'mahasiswa_list' => [
            ['no' => 1, 'nama' => 'ALDIN KARAMIDA', 'nim' => '22650002'],
            ['no' => 2, 'nama' => 'LA ODE MUH FARHAN', 'nim' => '22650019'],
        ],
        'kaprodi_nama' => 'Ir. ERY MUCHYAR HASIRI, S.Kom, M.T.',
        'kaprodi_nidn' => '0913098203',
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Surat Penarikan KP</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            margin: 0; /* Hapus margin body */
            padding: 0;
        }
        @page {
            size: A4;
            margin-top: 2.54cm;
            margin-right: 2.54cm;
            margin-bottom: 2.54cm;
            margin-left: 2.54cm;
        }
        .header-kop {
            display: flex;
            align-items: center;
            margin-bottom: 0;      /* Ubah dari 20px ke 0 */
            padding-bottom: 0;     /* Ubah dari 10px ke 0 */
            line-height: 1.2; /* Padding di bawah header kop */
        }
        .header-kop td {
            vertical-align: top;
            padding: 0;
            border: none;
        }
        .header-kop .logo-cell {
            width: 80px;
            text-align: center;
        }
        .header-kop .logo-cell img {
            width: 80px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .header-kop .text-cell {
            text-align: center;
            width: 100%; /* Lebar penuh karena tidak ada logo */
            padding-left: 0; /* Hapus padding karena tidak ada logo */
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
        .ttd-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .ttd-table td {
            vertical-align: top;
            padding: 0;
            border: none;
        }
        .ttd-table .spacer {
            width: 50%; /* Sesuaikan jika perlu */
        }
        .ttd-table .signature-block {
            width: 50%; /* Sesuaikan jika perlu */
            text-align: left;
            padding-left: 20px;
        }
        .ttd-table .signature-block p {
            margin: 0;
        }
        .ttd-table .jabatan {
            margin-top: 50px;
            font-weight: bold;
        }
        .student-list-penarikan {
            list-style: none;
            padding-left: 0; /* Ubah ini menjadi 0 agar bisa diatur secara manual */
            margin-top: 10px;
            margin-bottom: 20px;
            /* Gunakan display: table untuk alignment kolom yang lebih baik */
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .student-list-penarikan li {
            margin-bottom: 5px;
            display: table-row; /* Setiap LI menjadi baris tabel */
        }
        .student-list-penarikan li span {
            display: table-cell; /* Setiap SPAN menjadi sel tabel */
            vertical-align: top;
            padding-right: 10px;
        }
        .student-list-penarikan .header-row span {
            font-weight: bold;
            padding-bottom: 5px;
            border-bottom: 0.5px solid #000;
            margin-bottom: 5px;
        }
        .student-list-penarikan .no { width: 3%; text-align: right;} /* Sesuaikan lebar */
        .student-list-penarikan .nama { width: 45%; } /* Sesuaikan lebar */
        .student-list-penarikan .nim { width: 25%; } /* Sesuaikan lebar */
    </style>
</head>
<body>

    <div>
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
                <span class="label">Perihal</span><span>:</span><span>Penarikan Kerja Praktek</span>
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
            <p>Sehubungan telah berakhirnya masa Kerja Praktek lapangan mahasiswa kami yang bertempat di Instansi/Perusahaan yang Bapak/Ibu pimpin dan merupakan syarat dalam kurikulum mata kuliah Program Strata Satu (S1), yang dilaksanakan terhitung sejak masuk surat balasan kepada kami. Maka dengan ini kami Ketua Program Studi Teknik Informatika mengajukan surat penarikan mahasiswa kami, atas nama sebagai berikut:</p>

            <ul class="student-list-penarikan">
                <li class="header-row">
                    <span class="no">No.</span>
                    <span class="nama">Nama Mahasiswa</span>
                    <span class="nim">No. Induk Mahasiswa</span>
                </li>

                <?php $no = 1; ?>
                <?php if (!empty($data['mahasiswa_list'])) : ?>
                    <?php foreach ($data['mahasiswa_list'] as $mhs) : ?>
                        <li>
                            <span class="no"><?php echo $no++; ?>.</span>
                            <span class="nama"><?php echo htmlspecialchars($mhs['nama'] ?? ''); ?></span>
                            <span class="nim"><?php echo htmlspecialchars($mhs['nim'] ?? ''); ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li><span colspan="3">Tidak ada data mahasiswa untuk penarikan ini.</span></li>
                <?php endif; ?>
            </ul>

            <p>Demikian penyampaian ini, atas perhatian dan bantuan serta kerjasama yang baik selama ini kami ucapkan terima kasih.</p>
        </div>

        <table class="ttd-table">
        <tr>
            <td class="spacer"></td>
            <td class="signature-block">
                <p style="margin-bottom:0;">Hormat kami,</p>
                <p style="margin-bottom:82px;">Ketua Program Studi</p>
                <p style="font: size 12pt; font-weight:bold; margin-bottom:0; white-space:nowrap;">
                    <?php echo htmlspecialchars($data['kaprodi_nama']); ?>
                </p>
                <p style="margin-top:0;">NIDN. <?php echo htmlspecialchars($data['kaprodi_nidn']); ?></p>
            </td>
        </tr>
        </table>
    </div>

</body>
</html>
