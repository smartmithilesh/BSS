<?php
$maxChart=1;
foreach($monthly as $m) {
    $maxChart=max($maxChart,$m['purchase_amount'],$m['sales_amount'],$m['company_paid'],$m['school_received']);
}
?>
<div class="page-title">
  <div class="title_left"><h3><i class="fa fa-bar-chart"></i> Profit & Loss Report</h3></div>
</div>
<div class="clearfix"></div>

<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<form method="GET" action="<?= BASE_URL ?>" class="form-inline" style="margin-bottom:10px">
  <input type="hidden" name="controller" value="report"><input type="hidden" name="action" value="profitLoss">
  <div class="form-group" style="margin-right:6px">
    <select name="season_id" class="form-control"><option value="">All Seasons</option>
      <?php foreach($seasons as $s): ?><option value="<?= $s['id'] ?>" <?= $filters['season_id']==$s['id']?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group" style="margin-right:6px"><input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($filters['from_date']) ?>"></div>
  <div class="form-group" style="margin-right:6px"><input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($filters['to_date']) ?>"></div>
  <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
  <a href="<?= BASE_URL ?>?controller=report&action=profitLoss" class="btn btn-default">Reset</a>
</form>
</div></div></div></div>

<div class="row">
  <div class="col-md-3 col-sm-6"><div class="x_panel card-summary blue"><div class="x_content text-right"><h3>₹<?= number_format($summary['purchase_amount'],2) ?></h3><p>Purchase Amount</p></div></div></div>
  <div class="col-md-3 col-sm-6"><div class="x_panel card-summary orange"><div class="x_content text-right"><h3>₹<?= number_format($summary['company_paid'],2) ?></h3><p>Paid to Company</p></div></div></div>
  <div class="col-md-3 col-sm-6"><div class="x_panel card-summary green"><div class="x_content text-right"><h3>₹<?= number_format($summary['sales_amount'],2) ?></h3><p>School Sales</p></div></div></div>
  <div class="col-md-3 col-sm-6"><div class="x_panel card-summary red"><div class="x_content text-right"><h3>₹<?= number_format($summary['school_received'],2) ?></h3><p>Received from School</p></div></div></div>
</div>

<div class="row">
  <div class="col-md-4"><div class="x_panel"><div class="x_content text-center">
    <p>Company Balance</p><h2>₹<?= number_format($summary['company_balance'],2) ?></h2>
  </div></div></div>
  <div class="col-md-4"><div class="x_panel"><div class="x_content text-center">
    <p>School Balance</p><h2>₹<?= number_format($summary['school_balance'],2) ?></h2>
  </div></div></div>
  <div class="col-md-4"><div class="x_panel"><div class="x_content text-center">
    <p><?= $summary['profit_loss']>=0?'Profit':'Loss' ?></p>
    <h2 style="color:<?= $summary['profit_loss']>=0?'#27ae60':'#e74c3c' ?>">₹<?= number_format(abs($summary['profit_loss']),2) ?></h2>
  </div></div></div>
</div>

<div class="row"><div class="col-md-12"><div class="x_panel">
  <div class="x_title"><h2>Last 6 Months Graph</h2><div class="clearfix"></div></div>
  <div class="x_content">
    <div class="report-chart-legend">
      <span><i class="rc-purchase"></i> Purchase</span>
      <span><i class="rc-sales"></i> Sales</span>
      <span><i class="rc-paid"></i> Company Paid</span>
      <span><i class="rc-received"></i> School Received</span>
    </div>
    <div class="report-bar-chart">
      <?php foreach($monthly as $m): ?>
      <div class="report-month">
        <div class="report-bars">
          <span class="rc-purchase" style="height:<?= max(3,($m['purchase_amount']/$maxChart)*100) ?>%" title="Purchase ₹<?= number_format($m['purchase_amount'],2) ?>"></span>
          <span class="rc-sales" style="height:<?= max(3,($m['sales_amount']/$maxChart)*100) ?>%" title="Sales ₹<?= number_format($m['sales_amount'],2) ?>"></span>
          <span class="rc-paid" style="height:<?= max(3,($m['company_paid']/$maxChart)*100) ?>%" title="Paid ₹<?= number_format($m['company_paid'],2) ?>"></span>
          <span class="rc-received" style="height:<?= max(3,($m['school_received']/$maxChart)*100) ?>%" title="Received ₹<?= number_format($m['school_received'],2) ?>"></span>
        </div>
        <div class="report-month-label"><?= htmlspecialchars($m['label']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div></div></div>

<div class="row"><div class="col-md-12"><div class="x_panel"><div class="x_content">
<table class="table table-striped table-bordered">
  <thead><tr><th>Particular</th><th class="text-right">Amount</th><th>Meaning</th></tr></thead>
  <tbody>
    <tr><td>Purchase Amount</td><td class="text-right">₹<?= number_format($summary['purchase_amount'],2) ?></td><td>Total book purchases</td></tr>
    <tr><td>Paid to Company</td><td class="text-right">₹<?= number_format($summary['company_paid'],2) ?></td><td>Actual supplier payments done</td></tr>
    <tr><td>Company Balance</td><td class="text-right">₹<?= number_format($summary['company_balance'],2) ?></td><td>Purchase minus company paid</td></tr>
    <tr><td>School Sales</td><td class="text-right">₹<?= number_format($summary['sales_amount'],2) ?></td><td>Total school sale invoices</td></tr>
    <tr><td>Received from School</td><td class="text-right">₹<?= number_format($summary['school_received'],2) ?></td><td>Actual school payment received</td></tr>
    <tr><td>School Balance</td><td class="text-right">₹<?= number_format($summary['school_balance'],2) ?></td><td>Sales minus received</td></tr>
    <tr><td><strong>Profit / Loss</strong></td><td class="text-right"><strong>₹<?= number_format($summary['profit_loss'],2) ?></strong></td><td>Sales minus purchases</td></tr>
    <tr><td><strong>Cash Flow</strong></td><td class="text-right"><strong>₹<?= number_format($summary['cash_flow'],2) ?></strong></td><td>School received minus company paid</td></tr>
  </tbody>
</table>
</div></div></div></div>
