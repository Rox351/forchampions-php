<?php

declare(strict_types=1);

function loadWcdpdProductPricingFromSqlFile(string $sqlFile): array
{
    $handle = fopen($sqlFile, 'rb');
    if ($handle === false) {
        throw new RuntimeException('Nao foi possivel abrir database.sql');
    }

    while (($line = fgets($handle)) !== false) {
        if (!str_contains($line, "13691,'rp_wcdpd_settings'")) {
            continue;
        }
        if (!preg_match("/VALUES \\(13691,'rp_wcdpd_settings','(.+)','auto'\\);/", $line, $matches)) {
            break;
        }
        $settings = unserialize(stripcslashes($matches[1]), ['allowed_classes' => true]);
        fclose($handle);

        $store = is_array($settings) ? ($settings[1] ?? $settings) : [];

        return is_array($store['product_pricing'] ?? null) ? $store['product_pricing'] : [];
    }

    fclose($handle);

    return [];
}

function loadWcdpdProductPricingFromWp(PDO $wpDb): array
{
    $stmt = $wpDb->query(
        "SELECT option_value FROM wp_options WHERE option_name = 'rp_wcdpd_settings' LIMIT 1"
    );
    $raw = $stmt ? $stmt->fetchColumn() : false;
    if ($raw === false || $raw === '') {
        return loadWcdpdProductPricingFromSqlFile(
            dirname(__DIR__) . '/_extracted_wpress/database.sql'
        );
    }

    $settings = unserialize((string) $raw, ['allowed_classes' => true]);
    $store = is_array($settings) ? ($settings[1] ?? $settings) : [];

    return is_array($store['product_pricing'] ?? null) ? $store['product_pricing'] : [];
}

function fetchWpProductSlugMap(PDO $wpDb): array
{
    $map = [];
    $stmt = $wpDb->query(
        "SELECT ID, post_name FROM wp_posts WHERE post_type = 'product' AND post_status = 'publish'"
    );
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $map[(string) $row['ID']] = (string) $row['post_name'];
    }

    return $map;
}

function extractWpProductIdsFromRule(array $rule): array
{
    $ids = [];
    foreach ($rule['conditions'] ?? [] as $condition) {
        if (!is_array($condition)) {
            continue;
        }
        foreach ($condition['products'] ?? [] as $productId) {
            $ids[] = (string) $productId;
        }
    }

    return array_values(array_unique($ids));
}

function importQuantityTiers(PDO $appDb, PDO $wpDb): int
{
    ensureQuantityPricingSchema($appDb);

    $rules = loadWcdpdProductPricingFromWp($wpDb);
    $slugMap = fetchWpProductSlugMap($wpDb);

    $appSlugToId = [];
    $stmt = $appDb->query('SELECT id, slug FROM products');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $appSlugToId[(string) $row['slug']] = (int) $row['id'];
    }

    $appDb->exec('DELETE FROM product_quantity_tiers');

    $insert = $appDb->prepare(
        'INSERT INTO product_quantity_tiers (product_id, qty_min, qty_max, unit_price, sort_order)
         VALUES (:product_id, :qty_min, :qty_max, :unit_price, :sort_order)'
    );

    $imported = 0;
    $sort = 0;

    foreach ($rules as $rule) {
        if (!is_array($rule) || ($rule['method'] ?? '') !== 'bulk') {
            continue;
        }

        $wpIds = extractWpProductIdsFromRule($rule);
        $appProductIds = [];
        foreach ($wpIds as $wpId) {
            $slug = $slugMap[$wpId] ?? '';
            if ($slug !== '' && isset($appSlugToId[$slug])) {
                $appProductIds[] = $appSlugToId[$slug];
            }
        }
        $appProductIds = array_values(array_unique($appProductIds));
        if ($appProductIds === []) {
            continue;
        }

        $ranges = $rule['quantity_ranges'] ?? [];
        if (!is_array($ranges) || $ranges === []) {
            continue;
        }

        $order = 0;
        foreach ($ranges as $range) {
            if (!is_array($range)) {
                continue;
            }
            $qtyMin = (int) ($range['from'] ?? 0);
            if ($qtyMin <= 0) {
                continue;
            }
            $qtyMaxRaw = $range['to'] ?? null;
            $qtyMax = $qtyMaxRaw === null || $qtyMaxRaw === '' ? null : (int) $qtyMaxRaw;
            $unitPrice = (float) ($range['pricing_value'] ?? 0);
            if ($unitPrice <= 0) {
                continue;
            }

            foreach ($appProductIds as $productId) {
                $insert->execute([
                    'product_id' => $productId,
                    'qty_min' => $qtyMin,
                    'qty_max' => $qtyMax,
                    'unit_price' => $unitPrice,
                    'sort_order' => $order,
                ]);
                $imported++;
            }
            $order++;
        }
        $sort++;
    }

    return $imported;
}
