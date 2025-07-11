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
        // Menambahkan kolom surat pengantar dan surat penarikan yang baru
        $this->db->query('SELECT pk.*, i.nama_instansi, d.nama_lengkap as nama_dosen, i.alamat as instansi_alamat, i.kota_kab as instansi_kota_kab,
                                 pk.nomor_surat_kp, pk.kepada_yth_kp, pk.alamat_tujuan_kp,
                                 pk.nomor_surat_penarikan_kp, pk.kepada_yth_penarikan_kp, pk.alamat_tujuan_penarikan_kp, pk.tanggal_penarikan_kp
                          FROM penempatan_kp pk
                          JOIN instansi i ON pk.instansi_id = i.id
                          JOIN dosen d ON pk.dosen_pembimbing_id = d.id
                          WHERE pk.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getMahasiswaPenempatanDetails() {
        $this->db->query('SELECT
                            m.id as mahasiswa_id,
                            m.nim,
                            m.nama_lengkap as nama_mahasiswa,
                            m.program_studi,
                            pk.id as penempatan_id,
                            pk.tanggal_mulai,
                            pk.tanggal_selesai,
                            i.nama_instansi,
                            d.nama_lengkap as nama_dosen_pembimbing
                          FROM penempatan_kp_mahasiswa pkm
                          JOIN mahasiswa m ON pkm.mahasiswa_id = m.id
                          JOIN penempatan_kp pk ON pkm.penempatan_kp_id = pk.id
                          JOIN instansi i ON pk.instansi_id = i.id
                          JOIN dosen d ON pk.dosen_pembimbing_id = d.id
                          ORDER BY pk.tanggal_mulai DESC, m.nim ASC');
        return $this->db->resultSet();
    }

    public function addPenempatanKp($data) {
        $this->db->query("SET autocommit = 0");
        $this->db->execute();

        // Query INSERT tanpa kolom surat, karena ini diisi saat generate
        $this->db->query('INSERT INTO penempatan_kp (instansi_id, dosen_pembimbing_id, tanggal_mulai, tanggal_selesai)
                          VALUES (:instansi_id, :dosen_pembimbing_id, :tanggal_mulai, :tanggal_selesai)');
        $this->db->bind(':instansi_id', $data['instansi_id']);
        $this->db->bind(':dosen_pembimbing_id', $data['dosen_pembimbing_id']);
        $this->db->bind(':tanggal_mulai', $data['tanggal_mulai']);
        $this->db->bind(':tanggal_selesai', $data['tanggal_selesai']);

        if (!$this->db->execute()) {
            $this->db->query("ROLLBACK");
            return false;
        }

        $penempatan_kp_id = $this->db->lastInsertId();

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
        $this->db->query("SET autocommit = 0");
        $this->db->execute();

        // Query UPDATE tanpa kolom surat, ini di update oleh fungsi lain (generateSuratPengantarPdf)
        $this->db->query('UPDATE penempatan_kp SET instansi_id = :instansi_id, dosen_pembimbing_id = :dosen_pembimbing_id,
                          tanggal_mulai = :tanggal_mulai, tanggal_selesai = :tanggal_selesai
                          WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':instansi_id', $data['instansi_id']);
        $this->db->bind(':dosen_pembimbing_id', $data['dosen_pembimbing_id']);
        $this->db->bind(':tanggal_mulai', $data['tanggal_mulai']);
        $this->db->bind(':tanggal_selesai', $data['tanggal_selesai']);

        if (!$this->db->execute()) {
            $this->db->query("ROLLBACK");
            return false;
        }

        $current_mahasiswa_in_group = $this->getMahasiswaByPenempatanId($data['id']);
        $current_mahasiswa_ids_in_group = array_column($current_mahasiswa_in_group, 'id');

        foreach ($current_mahasiswa_in_group as $mhs_lama) {
            if (!in_array($mhs_lama['id'], $data['mahasiswa_ids'])) {
                $this->db->query('UPDATE mahasiswa SET status_kp = "Terdaftar", dosen_pembimbing_id = NULL, instansi_id = NULL WHERE id = :id');
                $this->db->bind(':id', $mhs_lama['id']);
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

        if (!empty($data['mahasiswa_ids'])) {
            foreach ($data['mahasiswa_ids'] as $mahasiswa_id) {
                $this->db->query('INSERT INTO penempatan_kp_mahasiswa (penempatan_kp_id, mahasiswa_id) VALUES (:penempatan_kp_id, :mahasiswa_id)');
                $this->db->bind(':penempatan_kp_id', $data['id']);
                $this->db->bind(':mahasiswa_id', $mahasiswa_id);
                if (!$this->db->execute()) {
                    $this->db->query("ROLLBACK");
                    return false;
                }

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
        $this->db->query("SET autocommit = 0");
        $this->db->execute();

        $mahasiswa_terkait = $this->getMahasiswaByPenempatanId($id);

        foreach ($mahasiswa_terkait as $mhs) {
            $this->db->query('UPDATE mahasiswa SET status_kp = "Terdaftar", dosen_pembimbing_id = NULL, instansi_id = NULL WHERE id = :id');
            $this->db->bind(':id', $mhs['id']);
            if (!$this->db->execute()) {
                $this->db->query("ROLLBACK");
                return false;
            }
        }

        $this->db->query('DELETE FROM penempatan_kp_mahasiswa WHERE penempatan_kp_id = :penempatan_kp_id');
        $this->db->bind(':penempatan_kp_id', $id);
        if (!$this->db->execute()) {
            $this->db->query("ROLLBACK");
            return false;
        }

        $this->db->query('DELETE FROM penempatan_kp WHERE id = :id');
        $this->db->bind(':id', $id);
        if (!$this->db->execute()) {
            $this->db->query("ROLLBACK");
            return false;
        }

        $this->db->query("COMMIT");
        $this->db->execute();
        return true;
    }

    public function getMahasiswaByPenempatanId($penempatan_kp_id) {
        $this->db->query('SELECT m.id, m.nim, m.nama_lengkap, m.program_studi
                          FROM penempatan_kp_mahasiswa pkm
                          JOIN mahasiswa m ON pkm.mahasiswa_id = m.id
                          WHERE pkm.penempatan_kp_id = :penempatan_kp_id
                          ORDER BY m.nim ASC');
        $this->db->bind(':penempatan_kp_id', $penempatan_kp_id);
        return $this->db->resultSet();
    }

    public function getPenempatanByMahasiswaId($mahasiswa_id) {
        // Menambahkan kolom surat pengantar dan surat penarikan yang baru
        $this->db->query('SELECT pk.*, i.nama_instansi, d.nama_lengkap as nama_dosen, d.nidn, i.alamat as instansi_alamat, i.kota_kab as instansi_kota_kab,
                                 pk.nomor_surat_kp, pk.kepada_yth_kp, pk.alamat_tujuan_kp,
                                 pk.nomor_surat_penarikan_kp, pk.kepada_yth_penarikan_kp, pk.alamat_tujuan_penarikan_kp, pk.tanggal_penarikan_kp
                          FROM penempatan_kp_mahasiswa pkm
                          JOIN penempatan_kp pk ON pkm.penempatan_kp_id = pk.id
                          JOIN instansi i ON pk.instansi_id = i.id
                          JOIN dosen d ON pk.dosen_pembimbing_id = d.id
                          WHERE pkm.mahasiswa_id = :mahasiswa_id');
        $this->db->bind(':mahasiswa_id', $mahasiswa_id);
        return $this->db->single();
    }

    /**
     * Menyimpan detail surat pengantar ke tabel penempatan_kp.
     */
    public function saveSuratDetails($penempatan_id, $nomor_surat, $kepada_yth, $alamat_tujuan) {
        $this->db->query('UPDATE penempatan_kp SET
                            nomor_surat_kp = :nomor_surat_kp,
                            kepada_yth_kp = :kepada_yth_kp,
                            alamat_tujuan_kp = :alamat_tujuan_kp
                          WHERE id = :id');
        $this->db->bind(':nomor_surat_kp', $nomor_surat);
        $this->db->bind(':kepada_yth_kp', $kepada_yth);
        $this->db->bind(':alamat_tujuan_kp', $alamat_tujuan);
        $this->db->bind(':id', $penempatan_id);
        return $this->db->execute();
    }

    /**
     * Menyimpan detail surat penarikan ke tabel penempatan_kp.
     */
    public function savePenarikanDetails($penempatan_id, $nomor_surat_penarikan, $kepada_yth_penarikan, $alamat_tujuan_penarikan, $tanggal_penarikan) {
        $this->db->query('UPDATE penempatan_kp SET
                            nomor_surat_penarikan_kp = :nomor_surat_penarikan_kp,
                            kepada_yth_penarikan_kp = :kepada_yth_penarikan_kp,
                            alamat_tujuan_penarikan_kp = :alamat_tujuan_penarikan_kp,
                            tanggal_penarikan_kp = :tanggal_penarikan_kp
                          WHERE id = :id');
        $this->db->bind(':nomor_surat_penarikan_kp', $nomor_surat_penarikan);
        $this->db->bind(':kepada_yth_penarikan_kp', $kepada_yth_penarikan);
        $this->db->bind(':alamat_tujuan_penarikan_kp', $alamat_tujuan_penarikan);
        $this->db->bind(':tanggal_penarikan_kp', $tanggal_penarikan);
        $this->db->bind(':id', $penempatan_id);
        return $this->db->execute();
    }
}
