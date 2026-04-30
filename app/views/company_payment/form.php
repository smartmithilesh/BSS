<div class="page-title"><div class="title_left"><h3><i class="fa fa-money"></i> Pay Company</h3></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-6 col-md-offset-3"><div class="x_panel"><div class="x_content">
<form method="POST" action="<?= BASE_URL ?>?controller=companyPayment&action=store">
  <div class="form-group"><label>Season *</label>
    <select name="season_id" class="form-control" required>
      <option value="">-- Select Season --</option>
      <?php foreach($seasons as $s): ?><option value="<?= $s['id'] ?>" <?= ($activeSeason&&$activeSeason['id']==$s['id'])?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group"><label>Company *</label>
    <select name="company_id" class="form-control" required>
      <option value="">-- Select Company --</option>
      <?php foreach($companies as $co): ?><option value="<?= $co['id'] ?>"><?= htmlspecialchars($co['name']) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="form-group"><label>Amount (₹) *</label>
    <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required></div>
  <div class="form-group"><label>Payment Date *</label>
    <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
  <div class="form-group"><label>Payment Mode</label>
    <select name="payment_mode" class="form-control">
      <option value="cash">Cash</option>
      <option value="cheque">Cheque</option>
      <option value="online">Online</option>
      <option value="upi">UPI</option>
    </select>
  </div>
  <div class="form-group"><label>Reference / Cheque No</label>
    <input type="text" name="reference_no" class="form-control"></div>
  <div class="form-group"><label>Notes</label>
    <textarea name="notes" class="form-control" rows="2"></textarea></div>
  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Record Payment</button>
  <a href="<?= BASE_URL ?>?controller=companyPayment&action=index" class="btn btn-default">Cancel</a>
</form>
</div></div></div></div>
