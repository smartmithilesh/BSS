<div class="page-title"><div class="title_left"><h3><?= $pageTitle ?></h3></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-9"><div class="x_panel"><div class="x_content">
<form method="POST" action="<?= BASE_URL ?>?controller=book&action=save">
  <input type="hidden" name="id" value="<?= $book['id']??'' ?>">
  <div class="row">
    <div class="col-md-8"><div class="form-group"><label>Book Name *</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($book['name']??'') ?>" required></div></div>
    <div class="col-md-4"><div class="form-group"><label>Class *</label>
      <select name="class_id" class="form-control" required>
        <option value="">-- Select --</option>
        <?php foreach($classes as $cl): ?><option value="<?= $cl['id'] ?>" <?= ($book['class_id']??'')==$cl['id']?'selected':'' ?>><?= htmlspecialchars($cl['name']) ?></option><?php endforeach; ?>
      </select></div></div>
  </div>
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Publication *</label>
      <select name="publication_id" class="form-control" required>
        <option value="">-- Select --</option>
        <?php foreach($publications as $p): ?><option value="<?= $p['id'] ?>" <?= ($book['publication_id']??'')==$p['id']?'selected':'' ?>><?= htmlspecialchars($p['name']) ?></option><?php endforeach; ?>
      </select></div></div>
    <div class="col-md-6"><div class="form-group"><label>Company / Publisher *</label>
      <select name="company_id" class="form-control" required>
        <option value="">-- Select --</option>
        <?php foreach($companies as $co): ?><option value="<?= $co['id'] ?>" <?= ($book['company_id']??'')==$co['id']?'selected':'' ?>><?= htmlspecialchars($co['name']) ?></option><?php endforeach; ?>
      </select></div></div>
  </div>
  <div class="row">
    <div class="col-md-3"><div class="form-group"><label>MRP (₹)</label>
      <input type="number" step="0.01" name="mrp" class="form-control" value="<?= $book['mrp']??0 ?>"></div></div>
    <div class="col-md-3"><div class="form-group"><label>Purchase Rate (₹)</label>
      <input type="number" step="0.01" name="purchase_rate" class="form-control" value="<?= $book['purchase_rate']??0 ?>"></div></div>
    <div class="col-md-3"><div class="form-group"><label>Sale Rate (₹)</label>
      <input type="number" step="0.01" name="sale_rate" class="form-control" value="<?= $book['sale_rate']??0 ?>"></div></div>
    <div class="col-md-3"><div class="form-group"><label>Default Discount %</label>
      <input type="number" step="0.01" name="discount_pct" class="form-control" value="<?= $book['discount_pct']??0 ?>"></div></div>
  </div>
  <div class="form-group">
    <label><input type="checkbox" name="is_active" value="1" <?= ($book['is_active']??1)?'checked':'' ?>> Active</label>
  </div>
  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Book</button>
  <a href="<?= BASE_URL ?>?controller=book&action=index" class="btn btn-default">Cancel</a>
</form>
</div></div></div></div>
