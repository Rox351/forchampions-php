<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

$totalProducts = (int) db()->query('SELECT COUNT(*) FROM products')->fetchColumn();
$activeProducts = (int) db()->query('SELECT COUNT(*) FROM products WHERE ativo = 1')->fetchColumn();
$featuredProducts = (int) db()->query('SELECT COUNT(*) FROM products WHERE destaque = 1')->fetchColumn();

renderAdminHeader('Dashboard');
?>

<h1 class="admin-page-title">Dashboard</h1>
<p class="admin-page-subtitle">Gerencie o catalogo da landing page For Champions.</p>

<div class="admin-stats">
    <div class="admin-stat"><strong><?= $totalProducts ?></strong><span>Total de produtos</span></div>
    <div class="admin-stat"><strong><?= $activeProducts ?></strong><span>Produtos ativos</span></div>
    <div class="admin-stat"><strong><?= $featuredProducts ?></strong><span>Mais vendidos</span></div>
</div>

<div class="admin-card">
    <h2>Acoes rapidas</h2>
    <div class="admin-actions">
        <a class="btn btn-whatsapp" href="/admin/produtos.php?action=create">Adicionar produto</a>
        <a class="btn btn-outline" href="/admin/produtos.php">Ver produtos</a>
        <a class="btn btn-outline" href="/admin/settings.php">Editar configuracoes</a>
    </div>
</div>

<?php renderAdminFooter(); ?>
