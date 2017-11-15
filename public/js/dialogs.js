/**
 * ViraCMS Core Interactive Dialogs collection
 * is a part of ViraCMS @url http://viracms.ru/
 *
 * @copyright Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 */
function viraCoreAlert(type, message, position, options) {
  var options = $.extend({
    appendSelector: 'body',
    autoClose: true,
    autoCloseTimeout: 3000
  }, options);
  var template = '<div class="alert alert-' + type + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + message + '</div>';
  var alert = $(template).hide().appendTo($(options.appendSelector));
  if (typeof position == 'string') {
    position = viraCalcPosition(alert, position);
  }
  else if (typeof position == 'undefined') {
    position = {position: 'fixed', right: '20px', top: '60px', width: 'auto'};
  }
  alert.css(position).css({zIndex: 10000}).fadeIn('normal');
  if (options.autoClose) {
    setTimeout(function () {
      alert.fadeOut('normal', function () {
        $(this).remove();
      });
    }, options.autoCloseTimeout);
  }
}

function viraCoreMessage(title, message, callback, context, appendSelector) {
  var template = '<div id="modal-alert" class="modal fade"><div class="modal-header"><a href="javascript:void(0)" class="close" data-dismiss="modal">&times;</a><h3>' + title + '</h3></div><div class="modal-body">' + message + '</div><div class="modal-footer"><a data-dismiss="modal" class="btn" href="javascript:void(0)">OK</a></div></div>';
  var modal = $(template).appendTo(appendSelector || 'body');
  $(modal).
    modal('show').
    on('hidden', function () {
      $(modal).remove();
      if (typeof callback != 'undefined') {
        $.proxy(callback, context)();
      }
    });
}

function viraCoreConfirm (title, content, callbackOk, callbackCancel, locale, appendSelector) {
  locale = $.extend({
    ok: 'OK',
    cancel: 'Cancel'
  }, locale);
  var template = '<div id="modal-confirm" class="modal fade" style="display:none;"><div class="modal-header"><a href="javascript:void(0)" class="close" data-dismiss="modal">&times;</a><h3>' + title + '</h3></div><div class="modal-body form-horizontal">' + content + '</div><div class="modal-footer"><a data-dismiss="modal" class="btn btn-link" href="javascript:void(0)">' + locale.cancel + '</a><button class="btn btn-primary" type="button">' + locale.ok + '</button></div></div>';
  var modal = $(template).appendTo(appendSelector || 'body');
  $(modal).
    modal('show').
    on('shown', function () {
      $('button', modal).bind('click', function (e)
      {
        e.preventDefault();
        if (typeof callbackOk === 'function') {
          callbackOk(e);
        }
      });
    }).
    on('hidden', function () {
      $(modal).remove();
      if (typeof callbackCancel === 'function') {
        callbackCancel();
      }
    });
  return modal;
}

function viraCoreEdit (callbackOk, locale) {
  locale = $.extend({
    ok: 'OK',
    cancel: 'Cancel'
  }, locale);
  var template = '<div id="modal-edit" class="modal fade" style="display:none;"><div class="modal-header"><a href="javascript:void(0)" class="close" data-dismiss="modal">&times;</a><h3 id="modal-edit-header"></h3></div><div class="modal-body form-horizontal" id="modal-edit-content"></div><div class="modal-footer"><button class="btn btn-primary" type="button">' + locale.ok + '</button><a data-dismiss="modal" class="btn" href="javascript:void(0)">' + locale.cancel + '</a></div></div>';
  var modal = $(template).appendTo('body');
  $(modal).
    modal('show').
    on('shown', function () {
      $('button', modal).bind('click', callbackOk);
    }).
    on('hidden', function () {
      $(modal).remove();
    });
  return modal;
}

function viraCoreConfigEdit (callback, locale) {
  locale = $.extend({
    useDefault: 'Use default value',
    ok: 'OK',
    cancel: 'Cancel'
  }, locale);
  var template = '<div id="modal-edit" class="modal fade" style="display:none;"><div class="modal-header"><a href="javascript:void(0)" class="close" data-dismiss="modal">&times;</a><h3 id="modal-edit-header"></h3></div><div class="modal-body form-horizontal" id="modal-edit-content"></div><div class="modal-footer"><button class="btn btn-info default pull-left" type="button">' + locale.useDefault + '</button><button class="btn btn-primary ok" type="button">' + locale.ok + '</button><a data-dismiss="modal" class="btn" href="javascript:void(0)">' + locale.cancel + '</a></div></div>';
  var modal = $(template).appendTo('body');
  $(modal).
    modal('show').
    on('shown', function () {
      $('button.ok,button.default', modal).bind('click', callback);
      $('form', modal).bind('submit', callback);
    }).
    on('hidden', function () {
      $(modal).remove();
    });
  return modal;
}

function viraCoreProgress (init, appendSelector) {
  var percent = init || 0;
  var progressBar = $('<div />', {
    id: 'modal-progress',
    'class': 'modal fade'
  }).append($('<div />', {
    'class': 'modal-body'
  }).html($('<div />', {
    'class': 'progress progress-striped active',
    'style': 'margin: 0;'
  }).html($('<div />', {
    id: 'modal-progress-bar',
    'class': 'bar',
    'style': 'width: ' + percent + '%'
  }))));

  $(progressBar).
    appendTo($(appendSelector || 'body')).
    modal('show').
    on('hidden', function () {
      $(progressBar).remove();
    });

  return progressBar;
}

function viraCoreLoading (message, appendSelector) {
  var loading = $('<div />', {
    id: 'modal-loading',
    'class': 'modal fade'
  }).data('backdrop', 'static').append($('<div />', {
    'class': 'modal-body'
  }).append($('<i/>', {
    'class': 'icon-spinner icon-spin',
    style: 'font-size:16px'
  }).after('<strong style="line-height:16px;padding:0 0 0 10px;">' + message + '</strong>')));

  $(loading).
    appendTo($(appendSelector || 'body')).
    modal('show').
    on('hidden', function () {
      $(loading).remove();
    });

  return loading;
}

function viraCalcPosition (el, position) {
  var elData = {
    w: $(el).outerWidth(),
    h: $(el).outerHeight()
  };

  var winData = {
    w: $(window).width(),
    h: $(window).height()
  };

  var css = {
    position: 'fixed',
    top: 0,
    left: 0,
    width: 'auto'
  };

  if (position == 'topCenter') {
    css.top = 60;
    css.left = winData.w / 2 - elData.w / 2;
  }
  else if (position == 'topLeft') {
    css.top = 60;
    css.left = 20;
  }
  else if (position == 'topRight') {
    css.top = 60;
    css.left = winData.w - elData.w - 20;
  }
  else if (position == 'bottomLeft') {
    css.top = winData.h - elData.h - 20;
    css.left = 20;
  }
  else if (position == 'bottomRight') {
    css.top = winData.h - elData.h - 20;
    css.left = winData.w - elData.w - 20;
  }
  else if (position == 'bottomCenter') {
    css.top = winData.h - elData.h - 20;
    css.left = winData.w / 2 - elData.w / 2;
  }
  else {
    // default is center of screen
    css.top = winData.h / 2 - elData.h / 2;
    css.left = winData.w / 2 - elData.w / 2;
  }

  return css;
}
