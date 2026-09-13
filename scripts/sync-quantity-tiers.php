<?php

declare(strict_types=1);

/**
 * Sincroniza faixas de desconto por quantidade (WCDPD) para o banco da aplicacao.
 *
 * Requer database WordPress em forchampions_wp (import-from-backup) ou rp_wcdpd_settings no SQL.
 *
 * Uso:
 *   php scripts/sync-quantity-tiers.php
 *   docker exec fc-php-web php /var/www/html/scripts/sync-quantity-tiers.php
 */

require_once dirname(__DIR__) . '/src/bootstrap.php';

function connectWpDatabase(): PDO
{
    $config = appConfig()['db'];
    $dbName = getenv('WP_DB_NAME') ?: 'forchampions_wp';
    $rootPass = getenv('DB_ROOT_PASSWORD') ?: 'rootpass';
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $config['host'],
        $config['port'],
        $dbName
    );

    return new PDO($dsn, 'root', $rootPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
    ]);
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__)) {
    try {
        $appDb = db();
        $wpDb = connectWpDatabase();
        $count = importQuantityTiers($appDb, $wpDb);
        echo "Faixas importadas (linhas): {$count}\n";
        echo "Concluido.\n";
    } catch (Throwable $e) {
        fwrite(STDERR, 'Erro: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}
