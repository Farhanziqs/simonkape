<?php
// simonkapedb/app/models/Absen.php

class Absen {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAbsenByMahasiswaAndDate($mahasiswa_id, $tanggal) {
        $this->db->query('SELECT * FROM absen_harian WHERE mahasiswa_id = :mahasiswa_id AND tanggal = :tanggal');
        $this->db->bind(':mahasiswa_id', $mahasiswa_id);
        $this->db->bind(':tanggal', $tanggal);
        return $this->db->single();
    }

    public function addAbsen($data) {
        $this->db->query('INSERT INTO absen_harian (mahasiswa_id, tanggal, waktu_absen, status_kehadiran)
                          VALUES (:mahasiswa_id, :tanggal, :waktu_absen, :status_kehadiran)');
        $this->db->bind(':mahasiswa_id', $data['mahasiswa_id']);
        $this->db->bind(':tanggal', $data['tanggal']);
        $this->db->bind(':waktu_absen', $data['waktu_absen']);
        $this->db->bind(':status_kehadiran', $data['status_kehadiran']);
        return $this->db->execute();
    }

    public function updateAbsenStatus($id, $status_kehadiran) {
        $this->db->query('UPDATE absen_harian SET status_kehadiran = :status_kehadiran WHERE id = :id');
        $this->db->bind(':status_kehadiran', $status_kehadiran);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getRiwayatAbsenByMahasiswaId($mahasiswa_id) {
        $this->db->query('SELECT * FROM absen_harian WHERE mahasiswa_id = :mahasiswa_id ORDER BY tanggal DESC, waktu_absen DESC');
        $this->db->bind(':mahasiswa_id', $mahasiswa_id);
        return $this->db->resultSet();
    }

    // START - New function for Dosen role
    public function getAbsenByMahasiswaAndDateRange($mahasiswa_id, $start_date, $end_date) {
    $this->db->query('SELECT a.*, m.nama_lengkap
                      FROM absen_harian a
                      JOIN mahasiswa m ON a.mahasiswa_id = m.id
                      WHERE a.mahasiswa_id = :mahasiswa_id
                        AND a.tanggal BETWEEN :start_date AND :end_date
                      ORDER BY a.tanggal DESC');
    $this->db->bind(':mahasiswa_id', $mahasiswa_id);
    $this->db->bind(':start_date', $start_date);
    $this->db->bind(':end_date', $end_date);
    return $this->db->resultSet();
    }

    public function getAbsenByDosenIdAndDateRange($dosen_id, $start_date, $end_date) {
        $this->db->query('SELECT ah.*, m.nama_lengkap, m.nim
                          FROM absen_harian ah
                          JOIN mahasiswa m ON ah.mahasiswa_id = m.id
                          WHERE m.dosen_pembimbing_id = :dosen_id
                          AND ah.tanggal BETWEEN :start_date AND :end_date
                          ORDER BY ah.tanggal DESC, m.nama_lengkap ASC');
        $this->db->bind(':dosen_id', $dosen_id);
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        return $this->db->resultSet();
    }
    // END - New function for Dosen role
}
