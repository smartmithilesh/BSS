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
