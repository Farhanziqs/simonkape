<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mingguan KP - <?php echo htmlspecialchars($data['laporan']['periode_mingguan']); ?></title>
    <style>
        @page {
            size: A4;
            margin-top: 2.54cm;
            margin-right: 2.54cm;
            margin-bottom: 2.54cm;
            margin-left: 2.54cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        table.identitas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table.identitas td {
            padding: 5px 10px;
            vertical-align: top;
        }

        table.logbook {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 50px;
            font-size: 14px;
        }

        table.logbook th, table.logbook td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
            text-align: left;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
             align-items: flex-end;
        }

        .signature-section .box {
            width: 45%;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            min-height: 120px;

        }

        .signature-section .name-line {
            margin-top: 70px auto 0 auto;
            border-top: 1px solid #000;
            display: block;
            width: 80%;
        }

        .print-button-container {
            text-align: center;
            margin-top: 30px;
        }

        .print-button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        @media print {
            .print-button-container {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>LAPORAN MINGGUAN KERJA PRAKTEK</h2>

        <table class="identitas">
            <tr>
                <td><strong>Nama Mahasiswa</strong></td>
                <td>: <?php echo htmlspecialchars($data['laporan']['nama_mahasiswa']); ?></td>
            </tr>
            <tr>
                <td><strong>NIM</strong></td>
                <td>: <?php echo htmlspecialchars($data['laporan']['nim']); ?></td>
            </tr>
            <tr>
                <td><strong>Program Studi</strong></td>
                <td>: <?php echo htmlspecialchars($data['laporan']['program_studi']); ?></td>
            </tr>
            <tr>
                <td><strong>Instansi KP</strong></td>
                <td>: <?php echo htmlspecialchars($data['laporan']['nama_instansi']); ?></td>
            </tr>
            <tr>
                <td><strong>Periode</strong></td>
                <td>: <?php echo htmlspecialchars($data['laporan']['periode_mingguan']); ?></td>
            </tr>
        </table>

        <table class="logbook">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Hari / Tanggal</th>
                    <th width="50%">Uraian Kegiatan</th>
                    <th width="40%">Dokumentasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['logbooks_in_report'])): ?>
                    <?php $no = 1; foreach ($data['logbooks_in_report'] as $logbook): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars(date('l, d F Y', strtotime($logbook['tanggal']))); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($logbook['uraian_kegiatan'])); ?></td>
                            <td>
                                <?php if ($logbook['dokumentasi']): ?>
                                    <?php if (strtolower(pathinfo($logbook['dokumentasi'], PATHINFO_EXTENSION)) === 'pdf'): ?>
                                        <a href="<?php echo BASE_URL . $logbook['dokumentasi']; ?>" target="_blank">Lihat PDF</a>
                                    <?php else: ?>
                                        <img src="<?php echo BASE_URL . $logbook['dokumentasi']; ?>" alt="Dokumentasi" style="max-width:100px; border:1px solid #ccc;">
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">Tidak ada data logbook</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h4 style="text-align:center">Mengetahui,</h3>
        <table style="width: 80%; margin-top:60px; border-collapse:collapse;">
            <tr>
                <td style="text-align:center; border:none;">
                    Dosen Pembimbing KP
                </td>
                <td style="text-align:center; border:none;">
                    Mahasiswa KP
                </td>
            </tr>
            <tr>
                <td style="height:60px; border:none;"></td>
                <td style="height:60px; border:none;"></td>
            </tr>
            <tr>
                <td style="text-align:center; border:none; font-weight:bold; text-transform:uppercase;">
                    <?php echo htmlspecialchars($data['laporan']['nama_dosen'] ?? '-'); ?>
                </td>
                <td style="text-align:center; border:none; font-weight:bold; text-transform:uppercase;">
                    <?php echo htmlspecialchars($data['laporan']['nama_mahasiswa']); ?>
                </td>
            </tr>
            <tr>
                <td style="text-align:center; border:none;">
                    NIDN: <?php echo htmlspecialchars($data['laporan']['nidn'] ?? '-'); ?>
                </td>
                <td style="text-align:center; border:none;">
                    NIM: <?php echo htmlspecialchars($data['laporan']['nim']); ?>
                </td>
            </tr>
        </table>
        <!-- <div class="signature-section">
            <div class="box">
                <p>Mengetahui,</p>
                <p>Dosen Pembimbing KP</p>
                <div class="name-line"><?php echo htmlspecialchars($data['laporan']['nama_dosen'] ?? ''); ?></div>
                <p>NIDN: <?php echo htmlspecialchars($data['laporan']['nidn'] ?? '-'); ?></p>
            </div>
            <div class="box">
                <p>Mahasiswa KP</p>
                <div class="name-line"><?php echo htmlspecialchars($data['laporan']['nama_mahasiswa']); ?></div>
                <p>NIM: <?php echo htmlspecialchars($data['laporan']['nim']); ?></p>
            </div>
        </div> -->
    </div>

    <div class="print-button-container">
        <button class="print-button" onclick="window.print()">Cetak Laporan</button>
    </div>
</body>
</html>
<?php
// This is the end of the file. The code above generates a printable report for weekly KP (Kerja Praktek) activities of a student, including their personal information, the activities they performed during the week, and a section for signatures from both the student and their supervising lecturer. The report is styled with CSS for clarity and printability.
// The report includes a button to print the document, which is hidden during printing to ensure a clean output. The data is dynamically populated from the `$data` array, which is expected to be passed to this view from the controller.
// The report is designed to be user-friendly and visually appealing, with a clear structure that separates the student's information, the activities performed, and the signatures required. The use of tables ensures that the data is presented in an organized manner, making it easy to read and understand.
// The CSS styles are applied to ensure that the report looks professional when printed, with appropriate margins, font choices, and spacing. The report is intended for use by students at the Universitas Dayanu Ikhsanuddin who are completing their Kerja Praktek (internship) as part of their academic requirements.
