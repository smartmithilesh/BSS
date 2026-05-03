<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle??AppSettings::get('shop_name',BASE_NAME)) ?> | <?= htmlspecialchars(AppSettings::get('shop_name',BASE_NAME)) ?></title>
<link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/nprogress.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/custom.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/ui-fixes.css" rel="stylesheet">
<script src="<?= BASE_URL ?>assets/js/jquery.min.js"></script>
<style>
.badge-success{background:#27ae60}.badge-danger{background:#e74c3c}.badge-warning{background:#f39c12;color:#fff}
.card-summary{border-left:4px solid}.card-summary.blue{border-color:#3498db}.card-summary.green{border-color:#27ae60}
.card-summary.orange{border-color:#e67e22}.card-summary.red{border-color:#e74c3c}
table.dataTable thead th{background:#2c3e50;color:#fff}
.btn-pdf{background:#c0392b;color:#fff;border:none}.btn-pdf:hover{background:#a93226;color:#fff}
.site-logo-img{height:30px;max-width:38px;object-fit:contain;margin-right:6px;vertical-align:middle}
.app-credit-footer{margin-top:24px;padding:12px 0 4px;color:#7f8c8d;font-size:12px;line-height:1.5;text-align:right;border-top:1px solid #e6ebf1}
.app-credit-footer a{color:#526a7a;text-decoration:none}
.app-credit-footer a:hover{color:#2c3e50;text-decoration:none}
@media(max-width:767px){.app-credit-footer{text-align:center;font-size:11px}.app-credit-footer .credit-separator{display:none}.app-credit-footer .credit-item{display:block}}
.app-popup-backdrop{position:fixed;inset:0;background:rgba(31,45,61,.48);z-index:10000;display:none;align-items:center;justify-content:center;padding:18px}
.app-popup{width:100%;max-width:420px;background:#fff;border-radius:6px;box-shadow:0 18px 50px rgba(0,0,0,.28);overflow:hidden;transform:translateY(8px);opacity:0;transition:all .16s ease}
.app-popup-backdrop.is-visible .app-popup{transform:translateY(0);opacity:1}
.app-popup-head{display:flex;align-items:center;gap:10px;padding:16px 18px 10px}
.app-popup-icon{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;flex:0 0 34px}
.app-popup-icon.success{background:#27ae60}.app-popup-icon.error{background:#e74c3c}.app-popup-icon.warning{background:#f39c12}.app-popup-icon.info{background:#3498db}
.app-popup-title{font-size:17px;font-weight:700;color:#2c3e50;margin:0}
.app-popup-message{padding:0 18px 16px 62px;color:#4a5568;font-size:14px;line-height:1.45;word-wrap:break-word}
.app-popup-actions{display:flex;justify-content:flex-end;gap:8px;padding:12px 18px;background:#f7f9fb;border-top:1px solid #e6ebf1}
.app-popup-actions .btn{min-width:78px}
@media(max-width:480px){.app-popup-message{padding-left:18px}.app-popup-actions{justify-content:stretch}.app-popup-actions .btn{flex:1}}
</style>
</head>
<body class="nav-md">
<div class="container body">
<div class="main_container">

<?php include __DIR__.'/leftnav.php'; ?>
<?php include __DIR__.'/topnav.php'; ?>

<div class="right_col" role="main">
<div class="">

<?php // Flash messages
$flashPopup = null;
if(!empty($_SESSION['flash'])):
    $f=$_SESSION['flash']; unset($_SESSION['flash']);
    $flashPopup = ['type'=>$f['type']==='success'?'success':'error','message'=>$f['msg']];
?>
<?php endif; ?>
