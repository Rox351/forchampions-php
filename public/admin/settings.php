<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/bootstrap.php';

$error = '';
$success = flash('success');
$settings = getSettings();
$faqItems = getFaqItems();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Token CSRF inválido.';
    } else {
        try {
            saveSetting('whatsapp', trim($_POST['whatsapp'] ?? ''));
            saveSetting('email', trim($_POST['email'] ?? ''));
            saveSetting('telefone', trim($_POST['telefone'] ?? ''));
            saveSetting('cnpj', trim($_POST['cnpj'] ?? ''));
            saveSetting('empresa', trim($_POST['empresa'] ?? 'For Champions'));
            saveSetting('endereco_retirada', trim($_POST['endereco_retirada'] ?? ''));
            saveSetting('copyright', trim($_POST['copyright'] ?? ''));

            $faq = [];
            foreach ($_POST['faq_pergunta'] ?? [] as $index => $pergunta) {
                $pergunta = trim((string) $pergunta);
                $resposta = trim((string) (($_POST['faq_resposta'] ?? [])[$index] ?? ''));
                if ($pergunta !== '' && $resposta !== '') {
                    $faq[] = ['pergunta' => $pergunta, 'resposta' => $resposta];
                }
            }

            saveSetting('faq_json', json_encode($faq, JSON_UNESCAPED_UNICODE));
            flash('success', 'Configurações salvas com sucesso.');
            redirect('/admin/settings.php');
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
    $settings = getSettings();
    $faqItems = getFaqItems();
}

renderAdminHeader('Configurações');
?>

<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

<h1 class="admin-page-title">Configurações do site</h1>
<p class="admin-page-subtitle">WhatsApp, contatos, rodapé e perguntas frequentes.</p>

<div class="admin-card">
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <label>WhatsApp<input type="text" name="whatsapp" value="<?= e($settings['whatsapp'] ?? '') ?>"></label>
        <label>E-mail<input type="email" name="email" value="<?= e($settings['email'] ?? appConfig()['app']['email']) ?>" readonly></label>
        <label>Telefone<input type="text" name="telefone" value="<?= e($settings['telefone'] ?? '') ?>"></label>
        <label>CNPJ<input type="text" name="cnpj" value="<?= e($settings['cnpj'] ?? '') ?>"></label>
        <label>Empresa<input type="text" name="empresa" value="<?= e($settings['empresa'] ?? 'For Champions') ?>"></label>
        <label>Retirada<input type="text" name="endereco_retirada" value="<?= e($settings['endereco_retirada'] ?? '') ?>"></label>
        <label>Copyright<input type="text" name="copyright" value="<?= e($settings['copyright'] ?? '') ?>"></label>
        <h2>FAQ</h2>
        <?php foreach ($faqItems as $item): ?>
            <label>Pergunta<input type="text" name="faq_pergunta[]" value="<?= e($item['pergunta'] ?? '') ?>"></label>
            <label>Resposta<textarea name="faq_resposta[]" rows="3"><?= e($item['resposta'] ?? '') ?></textarea></label>
        <?php endforeach; ?>
        <label>Nova pergunta<input type="text" name="faq_pergunta[]" value=""></label>
        <label>Nova resposta<textarea name="faq_resposta[]" rows="3"></textarea></label>
        <button class="btn btn-whatsapp" type="submit">Salvar configurações</button>
    </form>
</div>

<?php renderAdminFooter(); ?>
