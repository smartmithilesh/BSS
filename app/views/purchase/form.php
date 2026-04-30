<div class="page-title"><div class="title_left"><h3><i class="fa fa-shopping-cart"></i> New Purchase</h3></div></div>
<div class="clearfix"></div>

<form method="POST" action="<?= BASE_URL ?>?controller=purchase&action=store" id="purchaseForm">
<div class="row">
  <!-- Left: Purchase header -->
  <div class="col-md-4">
    <div class="x_panel">
      <div class="x_title"><h2>Purchase Details</h2><div class="clearfix"></div></div>
      <div class="x_content">
        <div class="form-group"><label>Season *</label>
          <select name="season_id" class="form-control" required>
            <option value="">-- Select Season --</option>
            <?php foreach($seasons as $s): ?>
            <option value="<?= $s['id'] ?>" <?= ($activeSeason&&$activeSeason['id']==$s['id'])?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Company *</label>
          <select name="company_id" id="companyId" class="form-control" required>
            <option value="">-- Select Company --</option>
            <?php foreach($companies as $co): ?>
            <option value="<?= $co['id'] ?>"><?= htmlspecialchars($co['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Invoice No</label>
          <input type="text" name="invoice_no" class="form-control" placeholder="Leave blank to auto-generate"></div>
        <div class="form-group"><label>Purchase Date *</label>
          <input type="date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
        <div class="form-group"><label>Notes</label>
          <textarea name="notes" class="form-control" rows="2"></textarea></div>
      </div>
    </div>
  </div>

  <!-- Right: Book items -->
  <div class="col-md-8">
    <div class="x_panel">
      <div class="x_title">
        <h2>Add Books</h2>
        <div class="nav navbar-right panel_toolbox">
          <div class="form-inline">
            <select id="addClassId" class="form-control form-control-sm" style="width:150px;margin-right:5px">
              <option value="">Class</option>
              <?php foreach($classes as $cl): ?>
              <option value="<?= $cl['id'] ?>"><?= htmlspecialchars($cl['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" id="loadBooksBtn" class="btn btn-sm btn-info"><i class="fa fa-search"></i> Load Books</button>
          </div>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">

        <!-- Book picker -->
        <div id="bookPickerArea" style="display:none;margin-bottom:15px;background:#f8f9fa;padding:10px;border-radius:4px">
          <table class="table table-sm table-bordered" id="bookPickerTable">
            <thead class="thead-dark"><tr><th>Book</th><th style="width:80px">Qty</th><th style="width:100px">Rate</th><th style="width:80px">Disc%</th><th></th></tr></thead>
            <tbody id="bookPickerBody"></tbody>
          </table>
          <button type="button" id="addSelectedBtn" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Selected</button>
        </div>

        <!-- Items table -->
        <table class="table table-bordered table-sm" id="itemsTable">
          <thead style="background:#2c3e50;color:#fff"><tr><th>#</th><th>Book</th><th>Class</th><th>Qty</th><th>Rate (₹)</th><th>Disc%</th><th>Amount (₹)</th><th></th></tr></thead>
          <tbody id="itemsBody">
            <tr id="emptyRow"><td colspan="8" class="text-center text-muted">No items added yet</td></tr>
          </tbody>
          <tfoot>
            <tr><td colspan="4"></td><td colspan="2" class="text-right"><strong>Gross:</strong></td><td id="grossTotal">0.00</td><td></td></tr>
            <tr><td colspan="4"></td><td colspan="2" class="text-right"><strong>Discount:</strong></td><td id="discTotal">0.00</td><td></td></tr>
            <tr style="background:#2c3e50;color:#fff"><td colspan="4"></td><td colspan="2" class="text-right"><strong>Net Amount:</strong></td><td id="netTotal"><strong>0.00</strong></td><td></td></tr>
          </tfoot>
        </table>

        <button type="submit" class="btn btn-success btn-lg" id="saveBtn" disabled><i class="fa fa-save"></i> Save Purchase</button>
        <a href="<?= BASE_URL ?>?controller=purchase&action=index" class="btn btn-default btn-lg">Cancel</a>
      </div>
    </div>
  </div>
</div>
</form>

<script>
var BASE_URL = '<?= BASE_URL ?>';
var itemCount = 0;
var itemsData = {}; // store name+class for display

$('#loadBooksBtn').on('click', function(){
  var companyId = $('#companyId').val();
  var classId   = $('#addClassId').val();
  if(!companyId){ alert('Please select a company first.'); return; }
  if(!classId)  { alert('Please select a class.'); return; }

  $.ajax({
    url: BASE_URL+'?controller=purchase&action=getBooks',
    type: 'GET',
    dataType: 'json',
    data: {company_id:companyId, class_id:classId},
    success: function(books){
      var $tbody = $('#bookPickerBody').empty();
      if(books.error) { 
        alert('Error: '+books.error); 
        return;
      }
      if(!books || !books.length){ 
        $tbody.html('<tr><td colspan="5" class="text-center text-muted">No books found for this company & class</td></tr>'); 
      } else {
        books.forEach(function(b){
          $tbody.append(
            '<tr data-id="'+b.id+'" data-name="'+escHtml(b.name)+'" data-class="'+escHtml(b.class_name)+'" data-rate="'+b.purchase_rate+'">' +
            '<td><label><input type="checkbox" class="pick-chk" value="'+b.id+'"> '+escHtml(b.name)+'</label></td>'+
            '<td><input type="number" class="form-control form-control-sm pick-qty" value="1" min="1" style="width:70px"></td>'+
            '<td><input type="number" class="form-control form-control-sm pick-rate" value="'+b.purchase_rate+'" step="0.01" style="width:90px"></td>'+
            '<td><input type="number" class="form-control form-control-sm pick-disc" value="0" step="0.01" style="width:70px"></td>'+
            '<td><button type="button" class="btn btn-xs btn-success add-one-btn">Add</button></td>'+
            '</tr>'
          );
        });
      }
      $('#bookPickerArea').show();
    },
    error: function(xhr, status, error){
      alert('Error loading books: '+error);
      console.log('AJAX Error:', xhr, status, error);
    }
  });
});

// Add one book directly
$(document).on('click', '.add-one-btn', function(){
  var $tr = $(this).closest('tr');
  addItemRow($tr.data('id'), $tr.data('name'), $tr.data('class'), $tr.find('.pick-qty').val(), $tr.find('.pick-rate').val(), $tr.find('.pick-disc').val());
});

// Add all selected
$('#addSelectedBtn').on('click', function(){
  $('#bookPickerBody tr').each(function(){
    if($(this).find('.pick-chk').is(':checked')){
      var $tr=$(this);
      addItemRow($tr.data('id'),$tr.data('name'),$tr.data('class'),$tr.find('.pick-qty').val(),$tr.find('.pick-rate').val(),$tr.find('.pick-disc').val());
      $(this).find('.pick-chk').prop('checked',false);
    }
  });
});

function addItemRow(bookId, bookName, className, qty, rate, disc){
  qty=parseFloat(qty)||1; rate=parseFloat(rate)||0; disc=parseFloat(disc)||0;
  itemCount++;
  var idx = itemCount;
  $('#emptyRow').remove();
  var amount = qty*rate*(1-disc/100);
  var row = '<tr id="item-row-'+idx+'">' +
    '<td>'+idx+'</td>' +
    '<td>'+escHtml(bookName)+'<input type="hidden" name="items['+idx+'][book_id]" value="'+bookId+'"></td>' +
    '<td>'+escHtml(className)+'</td>' +
    '<td><input type="number" class="form-control input-sm row-qty" name="items['+idx+'][qty]" value="'+qty+'" min="1" style="width:70px"></td>' +
    '<td><input type="number" class="form-control input-sm row-rate" name="items['+idx+'][rate]" value="'+rate+'" step="0.01" style="width:90px"></td>' +
    '<td><input type="number" class="form-control input-sm row-disc" name="items['+idx+'][discount_pct]" value="'+disc+'" step="0.01" style="width:70px"></td>' +
    '<td class="row-amount">'+amount.toFixed(2)+'</td>' +
    '<td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>' +
    '</tr>';
  $('#itemsBody').append(row);
  recalc();
  $('#saveBtn').prop('disabled', false);
}

$(document).on('input', '.row-qty,.row-rate,.row-disc', function(){
  var $tr=$(this).closest('tr');
  var qty=parseFloat($tr.find('.row-qty').val())||0;
  var rate=parseFloat($tr.find('.row-rate').val())||0;
  var disc=parseFloat($tr.find('.row-disc').val())||0;
  var amount=qty*rate*(1-disc/100);
  $tr.find('.row-amount').text(amount.toFixed(2));
  recalc();
});

$(document).on('click', '.remove-row', function(){
  $(this).closest('tr').remove();
  recalc();
  if($('#itemsBody tr').length===0){
    $('#itemsBody').append('<tr id="emptyRow"><td colspan="8" class="text-center text-muted">No items added yet</td></tr>');
    $('#saveBtn').prop('disabled',true);
  }
});

function recalc(){
  var gross=0, disc=0;
  $('#itemsBody tr:not(#emptyRow)').each(function(){
    var $tr=$(this);
    var qty=parseFloat($tr.find('.row-qty').val())||0;
    var rate=parseFloat($tr.find('.row-rate').val())||0;
    var d=parseFloat($tr.find('.row-disc').val())||0;
    var g=qty*rate; var rd=g*(d/100);
    gross+=g; disc+=rd;
  });
  $('#grossTotal').text(gross.toFixed(2));
  $('#discTotal').text(disc.toFixed(2));
  $('#netTotal').html('<strong>'+(gross-disc).toFixed(2)+'</strong>');
}

function escHtml(t){ return $('<div>').text(t).html(); }
</script>
