<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-file-text"></i> Purchase Invoice</h3></div>
  <div class="title_right">
    <a href="<?= BASE_URL ?>?controller=purchase&action=pdf&id=<?= $purchase['id'] ?>" class="btn btn-pdf pull-right" target="_blank"><i class="fa fa-file-pdf-o"></i> Download PDF</a>
    <a href="<?= BASE_URL ?>?controller=purchase&action=index" class="btn btn-default pull-right" style="margin-right:5px"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>
<div class="clearfix"></div>

<div class="row"><div class="col-md-10 col-md-offset-1">
<div class="x_panel" id="invoicePrint">
  <div class="x_content" style="padding:30px">

    <!-- Header -->
    <div style="text-align:center;border-bottom:2px solid #2c3e50;padding-bottom:15px;margin-bottom:20px">
      <h2 style="margin:0;color:#2c3e50"><?= htmlspecialchars(AppSettings::get('shop_name',BASE_NAME)) ?></h2>
      <h4 style="margin:5px 0;color:#7f8c8d">PURCHASE INVOICE</h4>
      <?php if(AppSettings::get('phone')||AppSettings::get('email')): ?><div><?= htmlspecialchars(trim(AppSettings::get('phone').' '.AppSettings::get('email'))) ?></div><?php endif; ?>
    </div>

    <!-- Meta -->
    <div class="row" style="margin-bottom:20px">
      <div class="col-md-6">
        <table class="table table-condensed" style="border:none">
          <tr><td><strong>Company:</strong></td><td><?= htmlspecialchars($purchase['company_name']) ?></td></tr>
          <tr><td><strong>Address:</strong></td><td><?= htmlspecialchars($purchase['company_address']??'') ?></td></tr>
          <tr><td><strong>Phone:</strong></td><td><?= htmlspecialchars($purchase['company_phone']??'') ?></td></tr>
        </table>
      </div>
      <div class="col-md-6 text-right">
        <table class="table table-condensed" style="border:none">
          <tr><td><strong>Invoice No:</strong></td><td><?= htmlspecialchars($purchase['invoice_no']) ?></td></tr>
          <tr><td><strong>Date:</strong></td><td><?= date('d/m/Y',strtotime($purchase['purchase_date'])) ?></td></tr>
          <tr><td><strong>Season:</strong></td><td><?= htmlspecialchars($purchase['season_name']) ?></td></tr>
        </table>
      </div>
    </div>

    <!-- Items -->
    <table class="table table-bordered table-hover">
      <thead style="background:#2c3e50;color:#fff">
        <tr><th>#</th><th>Book Name</th><th>Class</th><th>Qty</th><th>Rate (₹)</th><th>Disc%</th><th>Amount (₹)</th></tr>
      </thead>
      <tbody>
        <?php foreach($items as $i=>$it): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($it['book_name']) ?></td>
          <td><?= htmlspecialchars($it['class_name']) ?></td>
          <td><?= $it['qty'] ?></td>
          <td><?= number_format($it['rate'],2) ?></td>
          <td><?= number_format($it['discount_pct'],2) ?>%</td>
          <td><?= number_format($it['amount'],2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr><td colspan="6" class="text-right">Gross Amount:</td><td>₹<?= number_format($purchase['gross_amount'],2) ?></td></tr>
        <tr><td colspan="6" class="text-right">Discount:</td><td>(-) ₹<?= number_format($purchase['discount_amount'],2) ?></td></tr>
        <tr style="background:#2c3e50;color:#fff;font-weight:bold">
          <td colspan="6" class="text-right">NET AMOUNT:</td>
          <td>₹<?= number_format($purchase['net_amount'],2) ?></td>
        </tr>
      </tfoot>
    </table>

    <?php if(!empty($purchase['notes'])): ?>
    <p><strong>Notes:</strong> <?= htmlspecialchars($purchase['notes']) ?></p>
    <?php endif; ?>

  </div>
</div>
</div></div>
