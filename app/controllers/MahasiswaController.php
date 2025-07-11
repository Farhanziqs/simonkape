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
    private $penempatanKpModel;

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
        $this->penempatanKpModel = $this->model('PenempatanKp');
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
            if (
                empty($tanggal_logbook) ||
                empty($uraian_kegiatan) ||
                !isset($_FILES['dokumentasi']) ||
                $_FILES['dokumentasi']['error'] !== UPLOAD_ERR_OK
            ) {
                $_SESSION['error_message'] = 'Gagal memproses logbook harian untuk ' . ($tanggal_logbook ?: '[tanggal kosong]') . ' sebab tanggal, uraian kegiatan, atau dokumentasi tidak terisi.';
                header('Location: ' . BASE_URL . '/mahasiswa/logbook');
                exit();
            }
            // Cek apakah sudah membuat logbook hari ini
            $sudah_logbook = $this->logbookModel->getLogbookByMahasiswaAndDate($mahasiswa_id, $tanggal_logbook);
            if ($sudah_logbook) {
                // $_SESSION['error_message'] = 'Anda sudah mengisi logbook harian untuk hari ini.';
                $_SESSION['error_message'] = 'Anda sudah mengisi logbook harian untuk tanggal ' . date('d-m-Y', strtotime($tanggal_logbook)) . '.';
                header('Location: ' . BASE_URL . '/mahasiswa/logbook');
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
                } else {
                    $_SESSION['error_message'] = 'Gagal mengunggah file dokumentasi.';
                    header('Location: ' . BASE_URL . '/mahasiswa/logbook');
                    exit();
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
                $_SESSION['success_message'] = 'Logbook harian untuk tanggal ' . date('d-m-Y', strtotime($tanggal_logbook)) . ' berhasil disimpan.';
                // $_SESSION['success_message'] = 'Logbook harian berhasil disimpan.';
            } else {
                $_SESSION['error_message'] = 'Gagal menyimpan logbook harian.';
            }
        }
        header('Location: ' . BASE_URL . '/mahasiswa/logbook');
        exit();
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

    public function downloadSuratPengantar() {
        $mahasiswa_id = $_SESSION['specific_user_id'];

        // Dapatkan detail mahasiswa
        $mahasiswa = $this->mahasiswaModel->getMahasiswaById($mahasiswa_id);
        if (!$mahasiswa) {
            $_SESSION['error_message'] = 'Data mahasiswa tidak ditemukan.';
            header('Location: ' . BASE_URL . '/mahasiswa');
            exit();
        }

        // Dapatkan detail penempatan KP mahasiswa, termasuk data surat yang tersimpan
        $penempatan = $this->penempatanKpModel->getPenempatanByMahasiswaId($mahasiswa_id);
        if (!$penempatan) {
            $_SESSION['error_message'] = 'Mahasiswa belum memiliki penempatan KP.';
            header('Location: ' . BASE_URL . '/mahasiswa');
            exit();
        }

        // Pastikan detail surat sudah di generate dan disimpan admin
        if (empty($penempatan['nomor_surat_kp']) || empty($penempatan['kepada_yth_kp'])) {
            $_SESSION['error_message'] = 'Surat pengantar KP belum digenerate atau dikonfigurasi oleh admin.';
            header('Location: ' . BASE_URL . '/mahasiswa');
            exit();
        }

        // Dapatkan daftar mahasiswa di kelompok penempatan yang sama (untuk lampiran)
        $mahasiswa_list_raw = $this->penempatanKpModel->getMahasiswaByPenempatanId($penempatan['id']);
        if (empty($mahasiswa_list_raw)) {
            $_SESSION['error_message'] = 'Daftar mahasiswa untuk penempatan ini tidak ditemukan.';
            header('Location: ' . BASE_URL . '/mahasiswa');
            exit();
        }

        // Siapkan data mahasiswa untuk template lampiran
        $mahasiswa_list = [];
        $no = 1;
        foreach($mahasiswa_list_raw as $mhs) {
            $mahasiswa_list[] = [
                'no' => $no++,
                'nim' => $mhs['nim'],
                'nama' => $mhs['nama_lengkap'],
                'prodi' => $mhs['program_studi']
            ];
        }

        // --- Ambil Data Surat dari Database ---
        $nomor_surat = $penempatan['nomor_surat_kp'];
        $kepada_yth_nama = $penempatan['kepada_yth_kp'];
        $alamat_tujuan = $penempatan['alamat_tujuan_kp'];
        // Tanggal surat akan diambil dari tanggal hari ini saat mahasiswa unduh,
        // atau jika ada kolom tanggal_surat_kp di DB, bisa dipakai itu.

        // ...existing code...
        $tanggal_surat_obj = new DateTime(); // Tanggal hari ini
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $tanggal_surat_formatted = $tanggal_surat_obj->format('d') . ' ' .
            $bulan[(int)$tanggal_surat_obj->format('m')] . ' ' .
            $tanggal_surat_obj->format('Y');
        // ...existing code...
        // if (class_exists('IntlDateFormatter')) {
        //     $formatter = new IntlDateFormatter(
        //         'id_ID', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Asia/Makassar', IntlDateFormatter::LONG
        //     );
        //     $formatter->setPattern('d MMMMEEEE'); // Contoh: 11 April 2025
        //     $tanggal_surat_formatted = $formatter->format($tanggal_surat_obj);
        // } else {
        //     $bulan = [ 1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember' ];
        //     $tanggal_surat_formatted = $tanggal_surat_obj->format('d') . ' ' . $bulan[(int)$tanggal_surat_obj->format('m')] . ' ' . $tanggal_surat_obj->format('Y');
        // }

        $data_template = [
            'nomor_surat' => $nomor_surat,
            'tanggal_surat' => $tanggal_surat_formatted,
            'kepada_yth' => $kepada_yth_nama,
            'alamat_tujuan' => $alamat_tujuan,
            'mahasiswa_list' => $mahasiswa_list,
            'kaprodi_nama' => 'Ir. ERY MUCHYAR HASIRI, S.Kom, M.T.',
            'kaprodi_nidn' => '0913098203',
            'instansi_nama_tujuan' => $penempatan['nama_instansi'],
            'tanggal_mulai_kp' => $penempatan['tanggal_mulai'],
            'tanggal_selesai_kp' => $penempatan['tanggal_selesai'],
            // 'logo_base64' tidak perlu dikirim karena sudah di hardcode di template
        ];

        // Mulai output buffering untuk menangkap HTML dari view
        ob_start();
        $this->view('surat_pengantar/template', $data_template);
        $html = ob_get_clean();

        // Inisialisasi Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Times New Roman');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF ke browser
        $filename = "Surat Pengantar KP - " . str_replace(' ', '_', $mahasiswa['nama_lengkap']) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => false]);

        exit();
    }

    // --- Download Surat Penarikan KP ---
    public function downloadSuratPenarikan() {
        $mahasiswa_id = $_SESSION['specific_user_id'];

        // Dapatkan detail mahasiswa
        $mahasiswa = $this->mahasiswaModel->getMahasiswaById($mahasiswa_id);
        if (!$mahasiswa) {
            $_SESSION['error_message'] = 'Data mahasiswa tidak ditemukan.';
            header('Location: ' . BASE_URL . '/mahasiswa');
            exit();
        }

        // Dapatkan detail penempatan KP mahasiswa, termasuk data surat penarikan yang tersimpan
        $penempatan = $this->penempatanKpModel->getPenempatanByMahasiswaId($mahasiswa_id);
        if (!$penempatan) {
            $_SESSION['error_message'] = 'Mahasiswa belum memiliki penempatan KP.';
            header('Location: ' . BASE_URL . '/mahasiswa');
            exit();
        }

        // Pastikan detail surat penarikan sudah di generate dan disimpan admin
        if (empty($penempatan['nomor_surat_penarikan_kp']) || empty($penempatan['kepada_yth_penarikan_kp'])) {
            $_SESSION['error_message'] = 'Surat penarikan KP belum digenerate atau dikonfigurasi oleh admin.';
            header('Location: ' . BASE_URL . '/mahasiswa');
            exit();
        }

        // Dapatkan daftar mahasiswa di kelompok penempatan yang sama (untuk surat)
        $mahasiswa_list_raw = $this->penempatanKpModel->getMahasiswaByPenempatanId($penempatan['id']);
        if (empty($mahasiswa_list_raw)) {
            $_SESSION['error_message'] = 'Daftar mahasiswa untuk penempatan ini tidak ditemukan.';
            header('Location: ' . BASE_URL . '/mahasiswa');
            exit();
        }

        // Siapkan data mahasiswa untuk template
        $mahasiswa_list = [];
        $no = 1;
        foreach($mahasiswa_list_raw as $mhs) {
            $mahasiswa_list[] = [
                'no' => $no++,
                'nama' => $mhs['nama_lengkap'], // Nama mahasiswa
                'nim' => $mhs['nim'] // NIM mahasiswa
            ];
        }

        // --- Ambil Data Surat Penarikan dari Database ---
        $nomor_surat = $penempatan['nomor_surat_penarikan_kp'];
        $kepada_yth_nama = $penempatan['kepada_yth_penarikan_kp'];
        $alamat_tujuan = $penempatan['alamat_tujuan_penarikan_kp'];

        // Tanggal surat penarikan diambil dari DB
        $tanggal_surat_input = $penempatan['tanggal_penarikan_kp'];
        $tanggal_obj = new DateTime($tanggal_surat_input);

        if (class_exists('IntlDateFormatter')) {
            $formatter = new IntlDateFormatter(
                'id_ID', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Asia/Makassar', IntlDateFormatter::LONG
            );
            $formatter->setPattern('d MMMM yyyy');
            $tanggal_surat_formatted = $formatter->format($tanggal_obj);
        } else {
            $bulan = [ 1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember' ];
            $tanggal_surat_formatted = $tanggal_obj->format('d') . ' ' . $bulan[(int)$tanggal_obj->format('m')] . ' ' . $tanggal_obj->format('Y');
        }

        // Format tanggal mulai dan selesai KP untuk teks di surat
        $tanggal_mulai_kp_formatted = (new DateTime($penempatan['tanggal_mulai']))->format('d MMMM yyyy');
        $tanggal_selesai_kp_formatted = (new DateTime($penempatan['tanggal_selesai']))->format('d MMMM yyyy');

        // Tanggal surat balasan, karena tidak ada kolomnya lagi, bisa hardcode atau ambil dari tanggal selesai KP
        $tanggal_surat_balasan_text = $tanggal_selesai_kp_formatted; // Contoh: Gunakan tanggal selesai KP sebagai tanggal balasan

        $data_template = [
            'nomor_surat' => $nomor_surat,
            'tanggal_surat' => $tanggal_surat_formatted,
            'kepada_yth' => $kepada_yth_nama,
            'alamat_tujuan' => $alamat_tujuan,
            'mahasiswa_list' => $mahasiswa_list,
            'kaprodi_nama' => 'Ir. ERY MUCHYAR HASIRI, S.Kom, M.T.',
            'kaprodi_nidn' => '0913098203',
            'instansi_nama' => $penempatan['nama_instansi'], // Nama instansi
            'tanggal_mulai_kp' => $tanggal_mulai_kp_formatted,
            'tanggal_selesai_kp' => $tanggal_selesai_kp_formatted,
            'tanggal_surat_balasan' => $tanggal_surat_balasan_text // Dapatkan dari data yang relevan
        ];

        ob_start();
        $this->view('surat_penarikan/template', $data_template); // Memanggil template surat_penarikan
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Times New Roman');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Surat Penarikan KP - " . str_replace(' ', '_', $mahasiswa['nama_lengkap']) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => false]);

        exit();
    }
}
