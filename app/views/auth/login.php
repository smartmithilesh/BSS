<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | <?= htmlspecialchars(AppSettings::get('shop_name',BASE_NAME)) ?></title>
<link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/custom.css" rel="stylesheet">
<style>
body{background:#2c3e50}
.login-page{min-height:100vh;display:flex;align-items:center;justify-content:center}
.login-box{background:#fff;border-radius:6px;padding:40px;width:380px;box-shadow:0 10px 40px rgba(0,0,0,.3)}
.login-logo{text-align:center;margin-bottom:30px;color:#2c3e50}
.login-logo h1{font-size:28px;font-weight:700;margin:0}
.login-logo p{color:#7f8c8d;margin:0}
.btn-login{background:#2c3e50;color:#fff;width:100%;padding:10px;font-size:16px}
.btn-login:hover{background:#1a252f;color:#fff}
</style>
</head>
<body>
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <?php if(AppSettings::logoUrl()): ?><img src="<?= AppSettings::logoUrl() ?>" alt="Logo" style="max-height:70px;max-width:140px;margin-bottom:10px"><?php else: ?><i class="fa fa-book fa-3x" style="color:#2c3e50"></i><?php endif; ?>
      <h1><?= htmlspecialchars(AppSettings::get('shop_name',BASE_NAME)) ?></h1>
      <p><?= htmlspecialchars(AppSettings::get('tagline','Book Depot Management System')) ?></p>
    </div>
    <?php if(!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Email</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
          <input type="email" name="email" class="form-control" placeholder="admin@bharatbookdepot.com" required>
        </div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-lock"></i></span>
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
      </div>
      <button type="submit" class="btn btn-login">Sign In</button>
    </form>
    <p style="text-align:center;margin-top:15px;color:#999;font-size:12px">Default: admin@bharatbookdepot.com / password</p>
  </div>
</div>
<script src="<?= BASE_URL ?>assets/js/jquery.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
