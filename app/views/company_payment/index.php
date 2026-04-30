<?php require_once __DIR__.'/../layout/helpers.php'; ?>
<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-money"></i> Company Payments</h3></div>
  <div class="title_right">
    <a href="<?= BASE_URL ?>?controller=companyPayment&action=outstanding" class="btn btn-warning pull-right" style="margin-left:5px"><i class="fa fa-exclamation-circle"></i> Outstanding</a>
    <a href="<?= BASE_URL ?>?controller=companyPayment&action=create" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Pay Company</a>
  </div>
</div>
<div class="clearfix"></div>

<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<form method="GET" action="<?= url('companyPayment','index') ?>" class="form-inline" style="margin-bottom:10px">
  <div class="form-group" style="margin-right:6px">
    <select name="season_id" class="form-control"><option value="">All Seasons</option>
      <?php foreach($seasons as $s): ?><option value="<?= $s['id'] ?>" <?= $filters['season_id']==$s['id']?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" style="margin-right:6px">
    <select name="company_id" class="form-control"><option value="">All Companies</option>
      <?php foreach($companies as $co): ?><option value="<?= $co['id'] ?>" <?= $filters['company_id']==$co['id']?'selected':'' ?>><?= htmlspecialchars($co['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
  <a href="<?= BASE_URL ?>?controller=companyPayment&action=index" class="btn btn-default">Reset</a>
</form>
<table class="table table-striped table-hover table-bordered">
<thead><tr><th>#</th><th>Company</th><th>Season</th><th>Date</th><th>Amount</th><th>Mode</th><th>Reference</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($payments as $p): ?>
<tr>
  <td><?= $p['id'] ?></td>
  <td><?= htmlspecialchars($p['company_name']) ?></td>
  <td><?= htmlspecialchars($p['season_name']) ?></td>
  <td><?= date('d/m/Y',strtotime($p['payment_date'])) ?></td>
  <td><strong>₹<?= number_format($p['amount'],2) ?></strong></td>
  <td><?= ucfirst($p['payment_mode']) ?></td>
  <td><?= htmlspecialchars($p['reference_no']) ?></td>
  <td><a href="<?= BASE_URL ?>?controller=companyPayment&action=delete&id=<?= $p['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this payment?"><i class="fa fa-trash"></i></a></td>
</tr>
<?php endforeach; ?>
<?php if(empty($payments)): ?><tr><td colspan="8" class="text-center text-muted">No payments found</td></tr><?php endif; ?>
</tbody></table>
<?php
$qs=http_build_query(array_filter(['controller'=>'companyPayment','action'=>'index']+$filters));
echo paginate($total,$page,$limit,'?'.$qs);
?>
</div></div></div></div>
