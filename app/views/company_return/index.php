<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-undo"></i> Company Returns</h3></div>
  <div class="title_right">
    <a href="<?= BASE_URL ?>?controller=companyReturn&action=create" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New Return</a>
  </div>
</div>
<div class="clearfix"></div>

<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<form method="GET" action="<?= url('companyReturn','index') ?>" class="form-inline" style="margin-bottom:10px">
  <select name="season_id" class="form-control" style="margin-right:5px"><option value="">All Seasons</option>
    <?php foreach($seasons as $s): ?><option value="<?= $s['id'] ?>" <?= $seasonId==$s['id']?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
  <a href="<?= BASE_URL ?>?controller=companyReturn&action=index" class="btn btn-default">Reset</a>
</form>

<table class="table table-striped table-hover table-bordered">
<thead><tr><th>#</th><th>Company</th><th>Season</th><th>Date</th><th>Reference</th><th>Total Amount</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($returns as $r): ?>
<tr>
  <td><?= $r['id'] ?></td>
  <td><?= htmlspecialchars($r['company_name']) ?></td>
  <td><?= htmlspecialchars($r['season_name']) ?></td>
  <td><?= date('d/m/Y',strtotime($r['return_date'])) ?></td>
  <td><?= htmlspecialchars($r['reference_no']) ?></td>
  <td><strong>₹<?= number_format($r['total_amount'],2) ?></strong></td>
  <td><a href="<?= BASE_URL ?>?controller=companyReturn&action=view&id=<?= $r['id'] ?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> View</a></td>
</tr>
<?php endforeach; ?>
<?php if(empty($returns)): ?><tr><td colspan="7" class="text-center text-muted">No returns found</td></tr><?php endif; ?>
</tbody></table>
</div></div></div></div>
