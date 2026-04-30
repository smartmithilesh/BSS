<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-file-text-o"></i> Invoice: <?= htmlspecialchars($sale['invoice_no']) ?></h3></div>
  <div class="title_right">
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=pdf&id=<?= $sale['id'] ?>" class="btn btn-pdf pull-right" target="_blank"><i class="fa fa-file-pdf-o"></i> Download PDF</a>
    <?php if($sale['net_amount']-$sale['paid_amount']>0.01): ?>
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=receivePayment&id=<?= $sale['id'] ?>" class="btn btn-success pull-right" style="margin-right:5px"><i class="fa fa-inr"></i> Receive Payment</a>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>?controller=schoolsale&action=index" class="btn btn-default pull-right" style="margin-right:5px"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>
<div class="clearfix"></div>

<div class="row">
<div class="col-md-9">
  <div class="x_panel">
    <div class="x_content" style="padding:25px">

      <!-- Invoice Header -->
      <div style="text-align:center;border-bottom:3px solid #2c3e50;padding-bottom:15px;margin-bottom:20px">
        <h2 style="color:#2c3e50;margin:0"><?= htmlspecialchars(AppSettings::get('shop_name',BASE_NAME)) ?></h2>
        <h4 style="color:#7f8c8d;margin:4px 0">SCHOOL SALE INVOICE</h4>
        <?php if(AppSettings::get('phone')||AppSettings::get('email')): ?><div><?= htmlspecialchars(trim(AppSettings::get('phone').' '.AppSettings::get('email'))) ?></div><?php endif; ?>
      </div>

      <!-- Bill To + Invoice Info -->
      <div class="row" style="margin-bottom:20px">
        <div class="col-md-7">
          <strong>Bill To:</strong><br>
          <h4 style="margin:4px 0"><?= htmlspecialchars($sale['school_name']) ?></h4>
          <div style="color:#555"><?= nl2br(htmlspecialchars($sale['school_address']??'')) ?></div>
          <?php if($sale['school_phone']??''): ?>
          <div><i class="fa fa-phone"></i> <?= htmlspecialchars($sale['school_phone']) ?></div>
          <?php endif; ?>
        </div>
        <div class="col-md-5">
          <table class="table table-condensed" style="font-size:14px">
            <tr><td><strong>Invoice No:</strong></td><td><?= htmlspecialchars($sale['invoice_no']) ?></td></tr>
            <tr><td><strong>Date:</strong></td><td><?= date('d/m/Y',strtotime($sale['sale_date'])) ?></td></tr>
            <tr><td><strong>Season:</strong></td><td><?= htmlspecialchars($sale['season_name']) ?></td></tr>
          </table>
        </div>
      </div>

      <!-- Items table -->
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
          <tr><td colspan="6" class="text-right">Gross Amount:</td><td>₹<?= number_format($sale['gross_amount'],2) ?></td></tr>
          <tr><td colspan="6" class="text-right">Discount:</td><td>(-) ₹<?= number_format($sale['discount_amount'],2) ?></td></tr>
          <tr style="background:#2c3e50;color:#fff;font-weight:bold">
            <td colspan="6" class="text-right">NET AMOUNT:</td><td>₹<?= number_format($sale['net_amount'],2) ?></td>
          </tr>
        </tfoot>
      </table>

      <?php if(!empty($sale['notes'])): ?>
      <p><strong>Notes:</strong> <?= htmlspecialchars($sale['notes']) ?></p>
      <?php endif; ?>

    </div><!-- x_content -->
  </div><!-- x_panel -->
</div>

<!-- Right sidebar: payment summary -->
<div class="col-md-3">
  <!-- Balance card -->
  <?php $outstanding=$sale['net_amount']-$sale['paid_amount']; ?>
  <div class="x_panel" style="border-left:4px solid <?= $outstanding>0.01?'#e74c3c':'#27ae60' ?>">
    <div class="x_content text-center">
      <h4><?= $outstanding>0.01?'Balance Due':'Fully Paid' ?></h4>
      <h2 style="color:<?= $outstanding>0.01?'#e74c3c':'#27ae60' ?>">₹<?= number_format(abs($outstanding),2) ?></h2>
      <small>Net: ₹<?= number_format($sale['net_amount'],2) ?> | Paid: ₹<?= number_format($sale['paid_amount'],2) ?></small>
      <?php if($outstanding>0.01): ?>
      <div style="margin-top:10px">
        <a href="<?= BASE_URL ?>?controller=schoolsale&action=receivePayment&id=<?= $sale['id'] ?>" class="btn btn-success btn-block"><i class="fa fa-inr"></i> Receive Payment</a>
      </div>
      <?php else: ?>
      <span class="badge badge-success" style="font-size:14px">PAID</span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Payment history -->
  <div class="x_panel">
    <div class="x_title"><h2>Payment History</h2><div class="clearfix"></div></div>
    <div class="x_content">
      <?php if(empty($payments)): ?>
      <p class="text-muted text-center">No payments yet</p>
      <?php else: ?>
      <?php foreach($payments as $p): ?>
      <div style="border-bottom:1px solid #eee;padding:8px 0">
        <div><strong><?= date('d/m/Y',strtotime($p['payment_date'])) ?></strong> <span class="badge"><?= ucfirst($p['payment_mode']) ?></span></div>
        <div style="color:#27ae60;font-size:16px"><strong>₹<?= number_format($p['amount'],2) ?></strong></div>
        <?php if($p['reference_no']): ?><small class="text-muted"><?= htmlspecialchars($p['reference_no']) ?></small><?php endif; ?>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
</div>
