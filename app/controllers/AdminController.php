<?php
// simonkapedb/app/controllers/AdminController.php

class AdminController extends Controller {
    private $mahasiswaModel;
    private $dosenModel;
    private $instansiModel;
    private $userModel;
    private $penempatanKpModel; // Model baru

    public function __construct() {
        // Cek apakah user sudah login dan role-nya admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: ' . BASE_URL . '/auth');
            exit();
        }

        $this->mahasiswaModel = $this->model('Mahasiswa');
        $this->dosenModel = $this->model('Dosen');
        $this->instansiModel = $this->model('Instansi');
        $this->userModel = $this->model('User');
        $this->penempatanKpModel = $this->model('PenempatanKp'); // Inisialisasi model baru
    }

    // Halaman Dashboard Admin
    public function index()  {
        $data['active_menu'] = 'dashboard';
        $data = [
            'total_mahasiswa_aktif_kp' => $this->mahasiswaModel->getTotalMahasiswaAktifKp(),
            'total_dosen_pembimbing' => $this->dosenModel->getTotalDosenPembimbing(),
            'total_instansi_terdaftar' => $this->instansiModel->getTotalInstansiTerdaftar(),
            'laporan_mingguan_terbaru' => $this->mahasiswaModel->getTotalLaporanMingguanPerluVerifikasi(), // Buat method ini nanti
            'username' => $_SESSION['username'],
            'title' => 'Dashboard Admin - SIMONKAPE',
        ];
        $this->view('admin/dashboard', $data);
    }

    // --- Manajemen Mahasiswa ---
    public function mahasiswa() {
        $data['active_menu'] = 'mahasiswa';
        $data['mahasiswa'] = $this->mahasiswaModel->getAllMahasiswa();
        $data['instansi_list'] = $this->instansiModel->getAllInstansi();
        $data['dosen_list'] = $this->dosenModel->getAllDosen();
        $this->view('admin/mahasiswa/index', $data);
    }

    public function tambahMahasiswa() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nim' => trim($_POST['nim']),
                'nama_lengkap' => trim($_POST['nama_lengkap']),
                'program_studi' => trim($_POST['program_studi']),
                'instansi_id' => !empty($_POST['instansi_id']) ? $_POST['instansi_id'] : null,
                'dosen_pembimbing_id' => !empty($_POST['dosen_pembimbing_id']) ? $_POST['dosen_pembimbing_id'] : null,
                'status_kp' => trim($_POST['status_kp']),
                'error' => ''
            ];

            // Validasi sederhana
            if (empty($data['nim']) || empty($data['nama_lengkap']) || empty($data['program_studi'])) {
                $data['error'] = 'NIM, Nama Lengkap, dan Program Studi tidak boleh kosong.';
                $this->view('admin/mahasiswa/index', $data); // Tampilkan kembali form dengan error
                return;
            }

            if ($this->mahasiswaModel->getMahasiswaByNim($data['nim'])) {
                $data['error'] = 'NIM sudah terdaftar.';
                $this->view('admin/mahasiswa/index', $data);
                return;
            }

            if ($this->mahasiswaModel->addMahasiswa($data)) {
                // Buat akun user otomatis
                $hashed_password = password_hash(DEFAULT_USER_PASSWORD, PASSWORD_BCRYPT);
                $mahasiswa_baru = $this->mahasiswaModel->getMahasiswaByNim($data['nim']); // Ambil data mahasiswa yang baru dibuat untuk mendapatkan ID
                $this->userModel->addUser($data['nim'], $hashed_password, 'mahasiswa', $mahasiswa_baru['id']);
                header('Location: ' . BASE_URL . '/admin/mahasiswa');
                exit();
            } else {
                $data['error'] = 'Gagal menambahkan mahasiswa.';
                $this->view('admin/mahasiswa/index', $data);
            }
        } else {
            header('Location: ' . BASE_URL . '/admin/mahasiswa'); // Redirect jika bukan POST request
            exit();
        }
    }

    public function editMahasiswa($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'nim' => trim($_POST['nim']),
                'nama_lengkap' => trim($_POST['nama_lengkap']),
                'program_studi' => trim($_POST['program_studi']),
                'instansi_id' => !empty($_POST['instansi_id']) ? $_POST['instansi_id'] : null,
                'dosen_pembimbing_id' => !empty($_POST['dosen_pembimbing_id']) ? $_POST['dosen_pembimbing_id'] : null,
                'status_kp' => trim($_POST['status_kp']),
                'error' => ''
            ];

            // Validasi sederhana
            if (empty($data['nim']) || empty($data['nama_lengkap']) || empty($data['program_studi'])) {
                $data['error'] = 'NIM, Nama Lengkap, dan Program Studi tidak boleh kosong.';
                $data['mahasiswa_edit'] = $this->mahasiswaModel->getMahasiswaById($id); // Load data kembali
                $data['instansi_list'] = $this->instansiModel->getAllInstansi();
                $data['dosen_list'] = $this->dosenModel->getAllDosen();
                $this->view('admin/mahasiswa/index', array_merge($data, ['mahasiswa' => $this->mahasiswaModel->getAllMahasiswa()]));
                return;
            }

            // Cek NIM duplikat kecuali untuk NIM itu sendiri
            $existingMahasiswa = $this->mahasiswaModel->getMahasiswaByNim($data['nim']);
            if ($existingMahasiswa && $existingMahasiswa['id'] != $id) {
                $data['error'] = 'NIM sudah terdaftar untuk mahasiswa lain.';
                $data['mahasiswa_edit'] = $this->mahasiswaModel->getMahasiswaById($id);
                $data['instansi_list'] = $this->instansiModel->getAllInstansi();
                $data['dosen_list'] = $this->dosenModel->getAllDosen();
                $this->view('admin/mahasiswa/index', array_merge($data, ['mahasiswa' => $this->mahasiswaModel->getAllMahasiswa()]));
                return;
            }

            if ($this->mahasiswaModel->updateMahasiswa($data)) {
                 // Update username di tabel users jika NIM berubah
                $current_user = $this->userModel->getUserByUserIdAndRole($id, 'mahasiswa');
                if ($current_user && $current_user['username'] != $data['nim']) {
                    $this->userModel->updateUsername($current_user['id'], $data['nim']);
                }
                header('Location: ' . BASE_URL . '/admin/mahasiswa');
                exit();
            } else {
                $data['error'] = 'Gagal memperbarui mahasiswa.';
                $data['mahasiswa_edit'] = $this->mahasiswaModel->getMahasiswaById($id);
                $data['instansi_list'] = $this->instansiModel->getAllInstansi();
                $data['dosen_list'] = $this->dosenModel->getAllDosen();
                $this->view('admin/mahasiswa/index', array_merge($data, ['mahasiswa' => $this->mahasiswaModel->getAllMahasiswa()]));
            }
        } else {
            $data['mahasiswa_edit'] = $this->mahasiswaModel->getMahasiswaById($id);
            if (!$data['mahasiswa_edit']) {
                header('Location: ' . BASE_URL . '/admin/mahasiswa');
                exit();
            }
            $data['mahasiswa'] = $this->mahasiswaModel->getAllMahasiswa(); // Untuk menampilkan tabel di background
            $data['instansi_list'] = $this->instansiModel->getAllInstansi();
            $data['dosen_list'] = $this->dosenModel->getAllDosen();
            $this->view('admin/mahasiswa/index', $data);
        }
    }

    public function hapusMahasiswa($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->mahasiswaModel->deleteMahasiswa($id)) {
                // Hapus juga user terkait
                $this->userModel->deleteUserByUserIdAndRole($id, 'mahasiswa');
                header('Location: ' . BASE_URL . '/admin/mahasiswa');
                exit();
            } else {
                // Handle error
                header('Location: ' . BASE_URL . '/admin/mahasiswa');
                exit();
            }
        }
        header('Location: ' . BASE_URL . '/admin/mahasiswa'); // Redirect jika bukan POST
        exit();
    }


    // --- Manajemen Dosen ---
    public function dosen() {
        $data['active_menu'] = 'dosen';
        $data['dosen'] = $this->dosenModel->getAllDosen();
        $this->view('admin/dosen/index', $data);
    }

    public function tambahDosen() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nidn' => trim($_POST['nidn']),
                'nama_lengkap' => trim($_POST['nama_lengkap']),
                'email' => trim($_POST['email']),
                'nomor_telepon' => trim($_POST['nomor_telepon']),
                'status_aktif' => trim($_POST['status_aktif']),
                'error' => ''
            ];

            if (empty($data['nidn']) || empty($data['nama_lengkap']) || empty($data['email'])) {
                $data['error'] = 'NIDN, Nama Lengkap, dan Email tidak boleh kosong.';
                $this->view('admin/dosen/index', $data);
                return;
            }

            if ($this->dosenModel->getDosenByNidn($data['nidn'])) {
                $data['error'] = 'NIDN sudah terdaftar.';
                $this->view('admin/dosen/index', $data);
                return;
            }
            if ($this->dosenModel->getDosenByEmail($data['email'])) {
                $data['error'] = 'Email sudah terdaftar.';
                $this->view('admin/dosen/index', $data);
                return;
            }

            if ($this->dosenModel->addDosen($data)) {
                // Buat akun user otomatis
                $hashed_password = password_hash(DEFAULT_USER_PASSWORD, PASSWORD_BCRYPT);
                $dosen_baru = $this->dosenModel->getDosenByNidn($data['nidn']); // Ambil data dosen yang baru dibuat untuk mendapatkan ID
                $this->userModel->addUser($data['nidn'], $hashed_password, 'dosen', $dosen_baru['id']);
                header('Location: ' . BASE_URL . '/admin/dosen');
                exit();
            } else {
                $data['error'] = 'Gagal menambahkan dosen.';
                $this->view('admin/dosen/index', $data);
            }
        } else {
            header('Location: ' . BASE_URL . '/admin/dosen');
            exit();
        }
    }

    public function editDosen($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'nidn' => trim($_POST['nidn']),
                'nama_lengkap' => trim($_POST['nama_lengkap']),
                'email' => trim($_POST['email']),
                'nomor_telepon' => trim($_POST['nomor_telepon']),
                'status_aktif' => trim($_POST['status_aktif']),
                'error' => ''
            ];

            if (empty($data['nidn']) || empty($data['nama_lengkap']) || empty($data['email'])) {
                $data['error'] = 'NIDN, Nama Lengkap, dan Email tidak boleh kosong.';
                $data['dosen_edit'] = $this->dosenModel->getDosenById($id);
                $this->view('admin/dosen/index', array_merge($data, ['dosen' => $this->dosenModel->getAllDosen()]));
                return;
            }

            $existingDosenNidn = $this->dosenModel->getDosenByNidn($data['nidn']);
            if ($existingDosenNidn && $existingDosenNidn['id'] != $id) {
                $data['error'] = 'NIDN sudah terdaftar untuk dosen lain.';
                $data['dosen_edit'] = $this->dosenModel->getDosenById($id);
                $this->view('admin/dosen/index', array_merge($data, ['dosen' => $this->dosenModel->getAllDosen()]));
                return;
            }
            $existingDosenEmail = $this->dosenModel->getDosenByEmail($data['email']);
            if ($existingDosenEmail && $existingDosenEmail['id'] != $id) {
                $data['error'] = 'Email sudah terdaftar untuk dosen lain.';
                $data['dosen_edit'] = $this->dosenModel->getDosenById($id);
                $this->view('admin/dosen/index', array_merge($data, ['dosen' => $this->dosenModel->getAllDosen()]));
                return;
            }

            if ($this->dosenModel->updateDosen($data)) {
                 // Update username di tabel users jika NIDN berubah
                $current_user = $this->userModel->getUserByUserIdAndRole($id, 'dosen');
                if ($current_user && $current_user['username'] != $data['nidn']) {
                    $this->userModel->updateUsername($current_user['id'], $data['nidn']);
                }
                header('Location: ' . BASE_URL . '/admin/dosen');
                exit();
            } else {
                $data['error'] = 'Gagal memperbarui dosen.';
                $data['dosen_edit'] = $this->dosenModel->getDosenById($id);
                $this->view('admin/dosen/index', array_merge($data, ['dosen' => $this->dosenModel->getAllDosen()]));
            }
        } else {
            $data['dosen_edit'] = $this->dosenModel->getDosenById($id);
            if (!$data['dosen_edit']) {
                header('Location: ' . BASE_URL . '/admin/dosen');
                exit();
            }
            $data['dosen'] = $this->dosenModel->getAllDosen();
            $this->view('admin/dosen/index', $data);
        }
    }

    public function hapusDosen($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->dosenModel->deleteDosen($id)) {
                // Hapus juga user terkait
                $this->userModel->deleteUserByUserIdAndRole($id, 'dosen');
                header('Location: ' . BASE_URL . '/admin/dosen');
                exit();
            } else {
                // Handle error
                header('Location: ' . BASE_URL . '/admin/dosen');
                exit();
            }
        }
        header('Location: ' . BASE_URL . '/admin/dosen');
        exit();
    }

    // --- Manajemen Instansi ---
    public function instansi() {
        $data['active_menu'] = 'instansi';
        $data['instansi'] = $this->instansiModel->getAllInstansi();
        $this->view('admin/instansi/index', $data);
    }

    public function tambahInstansi() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nama_instansi' => trim($_POST['nama_instansi']),
                'bidang_kerja' => trim($_POST['bidang_kerja']),
                'alamat' => trim($_POST['alamat']),
                'kota_kab' => trim($_POST['kota_kab']),
                'telepon' => trim($_POST['telepon']),
                'email' => trim($_POST['email']),
                'pic' => trim($_POST['pic']),
                'error' => ''
            ];

            if (empty($data['nama_instansi'])) {
                $data['error'] = 'Nama Instansi tidak boleh kosong.';
                $this->view('admin/instansi/index', $data);
                return;
            }
            if ($this->instansiModel->getInstansiByName($data['nama_instansi'])) {
                $data['error'] = 'Nama Instansi sudah terdaftar.';
                $this->view('admin/instansi/index', $data);
                return;
            }

            if ($this->instansiModel->addInstansi($data)) {
                header('Location: ' . BASE_URL . '/admin/instansi');
                exit();
            } else {
                $data['error'] = 'Gagal menambahkan instansi.';
                $this->view('admin/instansi/index', $data);
            }
        } else {
            header('Location: ' . BASE_URL . '/admin/instansi');
            exit();
        }
    }

    public function editInstansi($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'nama_instansi' => trim($_POST['nama_instansi']),
                'bidang_kerja' => trim($_POST['bidang_kerja']),
                'alamat' => trim($_POST['alamat']),
                'kota_kab' => trim($_POST['kota_kab']),
                'telepon' => trim($_POST['telepon']),
                'email' => trim($_POST['email']),
                'pic' => trim($_POST['pic']),
                'error' => ''
            ];

            if (empty($data['nama_instansi'])) {
                $data['error'] = 'Nama Instansi tidak boleh kosong.';
                $data['instansi_edit'] = $this->instansiModel->getInstansiById($id);
                $this->view('admin/instansi/index', array_merge($data, ['instansi' => $this->instansiModel->getAllInstansi()]));
                return;
            }
             $existingInstansi = $this->instansiModel->getInstansiByName($data['nama_instansi']);
            if ($existingInstansi && $existingInstansi['id'] != $id) {
                $data['error'] = 'Nama Instansi sudah terdaftar untuk instansi lain.';
                $data['instansi_edit'] = $this->instansiModel->getInstansiById($id);
                $this->view('admin/instansi/index', array_merge($data, ['instansi' => $this->instansiModel->getAllInstansi()]));
                return;
            }

            if ($this->instansiModel->updateInstansi($data)) {
                header('Location: ' . BASE_URL . '/admin/instansi');
                exit();
            } else {
                $data['error'] = 'Gagal memperbarui instansi.';
                $data['instansi_edit'] = $this->instansiModel->getInstansiById($id);
                $this->view('admin/instansi/index', array_merge($data, ['instansi' => $this->instansiModel->getAllInstansi()]));
            }
        } else {
            $data['instansi_edit'] = $this->instansiModel->getInstansiById($id);
            if (!$data['instansi_edit']) {
                header('Location: ' . BASE_URL . '/admin/instansi');
                exit();
            }
            $data['instansi'] = $this->instansiModel->getAllInstansi();
            $this->view('admin/instansi/index', $data);
        }
    }

    public function hapusInstansi($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->instansiModel->deleteInstansi($id)) {
                header('Location: ' . BASE_URL . '/admin/instansi');
                exit();
            } else {
                // Handle error
                header('Location: ' . BASE_URL . '/admin/instansi');
                exit();
            }
        }
        header('Location: ' . BASE_URL . '/admin/instansi');
        exit();
    }

    // --- Manajemen Penempatan KP / Kelompok Instansi ---
    public function penempatan() {
        $data['active_menu'] = 'penempatan';
        $data['penempatan_kp'] = $this->penempatanKpModel->getAllPenempatanKp();
        $data['instansi_list'] = $this->instansiModel->getAllInstansi();
        $data['dosen_list'] = $this->dosenModel->getAllDosen();
        $data['mahasiswa_belum_ditempatkan'] = $this->mahasiswaModel->getMahasiswaBelumDitempatkan();
        $this->view('admin/penempatan/index', $data);
    }

    public function tambahPenempatan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'instansi_id' => $_POST['instansi_id'],
                'dosen_pembimbing_id' => $_POST['dosen_pembimbing_id'],
                'nama_kelompok' => trim($_POST['nama_kelompok']),
                'tanggal_mulai' => trim($_POST['tanggal_mulai']),
                'tanggal_selesai' => trim($_POST['tanggal_selesai']),
                'mahasiswa_ids' => isset($_POST['mahasiswa_ids']) ? $_POST['mahasiswa_ids'] : [], // Array ID mahasiswa
                'error' => ''
            ];

            if (empty($data['instansi_id']) || empty($data['dosen_pembimbing_id'])) {
                $data['error'] = 'Instansi dan Dosen Pembimbing harus dipilih.';
                // Perlu me-load ulang data untuk form
                $data['instansi_list'] = $this->instansiModel->getAllInstansi();
                $data['dosen_list'] = $this->dosenModel->getAllDosen();
                $data['mahasiswa_belum_ditempatkan'] = $this->mahasiswaModel->getMahasiswaBelumDitempatkan();
                $this->view('admin/penempatan/index', $data);
                return;
            }

            // Tambahkan validasi tanggal jika perlu (tanggal selesai setelah tanggal mulai)

            if ($this->penempatanKpModel->addPenempatanKp($data)) {
                header('Location: ' . BASE_URL . '/admin/penempatan');
                exit();
            } else {
                $data['error'] = 'Gagal menambahkan penempatan KP.';
                $data['instansi_list'] = $this->instansiModel->getAllInstansi();
                $data['dosen_list'] = $this->dosenModel->getAllDosen();
                $data['mahasiswa_belum_ditempatkan'] = $this->mahasiswaModel->getMahasiswaBelumDitempatkan();
                $this->view('admin/penempatan/index', $data);
            }
        } else {
            header('Location: ' . BASE_URL . '/admin/penempatan');
            exit();
        }
    }

    public function detailPenempatan($id) {
        $data['penempatan'] = $this->penempatanKpModel->getPenempatanKpById($id);
        if (!$data['penempatan']) {
            header('Location: ' . BASE_URL . '/admin/penempatan');
            exit();
        }
        $data['mahasiswa_di_penempatan'] = $this->penempatanKpModel->getMahasiswaByPenempatanId($id);
        $this->view('admin/penempatan/detail', $data);
    }

    public function editPenempatan($id) {
         if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'instansi_id' => $_POST['instansi_id'],
                'dosen_pembimbing_id' => $_POST['dosen_pembimbing_id'],
                'nama_kelompok' => trim($_POST['nama_kelompok']),
                'tanggal_mulai' => trim($_POST['tanggal_mulai']),
                'tanggal_selesai' => trim($_POST['tanggal_selesai']),
                'mahasiswa_ids' => isset($_POST['mahasiswa_ids']) ? $_POST['mahasiswa_ids'] : [], // Array ID mahasiswa
                'error' => ''
            ];

            if (empty($data['instansi_id']) || empty($data['dosen_pembimbing_id'])) {
                $data['error'] = 'Instansi dan Dosen Pembimbing harus dipilih.';
                $data['penempatan_edit'] = $this->penempatanKpModel->getPenempatanKpById($id);
                $data['instansi_list'] = $this->instansiModel->getAllInstansi();
                $data['dosen_list'] = $this->dosenModel->getAllDosen();
                $data['mahasiswa_belum_ditempatkan'] = $this->mahasiswaModel->getMahasiswaBelumDitempatkan();
                $data['mahasiswa_saat_ini'] = $this->penempatanKpModel->getMahasiswaByPenempatanId($id);
                $this->view('admin/penempatan/index', array_merge($data, ['penempatan_kp' => $this->penempatanKpModel->getAllPenempatanKp()]));
                return;
            }

            if ($this->penempatanKpModel->updatePenempatanKp($data)) {
                header('Location: ' . BASE_URL . '/admin/penempatan');
                exit();
            } else {
                $data['error'] = 'Gagal memperbarui penempatan KP.';
                $data['penempatan_edit'] = $this->penempatanKpModel->getPenempatanKpById($id);
                $data['instansi_list'] = $this->instansiModel->getAllInstansi();
                $data['dosen_list'] = $this->dosenModel->getAllDosen();
                $data['mahasiswa_belum_ditempatkan'] = $this->mahasiswaModel->getMahasiswaBelumDitempatkan();
                $data['mahasiswa_saat_ini'] = $this->penempatanKpModel->getMahasiswaByPenempatanId($id);
                $this->view('admin/penempatan/index', array_merge($data, ['penempatan_kp' => $this->penempatanKpModel->getAllPenempatanKp()]));
            }
        } else {
            $data['penempatan_edit'] = $this->penempatanKpModel->getPenempatanKpById($id);
            if (!$data['penempatan_edit']) {
                header('Location: ' . BASE_URL . '/admin/penempatan');
                exit();
            }
            $data['instansi_list'] = $this->instansiModel->getAllInstansi();
            $data['dosen_list'] = $this->dosenModel->getAllDosen();
            $data['mahasiswa_belum_ditempatkan'] = $this->mahasiswaModel->getMahasiswaBelumDitempatkan();
            $data['mahasiswa_saat_ini'] = $this->penempatanKpModel->getMahasiswaByPenempatanId($id); // Mahasiswa yang sudah di kelompok ini
            $data['penempatan_kp'] = $this->penempatanKpModel->getAllPenempatanKp();
            $this->view('admin/penempatan/index', $data);
        }
    }

    public function hapusPenempatan($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->penempatanKpModel->deletePenempatanKp($id)) {
                header('Location: ' . BASE_URL . '/admin/penempatan');
                exit();
            } else {
                // Handle error
                header('Location: ' . BASE_URL . '/admin/penempatan');
                exit();
            }
        }
        header('Location: ' . BASE_URL . '/admin/penempatan');
        exit();
    }

    // --- Laporan & Rekapitulasi ---
    public function laporan() {
        // Implementasi logika untuk laporan
        // Contoh: $data['rekap_absen'] = $this->absenModel->getRekapAbsen();
        // Akan memerlukan model Absen, Logbook, LaporanMingguan, dll.
        $data['active_menu'] = 'laporan';
        $data['rekap_absen'] = $this->mahasiswaModel->getRekapAbsensi(); // Contoh data
        $data['rekap_progress'] = $this->mahasiswaModel->getRekapProgressLaporan(); // Contoh data
        $data['mahasiswa_per_status'] = $this->mahasiswaModel->getMahasiswaPerStatusKp(); // Contoh data
        $data['pembagian_dosen'] = $this->dosenModel->getPembagianDosen(); // Contoh data
        $this->view('admin/laporan/index', $data);
    }

    public function cetakLaporan($type) {
        // Logika untuk mencetak laporan berdasarkan tipe
        // Akan memerlukan library PDF seperti FPDF atau TCPDF jika ingin generate PDF
        // Untuk saat ini, bisa redirect ke halaman laporan dengan parameter cetak
        if ($type == 'absen') {
            $data['rekap'] = $this->mahasiswaModel->getRekapAbsensi();
            $this->view('admin/laporan/cetak_absen', $data);
        } elseif ($type == 'progress') {
            $data['rekap'] = $this->mahasiswaModel->getRekapProgressLaporan();
            $this->view('admin/laporan/cetak_progress', $data);
        } elseif ($type == 'status') {
            $data['rekap'] = $this->mahasiswaModel->getMahasiswaPerStatusKp();
            $this->view('admin/laporan/cetak_status', $data);
        } elseif ($type == 'dosen') {
            $data['rekap'] = $this->dosenModel->getPembagianDosen();
            $this->view('admin/laporan/cetak_dosen', $data);
        } else {
            header('Location: ' . BASE_URL . '/admin/laporan');
            exit();
        }
    }
}
