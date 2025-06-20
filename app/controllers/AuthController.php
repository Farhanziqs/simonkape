<?php
// simonkapedb/app/controllers/AuthController.php

class AuthController extends Controller {
     private $userModel;
    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function index() {
        // Jika sudah login, redirect ke dashboard yang sesuai
        if (isset($_SESSION['user_id'])) {
            $this->redirectToDashboard($_SESSION['user_role']);
        }

        $data = [
            'title' => 'Login - SIMONKAPE'
        ];

        $this->view('auth/login', $data);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            //Validasi input
            if (empty($username) || empty($password)) {
                $data['error'] = 'Username dan password tidak boleh kosong.';
                $this->view('auth/login', $data);
                return;
            }

            $loggedInUser = $this->userModel->login($username, $password);

            if ($loggedInUser) {
                // Login berhasil, buat session
                $_SESSION['user_id'] = $loggedInUser['id'];
                $_SESSION['username'] = $loggedInUser['username'];
                $_SESSION['user_role'] = $loggedInUser['role'];
                $_SESSION['specific_user_id'] = $loggedInUser['user_id']; // ID dari tabel mahasiswa/dosen

                // Redirect ke dashboard yang sesuai
                $this->redirectToDashboard($loggedInUser['role']);
            } else {
                $data['error'] = 'Username atau password salah.';
                $this->view('auth/login', $data);
            }
        } else {
            // Tampilkan form login jika GET request
            $this->view('auth/login');
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/auth');
        exit();
    }

    private function redirectToDashboard($role) {
        switch ($role) {
            case 'admin':
                header('Location: ' . BASE_URL . '/admin');
                break;
            case 'mahasiswa':
                header('Location: ' . BASE_URL . '/mahasiswa');
                break;
            case 'dosen':
                header('Location: ' . BASE_URL . '/dosen');
                break;
            default:
                header('Location: ' . BASE_URL . '/auth');
        }
        exit();
    }
}
