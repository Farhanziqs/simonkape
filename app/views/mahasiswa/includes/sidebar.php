<div class="mahasiswa-sidebar">
    <h3 class="siderbar-title">SIMONKAPE Mahasiswa</h3>
    <ul>
        <li><a href="<?php echo BASE_URL; ?>/mahasiswa" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'dashboard') ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="<?php echo BASE_URL; ?>/mahasiswa/absen" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'absen') ? 'active' : ''; ?>">Absen Harian</a></li>
        <li><a href="<?php echo BASE_URL; ?>/mahasiswa/logbook" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'logbook') ? 'active' : ''; ?>">Logbook Harian</a></li>
        <li><a href="<?php echo BASE_URL; ?>/mahasiswa/laporan" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'laporan') ? 'active' : ''; ?>">Laporan Mingguan</a></li>
        <li><a href="<?php echo BASE_URL; ?>/auth/logout" class="logout-btn">Logout</a></li>
    </ul>
</div>
