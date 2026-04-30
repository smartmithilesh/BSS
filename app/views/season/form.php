<div class="page-title"><div class="title_left"><h3><?= $pageTitle ?></h3></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-6"><div class="x_panel"><div class="x_content">
<form method="POST" action="<?= BASE_URL ?>?controller=season&action=save">
  <input type="hidden" name="id" value="<?= $season['id']??'' ?>">
  <div class="form-group"><label>Season Name *</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($season['name']??'') ?>" placeholder="e.g. 2025-26" required></div>
  <div class="form-group"><label>Start Year *</label>
    <input type="number" name="start_year" class="form-control" value="<?= $season['start_year']??date('Y') ?>" required></div>
  <div class="form-group"><label>End Year *</label>
    <input type="number" name="end_year" class="form-control" value="<?= $season['end_year']??date('Y')+1 ?>" required></div>
  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
  <a href="<?= BASE_URL ?>?controller=season&action=index" class="btn btn-default">Cancel</a>
</form>
</div></div></div></div>
