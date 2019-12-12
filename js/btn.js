(function ($, document, window) {

  // Set button behaviours

  var btnFocus = false;

  $('.btn[data-trigger^="action"]').click(function(){
    switch($(this).attr('data-trigger')){
      case 'action-popover-close':

        // Close popover
        $(this).parent().attr('aria-hidden', 'true');
        $(this).parents('.stepchart__item').removeClass('show-popover');

        break;
    }
  });



})(jQuery, document, window);
