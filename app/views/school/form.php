<div class="page-title"><div class="title_left"><h3><?= $pageTitle ?></h3></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-8"><div class="x_panel"><div class="x_content">
<form method="POST" action="<?= BASE_URL ?>?controller=school&action=save">
  <input type="hidden" name="id" value="<?= $school['id']??'' ?>">
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>School Name *</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($school['name']??'') ?>" required></div></div>
    <div class="col-md-6"><div class="form-group"><label>Contact Person</label>
      <input type="text" name="contact_person" class="form-control" value="<?= htmlspecialchars($school['contact_person']??'') ?>"></div></div>
  </div>
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($school['phone']??'') ?>"></div></div>
    <div class="col-md-6"><div class="form-group"><label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($school['email']??'') ?>"></div></div>
  </div>
  <div class="form-group"><label>Address</label>
    <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($school['address']??'') ?></textarea></div>
  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
  <a href="<?= BASE_URL ?>?controller=school&action=index" class="btn btn-default">Cancel</a>
</form>
</div></div></div></div>
