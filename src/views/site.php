<?php

declare(strict_types=1);

function renderHeader(string $title, array $settings, string $active = ''): void
{
    $logo = file_exists(publicPath('assets/images/branding/logo.png'))
        ? assetUrl('assets/images/branding/logo.png')
        : assetUrl('assets/images/branding/logo.svg');
    $waLink = whatsappLink(['nome' => 'For Champions', 'preco' => 0], $settings);
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> | For Champions</title>
    <meta name="description" content="Uniformes e roupas esportivas personalizadas. Compre pelo WhatsApp.">
    <link rel="stylesheet" href="<?= assetUrl('assets/css/style.css') ?>">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= baseUrl() ?>" class="logo">
            <img src="<?= e($logo) ?>" alt="For Champions">
        </a>
        <form class="header-search" action="<?= baseUrl('loja.php') ?>" method="get" role="search">
            <input type="search" name="q" placeholder="Procurar Produtos..." aria-label="Procurar produtos">
        </form>
        <button class="menu-toggle" type="button" aria-label="Abrir menu" data-menu-toggle>☰</button>
        <nav class="main-nav" data-main-nav>
            <a href="<?= baseUrl() ?>" class="<?= $active === 'home' ? 'is-active' : '' ?>">Início</a>
            <a href="<?= baseUrl('loja.php') ?>" class="<?= $active === 'loja' ? 'is-active' : '' ?>">Loja</a>
            <a href="<?= baseUrl('pagina.php?slug=quem-somos') ?>" class="<?= $active === 'quem-somos' ? 'is-active' : '' ?>">Quem somos</a>
            <a href="<?= baseUrl() ?>#faq">FAQ</a>
            <a href="<?= baseUrl('pagina.php?slug=contato') ?>" class="<?= $active === 'contato' ? 'is-active' : '' ?>">Contato</a>
        </nav>
    </div>
</header>
<main>
<?php
}

function renderFooter(array $settings): void
{
    $whatsapp = $settings['whatsapp'] ?? '(51) 99188-6097';
    $email = $settings['email'] ?? 'contato@forchampions.com.br';
    $telefone = $settings['telefone'] ?? $whatsapp;
    $cnpj = $settings['cnpj'] ?? '';
    $empresa = $settings['empresa'] ?? 'For Champions';
    $waLink = whatsappLink(['nome' => 'For Champions', 'preco' => 0], $settings);
    ?>
</main>
<footer class="site-footer" id="contato">
    <div class="container footer-grid">
        <div>
            <h3><?= e($empresa) ?></h3>
            <p>Uniformes e roupas esportivas personalizadas desde janeiro de 2024.</p>
        </div>
        <div>
            <h4>Contato</h4>
            <p><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
            <p><a href="<?= e($waLink) ?>" target="_blank" rel="noopener"><?= e($telefone) ?></a></p>
            <?php if ($cnpj !== '' && $cnpj !== '00.000.000/0000-00'): ?>
                <p>CNPJ: <?= e($cnpj) ?></p>
            <?php endif; ?>
            <p><?= e($settings['endereco_retirada'] ?? 'Retirada disponível em Canoas/RS') ?></p>
        </div>
        <div>
            <h4>Políticas</h4>
            <ul class="footer-links">
                <li><a href="<?= baseUrl('pagina.php?slug=termo-de-uso') ?>">Termo de uso</a></li>
                <li><a href="<?= baseUrl('pagina.php?slug=politica-de-privacidade') ?>">Política de privacidade</a></li>
                <li><a href="<?= baseUrl('pagina.php?slug=politica-de-frete') ?>">Política de frete</a></li>
                <li><a href="<?= baseUrl('pagina.php?slug=politica-de-troca-e-devolucao') ?>">Troca e devolução</a></li>
            </ul>
        </div>
    </div>
    <div class="container footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= e($empresa) ?>. Todos os direitos reservados.</p>
    </div>
</footer>
<a href="<?= e($waLink) ?>" class="whatsapp-fab whatsapp-button" target="_blank" rel="noopener" aria-label="Falar no WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>
<button type="button" class="scroll-top" data-scroll-top aria-label="Voltar ao topo">↑</button>
<script src="<?= assetUrl('assets/js/main.js') ?>"></script>
</body>
</html>
<?php
}

function renderProductCard(array $product, array $settings): void
{
    $waLink = whatsappLink($product, $settings);
    ?>
<article class="product-card" data-product-card data-name="<?= e(strtolower($product['nome'])) ?>" data-category="<?= e(strtolower($product['categoria'])) ?>">
    <a href="<?= baseUrl('produto.php?slug=' . urlencode($product['slug'])) ?>" class="product-image-link">
        <img src="<?= e(productImageUrl($product['imagem'])) ?>" alt="<?= e($product['nome']) ?>" loading="lazy">
    </a>
    <div class="product-body">
        <span class="product-category"><?= e($product['categoria']) ?></span>
        <h3><a href="<?= baseUrl('produto.php?slug=' . urlencode($product['slug'])) ?>"><?= e($product['nome']) ?></a></h3>
        <p class="product-price"><?= e(formatPrice((float) $product['preco'])) ?></p>
        <div class="product-actions">
            <a href="<?= e($waLink) ?>" class="btn btn-primary" target="_blank" rel="noopener">Comprar no WhatsApp</a>
        </div>
    </div>
</article>
<?php
}
