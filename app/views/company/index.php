<div class="page-title"><div class="title_left"><h3><i class="fa fa-building"></i> Companies</h3></div>
<div class="title_right"><a href="<?= BASE_URL ?>?controller=company&action=form" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New Company</a></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover">
<thead><tr><th>#</th><th>Name</th><th>Contact</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($companies as $co): ?>
<tr>
  <td><?= $co['id'] ?></td>
  <td><?= htmlspecialchars($co['name']) ?></td>
  <td><?= htmlspecialchars($co['contact_person']) ?></td>
  <td><?= htmlspecialchars($co['phone']) ?></td>
  <td><?= htmlspecialchars($co['email']) ?></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=company&action=form&id=<?= $co['id'] ?>" class="btn btn-xs btn-primary">Edit</a>
    <a href="<?= BASE_URL ?>?controller=company&action=delete&id=<?= $co['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this company?">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div></div></div></div>
