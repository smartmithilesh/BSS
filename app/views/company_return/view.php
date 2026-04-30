<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-undo"></i> Return Details</h3></div>
  <div class="title_right"><a href="<?= BASE_URL ?>?controller=companyReturn&action=index" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i> Back</a></div>
</div>
<div class="clearfix"></div>

<div class="row"><div class="col-md-10 col-md-offset-1"><div class="x_panel"><div class="x_content" style="padding:25px">
  <div style="text-align:center;border-bottom:2px solid #2c3e50;padding-bottom:15px;margin-bottom:20px">
    <h2 style="color:#2c3e50"><?= htmlspecialchars(AppSettings::get('shop_name',BASE_NAME)) ?></h2>
    <h4 style="color:#7f8c8d">COMPANY RETURN NOTE</h4>
  </div>
  <div class="row" style="margin-bottom:20px">
    <div class="col-md-6">
      <strong>Return To:</strong><br>
      <h4><?= htmlspecialchars($ret['company_name']) ?></h4>
      <div><?= htmlspecialchars($ret['company_address']??'') ?></div>
    </div>
    <div class="col-md-6">
      <table class="table table-condensed">
        <tr><td><strong>Reference No:</strong></td><td><?= htmlspecialchars($ret['reference_no']) ?></td></tr>
        <tr><td><strong>Return Date:</strong></td><td><?= date('d/m/Y',strtotime($ret['return_date'])) ?></td></tr>
        <tr><td><strong>Season:</strong></td><td><?= htmlspecialchars($ret['season_name']) ?></td></tr>
      </table>
    </div>
  </div>
  <table class="table table-bordered table-hover">
    <thead style="background:#2c3e50;color:#fff"><tr><th>#</th><th>Book Name</th><th>Class</th><th>Qty</th><th>Rate (₹)</th><th>Amount (₹)</th></tr></thead>
    <tbody>
      <?php foreach($items as $i=>$it): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($it['book_name']) ?></td>
        <td><?= htmlspecialchars($it['class_name']) ?></td>
        <td><?= $it['qty'] ?></td>
        <td><?= number_format($it['rate'],2) ?></td>
        <td><?= number_format($it['amount'],2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr style="background:#2c3e50;color:#fff;font-weight:bold">
        <td colspan="5" class="text-right">TOTAL:</td>
        <td>₹<?= number_format($ret['total_amount'],2) ?></td>
      </tr>
    </tfoot>
  </table>
  <?php if(!empty($ret['notes'])): ?>
  <p><strong>Notes:</strong> <?= htmlspecialchars($ret['notes']) ?></p>
  <?php endif; ?>
</div></div></div></div>
