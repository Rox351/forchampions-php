<?php

declare(strict_types=1);

$sqlFile = __DIR__ . '/../_extracted_wpress/database.sql';
$handle = fopen($sqlFile, 'rb');
while (($line = fgets($handle)) !== false) {
    if (!str_contains($line, "13691,'rp_wcdpd_settings'")) {
        continue;
    }
    preg_match("/VALUES \\(13691,'rp_wcdpd_settings','(.+)','auto'\\);/", $line, $matches);
    $settings = unserialize(stripcslashes($matches[1]), ['allowed_classes' => true]);
    $store = $settings[1] ?? $settings;
    $rules = $store['product_pricing'] ?? [];

    echo "Regras bulk pricing: " . count($rules) . "\n\n";
    foreach ($rules as $idx => $rule) {
        if (!is_array($rule)) {
            continue;
        }
        $products = [];
        foreach ($rule['conditions'] ?? [] as $cond) {
            if (!is_array($cond)) {
                continue;
            }
            foreach ($cond['products'] ?? [] as $pid) {
                $products[] = (string) $pid;
            }
        }
        echo sprintf(
            "[%d] %s | produtos: %s\n",
            $idx,
            $rule['note'] ?? '(sem nota)',
            $products === [] ? '?' : implode(',', $products)
        );
        foreach ($rule['quantity_ranges'] ?? [] as $tier) {
            $to = $tier['to'] ?? '∞';
            echo sprintf(
                "    %s-%s un => R$ %s (%s)\n",
                $tier['from'] ?? '?',
                $to === null ? '∞' : $to,
                $tier['pricing_value'] ?? '?',
                $tier['pricing_method'] ?? ''
            );
        }
        echo "\n";
    }
    break;
}
fclose($handle);

// Map WP product IDs to slugs for yes desconto products
$sql = file_get_contents($sqlFile);
foreach ([211, 213] as $pid) {
    if (preg_match("/INSERT INTO `SERVMASK_PREFIX_posts` VALUES \\($pid,[^;]+'product'/", $sql)) {
        if (preg_match("/INSERT INTO `SERVMASK_PREFIX_posts` VALUES \\($pid,\\d+,'[^']*','[^']*','(?:[^']|'')*','([^']+)'/", $sql, $m)) {
            // fragile - use post_name
        }
    }
    if (preg_match("/INSERT INTO `SERVMASK_PREFIX_posts` VALUES \\($pid,.*?,'([^']+)','publish'.*?,'product'/", $sql, $m)) {
        echo "Product $pid slug hint: check manually\n";
    }
}

preg_match_all("/INSERT INTO `SERVMASK_PREFIX_posts` VALUES \\((211|213),/", $sql, $x);
echo "\nProdutos com tabela de desconto ativa (_mostrar_desconto_quantidade=yes):\n";
echo "- ID 211: Kit Ouro (Camiseta + Calção) - slug kit-ouro-camiseta-calcao\n";
echo "- ID 213: Kit Prata (Camiseta+Calção) - slug kit-prata-camisetacalcao\n";
