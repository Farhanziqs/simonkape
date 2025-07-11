<?php
// simonkapedb/app/models/Instansi.php

class Instansi {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllInstansi() {
        $this->db->query('SELECT * FROM instansi ORDER BY nama_instansi ASC');
        return $this->db->resultSet();
    }

    public function getInstansiById($id) {
        $this->db->query('SELECT * FROM instansi WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getInstansiByName($name) {
        $this->db->query('SELECT * FROM instansi WHERE nama_instansi = :name');
        $this->db->bind(':name', $name);
        return $this->db->single();
    }

    public function addInstansi($data) {
        $this->db->query('INSERT INTO instansi (nama_instansi, alamat, kota_kab, telepon, email, pic)
                          VALUES (:nama_instansi, :alamat, :kota_kab, :telepon, :email, :pic)');
        $this->db->bind(':nama_instansi', $data['nama_instansi']);
        $this->db->bind(':alamat', $data['alamat']);
        $this->db->bind(':kota_kab', $data['kota_kab']);
        $this->db->bind(':telepon', $data['telepon']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':pic', $data['pic']);

        return $this->db->execute();
    }

    public function updateInstansi($data) {
        $this->db->query('UPDATE instansi SET nama_instansi = :nama_instansi,
                          alamat = :alamat, kota_kab = :kota_kab, telepon = :telepon, email = :email, pic = :pic
                          WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nama_instansi', $data['nama_instansi']);
        $this->db->bind(':alamat', $data['alamat']);
        $this->db->bind(':kota_kab', $data['kota_kab']);
        $this->db->bind(':telepon', $data['telepon']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':pic', $data['pic']);

        return $this->db->execute();
    }

    public function deleteInstansi($id) {
        $this->db->query('DELETE FROM instansi WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Untuk dashboard admin
    public function getTotalInstansiTerdaftar() {
        $this->db->query("SELECT COUNT(*) as total FROM instansi");
        $result = $this->db->single();
        return $result['total'];
    }
}
