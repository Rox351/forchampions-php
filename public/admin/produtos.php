<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$error = '';
$success = flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Token CSRF invalido.';
    } else {
        try {
            $postAction = $_POST['action'] ?? '';

            if ($postAction === 'delete') {
                $stmt = db()->prepare('DELETE FROM products WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
                flash('success', 'Produto excluido com sucesso.');
                redirect('/admin/produtos.php');
            }

            $nome = trim($_POST['nome'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            $preco = (float) str_replace(',', '.', str_replace('.', '', $_POST['preco'] ?? '0'));
            $descricao = trim($_POST['descricao'] ?? '');
            $slug = trim($_POST['slug'] ?? '') ?: slugify($nome);
            $destaque = isset($_POST['destaque']) ? 1 : 0;
            $ativo = isset($_POST['ativo']) ? 1 : 0;
            $ordem = (int) ($_POST['ordem'] ?? 0);

            if ($nome === '' || $categoria === '') {
                throw new RuntimeException('Nome e categoria sao obrigatorios.');
            }

            $current = null;
            if ($postAction === 'update') {
                $stmt = db()->prepare('SELECT * FROM products WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
                $current = $stmt->fetch() ?: null;
            }

            $imagem = handleImageUpload($_FILES['imagem'] ?? null, $current['imagem'] ?? null);

            if ($postAction === 'create') {
                $stmt = db()->prepare(
                    'INSERT INTO products (nome, slug, categoria, preco, descricao, imagem, destaque, ativo, ordem)
                     VALUES (:nome, :slug, :categoria, :preco, :descricao, :imagem, :destaque, :ativo, :ordem)'
                );
                $stmt->execute(compact('nome', 'slug', 'categoria', 'preco', 'descricao', 'imagem', 'destaque', 'ativo', 'ordem'));
                flash('success', 'Produto criado com sucesso.');
            } elseif ($postAction === 'update') {
                $stmt = db()->prepare(
                    'UPDATE products SET nome = :nome, slug = :slug, categoria = :categoria, preco = :preco,
                     descricao = :descricao, imagem = :imagem, destaque = :destaque, ativo = :ativo, ordem = :ordem
                     WHERE id = :id'
                );
                $stmt->execute([
                    'id' => (int) ($_POST['id'] ?? 0),
                    'nome' => $nome,
                    'slug' => $slug,
                    'categoria' => $categoria,
                    'preco' => $preco,
                    'descricao' => $descricao,
                    'imagem' => $imagem,
                    'destaque' => $destaque,
                    'ativo' => $ativo,
                    'ordem' => $ordem,
                ]);
                flash('success', 'Produto atualizado com sucesso.');
            }

            redirect('/admin/produtos.php');
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}

$product = null;
if ($action === 'edit' && $id > 0) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch() ?: null;
}

$products = db()->query('SELECT * FROM products ORDER BY ordem ASC, nome ASC')->fetchAll();
renderAdminHeader($action === 'create' ? 'Novo produto' : ($action === 'edit' ? 'Editar produto' : 'Produtos'));
?>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

<?php if ($action === 'create' || $action === 'edit'): ?>
    <h1 class="admin-page-title"><?= $action === 'create' ? 'Adicionar produto' : 'Editar produto' ?></h1>
    <p class="admin-page-subtitle">Preencha os dados do produto exibidos na landing page.</p>
    <div class="admin-card">
        <form method="post" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
            <?php if ($product): ?><input type="hidden" name="id" value="<?= (int) $product['id'] ?>"><?php endif; ?>
            <label>Nome<input type="text" name="nome" required value="<?= e($product['nome'] ?? '') ?>"></label>
            <label>Slug<input type="text" name="slug" value="<?= e($product['slug'] ?? '') ?>" placeholder="Gerado automaticamente se vazio"></label>
            <label>Categoria<input type="text" name="categoria" required value="<?= e($product['categoria'] ?? '') ?>"></label>
            <label>Preco<input type="text" name="preco" required value="<?= e(isset($product['preco']) ? number_format((float) $product['preco'], 2, ',', '.') : '') ?>"></label>
            <label>Descrição<textarea name="descricao" rows="6"><?= e($product['descricao'] ?? '') ?></textarea></label>
            <label>Imagem<input type="file" name="imagem" accept="image/*"></label>
            <?php if (!empty($product['imagem'])): ?><img class="admin-thumb" src="<?= e(productImageUrl($product['imagem'])) ?>" alt=""><?php endif; ?>
            <label class="checkbox-label"><input type="checkbox" name="destaque" <?= !empty($product['destaque']) ? 'checked' : '' ?>> Destaque (mais vendidos)</label>
            <label class="checkbox-label"><input type="checkbox" name="ativo" <?= !isset($product['ativo']) || !empty($product['ativo']) ? 'checked' : '' ?>> Ativo</label>
            <label>Ordem<input type="number" name="ordem" value="<?= e((string) ($product['ordem'] ?? 0)) ?>"></label>
            <div class="admin-actions">
                <button class="btn btn-whatsapp" type="submit">Salvar produto</button>
                <a class="btn btn-outline" href="/admin/produtos.php">Cancelar</a>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="admin-card-header">
        <div>
            <h1 class="admin-page-title">Produtos</h1>
            <p class="admin-page-subtitle">Gerencie o catalogo exibido na loja.</p>
        </div>
        <a class="btn btn-whatsapp" href="/admin/produtos.php?action=create">Adicionar produto</a>
    </div>
    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>Imagem</th><th>Nome</th><th>Categoria</th><th>Preco</th><th>Status</th><th>Acoes</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $item): ?>
                        <tr>
                            <td><img class="admin-thumb" src="<?= e(productImageUrl($item['imagem'])) ?>" alt=""></td>
                            <td><?= e($item['nome']) ?></td>
                            <td><?= e($item['categoria']) ?></td>
                            <td><?= e(formatPrice((float) $item['preco'])) ?></td>
                            <td><?= (int) $item['ativo'] === 1 ? 'Ativo' : 'Inativo' ?><?= (int) $item['destaque'] === 1 ? ' / Destaque' : '' ?></td>
                            <td>
                                <a href="/admin/produtos.php?action=edit&id=<?= (int) $item['id'] ?>">Editar</a> |
                                <form method="post" class="admin-inline-form" onsubmit="return confirm('Excluir este produto?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php renderAdminFooter(); ?>
