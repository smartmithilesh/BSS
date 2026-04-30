<?php require_once __DIR__.'/../layout/helpers.php'; ?>
<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-inr"></i> School Payment Receive List</h3></div>
  <div class="title_right">
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=receivePayment" class="btn btn-success pull-right" style="margin-left:5px"><i class="fa fa-plus"></i> Receive Payment</a>
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=outstanding" class="btn btn-warning pull-right"><i class="fa fa-exclamation-circle"></i> Outstanding</a>
  </div>
</div>
<div class="clearfix"></div>

<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<form method="GET" action="<?= BASE_URL ?>" class="form-inline" style="margin-bottom:10px">
  <input type="hidden" name="controller" value="schoolsale"><input type="hidden" name="action" value="payments">
  <div class="form-group" style="margin-right:6px">
    <select name="season_id" class="form-control"><option value="">All Seasons</option>
      <?php foreach($seasons as $s): ?><option value="<?= $s['id'] ?>" <?= $filters['season_id']==$s['id']?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" style="margin-right:6px">
    <select name="school_id" class="form-control"><option value="">All Schools</option>
      <?php foreach($schools as $sc): ?><option value="<?= $sc['id'] ?>" <?= $filters['school_id']==$sc['id']?'selected':'' ?>><?= htmlspecialchars($sc['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" style="margin-right:6px"><input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($filters['from_date']) ?>"></div>
  <div class="form-group" style="margin-right:6px"><input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($filters['to_date']) ?>"></div>
  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
  <a href="<?= BASE_URL ?>?controller=schoolsale&action=payments" class="btn btn-default">Reset</a>
</form>

<p class="text-muted">Showing <?= count($payments) ?> of <?= $total ?> payments</p>
<table class="table table-striped table-hover table-bordered">
<thead><tr><th>#</th><th>Date</th><th>Invoice</th><th>School</th><th>Season</th><th>Amount</th><th>Mode</th><th>Reference</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($payments as $p): ?>
<tr>
  <td><?= $p['id'] ?></td>
  <td><?= date('d/m/Y',strtotime($p['payment_date'])) ?></td>
  <td><a href="<?= BASE_URL ?>?controller=schoolsale&action=view&id=<?= $p['sale_id'] ?>"><?= htmlspecialchars($p['invoice_no']) ?></a></td>
  <td><?= htmlspecialchars($p['school_name']) ?></td>
  <td><?= htmlspecialchars($p['season_name']) ?></td>
  <td><strong>₹<?= number_format($p['amount'],2) ?></strong></td>
  <td><?= ucfirst($p['payment_mode']) ?></td>
  <td><?= htmlspecialchars($p['reference_no']??'') ?></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=view&id=<?= $p['sale_id'] ?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=deletePayment&id=<?= $p['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this received payment?"><i class="fa fa-trash"></i></a>
  </td>
</tr>
<?php endforeach; ?>
<?php if(empty($payments)): ?><tr><td colspan="9" class="text-center text-muted">No payments found</td></tr><?php endif; ?>
</tbody></table>
<?php
$qs=http_build_query(array_filter(['controller'=>'schoolsale','action'=>'payments']+$filters));
echo paginate($total,$page,$limit,'?'.$qs);
?>
</div></div></div></div>
