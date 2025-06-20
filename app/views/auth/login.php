<?php include APP_ROOT . '/app/views/admin/includes/header.php'; ?>

<style>
    /* Simple CSS for demonstration */
    body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
    .login-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; text-align: center; }
    .login-container h2 { margin-bottom: 20px; color: #333; }
    .login-container input[type="text"],
    .login-container input[type="password"] { width: calc(100% - 20px); padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
    .login-container button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
    .login-container button:hover { background-color: #0056b3; }
    .error-message { color: red; margin-bottom: 15px; }
</style>

<div class="login-container">
    <h2>Selamat Datang di SIMONKAPE</h2>
    <p>Sistem Monitoring Kerja Praktik Mahasiswa Teknik Informatika Universitas Dayanu Ikhsanuddin.</p>

    <?php if (isset($data['error'])) : ?>
        <p class="error-message"><?php echo $data['error']; ?></p>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/auth/login" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

<?php include APP_ROOT . '/app/views/admin/includes/footer.php'; ?>
