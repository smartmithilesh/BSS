<?php
$c=$_GET['controller']??'dashboard';
$a=$_GET['action']??'index';
$inArr=fn($arr)=>in_array($c,$arr);
$userRole=strtolower(trim($_SESSION['user']['role']??''));
$userDepartment=strtolower(trim($_SESSION['user']['department_name']??''));
$isSuperAdmin=$userRole==='superadmin' || $userDepartment==='super admin';
$isItDepartment=$userRole==='it' || $userDepartment==='it';
?>
<div class="col-md-3 left_col">
<div class="left_col scroll-view">
<div class="navbar nav_title" style="border:0">
  <a href="<?= BASE_URL ?>?controller=dashboard&action=index" class="site_title">
    <?php if(AppSettings::logoUrl()): ?><img src="<?= AppSettings::logoUrl() ?>" class="site-logo-img" alt="Logo"><?php else: ?><i class="fa fa-book"></i><?php endif; ?>
    <span><?= htmlspecialchars(AppSettings::get('shop_name',BASE_NAME)) ?></span>
  </a>
</div>
<div class="clearfix"></div>
<?php $leftProfileImage=!empty($_SESSION['user']['profile_image']) ? BASE_URL.ltrim($_SESSION['user']['profile_image'],'/') : ''; ?>
<div class="profile clearfix <?= $leftProfileImage?'has-image':'' ?>">
  <?php if($leftProfileImage): ?><img src="<?= $leftProfileImage ?>" class="profile-sidebar-img" alt="Profile"><?php endif; ?>
  <div class="profile_info">
    <span>Welcome,</span>
    <h2><?= htmlspecialchars($_SESSION['user']['name']??'User') ?></h2>
  </div>
</div>

<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

<!-- GENERAL -->
<div class="menu_section">
<h3>General</h3>
<ul class="nav side-menu">
  <li class="<?= $c==='dashboard'?'active':'' ?>">
    <a href="<?= BASE_URL ?>?controller=dashboard&action=index"><i class="fa fa-home"></i> Dashboard</a>
  </li>

  <?php $mOpen=$inArr(['season','company','publication','class','school','department','role','user','siteSetting','migration','book']); ?>
  <li class="<?= $mOpen?'active':'' ?>">
    <a href="javascript:void(0)"><i class="fa fa-cogs"></i> Masters <span class="fa fa-chevron-down"></span></a>
    <ul class="nav child_menu" style="<?= $mOpen?'display:block':'' ?>">
      <li class="<?= $c==='season'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=season&action=index">Seasons</a></li>
      <li class="<?= $c==='company'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=company&action=index">Companies</a></li>
      <li class="<?= $c==='publication'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=publication&action=index">Publications</a></li>
      <li class="<?= $c==='class'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=class&action=index">Classes</a></li>
      <li class="<?= $c==='school'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=school&action=index">Schools</a></li>
      <li class="<?= $c==='department'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=department&action=index">Departments</a></li>
      <li class="<?= $c==='role'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=role&action=index">Roles</a></li>
      <li class="<?= $c==='user'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=user&action=index">Users</a></li>
      <?php if($isSuperAdmin): ?>
      <li class="<?= $c==='siteSetting'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=siteSetting&action=index">Site Settings</a></li>
      <?php endif; ?>
      <?php if($isItDepartment): ?>
      <li class="<?= $c==='migration'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=migration&action=index">Database Updates</a></li>
      <?php endif; ?>
      <li class="<?= $c==='book'?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=book&action=index">Books</a></li>
    </ul>
  </li>
</ul>
</div>

<!-- PURCHASES -->
<div class="menu_section">
<h3>Purchases</h3>
<ul class="nav side-menu">
  <?php $pOpen=$inArr(['purchase']); ?>
  <li class="<?= $pOpen?'active':'' ?>">
    <a href="javascript:void(0)"><i class="fa fa-shopping-cart"></i> Purchases <span class="fa fa-chevron-down"></span></a>
    <ul class="nav child_menu" style="<?= $pOpen?'display:block':'' ?>">
      <li class="<?= ($c==='purchase'&&$a==='create')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=purchase&action=create">New Purchase</a></li>
      <li class="<?= ($c==='purchase'&&$a==='index')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=purchase&action=index">Purchase List</a></li>
    </ul>
  </li>
  <?php $rOpen=$inArr(['companyReturn']); ?>
  <li class="<?= $rOpen?'active':'' ?>">
    <a href="javascript:void(0)"><i class="fa fa-undo"></i> Returns <span class="fa fa-chevron-down"></span></a>
    <ul class="nav child_menu" style="<?= $rOpen?'display:block':'' ?>">
      <li class="<?= ($c==='companyReturn'&&$a==='create')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=companyReturn&action=create">New Return</a></li>
      <li class="<?= ($c==='companyReturn'&&$a==='index')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=companyReturn&action=index">Return List</a></li>
    </ul>
  </li>
  <?php $cpOpen=$inArr(['companyPayment']); ?>
  <li class="<?= $cpOpen?'active':'' ?>">
    <a href="javascript:void(0)"><i class="fa fa-money"></i> Payments <span class="fa fa-chevron-down"></span></a>
    <ul class="nav child_menu" style="<?= $cpOpen?'display:block':'' ?>">
      <li class="<?= ($c==='companyPayment'&&$a==='create')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=companyPayment&action=create">Pay Company</a></li>
      <li class="<?= ($c==='companyPayment'&&$a==='index')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=companyPayment&action=index">Payment List</a></li>
      <li class="<?= ($c==='companyPayment'&&$a==='outstanding')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=companyPayment&action=outstanding">Outstanding</a></li>
    </ul>
  </li>
</ul>
</div>

<!-- SALES -->
<div class="menu_section">
<h3>Sales</h3>
<ul class="nav side-menu">
  <?php $sOpen=$inArr(['schoolsale']); ?>
  <li class="<?= $sOpen?'active':'' ?>">
    <a href="javascript:void(0)"><i class="fa fa-line-chart"></i> School Sales <span class="fa fa-chevron-down"></span></a>
    <ul class="nav child_menu" style="<?= $sOpen?'display:block':'' ?>">
      <li class="<?= ($c==='schoolsale'&&$a==='create')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=schoolsale&action=create">New Sale</a></li>
      <li class="<?= ($c==='schoolsale'&&$a==='index')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=schoolsale&action=index">Sales List</a></li>
      <li class="<?= ($c==='schoolsale'&&$a==='receivePayment')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=schoolsale&action=receivePayment">Receive Payment</a></li>
      <li class="<?= ($c==='schoolsale'&&$a==='payments')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=schoolsale&action=payments">Payment Receive List</a></li>
      <li class="<?= ($c==='schoolsale'&&$a==='outstanding')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=schoolsale&action=outstanding">Outstanding Report</a></li>
    </ul>
  </li>
  <li class="<?= $c==='stock'?'active':'' ?>">
    <a href="<?= BASE_URL ?>?controller=stock&action=index"><i class="fa fa-cubes"></i> Stock Report</a>
  </li>
  <?php $reportOpen=$inArr(['report']); ?>
  <li class="<?= $reportOpen?'active':'' ?>">
    <a href="javascript:void(0)"><i class="fa fa-bar-chart"></i> Reports <span class="fa fa-chevron-down"></span></a>
    <ul class="nav child_menu" style="<?= $reportOpen?'display:block':'' ?>">
      <li class="<?= ($c==='report'&&$a==='profitLoss')?'current-page':'' ?>"><a href="<?= BASE_URL ?>?controller=report&action=profitLoss">Profit & Loss</a></li>
    </ul>
  </li>
</ul>
</div>

</div><!-- #sidebar-menu -->
</div><!-- scroll-view -->
</div><!-- left_col -->
