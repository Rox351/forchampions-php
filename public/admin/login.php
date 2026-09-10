<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

if (isLoggedIn()) {
    redirect('/admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Informe usuário e senha.';
    } elseif (attemptLogin($username, $password)) {
        redirect('/admin/index.php');
    } else {
        $error = 'Credenciais inválidas.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Admin For Champions</title>
    <link rel="stylesheet" href="<?= assetUrl('assets/css/style.css') ?>">
</head>
<body class="admin-body">
<div class="admin-login-shell">
    <div class="admin-card admin-login-card">
        <div class="admin-brand">
            <img src="<?= e(adminLogoUrl()) ?>" alt="For Champions">
            <div>
                <strong>For Champions Admin</strong>
                <span>Acesso ao painel de produtos</span>
            </div>
        </div>
        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post" class="form-grid">
            <label>Usuário<input type="text" name="username" required autocomplete="username"></label>
            <label>Senha<input type="password" name="password" required autocomplete="current-password"></label>
            <button class="btn btn-whatsapp" type="submit">Entrar</button>
        </form>
        <p class="admin-login-note">Padrão inicial: admin / admin123</p>
    </div>
</div>
</body>
</html>
