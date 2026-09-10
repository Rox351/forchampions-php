<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    redirect(baseUrl());
}

$product = getProductBySlug($slug);
if ($product === null) {
    http_response_code(404);
    renderHeader('Produto não encontrado', getSettings());
    echo '<section class="section"><div class="container"><h1>Produto não encontrado</h1><p><a href="' . e(baseUrl()) . '">Voltar para a loja</a></p></div></section>';
    renderFooter(getSettings());
    exit;
}

$settings = getSettings();
renderHeader($product['nome'], $settings);
?>

<section class="section product-detail">
    <div class="container product-detail-grid">
        <div class="product-detail-image">
            <img src="<?= e(productImageUrl($product['imagem'])) ?>" alt="<?= e($product['nome']) ?>">
        </div>
        <div class="product-detail-content">
            <span class="product-category"><?= e($product['categoria']) ?></span>
            <h1><?= e($product['nome']) ?></h1>
            <p class="product-price large"><?= e(formatPrice((float) $product['preco'])) ?></p>
            <div class="product-description"><?= nl2br(e($product['descricao'])) ?></div>
            <div class="product-actions stacked">
                <a href="<?= e(whatsappLink($product, $settings)) ?>" class="btn btn-primary" target="_blank" rel="noopener">Comprar no WhatsApp</a>
                <a href="<?= baseUrl('loja.php') ?>" class="btn btn-outline">Voltar para a loja</a>
            </div>
        </div>
    </div>
</section>

<?php renderFooter($settings); ?>
