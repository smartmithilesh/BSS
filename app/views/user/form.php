<div class="page-title"><div class="title_left"><h3><?= htmlspecialchars($pageTitle) ?></h3></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-8"><div class="x_panel"><div class="x_content">
<form method="POST" action="<?= BASE_URL ?>?controller=user&action=save">
  <input type="hidden" name="id" value="<?= $user['id']??'' ?>">
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Name *</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']??'') ?>" required></div></div>
    <div class="col-md-6"><div class="form-group"><label>Email *</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']??'') ?>" required></div></div>
  </div>
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Department</label>
      <select name="department_id" class="form-control">
        <option value="">-- No Department --</option>
        <?php foreach($departments as $d): ?><option value="<?= $d['id'] ?>" <?= ($user['department_id']??'')==$d['id']?'selected':'' ?>><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?>
      </select>
    </div></div>
    <div class="col-md-6"><div class="form-group"><label>Role *</label>
      <select name="role_id" class="form-control" required>
        <?php foreach($roles as $r): ?>
        <option value="<?= $r['id'] ?>" <?= (($user['role_id']??'')==$r['id'] || (empty($user)&&$r['slug']==='staff'))?'selected':'' ?>>
          <?= htmlspecialchars($r['name']) ?><?= !empty($r['department_name'])?' - '.htmlspecialchars($r['department_name']):'' ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div></div>
  </div>
  <div class="form-group"><label>Password <?= empty($user)?'*':'(leave blank to keep current)' ?></label>
    <input type="password" name="password" class="form-control" <?= empty($user)?'required':'' ?>></div>
  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
  <a href="<?= BASE_URL ?>?controller=user&action=index" class="btn btn-default">Cancel</a>
</form>
</div></div></div></div>
