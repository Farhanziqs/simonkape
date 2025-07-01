<?php
// simonkapedb/app/models/Dosen.php

class Dosen {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllDosen() {
        $this->db->query('SELECT * FROM dosen ORDER BY nama_lengkap ASC');
        return $this->db->resultSet();
    }

    public function getDosenById($id) {
        $this->db->query('SELECT * FROM dosen WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getDosenByNidn($nidn) {
        $this->db->query('SELECT * FROM dosen WHERE nidn = :nidn');
        $this->db->bind(':nidn', $nidn);
        return $this->db->single();
    }

    public function getDosenByEmail($email) {
        $this->db->query('SELECT * FROM dosen WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function addDosen($data) {
        $this->db->query('INSERT INTO dosen (nidn, nama_lengkap, email, nomor_telepon, status_aktif)
                          VALUES (:nidn, :nama_lengkap, :email, :nomor_telepon, :status_aktif)');
        $this->db->bind(':nidn', $data['nidn']);
        $this->db->bind(':nama_lengkap', $data['nama_lengkap']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':nomor_telepon', $data['nomor_telepon']);
        $this->db->bind(':status_aktif', $data['status_aktif']);

        return $this->db->execute();
    }

    public function updateDosen($data) {
        $this->db->query('UPDATE dosen SET nidn = :nidn, nama_lengkap = :nama_lengkap, email = :email,
                          nomor_telepon = :nomor_telepon, status_aktif = :status_aktif WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nidn', $data['nidn']);
        $this->db->bind(':nama_lengkap', $data['nama_lengkap']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':nomor_telepon', $data['nomor_telepon']);
        $this->db->bind(':status_aktif', $data['status_aktif']);

        return $this->db->execute();
    }

    public function deleteDosen($id) {
        $this->db->query('DELETE FROM dosen WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Untuk dashboard admin
    public function getTotalDosenPembimbing() {
        $this->db->query("SELECT COUNT(*) as total FROM dosen WHERE status_aktif = 'Aktif'");
        $result = $this->db->single();
        return $result['total'];
    }

    // Contoh untuk laporan pembagian dosen
    public function getPembagianDosen() {
        $this->db->query('SELECT d.nama_lengkap AS dosen_pembimbing, GROUP_CONCAT(m.nama_lengkap SEPARATOR ", ") AS mahasiswa_bimbingan
                          FROM dosen d
                          LEFT JOIN mahasiswa m ON d.id = m.dosen_pembimbing_id
                          GROUP BY d.id
                          ORDER BY d.nama_lengkap ASC');
        return $this->db->resultSet();
    }

    public function getMahasiswaByDosenId($dosen_id) {
        $this->db->query('SELECT * FROM mahasiswa WHERE dosen_pembimbing_id = :dosen_id');
        $this->db->bind(':dosen_id', $dosen_id);
        return $this->db->resultSet();
    }
}
