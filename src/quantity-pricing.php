<?php

declare(strict_types=1);

function ensureQuantityPricingSchema(PDO $pdo): void
{
    $sqlFile = ROOT_PATH . '/database/02-quantity-tiers.sql';
    if (file_exists($sqlFile)) {
        $pdo->exec((string) file_get_contents($sqlFile));
    }
}

function getProductQuantityTiers(int $productId): array
{
    $stmt = db()->prepare(
        'SELECT id, qty_min, qty_max, unit_price, sort_order
         FROM product_quantity_tiers
         WHERE product_id = :product_id
         ORDER BY sort_order ASC, qty_min ASC'
    );
    $stmt->execute(['product_id' => $productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function formatQuantityRangeLabel(int $qtyMin, ?int $qtyMax): string
{
    if ($qtyMax === null || $qtyMax <= 0) {
        return $qtyMin . '+ un.';
    }

    return $qtyMin === $qtyMax
        ? $qtyMin . ' un.'
        : $qtyMin . ' a ' . $qtyMax . ' un.';
}

function resolveUnitPriceForQuantity(array $product, int $quantity): float
{
    $quantity = max(1, $quantity);
    $base = (float) ($product['preco'] ?? 0);
    $productId = (int) ($product['id'] ?? 0);

    if ($productId <= 0) {
        return $base;
    }

    $tiers = getProductQuantityTiers($productId);
    if ($tiers === []) {
        return $base;
    }

    foreach ($tiers as $tier) {
        $min = (int) $tier['qty_min'];
        $max = $tier['qty_max'] !== null ? (int) $tier['qty_max'] : null;
        if ($quantity < $min) {
            continue;
        }
        if ($max === null || $quantity <= $max) {
            return (float) $tier['unit_price'];
        }
    }

    return $base;
}

function productHasQuantityTiers(array $product): bool
{
    $productId = (int) ($product['id'] ?? 0);
    if ($productId <= 0) {
        return false;
    }

    $stmt = db()->prepare('SELECT COUNT(*) FROM product_quantity_tiers WHERE product_id = :id');
    $stmt->execute(['id' => $productId]);

    return (int) $stmt->fetchColumn() > 0;
}
