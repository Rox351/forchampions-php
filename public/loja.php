<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

$settings = getSettings();
$category = trim($_GET['categoria'] ?? '');
$search = trim($_GET['q'] ?? '');
$products = getProducts(true, $category !== '' ? $category : null, $search !== '' ? $search : null);
$categories = getCategories();

renderHeader('Loja', $settings, 'loja');
?>

<section class="section shop-page">
    <div class="container">
        <div class="section-head">
            <h1>Loja</h1>
            <p><?= count($products) ?> produto(s) encontrado(s)</p>
        </div>

        <div class="shop-filters">
            <form class="shop-search" action="<?= baseUrl('loja.php') ?>" method="get">
                <input type="search" name="q" value="<?= e($search) ?>" placeholder="Procurar Produtos...">
                <?php if ($category !== ''): ?>
                    <input type="hidden" name="categoria" value="<?= e($category) ?>">
                <?php endif; ?>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </form>

            <form class="shop-category" action="<?= baseUrl('loja.php') ?>" method="get">
                <?php if ($search !== ''): ?>
                    <input type="hidden" name="q" value="<?= e($search) ?>">
                <?php endif; ?>
                <label for="shop-category-select">Categoria</label>
                <select id="shop-category-select" name="categoria" onchange="this.form.submit()">
                    <option value="">Todas as categorias</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= e($cat) ?>" <?= $cat === $category ? 'selected' : '' ?>><?= e($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if ($products === []): ?>
            <p class="shop-empty">Nenhum produto encontrado. <a href="<?= baseUrl('loja.php') ?>">Ver todos</a></p>
        <?php else: ?>
            <div class="product-grid product-grid-5">
                <?php foreach ($products as $product): ?>
                    <?php renderProductCard($product, $settings); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php renderFooter($settings); ?>
