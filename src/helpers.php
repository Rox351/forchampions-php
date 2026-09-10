<?php

declare(strict_types=1);

function appConfig(): array
{
    return require SRC_PATH . '/config.php';
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function baseUrl(string $path = ''): string
{
    $base = appConfig()['app']['url'];
    $path = ltrim($path, '/');

    if (getenv('STATIC_BUILD') === '1') {
        return staticBuildUrl($base, $path);
    }

    return $path === '' ? $base : $base . '/' . $path;
}

function staticBuildUrl(string $base, string $path): string
{
    if ($path === '') {
        return $base . '/';
    }

    if (preg_match('/^loja\.php(?:\?(.*))?$/', $path, $matches)) {
        return isset($matches[1]) && $matches[1] !== ''
            ? $base . '/loja.html?' . $matches[1]
            : $base . '/loja.html';
    }

    if (preg_match('/^produto\.php\?slug=([^&#]+)/', $path, $matches)) {
        return $base . '/produto/' . rawurlencode(rawurldecode($matches[1])) . '.html';
    }

    if (preg_match('/^pagina\.php\?slug=([^&#]+)/', $path, $matches)) {
        return $base . '/pagina/' . rawurlencode(rawurldecode($matches[1])) . '.html';
    }

    return $base . '/' . $path;
}

function assetUrl(string $path): string
{
    return baseUrl(ltrim($path, '/'));
}

function publicPath(string $path = ''): string
{
    $path = ltrim($path, '/');
    return $path === '' ? PUBLIC_PATH : PUBLIC_PATH . '/' . $path;
}

function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    $text = strtolower((string) $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'produto';
}

function formatPrice(float $price): string
{
    return 'R$ ' . number_format($price, 2, ',', '.');
}

function normalizePhoneWhatsApp(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if (str_starts_with($digits, '0')) {
        $digits = ltrim($digits, '0');
    }
    if (!str_starts_with($digits, '55')) {
        $digits = '55' . $digits;
    }
    return $digits;
}

function whatsappLink(array $product, array $settings): string
{
    $phone = normalizePhoneWhatsApp($settings['whatsapp'] ?? '51991886097');
    $price = (float) ($product['preco'] ?? 0);

    if ($price > 0) {
        $message = "Olá! Vim pelo site For Champions e gostaria de comprar:\n\n"
            . '*' . $product['nome'] . "*\n"
            . 'Preço: ' . formatPrice($price) . "\n\n"
            . 'Pode me ajudar com tamanho e personalização?';
    } else {
        $message = 'Olá! Vim pelo site For Champions e gostaria de conhecer os produtos e fazer um pedido personalizado.';
    }

    return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
}

function getSettings(): array
{
    $stmt = db()->query('SELECT chave, valor FROM settings');
    $settings = [];
    foreach ($stmt->fetchAll() as $row) {
        $settings[$row['chave']] = $row['valor'];
    }
    $settings['email'] = appConfig()['app']['email'];
    return $settings;
}

function getSetting(string $key, string $default = ''): string
{
    if ($key === 'email') {
        return appConfig()['app']['email'];
    }
    return getSettings()[$key] ?? $default;
}

function getFaqItems(): array
{
    $items = json_decode(getSetting('faq_json', '[]'), true);
    return is_array($items) ? $items : [];
}

function getProducts(bool $onlyActive = true, ?string $category = null, ?string $search = null): array
{
    $sql = 'SELECT * FROM products WHERE 1=1';
    $params = [];

    if ($onlyActive) {
        $sql .= ' AND ativo = 1';
    }
    if ($category !== null && $category !== '' && $category !== 'all') {
        $sql .= ' AND categoria = :categoria';
        $params['categoria'] = $category;
    }
    if ($search !== null && $search !== '') {
        $sql .= ' AND (nome LIKE :search OR descricao LIKE :search OR categoria LIKE :search)';
        $params['search'] = '%' . $search . '%';
    }

    $sql .= ' ORDER BY ordem ASC, nome ASC';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProductBySlug(string $slug): ?array
{
    $stmt = db()->prepare('SELECT * FROM products WHERE slug = :slug AND ativo = 1 LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $product = $stmt->fetch();
    return $product ?: null;
}

function getCategories(): array
{
    $stmt = db()->query('SELECT DISTINCT categoria FROM products WHERE ativo = 1 ORDER BY categoria ASC');
    return array_column($stmt->fetchAll(), 'categoria');
}

function getPageBySlug(string $slug): ?array
{
    $stmt = db()->prepare('SELECT * FROM pages WHERE slug = :slug AND ativo = 1 LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $page = $stmt->fetch();
    return $page ?: null;
}

function renderPageContent(string $html): string
{
    return strip_tags($html, '<h1><h2><h3><h4><p><ul><ol><li><strong><em><a><br>');
}

function productImageUrl(?string $path): string
{
    if ($path === null || $path === '') {
        return assetUrl('assets/images/branding/placeholder-product.svg');
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    if (!file_exists(publicPath($path))) {
        return assetUrl('assets/images/branding/placeholder-product.svg');
    }
    return assetUrl($path);
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(?string $token): bool
{
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    return null;
}

function saveSetting(string $key, string $value): void
{
    if ($key === 'email') {
        $value = appConfig()['app']['email'];
    }

    $stmt = db()->prepare(
        'INSERT INTO settings (chave, valor) VALUES (:chave, :valor)
         ON DUPLICATE KEY UPDATE valor = VALUES(valor)'
    );
    $stmt->execute(['chave' => $key, 'valor' => $value]);
}

function handleImageUpload(?array $file, ?string $currentPath = null): ?string
{
    if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $currentPath;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Falha no upload da imagem.');
    }

    $config = appConfig()['upload'];
    if (($file['size'] ?? 0) > $config['max_bytes']) {
        throw new RuntimeException('Imagem maior que o limite permitido (5 MB).');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']) ?: '';
    if (!in_array($mime, $config['allowed'], true)) {
        throw new RuntimeException('Formato de imagem nao permitido.');
    }

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        default => 'bin',
    };

    $filename = slugify(pathinfo($file['name'], PATHINFO_FILENAME)) . '-' . time() . '.' . $ext;
    $uploadDir = publicPath('uploads');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $relative = 'uploads/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], publicPath($relative))) {
        throw new RuntimeException('Nao foi possivel salvar a imagem.');
    }

    return $relative;
}
