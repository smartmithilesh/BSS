<div class="page-title">
  <div class="title_left"><h3>Dashboard <?php if($season): ?><small><?= htmlspecialchars($season['name']) ?> (Active Season)</small><?php endif; ?></h3></div>
</div>
<div class="clearfix"></div>

<!-- Summary Cards -->
<div class="row">
  <div class="col-md-3 col-sm-6">
    <div class="x_panel card-summary blue">
      <div class="x_content"><div class="col-xs-3"><i class="fa fa-shopping-cart fa-3x text-info"></i></div>
      <div class="col-xs-9 text-right"><h3>₹<?= number_format($totalPurchase,2) ?></h3><p>Total Purchases</p></div><div class="clearfix"></div></div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="x_panel card-summary green">
      <div class="x_content"><div class="col-xs-3"><i class="fa fa-line-chart fa-3x text-success"></i></div>
      <div class="col-xs-9 text-right"><h3>₹<?= number_format($totalSales,2) ?></h3><p>Total Sales</p></div><div class="clearfix"></div></div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="x_panel card-summary orange">
      <div class="x_content"><div class="col-xs-3"><i class="fa fa-check-circle fa-3x" style="color:#e67e22"></i></div>
      <div class="col-xs-9 text-right"><h3>₹<?= number_format($totalCollected,2) ?></h3><p>Amount Collected</p></div><div class="clearfix"></div></div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="x_panel card-summary red">
      <div class="x_content"><div class="col-xs-3"><i class="fa fa-exclamation-circle fa-3x text-danger"></i></div>
      <div class="col-xs-9 text-right"><h3>₹<?= number_format($totalSales-$totalCollected,2) ?></h3><p>Outstanding</p></div><div class="clearfix"></div></div>
    </div>
  </div>
</div>

<?php
$maxDashChart=1;
foreach($financeMonthly as $m) {
    $maxDashChart=max($maxDashChart,$m['purchase_amount'],$m['sales_amount'],$m['company_paid'],$m['school_received']);
}
?>
<div class="row">
  <div class="col-md-8">
    <div class="x_panel">
      <div class="x_title"><h2>Purchase / Sales Graph</h2><div class="clearfix"></div></div>
      <div class="x_content">
        <div class="report-chart-legend">
          <span><i class="rc-purchase"></i> Purchase</span>
          <span><i class="rc-sales"></i> Sales</span>
          <span><i class="rc-paid"></i> Company Paid</span>
          <span><i class="rc-received"></i> School Received</span>
        </div>
        <div class="report-bar-chart compact">
          <?php foreach($financeMonthly as $m): ?>
          <div class="report-month">
            <div class="report-bars">
              <span class="rc-purchase" style="height:<?= max(3,($m['purchase_amount']/$maxDashChart)*100) ?>%" title="Purchase ₹<?= number_format($m['purchase_amount'],2) ?>"></span>
              <span class="rc-sales" style="height:<?= max(3,($m['sales_amount']/$maxDashChart)*100) ?>%" title="Sales ₹<?= number_format($m['sales_amount'],2) ?>"></span>
              <span class="rc-paid" style="height:<?= max(3,($m['company_paid']/$maxDashChart)*100) ?>%" title="Paid ₹<?= number_format($m['company_paid'],2) ?>"></span>
              <span class="rc-received" style="height:<?= max(3,($m['school_received']/$maxDashChart)*100) ?>%" title="Received ₹<?= number_format($m['school_received'],2) ?>"></span>
            </div>
            <div class="report-month-label"><?= htmlspecialchars($m['label']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="x_panel">
      <div class="x_title"><h2>Profit / Cash</h2><div class="clearfix"></div></div>
      <div class="x_content">
        <div class="finance-mini-row">
          <span>Profit / Loss</span>
          <strong class="<?= $financeSummary['profit_loss']>=0?'text-success':'text-danger' ?>">₹<?= number_format($financeSummary['profit_loss'],2) ?></strong>
        </div>
        <div class="finance-mini-row">
          <span>Cash Flow</span>
          <strong class="<?= $financeSummary['cash_flow']>=0?'text-success':'text-danger' ?>">₹<?= number_format($financeSummary['cash_flow'],2) ?></strong>
        </div>
        <div class="finance-mini-row">
          <span>Company Balance</span>
          <strong>₹<?= number_format($financeSummary['company_balance'],2) ?></strong>
        </div>
        <div class="finance-mini-row">
          <span>School Balance</span>
          <strong>₹<?= number_format($financeSummary['school_balance'],2) ?></strong>
        </div>
        <a href="<?= BASE_URL ?>?controller=report&action=profitLoss" class="btn btn-primary btn-sm btn-block">View Full Report</a>
      </div>
    </div>
  </div>
</div>

<!-- Count Cards -->
<div class="row">
  <div class="col-md-4"><div class="x_panel"><div class="x_content text-center">
    <h2><?= $companies ?></h2><p><i class="fa fa-building"></i> Companies</p>
    <a href="<?= BASE_URL ?>?controller=company&action=index" class="btn btn-sm btn-default">View All</a>
  </div></div></div>
  <div class="col-md-4"><div class="x_panel"><div class="x_content text-center">
    <h2><?= $schools ?></h2><p><i class="fa fa-university"></i> Schools</p>
    <a href="<?= BASE_URL ?>?controller=school&action=index" class="btn btn-sm btn-default">View All</a>
  </div></div></div>
  <div class="col-md-4"><div class="x_panel"><div class="x_content text-center">
    <h2><?= $books ?></h2><p><i class="fa fa-book"></i> Active Books</p>
    <a href="<?= BASE_URL ?>?controller=book&action=index" class="btn btn-sm btn-default">View All</a>
  </div></div></div>
</div>

<!-- Recent Sales -->
<div class="row">
  <div class="col-md-12">
    <div class="x_panel">
      <div class="x_title"><h2>Recent Sales</h2>
        <div class="nav navbar-right panel_toolbox">
          <a href="<?= BASE_URL ?>?controller=schoolsale&action=create" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> New Sale</a>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table class="table table-striped table-hover">
          <thead><tr><th>Invoice</th><th>School</th><th>Date</th><th>Amount</th><th>Outstanding</th><th>Action</th></tr></thead>
          <tbody>
          <?php foreach($recentSales as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['invoice_no']) ?></td>
            <td><?= htmlspecialchars($s['school_name']) ?></td>
            <td><?= date('d/m/Y',strtotime($s['sale_date'])) ?></td>
            <td>₹<?= number_format($s['net_amount'],2) ?></td>
            <td><?php $out=$s['net_amount']-$s['paid_amount']; ?>
              <span class="badge <?= $out>0?'badge-danger':'badge-success' ?>">₹<?= number_format($out,2) ?></span>
            </td>
            <td><a href="<?= BASE_URL ?>?controller=schoolsale&action=view&id=<?= $s['id'] ?>" class="btn btn-xs btn-info">View</a></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($recentSales)): ?><tr><td colspan="6" class="text-center text-muted">No sales yet</td></tr><?php endif; ?>
          </tbody>
        </table>
        <a href="<?= BASE_URL ?>?controller=schoolsale&action=index" class="btn btn-default btn-sm">View All Sales →</a>
      </div>
    </div>
  </div>
</div>
