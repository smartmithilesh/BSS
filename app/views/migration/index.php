<div class="page-title"><div class="title_left"><h3><i class="fa fa-database"></i> Database Updates</h3></div>
<div class="title_right"><a href="<?= BASE_URL ?>?controller=migration&action=run" class="btn btn-success pull-right"><i class="fa fa-play"></i> Run Pending Updates</a></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover">
<thead><tr><th>Migration</th><th>Status</th><th>Executed At</th></tr></thead>
<tbody>
<?php foreach($migrations as $m): ?>
<tr>
  <td><?= htmlspecialchars($m['migration']) ?></td>
  <td><span class="badge <?= $m['status']==='executed'?'badge-success':'badge-warning' ?>"><?= ucfirst($m['status']) ?></span></td>
  <td><?= htmlspecialchars($m['executed_at']??'') ?></td>
</tr>
<?php endforeach; ?>
<?php if(empty($migrations)): ?><tr><td colspan="3" class="text-center text-muted">No migration files found</td></tr><?php endif; ?>
</tbody></table>
</div></div></div></div>
