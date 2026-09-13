<?php

declare(strict_types=1);

$sqlFile = __DIR__ . '/../_extracted_wpress/database.sql';
$handle = fopen($sqlFile, 'rb');
if ($handle === false) {
    throw new RuntimeException('Cannot open SQL file');
}

$found = false;
while (($line = fgets($handle)) !== false) {
    if (!str_contains($line, "13691,'rp_wcdpd_settings'")) {
        continue;
    }

    $found = true;
    if (!preg_match("/VALUES \\(13691,'rp_wcdpd_settings','(.+)','auto'\\);/", $line, $matches)) {
        echo "Line found but regex failed\n";
        break;
    }

    $settings = unserialize(stripcslashes($matches[1]), ['allowed_classes' => true]);
    $store = is_array($settings) ? ($settings[1] ?? $settings) : [];

    echo "=== WooCommerce Dynamic Pricing & Discounts (RightPress) 2.4.6 ===\n";
    echo 'Label na loja: ' . ($store['promo_your_price_label'] ?? 'Seu Preço:') . "\n\n";

    $rules = $store['product_pricing'] ?? [];
    echo 'Total de regras product_pricing: ' . count($rules) . "\n\n";

    foreach ($rules as $ruleId => $rule) {
        if (!is_array($rule)) {
            continue;
        }
        echo "--- {$ruleId} | " . ($rule['title'] ?? '(sem titulo)') . " ---\n";
        echo '  enabled: ' . ($rule['enabled'] ?? '') . "\n";
        echo '  method: ' . ($rule['method'] ?? '') . "\n";
        if (!empty($rule['product_ids'])) {
            echo '  product_ids: ' . implode(',', (array) $rule['product_ids']) . "\n";
        }
        if (!empty($rule['category_ids'])) {
            echo '  category_ids: ' . implode(',', (array) $rule['category_ids']) . "\n";
        }
        foreach ($rule['pricing'] ?? [] as $tier) {
            if (!is_array($tier)) {
                continue;
            }
            echo '  faixa qty ' . ($tier['from'] ?? '?') . ' a ' . ($tier['to'] ?? '?')
                . ' => ' . ($tier['pricing_method'] ?? '') . ' ' . ($tier['pricing_value'] ?? '') . "\n";
        }
        echo "\n";
    }
    break;
}

fclose($handle);

if (!$found) {
    echo "rp_wcdpd_settings not found\n";
    exit(1);
}

// Plugin paths from update transient line
$handle = fopen($sqlFile, 'rb');
while (($line = fgets($handle)) !== false) {
    if (!str_contains($line, '_site_transient_update_plugins')) {
        continue;
    }
    preg_match_all('/s:\d+:"([^"]+\/[^"]+\.php)";/', $line, $plugins);
    $list = array_unique($plugins[1] ?? []);
    sort($list);
    echo "=== PLUGINS INSTALADOS (" . count($list) . ") ===\n";
    foreach ($list as $plugin) {
        echo $plugin . "\n";
    }
    break;
}
fclose($handle);
