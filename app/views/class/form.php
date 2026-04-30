<div class="page-title"><div class="title_left"><h3><?= $pageTitle ?></h3></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-5"><div class="x_panel"><div class="x_content">
<form method="POST" action="<?= BASE_URL ?>?controller=class&action=save">
  <input type="hidden" name="id" value="<?= $cls['id']??'' ?>">
  <div class="form-group"><label>Class Name *</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cls['name']??'') ?>" placeholder="e.g. Class 5" required></div>
  <div class="form-group"><label>Sort Order</label>
    <input type="number" name="sort_order" class="form-control" value="<?= $cls['sort_order']??0 ?>"></div>
  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
  <a href="<?= BASE_URL ?>?controller=class&action=index" class="btn btn-default">Cancel</a>
</form>
</div></div></div></div>
