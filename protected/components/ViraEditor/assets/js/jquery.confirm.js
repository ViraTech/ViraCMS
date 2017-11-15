/**
 * ViraCMS Confirmation Message Pop-up (jquery.confirm.js v1.0.1)
 * is a part of ViraCMS Editor Component
 * @url https://github.com/ViraTech/ViraCMS
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 */
(function ($)
{
	$.fn.viraConfirm = function (params)
	{
		var defaultOptions = {
			enabled: function () {
				return true;
			},
			position: 'auto', // left, right, top, bottom or auto
			callbacks: {
				show: function (el) {
					return true;
				},
				ok: function () {
				},
				cancel: function () {
				}
			},
			locale: {
				title: 'Confirmation',
				message: 'Please confirm this action',
				buttons: {
					ok: 'OK',
					cancel: 'Cancel'
				}
			},
			template: {
				container: '<div class="popover"><div class="arrow"></div><h3 class="popover-title">{{title}}</h3><div class="popover-content">{{message}}</div><div class="popover-content">{{buttons}}</div></div>',
				buttons: {
					ok: '<a href="#" class="btn btn-primary" data-action="ok">{{title}}</a>',
					cancel: '<a href="#" class="btn btn-link" data-action="cancel">{{title}}</a>'
				}
			},
			append: 'body'
		};

		if (params == 'destroy') {
			$(this).off('click');
			return $(this);
		}

		return $(this).each(function ()
		{
			var options = $.extend(true, {}, defaultOptions, params);

			var buttons = [];
			buttons.push(options.template.buttons.ok.replace('{{title}}', options.locale.buttons.ok));
			buttons.push(options.template.buttons.cancel.replace('{{title}}', options.locale.buttons.cancel));

			var template = options.template.container,
					template = template.replace('{{title}}', options.locale.title).
					replace('{{message}}', options.locale.message).
					replace('{{buttons}}', buttons.join(''));

			$(this).on('click', function (e)
			{
				if (!options.enabled()) {
					return true;
				}

				e.preventDefault();
				e.stopPropagation();

				if (typeof options.callbacks.show == 'function' && !options.callbacks.show(this)) {
					return false;
				}

				$('.popover').hide().remove();
				$('.open > .dropdown-toggle').parent().removeClass('open');

				var $bl = $(template).hide().appendTo($(options.append));
				var blData = {
					w: $bl.outerWidth(),
					h: $bl.outerHeight()
				};

				var $el = $(this);
				var elData = {
					x: $el.offset().left,
					y: $el.offset().top,
					w: $el.outerWidth() || $el.innerWidth(),
					h: $el.outerHeight() || $el.innerWidth()
				};

				var winData = {
					w: $(window).width(),
					h: $(window).height()
				};

				if (options.position == 'auto') {

					if (
						((elData.x + elData.w / 2 - blData.w / 2) > 0) &&
						((elData.x + elData.w / 2 + blData.w / 2) < winData.w)
					) {
						if ((elData.y - blData.h) > 0) {
							var position = 'top';
						}
						else {
							var position = 'bottom';
						}
					}

					else if ((elData.x - blData.w) > 0) {
						var position = 'left';
					}

					else {
						var position = 'right';
					}

				}
				else {
					var position = options.position;
				}

				var posData = {
					x: 0,
					y: 0
				};

				if (position == 'left') {
					posData.x = elData.x - blData.w;
					posData.y = elData.y + elData.h / 2 - blData.h / 2;
				}

				else if (position == 'right') {
					posData.x = elData.x + elData.w;
					posData.y = elData.y + elData.h / 2 - blData.h / 2;
				}

				else if (position == 'bottom') {
					posData.x = elData.x + elData.w / 2 - blData.w / 2;
					posData.y = elData.y + elData.h;
				}

				else {
					posData.x = elData.x + elData.w / 2 - blData.w / 2;
					posData.y = elData.y - blData.h;
				}

				$bl.
					addClass(position).
					css({
						position: 'absolute',
						left: posData.x,
						top: posData.y
					}).
					fadeIn().
					on('click', 'a[data-action="cancel"],a[data-action="ok"]', function (e)
					{
						e.preventDefault();
						e.stopPropagation();
						var button = $(this);
						var callback = function () {
						};

						if (button.data('action') === 'cancel') {
							callback = options.callbacks.cancel;
						}

						if (button.data('action') === 'ok') {
							callback = options.callbacks.ok;
						}

						$bl.fadeOut('fast', function ()
						{
							$bl.remove();
							if (typeof callback === 'function') {
								$.proxy(callback, $el)();
							}
						});
					});
			});
		});
	}
})(jQuery);