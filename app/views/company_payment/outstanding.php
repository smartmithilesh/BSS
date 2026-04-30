<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-exclamation-circle"></i> Company Outstanding</h3></div>
  <div class="title_right">
    <form method="GET" action="<?= url('companyPayment','outstanding') ?>" class="pull-right form-inline">
      <select name="season_id" class="form-control form-control-sm" onchange="this.form.submit()">
        <?php foreach($seasons as $s): ?><option value="<?= $s['id'] ?>" <?= $seasonId==$s['id']?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?>
      </select>
    </form>
  </div>
</div>
<div class="clearfix"></div>

<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover table-bordered">
<thead><tr><th>Company</th><th>Total Purchase</th><th>Total Paid</th><th>Total Returned</th><th>Outstanding</th><th>Action</th></tr></thead>
<tbody>
<?php foreach($rows as $r):
  $outstanding = $r['total_purchase'] - $r['total_paid'] - $r['total_returned'];
?>
<tr>
  <td><?= htmlspecialchars($r['name']) ?></td>
  <td>₹<?= number_format($r['total_purchase'],2) ?></td>
  <td>₹<?= number_format($r['total_paid'],2) ?></td>
  <td>₹<?= number_format($r['total_returned'],2) ?></td>
  <td><span class="badge <?= $outstanding>0.01?'badge-danger':'badge-success' ?>">₹<?= number_format($outstanding,2) ?></span></td>
  <td>
    <?php if($outstanding>0.01): ?>
    <a href="<?= BASE_URL ?>?controller=companyPayment&action=create" class="btn btn-xs btn-success">Pay Now</a>
    <?php else: ?>
    <span class="text-success"><i class="fa fa-check"></i> Settled</span>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></div></div></div>
