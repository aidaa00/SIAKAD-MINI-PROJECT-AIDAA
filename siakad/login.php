<?php
session_start();
require_once 'storage.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = login($_POST['username'], $_POST['password']);
    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SIAKAD Mini</title>
    <style>
        :root {
            --primary: #1a56db;
            --bg: #f3f7ff;
            --white: #ffffff;
            --text: #1e293b;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background: var(--white);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(26, 86, 219, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-card h2 { color: var(--primary); margin-bottom: 8px; }
        .login-card p { color: #64748b; margin-bottom: 24px; font-size: 14px; }
        .form-group { text-align: left; margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-sizing: border-box;
            transition: 0.3s;
        }
        input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1); }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-login:hover { background: #1e429f; }
        .error-msg { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>🎓 SIAKAD Mini</h2>
        <p>Silakan login untuk mengakses sistem</p>
        
        <?php if ($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username..." required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Masuk ke Sistem</button>
        </form>
    </div>
</body>
</html>