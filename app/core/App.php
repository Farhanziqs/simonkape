<?php
// app/core/App.php

class App {
    protected $controller = 'Auth'; // Default controller
    protected $method = 'index'; // Default method
    protected $params = []; // Default params

    public function __construct() {
        $url = $this->parseUrl();

        // Cek controller
        // if (file_exists(APP_ROOT . '/app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
        //     $this->controller = ucfirst($url[0]);
        //     unset($url[0]);
        // } else {
        //     // Handle 404 atau redirect ke default
        //     header('Location: ' . BASE_URL . '/auth'); // Atau halaman error 404
        //     exit();
        // }
        // // Tambahkan pengecekan untuk MahasiswaController
        // if (isset($url[0])) {
        //     if (file_exists(APP_ROOT . '/app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
        //         $this->controller = ucfirst($url[0]);
        //         unset($url[0]);
        //     } else if (file_exists(APP_ROOT . '/app/controllers/MahasiswaController.php') && $url[0] == 'mahasiswa') {
        //          // Ini untuk memastikan 'mahasiswa' tanpa 'Controller' di URL tetap bisa
        //          $this->controller = 'Mahasiswa';
        //          unset($url[0]);
        //     } else {
        //         // Handle 404 atau redirect ke default
        //         header('Location: ' . BASE_URL . '/auth'); // Atau halaman error 404
        //         exit();
        //     }
        // }

        // // dosen
        // if (isset($url[0])) {
        //     if (file_exists(APP_ROOT . '/app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
        //         $this->controller = ucfirst($url[0]);
        //         unset($url[0]);
        //     }
        // }

        if (isset($url[0])) {
            $controllerFile = ucfirst($url[0]) . 'Controller.php';
            if (file_exists(APP_ROOT . '/app/controllers/' . $controllerFile)) {
                $this->controller = ucfirst($url[0]);
                unset($url[0]);
            } else {
                // Handle 404 or redirect to default
                header('Location: ' . BASE_URL . '/auth'); // Or a 404 error page
                exit();
            }
        }

        require_once APP_ROOT . '/app/controllers/' . $this->controller . 'Controller.php';
        $controllerName = $this->controller . 'Controller'; // Tambahkan 'Controller' di belakang nama kelas
        $this->controller = new $controllerName(); // Instantiate controller

        // Cek method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Ambil params
        $this->params = $url ? array_values($url) : [];

        // Jalankan controller dan method, serta kirim params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return ['auth']; // Default URL jika tidak ada
    }
}
