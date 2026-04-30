<div class="page-title"><div class="title_left"><h3><?= htmlspecialchars($pageTitle) ?></h3></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-8"><div class="x_panel"><div class="x_content">
<form method="POST" action="<?= BASE_URL ?>?controller=role&action=save">
  <input type="hidden" name="id" value="<?= $role['id']??'' ?>">
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Role Name *</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($role['name']??'') ?>" required></div></div>
    <div class="col-md-6"><div class="form-group"><label>Slug</label>
      <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($role['slug']??'') ?>" placeholder="auto from name"></div></div>
  </div>
  <div class="form-group"><label>Department</label>
    <select name="department_id" class="form-control">
      <option value="">-- No Department --</option>
      <?php foreach($departments as $d): ?><option value="<?= $d['id'] ?>" <?= ($role['department_id']??'')==$d['id']?'selected':'' ?>><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group"><label>Description</label>
    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($role['description']??'') ?></textarea></div>
  <div class="checkbox"><label><input type="checkbox" name="is_active" value="1" <?= ($role['is_active']??1)?'checked':'' ?>> Active</label></div>
  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
  <a href="<?= BASE_URL ?>?controller=role&action=index" class="btn btn-default">Cancel</a>
</form>
</div></div></div></div>
