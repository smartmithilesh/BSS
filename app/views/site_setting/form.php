<div class="page-title"><div class="title_left"><h3><i class="fa fa-sliders"></i> Site Settings</h3></div></div>
<div class="clearfix"></div>
<div class="row"><div class="col-md-10"><div class="x_panel"><div class="x_content">
<form method="POST" action="<?= BASE_URL ?>?controller=siteSetting&action=save" enctype="multipart/form-data">
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Shop Name *</label>
      <input type="text" name="shop_name" class="form-control" value="<?= htmlspecialchars($settings['shop_name']??'') ?>" required></div></div>
    <div class="col-md-6"><div class="form-group"><label>Tagline</label>
      <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($settings['tagline']??'') ?>"></div></div>
  </div>
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Brand Logo</label>
      <input type="file" name="brand_logo" class="form-control" accept="image/*">
      <?php if(!empty($settings['brand_logo'])): ?><p class="help-block">Current: <?= htmlspecialchars($settings['brand_logo']) ?></p><?php endif; ?>
    </div></div>
    <div class="col-md-2"><div class="form-group"><label>Currency</label>
      <input type="text" name="currency_symbol" class="form-control" value="<?= htmlspecialchars($settings['currency_symbol']??'₹') ?>"></div></div>
    <div class="col-md-2"><div class="form-group"><label>Purchase Prefix</label>
      <input type="text" name="purchase_prefix" class="form-control" value="<?= htmlspecialchars($settings['purchase_prefix']??'PUR') ?>"></div></div>
    <div class="col-md-2"><div class="form-group"><label>Sale Prefix</label>
      <input type="text" name="sale_prefix" class="form-control" value="<?= htmlspecialchars($settings['sale_prefix']??'INV') ?>"></div></div>
  </div>
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Timezone</label>
      <input type="text" name="timezone" class="form-control" value="<?= htmlspecialchars($settings['timezone']??'Asia/Kolkata') ?>"></div></div>
  </div>
  <div class="row">
    <div class="col-md-6"><div class="form-group"><label>Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone']??'') ?>"></div></div>
    <div class="col-md-6"><div class="form-group"><label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($settings['email']??'') ?>"></div></div>
  </div>
  <div class="form-group"><label>Address</label>
    <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($settings['address']??'') ?></textarea></div>
  <div class="form-group"><label>Invoice Footer Text</label>
    <input type="text" name="invoice_footer" class="form-control" value="<?= htmlspecialchars($settings['invoice_footer']??'') ?>"></div>
  <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Settings</button>
</form>
</div></div></div></div>
