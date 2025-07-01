<?php
// simonkapedb/app/models/Logbook.php

class Logbook {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getLogbookByMahasiswaAndDate($mahasiswa_id, $tanggal) {
        $this->db->query('SELECT * FROM logbook_harian WHERE mahasiswa_id = :mahasiswa_id AND tanggal = :tanggal');
        $this->db->bind(':mahasiswa_id', $mahasiswa_id);
        $this->db->bind(':tanggal', $tanggal);
        return $this->db->single();
    }

    public function addLogbook($data) {
        $this->db->query('INSERT INTO logbook_harian (mahasiswa_id, tanggal, uraian_kegiatan, dokumentasi)
                          VALUES (:mahasiswa_id, :tanggal, :uraian_kegiatan, :dokumentasi)');
        $this->db->bind(':mahasiswa_id', $data['mahasiswa_id']);
        $this->db->bind(':tanggal', $data['tanggal']);
        $this->db->bind(':uraian_kegiatan', $data['uraian_kegiatan']);
        $this->db->bind(':dokumentasi', $data['dokumentasi']);
        return $this->db->execute();
    }

    public function getRiwayatLogbookByMahasiswaId($mahasiswa_id) {
        $this->db->query('SELECT * FROM logbook_harian WHERE mahasiswa_id = :mahasiswa_id ORDER BY tanggal DESC');
        $this->db->bind(':mahasiswa_id', $mahasiswa_id);
        return $this->db->resultSet();
    }

    public function getLogbooksByDateRange($mahasiswa_id, $start_date, $end_date) {
        // PERBAIKAN: Tambahkan JOIN dengan tabel mahasiswa untuk mengambil nama_lengkap
        $this->db->query('SELECT lh.*, m.nama_lengkap
                          FROM logbook_harian lh
                          JOIN mahasiswa m ON lh.mahasiswa_id = m.id
                          WHERE lh.mahasiswa_id = :mahasiswa_id
                          AND lh.tanggal BETWEEN :start_date AND :end_date
                          ORDER BY lh.tanggal ASC');

        $this->db->bind(':mahasiswa_id', $mahasiswa_id);
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        return $this->db->resultSet();
    }

    // START - New function for Dosen role
    public function getLogbookById($id) {
        $this->db->query('SELECT * FROM logbook_harian WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getLogbookByDosenIdAndDateRange($dosen_id, $start_date, $end_date) {
        $this->db->query('SELECT lh.*, m.nama_lengkap, m.nim
                          FROM logbook_harian lh
                          JOIN mahasiswa m ON lh.mahasiswa_id = m.id
                          WHERE m.dosen_pembimbing_id = :dosen_id
                          AND lh.tanggal BETWEEN :start_date AND :end_date
                          ORDER BY lh.tanggal DESC, m.nama_lengkap ASC');
        $this->db->bind(':dosen_id', $dosen_id);
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        return $this->db->resultSet();
    }
    // END - New function for Dosen role
}
