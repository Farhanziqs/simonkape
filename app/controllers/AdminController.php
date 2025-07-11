<?php
// simonkapedb/app/controllers/AdminController.php
require_once APP_ROOT . '/app/libraries/dompdf/autoload.inc.php'; // Pastikan path ini benar!
use Dompdf\Dompdf;
use Dompdf\Options;

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

    public function importMahasiswaCsv() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']['tmp_name'])) {
            $file = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($file, "r");
            if ($handle === FALSE) {
                $_SESSION['error_message'] = 'Gagal membuka file CSV.';
                header('Location: ' . BASE_URL . '/admin/mahasiswa');
                exit();
            }

            $header = fgetcsv($handle, 1000, ","); // Ambil baris header
            // Field yang relevan dengan input form dan tabel mahasiswa
            $expected_fields = ['nim', 'nama_lengkap', 'program_studi', 'instansi_id', 'dosen_pembimbing_id'];
            $imported_count = 0;
            $failed_count = 0;
            $error_messages = [];

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) == 0) continue; // Lewati baris kosong

                $mahasiswa_data = [];
                // Mapping data dari CSV ke field yang diharapkan dan memfilter kolom lain
                foreach ($header as $key => $col_name) {
                    $col_name_cleaned = trim(strtolower($col_name));
                    if (in_array($col_name_cleaned, $expected_fields)) {
                        $mahasiswa_data[$col_name_cleaned] = trim($row[$key]);
                    }
                }

                // Otomatis set status_kp menjadi 'Terdaftar'
                $mahasiswa_data['status_kp'] = 'Terdaftar';

                // Set nilai default untuk program_studi jika tidak ada di CSV
                $mahasiswa_data['program_studi'] = $mahasiswa_data['program_studi'] ?? 'Teknik Informatika';


                // Validasi dasar untuk field yang wajib: nim, nama_lengkap
                if (empty($mahasiswa_data['nim']) || empty($mahasiswa_data['nama_lengkap'])) {
                    $failed_count++;
                    $error_messages[] = 'Baris dilewati (NIM atau Nama Lengkap kosong di baris: ' . implode(', ', $row) . ').';
                    continue;
                }

                // Cek apakah NIM sudah terdaftar untuk mencegah duplikasi
                if ($this->mahasiswaModel->getMahasiswaByNim($mahasiswa_data['nim'])) {
                    $failed_count++;
                    $error_messages[] = 'Baris dilewati: NIM ' . htmlspecialchars($mahasiswa_data['nim']) . ' sudah terdaftar.';
                    continue;
                }

                // Konversi nama instansi menjadi ID jika diberikan nama (bukan ID numerik)
                if (isset($mahasiswa_data['instansi_id']) && !empty($mahasiswa_data['instansi_id'])) {
                    $instansi = $this->instansiModel->getInstansiByName($mahasiswa_data['instansi_id']);
                    $mahasiswa_data['instansi_id'] = $instansi ? $instansi['id'] : null;
                    if (!$mahasiswa_data['instansi_id']) {
                        $error_messages[] = 'Instansi "' . htmlspecialchars($mahasiswa_data['instansi_id']) . '" tidak ditemukan untuk NIM ' . htmlspecialchars($mahasiswa_data['nim']) . '.';
                    }
                } else {
                    $mahasiswa_data['instansi_id'] = null;
                }

                // Konversi NIDN dosen menjadi ID jika diberikan NIDN (bukan ID numerik)
                if (isset($mahasiswa_data['dosen_pembimbing_id']) && !empty($mahasiswa_data['dosen_pembimbing_id'])) {
                    $dosen = $this->dosenModel->getDosenByNidn($mahasiswa_data['dosen_pembimbing_id']);
                    $mahasiswa_data['dosen_pembimbing_id'] = $dosen ? $dosen['id'] : null;
                    if (!$mahasiswa_data['dosen_pembimbing_id']) {
                        $error_messages[] = 'Dosen dengan NIDN "' . htmlspecialchars($mahasiswa_data['dosen_pembimbing_id']) . '" tidak ditemukan untuk NIM ' . htmlspecialchars($mahasiswa_data['nim']) . '.';
                    }
                } else {
                    $mahasiswa_data['dosen_pembimbing_id'] = null;
                }

                // Tambahkan mahasiswa ke database
                if ($this->mahasiswaModel->addMahasiswa($mahasiswa_data)) {
                    $hashed_password = password_hash(DEFAULT_USER_PASSWORD, PASSWORD_BCRYPT);
                    $mahasiswa_baru = $this->mahasiswaModel->getMahasiswaByNim($mahasiswa_data['nim']);
                    if ($mahasiswa_baru) {
                        $this->userModel->addUser($mahasiswa_data['nim'], $hashed_password, 'mahasiswa', $mahasiswa_baru['id']);
                    }
                    $imported_count++;
                } else {
                    $failed_count++;
                    $error_messages[] = 'Gagal menambahkan mahasiswa ' . htmlspecialchars($mahasiswa_data['nim']) . '.';
                }
            }
            fclose($handle);

            // Set pesan feedback ke session
            if ($imported_count > 0) {
                $_SESSION['success_message'] = $imported_count . ' mahasiswa berhasil diimpor.';
            }
            if ($failed_count > 0) {
                // Tampilkan pesan error secara lebih spesifik, hindari duplikasi pesan
                $_SESSION['error_message'] = $failed_count . ' mahasiswa gagal diimpor. Detail: ' . implode('; ', array_unique($error_messages));
            }
        } else {
            $_SESSION['error_message'] = 'Tidak ada file CSV yang diunggah atau metode request tidak sesuai.';
        }
        header('Location: ' . BASE_URL . '/admin/mahasiswa');
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

    public function importDosenCsv() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']['tmp_name'])) {
            $file = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($file, "r");
            if ($handle === FALSE) {
                $_SESSION['error_message'] = 'Gagal membuka file CSV.';
                header('Location: ' . BASE_URL . '/admin/dosen');
                exit();
            }

            $header = fgetcsv($handle, 1000, ","); // Ambil baris header
            // Field yang relevan dengan input form dan tabel dosen
            $expected_fields = ['nidn', 'nama_lengkap', 'email', 'nomor_telepon', 'status_aktif'];
            $imported_count = 0;
            $failed_count = 0;
            $error_messages = [];

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) == 0) continue; // Lewati baris kosong

                $dosen_data = [];
                // Mapping data dari CSV ke field yang diharapkan dan memfilter kolom lain
                foreach ($header as $key => $col_name) {
                    $col_name_cleaned = trim(strtolower($col_name));
                    if (in_array($col_name_cleaned, $expected_fields)) {
                        $dosen_data[$col_name_cleaned] = trim($row[$key]);
                    }
                }

                // Set nilai default untuk status_aktif jika tidak ada di CSV
                $dosen_data['status_aktif'] = $dosen_data['status_aktif'] ?? 'Aktif';

                // Validasi dasar untuk field yang wajib: nidn, nama_lengkap, email
                if (empty($dosen_data['nidn']) || empty($dosen_data['nama_lengkap']) || empty($dosen_data['email'])) {
                    $failed_count++;
                    $error_messages[] = 'Baris dilewati (NIDN, Nama Lengkap, atau Email kosong di baris: ' . implode(', ', $row) . ').';
                    continue;
                }

                // Cek apakah NIDN sudah terdaftar untuk mencegah duplikasi
                if ($this->dosenModel->getDosenByNidn($dosen_data['nidn'])) {
                    $failed_count++;
                    $error_messages[] = 'Baris dilewati: NIDN ' . htmlspecialchars($dosen_data['nidn']) . ' sudah terdaftar.';
                    continue;
                }
                // Cek apakah Email sudah terdaftar untuk mencegah duplikasi
                if ($this->dosenModel->getDosenByEmail($dosen_data['email'])) {
                    $failed_count++;
                    $error_messages[] = 'Baris dilewati: Email ' . htmlspecialchars($dosen_data['email']) . ' sudah terdaftar.';
                    continue;
                }

                // Tambahkan dosen ke database
                if ($this->dosenModel->addDosen($dosen_data)) {
                    // Buat akun user otomatis untuk dosen yang baru diimpor
                    $hashed_password = password_hash(DEFAULT_USER_PASSWORD, PASSWORD_BCRYPT);
                    $dosen_baru = $this->dosenModel->getDosenByNidn($dosen_data['nidn']);
                    if ($dosen_baru) {
                        $this->userModel->addUser($dosen_data['nidn'], $hashed_password, 'dosen', $dosen_baru['id']);
                    }
                    $imported_count++;
                } else {
                    $failed_count++;
                    $error_messages[] = 'Gagal menambahkan dosen ' . htmlspecialchars($dosen_data['nidn']) . '.';
                }
            }
            fclose($handle);

            // Set pesan feedback ke session
            if ($imported_count > 0) {
                $_SESSION['success_message'] = $imported_count . ' dosen berhasil diimpor.';
            }
            if ($failed_count > 0) {
                $_SESSION['error_message'] = $failed_count . ' dosen gagal diimpor. Detail: ' . implode('; ', array_unique($error_messages));
            }
        } else {
            $_SESSION['error_message'] = 'Tidak ada file CSV yang diunggah atau metode request tidak sesuai.';
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

    public function importInstansiCsv() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']['tmp_name'])) {
            $file = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($file, "r");
            if ($handle === FALSE) {
                $_SESSION['error_message'] = 'Gagal membuka file CSV.';
                header('Location: ' . BASE_URL . '/admin/instansi');
                exit();
            }

            $header = fgetcsv($handle, 1000, ","); // Ambil baris header
            // Field yang relevan dengan input form dan tabel instansi
            $expected_fields = ['nama_instansi', 'alamat', 'kota_kab', 'telepon', 'email', 'pic']; // Menambahkan bidang_kerja sesuai schema
            $imported_count = 0;
            $failed_count = 0;
            $error_messages = [];

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) == 0) continue; // Lewati baris kosong

                $instansi_data = [];
                // Mapping data dari CSV ke field yang diharapkan dan memfilter kolom lain
                foreach ($header as $key => $col_name) {
                    $col_name_cleaned = trim(strtolower($col_name));
                    if (in_array($col_name_cleaned, $expected_fields)) {
                        $instansi_data[$col_name_cleaned] = trim($row[$key]);
                    }
                }

                // Validasi dasar untuk field yang wajib: nama_instansi
                if (empty($instansi_data['nama_instansi'])) {
                    $failed_count++;
                    $error_messages[] = 'Baris dilewati (Nama Instansi kosong di baris: ' . implode(', ', $row) . ').';
                    continue;
                }

                // Cek apakah Nama Instansi sudah terdaftar untuk mencegah duplikasi
                if ($this->instansiModel->getInstansiByName($instansi_data['nama_instansi'])) {
                    $failed_count++;
                    $error_messages[] = 'Baris dilewati: Nama Instansi ' . htmlspecialchars($instansi_data['nama_instansi']) . ' sudah terdaftar.';
                    continue;
                }

                // Tambahkan instansi ke database
                if ($this->instansiModel->addInstansi($instansi_data)) {
                    $imported_count++;
                } else {
                    $failed_count++;
                    $error_messages[] = 'Gagal menambahkan instansi ' . htmlspecialchars($instansi_data['nama_instansi']) . '.';
                }
            }
            fclose($handle);

            // Set pesan feedback ke session
            if ($imported_count > 0) {
                $_SESSION['success_message'] = $imported_count . ' instansi berhasil diimpor.';
            }
            if ($failed_count > 0) {
                $_SESSION['error_message'] = $failed_count . ' instansi gagal diimpor. Detail: ' . implode('; ', array_unique($error_messages));
            }
        } else {
            $_SESSION['error_message'] = 'Tidak ada file CSV yang diunggah atau metode request tidak sesuai.';
        }
        header('Location: ' . BASE_URL . '/admin/instansi');
        exit();
    }

    // --- Manajemen Penempatan KP / Kelompok Instansi ---
    public function penempatan() {
        $data['active_menu'] = 'penempatan';

        $raw_penempatan_details = $this->penempatanKpModel->getMahasiswaPenempatanDetails();

        // Proses data untuk rowspan
        $processed_penempatan = [];
        $current_instansi = null;
        $current_dosen = null;
        $instansi_group_index = -1;
        $dosen_group_index = -1;

        foreach ($raw_penempatan_details as $detail) {
            // Group by Instansi
            if ($detail['nama_instansi'] !== $current_instansi) {
                $instansi_group_index++;
                $processed_penempatan[$instansi_group_index] = [
                    'nama_instansi' => $detail['nama_instansi'],
                    'instansi_rowspan' => 0, // Akan dihitung nanti
                    'dosen_groups' => []
                ];
                $current_instansi = $detail['nama_instansi'];
                $current_dosen = null; // Reset dosen group when instansi changes
            }

            // Group by Dosen within Instansi
            if ($detail['nama_dosen_pembimbing'] !== $current_dosen) {
                $dosen_group_index = count($processed_penempatan[$instansi_group_index]['dosen_groups']);
                $processed_penempatan[$instansi_group_index]['dosen_groups'][$dosen_group_index] = [
                    'nama_dosen_pembimbing' => $detail['nama_dosen_pembimbing'],
                    'dosen_rowspan' => 0, // Akan dihitung nanti
                    'students' => []
                ];
                $current_dosen = $detail['nama_dosen_pembimbing'];
            }

            // Add student to the current dosen group
            $processed_penempatan[$instansi_group_index]['dosen_groups'][$dosen_group_index]['students'][] = $detail;
        }

        // Hitung rowspan
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

        // List instansi dan dosen untuk form tambah/edit
        $data['instansi_list'] = $this->instansiModel->getAllInstansi();
        $data['dosen_list'] = $this->dosenModel->getAllDosen();
        $data['mahasiswa_belum_ditempatkan'] = $this->mahasiswaModel->getMahasiswaBelumDitempatkan();

        // Data penempatan_kp (group) masih dimuat jika diperlukan untuk logika edit, dll.
        // Tidak lagi menjadi sumber data utama untuk tabel di view.
        $data['penempatan_kp'] = $this->penempatanKpModel->getAllPenempatanKp();
        foreach ($data['penempatan_kp'] as &$pkp) {
            $pkp['mahasiswa_list'] = $this->penempatanKpModel->getMahasiswaByPenempatanId($pkp['id']);
        }
        unset($pkp);

        $this->view('admin/penempatan/index', $data);
    }

    public function tambahPenempatan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'instansi_id' => $_POST['instansi_id'],
                'dosen_pembimbing_id' => $_POST['dosen_pembimbing_id'],
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
                $data['penempatan_kp'] = $this->penempatanKpModel->getAllPenempatanKp(); // Tetap load ini untuk merge
                foreach ($data['penempatan_kp'] as &$pkp) {
                    $pkp['mahasiswa_list'] = $this->penempatanKpModel->getMahasiswaByPenempatanId($pkp['id']);
                }
                unset($pkp);
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
                // Muat data penempatan_kp (group) untuk tabel di background
                $data['penempatan_kp'] = $this->penempatanKpModel->getAllPenempatanKp();
                foreach ($data['penempatan_kp'] as &$pkp) {
                    $pkp['mahasiswa_list'] = $this->penempatanKpModel->getMahasiswaByPenempatanId($pkp['id']);
                }
                unset($pkp);
                $this->view('admin/penempatan/index', array_merge($data, ['processed_penempatan_details' => $this->penempatanKpModel->getMahasiswaPenempatanDetails()]));
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
            $data['mahasiswa_saat_ini'] = $this->penempatanKpModel->getMahasiswaByPenempatanId($id);
            // Muat data penempatan_kp (group) untuk tabel di background
            $data['penempatan_kp'] = $this->penempatanKpModel->getAllPenempatanKp();
            foreach ($data['penempatan_kp'] as &$pkp) {
                $pkp['mahasiswa_list'] = $this->penempatanKpModel->getMahasiswaByPenempatanId($pkp['id']);
            }
            unset($pkp);
            $data['processed_penempatan_details'] = $this->penempatanKpModel->getMahasiswaPenempatanDetails(); // Load ini agar tabel utama tetap tampil
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

     public function showGenerateSuratForm($penempatan_id) {
        $penempatan = $this->penempatanKpModel->getPenempatanKpById($penempatan_id);

        if (!$penempatan) {
            $_SESSION['error_message'] = 'Penempatan KP tidak ditemukan.';
            header('Location: ' . BASE_URL . '/admin/penempatan');
            exit();
        }

        $data['active_menu'] = 'penempatan';
        $data['title'] = 'Generate Surat Pengantar - SIMONKAPE';
        $data['penempatan_id'] = $penempatan_id;
        $data['penempatan'] = $penempatan;

        // Default values for form: ambil dari DB jika sudah ada, kalau tidak pakai default
        $data['default_nomor_surat'] = $penempatan['nomor_surat_kp'] ?? '037/Q.11/TI-UND/IV/'.date('Y', time());
        $data['default_kepada_yth_nama'] = $penempatan['kepada_yth_kp'] ?? ('KEPALA ' . strtoupper($penempatan['nama_instansi'] ?? 'INSTANSI TERKAIT'));
        $data['default_alamat_tujuan'] = $penempatan['alamat_tujuan_kp'] ?? '';

        // Jika alamat_tujuan dari DB kosong, coba generate dari instansi
        if (empty($data['default_alamat_tujuan'])) {
            if (!empty($penempatan['instansi_alamat'])) {
                $data['default_alamat_tujuan'] .= $penempatan['instansi_alamat'];
            }
            if (!empty($penempatan['instansi_kota_kab'])) {
                if (!empty($data['default_alamat_tujuan'])) {
                    $data['default_alamat_tujuan'] .= ', ';
                }
                $data['default_alamat_tujuan'] .= 'KOTA ' . strtoupper($penempatan['instansi_kota_kab']);
            }
            if (empty($data['default_alamat_tujuan'])) {
                $data['default_alamat_tujuan'] = 'Tempat';
            }
        }

        $this->view('admin/surat_pengantar/generate_form', $data);
    }

    public function generateSuratPengantarPdf($penempatan_id) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $_SESSION['error_message'] = 'Metode permintaan tidak valid.';
            header('Location: ' . BASE_URL . '/admin/penempatan');
            exit();
        }

        $nomor_surat = trim($_POST['nomor_surat']);
        $tanggal_surat_input = trim($_POST['tanggal_surat']);
        $kepada_yth_nama = trim($_POST['kepada_yth_nama']);
        $alamat_tujuan = trim($_POST['alamat_tujuan']);

        if (empty($nomor_surat) || empty($tanggal_surat_input) || empty($kepada_yth_nama)) {
            $_SESSION['error_message'] = 'Nomor Surat, Tanggal Surat, dan Kepada Yth harus diisi.';
            header('Location: ' . BASE_URL . '/admin/showGenerateSuratForm/' . $penempatan_id);
            exit();
        }

        // --- SIMPAN DETAIL SURAT KE DATABASE ---
        $this->penempatanKpModel->saveSuratDetails($penempatan_id, $nomor_surat, $kepada_yth_nama, $alamat_tujuan);
        // --- AKHIR SIMPAN DETAIL SURAT ---

        $penempatan = $this->penempatanKpModel->getPenempatanKpById($penempatan_id); // Ambil ulang data setelah disimpan
        $mahasiswa_list_raw = $this->penempatanKpModel->getMahasiswaByPenempatanId($penempatan_id);

        if (!$penempatan || empty($mahasiswa_list_raw)) {
            $_SESSION['error_message'] = 'Data penempatan atau mahasiswa tidak ditemukan untuk generate surat.';
            header('Location: ' . BASE_URL . '/admin/penempatan');
            exit();
        }

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

        $tanggal_obj = new DateTime($tanggal_surat_input);
        if (class_exists('IntlDateFormatter')) {
            $formatter = new IntlDateFormatter(
                'id_ID',
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'Asia/Makassar',
                IntlDateFormatter::LONG
            );
            $formatter->setPattern('d MMMMVIDENCE');
            $tanggal_surat_formatted = $formatter->format($tanggal_obj);
        } else {
            $bulan = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            $tanggal_surat_formatted = $tanggal_obj->format('d') . ' ' . $bulan[(int)$tanggal_obj->format('m')] . ' ' . $tanggal_obj->format('Y');
        }

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
        ];

        ob_start();
        $this->view('surat_pengantar/template', $data_template);
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Times New Roman');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Surat Pengantar KP - " . str_replace(' ', '_', $penempatan['nama_instansi']) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => false]);

        exit();
    }

    //------------penarikan------------
    // --- Generate Surat Penarikan KP ---
    public function showGenerateSuratPenarikanForm($penempatan_id) {
        $penempatan = $this->penempatanKpModel->getPenempatanKpById($penempatan_id);

        if (!$penempatan) {
            $_SESSION['error_message'] = 'Penempatan KP tidak ditemukan.';
            header('Location: ' . BASE_URL . '/admin/penempatan');
            exit();
        }

        $data['active_menu'] = 'penempatan';
        $data['title'] = 'Generate Surat Penarikan - SIMONKAPE';
        $data['penempatan_id'] = $penempatan_id;
        $data['penempatan'] = $penempatan; // Detail penempatan

        // Default values for form: ambil dari DB jika sudah ada, kalau tidak pakai default
        $data['default_nomor_surat'] = $penempatan['nomor_surat_penarikan_kp'] ?? '152.I/Q.12/TI-UND/V/'.date('Y', time());
        $data['default_kepada_yth_nama'] = $penempatan['kepada_yth_penarikan_kp'] ?? ('KEPALA ' . strtoupper($penempatan['nama_instansi'] ?? 'INSTANSI TERKAIT'));
        $data['default_alamat_tujuan'] = $penempatan['alamat_tujuan_penarikan_kp'] ?? '';
        $data['default_tanggal_penarikan'] = $penempatan['tanggal_penarikan_kp'] ?? date('Y-m-d'); // Tanggal surat penarikan

        // Jika alamat_tujuan dari DB kosong, coba generate dari instansi
        if (empty($data['default_alamat_tujuan'])) {
            if (!empty($penempatan['instansi_alamat'])) {
                $data['default_alamat_tujuan'] .= $penempatan['instansi_alamat'];
            }
            if (!empty($penempatan['instansi_kota_kab'])) {
                if (!empty($data['default_alamat_tujuan'])) {
                    $data['default_alamat_tujuan'] .= ', ';
                }
                $data['default_alamat_tujuan'] .= 'KOTA ' . strtoupper($penempatan['instansi_kota_kab']);
            }
            if (empty($data['default_alamat_tujuan'])) {
                $data['default_alamat_tujuan'] = 'Tempat';
            }
        }

        // Tanggal balasan surat tidak lagi diminta di template
        // $data['default_tanggal_balasan'] = $penempatan['tanggal_surat_balasan_kp'] ?? $penempatan['tanggal_mulai'];

        $this->view('admin/surat_penarikan/generate_form', $data);
    }

    public function generateSuratPenarikanPdf($penempatan_id) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $_SESSION['error_message'] = 'Metode permintaan tidak valid.';
            header('Location: ' . BASE_URL . '/admin/penempatan');
            exit();
        }

        $nomor_surat = trim($_POST['nomor_surat']);
        $tanggal_surat_input = trim($_POST['tanggal_surat']); // Ini adalah tanggal surat penarikan
        $kepada_yth_nama = trim($_POST['kepada_yth_nama']);
        $alamat_tujuan = trim($_POST['alamat_tujuan']);
        // Tanggal balasan surat dihapus dari input form
        // $tanggal_surat_balasan_input = trim($_POST['tanggal_surat_balasan']);

        if (empty($nomor_surat) || empty($tanggal_surat_input) || empty($kepada_yth_nama)) {
            $_SESSION['error_message'] = 'Nomor Surat, Tanggal Surat, dan Kepada Yth harus diisi.';
            header('Location: ' . BASE_URL . '/admin/showGenerateSuratPenarikanForm/' . $penempatan_id);
            exit();
        }

        // --- SIMPAN DETAIL SURAT PENARIKAN KE DATABASE ---
        // Tanggal penarikan (tanggal_surat_input) akan disimpan
        $this->penempatanKpModel->savePenarikanDetails(
            $penempatan_id,
            $nomor_surat,
            $kepada_yth_nama,
            $alamat_tujuan,
            $tanggal_surat_input // Tanggal surat penarikan disimpan
            // tanggal_surat_balasan_input tidak disimpan
        );
        // --- AKHIR SIMPAN DETAIL SURAT PENARIKAN ---

        $penempatan = $this->penempatanKpModel->getPenempatanKpById($penempatan_id); // Ambil ulang data setelah disimpan
        $mahasiswa_list_raw = $this->penempatanKpModel->getMahasiswaByPenempatanId($penempatan['id']);

        if (!$penempatan || empty($mahasiswa_list_raw)) {
            $_SESSION['error_message'] = 'Data penempatan atau mahasiswa tidak ditemukan untuk generate surat penarikan.';
            header('Location: ' . BASE_URL . '/admin/penempatan');
            exit();
        }

        $mahasiswa_list = [];
        $no = 1;
        foreach($mahasiswa_list_raw as $mhs) {
            $mahasiswa_list[] = [
                'no' => $no++,
                'nama' => $mhs['nama_lengkap'], // Nama mahasiswa
                'nim' => $mhs['nim'] // NIM mahasiswa
            ];
        }

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
            'tanggal_surat_balasan' => 'Disesuaikan dengan surat balasan' // Ini menjadi teks default saja
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

        $filename = "Surat Penarikan KP - " . str_replace(' ', '_', $penempatan['nama_instansi']) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => false]);

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

    // --- Manajemen Kaprodi ---
    public function kaprodi() {
        $data['active_menu'] = 'kaprodi';
        $data['kaprodi_list'] = $this->kaprodiModel->getAllKaprodi();
        $this->view('admin/kaprodi/index', $data);
    }

    public function tambahKaprodi() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nama_lengkap' => trim($_POST['nama_lengkap']),
                'nidn' => trim($_POST['nidn']),
                'email' => trim($_POST['email']),
                'nomor_telepon' => trim($_POST['nomor_telepon']),
                'program_studi' => trim($_POST['program_studi']),
                'error' => ''
            ];

            if (empty($data['nama_lengkap']) || empty($data['nidn']) || empty($data['email'])) {
                $data['error'] = 'Nama Lengkap, NIDN, dan Email tidak boleh kosong.';
                $data['kaprodi_list'] = $this->kaprodiModel->getAllKaprodi(); // Load ulang data untuk view
                $this->view('admin/kaprodi/index', $data);
                return;
            }

            if ($this->kaprodiModel->getKaprodiByNidn($data['nidn'])) {
                $data['error'] = 'NIDN sudah terdaftar untuk Kaprodi lain.';
                $data['kaprodi_list'] = $this->kaprodiModel->getAllKaprodi();
                $this->view('admin/kaprodi/index', $data);
                return;
            }
            if ($this->kaprodiModel->getKaprodiByEmail($data['email'])) {
                $data['error'] = 'Email sudah terdaftar untuk Kaprodi lain.';
                $data['kaprodi_list'] = $this->kaprodiModel->getAllKaprodi();
                $this->view('admin/kaprodi/index', $data);
                return;
            }

            if ($this->kaprodiModel->addKaprodi($data)) {
                // Buat akun user otomatis
                $hashed_password = password_hash(DEFAULT_USER_PASSWORD, PASSWORD_BCRYPT);
                $kaprodi_baru = $this->kaprodiModel->getKaprodiByNidn($data['nidn']);
                if ($kaprodi_baru) {
                    $this->userModel->addUser($data['nidn'], $hashed_password, 'kaprodi', $kaprodi_baru['id']);
                }
                header('Location: ' . BASE_URL . '/admin/kaprodi');
                exit();
            } else {
                $data['error'] = 'Gagal menambahkan Kaprodi.';
                $data['kaprodi_list'] = $this->kaprodiModel->getAllKaprodi();
                $this->view('admin/kaprodi/index', $data);
            }
        } else {
            header('Location: ' . BASE_URL . '/admin/kaprodi');
            exit();
        }
    }

    public function editKaprodi($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'nama_lengkap' => trim($_POST['nama_lengkap']),
                'nidn' => trim($_POST['nidn']),
                'email' => trim($_POST['email']),
                'nomor_telepon' => trim($_POST['nomor_telepon']),
                'program_studi' => trim($_POST['program_studi']),
                'error' => ''
            ];

            if (empty($data['nama_lengkap']) || empty($data['nidn']) || empty($data['email'])) {
                $data['error'] = 'Nama Lengkap, NIDN, dan Email tidak boleh kosong.';
                $data['kaprodi_edit'] = $this->kaprodiModel->getKaprodiById($id);
                $data['kaprodi_list'] = $this->kaprodiModel->getAllKaprodi();
                $this->view('admin/kaprodi/index', array_merge($data, ['kaprodi_list' => $this->kaprodiModel->getAllKaprodi()]));
                return;
            }

            $existingKaprodiNidn = $this->kaprodiModel->getKaprodiByNidn($data['nidn']);
            if ($existingKaprodiNidn && $existingKaprodiNidn['id'] != $id) {
                $data['error'] = 'NIDN sudah terdaftar untuk Kaprodi lain.';
                $data['kaprodi_edit'] = $this->kaprodiModel->getKaprodiById($id);
                $data['kaprodi_list'] = $this->kaprodiModel->getAllKaprodi();
                $this->view('admin/kaprodi/index', array_merge($data, ['kaprodi_list' => $this->kaprodiModel->getAllKaprodi()]));
                return;
            }
            $existingKaprodiEmail = $this->kaprodiModel->getKaprodiByEmail($data['email']);
            if ($existingKaprodiEmail && $existingKaprodiEmail['id'] != $id) {
                $data['error'] = 'Email sudah terdaftar untuk Kaprodi lain.';
                $data['kaprodi_edit'] = $this->kaprodiModel->getKaprodiById($id);
                $data['kaprodi_list'] = $this->kaprodiModel->getAllKaprodi();
                $this->view('admin/kaprodi/index', array_merge($data, ['kaprodi_list' => $this->kaprodiModel->getAllKaprodi()]));
                return;
            }

            if ($this->kaprodiModel->updateKaprodi($data)) {
                $current_user = $this->userModel->getUserByUserIdAndRole($id, 'kaprodi');
                if ($current_user && $current_user['username'] != $data['nidn']) {
                    $this->userModel->updateUsername($current_user['id'], $data['nidn']);
                }
                header('Location: ' . BASE_URL . '/admin/kaprodi');
                exit();
            } else {
                $data['error'] = 'Gagal memperbarui Kaprodi.';
                $data['kaprodi_edit'] = $this->kaprodiModel->getKaprodiById($id);
                $data['kaprodi_list'] = $this->kaprodiModel->getAllKaprodi();
                $this->view('admin/kaprodi/index', array_merge($data, ['kaprodi_list' => $this->kaprodiModel->getAllKaprodi()]));
            }
        } else {
            $data['kaprodi_edit'] = $this->kaprodiModel->getKaprodiById($id);
            if (!$data['kaprodi_edit']) {
                header('Location: ' . BASE_URL . '/admin/kaprodi');
                exit();
            }
            $data['kaprodi_list'] = $this->kaprodiModel->getAllKaprodi();
            $this->view('admin/kaprodi/index', $data);
        }
    }

    public function hapusKaprodi($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->kaprodiModel->deleteKaprodi($id)) {
                $this->userModel->deleteUserByActorIdAndRole($id, 'kaprodi'); // Menggunakan actor_id
                header('Location: ' . BASE_URL . '/admin/kaprodi');
                exit();
            } else {
                header('Location: ' . BASE_URL . '/admin/kaprodi');
                exit();
            }
        }
        header('Location: ' . BASE_URL . '/admin/kaprodi');
        exit();
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
