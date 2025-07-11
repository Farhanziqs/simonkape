<?php
// simonkapedb/app/models/LaporanMingguan.php

class LaporanMingguan {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function addLaporan($data) {
        $this->db->query('INSERT INTO laporan_mingguan (mahasiswa_id, periode_mingguan, file_laporan, status_laporan, dosen_pembimbing_id)
                          VALUES (:mahasiswa_id, :periode_mingguan, :file_laporan, :status_laporan, :dosen_pembimbing_id)');
        $this->db->bind(':mahasiswa_id', $data['mahasiswa_id']);
        $this->db->bind(':periode_mingguan', $data['periode_mingguan']);
        $this->db->bind(':file_laporan', $data['file_laporan']);
        $this->db->bind(':status_laporan', $data['status_laporan']);
        $this->db->bind(':dosen_pembimbing_id', $data['dosen_pembimbing_id']);
        try {
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getLaporanByMahasiswaId($mahasiswa_id) {
        $this->db->query('SELECT lm.*, d.nama_lengkap as nama_dosen
                          FROM laporan_mingguan lm
                          LEFT JOIN dosen d ON lm.dosen_pembimbing_id = d.id
                          WHERE lm.mahasiswa_id = :mahasiswa_id
                          ORDER BY lm.created_at DESC');
        $this->db->bind(':mahasiswa_id', $mahasiswa_id);
        return $this->db->resultSet();
    }

    public function getLaporanById($id) {
        $this->db->query('SELECT lm.*, m.nama_lengkap as nama_mahasiswa, m.nim, m.program_studi, i.nama_instansi, d.nama_lengkap as nama_dosen, d.nidn
                          FROM laporan_mingguan lm
                          JOIN mahasiswa m ON lm.mahasiswa_id = m.id
                          LEFT JOIN instansi i ON m.instansi_id = i.id
                          LEFT JOIN dosen d ON lm.dosen_pembimbing_id = d.id
                          WHERE lm.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateLaporanStatus($id, $status, $feedback = null) {
        $this->db->query('UPDATE laporan_mingguan SET status_laporan = :status_laporan, feedback_dosen = :feedback_dosen WHERE id = :id');
        $this->db->bind(':status_laporan', $status);
        $this->db->bind(':feedback_dosen', $feedback);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // START - New function for Dosen role
    public function getLaporanByDosenId($dosen_id) {
        $this->db->query('SELECT lm.*, m.nama_lengkap, m.nim
                          FROM laporan_mingguan lm
                          JOIN mahasiswa m ON lm.mahasiswa_id = m.id
                          WHERE lm.dosen_pembimbing_id = :dosen_id
                          ORDER BY lm.created_at DESC');
        $this->db->bind(':dosen_id', $dosen_id);
        return $this->db->resultSet();
    }

    public function getLaporanByDosenAndPeriode($dosen_id, $periode) {
        $this->db->query('SELECT lm.*, m.nama_lengkap, m.nim
                          FROM laporan_mingguan lm
                          JOIN mahasiswa m ON lm.mahasiswa_id = m.id
                          WHERE lm.dosen_pembimbing_id = :dosen_id AND lm.periode_mingguan = :periode
                          ORDER BY lm.created_at DESC');
        $this->db->bind(':dosen_id', $dosen_id);
        $this->db->bind(':periode', $periode);
        return $this->db->resultSet();
    }

    public function getTotalLaporanMenunggu($dosen_id) {
        $this->db->query("SELECT COUNT(*) as total FROM laporan_mingguan WHERE dosen_pembimbing_id = :dosen_id AND status_laporan = 'Menunggu Persetujuan'");
        $this->db->bind(':dosen_id', $dosen_id);
        $result = $this->db->single();
        return $result['total'];
    }

    // Method baru untuk Kaprodi: Mengambil semua laporan mingguan dengan detail instansi dan dosen
    public function getAllLaporanMingguanWithDetails() {
        $this->db->query('SELECT
                            lm.*,
                            m.nim,
                            m.nama_lengkap as nama_mahasiswa,
                            i.nama_instansi,
                            d.nama_lengkap as nama_dosen,
                            d.nidn as nidn_dosen
                          FROM laporan_mingguan lm
                          JOIN mahasiswa m ON lm.mahasiswa_id = m.id
                          LEFT JOIN instansi i ON m.instansi_id = i.id
                          LEFT JOIN dosen d ON m.dosen_pembimbing_id = d.id
                          ORDER BY i.nama_instansi ASC, d.nama_lengkap ASC, lm.periode_mingguan DESC');
        return $this->db->resultSet();
    }
    // END - New function for Dosen role
}
