<?php
// app/core/App.php

class App {
    protected $controller = 'Auth'; // Default controller
    protected $method = 'index'; // Default method
    protected $params = []; // Default params

    public function __construct() {
        $url = $this->parseUrl();

        // Cek controller
        if (file_exists(APP_ROOT . '/app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
        } else {
            // Handle 404 atau redirect ke default
            header('Location: ' . BASE_URL . '/auth'); // Atau halaman error 404
            exit();
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
