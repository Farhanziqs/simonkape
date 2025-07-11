<?php
// simonkapedb/app/controllers/DosenController.php

class DosenController extends Controller {
    private $dosenModel;
    private $mahasiswaModel;
    private $absenModel;
    private $logbookModel;
    private $laporanMingguanModel;
    private $instansiModel;
    private $penempatanKpModel;

    public function __construct() {
        // Cek apakah user sudah login dan role-nya dosen
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'dosen') {
            header('Location: ' . BASE_URL . '/auth');
            exit();
        }

        $this->dosenModel = $this->model('Dosen');
        $this->mahasiswaModel = $this->model('Mahasiswa');
        $this->absenModel = $this->model('Absen');
        $this->logbookModel = $this->model('Logbook');
        $this->laporanMingguanModel = $this->model('LaporanMingguan');
        $this->instansiModel = $this->model('Instansi');
        $this->penempatanKpModel = $this->model('PenempatanKp');

        date_default_timezone_set(TIMEZONE);
    }

    public function index() {
        $dosen_id = $_SESSION['specific_user_id'];
        $dosen_data = $this->dosenModel->getDosenById($dosen_id);

        // --- Logic untuk Identifikasi Kaprodi ---
        $is_kaprodi = false;
        if ($dosen_data && $dosen_data['nidn'] === KAPRODI_NIDN) {
            $is_kaprodi = true;
        }

        $data = [
            'title' => 'Dashboard Dosen Pembimbing',
            'active_menu' => 'dashboard',
            'nama_dosen' => $dosen_data['nama_lengkap'],
            'total_mahasiswa_bimbingan' => $this->mahasiswaModel->getMahasiswaCountByDosenId($dosen_id),
            'total_laporan_menunggu' => $this->laporanMingguanModel->getTotalLaporanMenunggu($dosen_id),
            'mahasiswa_bimbingan' => $this->mahasiswaModel->getMahasiswaByDosenId($dosen_id),
            'is_kaprodi' => $is_kaprodi // <-- tambahkan ini!
        ];

        $this->view('dosen/dashboard', $data);
    }

    private function setKaprodiFlag(&$data) {
    $dosen_id = $_SESSION['specific_user_id'];
    $dosen_data = $this->dosenModel->getDosenById($dosen_id);
    // Ganti 'KAPRODI_NIDN' dengan NIDN kaprodi sebenarnya
    $data['is_kaprodi'] = ($dosen_data && $dosen_data['nidn'] === KAPRODI_NIDN);
    }

    // --- Menu 1: Lihat Absen Mahasiswa ---
    public function absen() {
        $dosen_id = $_SESSION['specific_user_id'];
        $data['active_menu'] = 'absen';
        $data['title'] = 'Lihat Absen Mahasiswa Bimbingan';
        $data['mahasiswa_bimbingan'] = $this->mahasiswaModel->getMahasiswaByDosenId($dosen_id);
        $data['selected_mahasiswa_id'] = 'all';
        $data['start_date'] = date('Y-m-d');
        $data['end_date'] = date('Y-m-d');
        $data['absen_data'] = [];

        // Handle filter form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['filter_absen'])) {
            $mahasiswa_filter = $_POST['mahasiswa_id'] ?? 'all';
            $start_date = $_POST['start_date'] ?? date('Y-m-d');
            $end_date = $_POST['end_date'] ?? date('Y-m-d');

            $data['selected_mahasiswa_id'] = $mahasiswa_filter;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            if ($mahasiswa_filter == 'all') {
                $data['absen_data'] = $this->absenModel->getAbsenByDosenIdAndDateRange($dosen_id, $start_date, $end_date);
            } else {
                $data['absen_data'] = $this->absenModel->getAbsenByMahasiswaAndDateRange($mahasiswa_filter, $start_date, $end_date);
            }
        }
        $this->setKaprodiFlag($data);
        $this->view('dosen/absen/index', $data);
    }

    // --- Menu 2: Lihat Logbook Harian Mahasiswa ---
    public function logbook() {
        $dosen_id = $_SESSION['specific_user_id'];
        $data['active_menu'] = 'logbook';
        $data['title'] = 'Lihat Logbook Harian Mahasiswa Bimbingan';
        $data['mahasiswa_bimbingan'] = $this->mahasiswaModel->getMahasiswaByDosenId($dosen_id);
        $data['selected_mahasiswa_id'] = 'all';
        $data['start_date'] = date('Y-m-d');
        $data['end_date'] = date('Y-m-d');
        $data['logbook_data'] = [];

        // Handle filter form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['filter_logbook'])) {
            $mahasiswa_filter = $_POST['mahasiswa_id'] ?? 'all';
            $start_date = $_POST['start_date'] ?? date('Y-m-d');
            $end_date = $_POST['end_date'] ?? date('Y-m-d');

            $data['selected_mahasiswa_id'] = $mahasiswa_filter;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;

            if ($mahasiswa_filter == 'all') {
                $data['logbook_data'] = $this->logbookModel->getLogbookByDosenIdAndDateRange($dosen_id, $start_date, $end_date);
            } else {
                $data['logbook_data'] = $this->logbookModel->getLogbooksByDateRange($mahasiswa_filter, $start_date, $end_date);
            }
        }
        $this->setKaprodiFlag($data);
        $this->view('dosen/logbook/index', $data);
    }

    // --- Menu 3: Lihat Laporan Mingguan ---
    public function laporan() {
        $dosen_id = $_SESSION['specific_user_id'];
        $data['active_menu'] = 'laporan';
        $data['title'] = 'Lihat Laporan Mingguan Mahasiswa Bimbingan';
        $data['mahasiswa_bimbingan'] = $this->mahasiswaModel->getMahasiswaByDosenId($dosen_id);
        $data['selected_mahasiswa_id'] = 'all';
        $data['selected_periode'] = 'all';
        $data['laporan_data'] = $this->laporanMingguanModel->getLaporanByDosenId($dosen_id);

        // Ambil daftar periode unik untuk filter
        $periode_list = array_unique(array_column($data['laporan_data'], 'periode_mingguan'));
        $data['periode_list'] = $periode_list;

        // Handle filter form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['filter_laporan'])) {
            $mahasiswa_filter = $_POST['mahasiswa_id'] ?? 'all';
            $periode_filter = $_POST['periode'] ?? 'all';

            $data['selected_mahasiswa_id'] = $mahasiswa_filter;
            $data['selected_periode'] = $periode_filter;

            $filtered_data = [];
            foreach($this->laporanMingguanModel->getLaporanByDosenId($dosen_id) as $laporan) {
                $is_mahasiswa_match = ($mahasiswa_filter == 'all' || $laporan['mahasiswa_id'] == $mahasiswa_filter);
                $is_periode_match = ($periode_filter == 'all' || $laporan['periode_mingguan'] == $periode_filter);
                if ($is_mahasiswa_match && $is_periode_match) {
                    $filtered_data[] = $laporan;
                }
            }
            $data['laporan_data'] = $filtered_data;
        }
        $this->setKaprodiFlag($data);
        $this->view('dosen/laporan/index', $data);
    }

    private function checkKaprodiAccess() {
        $dosen_id = $_SESSION['specific_user_id'];
        $current_dosen = $this->dosenModel->getDosenById($dosen_id);
        if (!$current_dosen || $current_dosen['nidn'] !== KAPRODI_NIDN) {
            $_SESSION['error_message'] = 'Akses ditolak. Fitur ini hanya untuk Kaprodi.';
            header('Location: ' . BASE_URL . '/dosen');
            exit();
        }
    }

    public function dataDosen() {
        $this->checkKaprodiAccess(); // Cek akses Kaprodi

        $data['active_menu'] = 'data_dosen';
        $data['title'] = 'Data Dosen Pembimbing - SIMONKAPE';
        $data['dosen_list'] = $this->dosenModel->getAllDosen();
        $data['is_kaprodi'] = true; // Konfirmasi ini untuk view
        $this->view('dosen/kaprodi_views/data_dosen', $data);
    }

    public function dataMahasiswa() {
        $this->checkKaprodiAccess(); // Cek akses Kaprodi

        $data['active_menu'] = 'data_mahasiswa';
        $data['title'] = 'Data Mahasiswa - SIMONKAPE';
        $data['mahasiswa_list'] = $this->mahasiswaModel->getAllMahasiswa();
        $data['is_kaprodi'] = true; // Konfirmasi ini untuk view
        $this->view('dosen/kaprodi_views/data_mahasiswa', $data);
    }

    public function dataInstansi() {
        $this->checkKaprodiAccess(); // Cek akses Kaprodi

        $data['active_menu'] = 'data_instansi';
        $data['title'] = 'Data Instansi - SIMONKAPE';
        $data['instansi_list'] = $this->instansiModel->getAllInstansi();
        $data['is_kaprodi'] = true; // Konfirmasi ini untuk view
        $this->view('dosen/kaprodi_views/data_instansi', $data);
    }

    public function statusKp() {
        $this->checkKaprodiAccess(); // Cek akses Kaprodi

        $data['active_menu'] = 'status_kp';
        $data['title'] = 'Status KP Mahasiswa per Instansi - SIMONKAPE';
        $raw_penempatan_details = $this->penempatanKpModel->getMahasiswaPenempatanDetails();

        $processed_penempatan = [];
        $current_instansi = null;
        $current_dosen_pembimbing = null;
        $instansi_group_index = -1;
        $dosen_group_index = -1;

        foreach ($raw_penempatan_details as $detail) {
            if ($detail['nama_instansi'] !== $current_instansi) {
                $instansi_group_index++;
                $processed_penempatan[$instansi_group_index] = [
                    'nama_instansi' => $detail['nama_instansi'],
                    'instansi_rowspan' => 0,
                    'dosen_groups' => []
                ];
                $current_instansi = $detail['nama_instansi'];
                $current_dosen_pembimbing = null;
            }

            if ($detail['nama_dosen_pembimbing'] !== $current_dosen_pembimbing) {
                $dosen_group_index = count($processed_penempatan[$instansi_group_index]['dosen_groups']);
                $processed_penempatan[$instansi_group_index]['dosen_groups'][$dosen_group_index] = [
                    'nama_dosen_pembimbing' => $detail['nama_dosen_pembimbing'],
                    'dosen_rowspan' => 0,
                    'students' => []
                ];
                $current_dosen_pembimbing = $detail['nama_dosen_pembimbing'];
            }

            $processed_penempatan[$instansi_group_index]['dosen_groups'][$dosen_group_index]['students'][] = $detail;
        }

        foreach ($processed_penempatan as $instansi_idx => $inst_group) {
            $total_instansi_rowspan = 0;
            foreach ($inst_group['dosen_groups'] as $dosen_idx => $dosen_group) {
                $num_students = count($dosen_group['students']);
                $processed_penempatan[$instansi_idx]['dosen_groups'][$dosen_idx]['dosen_rowspan'] = $num_students;
                $total_instansi_rowspan += $num_students;
            }
            $processed_penempatan[$instansi_idx]['instansi_rowspan'] = $total_instansi_rowspan;
        }

        $data['processed_penempatan_details'] = $processed_penempatan;
        $data['is_kaprodi'] = true; // Konfirmasi ini untuk view

        $this->view('dosen/kaprodi_views/status_kp', $data);
    }

    public function rekapitulasiLaporan() {
        $this->checkKaprodiAccess(); // Cek akses Kaprodi

        $data['active_menu'] = 'rekapitulasi_laporan';
        $data['title'] = 'Rekapitulasi Laporan Mingguan Mahasiswa - SIMONKAPE';
        // Ambil semua laporan mingguan, diurutkan per instansi dan menampilkan dosen pembimbing
        $data['rekap_laporan'] = $this->laporanMingguanModel->getAllLaporanMingguanWithDetails(); // Perlu method baru di model

        // Untuk pengelompokan di view, kita bisa proses datanya di controller ini
        $processed_reports = [];
        foreach ($data['rekap_laporan'] as $report) {
            $instansi_name = $report['nama_instansi'];
            $dosen_name = $report['nama_dosen']; // Nama dosen pembimbing dari penempatan_kp

            if (!isset($processed_reports[$instansi_name])) {
                $processed_reports[$instansi_name] = [
                    'instansi_details' => $report, // Simpan detail instansi
                    'dosen_groups' => []
                ];
            }
            if (!isset($processed_reports[$instansi_name]['dosen_groups'][$dosen_name])) {
                $processed_reports[$instansi_name]['dosen_groups'][$dosen_name] = [
                    'dosen_details' => $report, // Simpan detail dosen
                    'reports' => []
                ];
            }
            $processed_reports[$instansi_name]['dosen_groups'][$dosen_name]['reports'][] = $report;
        }
        $data['processed_reports'] = $processed_reports;
        $data['is_kaprodi'] = true; // Konfirmasi ini untuk view

        $this->view('dosen/kaprodi_views/rekapitulasi_laporan', $data);
    }

    public function tanggapan($laporan_id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $status = $_POST['status_laporan'] ?? 'Disetujui';
            $feedback = trim($_POST['feedback_dosen'] ?? '');

            if ($this->laporanMingguanModel->updateLaporanStatus($laporan_id, $status, $feedback)) {
                $_SESSION['success_message'] = 'Tanggapan laporan berhasil disimpan.';
            } else {
                $_SESSION['error_message'] = 'Gagal menyimpan tanggapan laporan.';
            }
        }
        header('Location: ' . BASE_URL . '/dosen/laporan');
        exit();
    }
}
