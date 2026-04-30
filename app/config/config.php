<?php

/* ================================
   PROJECT CONFIGURATION
================================ */
$localConfig = __DIR__ . '/config.local.php';
$local = file_exists($localConfig) ? require $localConfig : [];

define('PROJECT_FOLDER', $local['project_folder'] ?? 'bbd');
define('BASE_NAME', $local['base_name'] ?? 'Bharat Book Depot');

// BASE URL – adjust if your folder name differs
define('BASE_URL', $local['base_url'] ?? 'http://bharatbookdepot.local/');

/* ================================
   DATABASE
================================ */
define('DB_HOST', $local['db_host'] ?? 'localhost');
define('DB_NAME', $local['db_name'] ?? 'bharat_book_depot');
define('DB_USER', $local['db_user'] ?? 'root');
define('DB_PASS', $local['db_pass'] ?? 'root123');
define('APP_TIMEZONE', $local['timezone'] ?? 'Asia/Kolkata');
date_default_timezone_set(APP_TIMEZONE);

define('IMAGE_PATH', BASE_URL . 'assets/images/');

function app_url($controller = 'dashboard', $action = 'index', $params = []) {
    $controller = trim((string)$controller, '/');
    $action = trim((string)$action, '/');
    $path = rawurlencode($controller) . '/' . rawurlencode($action);
    $query = http_build_query(array_filter($params, static function($value) {
        return $value !== null && $value !== '';
    }));

    return rtrim(BASE_URL, '/') . '/' . $path . ($query ? '?' . $query : '');
}

function url($controller = 'dashboard', $action = 'index', $params = []) {
    return app_url($controller, $action, $params);
}

function pretty_url_from_legacy($path) {
    if (!is_string($path) || $path === '') return $path;

    $base = rtrim(BASE_URL, '/') . '/';
    $relative = $path;
    if (strpos($relative, BASE_URL) === 0) {
        $relative = substr($relative, strlen(BASE_URL));
    } elseif (strpos($relative, $base) === 0) {
        $relative = substr($relative, strlen($base));
    }

    if (strpos($relative, '?') === 0) {
        parse_str(substr($relative, 1), $params);
    } else {
        $parts = parse_url($relative);
        if (empty($parts['query'])) return $path;
        parse_str($parts['query'], $params);
    }

    if (empty($params['controller'])) return $path;

    $controller = $params['controller'];
    $action = $params['action'] ?? 'index';
    unset($params['controller'], $params['action']);

    return app_url($controller, $action, $params);
}

function rewrite_legacy_urls($html) {
    if (!is_string($html) || strpos($html, 'controller=') === false) return $html;

    $html = preg_replace_callback(
        '/((?:href|action)=["\'])([^"\']*\\?controller=[^"\']+)(["\'])/i',
        static function($matches) {
            return $matches[1] . htmlspecialchars(pretty_url_from_legacy(html_entity_decode($matches[2], ENT_QUOTES)), ENT_QUOTES) . $matches[3];
        },
        $html
    );

    $html = preg_replace_callback(
        '/BASE_URL\\s*\\+\\s*([\'"])(\\?controller=[^\'"]+)\\1/',
        static function($matches) {
            return $matches[1] . pretty_url_from_legacy($matches[2]) . $matches[1];
        },
        $html
    );

    return $html;
}
