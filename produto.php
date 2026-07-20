<?php
require_once __DIR__ . '/includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$p  = getProduct($id);
if (!$p || !(int)$p['active']) {
    http_response_code(404);
    $pageTitle = 'Produto não encontrado';
    require __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container prose" style="text-align:center">'
       . '<h1>Ops!</h1><p>Esse produto não está disponível.</p>'
       . '<a class="btn" href="catalogo.php">Voltar ao catálogo</a></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $p['name'] . ' — ' . setting('brand_name', 'CRRS');
$metaDesc  = mb_substr(trim($p['description']) ?: $p['name'], 0, 155);
$active    = 'catalogo';
$sizes     = sizesArray($p['sizes']);
$channel   = setting('buy_channel', 'whatsapp');
$brand     = setting('brand_name', 'CRRS');

require __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <article class="product">
      <div class="gallery">
        <img src="<?= e(productImage($p)) ?>" alt="<?= e($p['name']) ?>" width="640" height="800">
      </div>
      <div class="details">
        <h1><?= e($p['name']) ?></h1>
        <p class="price-lg"><?= money((float)$p['price']) ?></p>
        <?php if (trim($p['description'])): ?>
          <p class="desc"><?= nl2br(e($p['description'])) ?></p>
        <?php endif; ?>

        <?php if ($sizes): ?>
        <div class="field">
          <label>Tamanho</label>
          <div class="sizes" id="sizes">
            <?php foreach ($sizes as $s): ?>
              <span class="size-opt" data-size="<?= e($s) ?>"><?= e($s) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="field">
          <label for="obs">Observação (opcional)</label>
          <textarea id="obs" rows="2" placeholder="Ex.: cor, dúvida sobre medidas..."></textarea>
        </div>

        <button class="btn block" id="buyBtn" type="button">
          <?= $channel === 'instagram' ? 'Comprar pelo Instagram' : 'Comprar pelo WhatsApp' ?>
        </button>
        <div class="hint" id="hint"></div>
      </div>
    </article>
  </div>
</section>

<script>
(function(){
  var sizes = document.getElementById('sizes');
  var selected = null;
  if (sizes){
    sizes.addEventListener('click', function(ev){
      var opt = ev.target.closest('.size-opt');
      if (!opt) return;
      [].forEach.call(sizes.querySelectorAll('.size-opt'), function(o){ o.classList.remove('selected'); });
      opt.classList.add('selected');
      selected = opt.getAttribute('data-size');
      document.getElementById('hint').textContent = '';
    });
  }

  var CFG = {
    channel: <?= json_encode($channel) ?>,
    base: <?= json_encode(buyLinkBase($p)) ?>,
    needSize: <?= $sizes ? 'true' : 'false' ?>,
    product: <?= json_encode($p['name']) ?>,
    price: <?= json_encode(money((float)$p['price'])) ?>,
    brand: <?= json_encode($brand) ?>
  };

  document.getElementById('buyBtn').addEventListener('click', function(){
    if (CFG.needSize && !selected){
      document.getElementById('hint').textContent = 'Por favor, escolha um tamanho.';
      return;
    }
    var obs = (document.getElementById('obs').value || '').trim();
    var lines = [
      'Olá! Tenho interesse nesta peça da ' + CFG.brand + ':',
      '',
      '• Produto: ' + CFG.product,
      '• Preço: ' + CFG.price
    ];
    if (selected) lines.push('• Tamanho: ' + selected);
    if (obs)      lines.push('• Observação: ' + obs);
    var msg = lines.join('\n');

    var url;
    if (CFG.channel === 'instagram'){
      // O Instagram não aceita texto pré-preenchido no DM; copiamos a mensagem
      // e abrimos a conversa para o cliente colar.
      try { navigator.clipboard.writeText(msg); } catch(e){}
      document.getElementById('hint').style.color = '#2e7d32';
      document.getElementById('hint').textContent = 'Mensagem copiada! Cole no Instagram que já vai abrir.';
      url = CFG.base;
    } else {
      url = CFG.base + '?text=' + encodeURIComponent(msg);
    }
    window.open(url, '_blank');
  });
})();
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
