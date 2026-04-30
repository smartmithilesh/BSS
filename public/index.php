<?php
session_start();

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/AppSettings.php';
require_once __DIR__ . '/../app/core/MigrationRunner.php';
require_once __DIR__ . '/../app/core/Autoload.php';
if(class_exists('AppSettings')) date_default_timezone_set(AppSettings::get('timezone',APP_TIMEZONE));

// Route
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = rtrim(parse_url(BASE_URL, PHP_URL_PATH) ?: '/', '/');
if ($basePath && $basePath !== '/' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$path = trim($path, '/');
$segments = $path === '' ? [] : explode('/', $path);

$controller = $_GET['controller'] ?? ($segments[0] ?? 'dashboard');
$action     = $_GET['action']     ?? ($segments[1] ?? 'index');

// Sanitise
$controller = preg_replace('/[^a-z]/i', '', $controller);
$action     = preg_replace('/[^a-z]/i', '', $action);

if(!file_exists(__DIR__.'/../app/config/installed.php') && strtolower($controller)!=='setup') {
    $controller='setup';
    $action='index';
}

// Map controller slug → class name
$map = [
    'dashboard'      => 'DashboardController',
    'auth'           => 'AuthController',
    'setup'          => 'SetupController',
    'season'         => 'SeasonController',
    'company'        => 'CompanyController',
    'publication'    => 'PublicationController',
    'class'          => 'ClassController',
    'school'         => 'SchoolController',
    'department'     => 'DepartmentController',
    'role'           => 'RoleController',
    'user'           => 'UserController',
    'sitesetting'    => 'SiteSettingController',
    'migration'      => 'MigrationController',
    'book'           => 'BookController',
    'purchase'       => 'PurchaseController',
    'schoolsale'     => 'SchoolsaleController',
    'companypayment' => 'CompanyPaymentController',
    'companyreturn'  => 'CompanyReturnController',
    'stock'          => 'StockController',
    'report'         => 'ReportController',
];

$className = $map[strtolower($controller)] ?? null;

if (!$className) {
    http_response_code(404);
    die('Page not found.');
}

$obj = new $className();
if (!method_exists($obj, $action)) {
    http_response_code(404);
    die('Action not found.');
}

ob_start('rewrite_legacy_urls');
$obj->$action();
