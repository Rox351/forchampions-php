<?php

declare(strict_types=1);

/**
 * Gera site estatico em docs/ para GitHub Pages.
 *
 * Uso:
 *   STATIC_BASE=/forchampions-php php scripts/build-github-pages.php
 *   docker exec -e STATIC_BASE=/forchampions-php fc-php-web php /var/www/html/scripts/build-github-pages.php
 */

const DOCS_PATH = __DIR__ . '/../docs';
const PUBLIC_PATH = __DIR__ . '/../public';

function docsPath(string $path = ''): string
{
    $path = ltrim($path, '/');
    return $path === '' ? DOCS_PATH : DOCS_PATH . '/' . $path;
}

function publicAssetPath(string $path): string
{
    return PUBLIC_PATH . '/' . ltrim($path, '/');
}

function ensureDir(string $path): void
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function removeDir(string $path): void
{
    if (!is_dir($path)) {
        return;
    }

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($items as $item) {
        if ($item->isDir()) {
            rmdir($item->getPathname());
        } else {
            unlink($item->getPathname());
        }
    }

    rmdir($path);
}

function writeFile(string $path, string $contents): void
{
    ensureDir(dirname($path));
    file_put_contents($path, $contents);
}

function copyFile(string $from, string $to): void
{
    if (!file_exists($from)) {
        return;
    }
    ensureDir(dirname($to));
    copy($from, $to);
}

function copyTree(string $from, string $to): void
{
    if (!is_dir($from)) {
        return;
    }

    ensureDir($to);
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $target = $to . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        if ($item->isDir()) {
            ensureDir($target);
        } else {
            ensureDir(dirname($target));
            copy($item->getPathname(), $target);
        }
    }
}

function renderPublicPage(string $file, array $query = []): string
{
    $_GET = $query;
    $_POST = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['SCRIPT_NAME'] = '/' . $file;

    ob_start();
    include PUBLIC_PATH . '/' . $file;
    return (string) ob_get_clean();
}

function collectAssetPaths(array $products, array $pages): array
{
    $assets = [
        'assets/css/style.css',
        'assets/js/main.js',
        'assets/js/shop.js',
        'assets/images/branding/logo.svg',
        'assets/images/branding/placeholder-product.svg',
    ];

    $brandingDir = publicAssetPath('assets/images/branding');
    if (is_dir($brandingDir)) {
        foreach (glob($brandingDir . '/*') ?: [] as $file) {
            if (is_file($file)) {
                $assets[] = 'assets/images/branding/' . basename($file);
            }
        }
    }

    foreach ($products as $product) {
        $image = (string) ($product['imagem'] ?? '');
        if ($image !== '') {
            $assets[] = ltrim($image, '/');
        }
    }

    foreach ($pages as $page) {
        if (preg_match_all('#(?:assets/uploads/[^"\')\s>]+|uploads/[^"\')\s>]+)#', (string) $page['conteudo'], $matches)) {
            foreach ($matches[0] as $match) {
                $assets[] = ltrim($match, '/');
            }
        }
    }

    return array_values(array_unique($assets));
}

function copyAssets(array $assetPaths): int
{
    $copied = 0;
    foreach ($assetPaths as $relative) {
        $from = publicAssetPath($relative);
        $to = docsPath($relative);
        if (file_exists($from)) {
            copyFile($from, $to);
            $copied++;
        }
    }
    return $copied;
}

function buildStaticDemo(string $basePath): array
{
    $basePath = '/' . trim($basePath, '/');
    $appUrl = 'https://rox351.github.io' . $basePath;

    putenv('STATIC_BUILD=1');
    putenv('APP_URL=' . $appUrl);

    require_once __DIR__ . '/../src/bootstrap.php';

    if (is_dir(DOCS_PATH)) {
        removeDir(DOCS_PATH);
    }
    ensureDir(DOCS_PATH);

    $settings = getSettings();
    $products = getProducts(true);
    $pagesStmt = db()->query('SELECT slug, titulo, conteudo, ativo FROM pages WHERE ativo = 1 ORDER BY id');
    $pages = $pagesStmt->fetchAll();

    writeFile(docsPath('index.html'), renderPublicPage('index.php'));
    writeFile(docsPath('loja.html'), renderPublicPage('loja.php'));

    $productCount = 0;
    foreach ($products as $product) {
        $slug = (string) $product['slug'];
        writeFile(
            docsPath('produto/' . $slug . '.html'),
            renderPublicPage('produto.php', ['slug' => $slug])
        );
        $productCount++;
    }

    $pageCount = 0;
    foreach ($pages as $page) {
        $slug = (string) $page['slug'];
        writeFile(
            docsPath('pagina/' . $slug . '.html'),
            renderPublicPage('pagina.php', ['slug' => $slug])
        );
        $pageCount++;
    }

    $assetsCopied = copyAssets(collectAssetPaths($products, $pages));
    writeFile(docsPath('.nojekyll'), '');
    writeFile(docsPath('404.html'), renderPublicPage('pagina.php', ['slug' => 'quem-somos']));

    return [
        'url' => $appUrl . '/',
        'products' => $productCount,
        'pages' => $pageCount,
        'assets' => $assetsCopied,
    ];
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__)) {
    $base = getenv('STATIC_BASE') ?: '/forchampions-php';

    try {
        echo "Gerando demo estatica para GitHub Pages...\n";
        $result = buildStaticDemo($base);
        echo "URL prevista: {$result['url']}\n";
        echo "Produtos: {$result['products']}\n";
        echo "Paginas: {$result['pages']}\n";
        echo "Assets copiados: {$result['assets']}\n";
        echo "Concluido.\n";
    } catch (Throwable $e) {
        fwrite(STDERR, 'Erro: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}
