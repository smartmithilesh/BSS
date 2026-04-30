<div class="page-title"><div class="title_left"><h3><i class="fa fa-inr"></i> Receive Payment</h3></div></div>
<div class="clearfix"></div>

<?php
$hasSale=!empty($sale);
$outstanding=$hasSale ? ((float)$sale['net_amount']-(float)$sale['paid_amount']) : 0;
?>

<div class="row"><div class="col-md-6 col-md-offset-3">
<div class="x_panel">
  <div class="x_title"><h2><?= $hasSale ? 'Payment for Invoice: '.htmlspecialchars($sale['invoice_no']) : 'Select Invoice' ?></h2><div class="clearfix"></div></div>
  <div class="x_content">
    <?php if(!$hasSale && empty($outstandingSales)): ?>
      <div class="alert alert-info">No outstanding school invoices found.</div>
      <a href="<?= BASE_URL ?>?controller=schoolsale&action=payments" class="btn btn-default">Back</a>
    <?php else: ?>
    <form method="POST" action="<?= BASE_URL ?>?controller=schoolsale&action=storePayment" id="receivePaymentForm">
      <input type="hidden" name="return_to" value="<?= $hasSale ? 'invoice' : 'payments' ?>">

      <?php if($hasSale): ?>
        <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
      <?php else: ?>
        <div class="form-group"><label>Invoice *</label>
          <select name="sale_id" id="saleId" class="form-control" required>
            <option value="">-- Select Outstanding Invoice --</option>
            <?php foreach($outstandingSales as $s): ?>
              <?php $bal=(float)$s['net_amount']-(float)$s['paid_amount']; ?>
              <option value="<?= $s['id'] ?>"
                data-school="<?= htmlspecialchars($s['school_name']) ?>"
                data-invoice="<?= htmlspecialchars($s['invoice_no']) ?>"
                data-net="<?= (float)$s['net_amount'] ?>"
                data-paid="<?= (float)$s['paid_amount'] ?>"
                data-balance="<?= $bal ?>">
                <?= htmlspecialchars($s['school_name']) ?> - <?= htmlspecialchars($s['invoice_no']) ?> - ₹<?= number_format($bal,2) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>

      <div class="alert alert-info" id="paymentSummary" style="<?= $hasSale ? '' : 'display:none' ?>">
        <strong>School:</strong> <span id="summarySchool"><?= $hasSale ? htmlspecialchars($sale['school_name']) : '' ?></span><br>
        <strong>Invoice:</strong> <span id="summaryInvoice"><?= $hasSale ? htmlspecialchars($sale['invoice_no']) : '' ?></span><br>
        <strong>Net Amount:</strong> ₹<span id="summaryNet"><?= $hasSale ? number_format($sale['net_amount'],2) : '0.00' ?></span><br>
        <strong>Paid So Far:</strong> ₹<span id="summaryPaid"><?= $hasSale ? number_format($sale['paid_amount'],2) : '0.00' ?></span><br>
        <strong>Balance Due:</strong> <span style="color:#e74c3c;font-weight:bold">₹<span id="summaryBalance"><?= $hasSale ? number_format($outstanding,2) : '0.00' ?></span></span>
      </div>

      <div class="form-group"><label>Amount (₹) *</label>
        <input type="number" name="amount" id="paymentAmount" class="form-control" step="0.01" max="<?= $hasSale ? $outstanding : '' ?>" value="<?= $hasSale ? $outstanding : '' ?>" required></div>
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
      <div class="form-group"><label>Reference No / Cheque No</label>
        <input type="text" name="reference_no" class="form-control" placeholder="Optional"></div>
      <div class="form-group"><label>Notes</label>
        <textarea name="notes" class="form-control" rows="2"></textarea></div>

      <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Record Payment</button>
      <a href="<?= $hasSale ? BASE_URL.'?controller=schoolsale&action=view&id='.$sale['id'] : BASE_URL.'?controller=schoolsale&action=payments' ?>" class="btn btn-default">Cancel</a>
    </form>
    <?php endif; ?>
  </div>
</div>
</div></div>

<script>
$('#saleId').on('change', function(){
  var $opt=$(this).find('option:selected');
  var balance=parseFloat($opt.data('balance'))||0;
  if(!$(this).val()){
    $('#paymentSummary').hide();
    $('#paymentAmount').val('').removeAttr('max');
    return;
  }
  $('#summarySchool').text($opt.data('school')||'');
  $('#summaryInvoice').text($opt.data('invoice')||'');
  $('#summaryNet').text((parseFloat($opt.data('net'))||0).toFixed(2));
  $('#summaryPaid').text((parseFloat($opt.data('paid'))||0).toFixed(2));
  $('#summaryBalance').text(balance.toFixed(2));
  $('#paymentAmount').attr('max',balance).val(balance.toFixed(2));
  $('#paymentSummary').show();
});
</script>
