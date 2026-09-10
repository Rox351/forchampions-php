<?php

declare(strict_types=1);

function isLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('/admin/login.php');
    }
}

function attemptLogin(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['admin_id'] = (int) $user['id'];
    $_SESSION['admin_username'] = $user['username'];
    session_regenerate_id(true);

    return true;
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function currentAdminUsername(): string
{
    return $_SESSION['admin_username'] ?? 'admin';
}
