<div class="page-title"><div class="title_left"><h3><i class="fa fa-print"></i> Publications</h3></div>
<div class="title_right"><a href="<?= BASE_URL ?>?controller=publication&action=form" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New</a></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-8"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover">
<thead><tr><th>#</th><th>Name</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($publications as $p): ?>
<tr>
  <td><?= $p['id'] ?></td>
  <td><?= htmlspecialchars($p['name']) ?></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=publication&action=form&id=<?= $p['id'] ?>" class="btn btn-xs btn-primary">Edit</a>
    <a href="<?= BASE_URL ?>?controller=publication&action=delete&id=<?= $p['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete?">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></div></div></div>
