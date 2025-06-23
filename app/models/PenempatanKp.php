<?php
// simonkapedb/app/models/PenempatanKp.php

class PenempatanKp {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllPenempatanKp() {
        $this->db->query('SELECT pk.*, i.nama_instansi, d.nama_lengkap as nama_dosen
                          FROM penempatan_kp pk
                          JOIN instansi i ON pk.instansi_id = i.id
                          JOIN dosen d ON pk.dosen_pembimbing_id = d.id
                          ORDER BY pk.created_at DESC');
        return $this->db->resultSet();
    }

    public function getPenempatanKpById($id) {
        $this->db->query('SELECT pk.*, i.nama_instansi, d.nama_lengkap as nama_dosen
                          FROM penempatan_kp pk
                          JOIN instansi i ON pk.instansi_id = i.id
                          JOIN dosen d ON pk.dosen_pembimbing_id = d.id
                          WHERE pk.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addPenempatanKp($data) {
        // Mulai transaksi untuk memastikan semua insert berhasil
        $this->db->query("SET autocommit = 0");
        $this->db->execute(); // Jalankan query ini dulu

        // 1. Insert ke tabel penempatan_kp
        $this->db->query('INSERT INTO penempatan_kp (instansi_id, dosen_pembimbing_id, nama_kelompok, tanggal_mulai, tanggal_selesai)
                          VALUES (:instansi_id, :dosen_pembimbing_id, :nama_kelompok, :tanggal_mulai, :tanggal_selesai)');
        $this->db->bind(':instansi_id', $data['instansi_id']);
        $this->db->bind(':dosen_pembimbing_id', $data['dosen_pembimbing_id']);
        $this->db->bind(':nama_kelompok', $data['nama_kelompok']);
        $this->db->bind(':tanggal_mulai', $data['tanggal_mulai']);
        $this->db->bind(':tanggal_selesai', $data['tanggal_selesai']);

        if (!$this->db->execute()) {
            $this->db->query("ROLLBACK");
            return false;
        }

        $penempatan_kp_id = $this->db->lastInsertId(); // Ambil ID yang baru saja di-insert

        // 2. Insert ke tabel penempatan_kp_mahasiswa dan update status mahasiswa
        if (!empty($data['mahasiswa_ids'])) {
            foreach ($data['mahasiswa_ids'] as $mahasiswa_id) {
                $this->db->query('INSERT INTO penempatan_kp_mahasiswa (penempatan_kp_id, mahasiswa_id)
                                  VALUES (:penempatan_kp_id, :mahasiswa_id)');
                $this->db->bind(':penempatan_kp_id', $penempatan_kp_id);
                $this->db->bind(':mahasiswa_id', $mahasiswa_id);
                if (!$this->db->execute()) {
                    $this->db->query("ROLLBACK");
                    return false;
                }

                // Update dosen_pembimbing_id dan status_kp mahasiswa
                $this->db->query('UPDATE mahasiswa SET dosen_pembimbing_id = :dosen_pembimbing_id, instansi_id = :instansi_id, status_kp = "Sedang KP" WHERE id = :id');
                $this->db->bind(':dosen_pembimbing_id', $data['dosen_pembimbing_id']);
                $this->db->bind(':instansi_id', $data['instansi_id']);
                $this->db->bind(':id', $mahasiswa_id);
                if (!$this->db->execute()) {
                    $this->db->query("ROLLBACK");
                    return false;
                }
            }
        }

        $this->db->query("COMMIT");
        $this->db->execute();
        return true;
    }

    public function updatePenempatanKp($data) {
        // Mulai transaksi
        $this->db->query("SET autocommit = 0");
        $this->db->execute();

        // 1. Update tabel penempatan_kp
        $this->db->query('UPDATE penempatan_kp SET instansi_id = :instansi_id, dosen_pembimbing_id = :dosen_pembimbing_id,
                          nama_kelompok = :nama_kelompok, tanggal_mulai = :tanggal_mulai, tanggal_selesai = :tanggal_selesai
                          WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':instansi_id', $data['instansi_id']);
        $this->db->bind(':dosen_pembimbing_id', $data['dosen_pembimbing_id']);
        $this->db->bind(':nama_kelompok', $data['nama_kelompok']);
        $this->db->bind(':tanggal_mulai', $data['tanggal_mulai']);
        $this->db->bind(':tanggal_selesai', $data['tanggal_selesai']);
        if (!$this->db->execute()) {
            $this->db->query("ROLLBACK");
            return false;
        }

        // 2. Hapus semua mahasiswa dari penempatan_kp_mahasiswa untuk penempatan ini
        // dan reset status KP mereka jika tidak lagi di penempatan ini
        $current_mahasiswa_ids_in_kp = array_column($this->getMahasiswaByPenempatanId($data['id']), 'id');
        foreach ($current_mahasiswa_ids_in_kp as $mhs_id) {
            if (!in_array($mhs_id, $data['mahasiswa_ids'])) {
                // Jika mahasiswa ini tidak lagi ada di daftar baru, reset status KP-nya
                $this->db->query('UPDATE mahasiswa SET dosen_pembimbing_id = NULL, instansi_id = NULL, status_kp = "Belum Terdaftar" WHERE id = :id');
                $this->db->bind(':id', $mhs_id);
                if (!$this->db->execute()) {
                    $this->db->query("ROLLBACK");
                    return false;
                }
            }
        }
        $this->db->query('DELETE FROM penempatan_kp_mahasiswa WHERE penempatan_kp_id = :penempatan_kp_id');
        $this->db->bind(':penempatan_kp_id', $data['id']);
        if (!$this->db->execute()) {
            $this->db->query("ROLLBACK");
            return false;
        }


        // 3. Tambahkan mahasiswa yang dipilih ke penempatan_kp_mahasiswa dan update status mahasiswa
        if (!empty($data['mahasiswa_ids'])) {
            foreach ($data['mahasiswa_ids'] as $mahasiswa_id) {
                $this->db->query('INSERT INTO penempatan_kp_mahasiswa (penempatan_kp_id, mahasiswa_id)
                                  VALUES (:penempatan_kp_id, :mahasiswa_id)');
                $this->db->bind(':penempatan_kp_id', $data['id']);
                $this->db->bind(':mahasiswa_id', $mahasiswa_id);
                if (!$this->db->execute()) {
                    $this->db->query("ROLLBACK");
                    return false;
                }
                // Update dosen_pembimbing_id dan status_kp mahasiswa
                $this->db->query('UPDATE mahasiswa SET dosen_pembimbing_id = :dosen_pembimbing_id, instansi_id = :instansi_id, status_kp = "Sedang KP" WHERE id = :id');
                $this->db->bind(':dosen_pembimbing_id', $data['dosen_pembimbing_id']);
                $this->db->bind(':instansi_id', $data['instansi_id']);
                $this->db->bind(':id', $mahasiswa_id);
                if (!$this->db->execute()) {
                    $this->db->query("ROLLBACK");
                    return false;
                }
            }
        }

        $this->db->query("COMMIT");
        $this->db->execute();
        return true;
    }


    public function deletePenempatanKp($id) {
        // Saat penempatan KP dihapus, reset status KP mahasiswa yang terkait
        $this->db->query('UPDATE mahasiswa SET dosen_pembimbing_id = NULL, instansi_id = NULL, status_kp = "Belum Terdaftar"
                          WHERE id IN (SELECT mahasiswa_id FROM penempatan_kp_mahasiswa WHERE penempatan_kp_id = :id)');
        $this->db->bind(':id', $id);
        $this->db->execute(); // Jalankan update dulu

        $this->db->query('DELETE FROM penempatan_kp WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getMahasiswaByPenempatanId($penempatan_kp_id) {
        $this->db->query('SELECT m.id, m.nim, m.nama_lengkap
                          FROM mahasiswa m
                          JOIN penempatan_kp_mahasiswa pkm ON m.id = pkm.mahasiswa_id
                          WHERE pkm.penempatan_kp_id = :penempatan_kp_id');
        $this->db->bind(':penempatan_kp_id', $penempatan_kp_id);
        return $this->db->resultSet();
    }
}
