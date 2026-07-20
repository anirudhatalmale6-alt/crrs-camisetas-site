<?php
require_once __DIR__ . '/auth.php';
if (currentAdmin()) {
    redirect('index.php');
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    if (attemptLogin(trim($_POST['username'] ?? ''), $_POST['password'] ?? '')) {
        redirect('index.php');
    }
    $err = 'Usuário ou senha incorretos.';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Painel CRRS — Entrar</title>
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="login-box">
  <h1>CRRS</h1>
  <p class="sub" style="text-align:center">Painel administrativo</p>
  <?php if ($err): ?><div class="flash err"><?= e($err) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <label>Usuário</label>
    <input type="text" name="username" autofocus required>
    <label>Senha</label>
    <input type="password" name="password" required>
    <button class="btn" style="width:100%;margin-top:20px;justify-content:center" type="submit">Entrar</button>
  </form>
</div>
</body>
</html>
