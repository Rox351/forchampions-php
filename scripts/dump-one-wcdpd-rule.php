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
    $rule = $store['product_pricing'][0] ?? null;
    echo json_encode($rule, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    echo "\n--- Rule 211 mapping? ---\n";
    // find rule linked to product 211
    foreach ($store['product_pricing'] as $idx => $r) {
        if (!is_array($r)) {
            continue;
        }
        $ids = array_merge(
            (array) ($r['product_ids'] ?? []),
            (array) ($r['products'] ?? []),
            (array) ($r['applicable_products'] ?? [])
        );
        foreach ($ids as $id) {
            if ((int) $id === 211 || (string) $id === '211') {
                echo "Rule index $idx for product 211:\n";
                echo json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            }
        }
        // check nested structure
        if (isset($r['conditions'])) {
            $json = json_encode($r['conditions']);
            if (str_contains($json, '211')) {
                echo "Rule index $idx conditions mention 211\n";
            }
        }
    }
    break;
}
fclose($handle);

// sample _a_totals_pricing_rules
preg_match_all("/INSERT INTO `SERVMASK_PREFIX_postmeta` VALUES \\(\\d+,\\d+,'_a_totals_pricing_rules','([^']*)'\\);/", file_get_contents($sqlFile), $totals);
echo "\n=== _a_totals_pricing_rules samples ===\n";
foreach (array_slice($totals[1] ?? [], 0, 3) as $sample) {
    echo substr(stripcslashes($sample), 0, 500) . "\n---\n";
}
