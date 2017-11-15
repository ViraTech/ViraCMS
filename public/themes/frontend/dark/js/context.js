(function ($) {
  $(document).on('click', '#goto-page-top', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $('html,body').animate({
      scrollTop: 0
    }, 1000);
  });
})(jQuery);
