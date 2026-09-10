<?php

declare(strict_types=1);

function adminLogoUrl(): string
{
    return file_exists(publicPath('assets/images/branding/logo.png'))
        ? assetUrl('assets/images/branding/logo.png')
        : assetUrl('assets/images/branding/logo.svg');
}

function adminNavLink(string $href, string $label, string $activeFile): string
{
    $current = basename($_SERVER['PHP_SELF'] ?? '');
    $class = $current === $activeFile ? 'is-active' : '';
    return '<a href="' . e($href) . '" class="' . e($class) . '">' . e($label) . '</a>';
}

function renderAdminHeader(string $title): void
{
    requireLogin();
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> | Admin For Champions</title>
    <link rel="stylesheet" href="<?= assetUrl('assets/css/style.css') ?>">
</head>
<body class="admin-body">
<div class="admin-topbar">
    <div class="admin-header">
        <div class="admin-brand">
            <img src="<?= e(adminLogoUrl()) ?>" alt="For Champions">
            <div>
                <strong>For Champions Admin</strong>
                <span>Ola, <?= e(currentAdminUsername()) ?></span>
            </div>
        </div>
        <nav class="admin-nav">
            <?= adminNavLink('/admin/index.php', 'Dashboard', 'index.php') ?>
            <?= adminNavLink('/admin/produtos.php', 'Produtos', 'produtos.php') ?>
            <?= adminNavLink('/admin/settings.php', 'Configuracoes', 'settings.php') ?>
            <a href="/" target="_blank">Ver site</a>
            <a href="/admin/logout.php">Sair</a>
        </nav>
    </div>
</div>
<div class="admin-shell">
<?php
}

function renderAdminFooter(): void
{
    ?>
</div>
</body>
</html>
<?php
}
