<?php
/** Cabeçalho/rodapé compartilhado do painel. */
function adminHeader(string $title, string $active): void
{
    ?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title><?= e($title) ?> — Painel CRRS</title>
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="topbar">
  <div class="logo">CRRS</div>
  <nav>
    <a href="index.php" class="<?= $active==='produtos'?'active':'' ?>">Produtos</a>
    <a href="settings.php" class="<?= $active==='settings'?'active':'' ?>">Configurações</a>
    <a href="../index.php" target="_blank">Ver site ↗</a>
    <a href="logout.php">Sair</a>
  </nav>
</div>
<div class="wrap">
    <?php
}

function adminFooter(): void
{
    echo "</div></body></html>";
}

/** Flash via query string. */
function flash(): string
{
    $ok  = $_GET['ok']  ?? '';
    $err = $_GET['err'] ?? '';
    if ($ok)  return '<div class="flash ok">' . e($ok) . '</div>';
    if ($err) return '<div class="flash err">' . e($err) . '</div>';
    return '';
}
