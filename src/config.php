<?php

declare(strict_types=1);

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'forchampions',
        'user' => getenv('DB_USER') ?: 'fc_user',
        'pass' => getenv('DB_PASS') ?: 'fc_pass',
    ],
    'app' => [
        'url' => rtrim(getenv('APP_URL') ?: 'http://localhost:8081', '/'),
        'name' => 'For Champions',
        'email' => 'contato@forchampions.com.br',
        'timezone' => 'America/Sao_Paulo',
    ],
    'upload' => [
        'max_bytes' => 5 * 1024 * 1024,
        'allowed' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    ],
];
