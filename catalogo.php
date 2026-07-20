<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Catálogo — ' . setting('brand_name', 'CRRS');
$metaDesc  = 'Catálogo de camisetas da ' . setting('brand_name', 'CRRS') . '. ' . setting('brand_tagline', '');
$active    = 'catalogo';
$products  = activeProducts();
require __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <h2>Catálogo</h2>
      <p><?= count($products) ?> peça<?= count($products)===1?'':'s' ?> na coleção</p>
    </div>
    <?php if (!$products): ?>
      <p style="text-align:center;color:var(--muted)">Nenhum produto cadastrado ainda.</p>
    <?php else: ?>
    <div class="grid">
      <?php foreach ($products as $p): ?>
        <a class="card" href="produto.php?id=<?= (int)$p['id'] ?>">
          <div class="thumb">
            <img src="<?= e(productImage($p)) ?>" alt="<?= e($p['name']) ?>" loading="lazy" width="480" height="600">
          </div>
          <div class="info">
            <h3><?= e($p['name']) ?></h3>
            <div class="price"><?= money((float)$p['price']) ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
