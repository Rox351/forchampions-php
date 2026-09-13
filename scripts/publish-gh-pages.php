<?php

declare(strict_types=1);

/**
 * Publica o conteudo de docs/ na branch gh-pages (raiz do site estatico).
 *
 * Uso (apos build-github-pages.php):
 *   php scripts/publish-gh-pages.php
 */

const REPO_ROOT = __DIR__ . '/..';
const DOCS_DIR = REPO_ROOT . '/docs';

function run(string $command): string
{
    $output = [];
    $code = 0;
    exec($command . ' 2>&1', $output, $code);
    if ($code !== 0) {
        throw new RuntimeException(trim(implode("\n", $output)) ?: $command);
    }

    return trim(implode("\n", $output));
}

function removePath(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    if (is_file($path) || is_link($path)) {
        unlink($path);
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

function copyTree(string $from, string $to): void
{
    if (!is_dir($from)) {
        throw new RuntimeException('Pasta docs/ nao encontrada. Execute build-github-pages.php primeiro.');
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $target = $to . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        if ($item->isDir()) {
            if (!is_dir($target)) {
                mkdir($target, 0755, true);
            }
        } else {
            $dir = dirname($target);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            copy($item->getPathname(), $target);
        }
    }
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__)) {
    chdir(REPO_ROOT);

    if (!is_dir(DOCS_DIR)) {
        fwrite(STDERR, "Erro: docs/ inexistente.\n");
        exit(1);
    }

    try {
        $branch = trim(run('git rev-parse --abbrev-ref HEAD'));
        if ($branch !== 'main') {
            throw new RuntimeException('Execute na branch main (atual: ' . $branch . ')');
        }

        if (file_exists(REPO_ROOT . '/.nojekyll') && !file_exists(REPO_ROOT . '/docs/.nojekyll')) {
            @unlink(REPO_ROOT . '/.nojekyll');
        }

        $staging = sys_get_temp_dir() . '/forchampions-gh-pages-' . getmypid();
        removePath($staging);
        copyTree(DOCS_DIR, $staging);

        run('git fetch origin gh-pages');
        run('git checkout gh-pages');
        run('git reset --hard origin/gh-pages');

        foreach (['assets', 'pagina', 'produto'] as $dir) {
            removePath(REPO_ROOT . '/' . $dir);
        }
        foreach (['index.html', 'loja.html', '404.html', '.nojekyll'] as $file) {
            removePath(REPO_ROOT . '/' . $file);
        }

        copyTree($staging, REPO_ROOT);
        removePath($staging);

        run('git add .nojekyll index.html loja.html 404.html assets pagina produto');
        $status = run('git status --porcelain');
        if ($status === '') {
            echo "Nenhuma alteracao para publicar.\n";
        } else {
            run('git commit -m "chore: atualizar demo estatica GitHub Pages"');
            run('git push origin gh-pages');
            echo "Demo publicada em gh-pages.\n";
        }

        run('git checkout main');
        echo "Concluido.\n";
    } catch (Throwable $e) {
        fwrite(STDERR, 'Erro: ' . $e->getMessage() . PHP_EOL);
        @run('git checkout main');
        exit(1);
    }
}
