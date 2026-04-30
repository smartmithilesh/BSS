
</div><!-- .right_col content -->
</div><!-- .right_col -->
</div><!-- .main_container -->
</div><!-- .container body -->

<script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/nprogress.js"></script>
<script src="<?= BASE_URL ?>assets/js/custom.min.js"></script>
<div class="app-popup-backdrop" id="appPopupBackdrop" aria-hidden="true">
  <div class="app-popup" role="dialog" aria-modal="true" aria-labelledby="appPopupTitle">
    <div class="app-popup-head">
      <div class="app-popup-icon info" id="appPopupIcon"><i class="fa fa-info"></i></div>
      <h4 class="app-popup-title" id="appPopupTitle">Message</h4>
    </div>
    <div class="app-popup-message" id="appPopupMessage"></div>
    <div class="app-popup-actions" id="appPopupActions"></div>
  </div>
</div>
<script>
window.appPopup = (function(){
  var $backdrop = $('#appPopupBackdrop');
  var $icon = $('#appPopupIcon');
  var $title = $('#appPopupTitle');
  var $message = $('#appPopupMessage');
  var $actions = $('#appPopupActions');
  var iconMap = {
    success: 'fa-check',
    error: 'fa-times',
    warning: 'fa-exclamation',
    info: 'fa-info'
  };
  var titleMap = {
    success: 'Success',
    error: 'Error',
    warning: 'Please Confirm',
    info: 'Message'
  };

  function close(result, resolver) {
    $backdrop.removeClass('is-visible').attr('aria-hidden', 'true');
    setTimeout(function(){ $backdrop.hide(); }, 160);
    if(resolver) resolver(result);
  }

  function show(message, type, options) {
    type = type || 'info';
    options = options || {};
    return new Promise(function(resolve){
      $icon.removeClass('success error warning info').addClass(type)
        .html('<i class="fa '+(iconMap[type] || iconMap.info)+'"></i>');
      $title.text(options.title || titleMap[type] || titleMap.info);
      $message.text(message || '');
      $actions.empty();

      if(options.confirm) {
        $('<button type="button" class="btn btn-default">Cancel</button>')
          .appendTo($actions)
          .on('click', function(){ close(false, resolve); });
        $('<button type="button" class="btn btn-danger">Confirm</button>')
          .appendTo($actions)
          .on('click', function(){ close(true, resolve); });
      } else {
        $('<button type="button" class="btn btn-primary">OK</button>')
          .appendTo($actions)
          .on('click', function(){ close(true, resolve); });
      }

      $backdrop.css('display', 'flex').attr('aria-hidden', 'false');
      setTimeout(function(){
        $backdrop.addClass('is-visible');
        $actions.find('.btn:last').focus();
      }, 10);
    });
  }

  $(document).on('keydown', function(e){
    if(e.key === 'Escape' && $backdrop.is(':visible')) {
      $actions.find('.btn:first').trigger('click');
    }
  });

  return show;
})();

window.alert = function(message) {
  window.appPopup(message, 'info');
};

$(function(){
  var flashPopup = <?= json_encode($flashPopup ?? null) ?>;
  if(flashPopup) {
    window.appPopup(flashPopup.message, flashPopup.type);
  }

  $('[data-confirm]').on('click',function(e){
    e.preventDefault();
    var link = this;
    window.appPopup($(link).data('confirm') || 'Are you sure?', 'warning', {confirm:true}).then(function(ok){
      if(ok) window.location.href = link.href;
    });
  });
});
</script>
</body>
</html>
