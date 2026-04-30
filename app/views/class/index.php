<div class="page-title"><div class="title_left"><h3><i class="fa fa-graduation-cap"></i> Classes</h3></div>
<div class="title_right"><a href="<?= BASE_URL ?>?controller=class&action=form" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New Class</a></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-8"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover">
<thead><tr><th>#</th><th>Name</th><th>Sort Order</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($classes as $cl): ?>
<tr>
  <td><?= $cl['id'] ?></td>
  <td><?= htmlspecialchars($cl['name']) ?></td>
  <td><?= $cl['sort_order'] ?></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=class&action=form&id=<?= $cl['id'] ?>" class="btn btn-xs btn-primary">Edit</a>
    <a href="<?= BASE_URL ?>?controller=class&action=delete&id=<?= $cl['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this class?">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></div></div></div>
