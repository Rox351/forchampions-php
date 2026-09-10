<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    redirect(baseUrl());
}

$page = getPageBySlug($slug);
if ($page === null) {
    http_response_code(404);
    renderHeader('Página não encontrada', getSettings());
    echo '<section class="section"><div class="container"><h1>Página não encontrada</h1><p><a href="' . e(baseUrl()) . '">Voltar para o início</a></p></div></section>';
    renderFooter(getSettings());
    exit;
}

$settings = getSettings();
$active = in_array($slug, ['quem-somos', 'contato'], true) ? $slug : '';
renderHeader($page['titulo'], $settings, $active);
?>

<section class="section page-content">
    <div class="container content-page">
        <?php if ($slug === 'contato'): ?>
            <div class="contact-actions">
                <a href="<?= e(whatsappLink(['nome' => 'For Champions', 'preco' => 0], $settings)) ?>" class="btn btn-whatsapp" target="_blank" rel="noopener">Falar no WhatsApp</a>
                <a href="mailto:<?= e($settings['email'] ?? 'contato@forchampions.com.br') ?>" class="btn btn-outline">Enviar e-mail</a>
            </div>
        <?php endif; ?>
        <div class="wp-content">
            <?= renderPageContent((string) $page['conteudo']) ?>
        </div>
    </div>
</section>

<?php renderFooter($settings); ?>
