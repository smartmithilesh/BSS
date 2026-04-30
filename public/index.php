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
$controller = $_GET['controller'] ?? 'dashboard';
$action     = $_GET['action']     ?? 'index';

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

$obj->$action();
