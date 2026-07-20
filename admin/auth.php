<?php
/** CRRS admin — sessão e autenticação. */
require_once dirname(__DIR__) . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function currentAdmin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function requireLogin(): void
{
    if (!currentAdmin()) {
        redirect('login.php');
    }
}

function attemptLogin(string $user, string $pass): bool
{
    $stmt = db()->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$user]);
    $row = $stmt->fetch();
    if ($row && password_verify($pass, $row['password'])) {
        $_SESSION['admin'] = ['id' => $row['id'], 'username' => $row['username']];
        return true;
    }
    return false;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function checkCsrf(): void
{
    $t = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(403);
        exit('Sessão expirada. Recarregue a página e tente de novo.');
    }
}

/** Salva imagem enviada; retorna nome do arquivo ou null. */
function handleUpload(string $field): ?string
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $f    = $_FILES[$field];
    $ext  = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $allow = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'avif'];
    if (!in_array($ext, $allow, true)) {
        return null;
    }
    if ($f['size'] > 8 * 1024 * 1024) { // 8 MB
        return null;
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }
    $name = 'p_' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (move_uploaded_file($f['tmp_name'], UPLOAD_DIR . '/' . $name)) {
        return $name;
    }
    return null;
}
