<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/partials.php';
requireLogin();

$fields = [
    'brand_name', 'brand_tagline', 'hero_title', 'hero_subtitle', 'accent_color',
    'whatsapp', 'instagram', 'contact_email', 'contact_text', 'about_text',
    'buy_channel', 'currency',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'settings') {
    checkCsrf();
    $stmt = db()->prepare('INSERT INTO settings (key,value) VALUES (?,?) ON CONFLICT(key) DO UPDATE SET value=excluded.value');
    foreach ($fields as $f) {
        $stmt->execute([$f, trim((string)($_POST[$f] ?? ''))]);
    }
    // Logo
    $logo = handleUpload('logo');
    if ($logo) {
        $stmt->execute(['logo', $logo]);
    }
    if (($_POST['remove_logo'] ?? '') === '1') {
        $stmt->execute(['logo', '']);
    }
    redirect('settings.php?ok=' . rawurlencode('Configurações salvas.'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'password') {
    checkCsrf();
    $cur = $_POST['current'] ?? '';
    $new = $_POST['new'] ?? '';
    $adm = currentAdmin();
    $stmt = db()->prepare('SELECT * FROM admins WHERE id=?');
    $stmt->execute([$adm['id']]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($cur, $row['password'])) {
        redirect('settings.php?err=' . rawurlencode('Senha atual incorreta.'));
    }
    if (strlen($new) < 6) {
        redirect('settings.php?err=' . rawurlencode('A nova senha precisa ter ao menos 6 caracteres.'));
    }
    db()->prepare('UPDATE admins SET password=? WHERE id=?')
        ->execute([password_hash($new, PASSWORD_DEFAULT), $adm['id']]);
    redirect('settings.php?ok=' . rawurlencode('Senha alterada com sucesso.'));
}

$s = fn(string $k, string $d = '') => e(setting($k, $d));
$logoFile = setting('logo', '');
$hasLogo  = $logoFile && file_exists(CRRS_ROOT . '/uploads/' . $logoFile);
$channel  = setting('buy_channel', 'whatsapp');
adminHeader('Configurações', 'settings');
?>
<h1>Configurações</h1>
<p class="sub">Identidade da marca, contatos e canal de compra.</p>
<?= flash() ?>

<form method="post" enctype="multipart/form-data" class="card">
  <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
  <input type="hidden" name="form" value="settings">

  <h2>Marca</h2>
  <div class="row">
    <div>
      <label>Nome da marca</label>
      <input type="text" name="brand_name" value="<?= $s('brand_name') ?>">
    </div>
    <div>
      <label>Slogan / tagline</label>
      <input type="text" name="brand_tagline" value="<?= $s('brand_tagline') ?>">
    </div>
  </div>
  <div class="row">
    <div>
      <label>Título do banner (hero)</label>
      <input type="text" name="hero_title" value="<?= $s('hero_title') ?>">
    </div>
    <div>
      <label>Subtítulo do banner</label>
      <input type="text" name="hero_subtitle" value="<?= $s('hero_subtitle') ?>">
    </div>
  </div>
  <div class="row">
    <div>
      <label>Cor de destaque</label>
      <input type="color" name="accent_color" value="<?= $s('accent_color','#111111') ?>" style="height:44px;padding:4px">
    </div>
    <div>
      <label>Moeda</label>
      <input type="text" name="currency" value="<?= $s('currency','R$') ?>" placeholder="R$">
    </div>
  </div>

  <label>Logo (opcional)</label>
  <input type="file" name="logo" accept="image/*">
  <div class="help">Substitui o texto do nome no topo. PNG/SVG com fundo transparente fica melhor.</div>
  <?php if ($hasLogo): ?>
    <div style="margin-top:8px;background:#111;display:inline-block;padding:8px 14px;border-radius:8px">
      <img src="../uploads/<?= e($logoFile) ?>" alt="logo" style="height:30px">
    </div>
    <label style="font-weight:400"><input type="checkbox" name="remove_logo" value="1"> Remover logo</label>
  <?php endif; ?>

  <h2 style="margin-top:28px">Canal de compra</h2>
  <label>Botão “Comprar” abre:</label>
  <select name="buy_channel">
    <option value="whatsapp" <?= $channel==='whatsapp'?'selected':'' ?>>WhatsApp (recomendado — já vai com a mensagem pronta)</option>
    <option value="instagram" <?= $channel==='instagram'?'selected':'' ?>>Instagram Direct</option>
  </select>
  <div class="row" style="margin-top:6px">
    <div>
      <label>WhatsApp (com DDI+DDD)</label>
      <input type="text" name="whatsapp" value="<?= $s('whatsapp') ?>" placeholder="5511999999999">
      <div class="help">Só números. Ex.: 55 (Brasil) + 11 (DDD) + número.</div>
    </div>
    <div>
      <label>Instagram (@usuário)</label>
      <input type="text" name="instagram" value="<?= $s('instagram') ?>" placeholder="crrs">
    </div>
  </div>

  <h2 style="margin-top:28px">Páginas</h2>
  <label>Texto “Sobre”</label>
  <textarea name="about_text" rows="6"><?= $s('about_text') ?></textarea>
  <div class="help">Pule uma linha em branco para separar parágrafos.</div>

  <label>E-mail de contato (opcional)</label>
  <input type="email" name="contact_email" value="<?= $s('contact_email') ?>">

  <label>Texto da página “Contacto”</label>
  <textarea name="contact_text" rows="3"><?= $s('contact_text') ?></textarea>

  <div style="margin-top:22px">
    <button class="btn" type="submit">Salvar configurações</button>
  </div>
</form>

<form method="post" class="card">
  <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
  <input type="hidden" name="form" value="password">
  <h2>Alterar senha</h2>
  <div class="row">
    <div>
      <label>Senha atual</label>
      <input type="password" name="current" required>
    </div>
    <div>
      <label>Nova senha</label>
      <input type="password" name="new" required>
    </div>
  </div>
  <div style="margin-top:18px"><button class="btn ghost" type="submit">Alterar senha</button></div>
</form>
<?php adminFooter(); ?>
