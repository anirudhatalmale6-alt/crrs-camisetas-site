<?php
/** CRRS — funções utilitárias compartilhadas. */

require_once __DIR__ . '/db.php';

function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/** Preço formatado, ex.: R$ 89,90 */
function money(float $v): string
{
    return setting('currency', 'R$') . ' ' . number_format($v, 2, ',', '.');
}

/** URL da imagem do produto (com fallback para placeholder). */
function productImage(array $p): string
{
    $img = trim($p['image'] ?? '');
    if ($img === '') {
        return 'assets/img/placeholder.svg';
    }
    // Imagens de demonstração ficam em assets/img; uploads reais em /uploads.
    if (file_exists(CRRS_ROOT . '/assets/img/' . $img)) {
        return 'assets/img/' . $img;
    }
    return UPLOAD_URL . '/' . $img;
}

/** Lista de tamanhos como array. */
function sizesArray(string $sizes): array
{
    return array_values(array_filter(array_map('trim', explode(',', $sizes))));
}

/**
 * Monta o link de compra (WhatsApp ou Instagram) com mensagem pré-preenchida.
 * $size e $obs são preenchidos no cliente (JS); aqui geramos a base.
 */
function buyLinkBase(array $p): string
{
    $channel = setting('buy_channel', 'whatsapp');
    if ($channel === 'instagram') {
        $handle = ltrim(setting('instagram', ''), '@');
        return 'https://ig.me/m/' . rawurlencode($handle);
    }
    $number = preg_replace('/\D+/', '', setting('whatsapp', ''));
    return 'https://wa.me/' . $number;
}

/** Produtos ativos, ordenados. */
function activeProducts(): array
{
    return db()->query(
        'SELECT * FROM products WHERE active = 1 ORDER BY sort_order ASC, id ASC'
    )->fetchAll();
}

function getProduct(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Redireciona e encerra. */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}
