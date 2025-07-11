<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMONKAPE</title>
    <style>
        /* General Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Full page wrapper */
        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }

        /* Left side with background image */
        .login-image-section {
            background-image: url('<?php echo BASE_URL; ?>/images/login-bg.png');
            background-size: cover;
            background-position: center;
        }

        /* Right side with the form */
        .login-form-section {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            padding: 40px;
        }

        .login-container {
            width: 100%;
            max-width: 380px;
        }

        .login-container h2 {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #111;
            margin-bottom: 15px; /* Disesuaikan */
            letter-spacing: 1px;
        }

        /* CSS BARU: Untuk subjudul */
        .login-subtitle {
            text-align: center;
            font-size: 14px;
            color: #6c757d; /* Abu-abu */
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #004494;
        }

        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }
            .login-image-section {
                display: none;
            }
            .login-form-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-image-section"></div>

    <div class="login-form-section">
        <div class="login-container">
            <h2>LOGIN</h2>

            <p class="login-subtitle">
                Sistem Monitoring Kerja Praktik Mahasiswa Teknik Informatika Universitas Dayanu Ikhsanuddin
            </p>

            <?php if (isset($data['error'])) : ?>
                <p class="error-message"><?php echo $data['error']; ?></p>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/auth/login" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
