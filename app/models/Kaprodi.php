<?php
// simonkapedb/app/models/Kaprodi.php

class Kaprodi {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllKaprodi() {
        $this->db->query('SELECT * FROM kaprodi ORDER BY nama_lengkap ASC');
        return $this->db->resultSet();
    }

    public function getKaprodiById($id) {
        $this->db->query('SELECT * FROM kaprodi WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getKaprodiByNidn($nidn) {
        $this->db->query('SELECT * FROM kaprodi WHERE nidn = :nidn');
        $this->db->bind(':nidn', $nidn);
        return $this->db->single();
    }

    public function addKaprodi($data) {
        $this->db->query('INSERT INTO kaprodi (nama_lengkap, nidn, email, nomor_telepon, program_studi)
                          VALUES (:nama_lengkap, :nidn, :email, :nomor_telepon, :program_studi)');
        $this->db->bind(':nama_lengkap', $data['nama_lengkap']);
        $this->db->bind(':nidn', $data['nidn']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':nomor_telepon', $data['nomor_telepon']);
        $this->db->bind(':program_studi', $data['program_studi']);
        return $this->db->execute();
    }

    public function updateKaprodi($data) {
        $this->db->query('UPDATE kaprodi SET nama_lengkap = :nama_lengkap, nidn = :nidn, email = :email,
                          nomor_telepon = :nomor_telepon, program_studi = :program_studi WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nama_lengkap', $data['nama_lengkap']);
        $this->db->bind(':nidn', $data['nidn']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':nomor_telepon', $data['nomor_telepon']);
        $this->db->bind(':program_studi', $data['program_studi']);
        return $this->db->execute();
    }

    public function deleteKaprodi($id) {
        $this->db->query('DELETE FROM kaprodi WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
