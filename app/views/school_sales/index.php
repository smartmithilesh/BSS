<?php require_once __DIR__.'/../layout/helpers.php'; ?>
<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-line-chart"></i> School Sales</h3></div>
  <div class="title_right"><a href="<?= BASE_URL ?>?controller=schoolsale&action=create" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New Sale</a></div>
</div>
<div class="clearfix"></div>

<!-- Filters -->
<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<form method="GET" action="<?= BASE_URL ?>" class="form-inline">
  <input type="hidden" name="controller" value="schoolsale"><input type="hidden" name="action" value="index">
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
  <div class="form-group" style="margin-right:6px"><input type="text" name="invoice_no" class="form-control" placeholder="Invoice No" value="<?= htmlspecialchars($filters['invoice_no']) ?>"></div>
  <div class="form-group" style="margin-right:6px"><input type="date" name="from_date" class="form-control" value="<?= $filters['from_date'] ?>"></div>
  <div class="form-group" style="margin-right:6px"><input type="date" name="to_date" class="form-control" value="<?= $filters['to_date'] ?>"></div>
  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
  <a href="<?= BASE_URL ?>?controller=schoolsale&action=index" class="btn btn-default">Reset</a>
</form>
</div></div></div></div>

<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<p class="text-muted">Showing <?= count($sales) ?> of <?= $total ?> records</p>
<table class="table table-striped table-hover table-bordered">
<thead><tr><th>#</th><th>Invoice No</th><th>School</th><th>Season</th><th>Date</th><th>Net Amount</th><th>Paid</th><th>Outstanding</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($sales as $s): ?>
<?php $out=$s['net_amount']-$s['paid_amount']; ?>
<tr>
  <td><?= $s['id'] ?></td>
  <td><a href="<?= BASE_URL ?>?controller=schoolsale&action=view&id=<?= $s['id'] ?>"><?= htmlspecialchars($s['invoice_no']) ?></a></td>
  <td><?= htmlspecialchars($s['school_name']) ?></td>
  <td><?= htmlspecialchars($s['season_name']) ?></td>
  <td><?= date('d/m/Y',strtotime($s['sale_date'])) ?></td>
  <td>₹<?= number_format($s['net_amount'],2) ?></td>
  <td>₹<?= number_format($s['paid_amount'],2) ?></td>
  <td><span class="badge <?= $out>0.01?'badge-danger':'badge-success' ?>">₹<?= number_format($out,2) ?></span></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=view&id=<?= $s['id'] ?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=pdf&id=<?= $s['id'] ?>" class="btn btn-xs btn-pdf" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
    <?php if($out>0.01): ?>
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=receivePayment&id=<?= $s['id'] ?>" class="btn btn-xs btn-success"><i class="fa fa-inr"></i></a>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
<?php if(empty($sales)): ?><tr><td colspan="9" class="text-center text-muted">No sales found</td></tr><?php endif; ?>
</tbody></table>
<?php
$qs=http_build_query(array_filter(['controller'=>'schoolsale','action'=>'index']+$filters));
echo paginate($total,$page,$limit,'?'.$qs);
?>
</div></div></div></div>
