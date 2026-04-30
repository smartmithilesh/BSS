<div class="page-title"><div class="title_left"><h3><i class="fa fa-users"></i> Users</h3></div>
<div class="title_right"><a href="<?= BASE_URL ?>?controller=user&action=form" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New User</a></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover">
<thead><tr><th>#</th><th>Name</th><th>Email</th><th>Department</th><th>Role</th><th>Created</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($users as $u): ?>
<tr>
  <td><?= $u['id'] ?></td>
  <td><?= htmlspecialchars($u['name']) ?></td>
  <td><?= htmlspecialchars($u['email']) ?></td>
  <td><?= htmlspecialchars($u['department_name']??'') ?></td>
  <?php $roleLabels=['superadmin'=>'Super Admin','admin'=>'Admin','staff'=>'Staff']; $roleClasses=['superadmin'=>'badge-danger','admin'=>'badge-success','staff'=>'badge-warning','it'=>'badge-info']; ?>
  <td><span class="badge <?= $roleClasses[$u['role']]??'badge-warning' ?>"><?= htmlspecialchars($u['role_name'] ?: ($roleLabels[$u['role']]??ucfirst($u['role']))) ?></span></td>
  <td><?= date('d/m/Y',strtotime($u['created_at'])) ?></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=user&action=form&id=<?= $u['id'] ?>" class="btn btn-xs btn-primary">Edit</a>
    <?php if($u['id']!=(int)($_SESSION['user']['id']??0)): ?>
    <a href="<?= BASE_URL ?>?controller=user&action=delete&id=<?= $u['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this user?">Delete</a>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
<?php if(empty($users)): ?><tr><td colspan="7" class="text-center text-muted">No users found</td></tr><?php endif; ?>
</tbody></table>
</div></div></div></div>
