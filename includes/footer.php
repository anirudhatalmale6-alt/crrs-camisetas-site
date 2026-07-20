<?php
$brandName = setting('brand_name', 'CRRS');
$ig  = ltrim(setting('instagram', ''), '@');
$wa  = preg_replace('/\D+/', '', setting('whatsapp', ''));
?>
</main>
<footer class="site-footer">
  <div class="container">
    <div class="footer-inner">
      <div class="brand"><?= e($brandName) ?></div>
      <nav class="footer-links">
        <a href="catalogo.php">Catálogo</a>
        <a href="sobre.php">Sobre</a>
        <a href="contacto.php">Contacto</a>
        <?php if ($ig): ?><a href="https://instagram.com/<?= e($ig) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
        <?php if ($wa): ?><a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?>
      </nav>
    </div>
    <div class="copy">© <?= date('Y') ?> <?= e($brandName) ?>. Todos os direitos reservados.</div>
  </div>
</footer>
</body>
</html>
