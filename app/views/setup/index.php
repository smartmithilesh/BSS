<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Setup | <?= htmlspecialchars(BASE_NAME) ?></title>
<link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<style>
body{background:#2c3e50}.setup-page{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:25px}.setup-box{background:#fff;border-radius:6px;padding:30px;width:760px;box-shadow:0 10px 40px rgba(0,0,0,.3)}h1{margin-top:0;color:#2c3e50}
</style>
</head>
<body><div class="setup-page"><div class="setup-box">
<h1><i class="fa fa-cogs"></i> First-Time Setup</h1>
<?php if(!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="POST" action="<?= BASE_URL ?>?controller=setup&action=save">
  <h4>Database</h4>
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Host *</label><input name="db_host" class="form-control" value="localhost" required></div></div>
    <div class="col-md-6"><div class="form-group"><label>Database Name *</label><input name="db_name" class="form-control" value="bharat_book_depot" required></div></div>
  </div>
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Username *</label><input name="db_user" class="form-control" value="root" required></div></div>
    <div class="col-md-6"><div class="form-group"><label>Password</label><input type="password" name="db_pass" class="form-control"></div></div>
  </div>
  <h4>Application</h4>
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>App Name *</label><input name="app_name" class="form-control" value="Bharat Book Depot" required></div></div>
    <div class="col-md-6"><div class="form-group"><label>Timezone</label><input name="timezone" class="form-control" value="Asia/Kolkata"></div></div>
  </div>
  <h4>Default Super Admin</h4>
  <div class="row">
    <div class="col-md-4"><div class="form-group"><label>Name *</label><input name="admin_name" class="form-control" value="Admin" required></div></div>
    <div class="col-md-4"><div class="form-group"><label>Email *</label><input type="email" name="admin_email" class="form-control" required></div></div>
    <div class="col-md-4"><div class="form-group"><label>Password *</label><input type="password" name="admin_password" class="form-control" required></div></div>
  </div>
  <button class="btn btn-success btn-lg"><i class="fa fa-check"></i> Install</button>
</form>
</div></div></body></html>
