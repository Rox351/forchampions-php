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
$productId = (int) $product['id'];
$tiers = getProductQuantityTiers($productId);
$hasTiers = $tiers !== [];
$initialQty = 1;
$initialUnit = resolveUnitPriceForQuantity($product, $initialQty);
$extraScripts = $hasTiers ? ['assets/js/product-quantity.js'] : [];

$tierJson = [];
foreach ($tiers as $tier) {
    $tierJson[] = [
        'qty_min' => (int) $tier['qty_min'],
        'qty_max' => $tier['qty_max'] !== null ? (int) $tier['qty_max'] : null,
        'unit_price' => (float) $tier['unit_price'],
    ];
}

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
            <?php if ($hasTiers): ?>
            <div
                class="quantity-pricing"
                data-quantity-pricing
                data-product-name="<?= e($product['nome']) ?>"
                data-whatsapp-phone="<?= e($settings['whatsapp'] ?? '') ?>"
                data-base-price="<?= e((string) $product['preco']) ?>"
                data-tiers="<?= e(json_encode($tierJson, JSON_UNESCAPED_UNICODE)) ?>"
            >
                <p class="product-price-label">Preço unitário (conforme quantidade)</p>
                <p class="product-price large">
                    <span data-unit-price-display><?= e(formatPrice($initialUnit)) ?></span>
                </p>
                <p class="product-total-estimate">
                    Total estimado: <strong data-total-price-display><?= e(formatPrice($initialUnit * $initialQty)) ?></strong>
                </p>

                <label class="quantity-field" for="product-qty">
                    Quantidade
                    <input id="product-qty" type="number" min="1" step="1" value="1" data-quantity-input>
                </label>

                <div class="quantity-tiers-wrap">
                    <p class="quantity-tiers-title">Desconto por quantidade</p>
                    <table class="quantity-tiers-table">
                        <thead>
                            <tr>
                                <th scope="col">Quantidade</th>
                                <th scope="col">Preço unitário</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tiers as $tier): ?>
                            <?php
                            $qtyMin = (int) $tier['qty_min'];
                            $qtyMax = $tier['qty_max'] !== null ? (int) $tier['qty_max'] : null;
                            ?>
                            <tr
                                data-tier-row
                                data-qty-min="<?= $qtyMin ?>"
                                data-qty-max="<?= $qtyMax === null ? '' : (string) $qtyMax ?>"
                            >
                                <td><?= e(formatQuantityRangeLabel($qtyMin, $qtyMax)) ?></td>
                                <td><?= e(formatPrice((float) $tier['unit_price'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="product-actions stacked">
                    <a
                        href="<?= e(whatsappLink($product, $settings, $initialQty)) ?>"
                        class="btn btn-primary"
                        target="_blank"
                        rel="noopener"
                        data-product-wa-button
                    >Comprar no WhatsApp</a>
                    <a href="<?= baseUrl('loja.php') ?>" class="btn btn-outline">Voltar para a loja</a>
                </div>
            </div>
            <?php else: ?>
            <p class="product-price large"><?= e(formatPrice((float) $product['preco'])) ?></p>
            <?php endif; ?>

            <div class="product-description"><?= nl2br(e($product['descricao'])) ?></div>
            <?php if (!$hasTiers): ?>
            <div class="product-actions stacked">
                <a
                    href="<?= e(whatsappLink($product, $settings, $initialQty)) ?>"
                    class="btn btn-primary"
                    target="_blank"
                    rel="noopener"
                >Comprar no WhatsApp</a>
                <a href="<?= baseUrl('loja.php') ?>" class="btn btn-outline">Voltar para a loja</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php renderFooter($settings, $extraScripts); ?>
