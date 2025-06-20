<!--
<style>
/* Simple CSS for demonstration */
/*body { font-family: Arial, sans-serif; margin: 0; display: flex; }
.sidebar { width: 250px; background-color: #2c3e50; color: white; min-height: 100vh; padding-top: 20px; }
.sidebar h3 { text-align: center; margin-bottom: 30px; }
.sidebar ul { list-style: none; padding: 0; }
.sidebar ul li a { display: block; padding: 15px 20px; color: white; text-decoration: none; border-bottom: 1px solid #34495e; }
.sidebar ul li a:hover, .sidebar ul li a.active { background-color: #34495e; }
.main-content { flex-grow: 1; padding: 20px; background-color: #ecf0f1; }
.header { background-color: #fff; padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
.stat-card { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
.stat-card h3 { color: #555; margin-bottom: 10px; }
.stat-card p { font-size: 2em; font-weight: bold; color: #007bff; }
.activity-log { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-top: 20px; }
.logout-btn { background-color: #e74c3c; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; margin-top: 30px; display: block; text-align: center; }
*/
</style>
-->

<?php include APP_ROOT . '/app/views/admin/includes/header.php'; ?>
<?php include APP_ROOT . '/app/views/admin/includes/sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h2>Selamat Pagi, Admin Prodi</h2>
        <span><?php echo htmlspecialchars($data['username']); ?></span>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Mahasiswa KP Aktif</h3>
            <p><?php echo $data['total_mahasiswa_aktif_kp']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Dosen Pembimbing</h3>
            <p><?php echo $data['total_dosen_pembimbing']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Instansi Terdaftar</h3>
            <p><?php echo $data['total_instansi_terdaftar']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Laporan Mingguan Terbaru</h3>
            <p><?php echo $data['laporan_mingguan_terbaru']; ?></p>
        </div>
    </div>

    <div class="activity-log">
        <h3>Aktivitas Terbaru</h3>
        <p>Belum ada aktivitas yang tercatat.</p>
    </div>
</div>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
