<?php
// simonkapedb/app/controllers/MahasiswaController.php
require_once APP_ROOT . '/app/libraries/dompdf/autoload.inc.php';
            use Dompdf\Dompdf;
            use Dompdf\Options;

class MahasiswaController extends Controller {
    private $mahasiswaModel;
    private $absenModel;
    private $logbookModel;
    private $laporanMingguanModel;

    public function __construct() {
        // Cek apakah user sudah login dan role-nya mahasiswa
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'mahasiswa') {
            header('Location: ' . BASE_URL . '/auth');
            exit();
        }

        $this->mahasiswaModel = $this->model('Mahasiswa');
        $this->absenModel = $this->model('Absen');
        $this->logbookModel = $this->model('Logbook');
        $this->laporanMingguanModel = $this->model('LaporanMingguan');

        // Set zona waktu
        date_default_timezone_set(TIMEZONE);
    }

    public function index() {
        $mahasiswa_id = $_SESSION['specific_user_id'];
        $mahasiswa_data = $this->mahasiswaModel->getMahasiswaById($mahasiswa_id);

         // Inisialisasi model PenempatanKp
        $penempatanModel = $this->model('PenempatanKp');

        // Ambil data penempatan berdasarkan ID mahasiswa
        $penempatan_data = $penempatanModel->getPenempatanByMahasiswaId($mahasiswa_id);

        $anggota_kelompok = [];
        // Jika mahasiswa sudah ditempatkan, cari anggota kelompok lainnya
        if ($penempatan_data) {
            $anggota_kelompok = $penempatanModel->getMahasiswaByPenempatanId($penempatan_data['id']);
        }

        $data = [
            'title' => 'Dashboard Mahasiswa',
            'active_menu' => 'dashboard',
            'mahasiswa' => $mahasiswa_data,
            'penempatan' => $penempatan_data,
            'anggota_kelompok' => $anggota_kelompok,
        ];
        $this->view('mahasiswa/dashboard', $data);
    }

    // --- Absen Harian ---
    public function absen() {
        $mahasiswa_id = $_SESSION['specific_user_id'];
        $tanggal_sekarang = date('Y-m-d');

        $data = [
            'title' => 'Absen Harian',
            'tanggal_sekarang' => $tanggal_sekarang,
            'waktu_sekarang' => date('H:i:s'),
            'absensi_hari_ini' => $this->absenModel->getAbsenByMahasiswaAndDate($mahasiswa_id, $tanggal_sekarang),
            'riwayat_absen' => $this->absenModel->getRiwayatAbsenByMahasiswaId($mahasiswa_id),
            'error' => '',
            'success' => ''
        ];

        // Logika untuk menentukan status "Alpha" jika belum absen sampai akhir hari
        // Ini bisa dilakukan oleh cron job atau saat halaman dashboard admin dibuka
        // Untuk demo, kita bisa panggil di sini, tapi di production lebih baik dengan cron.
        $this->checkAndMarkAlpha();

        $this->view('mahasiswa/absen/index', $data);
    }

    public function prosesAbsen() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $mahasiswa_id = $_SESSION['specific_user_id'];
            $tanggal_sekarang = date('Y-m-d');
            $waktu_absen = date('H:i:s');
            $status_kehadiran = $_POST['status_kehadiran'] ?? 'Hadir'; // Default Hadir

            // Cek apakah sudah absen hari ini
            $sudah_absen = $this->absenModel->getAbsenByMahasiswaAndDate($mahasiswa_id, $tanggal_sekarang);

            if ($sudah_absen) {
                // Jika statusnya sudah Hadir, tidak bisa diubah
                if ($sudah_absen['status_kehadiran'] == 'Hadir') {
                     $_SESSION['error_message'] = 'Anda sudah melakukan absensi hari ini dengan status Hadir.';
                } else {
                    // Jika statusnya Izin/Sakit/Alpha, bisa diupdate jika tombol "Absen Sekarang" ditekan
                    // Logika: hanya bisa update jika statusnya bukan 'Hadir' dan tombol absen ditekan
                    // Jika tombol Izin/Sakit ditekan, maka update status_kehadiran
                    if ($status_kehadiran == 'Hadir') {
                        // Cek batas waktu absen jika statusnya Hadir
                        $jam_absen_saat_ini = (int)date('H');
                        if ($jam_absen_saat_ini < ABSEN_START_HOUR || $jam_absen_saat_ini >= ABSEN_END_HOUR) {
                            $_SESSION['error_message'] = 'Anda tidak bisa absen Hadir di luar jam ' . sprintf('%02d:00', ABSEN_START_HOUR) . ' - ' . sprintf('%02d:00', ABSEN_END_HOUR) . '.';
                        } else {
                            if ($this->absenModel->updateAbsenStatus($sudah_absen['id'], 'Hadir')) {
                                $_SESSION['success_message'] = 'Absensi berhasil diperbarui menjadi Hadir.';
                            } else {
                                $_SESSION['error_message'] = 'Gagal memperbarui absensi.';
                            }
                        }
                    } else { // Jika status_kehadiran adalah Izin atau Sakit
                         if ($this->absenModel->updateAbsenStatus($sudah_absen['id'], $status_kehadiran)) {
                            $_SESSION['success_message'] = 'Status absensi berhasil diperbarui menjadi ' . $status_kehadiran . '.';
                        } else {
                            $_SESSION['error_message'] = 'Gagal memperbarui status absensi.';
                        }
                    }
                }
            } else {
                // Belum absen hari ini, insert data baru
                $data_absen = [
                    'mahasiswa_id' => $mahasiswa_id,
                    'tanggal' => $tanggal_sekarang,
                    'waktu_absen' => $waktu_absen,
                    'status_kehadiran' => $status_kehadiran
                ];

                if ($status_kehadiran == 'Hadir') {
                    // Cek batas waktu absen
                    $jam_absen_saat_ini = (int)date('H');
                    if ($jam_absen_saat_ini < ABSEN_START_HOUR || $jam_absen_saat_ini >= ABSEN_END_HOUR) {
                        $data_absen['status_kehadiran'] = 'Alpha'; // Otomatis Alpha jika di luar jam
                        $_SESSION['error_message'] = 'Anda absen di luar jam yang ditentukan (' . sprintf('%02d:00', ABSEN_START_HOUR) . ' - ' . sprintf('%02d:00', ABSEN_END_HOUR) . '). Absensi Anda dicatat sebagai Alpha.';
                    }
                }

                if ($this->absenModel->addAbsen($data_absen)) {
                    if (!isset($_SESSION['error_message'])) { // Jangan timpa pesan error jika sudah ada
                         $_SESSION['success_message'] = 'Absensi berhasil dicatat sebagai ' . $data_absen['status_kehadiran'] . '.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Gagal mencatat absensi.';
                }
            }
        }
        header('Location: ' . BASE_URL . '/mahasiswa/absen');
        exit();
    }

    private function checkAndMarkAlpha() {
        // Fungsi ini bisa dipanggil setiap hari oleh cron job,
        // atau saat admin/mahasiswa login (untuk kemudahan demo)
        $mahasiswa_id = $_SESSION['specific_user_id'];
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $today = date('Y-m-d');

        // Untuk setiap mahasiswa, cek apakah ada absen untuk kemarin
        $absen_kemarin = $this->absenModel->getAbsenByMahasiswaAndDate($mahasiswa_id, $yesterday);

        // Jika belum ada absen untuk kemarin dan sekarang sudah hari berikutnya
        // Dan pastikan bukan hari libur (Sabtu, Minggu) - ini bisa disempurnakan
        $dayOfWeek = date('N', strtotime($yesterday)); // 1 (for Monday) through 7 (for Sunday)

        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Hanya hari kerja Senin-Jumat
            if (!$absen_kemarin) {
                // Jika belum absen kemarin, tandai sebagai Alpha
                $data_alpha = [
                    'mahasiswa_id' => $mahasiswa_id,
                    'tanggal' => $yesterday,
                    'waktu_absen' => '00:00:00', // Waktu default untuk alpha
                    'status_kehadiran' => 'Alpha'
                ];
                $this->absenModel->addAbsen($data_alpha);
            }
        }
    }


    // --- Logbook Harian ---
    public function logbook() {
        $mahasiswa_id = $_SESSION['specific_user_id'];
        $tanggal_sekarang = date('Y-m-d');

        $data = [
            'title' => 'Logbook Harian',
            'tanggal_sekarang' => $tanggal_sekarang,
            // 'logbook_hari_ini' => $this->logbookModel->getLogbookByMahasiswaAndDate($mahasiswa_id, $tanggal_sekarang),
            'riwayat_logbook' => $this->logbookModel->getRiwayatLogbookByMahasiswaId($mahasiswa_id),
            'error' => '',
            'success' => ''
        ];
        $this->view('mahasiswa/logbook/index', $data);
    }

    public function prosesLogbook() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $mahasiswa_id = $_SESSION['specific_user_id'];
            // $tanggal_sekarang = date('Y-m-d');
            $tanggal_logbook = trim($_POST['tanggal_logbook'] ?? ''); // Ambil tanggal dari form
            $uraian_kegiatan = trim($_POST['uraian_kegiatan'] ?? '');
            $dokumentasi_path = null;

            // Validasi input
            // if (empty($tanggal_logbook) || empty($uraian_kegiatan)) {
            //     $_SESSION['error_message'] = 'Tanggal dan uraian kegiatan tidak boleh kosong.';
            //     header('Location: ' . BASE_URL . '/mahasiswa/logbook');
            //     exit();
            // }
            if (empty($tanggal_logbook) || empty($uraian_kegiatan)) {
                $this->tampilkanHalamanLogbook($mahasiswa_id, ['error_message' => 'Tanggal dan uraian kegiatan tidak boleh kosong.']);
                exit();
            }
            // Cek apakah sudah membuat logbook hari ini
            $sudah_logbook = $this->logbookModel->getLogbookByMahasiswaAndDate($mahasiswa_id, $tanggal_logbook);
            if ($sudah_logbook) {
                $this->tampilkanHalamanLogbook($mahasiswa_id, ['error_message' => 'Anda sudah mengisi logbook harian untuk tanggal ' . date('d-m-Y', strtotime($tanggal_logbook)) . '.']);
                exit();
            }

            // if (empty($uraian_kegiatan)) {
            //     $_SESSION['error_message'] = 'Uraian kegiatan tidak boleh kosong.';
            //     header('Location: ' . BASE_URL . '/mahasiswa/logbook');
            //     exit();
            // }

            // Handle file upload
            if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] == UPLOAD_ERR_OK) {
                $uploadDir = APP_ROOT . '/public/uploads/logbook/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileExtension = pathinfo($_FILES['dokumentasi']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('logbook_') . '.' . $fileExtension;
                $targetFilePath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['dokumentasi']['tmp_name'], $targetFilePath)) {
                    $dokumentasi_path = '/uploads/logbook/' . $fileName;
                }
            }

            $data_logbook = [
                'mahasiswa_id' => $mahasiswa_id,
                // 'tanggal' => $tanggal_sekarang,
                'tanggal' => $tanggal_logbook, // Gunakan tanggal dari form
                'uraian_kegiatan' => $uraian_kegiatan,
                'dokumentasi' => $dokumentasi_path
            ];

            if ($this->logbookModel->addLogbook($data_logbook)) {
                $this->tampilkanHalamanLogbook($mahasiswa_id, ['success_message' => 'Logbook harian untuk tanggal ' . date('d-m-Y', strtotime($tanggal_logbook)) . ' berhasil disimpan.']);
            } else {
                $this->tampilkanHalamanLogbook($mahasiswa_id, ['error_message' => 'Gagal menyimpan logbook harian.']);
            }
        } else {
            header('Location: ' . BASE_URL . '/mahasiswa/logbook');
            exit();
        }
    }

    private function tampilkanHalamanLogbook($mahasiswa_id, $pesan = []) {
        $data = [
            'title' => 'Logbook Harian',
            'riwayat_logbook' => $this->logbookModel->getRiwayatLogbookByMahasiswaId($mahasiswa_id),
        ];
        // Gabungkan pesan notifikasi ke data
        $data = array_merge($data, $pesan);
        $this->view('mahasiswa/logbook/index', $data);
    }



    // --- Laporan Mingguan ---
    public function laporan() {
        $mahasiswa_id = $_SESSION['specific_user_id'];
        $mahasiswa_data = $this->mahasiswaModel->getMahasiswaById($mahasiswa_id);

        $data = [
            'title' => 'Laporan Mingguan',
            'riwayat_laporan_mingguan' => $this->laporanMingguanModel->getLaporanByMahasiswaId($mahasiswa_id),
            'dosen_pembimbing_id' => $mahasiswa_data['dosen_pembimbing_id'],
            'error' => '',
            'success' => ''
        ];

        // Atur tanggal default untuk form (misal: 7 hari terakhir)
        $data['start_date'] = date('Y-m-d', strtotime('-6 days'));
        $data['end_date'] = date('Y-m-d');

        // Jika form filter tanggal disubmit
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['filter_logbook'])) {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            // Perbarui tanggal di data agar form mengingat pilihan terakhir
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            // Validasi tanggal
            if (strtotime($end_date) < strtotime($start_date)) {
                $_SESSION['error_message'] = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
                // Redirect untuk menghindari resubmit form dengan F5
                header('Location: ' . BASE_URL . '/mahasiswa/laporan');
                exit();
            }

            // Ambil logbook berdasarkan rentang tanggal yang dipilih
            $data['logbooks_available_for_report'] = $this->logbookModel->getLogbooksByDateRange($mahasiswa_id, $start_date, $end_date);

            // Buat string periode untuk ditampilkan
            $data['periode_mingguan'] = date('d M Y', strtotime($start_date)) . ' - ' . date('d M Y', strtotime($end_date));
        }


        // Logika untuk menentukan rentang tanggal laporan mingguan berikutnya
        // Misal: laporan minggu ke-X adalah hari Senin-Jumat dari minggu tersebut
        // Untuk kemudahan, kita bisa ambil logbook dari 5 hari kerja terakhir (Senin-Jumat)
        // $today = new DateTime(date('Y-m-d'));
        // $dayOfWeek = (int)$today->format('N'); // 1 (for Monday) through 7 (for Sunday)

        // // Jika hari ini Senin (1), kita bisa mulai mencari logbook dari Senin minggu lalu
        // // Jika hari ini Minggu (7), kita bisa mencari logbook dari Senin minggu ini hingga Jumat minggu ini
        // // Atau lebih sederhana, tentukan rentang 7 hari ke belakang (atau 5 hari kerja)
        // $end_date_for_logbook = clone $today;
        // // Kita ingin logbook dari seminggu ke belakang
        // $start_date_for_logbook = (clone $today)->modify('-6 days'); // 7 hari termasuk hari ini

        // // Sesuaikan jika hanya ingin hari kerja (Senin-Jumat)
        // $logbooks_available_for_report = [];
        // $current_date_iter = clone $start_date_for_logbook;
        // while ($current_date_iter <= $end_date_for_logbook) {
        //     $dayOfWeekIter = (int)$current_date_iter->format('N');
        //     if ($dayOfWeekIter >= 1 && $dayOfWeekIter <= 5) { // Hanya hari kerja
        //          $logbook_data = $this->logbookModel->getLogbookByMahasiswaAndDate($mahasiswa_id, $current_date_iter->format('Y-m-d'));
        //          if ($logbook_data) {
        //              $logbooks_available_for_report[] = $logbook_data;
        //          }
        //     }
        //     $current_date_iter->modify('+1 day');
        // }

        // $data['logbooks_available_for_report'] = $logbooks_available_for_report;

        // // Tentukan periode mingguan otomatis (misal: "29 Juli - 02 Agustus 2024")
        // if (!empty($logbooks_available_for_report)) {
        //     $first_date = new DateTime($logbooks_available_for_report[0]['tanggal']);
        //     $last_date = new DateTime($logbooks_available_for_report[count($logbooks_available_for_report) - 1]['tanggal']);
        //     $data['periode_mingguan_otomatis'] = $first_date->format('d M') . ' - ' . $last_date->format('d M Y');
        // } else {
        //     $data['periode_mingguan_otomatis'] = 'Belum ada logbook yang cukup untuk laporan.';
        // }


        $this->view('mahasiswa/laporan/index', $data);
    }

    public function buatLaporanMingguan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $mahasiswa_id = $_SESSION['specific_user_id'];
            $mahasiswa_data = $this->mahasiswaModel->getMahasiswaById($mahasiswa_id); // ini baru
            $dosen_pembimbing_id = $mahasiswa_data['dosen_pembimbing_id'] ?? null;
            $periode_mingguan = $_POST['periode_mingguan'] ?? '';
            $selected_logbook_ids = $_POST['selected_logbook_ids'] ?? [];

            if (empty($dosen_pembimbing_id)) {
                $_SESSION['error_message'] = 'Dosen Pembimbing belum diatur untuk Anda. Mohon hubungi admin.';
                header('Location: ' . BASE_URL . '/mahasiswa/laporan');
                exit();
            }

            if (empty($periode_mingguan) || empty($selected_logbook_ids) || count($selected_logbook_ids) < 5) {
                $_SESSION['error_message'] = 'Periode mingguan tidak valid atau logbook kurang dari 5 hari kerja.';
                header('Location: ' . BASE_URL . '/mahasiswa/laporan');
                exit();
            }

            // 1. Kumpulkan semua data yang dibutuhkan untuk template PDF (cetak.php)
            $mahasiswa_data = $this->mahasiswaModel->getMahasiswaById($mahasiswa_id);
            $logbooks_in_report = [];
            foreach ($selected_logbook_ids as $logbook_id) {
                $logbook_detail = $this->logbookModel->getLogbookById($logbook_id);
                if ($logbook_detail) {
                    $logbooks_in_report[] = $logbook_detail;
                }
            }

            // Urutkan logbook berdasarkan tanggal
            usort($logbooks_in_report, function($a, $b) {
                return strtotime($a['tanggal']) - strtotime($b['tanggal']);
            });

            // Siapkan array data untuk dilempar ke view cetak
            $data_for_pdf = [
                'laporan' => [
                    'periode_mingguan' => $periode_mingguan,
                    'nama_mahasiswa'   => $mahasiswa_data['nama_lengkap'],
                    'nim'              => $mahasiswa_data['nim'],
                    'program_studi'    => $mahasiswa_data['program_studi'],
                    'nama_instansi'    => $mahasiswa_data['nama_instansi'],
                    'nama_dosen'       => $mahasiswa_data['nama_dosen'],
                    'nidn'             => $mahasiswa_data['nidn'],
                ],
                'logbooks_in_report' => $logbooks_in_report
            ];

            // 2. Muat konten HTML dari view 'cetak.php' ke dalam variabel
            ob_start();
            $this->view('mahasiswa/laporan/cetak', $data_for_pdf);
            $html = ob_get_clean();

            // Hilangkan tombol cetak dari HTML sebelum di-render ke PDF
            $html = str_replace('<div class="print-button-container">', '<div class="print-button-container" style="display:none;">', $html);

            // 3. Panggil library dompdf


            // 4. Konfigurasi dan buat PDF
            $options = new Options();
            $options->set('isRemoteEnabled', TRUE); // WAJIB, agar gambar dari URL bisa dimuat
            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // 5. Simpan file PDF ke server
            $fileName = 'laporan_mingguan_' . $mahasiswa_id . '_' . str_replace(' ', '_', $periode_mingguan) . '.pdf'; // Ganti ekstensi menjadi .pdf
            $filePath = APP_ROOT . '/public/uploads/laporan_mingguan/' . $fileName;
            file_put_contents($filePath, $dompdf->output());

            // 6. Simpan informasi laporan ke database
            $data_laporan = [
                'mahasiswa_id'        => $mahasiswa_id,
                'periode_mingguan'    => $periode_mingguan,
                'file_laporan'        => '/uploads/laporan_mingguan/' . $fileName, // Path yang benar
                'status_laporan'      => 'Menunggu Persetujuan',
                'dosen_pembimbing_id' => $dosen_pembimbing_id
            ];

            if ($this->laporanMingguanModel->addLaporan($data_laporan)) {
                $_SESSION['success_message'] = 'Laporan mingguan dalam format PDF berhasil dibuat dan dikirim.';
            } else {
                $_SESSION['error_message'] = 'Gagal menyimpan data laporan mingguan ke database.';
            }
        }
        header('Location: ' . BASE_URL . '/mahasiswa/laporan');
        exit();
    }

    public function cetakLaporanMingguan($laporan_id) {
        $laporan_data = $this->laporanMingguanModel->getLaporanById($laporan_id);

        if (!$laporan_data || $laporan_data['mahasiswa_id'] != $_SESSION['specific_user_id']) {
            $_SESSION['error_message'] = 'Laporan tidak ditemukan atau Anda tidak memiliki akses.';
            header('Location: ' . BASE_URL . '/mahasiswa/laporan');
            exit();
        }

        // Ambil logbook yang terkait dengan periode laporan ini
        // Kita perlu mencari logbook berdasarkan rentang tanggal periode_mingguan
        // Ini perlu parsing string periode_mingguan
        $periode_parts = explode(' - ', $laporan_data['periode_mingguan']);
        $start_date_str = trim($periode_parts[0]) . ' ' . (new DateTime($periode_parts[1]))->format('Y'); // Tambahkan tahun ke tanggal mulai
        $end_date_str = trim($periode_parts[1]);

        $start_date = date('Y-m-d', strtotime($start_date_str));
        $end_date = date('Y-m-d', strtotime($end_date_str));

        $logbooks_in_report = $this->logbookModel->getLogbooksByDateRange($laporan_data['mahasiswa_id'], $start_date, $end_date);

        $data = [
            'title' => 'Preview Laporan Mingguan',
            'laporan' => $laporan_data,
            'logbooks_in_report' => $logbooks_in_report,
            'instansi_kp_nama' => $laporan_data['nama_instansi'], // Ambil dari join di getLaporanById
            'dosen_pembimbing_nama' => $laporan_data['nama_dosen'], // Ambil dari join di getLaporanById
            'dosen_nidn' => $laporan_data['nidn'],
        ];

        // Tampilkan view cetak
        $this->view('mahasiswa/laporan/cetak', $data);
    }
}
