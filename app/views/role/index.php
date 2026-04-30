<div class="page-title"><div class="title_left"><h3><i class="fa fa-key"></i> Roles</h3></div>
<div class="title_right"><a href="<?= BASE_URL ?>?controller=role&action=form" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New Role</a></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover">
<thead><tr><th>#</th><th>Role</th><th>Slug</th><th>Department</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($roles as $r): ?>
<tr>
  <td><?= $r['id'] ?></td>
  <td><?= htmlspecialchars($r['name']) ?></td>
  <td><?= htmlspecialchars($r['slug']) ?></td>
  <td><?= htmlspecialchars($r['department_name']??'') ?></td>
  <td><span class="badge <?= $r['is_active']?'badge-success':'badge-danger' ?>"><?= $r['is_active']?'Active':'Inactive' ?></span></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=role&action=form&id=<?= $r['id'] ?>" class="btn btn-xs btn-primary">Edit</a>
    <a href="<?= BASE_URL ?>?controller=role&action=delete&id=<?= $r['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this role?">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
<?php if(empty($roles)): ?><tr><td colspan="6" class="text-center text-muted">No roles found</td></tr><?php endif; ?>
</tbody></table>
</div></div></div></div>
