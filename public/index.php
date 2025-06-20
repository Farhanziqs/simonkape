<?php
// simonkapedb/public/index.php
session_start();
// Mengaktifkan error reporting untuk pengembangan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definisikan path absolut ke folder aplikasi
define('APP_ROOT', dirname(__DIR__)); // dirname(__DIR__) akan mengarah ke simonkapedb/

// Memuat konfigurasi (akan kita buat di step selanjutnya)
require_once APP_ROOT . '/app/config/app.php';
require_once APP_ROOT . '/app/config/database.php';

// Memuat kelas inti (akan kita buat di step selanjutnya)
require_once APP_ROOT . '/app/core/Database.php';

require_once APP_ROOT . '/app/core/Controller.php';

require_once APP_ROOT . '/app/core/App.php';


// Autoload Models dan Controllers secara sederhana (bisa diganti Composer nanti)
spl_autoload_register(function($className) {
    // Coba load dari models
    $path = APP_ROOT . '/app/models/' . $className . '.php';
    if (file_exists($path)) {
        require_once $path;
        return;
    }
    // Coba load dari controllers
    $path = APP_ROOT . '/app/controllers/' . $className . '.php';
    if (file_exists($path)) {
        require_once $path;
        return;
    }
});

// Inisialisasi aplikasi
$app = new App();
