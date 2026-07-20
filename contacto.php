<?php
require_once __DIR__ . '/includes/functions.php';
$brand     = setting('brand_name', 'CRRS');
$pageTitle = 'Contacto — ' . $brand;
$metaDesc  = 'Fale com a ' . $brand . ' pelo WhatsApp ou Instagram.';
$active    = 'contacto';
$ig    = ltrim(setting('instagram', ''), '@');
$wa    = preg_replace('/\D+/', '', setting('whatsapp', ''));
$email = trim(setting('contact_email', ''));
require __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container prose">
    <h1>Contacto</h1>
    <p><?= nl2br(e(setting('contact_text', ''))) ?></p>
    <div class="contact-cards">
      <?php if ($wa): ?>
      <div class="contact-card">
        <div class="ic">💬</div>
        <h3>WhatsApp</h3>
        <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener">Enviar mensagem</a>
      </div>
      <?php endif; ?>
      <?php if ($ig): ?>
      <div class="contact-card">
        <div class="ic">📸</div>
        <h3>Instagram</h3>
        <a href="https://instagram.com/<?= e($ig) ?>" target="_blank" rel="noopener">@<?= e($ig) ?></a>
      </div>
      <?php endif; ?>
      <?php if ($email): ?>
      <div class="contact-card">
        <div class="ic">✉️</div>
        <h3>Email</h3>
        <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
