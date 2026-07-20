<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/partials.php';
requireLogin();

// Ações rápidas: excluir / alternar ativo.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'delete' && $id) {
        $p = getProduct($id);
        if ($p && $p['image'] && !file_exists(CRRS_ROOT . '/assets/img/' . $p['image'])) {
            @unlink(UPLOAD_DIR . '/' . $p['image']); // remove upload real, não os demos
        }
        db()->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        redirect('index.php?ok=' . rawurlencode('Produto excluído.'));
    }
    if ($action === 'toggle' && $id) {
        db()->prepare('UPDATE products SET active = 1 - active WHERE id = ?')->execute([$id]);
        redirect('index.php?ok=' . rawurlencode('Status atualizado.'));
    }
}

$products = db()->query('SELECT * FROM products ORDER BY sort_order ASC, id ASC')->fetchAll();
adminHeader('Produtos', 'produtos');
?>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:8px">
  <div>
    <h1>Produtos</h1>
    <p class="sub" style="margin:0"><?= count($products) ?> cadastrado<?= count($products)===1?'':'s' ?></p>
  </div>
  <a class="btn" href="product-form.php">+ Adicionar produto</a>
</div>
<?= flash() ?>

<div class="card">
<?php if (!$products): ?>
  <div class="empty">Nenhum produto ainda. Clique em “Adicionar produto” para começar.</div>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Foto</th><th>Nome</th><th class="hide-sm">Preço</th>
        <th class="hide-sm">Tamanhos</th><th>Status</th><th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
      <tr>
        <td><img src="../<?= e(productImage($p)) ?>" alt=""></td>
        <td><strong><?= e($p['name']) ?></strong></td>
        <td class="hide-sm"><?= money((float)$p['price']) ?></td>
        <td class="hide-sm"><?= e($p['sizes']) ?></td>
        <td>
          <?php if ((int)$p['active']): ?>
            <span class="badge on">Ativo</span>
          <?php else: ?>
            <span class="badge off">Oculto</span>
          <?php endif; ?>
        </td>
        <td>
          <div class="actions">
            <a class="btn sm ghost" href="product-form.php?id=<?= (int)$p['id'] ?>">Editar</a>
            <form method="post" style="display:inline">
              <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <input type="hidden" name="action" value="toggle">
              <button class="btn sm ghost" type="submit"><?= (int)$p['active'] ? 'Ocultar' : 'Mostrar' ?></button>
            </form>
            <form method="post" style="display:inline" onsubmit="return confirm('Excluir este produto?')">
              <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button class="btn sm danger" type="submit">Excluir</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
</div>
<?php adminFooter(); ?>
