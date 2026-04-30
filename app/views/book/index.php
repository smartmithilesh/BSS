<?php require_once __DIR__.'/../layout/helpers.php'; ?>
<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-book"></i> Books</h3></div>
  <div class="title_right"><a href="<?= BASE_URL ?>?controller=book&action=form" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New Book</a></div>
</div>
<div class="clearfix"></div>

<!-- Filters -->
<div class="row"><div class="col-md-12"><div class="x_panel">
<div class="x_title"><h2>Filter Books</h2><div class="clearfix"></div></div>
<div class="x_content">
<form method="GET" action="<?= url('book','index') ?>" class="form-inline">
  <div class="form-group" style="margin-right:8px"><input type="text" name="name" class="form-control" placeholder="Book name" value="<?= htmlspecialchars($filters['name']) ?>"></div>
  <div class="form-group" style="margin-right:8px">
    <select name="class_id" class="form-control">
      <option value="">All Classes</option>
      <?php foreach($classes as $cl): ?><option value="<?= $cl['id'] ?>" <?= $filters['class_id']==$cl['id']?'selected':'' ?>><?= htmlspecialchars($cl['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" style="margin-right:8px">
    <select name="company_id" class="form-control">
      <option value="">All Companies</option>
      <?php foreach($companies as $co): ?><option value="<?= $co['id'] ?>" <?= $filters['company_id']==$co['id']?'selected':'' ?>><?= htmlspecialchars($co['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" style="margin-right:8px">
    <select name="is_active" class="form-control">
      <option value="">All Status</option>
      <option value="1" <?= $filters['is_active']==='1'?'selected':'' ?>>Active</option>
      <option value="0" <?= $filters['is_active']==='0'?'selected':'' ?>>Inactive</option>
    </select>
  </div>
  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
  <a href="<?= BASE_URL ?>?controller=book&action=index" class="btn btn-default">Reset</a>
</form>
</div></div></div></div>

<!-- Table -->
<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<p class="text-muted">Showing <?= count($books) ?> of <?= $total ?> books</p>
<table class="table table-striped table-hover table-bordered">
<thead><tr><th>#</th><th>Book Name</th><th>Class</th><th>Publication</th><th>Company</th><th>MRP</th><th>Sale Rate</th><th>Disc%</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($books as $b): ?>
<tr>
  <td><?= $b['id'] ?></td>
  <td><?= htmlspecialchars($b['name']) ?></td>
  <td><?= htmlspecialchars($b['class_name']) ?></td>
  <td><?= htmlspecialchars($b['publication_name']) ?></td>
  <td><?= htmlspecialchars($b['company_name']) ?></td>
  <td>₹<?= number_format($b['mrp'],2) ?></td>
  <td>₹<?= number_format($b['sale_rate'],2) ?></td>
  <td><?= $b['discount_pct'] ?>%</td>
  <td><span class="badge <?= $b['is_active']?'badge-success':'badge-danger' ?>"><?= $b['is_active']?'Active':'Inactive' ?></span></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=book&action=form&id=<?= $b['id'] ?>" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
    <a href="<?= BASE_URL ?>?controller=book&action=delete&id=<?= $b['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this book?"><i class="fa fa-trash"></i></a>
  </td>
</tr>
<?php endforeach; ?>
<?php if(empty($books)): ?><tr><td colspan="10" class="text-center text-muted">No books found</td></tr><?php endif; ?>
</tbody></table>
<?php
$qs=http_build_query(array_filter(['controller'=>'book','action'=>'index']+$filters));
echo paginate($total,$page,$limit,'?'.$qs);
?>
</div></div></div></div>
