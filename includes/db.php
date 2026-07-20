<?php
/**
 * CRRS — conexão com o banco de dados (SQLite) e inicialização do schema.
 * SQLite escolhido para ser 100% portátil: roda em qualquer VPS com PHP,
 * sem precisar configurar MySQL/servidor de banco.
 */

define('CRRS_ROOT', dirname(__DIR__));
define('DB_PATH', CRRS_ROOT . '/data/crrs.db');
define('UPLOAD_DIR', CRRS_ROOT . '/uploads');
define('UPLOAD_URL', 'uploads');

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $firstRun = !file_exists(DB_PATH);
    if (!is_dir(dirname(DB_PATH))) {
        mkdir(dirname(DB_PATH), 0775, true);
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    initSchema($pdo, $firstRun);
    return $pdo;
}

function initSchema(PDO $pdo, bool $firstRun): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            name        TEXT    NOT NULL,
            description TEXT    DEFAULT '',
            price       REAL    NOT NULL DEFAULT 0,
            sizes       TEXT    DEFAULT '',
            image       TEXT    DEFAULT '',
            active      INTEGER NOT NULL DEFAULT 1,
            sort_order  INTEGER NOT NULL DEFAULT 0,
            created_at  TEXT    DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            key   TEXT PRIMARY KEY,
            value TEXT
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id       INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL
        )
    ");

    if ($firstRun) {
        seedDefaults($pdo);
    }
}

function seedDefaults(PDO $pdo): void
{
    $defaults = [
        'brand_name'      => 'CRRS',
        'brand_tagline'   => 'Coleção de camisetas — edição limitada',
        'hero_title'      => 'CRRS',
        'hero_subtitle'   => 'Streetwear minimalista. Peças pensadas para o dia a dia.',
        'accent_color'    => '#111111',
        'whatsapp'        => '5511999999999',
        'instagram'       => 'crrs',
        'contact_email'   => '',
        'contact_text'    => 'Fale com a gente pelo WhatsApp ou Instagram. Respondemos rápido!',
        'about_text'      => "A CRRS nasceu da vontade de criar peças simples, bem-feitas e com atitude.\n\nCada camiseta é escolhida com cuidado — tecido de qualidade, caimento certo e um visual clean que combina com tudo. Menos é mais.",
        'buy_channel'     => 'whatsapp', // whatsapp | instagram
        'currency'        => 'R$',
    ];

    $stmt = $pdo->prepare('INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)');
    foreach ($defaults as $k => $v) {
        $stmt->execute([$k, $v]);
    }

    // Admin padrão — o cliente deve trocar a senha depois.
    $pdo->prepare('INSERT OR IGNORE INTO admins (username, password) VALUES (?, ?)')
        ->execute(['admin', password_hash('crrs2026', PASSWORD_DEFAULT)]);

    // Produtos de demonstração (placeholders).
    $demo = [
        ['Camiseta Essential Preta', 'Algodão premium 100%, corte regular. O básico que nunca falha.', 89.90, 'P,M,G,GG', 'tee-black.svg', 1],
        ['Camiseta Off-White',       'Tom off-white suave, gola reforçada e caimento perfeito.',        89.90, 'P,M,G,GG', 'tee-white.svg', 2],
        ['Camiseta Oversized Areia', 'Modelagem oversized, toque macio. Conforto o dia todo.',          109.90, 'M,G,GG',   'tee-sand.svg',  3],
        ['Camiseta Verde Militar',   'Verde militar com estampa discreta no peito.',                    99.90, 'P,M,G',    'tee-green.svg', 4],
    ];
    $stmt = $pdo->prepare('INSERT INTO products (name, description, price, sizes, image, sort_order) VALUES (?,?,?,?,?,?)');
    foreach ($demo as $d) {
        $stmt->execute($d);
    }
}

/** Lê uma configuração. */
function setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db()->query('SELECT key, value FROM settings') as $row) {
            $cache[$row['key']] = $row['value'];
        }
    }
    return $cache[$key] ?? $default;
}
