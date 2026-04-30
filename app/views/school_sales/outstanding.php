<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-exclamation-circle"></i> School Outstanding Report</h3></div>
  <div class="title_right">
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=payments" class="btn btn-info pull-right"><i class="fa fa-list"></i> Payment List</a>
  </div>
</div>
<div class="clearfix"></div>

<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<form method="GET" action="<?= BASE_URL ?>" class="form-inline" style="margin-bottom:10px">
  <input type="hidden" name="controller" value="schoolsale"><input type="hidden" name="action" value="outstanding">
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
  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
  <a href="<?= BASE_URL ?>?controller=schoolsale&action=outstanding" class="btn btn-default">Reset</a>
</form>

<?php
$totalNet=$totalPaid=$totalOutstanding=0;
foreach($rows as $r) { $totalNet+=(float)$r['net_amount']; $totalPaid+=(float)$r['paid_amount']; $totalOutstanding+=(float)$r['outstanding']; }
?>
<div class="row">
  <div class="col-md-4"><div class="well well-sm"><strong>Total Sale:</strong> ₹<?= number_format($totalNet,2) ?></div></div>
  <div class="col-md-4"><div class="well well-sm"><strong>Total Received:</strong> ₹<?= number_format($totalPaid,2) ?></div></div>
  <div class="col-md-4"><div class="well well-sm"><strong>Total Outstanding:</strong> ₹<?= number_format($totalOutstanding,2) ?></div></div>
</div>

<table class="table table-striped table-hover table-bordered">
<thead><tr><th>Invoice</th><th>School</th><th>Season</th><th>Date</th><th>Net Amount</th><th>Paid</th><th>Outstanding</th><th>Action</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
  <td><a href="<?= BASE_URL ?>?controller=schoolsale&action=view&id=<?= $r['id'] ?>"><?= htmlspecialchars($r['invoice_no']) ?></a></td>
  <td><?= htmlspecialchars($r['school_name']) ?></td>
  <td><?= htmlspecialchars($r['season_name']) ?></td>
  <td><?= date('d/m/Y',strtotime($r['sale_date'])) ?></td>
  <td>₹<?= number_format($r['net_amount'],2) ?></td>
  <td>₹<?= number_format($r['paid_amount'],2) ?></td>
  <td><span class="badge badge-danger">₹<?= number_format($r['outstanding'],2) ?></span></td>
  <td>
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=receivePayment&id=<?= $r['id'] ?>" class="btn btn-xs btn-success"><i class="fa fa-inr"></i> Receive</a>
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=view&id=<?= $r['id'] ?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
  </td>
</tr>
<?php endforeach; ?>
<?php if(empty($rows)): ?><tr><td colspan="8" class="text-center text-muted">No outstanding school invoices found</td></tr><?php endif; ?>
</tbody></table>
</div></div></div></div>
