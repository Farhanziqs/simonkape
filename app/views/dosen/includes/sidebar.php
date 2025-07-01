<div class="dosen-sidebar">
    <h3 class="sidebar-title">SIMONKAPE</h3>
    <ul>
        <li><a href="<?php echo BASE_URL; ?>/dosen" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'dashboard') ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="<?php echo BASE_URL; ?>/dosen/absen" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'absen') ? 'active' : ''; ?>">Lihat Absen Mahasiswa</a></li>
        <li><a href="<?php echo BASE_URL; ?>/dosen/logbook" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'logbook') ? 'active' : ''; ?>">Lihat Logbook Mahasiswa</a></li>
        <li><a href="<?php echo BASE_URL; ?>/dosen/laporan" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'laporan') ? 'active' : ''; ?>">Lihat Laporan Mingguan</a></li>
        <li><a href="<?php echo BASE_URL; ?>/auth/logout" class="logout-btn">Logout</a></li>
    </ul>
</div>
