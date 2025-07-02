<div class="sidebar">
    <h3 class="sidebar-title">SIMONKAPE</h3>
    <ul>
        <li><a href="<?php echo BASE_URL; ?>/admin" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'dashboard') ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="<?php echo BASE_URL; ?>/admin/mahasiswa" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'mahasiswa') ? 'active' : ''; ?>">Manajemen Mahasiswa</a></li>
        <li><a href="<?php echo BASE_URL; ?>/admin/dosen" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'dosen') ? 'active' : ''; ?>">Manajemen Dosen Pembimbing</a></li>
        <li><a href="<?php echo BASE_URL; ?>/admin/instansi" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'instansi') ? 'active' : ''; ?>">Manajemen Instansi</a></li>
        <li><a href="<?php echo BASE_URL; ?>/admin/penempatan" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'penempatan') ? 'active' : ''; ?>">Manajemen Penempatan KP</a></li>
        <li><a href="<?php echo BASE_URL; ?>/admin/laporan" class="<?php echo (isset($data['active_menu']) && $data['active_menu'] == 'laporan') ? 'active' : ''; ?>">Laporan & Rekapitulasi</a></li>
        <li><a href="<?php echo BASE_URL; ?>/auth/logout" class="logout-btn">Logout</a></li>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const links = document.querySelectorAll('div.sidebar a');
                links.forEach(link => {
                    // Bandingkan href absolut dengan URL saat ini
                    if (link.href === window.location.href) {
                        // Nonaktifkan link
                        link.style.pointerEvents = 'none';
                        // link.style.color = 'gray'; // opsional: ubah tampilan
                        // link.style.textDecoration = 'none'; // opsional: hilangkan underline
                        link.setAttribute('aria-disabled', 'true');
                        // link.title = 'You are here'; // opsional: penanda
                    }
                });
            });
        </script>
    </ul>
</div>
