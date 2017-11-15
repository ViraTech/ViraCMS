function setClearFields()
{
  var selector = 'tr.filters div.filter-container,form.filter .filter-text,form.filter .filter-range label.range';
  $(document).on('mouseover', selector, function () {
    var self = $(this).find('input[type=text]');
    if (self.val()) {
      if (!$(this).find('div.clear-filter-field').size()) {
        var offset = self.offset();
        var clear = $('<div />')
          .addClass('clear-filter-field')
          .css('z-index', self.css('z-index') + 1)
          .css('display', 'none')
          .css('position', 'absolute')
          .css('line-height', '0')
          .html('<a href="javascript:void(0)" style="color: #eee; text-decoration: none;"><i class="icon-remove-sign"></i></a>')
          .bind('click', function () {
            self.val('');
            $(this).fadeOut('fast');
            $(this).remove();
            self.change();
          })
          .insertAfter(self);

        clear.css('left', offset.left + self.width() - clear.width() + 10)
          .css('top', offset.top + self.height() / 2 - 2)
          .fadeIn('fast');
      }
    }
  });
  $(document).on('mouseleave', selector, function () {
    $('div.clear-filter-field').fadeOut('fast').remove();
  });
}

jQuery(function ($) {
  setClearFields();
  $(document).ajaxComplete(function () {
    setTimeout(setClearFields, 100);
  });
  $(document).on('click', 'a.disabled,a[disabled],li.disabled a', function (e) {
    e.preventDefault();
    e.stopPropagation();
  });
});
