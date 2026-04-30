<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-cubes"></i> Stock Report</h3></div>
  <div class="title_right">
    <form method="GET" action="<?= url('stock','index') ?>" class="pull-right form-inline">
      <select name="season_id" class="form-control form-control-sm" onchange="this.form.submit()" style="margin-right:5px">
        <?php foreach($seasons as $s): ?><option value="<?= $s['id'] ?>" <?= $seasonId==$s['id']?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?>
      </select>
    </form>
  </div>
</div>
<div class="clearfix"></div>

<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-hover table-bordered">
<thead><tr><th>#</th><th>Book Name</th><th>Class</th><th>Company</th><th>Stock Qty</th><th>Status</th></tr></thead>
<tbody>
<?php foreach($stocks as $i=>$s): ?>
<tr>
  <td><?= $i+1 ?></td>
  <td><?= htmlspecialchars($s['book_name']) ?></td>
  <td><?= htmlspecialchars($s['class_name']) ?></td>
  <td><?= htmlspecialchars($s['company_name']) ?></td>
  <td><strong><?= $s['qty'] ?></strong></td>
  <td>
    <?php if($s['qty']<=0): ?><span class="badge badge-danger">Out of Stock</span>
    <?php elseif($s['qty']<=10): ?><span class="badge badge-warning">Low Stock</span>
    <?php else: ?><span class="badge badge-success">In Stock</span>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
<?php if(empty($stocks)): ?><tr><td colspan="6" class="text-center text-muted">No stock data found</td></tr><?php endif; ?>
</tbody></table>
</div></div></div></div>
