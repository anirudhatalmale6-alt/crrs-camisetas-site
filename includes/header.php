<?php
require_once __DIR__ . '/functions.php';
$brandName   = setting('brand_name', 'CRRS');
$accent      = setting('accent_color', '#111111');
$pageTitle   = $pageTitle ?? $brandName;
$metaDesc    = $metaDesc ?? setting('brand_tagline', '');
$logoFile    = setting('logo', '');
$hasLogo     = $logoFile && file_exists(CRRS_ROOT . '/uploads/' . $logoFile);
$active      = $active ?? '';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($metaDesc) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($metaDesc) ?>">
<meta property="og:type" content="website">
<link rel="preconnect" href="https://wa.me">
<link rel="stylesheet" href="assets/css/style.css">
<style>:root{--accent:<?= e($accent) ?>}</style>
</head>
<body>
<header class="site-header">
  <div class="container nav">
    <a class="brand" href="index.php">
      <?php if ($hasLogo): ?>
        <img src="uploads/<?= e($logoFile) ?>" alt="<?= e($brandName) ?>">
      <?php else: ?>
        <?= e($brandName) ?>
      <?php endif; ?>
    </a>
    <button class="nav-toggle" aria-label="Menu" onclick="document.getElementById('navlinks').classList.toggle('open')">
      <span></span><span></span><span></span>
    </button>
    <nav class="nav-links" id="navlinks">
      <a href="index.php"<?= $active==='home'?' style="opacity:.5"':'' ?>>Início</a>
      <a href="catalogo.php"<?= $active==='catalogo'?' style="opacity:.5"':'' ?>>Catálogo</a>
      <a href="sobre.php"<?= $active==='sobre'?' style="opacity:.5"':'' ?>>Sobre</a>
      <a href="contacto.php"<?= $active==='contacto'?' style="opacity:.5"':'' ?>>Contacto</a>
    </nav>
  </div>
</header>
<main>
