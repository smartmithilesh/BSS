<div class="page-title"><div class="title_left"><h3><i class="fa fa-user"></i> Edit Profile</h3></div></div>
<div class="clearfix"></div>

<div class="row"><div class="col-md-6 col-md-offset-3"><div class="x_panel"><div class="x_content">
<form method="POST" action="<?= BASE_URL ?>?controller=user&action=saveProfile" enctype="multipart/form-data">
  <?php $profileImage=!empty($user['profile_image']) ? BASE_URL.ltrim($user['profile_image'],'/') : ''; ?>
  <div class="profile-edit-preview">
    <?php if($profileImage): ?>
      <img src="<?= $profileImage ?>" alt="Profile Image">
    <?php else: ?>
      <span><i class="fa fa-user"></i></span>
    <?php endif; ?>
  </div>
  <div class="form-group"><label>Profile Image</label>
    <input type="file" name="profile_image" class="form-control" accept="image/*">
    <p class="help-block">JPG, PNG, GIF or WebP. Maximum 2 MB.</p>
  </div>
  <div class="form-group"><label>Name *</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']??'') ?>" required></div>
  <div class="form-group"><label>Email *</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']??'') ?>" required></div>
  <div class="form-group"><label>New Password</label>
    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password"></div>
  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Profile</button>
  <a href="<?= BASE_URL ?>?controller=dashboard&action=index" class="btn btn-default">Cancel</a>
</form>
</div></div></div></div>
