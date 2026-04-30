<div class="page-title"><div class="title_left"><h3><i class="fa fa-line-chart"></i> New School Sale</h3></div></div>
<div class="clearfix"></div>

<form method="POST" action="<?= BASE_URL ?>?controller=schoolsale&action=store" id="saleForm">
<div class="row">
  <!-- Left panel -->
  <div class="col-md-4">
    <div class="x_panel">
      <div class="x_title"><h2>Sale Details</h2><div class="clearfix"></div></div>
      <div class="x_content">
        <div class="form-group"><label>Season *</label>
          <select name="season_id" id="seasonId" class="form-control" required>
            <option value="">-- Select Season --</option>
            <?php foreach($seasons as $s): ?>
            <option value="<?= $s['id'] ?>" <?= ($activeSeason&&$activeSeason['id']==$s['id'])?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>School *</label>
          <select name="school_id" class="form-control" required>
            <option value="">-- Select School --</option>
            <?php foreach($schools as $sc): ?>
            <option value="<?= $sc['id'] ?>"><?= htmlspecialchars($sc['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Invoice No</label>
          <input type="text" name="invoice_no" class="form-control" placeholder="Auto-generated if blank"></div>
        <div class="form-group"><label>Sale Date *</label>
          <input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
        <div class="form-group"><label>Notes</label>
          <textarea name="notes" class="form-control" rows="2"></textarea></div>
      </div>
    </div>
  </div>

  <!-- Right panel -->
  <div class="col-md-8">
    <div class="x_panel">
      <div class="x_title">
        <h2>Select Books</h2>
        <div class="nav navbar-right panel_toolbox">
          <div class="form-inline">
            <select id="addClassId" class="form-control form-control-sm" style="width:160px;margin-right:5px">
              <option value="">-- Class --</option>
              <?php foreach($classes as $cl): ?>
              <option value="<?= $cl['id'] ?>"><?= htmlspecialchars($cl['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" id="loadBooksBtn" class="btn btn-sm btn-info"><i class="fa fa-search"></i> Load</button>
          </div>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">

        <!-- Book Picker -->
        <div id="bookPickerArea" style="display:none;background:#f0f8ff;padding:10px;border-radius:4px;margin-bottom:15px">
          <div id="bookPickerContent"></div>
        </div>

        <!-- Items table -->
        <table class="table table-bordered table-sm" id="itemsTable">
          <thead style="background:#2c3e50;color:#fff">
            <tr><th>#</th><th>Book</th><th>Class</th><th>Avail</th><th>Qty</th><th>Rate(₹)</th><th>Disc%</th><th>Amount(₹)</th><th></th></tr>
          </thead>
          <tbody id="itemsBody">
            <tr id="emptyRow"><td colspan="9" class="text-center text-muted">No items added</td></tr>
          </tbody>
          <tfoot>
            <tr><td colspan="5"></td><td colspan="2" class="text-right"><strong>Gross:</strong></td><td id="grossTotal">0.00</td><td></td></tr>
            <tr><td colspan="5"></td><td colspan="2" class="text-right"><strong>Discount:</strong></td><td id="discTotal">0.00</td><td></td></tr>
            <tr style="background:#27ae60;color:#fff">
              <td colspan="5"></td><td colspan="2" class="text-right"><strong>NET AMOUNT:</strong></td>
              <td id="netTotal"><strong>0.00</strong></td><td></td>
            </tr>
          </tfoot>
        </table>

        <button type="submit" class="btn btn-success btn-lg" id="saveBtn" disabled>
          <i class="fa fa-save"></i> Create Invoice
        </button>
        <a href="<?= BASE_URL ?>?controller=schoolsale&action=index" class="btn btn-default btn-lg">Cancel</a>
      </div>
    </div>
  </div>
</div>
</form>

<script>
var BASE_URL = '<?= BASE_URL ?>';
var itemCount = 0;
var addedBooks = {};

$('#loadBooksBtn').on('click', function(){
  var seasonId = $('#seasonId').val();
  var classId  = $('#addClassId').val();
  if(!seasonId){ alert('Please select a season first.'); return; }
  if(!classId) { alert('Please select a class.'); return; }

      $.getJSON(BASE_URL+'?controller=schoolsale&action=getBooks', {class_id:classId, season_id:seasonId}, function(books){
    var html = '';
    if(books.error){ alert('Error: '+books.error); return; }
    if(!books.length){
      html = '<p class="text-muted text-center">No books available for this class / season.</p>';
    } else {
      html = '<table class="table table-sm table-bordered"><thead class="thead-dark"><tr><th>Book</th><th>Available</th><th>Qty</th><th>Rate(₹)</th><th>Disc%</th><th></th></tr></thead><tbody>';
      books.forEach(function(b){
        var disabled = (b.available_qty<=0) ? 'disabled' : '';
        var badge    = (b.available_qty<=0) ? '<span class="badge badge-danger">Out</span>' : '<span class="badge badge-success">'+b.available_qty+'</span>';
        html += '<tr data-id="'+b.id+'" data-name="'+escHtml(b.name)+'" data-class="'+escHtml(b.class_name)+'" data-avail="'+b.available_qty+'">'+
          '<td>'+escHtml(b.name)+'</td>'+
          '<td>'+badge+'</td>'+
          '<td><input type="number" class="form-control form-control-sm pick-qty" value="1" min="1" max="'+b.available_qty+'" style="width:65px" '+disabled+'></td>'+
          '<td><input type="number" class="form-control form-control-sm pick-rate" value="'+b.sale_rate+'" step="0.01" style="width:85px" '+disabled+'></td>'+
          '<td><input type="number" class="form-control form-control-sm pick-disc" value="'+b.discount_pct+'" step="0.01" style="width:65px" '+disabled+'></td>'+
          '<td><button type="button" class="btn btn-xs btn-success add-one-btn" '+disabled+'><i class="fa fa-plus"></i></button></td>'+
          '</tr>';
      });
      html += '</tbody></table>';
    }
    $('#bookPickerContent').html(html);
    $('#bookPickerArea').show();
  });
});

$(document).on('click', '.add-one-btn', function(){
  var $tr = $(this).closest('tr');
  var bookId = $tr.data('id');
  if(addedBooks[bookId]){ alert('This book is already in the list. Adjust qty instead.'); return; }
  var avail = parseInt($tr.data('avail'))||0;
  var qty   = parseFloat($tr.find('.pick-qty').val())||1;
  if(qty > avail){ alert('Quantity cannot exceed available stock ('+avail+').'); return; }
  addedBooks[bookId] = true;
  addItemRow(bookId, $tr.data('name'), $tr.data('class'), avail, qty, $tr.find('.pick-rate').val(), $tr.find('.pick-disc').val());
});

function addItemRow(bookId, bookName, className, avail, qty, rate, disc){
  qty=parseFloat(qty)||1; rate=parseFloat(rate)||0; disc=parseFloat(disc)||0;
  itemCount++;
  var idx=itemCount;
  $('#emptyRow').remove();
  var amount=qty*rate*(1-disc/100);
  $('#itemsBody').append(
    '<tr id="item-row-'+idx+'">'+
    '<td>'+idx+'</td>'+
    '<td>'+escHtml(bookName)+'<input type="hidden" name="items['+idx+'][book_id]" value="'+bookId+'"></td>'+
    '<td>'+escHtml(className)+'</td>'+
    '<td><small class="text-muted">'+avail+'</small></td>'+
    '<td><input type="number" class="form-control input-sm row-qty" name="items['+idx+'][qty]" value="'+qty+'" min="1" max="'+avail+'" style="width:65px"></td>'+
    '<td><input type="number" class="form-control input-sm row-rate" name="items['+idx+'][rate]" value="'+rate+'" step="0.01" style="width:80px"></td>'+
    '<td><input type="number" class="form-control input-sm row-disc" name="items['+idx+'][discount_pct]" value="'+disc+'" step="0.01" style="width:65px"></td>'+
    '<td class="row-amount">'+amount.toFixed(2)+'</td>'+
    '<td><button type="button" class="btn btn-xs btn-danger remove-row" data-book="'+bookId+'"><i class="fa fa-times"></i></button></td>'+
    '</tr>'
  );
  recalc();
  $('#saveBtn').prop('disabled',false);
}

$(document).on('input', '.row-qty,.row-rate,.row-disc', function(){
  var $tr=$(this).closest('tr');
  var qty=parseFloat($tr.find('.row-qty').val())||0;
  var rate=parseFloat($tr.find('.row-rate').val())||0;
  var disc=parseFloat($tr.find('.row-disc').val())||0;
  $tr.find('.row-amount').text((qty*rate*(1-disc/100)).toFixed(2));
  recalc();
});

$(document).on('click', '.remove-row', function(){
  var bookId=$(this).data('book');
  delete addedBooks[bookId];
  $(this).closest('tr').remove();
  recalc();
  if($('#itemsBody tr').length===0){
    $('#itemsBody').append('<tr id="emptyRow"><td colspan="9" class="text-center text-muted">No items added</td></tr>');
    $('#saveBtn').prop('disabled',true);
  }
});

function recalc(){
  var gross=0,disc=0;
  $('#itemsBody tr:not(#emptyRow)').each(function(){
    var qty=parseFloat($(this).find('.row-qty').val())||0;
    var rate=parseFloat($(this).find('.row-rate').val())||0;
    var d=parseFloat($(this).find('.row-disc').val())||0;
    var g=qty*rate; gross+=g; disc+=g*(d/100);
  });
  $('#grossTotal').text(gross.toFixed(2));
  $('#discTotal').text(disc.toFixed(2));
  $('#netTotal').html('<strong>'+(gross-disc).toFixed(2)+'</strong>');
}

function escHtml(t){ return $('<div>').text(t).html(); }
</script>
