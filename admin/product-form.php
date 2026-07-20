<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/partials.php';
requireLogin();

$id      = (int)($_GET['id'] ?? 0);
$editing = $id > 0;
$p       = $editing ? getProduct($id) : null;
if ($editing && !$p) {
    redirect('index.php?err=' . rawurlencode('Produto não encontrado.'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $name  = trim($_POST['name'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $price = (float)str_replace(',', '.', $_POST['price'] ?? '0');
    $sizes = trim($_POST['sizes'] ?? '');
    $order = (int)($_POST['sort_order'] ?? 0);
    $active = isset($_POST['active']) ? 1 : 0;

    // normaliza tamanhos "P, M , G" -> "P,M,G"
    $sizes = implode(',', sizesArray($sizes));

    if ($name === '') {
        redirect('product-form.php' . ($editing ? "?id=$id" : '') . '&err=' . rawurlencode('O nome é obrigatório.'));
    }

    $newImage = handleUpload('image');
    $image    = $p['image'] ?? '';
    if ($newImage) {
        // apaga upload antigo (não os demos)
        if ($image && !file_exists(CRRS_ROOT . '/assets/img/' . $image)) {
            @unlink(UPLOAD_DIR . '/' . $image);
        }
        $image = $newImage;
    }

    if ($editing) {
        db()->prepare('UPDATE products SET name=?, description=?, price=?, sizes=?, image=?, sort_order=?, active=? WHERE id=?')
            ->execute([$name, $desc, $price, $sizes, $image, $order, $active, $id]);
    } else {
        db()->prepare('INSERT INTO products (name, description, price, sizes, image, sort_order, active) VALUES (?,?,?,?,?,?,?)')
            ->execute([$name, $desc, $price, $sizes, $image, $order, $active]);
    }
    redirect('index.php?ok=' . rawurlencode($editing ? 'Produto atualizado.' : 'Produto adicionado.'));
}

$v = function (string $k, $d = '') use ($p) { return e($p[$k] ?? $d); };
adminHeader($editing ? 'Editar produto' : 'Novo produto', 'produtos');
?>
<h1><?= $editing ? 'Editar produto' : 'Novo produto' ?></h1>
<p class="sub"><a href="index.php">← Voltar aos produtos</a></p>
<?= flash() ?>

<form method="post" enctype="multipart/form-data" class="card">
  <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

  <label>Nome do produto *</label>
  <input type="text" name="name" value="<?= $v('name') ?>" required>

  <label>Descrição</label>
  <textarea name="description" rows="4" placeholder="Tecido, caimento, detalhes..."><?= $v('description') ?></textarea>

  <div class="row">
    <div>
      <label>Preço</label>
      <input type="text" name="price" value="<?= $p ? number_format((float)$p['price'],2,',','') : '' ?>" placeholder="89,90">
      <div class="help">Use vírgula ou ponto. Ex.: 89,90</div>
    </div>
    <div>
      <label>Tamanhos</label>
      <input type="text" name="sizes" value="<?= $v('sizes') ?>" placeholder="P,M,G,GG">
      <div class="help">Separe por vírgula.</div>
    </div>
    <div>
      <label>Ordem</label>
      <input type="number" name="sort_order" value="<?= $v('sort_order','0') ?>">
      <div class="help">Menor aparece primeiro.</div>
    </div>
  </div>

  <label>Foto do produto</label>
  <input type="file" name="image" accept="image/*">
  <div class="help">JPG, PNG, WEBP ou SVG (até 8 MB). <?= $editing ? 'Deixe vazio para manter a atual.' : '' ?></div>
  <?php if ($p && $p['image']): ?>
    <img class="thumb-prev" src="../<?= e(productImage($p)) ?>" alt="atual">
  <?php endif; ?>

  <label style="margin-top:18px"><input type="checkbox" name="active" value="1" <?= (!$editing || (int)$p['active']) ? 'checked' : '' ?>> Mostrar no site</label>

  <div style="margin-top:24px;display:flex;gap:10px">
    <button class="btn" type="submit"><?= $editing ? 'Salvar alterações' : 'Adicionar produto' ?></button>
    <a class="btn ghost" href="index.php">Cancelar</a>
  </div>
</form>
<?php adminFooter(); ?>
