<div class="page-title"><div class="title_left"><h3><i class="fa fa-calendar"></i> Seasons</h3></div>
<div class="title_right"><a href="<?= BASE_URL ?>?controller=season&action=form" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New Season</a></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover">
<thead><tr><th>#</th><th>Name</th><th>Year</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($seasons as $s): ?>
<tr>
  <td><?= $s['id'] ?></td>
  <td><?= htmlspecialchars($s['name']) ?></td>
  <td><?= $s['start_year'] ?>–<?= $s['end_year'] ?></td>
  <td><?php if($s['is_active']): ?><span class="badge badge-success">Active</span><?php else: ?><span class="badge">Inactive</span><?php endif; ?></td>
  <td>
    <?php if(!$s['is_active']): ?>
    <a href="<?= BASE_URL ?>?controller=season&action=setActive&id=<?= $s['id'] ?>" class="btn btn-xs btn-success" data-confirm="Set as active season?">Set Active</a>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>?controller=season&action=form&id=<?= $s['id'] ?>" class="btn btn-xs btn-primary">Edit</a>
    <a href="<?= BASE_URL ?>?controller=season&action=delete&id=<?= $s['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this season?">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div></div></div></div>
