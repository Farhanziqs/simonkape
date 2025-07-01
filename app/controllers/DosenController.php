<?php
// simonkapedb/app/controllers/DosenController.php

class DosenController extends Controller {
    private $dosenModel;
    private $mahasiswaModel;
    private $absenModel;
    private $logbookModel;
    private $laporanMingguanModel;

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

        date_default_timezone_set(TIMEZONE);
    }

    public function index() {
        $dosen_id = $_SESSION['specific_user_id'];
        $dosen_data = $this->dosenModel->getDosenById($dosen_id);

        $data = [
            'title' => 'Dashboard Dosen Pembimbing',
            'active_menu' => 'dashboard',
            'nama_dosen' => $dosen_data['nama_lengkap'],
            'total_mahasiswa_bimbingan' => $this->mahasiswaModel->getMahasiswaCountByDosenId($dosen_id),
            'total_laporan_menunggu' => $this->laporanMingguanModel->getTotalLaporanMenunggu($dosen_id),
            'mahasiswa_bimbingan' => $this->mahasiswaModel->getMahasiswaByDosenId($dosen_id)
        ];

        $this->view('dosen/dashboard', $data);
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

        $this->view('dosen/laporan/index', $data);
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
