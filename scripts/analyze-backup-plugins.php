<?php

declare(strict_types=1);

$sqlFile = __DIR__ . '/../_extracted_wpress/database.sql';
$sql = file_get_contents($sqlFile);

// Installed plugins from update transient (no_update + response)
if (preg_match("/INSERT INTO `SERVMASK_PREFIX_options` VALUES \\(53974,'_site_transient_update_plugins','(.+)','off'\\);/s", $sql, $m)) {
    $blob = stripcslashes($m[1]);
    $obj = unserialize($blob, ['allowed_classes' => true]);
    $plugins = [];
    if ($obj) {
        foreach (['response', 'no_update'] as $key) {
            if (isset($obj->$key) && is_object($obj->$key)) {
                foreach (array_keys((array) $obj->$key) as $pluginFile) {
                    $plugins[$pluginFile] = true;
                }
            }
        }
    }
    echo "=== PLUGINS INSTALADOS (" . count($plugins) . ") ===\n";
    ksort($plugins);
    foreach (array_keys($plugins) as $p) {
        echo $p . "\n";
    }
}

// wcdpd rules posts
preg_match_all("/INSERT INTO `SERVMASK_PREFIX_posts` VALUES \\([^;]+'([^']+)'\\);/", $sql, $postTypes);
$types = array_count_values($postTypes[1] ?? []);
arsort($types);
echo "\n=== POST TYPES (top) ===\n";
foreach (array_slice($types, 0, 25, true) as $type => $count) {
    echo "$type: $count\n";
}

// Meta keys related to discount
preg_match_all("/,'(_[^']*(?:desconto|pricing|wcdpd|faixa|tab)[^']*)',/", $sql, $metaKeys);
$keys = array_count_values($metaKeys[1] ?? []);
arsort($keys);
echo "\n=== META KEYS (desconto/pricing/tab) ===\n";
foreach ($keys as $k => $c) {
    echo "$k ($c)\n";
}

// Products with mostrar_desconto yes
preg_match_all("/INSERT INTO `SERVMASK_PREFIX_postmeta` VALUES \\(\\d+,\\d+,'_mostrar_desconto_quantidade','yes'\\);/", $sql, $yes);
echo "\n=== PRODUTOS com _mostrar_desconto_quantidade=yes: " . count($yes[0]) . " ===\n";

// Extract rp_wcdpd product pricing rules option snippet
if (preg_match("/INSERT INTO `SERVMASK_PREFIX_options` VALUES \\(13691,'rp_wcdpd_settings','(.+)','auto'\\);/s", $sql, $rm)) {
    $settings = @unserialize(stripcslashes($rm[1]), ['allowed_classes' => true]);
    if (is_array($settings)) {
        echo "\n=== rp_wcdpd_settings keys ===\n";
        foreach ($settings as $uid => $cfg) {
            if (is_array($cfg)) {
                echo "User/store $uid: " . implode(', ', array_slice(array_keys($cfg), 0, 15)) . "...\n";
                if (isset($cfg['product_pricing'])) {
                    echo "product_pricing rules count: " . (is_array($cfg['product_pricing']) ? count($cfg['product_pricing']) : 0) . "\n";
                }
            }
        }
    }
}
