<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = setting('brand_name', 'CRRS') . ' — ' . setting('brand_tagline', '');
$metaDesc  = setting('brand_tagline', '');
$active    = 'home';
$products  = activeProducts();
$featured  = array_slice($products, 0, 8);
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="container">
    <h1><?= e(setting('hero_title', 'CRRS')) ?></h1>
    <p><?= e(setting('hero_subtitle', '')) ?></p>
    <a class="btn" href="catalogo.php">Ver coleção</a>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2>Coleção</h2>
      <p>As peças da temporada. Escolha o tamanho e finalize direto com a gente.</p>
    </div>
    <?php if (!$featured): ?>
      <p style="text-align:center;color:var(--muted)">Em breve novidades por aqui.</p>
    <?php else: ?>
    <div class="grid">
      <?php foreach ($featured as $p): ?>
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
    <div style="text-align:center;margin-top:44px">
      <a class="btn outline" href="catalogo.php">Ver catálogo completo</a>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
