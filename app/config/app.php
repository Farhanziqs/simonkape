<?php
// simonkapedb/app/config/app.php

define('BASE_URL', 'http://localhost/simonkapedb/public');
define('DEFAULT_USER_PASSWORD', 'password123'); // Password default untuk user baru

// Tambahan konfigurasi untuk fitur mahasiswa
define('TIMEZONE', 'Asia/Makassar'); // Sesuaikan dengan zona waktu lokasi Anda
define('ABSEN_START_HOUR', 6); // Jam mulai absen (06:00)
define('ABSEN_END_HOUR', 10);  // Jam berakhir absen (10:00)

define('KAPRODI_NIDN', '0913098203'); // NIDN Ketua Program Studi