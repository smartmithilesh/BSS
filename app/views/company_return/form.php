<div class="page-title"><div class="title_left"><h3><i class="fa fa-undo"></i> Return Books to Company</h3></div></div>
<div class="clearfix"></div>

<form method="POST" action="<?= BASE_URL ?>?controller=companyReturn&action=store" id="returnForm">
<div class="row">
  <div class="col-md-4">
    <div class="x_panel">
      <div class="x_title"><h2>Return Details</h2><div class="clearfix"></div></div>
      <div class="x_content">
        <div class="form-group"><label>Season *</label>
          <select name="season_id" id="seasonId" class="form-control" required>
            <option value="">-- Select Season --</option>
            <?php foreach($seasons as $s): ?><option value="<?= $s['id'] ?>" <?= ($activeSeason&&$activeSeason['id']==$s['id'])?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Company *</label>
          <select name="company_id" id="companyId" class="form-control" required>
            <option value="">-- Select Company --</option>
            <?php foreach($companies as $co): ?><option value="<?= $co['id'] ?>"><?= htmlspecialchars($co['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Return Date *</label>
          <input type="date" name="return_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
        <div class="form-group"><label>Reference No</label>
          <input type="text" name="reference_no" class="form-control"></div>
        <div class="form-group"><label>Notes</label>
          <textarea name="notes" class="form-control" rows="2"></textarea></div>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="x_panel">
      <div class="x_title">
        <h2>Add Return Items</h2>
        <div class="nav navbar-right panel_toolbox">
          <div class="form-inline">
            <select id="addClassId" class="form-control form-control-sm" style="width:150px;margin-right:5px">
              <option value="">Class</option>
              <?php foreach($classes as $cl): ?><option value="<?= $cl['id'] ?>"><?= htmlspecialchars($cl['name']) ?></option><?php endforeach; ?>
            </select>
            <button type="button" id="loadBooksBtn" class="btn btn-sm btn-info"><i class="fa fa-search"></i> Load Books</button>
          </div>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div id="bookPickerArea" style="display:none;background:#fff9e6;padding:10px;border-radius:4px;margin-bottom:15px">
          <div id="bookPickerContent"></div>
        </div>
        <table class="table table-bordered table-sm" id="itemsTable">
          <thead style="background:#2c3e50;color:#fff"><tr><th>#</th><th>Book</th><th>Available</th><th>Qty</th><th>Rate(₹)</th><th>Amount</th><th></th></tr></thead>
          <tbody id="itemsBody"><tr id="emptyRow"><td colspan="7" class="text-center text-muted">No items added</td></tr></tbody>
          <tfoot>
            <tr style="background:#2c3e50;color:#fff"><td colspan="4"></td><td colspan="1" class="text-right"><strong>Total:</strong></td><td id="totalAmt"><strong>0.00</strong></td><td></td></tr>
          </tfoot>
        </table>
        <button type="submit" class="btn btn-warning btn-lg" id="saveBtn" disabled><i class="fa fa-save"></i> Save Return</button>
        <a href="<?= BASE_URL ?>?controller=companyReturn&action=index" class="btn btn-default btn-lg">Cancel</a>
      </div>
    </div>
  </div>
</div>
</form>

<script>
var BASE_URL='<?= BASE_URL ?>';
var itemCount=0;
$('#loadBooksBtn').on('click',function(){
  var sid=$('#seasonId').val(),cid=$('#companyId').val(),clid=$('#addClassId').val();
  if(!sid||!cid||!clid){alert('Select Season, Company and Class first.');return;}
  $.getJSON(BASE_URL+'?controller=companyReturn&action=getBooks',{season_id:sid,company_id:cid,class_id:clid},function(books){
    var html='';
    if(!books.length){html='<p class="text-muted text-center">No books with stock found.</p>';}
    else{
      html='<table class="table table-sm table-bordered"><thead class="thead-dark"><tr><th>Book</th><th>Stock</th><th>Qty</th><th>Rate(₹)</th><th></th></tr></thead><tbody>';
      books.forEach(function(b){
        var d=(b.available_qty<=0)?'disabled':'';
        html+='<tr data-id="'+b.id+'" data-name="'+escHtml(b.name)+'" data-avail="'+b.available_qty+'">'+
          '<td>'+escHtml(b.name)+'</td><td>'+b.available_qty+'</td>'+
          '<td><input type="number" class="form-control form-control-sm pick-qty" value="1" min="1" max="'+b.available_qty+'" style="width:70px" '+d+'></td>'+
          '<td><input type="number" class="form-control form-control-sm pick-rate" value="'+b.rate+'" step="0.01" style="width:90px" '+d+'></td>'+
          '<td><button type="button" class="btn btn-xs btn-warning add-ret-btn" '+d+'>Return</button></td></tr>';
      });
      html+='</tbody></table>';
    }
    $('#bookPickerContent').html(html);
    $('#bookPickerArea').show();
  });
});
$(document).on('click','.add-ret-btn',function(){
  var $tr=$(this).closest('tr');
  var qty=parseFloat($tr.find('.pick-qty').val())||1;
  var avail=parseInt($tr.data('avail'));
  if(qty>avail){alert('Cannot return more than available stock ('+avail+').');return;}
  itemCount++;
  var rate=parseFloat($tr.find('.pick-rate').val())||0;
  var amt=qty*rate;
  $('#emptyRow').remove();
  $('#itemsBody').append('<tr><td>'+itemCount+'</td><td>'+escHtml($tr.data('name'))+
    '<input type="hidden" name="items['+itemCount+'][book_id]" value="'+$tr.data('id')+'"></td>'+
    '<td>'+avail+'</td>'+
    '<td><input type="number" class="form-control input-sm row-qty" name="items['+itemCount+'][qty]" value="'+qty+'" min="1" max="'+avail+'" style="width:70px"></td>'+
    '<td><input type="number" class="form-control input-sm row-rate" name="items['+itemCount+'][rate]" value="'+rate+'" step="0.01" style="width:90px"></td>'+
    '<td class="row-amount">'+amt.toFixed(2)+'</td>'+
    '<td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td></tr>');
  recalc();
  $('#saveBtn').prop('disabled',false);
});
$(document).on('input','.row-qty,.row-rate',function(){
  var $tr=$(this).closest('tr');
  $tr.find('.row-amount').text((parseFloat($tr.find('.row-qty').val()||0)*parseFloat($tr.find('.row-rate').val()||0)).toFixed(2));
  recalc();
});
$(document).on('click','.remove-row',function(){
  $(this).closest('tr').remove();recalc();
  if(!$('#itemsBody tr').length){$('#itemsBody').append('<tr id="emptyRow"><td colspan="7" class="text-center text-muted">No items added</td></tr>');$('#saveBtn').prop('disabled',true);}
});
function recalc(){var t=0;$('#itemsBody tr:not(#emptyRow) .row-amount').each(function(){t+=parseFloat($(this).text())||0;});$('#totalAmt').html('<strong>'+t.toFixed(2)+'</strong>');}
function escHtml(t){return $('<div>').text(t).html();}
</script>
