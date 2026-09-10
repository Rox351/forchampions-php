<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

$settings = getSettings();
$products = getProducts(true);
$showcaseProducts = array_slice($products, 0, 10);
$bestSellers = array_values(array_filter($products, static fn(array $p): bool => (int) $p['destaque'] === 1));
if ($bestSellers === []) {
    $bestSellers = array_slice($products, 0, 10);
}
$bestSellers = array_slice($bestSellers, 0, 10);
$categories = getCategories();
$faqItems = getFaqItems();

function bannerUrl(string $relative): string
{
    return file_exists(publicPath($relative))
        ? assetUrl($relative)
        : assetUrl('assets/images/branding/logo.svg');
}

renderHeader('Home', $settings, 'home');
?>

<section class="home-banner" id="inicio">
    <img src="<?= e(bannerUrl('assets/images/branding/banner-home.jpg')) ?>" alt="For Champions Banner">
</section>

<section class="benefits-bar esconder_mobile">
    <div class="container benefits-grid">
        <div class="benefit-item">
            <strong>Enviamos suas compras</strong>
            <span>Enviamos para todo o Brasil</span>
        </div>
        <div class="benefit-item">
            <strong>Pague como quiser</strong>
            <span>Cartões de crédito em até 12X ou à vista</span>
        </div>
        <div class="benefit-item">
            <strong>Compre com segurança</strong>
            <span>Seus dados sempre protegidos</span>
        </div>
        <div class="benefit-item">
            <strong>Contato Direto</strong>
            <span>Fale diretamente conosco.</span>
        </div>
    </div>
</section>

<section class="section section-alt" id="produtos">
    <div class="container">
        <div class="section-head">
            <h2>For Champions</h2>
        </div>
        <div class="product-grid product-grid-5" id="product-grid">
            <?php foreach ($showcaseProducts as $product): ?>
                <?php renderProductCard($product, $settings); ?>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a class="btn btn-primary" href="<?= baseUrl('loja.php') ?>">Veja todas os Produtos</a>
        </div>
    </div>
</section>

<section class="section search-section" id="busca">
    <div class="container search-box">
        <h2>Encontre o que procura, escreva aqui:</h2>
        <p class="search-example"><strong>Exemplo: Uniforme</strong></p>
        <form action="<?= baseUrl('loja.php') ?>" method="get">
            <input type="search" name="q" id="product-search" placeholder="Procurar Produtos..." aria-label="Buscar produtos">
        </form>
    </div>
</section>

<section class="section section-alt banners-section">
    <div class="container banner-grid">
        <div class="banner-card">
            <img src="<?= e(bannerUrl('assets/images/branding/promo-champions.jpg')) ?>" alt="Promocao Champions" loading="lazy">
        </div>
        <div class="banner-card">
            <img src="<?= e(bannerUrl('assets/images/branding/promo-1.jpg')) ?>" alt="Promocao For Champions" loading="lazy">
        </div>
        <div class="banner-card">
            <img src="<?= e(bannerUrl('assets/images/branding/promo-2.jpg')) ?>" alt="Promocao For Champions" loading="lazy">
        </div>
    </div>
</section>

<section class="section" id="mais-vendidos">
    <div class="container">
        <div class="section-head">
            <h2>Mais vendidos</h2>
        </div>
        <div class="product-grid product-grid-5">
            <?php foreach ($bestSellers as $product): ?>
                <?php renderProductCard($product, $settings); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-alt" id="categorias">
    <div class="container">
        <div class="section-head section-head-center">
            <h2>Ainda não encontrou ? Navegue pelas categorias:</h2>
        </div>
        <div class="category-filter category-filter-center">
            <label for="category-select">Categoria</label>
            <select id="category-select" data-category-redirect>
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e(urlencode($category)) ?>"><?= e($category) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</section>

<section class="section faq-section" id="faq">
    <div class="container">
        <div class="section-head">
            <h2>Perguntas Frequentes</h2>
        </div>
        <div class="faq-list">
            <?php foreach ($faqItems as $index => $item): ?>
                <details class="faq-item" <?= $index === 0 ? 'open' : '' ?>>
                    <summary><?= e($item['pergunta'] ?? '') ?></summary>
                    <p><?= e($item['resposta'] ?? '') ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php renderFooter($settings); ?>
