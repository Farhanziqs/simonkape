<?php
// simonkapedb/app/models/Mahasiswa.php

class Mahasiswa {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllMahasiswa() {
        $this->db->query('SELECT m.*, i.nama_instansi, d.nama_lengkap as nama_dosen
                          FROM mahasiswa m
                          LEFT JOIN instansi i ON m.instansi_id = i.id
                          LEFT JOIN dosen d ON m.dosen_pembimbing_id = d.id
                          ORDER BY m.nim ASC');
        return $this->db->resultSet();
    }

    public function getMahasiswaById($id) {
        $this->db->query('SELECT m.*, i.nama_instansi, d.nama_lengkap as nama_dosen
                          FROM mahasiswa m
                          LEFT JOIN instansi i ON m.instansi_id = i.id
                          LEFT JOIN dosen d ON m.dosen_pembimbing_id = d.id
                          WHERE m.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getMahasiswaByNim($nim) {
        $this->db->query('SELECT * FROM mahasiswa WHERE nim = :nim');
        $this->db->bind(':nim', $nim);
        return $this->db->single();
    }

    public function addMahasiswa($data) {
        $this->db->query('INSERT INTO mahasiswa (nim, nama_lengkap, program_studi, instansi_id, dosen_pembimbing_id, status_kp)
                          VALUES (:nim, :nama_lengkap, :program_studi, :instansi_id, :dosen_pembimbing_id, :status_kp)');
        $this->db->bind(':nim', $data['nim']);
        $this->db->bind(':nama_lengkap', $data['nama_lengkap']);
        $this->db->bind(':program_studi', $data['program_studi']);
        $this->db->bind(':instansi_id', $data['instansi_id']);
        $this->db->bind(':dosen_pembimbing_id', $data['dosen_pembimbing_id']);
        $this->db->bind(':status_kp', $data['status_kp']);

        return $this->db->execute();
    }

    public function updateMahasiswa($data) {
        $this->db->query('UPDATE mahasiswa SET nim = :nim, nama_lengkap = :nama_lengkap, program_studi = :program_studi,
                          instansi_id = :instansi_id, dosen_pembimbing_id = :dosen_pembimbing_id, status_kp = :status_kp
                          WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nim', $data['nim']);
        $this->db->bind(':nama_lengkap', $data['nama_lengkap']);
        $this->db->bind(':program_studi', $data['program_studi']);
        $this->db->bind(':instansi_id', $data['instansi_id']);
        $this->db->bind(':dosen_pembimbing_id', $data['dosen_pembimbing_id']);
        $this->db->bind(':status_kp', $data['status_kp']);

        return $this->db->execute();
    }

    public function deleteMahasiswa($id) {
        $this->db->query('DELETE FROM mahasiswa WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Untuk dashboard admin
    public function getTotalMahasiswaAktifKp() {
        $this->db->query("SELECT COUNT(*) as total FROM mahasiswa WHERE status_kp = 'Sedang KP' OR status_kp = 'Terdaftar'");
        $result = $this->db->single();
        return $result['total'];
    }

    public function getTotalLaporanMingguanPerluVerifikasi() {
        // Asumsi 'Menunggu Persetujuan' adalah status yang perlu diverifikasi
        $this->db->query("SELECT COUNT(*) as total FROM laporan_mingguan WHERE status_laporan = 'Menunggu Persetujuan'");
        $result = $this->db->single();
        return $result['total'];
    }

    public function getMahasiswaBelumDitempatkan() {
        // Mahasiswa yang status_kp nya 'Terdaftar' atau 'Belum Terdaftar' dan belum ada di tabel penempatan_kp_mahasiswa
        $this->db->query('SELECT m.id, m.nim, m.nama_lengkap
                          FROM mahasiswa m
                          LEFT JOIN penempatan_kp_mahasiswa pkm ON m.id = pkm.mahasiswa_id
                          WHERE pkm.mahasiswa_id IS NULL AND (m.status_kp = "Belum Terdaftar" OR m.status_kp = "Terdaftar")
                          ORDER BY m.nama_lengkap ASC');
        return $this->db->resultSet();
    }

    // Contoh untuk laporan rekap absensi
    public function getRekapAbsensi() {
        $this->db->query('SELECT m.nim, m.nama_lengkap, ah.tanggal, ah.status_kehadiran
                          FROM mahasiswa m
                          JOIN absen_harian ah ON m.id = ah.mahasiswa_id
                          ORDER BY ah.tanggal DESC, m.nama_lengkap ASC LIMIT 5'); // Ambil 5 data terbaru sebagai contoh
        return $this->db->resultSet();
    }

    // Contoh untuk laporan rekap progress laporan
    public function getRekapProgressLaporan() {
        $this->db->query('SELECT m.nim, m.nama_lengkap, lm.periode_mingguan, lm.status_laporan
                          FROM mahasiswa m
                          JOIN laporan_mingguan lm ON m.id = lm.mahasiswa_id
                          ORDER BY lm.created_at DESC, m.nama_lengkap ASC LIMIT 5'); // Ambil 5 data terbaru sebagai contoh
        return $this->db->resultSet();
    }

    // Contoh untuk laporan mahasiswa per status KP
    public function getMahasiswaPerStatusKp() {
        $this->db->query('SELECT status_kp, COUNT(*) as total FROM mahasiswa GROUP BY status_kp');
        return $this->db->resultSet();
    }
}
