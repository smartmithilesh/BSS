<?php require_once __DIR__.'/../layout/helpers.php'; ?>
<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-shopping-cart"></i> Purchase List</h3></div>
  <div class="title_right"><a href="<?= BASE_URL ?>?controller=purchase&action=create" class="btn btn-success pull-right"><i class="fa fa-plus"></i> New Purchase</a></div>
</div>
<div class="clearfix"></div>

<!-- Filters -->
<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<form method="GET" action="<?= BASE_URL ?>" class="form-inline">
  <input type="hidden" name="controller" value="purchase"><input type="hidden" name="action" value="index">
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
  <div class="form-group" style="margin-right:6px"><input type="text" name="invoice_no" class="form-control" placeholder="Invoice No" value="<?= htmlspecialchars($filters['invoice_no']) ?>"></div>
  <div class="form-group" style="margin-right:6px"><input type="date" name="from_date" class="form-control" value="<?= $filters['from_date'] ?>"></div>
  <div class="form-group" style="margin-right:6px"><input type="date" name="to_date" class="form-control" value="<?= $filters['to_date'] ?>"></div>
  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
  <a href="<?= BASE_URL ?>?controller=purchase&action=index" class="btn btn-default">Reset</a>
</form>
</div></div></div></div>

<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<p class="text-muted">Showing <?= count($purchases) ?> of <?= $total ?> records</p>
<table class="table table-striped table-hover table-bordered">
<thead><tr><th>#</th><th>Invoice No</th><th>Company</th><th>Season</th><th>Date</th><th>Gross</th><th>Discount</th><th>Net Amount</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($purchases as $p): ?>
<tr>
  <td><?= $p['id'] ?></td>
  <td><?= htmlspecialchars($p['invoice_no']) ?></td>
  <td><?= htmlspecialchars($p['company_name']) ?></td>
  <td><?= htmlspecialchars($p['season_name']) ?></td>
  <td><?= date('d/m/Y',strtotime($p['purchase_date'])) ?></td>
  <td>₹<?= number_format($p['gross_amount'],2) ?></td>
  <td>₹<?= number_format($p['discount_amount'],2) ?></td>
  <td><strong>₹<?= number_format($p['net_amount'],2) ?></strong></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=purchase&action=invoice&id=<?= $p['id'] ?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> View</a>
    <a href="<?= BASE_URL ?>?controller=purchase&action=pdf&id=<?= $p['id'] ?>" class="btn btn-xs btn-pdf" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
    <a href="<?= BASE_URL ?>?controller=purchase&action=delete&id=<?= $p['id'] ?>" class="btn btn-xs btn-danger" data-confirm="Delete this purchase? Stock will be reversed."><i class="fa fa-trash"></i></a>
  </td>
</tr>
<?php endforeach; ?>
<?php if(empty($purchases)): ?><tr><td colspan="9" class="text-center text-muted">No purchases found</td></tr><?php endif; ?>
</tbody></table>
<?php
$qs=http_build_query(array_filter(['controller'=>'purchase','action'=>'index']+$filters));
echo paginate($total,$page,$limit,'?'.$qs);
?>
</div></div></div></div>
