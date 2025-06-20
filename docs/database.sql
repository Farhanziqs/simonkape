-- simonkapedb/docs/database.sql (optional: simpan ini di folder docs untuk referensi)

-- Tabel untuk pengguna (admin, mahasiswa, dosen)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Simpan hashed password
    role ENUM('admin', 'mahasiswa', 'dosen') NOT NULL,
    user_id INT NULL -- Foreign key ke tabel mahasiswa atau dosen
);

-- Tabel untuk data mahasiswa
CREATE TABLE mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    program_studi VARCHAR(100) NOT NULL,
    instansi_id INT NULL, -- FK ke tabel instansi
    dosen_pembimbing_id INT NULL, -- FK ke tabel dosen
    status_kp VARCHAR(50) DEFAULT 'Belum Terdaftar',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel untuk data dosen
CREATE TABLE dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nidn VARCHAR(20) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    status_aktif ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    email VARCHAR(255) UNIQUE NOT NULL,
    nomor_telepon VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel untuk data instansi
CREATE TABLE instansi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_instansi VARCHAR(255) UNIQUE NOT NULL,
    bidang_kerja VARCHAR(255) NULL,
    alamat TEXT NULL,
    kota_kab VARCHAR(100) NULL,
    telepon VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    pic VARCHAR(255) NULL, -- Person In Charge
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel untuk absen harian mahasiswa
CREATE TABLE absen_harian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    tanggal DATE NOT NULL,
    waktu_absen TIME NOT NULL,
    status_kehadiran ENUM('Hadir', 'Izin', 'Sakit', 'Alpha') DEFAULT 'Hadir',
    CONSTRAINT fk_absen_mahasiswa FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
    UNIQUE (mahasiswa_id, tanggal) -- Memastikan 1 absen per mahasiswa per hari
);

-- Tabel untuk logbook harian mahasiswa
CREATE TABLE logbook_harian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    tanggal DATE NOT NULL,
    uraian_kegiatan TEXT NOT NULL,
    dokumentasi VARCHAR(255) NULL, -- Path file dokumentasi
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logbook_mahasiswa FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
    UNIQUE (mahasiswa_id, tanggal) -- Memastikan 1 logbook per mahasiswa per hari
);

-- Tabel untuk laporan mingguan mahasiswa
CREATE TABLE laporan_mingguan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    periode_mingguan VARCHAR(100) NOT NULL, -- Contoh: "29 Juli - 02 Agustus 2024"
    file_laporan VARCHAR(255) NULL, -- Path file PDF laporan
    status_laporan ENUM('Belum Dibuat', 'Menunggu Persetujuan', 'Disetujui', 'Ditolak', 'Revisi') DEFAULT 'Belum Dibuat',
    feedback_dosen TEXT NULL,
    dosen_pembimbing_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_laporan_mahasiswa FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
    CONSTRAINT fk_laporan_dosen FOREIGN KEY (dosen_pembimbing_id) REFERENCES dosen(id) ON DELETE SET NULL
);

-- Tambahkan beberapa data awal (opsional)
-- Admin
INSERT INTO users (username, password, role) VALUES ('adminprodi', '$2y$10$Q7.J9.C6C6C6C6C6C6C6eO2K6W0A2.oP.mF8.H9/V0xW.V1P.g4U.', 'admin'); -- Password: password123

-- Dosen (contoh, sesuaikan user_id nanti setelah menambahkan dosen di manajemen admin)
-- Contoh password: password123
INSERT INTO users (username, password, role) VALUES ('dosen1', '$2y$10$Q7.J9.C6C6C6C6C6C6C6eO2K6W0A2.oP.mF8.H9/V0xW.V1P.g4U.', 'dosen');
INSERT INTO dosen (nidn, nama_lengkap, email) VALUES ('001234501', 'Dr. Ir. Endang Purnawati, M.Kom.', 'endang.p@example.com');
UPDATE users SET user_id = (SELECT id FROM dosen WHERE nidn = '001234501') WHERE username = 'dosen1';

-- Mahasiswa (contoh, sesuaikan user_id nanti setelah menambahkan mahasiswa di manajemen admin)
-- Contoh password: password123
INSERT INTO users (username, password, role) VALUES ('mahasiswa1', '$2y$10$Q7.J9.C6C6C6C6C6C6C6eO2K6W0A2.oP.mF8.H9/V0xW.V1P.g4U.', 'mahasiswa');
INSERT INTO mahasiswa (nim, nama_lengkap, program_studi) VALUES ('192001', 'Ahmad Dahlan', 'Teknik Informatika');
UPDATE users SET user_id = (SELECT id FROM mahasiswa WHERE nim = '192001') WHERE username = 'mahasiswa1';

-- simonkapedb/docs/database.sql (Tambahkan ini)

CREATE TABLE penempatan_kp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instansi_id INT NOT NULL,
    dosen_pembimbing_id INT NOT NULL,
    nama_kelompok VARCHAR(255) NULL, -- Nama kelompok (opsional)
    tanggal_mulai DATE NULL,
    tanggal_selesai DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_penempatan_instansi FOREIGN KEY (instansi_id) REFERENCES instansi(id) ON DELETE CASCADE,
    CONSTRAINT fk_penempatan_dosen FOREIGN KEY (dosen_pembimbing_id) REFERENCES dosen(id) ON DELETE CASCADE
);

-- Tabel pivot untuk mahasiswa dalam satu penempatan/kelompok KP
CREATE TABLE penempatan_kp_mahasiswa (
    penempatan_kp_id INT NOT NULL,
    mahasiswa_id INT NOT NULL,
    PRIMARY KEY (penempatan_kp_id, mahasiswa_id),
    CONSTRAINT fk_pkm_penempatan FOREIGN KEY (penempatan_kp_id) REFERENCES penempatan_kp(id) ON DELETE CASCADE,
    CONSTRAINT fk_pkm_mahasiswa FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE
);
