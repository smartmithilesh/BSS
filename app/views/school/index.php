<div class="page-title"><div class="title_left"><h3><i class="fa fa-university"></i> Schools</h3></div>
<div class="title_right"><a href="<?= BASE_URL ?>?controller=school&action=form" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New School</a></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover">
<thead><tr><th>#</th><th>Name</th><th>Contact Person</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($schools as $sc): ?>
<tr>
  <td><?= $sc['id'] ?></td>
  <td><?= htmlspecialchars($sc['name']) ?></td>
  <td><?= htmlspecialchars($sc['contact_person']) ?></td>
  <td><?= htmlspecialchars($sc['phone']) ?></td>
  <td><?= htmlspecialchars($sc['email']) ?></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=school&action=form&id=<?= $sc['id'] ?>" class="btn btn-xs btn-primary">Edit</a>
    <a href="<?= BASE_URL ?>?controller=school&action=delete&id=<?= $sc['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this school?">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></div></div></div>
