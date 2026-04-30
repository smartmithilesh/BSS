<div class="page-title"><div class="title_left"><h3><?= htmlspecialchars($pageTitle) ?></h3></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-8"><div class="x_panel"><div class="x_content">
<form method="POST" action="<?= BASE_URL ?>?controller=department&action=save">
  <input type="hidden" name="id" value="<?= $department['id']??'' ?>">
  <div class="form-group"><label>Department Name *</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($department['name']??'') ?>" required></div>
  <div class="form-group"><label>Description</label>
    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($department['description']??'') ?></textarea></div>
  <div class="checkbox"><label><input type="checkbox" name="is_active" value="1" <?= ($department['is_active']??1)?'checked':'' ?>> Active</label></div>
  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
  <a href="<?= BASE_URL ?>?controller=department&action=index" class="btn btn-default">Cancel</a>
</form>
</div></div></div></div>
