/**
 * Vira Content Block Designer (jquery.designer.js v1.0.0)
 * is a part of ViraCMS Editor Component
 * @url https://github.com/ViraTech/ViraCMS
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 */
(function ($)
{
	$.fn.designer = function (options, action)
	{
		var defaultOptions = {
				dragEl: '.movable',
				dropTo: 'div.ng-row > div[class^=row] > div[class^=span]',
				dragZIndex: 1500,
				placeholder: '<div class="placeholder"></div>',
				upperScrollThreshold: 65,
				lowerScrollThreshold: 45,
				scrollStep: 50,
				onBeforeDrag: null,
				onAfterDrag: null
			},
			scrollTimer,
			dropTo,
			$el,
			elData,
			self = this;

		if (options == 'destroy') {
			$(document)
				.off('mousedown')
				.off('selectstart');
			$(self)
				.off('mouseup')
				.removeAttr('style');

			return self;
		}

		options = $.extend(defaultOptions, options);

		var placeholder = $(options.placeholder);

		function onDragStart(e)
		{
			e.preventDefault();

			if ($(e.target).data('action') || $(e.target).parent().data('action')) {
				return false;
			}

			if (!!e.button && e.which != 1) {
				return false;
			}

			if ($('.draggable').length) {
				return false;
			}

			$el = $(e.target).closest(options.dragEl);
			if (!$el.length) {
				return self;
			}

			if (typeof options.onBeforeDrag == 'function') {
				options.onBeforeDrag(e, $el);
			}

			elData = {
				zIndex: $el.css('z-index'),
				width: $el.outerWidth(),
				height: $el.outerHeight(),
				innerWidth: $el.width(),
				innerHeight: $el.height(),
				positionX: e.pageX - $el.offset().left,
				positionY: e.pageY - $el.offset().top,
				margin: $el.css('margin')
			};

			$el.
				before(placeholder).
				addClass('draggable').
				css({
					position: 'absolute',
					zIndex: options.dragZIndex,
					width: elData.innerWidth,
					height: elData.innerHeight,
					left: e.pageX - elData.width / 2,
					top: e.pageY - 10,
					marginTop: 0
				});

			$(document).on('mousemove', onDragMove);

			$el.on('mouseup', onDragStop);
		}

		function onDragMove(e)
		{
			e.preventDefault();

			clearInterval(scrollTimer);
			var position = {
				top: $('body').scrollTop(),
				bottom: $('body').scrollTop() + $(window).height(),
				windowHeight: $(window).height(),
				documentHeight: $('body').height()
			};

			if ((position.top > 0) && ((e.pageY - position.top) < options.upperScrollThreshold)) {
				scrollTimer = setInterval(function ()
				{
					var scrollTo = $('body').scrollTop() > options.scrollStep ? $('body').scrollTop() - options.scrollStep : 0;
					$('body').scrollTop(scrollTo);

					$el.offset({
						top: $el.offset().top - options.scrollStep
					});

					if (scrollTo == 0) {
						clearInterval(scrollTimer);
					}
				}, 30);
			}
			else if ((position.bottom < position.documentHeight) && ((e.pageY - position.top) > (position.windowHeight - options.lowerScrollThreshold))) {
				scrollTimer = setInterval(function ()
				{
					var bottom = $('body').scrollTop() + position.windowHeight;
					var scrollTo = bottom < (position.documentHeight + options.scrollStep) ? bottom - position.windowHeight + options.scrollStep : position.documentHeight - position.windowHeight;
					$('body').scrollTop(scrollTo);

					$el.offset({
						top: $el.offset().top + options.scrollStep
					});

					if ((scrollTo + position.windowHeight) >= position.documentHeight) {
						clearInterval(scrollTimer);
					}
				}, 30);
			}

			$el.offset({
				left: e.pageX - elData.width / 2,
				top: e.pageY - 10
			});

			dropTo = 'placeholder';
			var container = findDropContainer(e, $el);
			if (container) {
				placeholder.detach();
				var before = findBeforeContainer(e, container, $el);
				var after = findAfterContainer(e, container, $el);
				if (before) {
					placeholder.insertBefore(before);
				}
				else if (after) {
					placeholder.insertAfter(after);
				}
				else {
					placeholder.appendTo(container);
				}
			}
		}

		function onDragStop(e)
		{
			e.preventDefault();
			clearInterval(scrollTimer);

			$el = $(this);
			if ($el.hasClass('draggable') && !$el.is(':animated')) {
				$el.off('mouseup');
				$(document).off('mousemove');
				var attached = placeholder.parents().length;
				if (attached) {
					$el.removeClass('draggable');
					$el.removeAttr('style');
					$el.detach().insertBefore(placeholder);
					placeholder.detach();
					$('body *').stop();
					if (typeof options.onAfterDrag == 'function') {
						options.onAfterDrag(e, $el);
					}
				}
				else {
					$el.removeAttr('style');
				}
			}
		}

		function findDropContainer(e,$el)
		{
			var container;

			$(options.dropTo).each(function ()
			{
				var self = $(this);
				var offset = self.offset();
				var width = self.outerWidth();
				var height = self.outerHeight();
				if ((e.pageX >= offset.left) &&
						(e.pageX <= (offset.left + width)) &&
						(e.pageY >= offset.top) &&
						(e.pageY <= (offset.top + height))) {

					container = self;
				}
			});

			return container;
		}

		function findBeforeContainer(e, $container)
		{
			var before;

			$(options.dragEl, $container).each(function ()
			{
				var self = $(this);
				if (!self.hasClass('draggable')) {
					var offset = self.offset();
					var width = self.outerWidth();
					var height = self.outerHeight();
					if ((e.pageX >= offset.left) &&
							(e.pageX <= (offset.left + width)) &&
							(e.pageY >= offset.top) &&
							(e.pageY <= (offset.top + height / 2))) {

						before = self;
					}
				}
			});

			return before;
		}

		function findAfterContainer(e, $container)
		{
			var after;

			$(options.dragEl, $container).each(function ()
			{
				var self = $(this);
				if (!self.hasClass('draggable')) {
					var offset = self.offset();
					var width = self.outerWidth();
					var height = self.outerHeight();
					if ((e.pageX >= offset.left) &&
							(e.pageX <= (offset.left + width)) &&
							(e.pageY >= offset.top) &&
							(e.pageY >= (offset.top + height / 2))) {

						after = self;
					}
				}
			});

			return after;
		}

		$(document).on('selectstart', function (e)
		{
			e.preventDefault();
			return false;
		});

		$(document).on('mousedown', self, onDragStart);

		return self;
	};
})(jQuery);