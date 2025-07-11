<?php
// simonkapedb/app/views/dosen/includes/sidebar.php

$active_menu = $data['active_menu'] ?? '';
$is_kaprodi = $data['is_kaprodi'] ?? false; // Pastikan variabel ini ada dari controller, berikan default false
?>

<style>
.dosen-sidebar {
    width: 230px;
    background: #fff;
    border-right: 1px solid #eee;
    min-height: 100vh;
    padding: 0 0 30px 0;
    font-family: 'Segoe UI', Arial, sans-serif;
}
.sidebar-title {
    color: #2196f3;
    font-weight: bold;
    text-align: center;
    margin: 24px 0 18px 0;
    letter-spacing: 1px;
    font-size: 20px;
}
.dosen-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.dosen-sidebar ul li {
    margin-bottom: 2px;
}
.dosen-sidebar ul li a {
    display: block;
    padding: 10px 18px;
    color: #222;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.2s, color 0.2s;
}
.dosen-sidebar ul li a.active,
.dosen-sidebar ul li a:hover {
    background: #e3f2fd;
    color: #1976d2;
    font-weight: 500;
}
.dosen-sidebar ul li.kaprodi-menu {
    margin-top: 6px;
}
.sidebar-menu-separator {
    margin: 24px 0 10px 0;
    padding: 0 18px;
    color: #888;
    font-size: 13px;
    font-weight: bold;
    letter-spacing: 0.5px;
}
.sidebar-menu-separator hr {
    border: none;
    border-top: 2px solid #bbb;
    margin: 0 0 8px 0;
}
.logout-btn {
    color: #e53935 !important;
    border: 1px solid #e53935;
    margin: 18px 18px 0 18px;
    border-radius: 4px;
    text-align: center;
    background: #fff;
    font-weight: 500;
    transition: background 0.2s, color 0.2s;
    padding: 10px 0;
    display: block;
}
.logout-btn:hover {
    background: #e53935;
    color: #fff !important;
}
.kaprodi-label {
    display: block;
    font-size: 14px;
    font-weight: bold;
    color: #1976d2;
    margin: 8px 0 10px 0;
    letter-spacing: 0.5px;
}
</style>

<div class="dosen-sidebar">
    <h3 class="sidebar-title">SIMONKAPE</h3>
    <ul>
        <li>
            <a href="<?php echo BASE_URL; ?>/dosen" class="<?php echo ($active_menu == 'dashboard') ? 'active' : ''; ?>">
                Dashboard
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/dosen/absen" class="<?php echo ($active_menu == 'absen') ? 'active' : ''; ?>">
                Lihat Absen Mahasiswa
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/dosen/logbook" class="<?php echo ($active_menu == 'logbook') ? 'active' : ''; ?>">
                Lihat Logbook Mahasiswa
            </a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>/dosen/laporan" class="<?php echo ($active_menu == 'laporan') ? 'active' : ''; ?>">
                Lihat Laporan Mingguan
            </a>
        </li>

        <?php if (!empty($is_kaprodi)) : ?>
            <li class="sidebar-menu-separator">
                <hr>
                <span class="kaprodi-label">Akses Kaprodi</span>
            </li>
            <li class="kaprodi-menu">
                <a href="<?php echo BASE_URL; ?>/dosen/dataDosen" class="<?php echo ($active_menu === 'data_dosen') ? 'active' : ''; ?>">
                    <i class="fa fa-users"></i> Data Dosen
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/dosen/dataMahasiswa" class="<?php echo ($active_menu === 'data_mahasiswa') ? 'active' : ''; ?>">
                    <i class="fa fa-user-graduate"></i> Data Mahasiswa
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/dosen/dataInstansi" class="<?php echo ($active_menu === 'data_instansi') ? 'active' : ''; ?>">
                    <i class="fa fa-building"></i> Data Instansi
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/dosen/statusKp" class="<?php echo ($active_menu === 'status_kp') ? 'active' : ''; ?>">
                    <i class="fa fa-tasks"></i> Status KP Mahasiswa
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/dosen/rekapitulasiLaporan" class="<?php echo ($active_menu === 'rekapitulasi_laporan') ? 'active' : ''; ?>">
                    <i class="fa fa-chart-bar"></i> Rekapitulasi Laporan
                </a>
            </li>
        <?php endif; ?>

        <li>
            <a href="<?php echo BASE_URL; ?>/auth/logout" class="logout-btn">Logout</a>
        </li>
    </ul>
</div>
