<?php
require_once __DIR__ . '/includes/functions.php';
$brand     = setting('brand_name', 'CRRS');
$pageTitle = 'Sobre — ' . $brand;
$metaDesc  = 'Conheça a história da ' . $brand . '.';
$active    = 'sobre';
require __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container prose">
    <h1>Sobre a <?= e($brand) ?></h1>
    <?php foreach (preg_split('/\n\s*\n/', trim(setting('about_text', ''))) as $par): ?>
      <p><?= nl2br(e($par)) ?></p>
    <?php endforeach; ?>
    <div style="margin-top:34px">
      <a class="btn" href="catalogo.php">Ver a coleção</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
