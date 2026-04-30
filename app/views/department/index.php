<div class="page-title"><div class="title_left"><h3><i class="fa fa-sitemap"></i> Departments</h3></div>
<div class="title_right"><a href="<?= BASE_URL ?>?controller=department&action=form" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New Department</a></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover">
<thead><tr><th>#</th><th>Name</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($departments as $d): ?>
<tr>
  <td><?= $d['id'] ?></td>
  <td><?= htmlspecialchars($d['name']) ?></td>
  <td><?= htmlspecialchars($d['description']??'') ?></td>
  <td><span class="badge <?= $d['is_active']?'badge-success':'badge-danger' ?>"><?= $d['is_active']?'Active':'Inactive' ?></span></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=department&action=form&id=<?= $d['id'] ?>" class="btn btn-xs btn-primary">Edit</a>
    <a href="<?= BASE_URL ?>?controller=department&action=delete&id=<?= $d['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this department?">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
<?php if(empty($departments)): ?><tr><td colspan="5" class="text-center text-muted">No departments found</td></tr><?php endif; ?>
</tbody></table>
</div></div></div></div>
