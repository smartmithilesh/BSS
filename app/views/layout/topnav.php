<div class="top_nav">
  <div class="nav_menu">
    <nav>
      <div class="nav toggle"><a id="menu_toggle"><i class="fa fa-bars"></i></a></div>
      <ul class="nav navbar-nav navbar-right">
        <?php $topProfileImage=!empty($_SESSION['user']['profile_image']) ? BASE_URL.ltrim($_SESSION['user']['profile_image'],'/') : ''; ?>
        <li><a class="user-profile dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
          <?php if($topProfileImage): ?><img src="<?= $topProfileImage ?>" alt="Profile"><?php else: ?><i class="fa fa-user"></i><?php endif; ?>
          <?= htmlspecialchars($_SESSION['user']['name']??'') ?>
          <span class="fa fa-angle-down"></span>
        </a>
        <ul class="dropdown-menu dropdown-usermenu pull-right">
          <li><a href="<?= BASE_URL ?>?controller=user&action=profile"><i class="fa fa-user"></i> Edit Profile</a></li>
          <li><a href="<?= BASE_URL ?>?controller=auth&action=logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
        </ul></li>
      </ul>
    </nav>
  </div>
</div>
